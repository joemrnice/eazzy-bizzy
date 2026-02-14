-- EazzyBizzy Database Seed File
-- Generated with realistic dummy data for e-commerce platform
-- All passwords are hashed with bcrypt cost 12
-- Password for all users/admins: password123

USE eazzybizzy_db;

SET foreign_key_checks = 0;
TRUNCATE TABLE activity_log;
TRUNCATE TABLE coupon_usage;
TRUNCATE TABLE shipments;
TRUNCATE TABLE order_items;
TRUNCATE TABLE orders;
TRUNCATE TABLE addresses;
TRUNCATE TABLE wishlist;
TRUNCATE TABLE carts;
TRUNCATE TABLE reviews;
TRUNCATE TABLE product_variants;
TRUNCATE TABLE product_attributes;
TRUNCATE TABLE product_images;
TRUNCATE TABLE products;
TRUNCATE TABLE brands;
TRUNCATE TABLE categories;
TRUNCATE TABLE users;
TRUNCATE TABLE admins;
TRUNCATE TABLE coupons;
TRUNCATE TABLE banners;
TRUNCATE TABLE pages;
TRUNCATE TABLE newsletter;
SET foreign_key_checks = 1;

-- =============================================
-- ADMINS (Bcrypt cost 12: password123)
-- =============================================
INSERT INTO admins (id, name, email, password, role, status) VALUES
(1, 'John Administrator', 'admin@eazzybizzy.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', 'super_admin', 'active'),
(2, 'Sarah Manager', 'manager@eazzybizzy.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', 'manager', 'active');

-- =============================================
-- USERS (Bcrypt cost 12: password123)
-- =============================================
INSERT INTO users (id, name, email, password, phone, email_verified, status) VALUES
(1, 'Michael Johnson', 'michael.johnson@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', '+1-555-0101', 1, 'active'),
(2, 'Emily Davis', 'emily.davis@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', '+1-555-0102', 1, 'active'),
(3, 'David Wilson', 'david.wilson@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', '+1-555-0103', 1, 'active'),
(4, 'Jennifer Brown', 'jennifer.brown@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', '+1-555-0104', 1, 'active'),
(5, 'Robert Taylor', 'robert.taylor@example.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni', '+1-555-0105', 1, 'active');

-- =============================================
-- CATEGORIES (8 Parent + 16 Child)
-- =============================================
INSERT INTO categories (id, parent_id, name, slug, description, icon, display_order, is_active) VALUES
-- Parent Categories
(1, NULL, 'Smartphones', 'smartphones', 'Latest smartphones and mobile devices', 'smartphone', 1, 1),
(2, NULL, 'Laptops', 'laptops', 'High-performance laptops and notebooks', 'laptop', 2, 1),
(3, NULL, 'TVs', 'tvs', 'Smart TVs and home entertainment', 'tv', 3, 1),
(4, NULL, 'Audio', 'audio', 'Headphones, speakers, and audio equipment', 'headphones', 4, 1),
(5, NULL, 'Cameras', 'cameras', 'Digital cameras and photography gear', 'camera', 5, 1),
(6, NULL, 'Appliances', 'appliances', 'Home and kitchen appliances', 'appliance', 6, 1),
(7, NULL, 'Gaming', 'gaming', 'Gaming consoles and accessories', 'gamepad', 7, 1),
(8, NULL, 'Accessories', 'accessories', 'Tech accessories and peripherals', 'accessories', 8, 1),

-- Child Categories - Smartphones
(9, 1, 'Android Phones', 'android-phones', 'Android smartphones from top brands', NULL, 1, 1),
(10, 1, 'iPhones', 'iphones', 'Apple iPhone series', NULL, 2, 1),

-- Child Categories - Laptops
(11, 2, 'Gaming Laptops', 'gaming-laptops', 'High-performance gaming laptops', NULL, 1, 1),
(12, 2, 'Business Laptops', 'business-laptops', 'Professional business laptops', NULL, 2, 1),
(13, 2, 'Ultrabooks', 'ultrabooks', 'Slim and lightweight ultrabooks', NULL, 3, 1),

-- Child Categories - TVs
(14, 3, 'Smart TVs', 'smart-tvs', 'Internet-connected smart televisions', NULL, 1, 1),
(15, 3, '4K TVs', '4k-tvs', 'Ultra HD 4K televisions', NULL, 2, 1),

-- Child Categories - Audio
(16, 4, 'Wireless Headphones', 'wireless-headphones', 'Bluetooth and wireless headphones', NULL, 1, 1),
(17, 4, 'Bluetooth Speakers', 'bluetooth-speakers', 'Portable Bluetooth speakers', NULL, 2, 1),
(18, 4, 'Soundbars', 'soundbars', 'Home theater soundbars', NULL, 3, 1),

-- Child Categories - Cameras
(19, 5, 'DSLR Cameras', 'dslr-cameras', 'Professional DSLR cameras', NULL, 1, 1),
(20, 5, 'Mirrorless Cameras', 'mirrorless-cameras', 'Compact mirrorless cameras', NULL, 2, 1),

-- Child Categories - Appliances
(21, 6, 'Refrigerators', 'refrigerators', 'Kitchen refrigerators and freezers', NULL, 1, 1),
(22, 6, 'Washing Machines', 'washing-machines', 'Automatic washing machines', NULL, 2, 1),

-- Child Categories - Gaming
(23, 7, 'Gaming Consoles', 'gaming-consoles', 'PlayStation, Xbox, and Nintendo', NULL, 1, 1),
(24, 7, 'Gaming Accessories', 'gaming-accessories', 'Controllers, headsets, and more', NULL, 2, 1);

-- =============================================
-- BRANDS (15 Brands)
-- =============================================
INSERT INTO brands (id, name, slug, logo, description, is_active) VALUES
(1, 'Apple', 'apple', 'brands/apple.png', 'Innovation that changes everything', 1),
(2, 'Samsung', 'samsung', 'brands/samsung.png', 'Inspire the world, create the future', 1),
(3, 'Sony', 'sony', 'brands/sony.png', 'Be moved by technology', 1),
(4, 'LG', 'lg', 'brands/lg.png', 'Life''s Good', 1),
(5, 'HP', 'hp', 'brands/hp.png', 'Keep reinventing', 1),
(6, 'Dell', 'dell', 'brands/dell.png', 'Technology that empowers', 1),
(7, 'Bose', 'bose', 'brands/bose.png', 'Better sound through research', 1),
(8, 'Canon', 'canon', 'brands/canon.png', 'Imaging solutions', 1),
(9, 'Nikon', 'nikon', 'brands/nikon.png', 'At the heart of the image', 1),
(10, 'Microsoft', 'microsoft', 'brands/microsoft.png', 'Empowering everyone', 1),
(11, 'Lenovo', 'lenovo', 'brands/lenovo.png', 'Smarter technology for all', 1),
(12, 'ASUS', 'asus', 'brands/asus.png', 'Inspiring innovation', 1),
(13, 'JBL', 'jbl', 'brands/jbl.png', 'Dare to listen', 1),
(14, 'Whirlpool', 'whirlpool', 'brands/whirlpool.png', 'Every day, care', 1),
(15, 'GE', 'ge', 'brands/ge.png', 'Building a world that works', 1);

-- =============================================
-- PRODUCTS (First 25 products)
-- =============================================
INSERT INTO products (id, category_id, brand_id, name, slug, sku, short_description, description, price, sale_price, stock, is_featured, is_active) VALUES
-- Smartphones (10 products)
(1, 10, 1, 'iPhone 15 Pro Max', 'iphone-15-pro-max', 'IP15PM-256-TB', 'Latest iPhone with A17 Pro chip', 'The iPhone 15 Pro Max features the powerful A17 Pro chip, ProMotion display, and advanced camera system with 5x optical zoom. Built with aerospace-grade titanium.', 1199.00, 1149.00, 45, 1, 1),
(2, 10, 1, 'iPhone 14', 'iphone-14', 'IP14-128-BL', 'Powerful iPhone 14 with advanced features', 'iPhone 14 with A15 Bionic chip, amazing camera capabilities, and all-day battery life. Available in stunning colors.', 799.00, NULL, 78, 1, 1),
(3, 9, 2, 'Samsung Galaxy S24 Ultra', 'samsung-galaxy-s24-ultra', 'SGS24U-512-BK', 'Premium Android flagship phone', 'Samsung Galaxy S24 Ultra with S Pen, 200MP camera, Snapdragon 8 Gen 3, and stunning 6.8" Dynamic AMOLED display.', 1299.99, 1249.99, 34, 1, 1),
(4, 9, 2, 'Samsung Galaxy S23', 'samsung-galaxy-s23', 'SGS23-256-GR', 'Compact flagship with great performance', 'Galaxy S23 with Snapdragon 8 Gen 2, exceptional camera, and sleek design. Perfect balance of size and power.', 799.99, 749.99, 62, 0, 1),
(5, 9, 2, 'Samsung Galaxy A54', 'samsung-galaxy-a54', 'SGA54-128-BL', 'Mid-range smartphone with premium features', 'Galaxy A54 with Super AMOLED display, 50MP OIS camera, and 5000mAh battery. Great value flagship killer.', 449.99, NULL, 120, 0, 1),
(6, 10, 1, 'iPhone 13', 'iphone-13', 'IP13-128-RD', 'Reliable iPhone with great features', 'iPhone 13 featuring A15 Bionic, dual-camera system, and Ceramic Shield. Available in vibrant colors.', 599.00, 549.00, 95, 0, 1),
(7, 9, 2, 'Samsung Galaxy Z Fold 5', 'samsung-galaxy-z-fold-5', 'SGZF5-256-GR', 'Revolutionary foldable smartphone', 'Galaxy Z Fold 5 with Flex Mode, multitasking capabilities, and ultra-thin design. The future of smartphones.', 1799.99, NULL, 18, 1, 1),
(8, 9, 2, 'Samsung Galaxy Z Flip 5', 'samsung-galaxy-z-flip-5', 'SGZF5F-256-LV', 'Compact foldable flip phone', 'Galaxy Z Flip 5 with larger cover screen, improved hinge, and pocket-friendly design. Fashion meets technology.', 999.99, 949.99, 42, 0, 1),
(9, 9, 3, 'Sony Xperia 5 V', 'sony-xperia-5-v', 'SXP5V-256-BK', 'Compact flagship with pro features', 'Xperia 5 V with 21:9 display, exceptional camera, and audio quality. Perfect for content creators.', 899.00, NULL, 28, 0, 1),
(10, 9, 2, 'Samsung Galaxy A34', 'samsung-galaxy-a34', 'SGA34-128-BL', 'Affordable smartphone with great display', 'Galaxy A34 with Super AMOLED 120Hz display, 48MP camera, and long-lasting battery. Best value option.', 349.99, 319.99, 156, 0, 1),

-- Laptops (15 products)
(11, 13, 1, 'MacBook Air M3', 'macbook-air-m3', 'MBA-M3-256-SG', 'Incredibly thin and powerful laptop', 'MacBook Air with M3 chip, 15.3" Liquid Retina display, and up to 18 hours battery life. Ultra-portable powerhouse.', 1299.00, NULL, 56, 1, 1),
(12, 13, 1, 'MacBook Pro 14" M3 Pro', 'macbook-pro-14-m3-pro', 'MBP14-M3P-512-SB', 'Professional laptop for creators', 'MacBook Pro 14" with M3 Pro chip, Liquid Retina XDR display, and exceptional performance for video editing.', 1999.00, 1899.00, 32, 1, 1),
(13, 11, 6, 'Dell G15 Gaming Laptop', 'dell-g15-gaming', 'DG15-RTX4060-512', 'Affordable gaming powerhouse', 'Dell G15 with RTX 4060, Intel i7-13650HX, 16GB RAM, 512GB SSD, and 165Hz display. Great gaming value.', 1199.99, 1099.99, 24, 0, 1),
(14, 11, 12, 'ASUS ROG Strix G16', 'asus-rog-strix-g16', 'AROG16-4070-1TB', 'Premium gaming laptop', 'ROG Strix G16 with RTX 4070, Intel i9-13980HX, 32GB RAM, QHD 240Hz display. Elite gaming experience.', 2199.99, NULL, 15, 1, 1),
(15, 12, 5, 'HP EliteBook 840 G10', 'hp-elitebook-840-g10', 'HPEB840-512-SV', 'Business laptop with security features', 'EliteBook 840 with Intel i7-1355U, 16GB RAM, 512GB SSD, and enterprise-grade security. Perfect for professionals.', 1499.00, NULL, 38, 0, 1),
(16, 12, 11, 'Lenovo ThinkPad X1 Carbon Gen 11', 'lenovo-thinkpad-x1-carbon-gen-11', 'LNTPX1C11-1TB', 'Ultra-light business laptop', 'ThinkPad X1 Carbon with Intel i7-1365U, 32GB RAM, 1TB SSD, and legendary keyboard. Business excellence.', 1899.00, 1799.00, 28, 1, 1),
(17, 13, 6, 'Dell XPS 13 Plus', 'dell-xps-13-plus', 'DXPS13P-512-PT', 'Sleek ultrabook with edge-to-edge display', 'XPS 13 Plus with Intel i7-1360P, 16GB RAM, stunning 13.4" OLED display. Premium ultraportable.', 1399.99, NULL, 42, 0, 1),
(18, 11, 12, 'ASUS TUF Gaming A15', 'asus-tuf-gaming-a15', 'ATUF15-4050-512', 'Durable gaming laptop', 'TUF Gaming A15 with RTX 4050, Ryzen 7 7735HS, 16GB RAM, military-grade durability. Budget gaming beast.', 999.99, 899.99, 48, 0, 1),
(19, 12, 5, 'HP Pavilion 15', 'hp-pavilion-15', 'HPP15-256-SV', 'Versatile everyday laptop', 'Pavilion 15 with Intel i5-1335U, 8GB RAM, 256GB SSD, and Full HD display. Perfect for daily tasks.', 649.99, 599.99, 85, 0, 1),
(20, 13, 10, 'Microsoft Surface Laptop 5', 'microsoft-surface-laptop-5', 'MSFL5-512-BK', 'Premium Windows laptop', 'Surface Laptop 5 with Intel i7-1255U, 16GB RAM, beautiful PixelSense touchscreen. Elegant design.', 1499.00, NULL, 35, 0, 1),
(21, 11, 11, 'Lenovo Legion Pro 5', 'lenovo-legion-pro-5', 'LNL5P-4060-1TB', 'Performance gaming laptop', 'Legion Pro 5 with RTX 4060, Ryzen 7 7745HX, 16GB RAM, WQHD 165Hz display. Serious gaming machine.', 1399.00, 1299.00, 31, 0, 1),
(22, 12, 6, 'Dell Latitude 5440', 'dell-latitude-5440', 'DL5440-512-BK', 'Reliable business laptop', 'Latitude 5440 with Intel i5-1345U, 16GB RAM, excellent battery life, and business features.', 1099.00, NULL, 52, 0, 1),
(23, 13, 1, 'MacBook Air M2', 'macbook-air-m2', 'MBA-M2-512-MN', 'Previous gen MacBook Air', 'MacBook Air with M2 chip, 13.6" display, great performance, and all-day battery. Still excellent.', 1099.00, 999.00, 67, 0, 1),
(24, 11, 5, 'HP Omen 16', 'hp-omen-16', 'HPO16-4070-1TB', 'High-performance gaming laptop', 'Omen 16 with RTX 4070, Intel i7-13700HX, 32GB RAM, QHD display. Gaming and creativity powerhouse.', 1799.00, NULL, 19, 0, 1),
(25, 12, 11, 'Lenovo IdeaPad Slim 5', 'lenovo-ideapad-slim-5', 'LNIPS5-512-GR', 'Affordable productivity laptop', 'IdeaPad Slim 5 with Ryzen 5 7530U, 16GB RAM, Full HD display. Great value for students and professionals.', 699.00, 649.00, 94, 0, 1);

-- TVs, Audio, Cameras, Appliances, Gaming, Accessories (55 more products)
INSERT INTO products (id, category_id, brand_id, name, slug, sku, short_description, description, price, sale_price, stock, is_featured, is_active) VALUES
-- TVs (10 products)
(26, 15, 2, 'Samsung 65" QLED 4K Smart TV', 'samsung-65-qled-4k', 'SQ65-4K-2024', 'Stunning QLED picture quality', 'Samsung 65" QLED with Quantum Processor 4K, billion shades of color, and smart TV features. Cinematic experience.', 1299.99, 1199.99, 22, 1, 1),
(27, 15, 4, 'LG 55" OLED 4K Smart TV', 'lg-55-oled-4k', 'LGO55-4K-C3', 'Perfect blacks with OLED technology', 'LG OLED C3 with self-lit pixels, α9 AI Processor, Dolby Vision, and gaming features. Picture perfection.', 1499.00, 1399.00, 18, 1, 1),
(28, 14, 3, 'Sony 55" Bravia XR 4K TV', 'sony-55-bravia-xr', 'SBX55-4K-X90K', 'Cognitive intelligence TV', 'Sony Bravia XR with Cognitive Processor XR, Full Array LED, and exceptional color. Sony excellence.', 1199.00, NULL, 25, 0, 1),
(29, 15, 2, 'Samsung 75" Neo QLED 8K TV', 'samsung-75-neo-qled-8k', 'SNQ75-8K-2024', 'Next-gen 8K resolution', 'Samsung Neo QLED 8K with Quantum Matrix Technology, Neural Quantum Processor, and infinity screen. Ultimate TV.', 2499.99, 2299.99, 8, 1, 1),
(30, 14, 4, 'LG 43" 4K Smart TV', 'lg-43-4k-smart', 'LG43-4K-UP75', 'Compact 4K smart TV', 'LG 43" with 4K upscaling, webOS, ThinQ AI, and sleek design. Perfect for bedrooms.', 379.99, 349.99, 68, 0, 1),
(31, 15, 3, 'Sony 65" Bravia XR OLED', 'sony-65-bravia-oled', 'SBO65-4K-A80L', 'Premium OLED experience', 'Sony A80L OLED with XR OLED Contrast Pro, Acoustic Surface Audio, perfect for PS5. Premium choice.', 1999.00, NULL, 14, 1, 1),
(32, 14, 2, 'Samsung 50" Crystal 4K TV', 'samsung-50-crystal-4k', 'SC50-4K-2024', 'Budget-friendly 4K TV', 'Samsung Crystal 4K with PurColor, HDR, and smart features. Great value 4K option.', 429.99, 399.99, 92, 0, 1),
(33, 15, 4, 'LG 77" OLED G3 4K TV', 'lg-77-oled-g3', 'LGO77-G3-2024', 'Gallery design OLED TV', 'LG G3 with Brightness Booster Max, gallery design, α9 Gen6 processor. Wall art that is a TV.', 2999.00, NULL, 6, 1, 1),
(34, 14, 3, 'Sony 43" Bravia 4K TV', 'sony-43-bravia-4k', 'SB43-4K-X80K', 'Compact Sony smart TV', 'Sony X80K with 4K HDR Processor X1, Triluminos Pro, and Google TV. Reliable Sony quality.', 499.00, 449.00, 54, 0, 1),
(35, 15, 2, 'Samsung 55" Frame TV', 'samsung-55-frame-tv', 'SFR55-4K-2024', 'Art meets television', 'Samsung Frame TV with Art Mode, customizable bezels, QLED 4K, and matte display. TV as art.', 1199.99, NULL, 31, 0, 1),

-- Audio (10 products)
(36, 16, 7, 'Bose QuietComfort Ultra', 'bose-quietcomfort-ultra', 'BQC-ULTRA-BK', 'Premium noise cancelling headphones', 'Bose QC Ultra with world-class ANC, Spatial Audio, and 24-hour battery. Silence perfected.', 429.00, 399.00, 78, 1, 1),
(37, 16, 3, 'Sony WH-1000XM5', 'sony-wh-1000xm5', 'SWH1000XM5-BK', 'Industry-leading ANC headphones', 'Sony XM5 with exceptional noise cancellation, 30-hour battery, multipoint connection. Audio excellence.', 399.00, 379.00, 124, 1, 1),
(38, 16, 1, 'Apple AirPods Max', 'apple-airpods-max', 'APM-SG-2023', 'Luxury over-ear headphones', 'AirPods Max with computational audio, Adaptive EQ, spatial audio, and premium build. Apple luxury.', 549.00, NULL, 45, 0, 1),
(39, 17, 13, 'JBL Flip 6', 'jbl-flip-6', 'JBLF6-BK', 'Portable waterproof speaker', 'JBL Flip 6 with powerful sound, IP67 waterproof, 12-hour playtime. Party anywhere speaker.', 129.99, 109.99, 186, 0, 1),
(40, 17, 7, 'Bose SoundLink Flex', 'bose-soundlink-flex', 'BSLF-BL', 'Rugged portable speaker', 'Bose SoundLink Flex with PositionIQ, waterproof design, and clear sound. Adventure ready.', 149.00, NULL, 142, 0, 1),
(41, 18, 7, 'Bose Smart Soundbar 900', 'bose-smart-soundbar-900', 'BSB900-BK', 'Premium Dolby Atmos soundbar', 'Bose 900 with Dolby Atmos, Voice4Video, ADAPTiQ calibration. Cinematic home audio.', 899.00, 849.00, 34, 1, 1),
(42, 18, 3, 'Sony HT-A7000', 'sony-ht-a7000', 'SHA7000-BK', 'High-end soundbar system', 'Sony HT-A7000 with 360 Spatial Sound, Dolby Atmos, DTS:X, and built-in subwoofers. Theater quality.', 1299.00, NULL, 22, 0, 1),
(43, 16, 13, 'JBL Tune 760NC', 'jbl-tune-760nc', 'JBLT760-BL', 'Affordable ANC headphones', 'JBL 760NC with active noise cancelling, 50-hour battery, and JBL Pure Bass. Budget ANC winner.', 129.99, 99.99, 215, 0, 1),
(44, 17, 13, 'JBL Charge 5', 'jbl-charge-5', 'JBLC5-GR', 'Powerful portable speaker', 'JBL Charge 5 with bold sound, powerbank feature, IP67 rated, 20-hour battery. Power and sound.', 179.99, 159.99, 168, 0, 1),
(45, 16, 3, 'Sony WF-1000XM5', 'sony-wf-1000xm5', 'SWF1000XM5-BK', 'Premium wireless earbuds', 'Sony XM5 earbuds with best-in-class ANC, 8-hour battery, LDAC support. True wireless excellence.', 299.00, 279.00, 96, 0, 1),

-- Cameras (10 products)
(46, 19, 8, 'Canon EOS R6 Mark II', 'canon-eos-r6-mark-ii', 'CER6M2-BODY', 'Professional mirrorless camera', 'Canon R6 II with 24.2MP, 40fps burst, 6K video, IBIS. Professional photography redefined.', 2499.00, NULL, 12, 1, 1),
(47, 19, 9, 'Nikon Z8', 'nikon-z8', 'NZ8-BODY', 'Flagship mirrorless camera', 'Nikon Z8 with 45.7MP, 8K video, 120fps burst, dual card slots. Nikon finest.', 3999.00, 3799.00, 8, 1, 1),
(48, 20, 3, 'Sony A7 IV', 'sony-a7-iv', 'SA7IV-BODY', 'Versatile full-frame camera', 'Sony A7 IV with 33MP, 4K 60p, incredible AF, IBIS. Perfect all-rounder camera.', 2499.00, 2399.00, 18, 1, 1),
(49, 19, 8, 'Canon EOS R5', 'canon-eos-r5', 'CER5-BODY', 'High-resolution powerhouse', 'Canon R5 with 45MP, 8K video, dual card slots, professional features. Content creator dream.', 3899.00, NULL, 10, 0, 1),
(50, 20, 3, 'Sony ZV-E10', 'sony-zv-e10', 'SZVE10-BK', 'Vlogging camera', 'Sony ZV-E10 with flip screen, product showcase, great AF. Perfect vlogging companion.', 699.00, 649.00, 45, 0, 1),
(51, 19, 9, 'Nikon D850', 'nikon-d850', 'ND850-BODY', 'Professional DSLR', 'Nikon D850 with 45.7MP, 7fps, 4K video, exceptional battery life. DSLR excellence.', 2999.00, NULL, 14, 0, 1),
(52, 20, 8, 'Canon EOS R10', 'canon-eos-r10', 'CER10-BODY', 'APS-C mirrorless camera', 'Canon R10 with 24.2MP, 4K 60p, 15fps burst, compact design. Enthusiast choice.', 979.00, 899.00, 38, 0, 1),
(53, 20, 3, 'Sony A6400', 'sony-a6400', 'SA6400-BODY', 'Compact APS-C camera', 'Sony A6400 with 24.2MP, fast AF, 4K video, flip screen. Travel photography perfect.', 899.00, NULL, 52, 0, 1),
(54, 19, 8, 'Canon EOS 90D', 'canon-eos-90d', 'CE90D-BODY', 'APS-C DSLR camera', 'Canon 90D with 32.5MP, 10fps, 4K video, weather-sealed. Versatile DSLR.', 1199.00, 1099.00, 26, 0, 1),
(55, 20, 9, 'Nikon Z30', 'nikon-z30', 'NZ30-BODY', 'Entry mirrorless for creators', 'Nikon Z30 with 4K video, flip screen, great AF, compact. Creator starter camera.', 709.00, 679.00, 41, 0, 1),

-- Appliances (10 products)
(56, 21, 4, 'LG French Door Refrigerator', 'lg-french-door-fridge', 'LGFDR-28-SS', '28 cu ft French door refrigerator', 'LG French Door with InstaView, Craft Ice, Smart Cooling. Premium refrigeration.', 2299.00, NULL, 15, 1, 1),
(57, 22, 14, 'Whirlpool Front Load Washer', 'whirlpool-front-load-washer', 'WPFL-45-WH', 'High-efficiency washing machine', 'Whirlpool washer with Load & Go dispenser, steam clean, 4.5 cu ft. Smart washing.', 899.00, 849.00, 28, 0, 1),
(58, 21, 2, 'Samsung Family Hub Refrigerator', 'samsung-family-hub-fridge', 'SFH-27-SS', 'Smart refrigerator with screen', 'Samsung Family Hub with 21.5" touchscreen, cameras, Bixby, meal planning. Kitchen command center.', 3299.00, 3099.00, 9, 1, 1),
(59, 22, 4, 'LG TurboWash Washer Dryer Combo', 'lg-turbowash-combo', 'LGTW-COMBO-WH', 'All-in-one washer and dryer', 'LG combo with AI DD, Steam, TurboWash, ventless. Space-saving laundry solution.', 1699.00, NULL, 22, 0, 1),
(60, 21, 15, 'GE Side-by-Side Refrigerator', 'ge-side-by-side-fridge', 'GESBS-25-SS', '25 cu ft side-by-side fridge', 'GE refrigerator with external dispenser, LED lighting, adjustable shelves. Classic reliability.', 1499.00, 1399.00, 31, 0, 1),
(61, 22, 2, 'Samsung FlexWash Washer', 'samsung-flexwash', 'SFLW-5-BL', 'Dual washer system', 'Samsung FlexWash with two washers, Super Speed, Steam. Wash smart, wash fast.', 1899.00, NULL, 18, 0, 1),
(62, 21, 14, 'Whirlpool Top Freezer Refrigerator', 'whirlpool-top-freezer', 'WPTF-18-WH', '18 cu ft top freezer fridge', 'Whirlpool refrigerator with LED lighting, glass shelves, energy efficient. Affordable quality.', 799.00, 749.00, 42, 0, 1),
(63, 22, 14, 'Whirlpool Top Load Washer', 'whirlpool-top-load-washer', 'WPTL-44-WH', 'Traditional top load washer', 'Whirlpool washer with Deep Water Wash, 4.4 cu ft, simple controls. Dependable washing.', 649.00, 599.00, 56, 0, 1),
(64, 21, 4, 'LG InstaView Refrigerator', 'lg-instaview-fridge', 'LGIV-26-SS', 'Knock-to-see refrigerator', 'LG InstaView with door-in-door, craft ice, smart features. See inside without opening.', 2599.00, NULL, 12, 0, 1),
(65, 22, 15, 'GE Front Load Washer', 'ge-front-load-washer', 'GEFL-48-WH', 'UltraFresh vent system washer', 'GE washer with UltraFresh, steam, WiFi, 4.8 cu ft. Smart and fresh laundry.', 1099.00, 999.00, 35, 0, 1),

-- Gaming (10 products)
(66, 23, 3, 'PlayStation 5', 'playstation-5', 'PS5-DISC-2024', 'Next-gen gaming console', 'PS5 with ultra-high-speed SSD, ray tracing, 4K gaming, DualSense controller. Gaming evolved.', 499.99, NULL, 68, 1, 1),
(67, 23, 10, 'Xbox Series X', 'xbox-series-x', 'XSX-1TB-2024', 'Most powerful Xbox ever', 'Xbox Series X with 12 teraflops, Quick Resume, Game Pass. True 4K gaming beast.', 499.99, NULL, 54, 1, 1),
(68, 23, 3, 'PlayStation 5 Digital', 'playstation-5-digital', 'PS5-DIGI-2024', 'All-digital PS5 console', 'PS5 Digital Edition with same power, no disc drive, sleeker design. Digital gaming future.', 449.99, 429.99, 82, 0, 1),
(69, 23, 10, 'Xbox Series S', 'xbox-series-s', 'XSS-512-2024', 'Compact Xbox console', 'Xbox Series S with 1440p gaming, 512GB SSD, Game Pass ready. Affordable next-gen.', 299.99, 279.99, 124, 0, 1),
(70, 24, 3, 'DualSense Wireless Controller', 'dualsense-controller', 'DS-WL-BK', 'PS5 wireless controller', 'DualSense with haptic feedback, adaptive triggers, built-in mic. Immersive gaming.', 69.99, 59.99, 245, 0, 1),
(71, 24, 10, 'Xbox Wireless Controller', 'xbox-wireless-controller', 'XWC-CARBON-BK', 'Xbox carbon black controller', 'Xbox controller with textured grip, Bluetooth, 40-hour battery. Classic gaming.', 59.99, 49.99, 286, 0, 1),
(72, 24, 3, 'PlayStation VR2', 'playstation-vr2', 'PSVR2-KIT', 'Next-gen VR for PS5', 'PSVR2 with 4K HDR, eye tracking, haptic feedback, 110° FOV. Virtual reality perfected.', 549.99, NULL, 32, 1, 1),
(73, 24, 10, 'Xbox Elite Controller Series 2', 'xbox-elite-controller-2', 'XELITE2-BK', 'Pro gaming controller', 'Elite 2 with adjustable tension, swappable components, 40-hour battery. Pro esports ready.', 179.99, 159.99, 76, 0, 1),
(74, 23, 3, 'PlayStation Portal', 'playstation-portal', 'PSP-REMOTE-WH', 'Remote play handheld', 'PlayStation Portal for PS5 remote play, 8" LCD, DualSense features. Play anywhere.', 199.99, NULL, 98, 0, 1),
(75, 24, 3, 'DualSense Edge Controller', 'dualsense-edge', 'DSE-PRO-WH', 'Pro PS5 controller', 'DualSense Edge with replaceable modules, back buttons, profiles. Competitive advantage.', 199.99, 189.99, 64, 0, 1),

-- Accessories (5 products)
(76, 8, 1, 'Apple AirTag 4-Pack', 'apple-airtag-4pack', 'AT4-PACK-WH', 'Item tracking devices', 'AirTag 4-pack with precision finding, replaceable battery, privacy built-in. Find anything.', 99.00, 89.00, 324, 0, 1),
(77, 8, 2, 'Samsung Fast Wireless Charger', 'samsung-fast-wireless-charger', 'SFWC-15W-BK', '15W wireless charging pad', 'Samsung wireless charger with fast charging, LED indicator, sleep-friendly. Charge wirelessly.', 39.99, 34.99, 486, 0, 1),
(78, 8, 1, 'Apple MagSafe Charger', 'apple-magsafe-charger', 'AMC-15W-WH', 'Magnetic wireless charger', 'MagSafe charger for iPhone with perfect alignment, 15W fast charging. Snap and charge.', 39.00, NULL, 412, 0, 1),
(79, 8, 12, 'ASUS ROG Gaming Mouse', 'asus-rog-gaming-mouse', 'AROGM-26K-BK', 'Professional gaming mouse', 'ROG mouse with 26,000 DPI, wireless, programmable buttons, RGB. Precision gaming.', 89.99, 79.99, 158, 0, 1),
(80, 8, 11, 'Lenovo USB-C Hub', 'lenovo-usb-c-hub', 'LNUSBC-7PORT', '7-in-1 USB-C hub', 'Lenovo hub with HDMI, USB 3.0, card reader, PD charging. Connectivity solved.', 49.99, 44.99, 276, 0, 1);

-- =============================================
-- PRODUCT IMAGES (Sample images for products)
-- =============================================
INSERT INTO product_images (product_id, image, display_order, is_primary) VALUES
-- Smartphones
(1, 'products/iphone-15-pro-max-01.jpg', 1, 1), (1, 'products/iphone-15-pro-max-02.jpg', 2, 0), (1, 'products/iphone-15-pro-max-03.jpg', 3, 0),
(2, 'products/iphone-14-01.jpg', 1, 1), (2, 'products/iphone-14-02.jpg', 2, 0),
(3, 'products/samsung-s24-ultra-01.jpg', 1, 1), (3, 'products/samsung-s24-ultra-02.jpg', 2, 0), (3, 'products/samsung-s24-ultra-03.jpg', 3, 0),
(4, 'products/samsung-s23-01.jpg', 1, 1), (4, 'products/samsung-s23-02.jpg', 2, 0),
(5, 'products/samsung-a54-01.jpg', 1, 1),
(6, 'products/iphone-13-01.jpg', 1, 1), (6, 'products/iphone-13-02.jpg', 2, 0),
(7, 'products/samsung-fold5-01.jpg', 1, 1), (7, 'products/samsung-fold5-02.jpg', 2, 0),
(8, 'products/samsung-flip5-01.jpg', 1, 1), (8, 'products/samsung-flip5-02.jpg', 2, 0),
(9, 'products/sony-xperia5v-01.jpg', 1, 1),
(10, 'products/samsung-a34-01.jpg', 1, 1),
-- Laptops
(11, 'products/macbook-air-m3-01.jpg', 1, 1), (11, 'products/macbook-air-m3-02.jpg', 2, 0),
(12, 'products/macbook-pro-14-01.jpg', 1, 1), (12, 'products/macbook-pro-14-02.jpg', 2, 0),
(13, 'products/dell-g15-01.jpg', 1, 1),
(14, 'products/asus-rog-g16-01.jpg', 1, 1), (14, 'products/asus-rog-g16-02.jpg', 2, 0),
(15, 'products/hp-elitebook-01.jpg', 1, 1),
(16, 'products/thinkpad-x1-01.jpg', 1, 1), (16, 'products/thinkpad-x1-02.jpg', 2, 0),
(17, 'products/dell-xps13-01.jpg', 1, 1),
(18, 'products/asus-tuf-a15-01.jpg', 1, 1),
(19, 'products/hp-pavilion-01.jpg', 1, 1),
(20, 'products/surface-laptop-01.jpg', 1, 1),
-- TVs
(26, 'products/samsung-qled-65-01.jpg', 1, 1), (26, 'products/samsung-qled-65-02.jpg', 2, 0),
(27, 'products/lg-oled-55-01.jpg', 1, 1), (27, 'products/lg-oled-55-02.jpg', 2, 0),
(28, 'products/sony-bravia-55-01.jpg', 1, 1),
(29, 'products/samsung-neo-75-01.jpg', 1, 1), (29, 'products/samsung-neo-75-02.jpg', 2, 0),
(30, 'products/lg-43-01.jpg', 1, 1),
-- Audio
(36, 'products/bose-qc-ultra-01.jpg', 1, 1), (36, 'products/bose-qc-ultra-02.jpg', 2, 0),
(37, 'products/sony-xm5-01.jpg', 1, 1), (37, 'products/sony-xm5-02.jpg', 2, 0),
(38, 'products/airpods-max-01.jpg', 1, 1),
(39, 'products/jbl-flip6-01.jpg', 1, 1),
(41, 'products/bose-soundbar-01.jpg', 1, 1),
-- Cameras
(46, 'products/canon-r6ii-01.jpg', 1, 1), (46, 'products/canon-r6ii-02.jpg', 2, 0),
(47, 'products/nikon-z8-01.jpg', 1, 1), (47, 'products/nikon-z8-02.jpg', 2, 0),
(48, 'products/sony-a7iv-01.jpg', 1, 1),
-- Appliances
(56, 'products/lg-french-door-01.jpg', 1, 1),
(57, 'products/whirlpool-washer-01.jpg', 1, 1),
(58, 'products/samsung-family-hub-01.jpg', 1, 1), (58, 'products/samsung-family-hub-02.jpg', 2, 0),
-- Gaming
(66, 'products/ps5-01.jpg', 1, 1), (66, 'products/ps5-02.jpg', 2, 0),
(67, 'products/xbox-series-x-01.jpg', 1, 1), (67, 'products/xbox-series-x-02.jpg', 2, 0),
(68, 'products/ps5-digital-01.jpg', 1, 1),
(69, 'products/xbox-series-s-01.jpg', 1, 1),
(70, 'products/dualsense-01.jpg', 1, 1),
(72, 'products/psvr2-01.jpg', 1, 1), (72, 'products/psvr2-02.jpg', 2, 0),
-- Accessories
(76, 'products/airtag-01.jpg', 1, 1),
(77, 'products/samsung-charger-01.jpg', 1, 1),
(78, 'products/magsafe-01.jpg', 1, 1),
(79, 'products/asus-mouse-01.jpg', 1, 1),
(80, 'products/lenovo-hub-01.jpg', 1, 1);

-- =============================================
-- PRODUCT ATTRIBUTES (Specifications)
-- =============================================
INSERT INTO product_attributes (product_id, attribute_name, attribute_value) VALUES
-- iPhone 15 Pro Max
(1, 'Display', '6.7" Super Retina XDR OLED'),
(1, 'Processor', 'A17 Pro chip'),
(1, 'Storage', '256GB'),
(1, 'Camera', '48MP Main + 12MP Ultra Wide + 12MP Telephoto'),
(1, 'Battery', 'Up to 29 hours video playback'),
(1, 'Color', 'Titanium Blue'),
-- Samsung Galaxy S24 Ultra
(3, 'Display', '6.8" Dynamic AMOLED 2X'),
(3, 'Processor', 'Snapdragon 8 Gen 3'),
(3, 'Storage', '512GB'),
(3, 'Camera', '200MP Main + 12MP Ultra Wide + 10MP + 10MP Telephoto'),
(3, 'RAM', '12GB'),
(3, 'S Pen', 'Included'),
-- MacBook Air M3
(11, 'Display', '15.3" Liquid Retina'),
(11, 'Processor', 'Apple M3 chip'),
(11, 'RAM', '8GB Unified Memory'),
(11, 'Storage', '256GB SSD'),
(11, 'Battery', 'Up to 18 hours'),
(11, 'Weight', '3.3 lbs'),
-- Samsung 65" QLED TV
(26, 'Screen Size', '65 inches'),
(26, 'Resolution', '4K UHD (3840 x 2160)'),
(26, 'HDR', 'Quantum HDR'),
(26, 'Smart TV', 'Tizen OS'),
(26, 'Refresh Rate', '120Hz'),
(26, 'Ports', '4x HDMI 2.1, 2x USB'),
-- Bose QuietComfort Ultra
(36, 'Type', 'Over-ear wireless'),
(36, 'Noise Cancellation', 'Active (World-class)'),
(36, 'Battery Life', '24 hours'),
(36, 'Connectivity', 'Bluetooth 5.3'),
(36, 'Spatial Audio', 'Immersive Audio'),
(36, 'Weight', '254g'),
-- Canon EOS R6 Mark II
(46, 'Sensor', '24.2MP Full-Frame CMOS'),
(46, 'Video', '6K Raw, 4K 60p'),
(46, 'Burst Speed', '40 fps electronic'),
(46, 'ISO Range', '100-102,400'),
(46, 'Image Stabilization', '5-axis In-Body'),
(46, 'Viewfinder', '3.69M-dot EVF'),
-- LG French Door Refrigerator
(56, 'Capacity', '28 cu ft'),
(56, 'Type', 'French Door'),
(56, 'Ice Maker', 'Craft Ice (Sphere)'),
(56, 'InstaView', 'Knock to see inside'),
(56, 'Cooling', 'Smart Cooling Plus'),
(56, 'Energy Star', 'Certified'),
-- PlayStation 5
(66, 'Storage', '825GB SSD'),
(66, 'Resolution', 'Up to 4K 120Hz'),
(66, 'Ray Tracing', 'Hardware-based'),
(66, 'Controller', 'DualSense with haptic feedback'),
(66, 'Ports', 'HDMI 2.1, USB-C, USB-A'),
(66, 'Dimensions', '15.4 x 10.2 x 4.1 inches'),
-- Dell G15 Gaming Laptop
(13, 'Display', '15.6" FHD 165Hz'),
(13, 'Processor', 'Intel Core i7-13650HX'),
(13, 'Graphics', 'NVIDIA RTX 4060 8GB'),
(13, 'RAM', '16GB DDR5'),
(13, 'Storage', '512GB NVMe SSD'),
(13, 'Operating System', 'Windows 11 Home'),
-- Sony WH-1000XM5
(37, 'Type', 'Over-ear wireless'),
(37, 'Noise Cancellation', 'Industry-leading ANC'),
(37, 'Battery Life', '30 hours'),
(37, 'Charging', 'USB-C Quick Charge'),
(37, 'Codec Support', 'LDAC, AAC'),
(37, 'Multipoint', 'Connect 2 devices');

-- =============================================
-- PRODUCT REVIEWS (22 reviews with varying ratings)
-- =============================================
INSERT INTO reviews (product_id, user_id, rating, title, body, verified_purchase, status, created_at) VALUES
(1, 1, 5, 'Amazing iPhone!', 'The iPhone 15 Pro Max is absolutely incredible. The camera quality is outstanding and the titanium build feels premium. Battery life easily lasts me through the day. Highly recommended!', 1, 'approved', '2024-01-15 10:30:00'),
(1, 2, 5, 'Best iPhone yet', 'Upgraded from iPhone 12 and the difference is night and day. The A17 Pro chip is blazing fast and the 5x zoom is perfect for photography.', 1, 'approved', '2024-01-20 14:22:00'),
(1, 3, 4, 'Great but pricey', 'Fantastic phone with amazing features, but the price is quite steep. If you can afford it, definitely worth it.', 1, 'approved', '2024-02-01 09:15:00'),
(3, 4, 5, 'S Pen is game changer', 'The S24 Ultra with S Pen is perfect for productivity. The 200MP camera takes stunning photos and the screen is gorgeous. Best Android phone!', 1, 'approved', '2024-01-18 16:45:00'),
(3, 5, 5, 'Perfect flagship', 'Switched from iPhone and loving it. The customization options are endless and the phone feels incredibly fast.', 1, 'approved', '2024-01-25 11:30:00'),
(11, 1, 5, 'Perfect laptop for travel', 'The MacBook Air M3 is incredibly thin and light. Battery life is amazing - I can work all day without charging. The M3 chip handles everything I throw at it.', 1, 'approved', '2024-02-05 13:20:00'),
(11, 3, 5, 'Love this MacBook', 'Best laptop I have ever owned. Silent operation, beautiful display, and the battery life is unreal. Worth every penny!', 1, 'approved', '2024-02-10 10:15:00'),
(12, 2, 5, 'Pro-level performance', 'The MacBook Pro 14 with M3 Pro handles 4K video editing like a breeze. The display is stunning and the speakers are incredible.', 1, 'approved', '2024-01-28 15:40:00'),
(13, 4, 4, 'Great gaming laptop', 'Dell G15 offers excellent gaming performance for the price. The 165Hz display is smooth and RTX 4060 handles all games well. Gets a bit hot under load.', 1, 'approved', '2024-02-03 19:25:00'),
(13, 5, 4, 'Good value', 'For the price, this is a solid gaming laptop. Not the prettiest design but performs well. Recommend getting a cooling pad.', 1, 'approved', '2024-02-08 21:10:00'),
(26, 1, 5, 'Stunning picture quality', 'The QLED technology delivers vibrant colors and deep blacks. Smart features work flawlessly. Best TV purchase ever!', 1, 'approved', '2024-01-22 20:30:00'),
(27, 2, 5, 'OLED perfection', 'LG OLED is simply the best. Perfect blacks, infinite contrast, and great for gaming. Worth the premium price.', 1, 'approved', '2024-02-02 18:45:00'),
(36, 3, 5, 'Best noise cancelling', 'Bose QuietComfort Ultra has incredible ANC. Complete silence when activated. Comfortable for long flights. Audio quality is top-notch.', 1, 'approved', '2024-01-30 12:15:00'),
(36, 4, 4, 'Great headphones', 'Excellent noise cancellation and comfort. Only downside is the price, but you get what you pay for.', 1, 'approved', '2024-02-06 14:50:00'),
(37, 5, 5, 'Sony excellence', 'Sony XM5 has amazing sound quality and the best ANC I have experienced. Battery lasts forever. Highly recommend!', 1, 'approved', '2024-01-27 11:20:00'),
(46, 1, 5, 'Professional powerhouse', 'Canon R6 Mark II is a beast. 40fps burst is insane and the autofocus never misses. Perfect for wildlife and sports photography.', 1, 'approved', '2024-02-04 09:30:00'),
(48, 2, 5, 'Perfect all-rounder', 'Sony A7 IV does everything well. Great for photos and video. The autofocus is incredible and image quality is outstanding.', 1, 'approved', '2024-01-29 16:40:00'),
(66, 3, 5, 'Gaming perfection', 'PS5 is amazing! Games load instantly with the SSD. Graphics are stunning and the DualSense controller is revolutionary. Love it!', 1, 'approved', '2024-01-16 20:15:00'),
(66, 4, 5, 'Next-gen is here', 'The PS5 delivers on all fronts. Ray tracing looks incredible and 4K 60fps is smooth. Game library keeps growing.', 1, 'approved', '2024-01-24 19:30:00'),
(66, 5, 4, 'Great console', 'Loving the PS5! Only complaint is the storage fills up quickly, but overall an amazing gaming experience.', 1, 'approved', '2024-02-07 18:20:00'),
(67, 1, 5, 'Xbox is back!', 'Xbox Series X is powerful. Quick Resume is a game changer and Game Pass offers incredible value. Best Xbox ever made.', 1, 'approved', '2024-01-19 17:45:00'),
(67, 3, 5, 'Powerful beast', 'The Series X runs games beautifully at 4K 60fps. Fast loading times and Game Pass makes it an easy recommendation.', 1, 'approved', '2024-02-09 16:55:00');

-- =============================================
-- COUPONS (3 active coupons)
-- =============================================
INSERT INTO coupons (id, code, type, value, min_order_amount, max_discount, usage_limit, used_count, per_user_limit, starts_at, expires_at, is_active) VALUES
(1, 'WELCOME50', 'fixed', 50.00, 200.00, NULL, 1000, 45, 1, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 1),
(2, 'SAVE15', 'percentage', 15.00, 100.00, 200.00, NULL, 128, 3, '2024-01-01 00:00:00', '2024-12-31 23:59:59', 1),
(3, 'ELECTRONICS20', 'percentage', 20.00, 500.00, 300.00, 500, 67, 2, '2024-02-01 00:00:00', '2024-06-30 23:59:59', 1);

-- =============================================
-- BANNERS (2 hero banners for homepage)
-- =============================================
INSERT INTO banners (id, title, subtitle, image, link, button_text, display_order, is_active) VALUES
(1, 'Spring Sale 2024', 'Up to 50% off on Electronics', 'banners/spring-sale-2024.jpg', '/products?sale=true', 'Shop Now', 1, 1),
(2, 'New Gaming Consoles', 'PlayStation 5 & Xbox Series X In Stock', 'banners/gaming-consoles-banner.jpg', '/category/gaming-consoles', 'Explore Gaming', 2, 1);

-- =============================================
-- STATIC PAGES (3 pages)
-- =============================================
INSERT INTO pages (id, title, slug, content, meta_title, meta_description, is_active) VALUES
(1, 'About Us', 'about-us', 
'<h1>About EazzyBizzy</h1>
<p>Welcome to EazzyBizzy, your trusted destination for the latest electronics and home appliances. Founded in 2020, we have grown to become one of the leading online retailers in the electronics space.</p>

<h2>Our Mission</h2>
<p>At EazzyBizzy, our mission is to make technology accessible to everyone. We believe that high-quality electronics should be available at competitive prices with exceptional customer service.</p>

<h2>Why Choose Us?</h2>
<ul>
<li><strong>Authentic Products:</strong> We only sell 100% genuine products from authorized distributors.</li>
<li><strong>Best Prices:</strong> Our competitive pricing ensures you get the best value for your money.</li>
<li><strong>Fast Shipping:</strong> Most orders ship within 24 hours and arrive within 3-5 business days.</li>
<li><strong>Excellent Support:</strong> Our customer support team is available 7 days a week to assist you.</li>
<li><strong>Secure Shopping:</strong> Your data is protected with industry-standard encryption.</li>
</ul>

<h2>Our Product Range</h2>
<p>We offer a wide range of products including smartphones, laptops, TVs, audio equipment, cameras, home appliances, gaming consoles, and accessories from top brands like Apple, Samsung, Sony, LG, HP, Dell, and many more.</p>

<h2>Contact Us</h2>
<p>Have questions? Our customer service team is here to help!</p>
<p>Email: support@eazzybizzy.com<br>
Phone: 1-800-EAZZY-BIZ<br>
Hours: Monday-Friday 9AM-6PM EST</p>',
'About EazzyBizzy - Your Trusted Electronics Retailer',
'Learn about EazzyBizzy, your trusted online destination for electronics, home appliances, and technology products.',
1),

(2, 'Privacy Policy', 'privacy-policy',
'<h1>Privacy Policy</h1>
<p><em>Last Updated: February 14, 2024</em></p>

<h2>Introduction</h2>
<p>EazzyBizzy ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or make a purchase.</p>

<h2>Information We Collect</h2>
<h3>Personal Information</h3>
<p>We collect personal information that you voluntarily provide to us when you:</p>
<ul>
<li>Register for an account</li>
<li>Make a purchase</li>
<li>Subscribe to our newsletter</li>
<li>Contact customer support</li>
<li>Participate in surveys or promotions</li>
</ul>

<p>This information may include:</p>
<ul>
<li>Name and contact information (email, phone number, shipping address)</li>
<li>Payment information (credit card details, billing address)</li>
<li>Account credentials (username, password)</li>
<li>Purchase history and preferences</li>
</ul>

<h3>Automatically Collected Information</h3>
<p>When you visit our website, we automatically collect certain information about your device, including:</p>
<ul>
<li>IP address</li>
<li>Browser type and version</li>
<li>Device type</li>
<li>Pages visited and time spent</li>
<li>Referring website</li>
</ul>

<h2>How We Use Your Information</h2>
<p>We use the information we collect to:</p>
<ul>
<li>Process and fulfill your orders</li>
<li>Communicate with you about your orders and account</li>
<li>Send marketing communications (with your consent)</li>
<li>Improve our website and services</li>
<li>Prevent fraud and enhance security</li>
<li>Comply with legal obligations</li>
</ul>

<h2>Information Sharing</h2>
<p>We do not sell your personal information. We may share your information with:</p>
<ul>
<li>Service providers (payment processors, shipping companies)</li>
<li>Legal authorities when required by law</li>
<li>Business partners (with your consent)</li>
</ul>

<h2>Data Security</h2>
<p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the Internet is 100% secure.</p>

<h2>Your Rights</h2>
<p>You have the right to:</p>
<ul>
<li>Access your personal information</li>
<li>Correct inaccurate information</li>
<li>Request deletion of your information</li>
<li>Opt-out of marketing communications</li>
<li>Object to processing of your information</li>
</ul>

<h2>Cookies</h2>
<p>We use cookies and similar tracking technologies to enhance your browsing experience. You can control cookies through your browser settings.</p>

<h2>Children''s Privacy</h2>
<p>Our website is not intended for children under 13 years of age. We do not knowingly collect personal information from children.</p>

<h2>Changes to This Policy</h2>
<p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last Updated" date.</p>

<h2>Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at:<br>
Email: privacy@eazzybizzy.com<br>
Phone: 1-800-EAZZY-BIZ</p>',
'Privacy Policy - EazzyBizzy',
'EazzyBizzy Privacy Policy - Learn how we collect, use, and protect your personal information.',
1),

(3, 'Terms and Conditions', 'terms-conditions',
'<h1>Terms and Conditions</h1>
<p><em>Last Updated: February 14, 2024</em></p>

<h2>Agreement to Terms</h2>
<p>By accessing and using EazzyBizzy ("the Website"), you accept and agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our Website.</p>

<h2>Use of Website</h2>
<h3>Eligibility</h3>
<p>You must be at least 18 years old to make purchases on our Website. By using the Website, you represent that you meet this age requirement.</p>

<h3>Account Responsibilities</h3>
<p>When you create an account, you are responsible for:</p>
<ul>
<li>Maintaining the confidentiality of your account credentials</li>
<li>All activities that occur under your account</li>
<li>Notifying us immediately of any unauthorized use</li>
</ul>

<h2>Product Information</h2>
<p>We strive to provide accurate product descriptions, images, and pricing. However, we do not warrant that:</p>
<ul>
<li>Product descriptions are accurate, complete, or error-free</li>
<li>Images exactly represent the product color or appearance</li>
<li>All products will be available at all times</li>
</ul>

<h2>Pricing and Payment</h2>
<ul>
<li>All prices are in USD and subject to change without notice</li>
<li>We reserve the right to correct pricing errors</li>
<li>Payment must be received before we process your order</li>
<li>We accept major credit cards and other payment methods as displayed</li>
</ul>

<h2>Orders and Fulfillment</h2>
<h3>Order Acceptance</h3>
<p>We reserve the right to refuse or cancel any order for reasons including but not limited to:</p>
<ul>
<li>Product availability</li>
<li>Errors in pricing or product information</li>
<li>Suspected fraudulent activity</li>
</ul>

<h3>Shipping</h3>
<p>Shipping times and costs vary based on destination and shipping method selected. We are not responsible for delays caused by shipping carriers.</p>

<h2>Returns and Refunds</h2>
<p>We accept returns within 30 days of delivery for most products. Items must be:</p>
<ul>
<li>In original condition and packaging</li>
<li>Unused and undamaged</li>
<li>Accompanied by proof of purchase</li>
</ul>

<p>Some items may not be eligible for return. Refunds will be processed within 5-10 business days after receiving the returned item.</p>

<h2>Warranties</h2>
<p>Products are covered by manufacturer warranties. We are not responsible for warranty claims, but we will assist in facilitating the process.</p>

<h2>Limitation of Liability</h2>
<p>To the maximum extent permitted by law, EazzyBizzy shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the Website or products purchased.</p>

<h2>Intellectual Property</h2>
<p>All content on the Website, including text, graphics, logos, and images, is the property of EazzyBizzy or its licensors and is protected by copyright and trademark laws.</p>

<h2>User Content</h2>
<p>By submitting reviews, comments, or other content, you grant us a non-exclusive, royalty-free, perpetual license to use, reproduce, and display such content.</p>

<h2>Privacy</h2>
<p>Your use of the Website is also governed by our Privacy Policy. Please review our Privacy Policy to understand our practices.</p>

<h2>Modifications</h2>
<p>We reserve the right to modify these Terms and Conditions at any time. Changes will be effective immediately upon posting on the Website.</p>

<h2>Governing Law</h2>
<p>These Terms and Conditions are governed by the laws of the United States. Any disputes shall be resolved in the courts of [Your State/Jurisdiction].</p>

<h2>Contact Information</h2>
<p>For questions about these Terms and Conditions, please contact:<br>
Email: legal@eazzybizzy.com<br>
Phone: 1-800-EAZZY-BIZ<br>
Address: EazzyBizzy Inc., 123 Tech Street, Silicon Valley, CA 94000</p>',
'Terms and Conditions - EazzyBizzy',
'EazzyBizzy Terms and Conditions - Review our terms of use, purchase policies, and legal agreements.',
1);

-- =============================================
-- NEWSLETTER SUBSCRIPTIONS (Sample subscriptions)
-- =============================================
INSERT INTO newsletter (id, email, token, status) VALUES
(1, 'john.smith@example.com', 'a1b2c3d4e5f6g7h8i9j0', 'subscribed'),
(2, 'sarah.jones@example.com', 'k1l2m3n4o5p6q7r8s9t0', 'subscribed'),
(3, 'mike.williams@example.com', 'u1v2w3x4y5z6a7b8c9d0', 'subscribed'),
(4, 'emma.davis@example.com', 'e1f2g3h4i5j6k7l8m9n0', 'subscribed'),
(5, 'alex.rodriguez@example.com', 'o1p2q3r4s5t6u7v8w9x0', 'subscribed'),
(6, 'lisa.martinez@example.com', 'y1z2a3b4c5d6e7f8g9h0', 'subscribed'),
(7, 'chris.garcia@example.com', 'i1j2k3l4m5n6o7p8q9r0', 'subscribed'),
(8, 'amanda.wilson@example.com', 's1t2u3v4w5x6y7z8a9b0', 'unsubscribed'),
(9, 'david.taylor@example.com', 'c1d2e3f4g5h6i7j8k9l0', 'subscribed'),
(10, 'jessica.anderson@example.com', 'm1n2o3p4q5r6s7t8u9v0', 'subscribed');

-- =============================================
-- END OF SEED FILE
-- =============================================

