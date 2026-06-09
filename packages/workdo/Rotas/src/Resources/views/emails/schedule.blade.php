<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Details of your rotas for this week') }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 8px; margin-bottom: 20px; }
        .schedule-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .schedule-table th, .schedule-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .schedule-table th { background: #007bff; color: white; }
        .shift { background: #e8f5e8; }
        .dayoff { background: #fff3cd; }
        .leave { background: #f8d7da; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $company_name }}</h1>
            <h2>{{ __('Details of your rotas for this week') }}</h2>
            <p>{{ \Carbon\Carbon::parse($start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}</p>
        </div>

        <p>{{ __("Hello") }} {{ $employee->user->name }},</p>
        <p>{{ __("Here is your rotas for the week:") }}</p>

        <table class="schedule-table">
            <thead>
                <tr>
                    <th>{{ __("Date") }}</th>
                    <th>{{ __("Day") }}</th>
                    <th>{{ __("Time") }}</th>
                    <th>{{ __("Type") }}</th>
                    <th>{{ __("Notes") }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rotas as $rota)
                <tr class="{{ $rota->type }}">
                    <td>{{ \Carbon\Carbon::parse($rota->rotas_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($rota->rotas_date)->format('l') }}</td>
                    <td>
                        @if($rota->type === 'shift')
                            {{ \Carbon\Carbon::parse($rota->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($rota->end_time)->format('g:i A') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($rota->type === 'shift')
                            {{ $rota->shift ? $rota->shift->shift_name : __("Shift") }}
                        @elseif($rota->type === 'dayoff')
                            {{ __("Day Off") }}
                        @else
                            {{ __("Leave") }}
                        @endif
                    </td>
                    <td>{{ $rota->notes ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $totalHours = 0;
            foreach($rotas as $rota) {
                if($rota->type === 'shift') {
                    $start = \Carbon\Carbon::parse($rota->start_time);
                    $end = \Carbon\Carbon::parse($rota->end_time);
                    $totalHours += abs($end->diffInMinutes($start)) / 60;
                }
            }
        @endphp

        <p><strong>{{ __("Total Hours:") }} {{ number_format($totalHours, 1) }} {{ __('hours') }}</strong></p>

        <div class="footer">
            <p>{{ __("If you have any questions about your rotas, please contact your manager.") }}</p>
            <p>{{ __("This is an automated email. Please do not reply to this message.") }}</p>
        </div>
    </div>
</body>
</html>