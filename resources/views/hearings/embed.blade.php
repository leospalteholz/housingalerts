<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hearings Table</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }

        .table-container {
            flex: 1 1 auto;
            overflow: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 1200px;
        }

        thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #f8fafc;
            border-bottom: 2px solid #cbd5f5;
            text-align: left;
            padding: 6px 8px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #475569;
            white-space: nowrap;
        }

        tbody td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
            white-space: nowrap;
            color: #0f172a;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        tbody tr:hover {
            background: #e2e8f0;
        }

        .bottom-bar {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #f8fafc;
            border-top: 1px solid #cbd5f5;
            font-size: 13px;
            color: #475569;
        }

        .bottom-bar strong {
            color: #0f172a;
        }

        .csv-button {
            display: inline-flex;
            align-items: center;
            background: #2563eb;
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
            transition: background 0.2s ease;
        }

        .csv-button:hover {
            background: #1d4ed8;
        }

        .empty-state {
            padding: 24px;
            text-align: center;
            color: #475569;
        }

    </style>
</head>
<body>
    <div class="table-container">
    <table id="hearings-table" data-sortable-table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td title="{{ $value }}">{{ $value }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td class="empty-state" colspan="{{ count($columns) }}">No hearings available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bottom-bar">
        <span><strong>{{ number_format($recordCount) }}</strong> hearings • Updated {{ $generatedAt }}</span>
        <a class="csv-button" href="{{ route('organization.hearings.export', ['organization' => $organization->slug]) }}" target="_blank" rel="noopener">Export CSV</a>
    </div>
    @vite('resources/js/embed-tables.js')
</body>
</html>
