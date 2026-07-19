<head>
    <title>Certificate</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0px;
            padding: 0px;
            width: 100%;
            height: 100%;
        }

        .certificate-container {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            @if($bgPath)
            background-image: url('{{ $bgPath }}');
            background-size: cover;
            background-position: center;
            @else
            background-color: #fefefe;
            border: 20px solid #1a1235;
            @endif
        }

        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
        }

        .title {
            font-size: 45px;
            font-weight: 900;
            color: #1a1235;
            text-transform: uppercase;
            letter-spacing: -2px;
            margin-bottom: 30px;
            font-family: 'Helvetica Black', sans-serif;
        }

        .name {
            font-size: 52px;
            font-weight: bold;
            color: #4f46e5;
            margin: 20px 0;
            border-bottom: 3px solid #e2e8f0;
            display: inline-block;
            padding: 0 40px 10px 40px;
        }

        .body-text {
            font-size: 20px;
            color: #4b5563;
            line-height: 1.6;
            margin: 20px auto;
            max-width: 600px;
        }

        .event-name {
            font-size: 24px;
            font-weight: 800;
            color: #1a1235;
            display: block;
            margin-top: 10px;
        }

        .footer {
            position: absolute;
            bottom: 100px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .signature-box {
            display: inline-block;
            width: 250px;
            text-align: center;
        }

        .signature-img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .signer-name {
            font-size: 18px;
            font-weight: 900;
            color: #1a1235;
            text-transform: uppercase;
            border-top: 2px solid #1a1235;
            padding-top: 5px;
            margin-top: 10px;
        }

        .signer-title {
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="content">
            <div class="title">{{ $title }}</div>
            
            <div class="body-text">
                This is to certify that
            </div>

            <div class="name">{{ $registrantName }}</div>

            <div class="body-text">
                {{ $body }}
                <span class="event-name">{{ $eventName }}</span>
            </div>
        </div>

        <div class="footer">
            <div class="signature-box">
                @if($signaturePath)
                <img src="{{ $signaturePath }}" class="signature-img">
                @else
                <div style="height: 80px;"></div>
                @endif
                <div class="signer-name">{{ $signerName }}</div>
                <div class="signer-title">{{ $signerTitle }}</div>
            </div>
        </div>
    </div>
</body>