<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Report - {{ $event->name }}</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1235;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            max-height: 50px;
            float: left;
        }
        .report-title {
            float: right;
            text-align: right;
        }
        .report-title h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6366f1;
        }
        .report-title p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #94a3b8;
            font-weight: bold;
        }
        .clear { clear: both; }
        
        .event-info {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 10px;
        }
        .event-info h2 {
            margin: 0 0 10px;
            font-size: 24px;
            font-weight: 900;
            color: #1a1235;
        }
        .event-info-grid {
            width: 100%;
        }
        .event-info-grid td {
            font-size: 11px;
            padding: 2px 0;
        }
        .label {
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: bold;
            width: 100px;
        }

        .kpi-grid {
            width: 100%;
            margin-bottom: 30px;
        }
        .kpi-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
            width: 23%;
        }
        .kpi-value {
            font-size: 20px;
            font-weight: 900;
            display: block;
            margin-bottom: 5px;
        }
        .kpi-label {
            font-size: 8px;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            margin-bottom: 15px;
            padding-left: 10px;
            border-left: 4px solid #6366f1;
        }

        .chart-container {
            margin-bottom: 30px;
            text-align: center;
        }
        .chart-img {
            max-width: 100%;
            height: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th {
            background: #f1f5f9;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            padding: 10px;
            color: #64748b;
        }
        .data-table td {
            padding: 10px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #cbd5e1;
            padding-bottom: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bg-emerald { background: #ecfdf5; color: #10b981; }
        .bg-indigo { background: #eef2ff; color: #6366f1; }
        .bg-rose { background: #fff1f2; color: #f43f5e; }
        .bg-amber { background: #fffbeb; color: #f59e0b; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            @if($event->organizer && $event->organizer->logo_path)
                @php
                    $path = storage_path('app/public/' . $event->organizer->logo_path);
                @endphp
                @if(file_exists($path))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($path)) }}" height="40">
                @else
                    <div style="font-weight: 900; font-size: 18px; color: #1a1235;">{{ config('app.name') }}</div>
                @endif
            @else
                <div style="font-weight: 900; font-size: 18px; color: #1a1235;">{{ config('app.name') }}</div>
            @endif
        </div>
        <div class="report-title">
            <h1>Executive Summary</h1>
            <p>Generated on {{ now()->format('d M Y, H:i') }}</p>
        </div>
        <div class="clear"></div>
    </div>

    <div class="event-info">
        <h2>{{ $event->name }}</h2>
        <table class="event-info-grid">
            <tr>
                <td class="label">Schedule</td>
                <td>{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</td>
                <td class="label">Venue</td>
                <td>{{ $event->venue ?: 'Online Event' }}</td>
            </tr>
            <tr>
                <td class="label">Organizer</td>
                <td>{{ $event->organizer->name ?? 'System Admin' }}</td>
                <td class="label">Status</td>
                <td>{{ strtoupper($event->status) }}</td>
            </tr>
        </table>
    </div>

    <table class="kpi-grid">
        <tr>
            <td class="kpi-card">
                <span class="kpi-value">{{ number_format($conversionMetrics['invited']) }}</span>
                <span class="kpi-label">Total Invited</span>
            </td>
            <td style="width: 2%"></td>
            <td class="kpi-card">
                <span class="kpi-value">{{ number_format($conversionMetrics['registered']) }}</span>
                <span class="kpi-label">Registered</span>
            </td>
            <td style="width: 2%"></td>
            <td class="kpi-card" style="border-color: #10b981;">
                <span class="kpi-value" style="color: #10b981;">{{ number_format($conversionMetrics['attended']) }}</span>
                <span class="kpi-label">Actual Presence</span>
            </td>
            <td style="width: 2%"></td>
            <td class="kpi-card" style="border-color: #6366f1; background: #6366f1;">
                <span class="kpi-value" style="color: #ffffff;">{{ $conversionMetrics['conversion_rate'] }}%</span>
                <span class="kpi-label" style="color: #ffffff; opacity: 0.8;">Conversion</span>
            </td>
        </tr>
    </table>

    <div class="section-title">Participation Analytics</div>
    <div class="chart-container">
        @php
            $chartData = [
                'type' => 'bar',
                'data' => [
                    'labels' => $chartCategories,
                    'datasets' => [[
                        'label' => 'Attendees',
                        'data' => $chartSeries,
                        'backgroundColor' => '#6366f1'
                    ]]
                ],
                'options' => [
                    'title' => ['display' => true, 'text' => 'Daily Attendance Trend'],
                ]
            ];
            $chartUrl = "https://quickchart.io/chart?c=" . urlencode(json_encode($chartData));
        @endphp
        <img src="{{ $chartUrl }}" class="chart-img" style="height: 250px;">
    </div>

    <div style="width: 100%; margin-bottom: 30px;">
        <div style="width: 48%; float: left;">
            <div class="section-title">Ticket Distribution</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: right;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ticketDistribution as $tier)
                        <tr>
                            <td>{{ $tier['name'] }}</td>
                            <td style="text-align: center;">{{ $tier['total'] }}</td>
                            <td style="text-align: right;">{{ $tier['percentage'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="width: 48%; float: right;">
            <div class="section-title">Invitation Response</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th style="text-align: center;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Confirmed</td>
                        <td style="text-align: center;"><span class="badge bg-emerald">{{ $invitationStats['confirmed'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Represented</td>
                        <td style="text-align: center;"><span class="badge bg-indigo">{{ $invitationStats['represented'] }}</span></td>
                    </tr>
                    <tr>
                        <td>Declined</td>
                        <td style="text-align: center;"><span class="badge bg-rose">{{ $invitationStats['declined'] }}</span></td>
                    </tr>
                    <tr>
                        <td>No Response</td>
                        <td style="text-align: center;"><span class="badge bg-amber">{{ $invitationStats['pending'] }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} Analytical Studio | This document is an official summary generated for stakeholder review.
    </div>
</body>
</html>
