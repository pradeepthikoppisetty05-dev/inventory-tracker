# Inventory Tracker

A simple PHP-based inventory management system for tracking items, suppliers, and stock adjustments.

## Features

- **Dashboard**: View overall statistics and low-stock alerts
- **Items Management**: Add, view, and delete inventory items
- **Suppliers Management**: Manage supplier information
- **Stock Adjustments**: Record stock IN/OUT transactions
- **Reports**: View transaction summaries and trends
- **Low Stock Alerts**: Automatic alerts when items reach minimum stock levels

## Technologies Used

- PHP 7.4+
- MySQL 5.7+
- HTML5/CSS3
- XAMPP (Development Environment)

## Installation Instructions

### Prerequisites
- XAMPP installed on your system
- Web browser (Chrome, Firefox, Edge, etc.)

### Setup Steps

1. **Install XAMPP**
   - Download from https://www.apachefriends.org/
   - Install with default settings
   - Start Apache and MySQL from XAMPP Control Panel

2. **Create Project Folder**
   - Navigate to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
   - Create folder named `inventory-tracker`
   - Copy all project files to this folder

3. **Create Database**
   - Open browser and go to `http://localhost/phpmyadmin`
   - Click "New" to create database
   - Database name: `inventory_tracker`
   - Click on the database
   - Go to "SQL" tab
   - Copy and paste the SQL from `database.sql` file
   - Click "Go" to execute

4. **Access Application**
   - Open browser
   - Navigate to: `http://localhost/inventory-tracker/`
   - You should see the dashboard

## Project Structure

```
inventory-tracker/
├── config/
│   └── database.php          # Database connection
├── includes/
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── css/
│   └── style.css             # Styling
├── pages/
│   ├── items.php             # Items management
│   ├── suppliers.php         # Suppliers management
│   ├── adjustments.php       # Stock adjustments
│   └── reports.php           # Reports and analytics
├── actions/
│   ├── item_actions.php      # Item CRUD operations
│   ├── supplier_actions.php  # Supplier CRUD operations
│   └── adjustment_actions.php # Adjustment operations
├── index.php                 # Dashboard
├── database.sql              # Database schema
└── README.md                 # This file
```

## Usage Guide

### Dashboard
- View total items, low stock alerts, and stock value
- See items that need restocking

### Managing Items
1. Go to "Items" page
2. Fill in item details (name, supplier, stock, price)
3. Click "Add Item"
4. View all items in the table below
5. Delete items using the delete button

### Managing Suppliers
1. Go to "Suppliers" page
2. Add supplier information
3. View all suppliers and their item counts
4. Delete suppliers (items will be unlinked)

### Recording Stock Adjustments
1. Go to "Adjustments" page
2. Select item from dropdown
3. Choose adjustment type (IN or OUT)
4. Enter quantity and reason
5. Submit to update stock levels
6. View transaction history below

### Viewing Reports
1. Go to "Reports" page
2. View transaction summaries
3. See most active items
4. Check monthly trends
5. Review current inventory status

## Database Schema

### Tables

**suppliers**
- id (Primary Key)
- name
- contact_person
- phone
- email
- created_at

**items**
- id (Primary Key)
- name
- description
- supplier_id (Foreign Key)
- current_stock
- minimum_stock
- unit_price
- created_at

**stock_adjustments**
- id (Primary Key)
- item_id (Foreign Key)
- adjustment_type (in/out)
- quantity
- reason
- adjusted_by
- adjustment_date

## Key Features Demonstrated

1. **Database Transactions**: Stock adjustments use transactions to ensure data consistency
2. **Foreign Keys**: Proper relationships between tables
3. **CRUD Operations**: Create, Read, Update, Delete for all entities
4. **Reporting Queries**: Aggregate queries for statistics and trends
5. **Low Stock Alerts**: Conditional logic for inventory management
6. **Responsive Design**: Clean, user-friendly interface

## Future Enhancements

- User authentication system
- Export reports to PDF/Excel
- Search and filter functionality
- Barcode scanning support
- Email notifications for low stock
- Multi-location support

## Author

Developed as a PHP Developer Assignment

## License

This project is created for educational purposes.