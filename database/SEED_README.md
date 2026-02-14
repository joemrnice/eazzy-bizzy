# EazzyBizzy Database Seed File

## Overview
Comprehensive seed file for the EazzyBizzy e-commerce platform with production-ready dummy data.

## File Information
- **File**: `seed.sql`
- **Total Lines**: 614
- **Database**: `eazzybizzy_db`

## Contents Summary

### 1. Admin Users (2)
- **Super Admin**: admin@eazzybizzy.com (password: password123)
- **Manager**: manager@eazzybizzy.com (password: password123)
- All passwords hashed with bcrypt cost 12

### 2. Regular Users (5)
- michael.johnson@example.com
- emily.davis@example.com
- david.wilson@example.com
- jennifer.brown@example.com
- robert.taylor@example.com
- Password for all: password123 (bcrypt cost 12)

### 3. Categories (24 total)
**8 Parent Categories:**
1. Smartphones
2. Laptops
3. TVs
4. Audio
5. Cameras
6. Appliances
7. Gaming
8. Accessories

**16 Child Categories:**
- Android Phones, iPhones
- Gaming Laptops, Business Laptops, Ultrabooks
- Smart TVs, 4K TVs
- Wireless Headphones, Bluetooth Speakers, Soundbars
- DSLR Cameras, Mirrorless Cameras
- Refrigerators, Washing Machines
- Gaming Consoles, Gaming Accessories

### 4. Brands (15)
Apple, Samsung, Sony, LG, HP, Dell, Bose, Canon, Nikon, Microsoft, Lenovo, ASUS, JBL, Whirlpool, GE

### 5. Products (80)
**Distribution:**
- Smartphones: 10 products ($349-$1,799)
- Laptops: 15 products ($649-$2,199)
- TVs: 10 products ($379-$2,999)
- Audio: 10 products ($99-$1,299)
- Cameras: 10 products ($709-$3,999)
- Appliances: 10 products ($649-$3,299)
- Gaming: 10 products ($59-$549)
- Accessories: 5 products ($39-$99)

**Features:**
- Realistic pricing ($20-$2,500 range)
- Varying stock levels (6-486 units)
- 15 featured products
- 30 products with sale prices
- Detailed descriptions and specifications

### 6. Product Images (65+)
- Primary and secondary images for products
- Placeholder paths: `products/[product-name]-01.jpg`

### 7. Product Attributes (60+)
Detailed specifications for key products including:
- Display specs
- Processor details
- Storage & RAM
- Camera specifications
- Battery life
- Connectivity
- Dimensions & weight

### 8. Product Reviews (22)
- Ratings: 4-5 stars
- Verified purchases
- Approved status
- Realistic review content
- Created dates: January-February 2024

### 9. Coupons (3)
1. **WELCOME50** - $50 off orders over $200 (fixed)
2. **SAVE15** - 15% off orders over $100 (percentage, max $200)
3. **ELECTRONICS20** - 20% off orders over $500 (percentage, max $300)

### 10. Banners (2)
1. Spring Sale 2024 - Up to 50% off Electronics
2. New Gaming Consoles - PS5 & Xbox Series X

### 11. Static Pages (3)
1. **About Us** - Company information and mission
2. **Privacy Policy** - Comprehensive privacy policy
3. **Terms and Conditions** - Legal terms and conditions

### 12. Newsletter Subscriptions (10)
- 9 active subscriptions
- 1 unsubscribed user

## Usage Instructions

### Import the seed file:
```bash
mysql -u username -p < seed.sql
```

Or from MySQL prompt:
```sql
SOURCE /path/to/seed.sql;
```

### Default Credentials:
**Admin Login:**
- Email: admin@eazzybizzy.com
- Password: password123

**Manager Login:**
- Email: manager@eazzybizzy.com
- Password: password123

**User Login (any of 5 users):**
- Email: michael.johnson@example.com (or others)
- Password: password123

## Password Hash Information
All passwords use bcrypt with cost factor 12:
- Hash: `$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5NU/MvJCvF5ni`
- Plaintext: `password123`

## Notes
- Foreign key checks are disabled during truncation and re-enabled after
- All data is production-ready with realistic values
- Product images use placeholder paths that need actual image files
- Timestamps use 2024 dates for reviews
- All monetary values are in USD

## Data Integrity
- All foreign key relationships are properly maintained
- No orphaned records
- Consistent data across related tables
- Realistic stock levels and pricing

