<?php
require_once '../config/database.php';
include '../includes/header.php';

// Get all items for dropdown
$items_query = "SELECT * FROM items ORDER BY name";
$items_result = mysqli_query($conn, $items_query);

// Get recent adjustments
$adjustments_query = "SELECT sa.*, i.name as item_name 
                      FROM stock_adjustments sa 
                      JOIN items i ON sa.item_id = i.id 
                      ORDER BY sa.adjustment_date DESC 
                      LIMIT 50";
$adjustments_result = mysqli_query($conn, $adjustments_query);

// Handle messages
$message = '';
if (isset($_GET['success'])) {
    $message = '<div class="alert alert-success">Stock adjustment recorded successfully!</div>';
} elseif (isset($_GET['error'])) {
    $message = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
}
?>

<h1>Stock Adjustments</h1>

<?php echo $message; ?>

<!-- Add Stock Adjustment Form -->
<div class="card">
    <h2>Record Stock Adjustment</h2>
    <form action="../actions/adjustment_actions.php" method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="item_id">Select Item *</label>
            <select id="item_id" name="item_id" required>
                <option value="">-- Select Item --</option>
                <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                    <option value="<?php echo $item['id']; ?>">
                        <?php echo htmlspecialchars($item['name']); ?> 
                        (Current: <?php echo $item['current_stock']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="adjustment_type">Adjustment Type *</label>
            <select id="adjustment_type" name="adjustment_type" required>
                <option value="in">Stock In (Add)</option>
                <option value="out">Stock Out (Remove)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity" min="1" required>
        </div>
        
        <div class="form-group">
            <label for="reason">Reason</label>
            <input type="text" id="reason" name="reason" 
                   placeholder="e.g., New purchase, Sales, Damaged goods">
        </div>
        
        <div class="form-group">
            <label for="adjusted_by">Adjusted By *</label>
            <input type="text" id="adjusted_by" name="adjusted_by" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Record Adjustment</button>
    </form>
</div>

<!-- Recent Adjustments -->
<div class="card">
    <h2>Recent Adjustments</h2>
    
    <?php if (mysqli_num_rows($adjustments_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Item</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th>Adjusted By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($adjustment = mysqli_fetch_assoc($adjustments_result)): ?>
            <tr>
                <td><?php echo $adjustment['id']; ?></td>
                <td><?php echo htmlspecialchars($adjustment['item_name']); ?></td>
                <td>
                    <?php if ($adjustment['adjustment_type'] == 'in'): ?>
                        <span class="badge badge-success">Stock IN</span>
                    <?php else: ?>
                        <span class="badge badge-danger">Stock OUT</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $adjustment['quantity']; ?></td>
                <td><?php echo htmlspecialchars($adjustment['reason']); ?></td>
                <td><?php echo htmlspecialchars($adjustment['adjusted_by']); ?></td>
                <td><?php echo date('M d, Y H:i', strtotime($adjustment['adjustment_date'])); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <p>No adjustments recorded yet. Add your first adjustment above!</p>
    </div>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>