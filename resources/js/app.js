import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu
    const btnMobile = document.getElementById('mobile-menu-btn');
    const menuMobile = document.getElementById('mobile-menu');
    if(btnMobile && menuMobile) {
        btnMobile.addEventListener('click', () => {
            menuMobile.classList.toggle('hidden');
        });
    }

    // 2. Navbar Scroll Effect (Swap Logo & Text Color)
    const navbar = document.getElementById('main-navbar');
    const navLinks = document.querySelectorAll('.nav-link-item');
    const logoText = document.getElementById('logo-text');
    const logoImg = document.getElementById('navbar-logo');
    
    // Ambil URL gambar dari data attribute
    const logoWhite = logoImg ? logoImg.getAttribute('data-white') : '';
    const logoGreen = logoImg ? logoImg.getAttribute('data-green') : '';

    function updateNavbar() {
        if (window.scrollY > 50) {
            // SCROLLED STATE (Background Cream, Text Hijau, Logo Hijau)
            navbar.classList.add('bg-cream/95', 'shadow-md', 'backdrop-blur-md', 'py-2');
            navbar.classList.remove('bg-transparent', 'py-4');
            
            navLinks.forEach(link => {
                link.classList.remove('text-white', 'hover:text-accent');
                link.classList.add('text-primary', 'hover:text-accent');
            });

            if(logoText) {
                logoText.classList.remove('text-white');
                logoText.classList.add('text-primary');
            }

            if(logoImg && logoGreen) {
                logoImg.src = logoGreen;
            }

        } else {
            // TOP STATE (Background Transparan, Text Putih, Logo Putih)
            if(document.getElementById('hero-section')) {
                navbar.classList.remove('bg-cream/95', 'shadow-md', 'backdrop-blur-md', 'py-2');
                navbar.classList.add('bg-transparent', 'py-4');
                
                navLinks.forEach(link => {
                    link.classList.add('text-white', 'hover:text-accent');
                    link.classList.remove('text-primary');
                });

                if(logoText) {
                    logoText.classList.add('text-white');
                    logoText.classList.remove('text-primary');
                }

                if(logoImg && logoWhite) {
                    logoImg.src = logoWhite;
                }
            }
        }
    }

    window.addEventListener('scroll', updateNavbar);
    updateNavbar(); // Init load
});