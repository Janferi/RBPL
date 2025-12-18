<?php
require_once '../security_headers.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUZ Coffee - Experience Luxury</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Premium Fonts -->
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
                        'cruz-gray': '#888888',
                    },
                    fontFamily: {
                        'serif': ['"Playfair Display"', 'serif'],
                        'sans': ['"Manrope"', 'sans-serif'],
                    },
                    letterSpacing: {
                        'widest-xl': '0.25em',
                    }
                }
            }
        }
    </script>
    <style>
        .hero-bg {
            background-image: url('../image/backgroundhome.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            /* Parallax feel */
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .text-shadow-hero {
            text-shadow: 2px 2px 40px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body class="bg-cruz-cream text-cruz-charcoal font-sans antialiased overflow-x-hidden">

    <!-- Navbar (Transparent & Elegant) -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300 glass-nav" id="navbar">
        <div class="container mx-auto px-6 py-6 flex justify-between items-center">
            <!-- Brand -->
            <a href="home.php"
                class="text-2xl font-serif font-bold text-white tracking-widest-xl uppercase hover:text-cruz-gold transition duration-500">
                CRUZ.
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center space-x-12">
                <a href="home.php"
                    class="text-xs uppercase tracking-[0.2em] text-white hover:text-cruz-gold transition duration-300">Home</a>
                <a href="halamanmenu.php"
                    class="text-xs uppercase tracking-[0.2em] text-white hover:text-cruz-gold transition duration-300">Collection</a>
                <a href="order.php"
                    class="text-xs uppercase tracking-[0.2em] text-white hover:text-cruz-gold transition duration-300">Orders</a>
            </div>

            <!-- Icons -->
            <div class="flex items-center space-x-8">
                <a href="keranjang.php" class="relative text-white hover:text-cruz-gold transition duration-300 group">
                    <i class="fas fa-shopping-bag text-lg"></i>
                    <?php if (isset($_SESSION['keranjang']) && count($_SESSION['keranjang']) > 0): ?>
                        <span
                            class="absolute -top-2 -right-2 bg-cruz-gold text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">
                            <?= count($_SESSION['keranjang']) ?>
                        </span>
                    <?php endif; ?>
                </a>
                <button id="mobile-menu-btn" class="md:hidden text-white hover:text-cruz-gold transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu"
            class="hidden absolute top-full left-0 w-full bg-cruz-charcoal/95 backdrop-blur-xl border-t border-white/10 p-8 text-center transition-all">
            <a href="home.php"
                class="block py-4 text-white uppercase tracking-widest text-sm hover:text-cruz-gold">Home</a>
            <a href="halamanmenu.php"
                class="block py-4 text-white uppercase tracking-widest text-sm hover:text-cruz-gold">Collection</a>
            <a href="order.php"
                class="block py-4 text-white uppercase tracking-widest text-sm hover:text-cruz-gold">Orders</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative w-full h-screen hero-bg flex items-center justify-center">
        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-black/80"></div>

        <div class="relative z-10 text-center px-6 max-w-5xl mx-auto" data-aos="fade-up">
            <p class="text-cruz-gold uppercase tracking-[0.4em] text-xs md:text-sm mb-6 animate-pulse">Est. 2024 •
                Premium Coffee House</p>
            <h1
                class="text-5xl md:text-7xl lg:text-8xl font-serif text-white mb-8 leading-tight italic text-shadow-hero">
                Good Place, <br>
                <span class="not-italic font-light">Good Friend.</span>
            </h1>
            <p
                class="text-gray-300 text-sm md:text-base tracking-wide max-w-lg mx-auto mb-12 leading-relaxed font-light">
                Experience the finest blend of atmosphere and taste. Where every cup is crafted with precision and
                passion.
            </p>

            <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-6">
                <a href="halamanmenu.php"
                    class="px-10 py-4 bg-cruz-gold text-white text-xs uppercase tracking-widest hover:bg-white hover:text-cruz-black transition-all duration-300 border border-cruz-gold">
                    View Menu
                </a>
                <a href="#story"
                    class="px-10 py-4 bg-transparent border border-white/30 text-white text-xs uppercase tracking-widest hover:bg-white hover:text-cruz-black transition-all duration-300">
                    Our Story
                </a>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center animate-bounce">
            <span class="text-white/50 text-[10px] uppercase tracking-widest mb-2">Scroll</span>
            <div class="w-[1px] h-12 bg-gradient-to-b from-white to-transparent"></div>
        </div>
    </header>

    <!-- Content Section (Story) -->
    <section id="story" class="py-24 md:py-32 bg-cruz-cream">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-16">
                <div class="md:w-1/2">
                    <div
                        class="relative h-96 w-full flex items-center justify-center p-12 bg-[#F9F9F7] rounded-sm shadow-xl">
                        <div
                            class="absolute -top-4 -left-4 w-full h-full border border-cruz-gold/30 rounded-sm hidden md:block pointer-events-none">
                        </div>
                        <img src="../images/menu/Latte.png" alt="Coffee Art"
                            class="w-full h-full object-contain grayscale hover:grayscale-0 transition duration-700 ease-in-out relative z-10 filter contrast-125">
                    </div>
                </div>
                <div class="md:w-1/2 text-center md:text-left">
                    <span class="text-cruz-gold uppercase tracking-[0.2em] text-xs font-bold mb-4 block">The
                        Philosophy</span>
                    <h2 class="text-4xl md:text-5xl font-serif text-cruz-black mb-8 leading-tight">Crafting
                        Moments,<br>One Cup at a Time.</h2>
                    <p class="text-gray-600 font-light leading-relaxed mb-8">
                        At CRUZ, we believe coffee is more than just a drink—it's a ritual. A moment of pause in a
                        chaotic world. We source the finest beans, roast them to perfection, and brew them with an
                        artist's touch.
                    </p>
                    <a href="halamanmenu.php"
                        class="inline-block text-cruz-black border-b border-cruz-black pb-1 text-xs uppercase tracking-widest hover:text-cruz-gold hover:border-cruz-gold transition">
                        Read More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Menu (Preview) -->
    <section class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-serif text-cruz-black mb-4">Signature Selections</h2>
                <div class="w-24 h-[1px] bg-cruz-gold mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                include 'koneksi.php';
                // Fetch 3 random items for display
                $home_query = "SELECT * FROM menu ORDER BY RAND() LIMIT 3";
                $home_result = mysqli_query($koneksi, $home_query);

                if (mysqli_num_rows($home_result) > 0) {
                    $mt = 0;
                    while ($row = mysqli_fetch_assoc($home_result)) {
                        $name = htmlspecialchars($row['nama']);
                        $price = number_format($row['harga'], 0, ',', '.');
                        $marginTopClass = ($mt == 1) ? 'md:-mt-12' : 'mt-0'; // Apply stagger effect to 2nd item
                        $mt++;
                        ?>
                        <div class="group cursor-pointer <?= $marginTopClass ?>">
                            <div
                                class="overflow-hidden mb-6 relative w-full aspect-[3/4] bg-[#F9F9F7] flex items-center justify-center p-6">
                                <a href="halamanpesam.php?id=<?= $row['id'] ?>"
                                    class="w-full h-full flex items-center justify-center">
                                    <img src="data:image/jpeg;base64,<?= base64_encode($row['gambar']) ?>" alt="<?= $name ?>"
                                        class="max-w-full max-h-full object-contain transform group-hover:scale-110 transition duration-700 ease-out filter grayscale-0 group-hover:contrast-105">
                                </a>
                                <div class="absolute inset-0 bg-black/5 group-hover:bg-transparent transition duration-500">
                                </div>
                            </div>
                            <div class="text-center">
                                <h3 class="text-xl font-serif text-cruz-black group-hover:text-cruz-gold transition">
                                    <a href="halamanpesam.php?id=<?= $row['id'] ?>"><?= $name ?></a>
                                </h3>
                                <p class="text-gray-400 font-light text-sm mt-2">IDR <?= $price ?></p>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

            <div class="text-center mt-16">
                <a href="halamanmenu.php"
                    class="px-12 py-4 border border-cruz-black text-cruz-black text-xs uppercase tracking-widest hover:bg-cruz-black hover:text-white transition-all duration-300">
                    View Full Collection
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-cruz-charcoal text-white py-20 border-t border-white/5">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-serif mb-8 tracking-wider">CRUZ.</h2>
            <div class="flex justify-center space-x-8 mb-12">
                <a href="#" class="text-gray-400 hover:text-cruz-gold transition"><i
                        class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-cruz-gold transition"><i
                        class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="text-gray-400 hover:text-cruz-gold transition"><i
                        class="fab fa-facebook-f text-xl"></i></a>
            </div>
            <p class="text-gray-500 text-xs tracking-widest uppercase">&copy; 2024 CRUZ Coffee. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) {
                nav.classList.add('bg-cruz-charcoal', 'shadow-lg');
                nav.classList.remove('glass-nav');
            } else {
                nav.classList.add('glass-nav');
                nav.classList.remove('bg-cruz-charcoal', 'shadow-lg');
            }
        });

        // Mobile Menu Toggle
        const btn = document.getElementById('mobile-menu-btn');
        const menu = document.getElementById('mobile-menu');

        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
    </script>
</body>

</html>