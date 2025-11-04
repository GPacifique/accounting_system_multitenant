{{-- resources/views/exports/pdf-template.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Export' }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            margin: 20px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header .logo-container {
            margin-bottom: 15px;
        }
        .header .logo {
            height: 60px;
            width: auto;
            display: block;
            margin: 0 auto;
            max-width: 200px;
        }
        .header h1 { 
            color: #333; 
            margin: 5px 0 5px 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header .subtitle { 
            color: #666; 
            font-size: 14px;
            margin: 0;
            font-style: italic;
        }
        .meta-info {
            margin-bottom: 20px;
            font-size: 11px;
            color: #666;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left;
            font-size: 11px;
        }
        th { 
            background-color: #f5f5f5; 
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .amount { font-weight: bold; color: #2563eb; }
        .total-row { 
            font-weight: bold; 
            background-color: #f0f9ff;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        {{-- Logo Section --}}
        <div class="logo-container">
            @php
                $logoPath = public_path('images/logo.png');
                $logoExists = file_exists($logoPath);
            @endphp
            @if($logoExists)
                <img src="{{ $logoPath }}" 
                     alt="SiteLedger Logo" 
                     class="logo">
            @else
                {{-- Fallback if logo doesn't exist --}}
                <div style="height: 60px; display: flex; align-items: center; justify-content: center; color: #666;">
                    SiteLedger Logo
                </div>
            @endif
        </div>
        <h1>{{ config('app.name', 'SiteLedger') }}</h1>
        <p class="subtitle">{{ $subtitle ?? 'Construction Finance Management' }}</p>
    </div>
    
    {{-- Meta Information --}}
    <div class="meta-info">
        <p><strong>Report:</strong> {{ $title ?? 'Data Export' }}</p>
        <p><strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
        <p><strong>Total Records:</strong> {{ $totalRecords ?? count($data ?? []) }}</p>
        @if(isset($dateRange))
            <p><strong>Date Range:</strong> {{ $dateRange }}</p>
        @endif
    </div>
    
    {{-- Main Content --}}
    <div class="content">
        @yield('content')
    </div>
    
    {{-- Footer --}}
    <div class="footer">
        <p><strong>{{ config('app.name', 'SiteLedger') }}</strong> - Construction Finance Management System</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p style="font-size: 9px; color: #999;">Professional construction finance tracking and reporting</p>
    </div>
</body>
</html>