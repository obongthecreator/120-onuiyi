# 120 Stand Inventory Management System

A comprehensive WordPress plugin for inventory management designed for 120 Stand fruit salad business.

## Features

- **Take Order**: Process customer orders with automatic calculations
- **Order Preparation**: Track daily fruit preparation (opening, added, sold, closing)
- **Stock Inventory**: Monitor pack levels for fruits and non-fruit items
- **Chopping Inventory**: Record whole fruit processing
- **Import Record**: Log daily product imports with real-time sync
- **Product Summary**: View sales analytics and breakdowns
- **Financial Summary**: Track daily finances and cash reconciliation
- **Admin Panel**: Manage products, staff, and opening values
- **Offline Support**: Take orders offline with automatic sync
- **Beautiful UI**: Dark red theme with glassmorphism design

## Installation

1. Download the `120-stand-inventory` folder
2. Upload to your WordPress `wp-content/plugins/` directory
3. Activate the plugin from WordPress admin
4. Visit `/120-stand/` to access the system

## Default Access

After activation:
- Administrators automatically have access
- Create staff accounts from the Admin Panel
- Staff can login at `/120-stand/login/`

## Default Products

The plugin includes default products:
- Fruit items: Watermelon, Pineapple, Pawpaw, Apple, Banana, Orange, Mango, Grape
- Non-fruit items: Condensed Milk, Evaporated Milk, Yoghurt, Ice Cream, Cups, Spoons
- Menu items: Various fruit salad sizes

## Theme Colors

- Primary: #8B0000 (Dark Red)
- Background: #2D1F1F (Dark Reddish Ash)
- Text: #FFFFFF (White)

## Technical Details

- Built with PHP, JavaScript, CSS
- Uses WordPress database tables
- Service Worker for offline functionality
- Real-time auto-save on inventory forms
- Mobile-responsive design with bottom navigation

## Database Tables

- `stand120_products` - Products/menu items
- `stand120_staff` - Staff information
- `stand120_orders` - Customer orders
- `stand120_order_items` - Order line items
- `stand120_order_preparation` - Daily fruit preparation
- `stand120_stock_inventory` - Pack inventory
- `stand120_chopping_inventory` - Whole fruit processing
- `stand120_import_records` - Daily imports
- `stand120_financial_summary` - Daily financial data
- `stand120_sync_queue` - Offline sync queue
- `stand120_activity_log` - Activity logging

## License

GPL v2 or later
