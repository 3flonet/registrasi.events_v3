<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
    </style>
</head>
<body>
    <h2>Halo, Terima kasih telah menghubungi kami!</h2>
    <p>Kami telah menerima permohonan inquiry Anda untuk <strong>{{ $submission->form->name }}</strong>.</p>
    
    <p>Tim kami sedang meninjau data Anda dan akan menghubungi Anda secepatnya untuk diskusi lebih lanjut.</p>
    
    @if($proposalUrl)
    <p>Sementara menunggu, Anda dapat mempelajari penawaran kami melalui dokumen berikut:</p>
    <p>
        <a href="{{ $proposalUrl }}" style="background-color: #4F46E5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Download Proposal</a>
    </p>
    @endif
    
    <br>
    <p>Terima kasih,<br>Tim Aigis Moi</p>
</body>
</html>
