<?php
require_once '../config/database.php';
include '../includes/header.php';

// Get transaction summary
$summary_query = "SELECT 
                    adjustment_type,
                    COUNT(*) as count,
                    SUM(quantity) as total_quantity
                  FROM stock_adjustments 
                  GROUP BY adjustment_type";
$summary_result = mysqli_query($conn, $summary_query);
$summary = ['in' => ['count' => 0, 'total' => 0], 'out' => ['count' => 0, 'total' => 0]];

while ($row = mysqli_fetch_assoc($summary_result)) {
    $summary[$row['adjustment_type']]['count'] = $row['count'];
    $summary[$row['adjustment_type']]['total'] = $row['total_quantity'];
}

// Get items with most transactions
$top_items_query = "SELECT i.name, COUNT(sa.id) as transaction_count 
                    FROM items i 
                    LEFT JOIN stock_adjustments sa ON i.id = sa.item_id 
                    GROUP BY i.id 
                    HAVING transaction_count > 0 
                    ORDER BY transaction_count DESC 
                    LIMIT 10";
$top_items_result = mysqli_query($conn, $top_items_query);

// Get monthly transaction summary
$monthly_query = "SELECT 
                    DATE_FORMAT(adjustment_date, '%Y-%m') as month,
                    adjustment_type,
                    COUNT(*) as count,
                    SUM(quantity) as total_quantity
                  FROM stock_adjustments 
                  WHERE adjustment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                  GROUP BY month, adjustment_type 
                  ORDER BY month DESC";
$monthly_result = mysqli_query($conn, $monthly_query);

// Organize monthly data
$monthly_data = [];
while ($row = mysqli_fetch_assoc($monthly_result)) {
    if (!isset($monthly_data[$row['month']])) {
        $monthly_data[$row['month']] = ['in' => 0, 'out' => 0];
    }
    $monthly_data[$row['month']][$row['adjustment_type']] = $row['total_quantity'];
}
?>

<h1>Reports & Analytics</h1>

<!-- Transaction Summary -->
<div class="card">
    <h2>Transaction Summary</h2>
    <div class="stats-grid">
        <div class="stat-card success">
            <h3>Total Stock IN Transactions</h3>
            <div class="value"><?php echo $summary['in']['count']; ?></div>
            <p>Total Quantity: <?php echo $summary['in']['total']; ?></p>
        </div>
        
        <div class="stat-card warning">
            <h3>Total Stock OUT Transactions</h3>
            <div class="value"><?php echo $summary['out']['count']; ?></div>
            <p>Total Quantity: <?php echo $summary['out']['total']; ?></p>
        </div>
    </div>
</div>

<!-- Top Items by Transaction -->
<div class="card">
    <h2>Most Active Items</h2>
    <?php if (mysqli_num_rows($top_items_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Transaction Count</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($top_items_result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>
                    <span class="badge badge-info"><?php echo $item['transaction_count']; ?> transactions</span>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <p>No transaction data available yet.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Monthly Trends -->
<div class="card">
    <h2>Monthly Transaction Trends (Last 6 Months)</h2>
    <?php if (!empty($monthly_data)): ?>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Stock IN</th>
                <th>Stock OUT</th>
                <th>Net Change</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthly_data as $month => $data): 
                $net_change = $data['in'] - $data['out'];
                $net_class = $net_change > 0 ? 'badge-success' : ($net_change < 0 ? 'badge-danger' : 'badge-info');
            ?>
            <tr>
                <td><?php echo date('F Y', strtotime($month . '-01')); ?></td>
                <td><span class="badge badge-success"><?php echo $data['in']; ?></span></td>
                <td><span class="badge badge-danger"><?php echo $data['out']; ?></span></td>
                <td><span class="badge <?php echo $net_class; ?>"><?php echo $net_change; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <p>No monthly data available yet.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Current Stock Status -->
<div class="card">
    <h2>Current Inventory Status</h2>
    <?php
    $status_query = "SELECT 
                        COUNT(*) as total_items,
                        SUM(CASE WHEN current_stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                        SUM(CASE WHEN current_stock > 0 AND current_stock <= minimum_stock THEN 1 ELSE 0 END) as low_stock,
                        SUM(CASE WHEN current_stock > minimum_stock THEN 1 ELSE 0 END) as healthy_stock
                     FROM items";
    $status_result = mysqli_query($conn, $status_query);
    $status = mysqli_fetch_assoc($status_result);
    ?>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Items</h3>
            <div class="value"><?php echo $status['total_items']; ?></div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <h3>Healthy Stock</h3>
            <div class="value"><?php echo $status['healthy_stock']; ?></div>
        </div>
        
        <div class="stat-card warning">
            <h3>Low Stock</h3>
            <div class="value"><?php echo $status['low_stock']; ?></div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
            <h3>Out of Stock</h3>
            <div class="value"><?php echo $status['out_of_stock']; ?></div>
        </div>
    </div>
</div>

<?php
include '../includes/footer.php';
?>