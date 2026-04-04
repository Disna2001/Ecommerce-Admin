@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $siteName }} - Admin Activity Logs</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #0f172a; font-size: 12px; }
        h1 { margin: 0 0 8px; font-size: 22px; }
        p { margin: 0 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; vertical-align: top; text-align: left; }
        th { background: #e2e8f0; font-size: 11px; text-transform: uppercase; }
        .muted { color: #64748b; font-size: 11px; }
        .meta { margin-bottom: 14px; }
    </style>
</head>
<body>
    <h1>{{ $siteName }} Admin Activity Logs</h1>
    <div class="meta">
        <p class="muted">Exported at: {{ $exportedAt }}</p>
        <p class="muted">Records: {{ $logs->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Action</th>
                <th>Description</th>
                <th>User</th>
                <th>Subject</th>
                <th>When</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $log->action)) }}</td>
                    <td>{{ $log->description ?: 'No description recorded.' }}</td>
                    <td>{{ $log->user?->name ?? 'System / Unknown' }}</td>
                    <td>{{ class_basename($log->subject_type ?? 'General') }} @if($log->subject_id)#{{ $log->subject_id }}@endif</td>
                    <td>{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
