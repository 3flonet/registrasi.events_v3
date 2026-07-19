<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Notice</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-[#1a1235] min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    
    <!-- Background Decor -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] bg-indigo-600/20 rounded-full blur-[120px]"></div>
        <div class="absolute -bottom-[10%] -left-[10%] w-[50%] h-[50%] bg-purple-600/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="max-w-md w-full glass-card rounded-[3rem] p-12 text-center shadow-2xl">
        <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
            <i class="fas fa-info-circle text-4xl animate-pulse"></i>
        </div>
        <h1 class="text-3xl font-black text-[#1a1235] uppercase tracking-tighter mb-4">Notice</h1>
        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest leading-relaxed mb-10">{{ $message }}</p>
        
        <a href="{{ route('events.index') }}" class="inline-flex items-center justify-center w-full py-5 bg-[#1a1235] text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 active:scale-95">
            <i class="fas fa-arrow-left mr-2"></i> Return to Event Page
        </a>
    </div>

</body>
</html>