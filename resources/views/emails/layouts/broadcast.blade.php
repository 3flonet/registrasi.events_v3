<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        a {
            color: #007BFF;
        }

        /* Reset paragraph margins for consistent spacing across email clients */
        p {
            margin-top: 0 !important;
            margin-bottom: 12px !important;
        }

        p:last-child {
            margin-bottom: 0 !important;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        {{ strip_tags(Str::limit($content, 150)) }}
    </div>

    <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; margin: 20px auto; border: 1px solid #eeeeee; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        @if($bannerPath && file_exists($bannerPath))
        <tr>
            <td>
                <img src="{{ $message->embed($bannerPath) }}" alt="Event Banner" width="600" style="display: block; width: 100%; height: auto;">
            </td>
        </tr>
        @else
        <tr>
            <td align="center" style="padding: 35px 0; background-color: {{ $primaryColor ?? '#3b82f6' }};">
                @if($logoPath && file_exists($logoPath))
                <img src="{{ $message->embed($logoPath) }}" alt="{{ $appName }}" width="140" style="display: block; outline: none; border: none; text-decoration: none;">
                @else
                <h1 style="margin: 0; font-size: 28px; font-weight: 800; color: #ffffff; letter-spacing: -1px; text-transform: uppercase;">{{ $appName }}</h1>
                @endif
            </td>
        </tr>
        @endif

        <tr>
            <td style="padding: 45px 40px; color: #1a1a1a; font-size: 16px; line-height: 1.5;">
                <div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
                    {!! $content !!}
                </div>
            </td>
        </tr>

        <tr>
            <td align="center" style="padding: 30px 40px; background-color: #fafafa; border-top: 1px solid #eeeeee; color: #888888; font-size: 12px;">
                <p style="margin: 0; font-weight: bold; color: #555555;">Sent via {{ $appName }}</p>
                <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                <p style="margin: 10px 0 0 0; font-style: italic;">This email is sent automatically. Please do not reply.</p>
            </td>
        </tr>
    </table>
</body>

</html>