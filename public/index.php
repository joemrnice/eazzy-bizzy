<?php
/**
 * EazzyBizzy - Main Index
 * Entry point for the application
 */

session_start();

require_once __DIR__ . '/app/helpers/functions.php';

// Get current page
$page = $_GET['page'] ?? 'home';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EazzyBizzy - Electronics & Home Appliances</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0ea5e9',
                        primaryDark: '#0284c7',
                        accent: '#f97316',
                        accentDark: '#ea580c',
                        dark: '#0f172a',
                        surface: '#f8fafc'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="text-2xl font-bold text-primary">
                    <i class="fas fa-bolt"></i> EazzyBizzy
                </div>
                
                <nav class="hidden md:flex space-x-6">
                    <a href="/" class="text-gray-700 hover:text-primary">Home</a>
                    <a href="?page=shop" class="text-gray-700 hover:text-primary">Shop</a>
                    <a href="?page=about" class="text-gray-700 hover:text-primary">About</a>
                    <a href="?page=contact" class="text-gray-700 hover:text-primary">Contact</a>
                </nav>
                
                <div class="flex items-center space-x-4">
                    <a href="?page=cart" class="relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="cart-count absolute -top-2 -right-2 bg-accent text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">0</span>
                    </a>
                    <?php if (isLoggedIn()): ?>
                        <a href="?page=account" class="text-gray-700 hover:text-primary">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="?page=auth" class="btn-primary">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="min-h-screen">
        <?php if ($page === 'home'): ?>
            <section class="bg-gradient-to-r from-primary to-primaryDark text-white py-20">
                <div class="container mx-auto px-4 text-center">
                    <h1 class="text-5xl font-bold mb-4">Welcome to EazzyBizzy</h1>
                    <p class="text-xl mb-8">Your one-stop shop for Electronics & Home Appliances</p>
                    <a href="?page=shop" class="btn-accent inline-block">Shop Now</a>
                </div>
            </section>
            
            <section class="py-16">
                <div class="container mx-auto px-4">
                    <h2 class="text-3xl font-bold mb-8 text-center">Featured Products</h2>
                    <div id="featured-products" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Products will be loaded via JavaScript -->
                    </div>
                </div>
            </section>
        <?php else: ?>
            <div class="container mx-auto px-4 py-8">
                <h1 class="text-3xl font-bold mb-4">Page: <?php echo htmlspecialchars($page); ?></h1>
                <p>This page is under construction. The full application includes:</p>
                <ul class="list-disc ml-6 space-y-2 mt-4">
                    <li>Product Listing with Filters</li>
                    <li>Product Detail Pages</li>
                    <li>Shopping Cart</li>
                    <li>Checkout Flow</li>
                    <li>User Account Dashboard</li>
                    <li>Admin Panel</li>
                </ul>
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2024 EazzyBizzy. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
    <script>
        // Load featured products
        if (document.getElementById('featured-products')) {
            fetch('/api/products.php?action=featured&limit=8')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('featured-products');
                    if (data.success && data.data.products) {
                        container.innerHTML = data.data.products.map(product => `
                            <div class="card">
                                <img src="/assets/images/${product.primary_image || 'placeholder.jpg'}" alt="${product.name}" class="w-full h-48 object-cover rounded mb-4">
                                <h3 class="font-semibold mb-2">${product.name}</h3>
                                <p class="text-primary font-bold">$${product.sale_price || product.price}</p>
                                <button onclick="cart.add(${product.id})" class="btn-primary w-full mt-4">Add to Cart</button>
                            </div>
                        `).join('');
                    }
                })
                .catch(err => console.error('Failed to load products:', err));
        }
    </script>
</body>
</html>
