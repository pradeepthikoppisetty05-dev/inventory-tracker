<?php
require_once 'config/database.php';
include 'includes/header.php';

// Get total items count
$total_items_query = "SELECT COUNT(*) as total FROM items";
$total_items_result = mysqli_query($conn, $total_items_query);
$total_items = mysqli_fetch_assoc($total_items_result)['total'];

// Get low stock items count
$low_stock_query = "SELECT COUNT(*) as total FROM items WHERE current_stock <= minimum_stock";
$low_stock_result = mysqli_query($conn, $low_stock_query);
$low_stock_count = mysqli_fetch_assoc($low_stock_result)['total'];

// Get total stock value
$stock_value_query = "SELECT SUM(current_stock * unit_price) as total_value FROM items";
$stock_value_result = mysqli_query($conn, $stock_value_query);
$total_value = mysqli_fetch_assoc($stock_value_result)['total_value'];

// Get recent adjustments count (last 7 days)
$recent_adjustments_query = "SELECT COUNT(*) as total FROM stock_adjustments 
                             WHERE adjustment_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$recent_adjustments_result = mysqli_query($conn, $recent_adjustments_query);
$recent_adjustments = mysqli_fetch_assoc($recent_adjustments_result)['total'];

// Get low stock items details
$low_stock_items_query = "SELECT i.*, s.name as supplier_name 
                          FROM items i 
                          LEFT JOIN suppliers s ON i.supplier_id = s.id 
                          WHERE i.current_stock <= i.minimum_stock 
                          ORDER BY i.current_stock ASC";
$low_stock_items_result = mysqli_query($conn, $low_stock_items_query);
?>

<h1>Dashboard</h1>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Items</h3>
        <div class="value"><?php echo $total_items; ?></div>
    </div>
    
    <div class="stat-card warning">
        <h3>Low Stock Items</h3>
        <div class="value"><?php echo $low_stock_count; ?></div>
    </div>
    
    <div class="stat-card success">
        <h3>Total Stock Value</h3>
        <div class="value">₹<?php echo number_format($total_value, 2); ?></div>
    </div>
    
    <div class="stat-card">
        <h3>Recent Adjustments (7 days)</h3>
        <div class="value"><?php echo $recent_adjustments; ?></div>
    </div>
</div>

<!-- Low Stock Alerts -->
<?php if ($low_stock_count > 0): ?>
<div class="card">
    <h2>⚠️ Low Stock Alerts</h2>
    <div class="alert alert-warning">
        You have <?php echo $low_stock_count; ?> item(s) with low stock levels. Please restock soon!
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Supplier</th>
                <th>Current Stock</th>
                <th>Minimum Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($low_stock_items_result)): ?>
            <tr class="low-stock">
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['supplier_name'] ?? 'N/A'); ?></td>
                <td><?php echo $item['current_stock']; ?></td>
                <td><?php echo $item['minimum_stock']; ?></td>
                <td>
                    <?php if ($item['current_stock'] == 0): ?>
                        <span class="badge badge-danger">Out of Stock</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Low Stock</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="card">
    <h2>✅ Stock Status</h2>
    <div class="alert alert-success">
        All items are well stocked. No low stock alerts at this time.
    </div>
</div>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>