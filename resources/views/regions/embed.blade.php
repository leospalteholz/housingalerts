<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Region Vote Breakdown</title>
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

        .header-bar {
            flex: 0 0 auto;
            padding: 12px 16px;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .header-bar h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .header-bar span {
            font-size: 12px;
            color: rgba(248, 250, 252, 0.75);
        }

        .table-container {
            flex: 1 1 auto;
            overflow: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 900px;
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
            padding: 10px 16px;
            background: #f8fafc;
            border-top: 1px solid #cbd5f5;
            font-size: 13px;
            color: #475569;
        }

        .bottom-bar strong {
            color: #0f172a;
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
    <table id="councillor-table" data-sortable-table>
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
                        <td class="empty-state" colspan="{{ count($columns) }}">No councillor vote data available yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="bottom-bar">
        <span><strong>{{ number_format($recordCount) }}</strong> councillor{{ $recordCount === 1 ? '' : 's' }}</span>
        <span>Updated {{ $generatedAt }}</span>
    </div>
    @vite('resources/js/embed-tables.js')
</body>
</html>
