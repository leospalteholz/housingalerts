<?php

namespace App\Console\Commands;

use App\Models\Councillor;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportVotesFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:import-votes {path=Votes.csv}';

    /**
     * The console command description.
     */
    protected $description = 'Import hearings and councillor votes from a CSV export.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $pathArgument = $this->argument('path');
        $csvPath = $this->resolvePath($pathArgument);

        if (! is_readable($csvPath)) {
            $this->error("CSV file not found or unreadable at: {$csvPath}");

            return self::FAILURE;
        }

        $rows = $this->parseCsv($csvPath);

        if ($rows->isEmpty()) {
            $this->warn('CSV file contains no data rows.');

            return self::SUCCESS;
        }

        $rowsMissingAddress = $rows->filter(function ($row) {
            $value = $row['Street Address'] ?? null;

            return trim((string) $value) === '';
        })->values();

        if ($rowsMissingAddress->isNotEmpty()) {
            $this->error('Street Address is required for every row. The import was cancelled because of the following entries:');

            $rowsMissingAddress->each(function ($row) {
                $municipality = trim((string) ($row['Municipality'] ?? 'Unknown municipality'));
                $date = trim((string) ($row['Date'] ?? 'Unknown date'));

                $this->line(" - {$municipality} ({$date})");
            });

            $this->warn('No records were imported. Please fill in the missing street addresses and try again.');

            return self::FAILURE;
        }

        [$regionsByKey, $missingRegions] = $this->validateRegions($rows);
        [$councillorsByKey, $missingCouncillors] = $this->validateCouncillors($rows);

        if ($missingRegions->isNotEmpty() || $missingCouncillors->isNotEmpty()) {
            if ($missingRegions->isNotEmpty()) {
                $this->error('Missing regions for the following municipalities:');
                $missingRegions->each(fn (string $name) => $this->line(" - {$name}"));
            }

            if ($missingCouncillors->isNotEmpty()) {
                $this->error('Missing councillors referenced in the CSV:');
                $missingCouncillors->each(fn (string $name) => $this->line(" - {$name}"));
            }

            $this->warn('No records were imported. Please address the missing data and try again.');

            return self::FAILURE;
        }

        $createdHearings = 0;
        $updatedHearings = 0;
        $createdVotes = 0;

        DB::transaction(function () use ($rows, $regionsByKey, $councillorsByKey, &$createdHearings, &$updatedHearings, &$createdVotes) {
            foreach ($rows as $row) {
                $region = $regionsByKey->get($this->normalizeKey($row['Municipality']));

                if (! $region) {
                    // Should not happen because of earlier validation, but guard just in case.
                    throw new \RuntimeException('Region lookup failed for municipality: ' . $row['Municipality']);
                }

                $hearingDate = $this->parseDate($row['Date']);
                $hearingEnd = $hearingDate->clone()->addHours(2);

                $streetAddress = $row['Street Address'] ?? null;
                $postalCode = $row['Postal Code'] ?? null;

                $description = $this->buildHearingDescription($row);
                $decision = $this->toBoolean($row['Decision']);
                $title = $streetAddress ?: ($row['Municipality'] ? $row['Municipality'] . ' Development Hearing' : 'Development Hearing');

                $hearing = Hearing::updateOrCreate(
                    [
                        'street_address' => $streetAddress,
                        'start_datetime' => $hearingDate,
                        'region_id' => $region->id,
                    ],
                    [
                        'organization_id' => $region->organization_id,
                        'type' => 'development',
                        'title' => $title,
                        'postal_code' => $postalCode,
                        'rental' => $this->toBoolean($row['Rental']),
                        'units' => $this->toNullableInt($row['Units']),
                        'below_market_units' => $this->toIntOrZero($row['Below Market Units']),
                        'replaced_units' => $this->toNullableInt($row['Replaced Units']),
                        'subject_to_vote' => true,
                        'approved' => true,
                        'description' => $description,
                        'start_datetime' => $hearingDate,
                        'end_datetime' => $hearingEnd,
                    ]
                );

                if ($hearing->wasRecentlyCreated) {
                    $createdHearings++;
                } else {
                    $updatedHearings++;
                }

                $notes = $this->buildVoteNotes($row);

                $hearingVote = HearingVote::updateOrCreate(
                    [
                        'hearing_id' => $hearing->id,
                    ],
                    [
                        'vote_date' => $hearingDate->toDateString(),
                        'passed' => $decision,
                        'notes' => $notes,
                    ]
                );

                if ($hearingVote->wasRecentlyCreated) {
                    $createdVotes++;
                }

                $hearingVote->councillorVotes()->delete();

                $supporters = $this->parseNameList($row['Support']);
                $opponents = $this->parseNameList($row['Against']);

                $this->attachVotes($hearingVote, $supporters, 'for', $region->id, $councillorsByKey);
                $this->attachVotes($hearingVote, $opponents, 'against', $region->id, $councillorsByKey);
            }
        });

        $this->info('Import complete.');
        $this->line("Hearings created: {$createdHearings}");
        $this->line("Hearings updated: {$updatedHearings}");
        $this->line("Votes created: {$createdVotes}");

        return self::SUCCESS;
    }

    /**
     * Resolve the CSV path relative to the project root.
     */
    private function resolvePath(string $path): string
    {
        if (Str::startsWith($path, DIRECTORY_SEPARATOR) || Str::startsWith($path, ['./'])) {
            return realpath($path) ?: $path;
        }

        $fullPath = base_path($path);

        return realpath($fullPath) ?: $fullPath;
    }

    /**
     * Parse the CSV into a collection of associative arrays.
     */
    private function parseCsv(string $path): Collection
    {
        $handle = fopen($path, 'r');

        if (! $handle) {
            throw new \RuntimeException('Unable to open CSV file: ' . $path);
        }

        $headers = null;
        $rows = collect();

        while (($data = fgetcsv($handle)) !== false) {
            if ($headers === null) {
                $headers = array_map(fn ($header) => $this->sanitizeHeader($header), $data);
                continue;
            }

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $row = $this->combineRow($headers, $data);
            $rows->push($row);
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Determine whether the row contains any actual values.
     */
    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * Combine headers with row values and trim string values.
     */
    private function combineRow(array $headers, array $data): array
    {
        if (count($data) < count($headers)) {
            $data = array_pad($data, count($headers), null);
        }

        $combined = [];

        foreach ($headers as $index => $header) {
            $value = $data[$index] ?? null;
            $combined[$header] = is_string($value) ? trim($value) : $value;
        }

        return $combined;
    }

    /**
     * Normalise header names so that lookups are consistent.
     */
    private function sanitizeHeader(?string $header): string
    {
        if ($header === null) {
            return '';
        }

        $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        return trim($cleaned);
    }

    /**
     * Validate regions referenced in the CSV and return lookup map.
     */
    private function validateRegions(Collection $rows): array
    {
        $regionNames = $rows
            ->pluck('Municipality')
            ->filter()
            ->map(fn ($name) => trim($name))
            ->unique();

        $regions = Region::query()->get();
        $regionsByKey = $regions->keyBy(fn (Region $region) => $this->normalizeKey($region->name));

        $missing = $regionNames->filter(fn ($name) => ! $regionsByKey->has($this->normalizeKey($name)))->values();

        return [$regionsByKey, $missing];
    }

    /**
     * Validate councillors referenced in the CSV and return lookup map.
     */
    private function validateCouncillors(Collection $rows): array
    {
        $names = $rows->flatMap(function ($row) {
            return $this->parseNameList($row['Support'] ?? '')
                ->merge($this->parseNameList($row['Against'] ?? ''));
        })->filter()->unique();

        $councillors = Councillor::query()->get();
        $councillorsByKey = $councillors->groupBy(fn (Councillor $councillor) => $this->normalizeKey($councillor->name));

        $missing = $names->filter(fn ($name) => ! $councillorsByKey->has($this->normalizeKey($name)))->values();

        return [$councillorsByKey, $missing];
    }

    /**
     * Attach councillor votes to the hearing vote.
     */
    private function attachVotes(HearingVote $hearingVote, Collection $names, string $voteType, int $regionId, Collection $councillorsByKey): void
    {
        $names->each(function (string $name) use ($hearingVote, $voteType, $regionId, $councillorsByKey) {
            $lookupKey = $this->normalizeKey($name);
            $matches = $councillorsByKey->get($lookupKey, collect());

            /** @var null|Councillor $councillor */
            $councillor = $matches->firstWhere('region_id', $regionId) ?: $matches->first();

            if (! $councillor) {
                throw new \RuntimeException('Unable to resolve councillor: ' . $name);
            }

            $hearingVote->councillorVotes()->create([
                'councillor_id' => $councillor->id,
                'vote' => $voteType,
            ]);
        });
    }

    /**
     * Convert a CSV value to a nullable integer.
     */
    private function toNullableInt($value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Convert a CSV value to an integer, defaulting missing values to zero.
     */
    private function toIntOrZero($value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    /**
     * Convert a CSV value to a nullable boolean.
     */
    private function toBoolean($value): ?bool
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $normalized = $this->normalizeKey((string) $value);

        return in_array($normalized, ['yes', 'y', 'true', '1'], true) ? true : (in_array($normalized, ['no', 'n', 'false', '0'], true) ? false : null);
    }

    /**
     * Build a short description for the hearing.
     */
    private function buildHearingDescription(array $row): ?string
    {
        $parts = collect();

        if (! empty($row['Hearing Type'])) {
            $parts->push('Source hearing type: ' . $row['Hearing Type']);
        }

        if (! empty($row['Consistent w/ OCP'])) {
            $parts->push('Consistent with OCP: ' . $row['Consistent w/ OCP']);
        }

        return $parts->isNotEmpty() ? $parts->implode("\n") : null;
    }

    /**
     * Build notes that will be stored on the hearing vote.
     */
    private function buildVoteNotes(array $row): ?string
    {
        $parts = collect();

        if (! empty($row['Notes'])) {
            $parts->push($row['Notes']);
        }

        return $parts->isNotEmpty() ? $parts->implode("\n") : null;
    }

    /**
     * Convert comma-separated councillor names into a collection.
     */
    private function parseNameList(?string $value): Collection
    {
        if ($value === null || trim($value) === '') {
            return collect();
        }

        return collect(explode(',', $value))
            ->map(fn ($name) => trim($name))
            ->filter(fn ($name) => $name !== '')
            ->values();
    }

    /**
     * Parse a date string from the CSV.
     */
    private function parseDate(?string $value): Carbon
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Date value is required for each row.');
        }

        return Carbon::parse($value, config('app.timezone'))->setTime(19, 0, 0);
    }

    /**
     * Normalise string values for key lookups.
     */
    private function normalizeKey(?string $value): string
    {
        return Str::lower(trim((string) $value));
    }
}
