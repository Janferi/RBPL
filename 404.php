<?php
require_once 'security_headers.php';

// Set HTTP response code
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | CRUZ Coffee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Manrope:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cruz-black': '#121212',
                        'cruz-charcoal': '#1a1a1a',
                        'cruz-gold': '#C5A065',
                        'cruz-cream': '#F5F5F0',
                    },
                    fontFamily: {
                        'serif': ['"Playfair Display"', 'serif'],
                        'sans': ['"Manrope"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased min-h-screen flex items-center justify-center">

    <div class="text-center px-6">
        <!-- Error Code -->
        <h1 class="text-8xl md:text-9xl font-serif text-cruz-gold mb-4 italic">404</h1>

        <!-- Divider -->
        <div class="w-16 h-[1px] bg-cruz-gold mx-auto mb-8"></div>

        <!-- Message -->
        <h2 class="text-2xl md:text-3xl font-serif text-cruz-black mb-4">Page Not Found</h2>
        <p class="text-gray-500 font-light mb-12 max-w-md mx-auto">
            The page you're looking for doesn't exist or has been moved.
            Let's get you back on track.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="/CRUZ/Customer/home.php"
                class="px-8 py-4 bg-cruz-charcoal text-white text-xs uppercase tracking-[0.2em] hover:bg-cruz-gold transition duration-300">
                <i class="fas fa-home mr-2"></i>Back to Home
            </a>
            <a href="/CRUZ/Customer/halamanmenu.php"
                class="px-8 py-4 border border-cruz-charcoal text-cruz-charcoal text-xs uppercase tracking-[0.2em] hover:bg-cruz-charcoal hover:text-white transition duration-300">
                <i class="fas fa-coffee mr-2"></i>View Menu
            </a>
        </div>

        <!-- Brand -->
        <div class="mt-16">
            <span class="text-cruz-gold text-xs uppercase tracking-[0.3em]">CRUZ Coffee</span>
        </div>
    </div>

</body>

</html>