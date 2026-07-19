<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $template->name }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS (via CDN for preview simplicity) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1235',
                        accent: '#FFD700',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        {!! $css !!}
        body { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="preview-container">
        {!! $html !!}
    </div>
</body>
</html>
