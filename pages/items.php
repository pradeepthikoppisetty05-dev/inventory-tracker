<?php
require_once '../config/database.php';
include '../includes/header.php';

// Get all suppliers for dropdown
$suppliers_query = "SELECT * FROM suppliers ORDER BY name";
$suppliers_result = mysqli_query($conn, $suppliers_query);

// Get all items with supplier info
$items_query = "SELECT i.*, s.name as supplier_name 
                FROM items i 
                LEFT JOIN suppliers s ON i.supplier_id = s.id 
                ORDER BY i.name";
$items_result = mysqli_query($conn, $items_query);

// Handle success/error messages
$message = '';
if (isset($_GET['success'])) {
    $message = '<div class="alert alert-success">Operation completed successfully!</div>';
} elseif (isset($_GET['error'])) {
    $message = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
}
?>

<h1>Manage Items</h1>

<?php echo $message; ?>

<!-- Add New Item Form -->
<div class="card">
    <h2>Add New Item</h2>
    <form action="../actions/item_actions.php" method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="name">Item Name *</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label for="supplier_id">Supplier</label>
            <select id="supplier_id" name="supplier_id">
                <option value="">-- Select Supplier --</option>
                <?php 
                mysqli_data_seek($suppliers_result, 0); // Reset pointer
                while ($supplier = mysqli_fetch_assoc($suppliers_result)): 
                ?>
                    <option value="<?php echo $supplier['id']; ?>">
                        <?php echo htmlspecialchars($supplier['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="current_stock">Current Stock *</label>
            <input type="number" id="current_stock" name="current_stock" value="0" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="minimum_stock">Minimum Stock *</label>
            <input type="number" id="minimum_stock" name="minimum_stock" value="10" min="0" required>
        </div>
        
        <div class="form-group">
            <label for="unit_price">Unit Price (₹) *</label>
            <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Item</button>
    </form>
</div>

<!-- Items List -->
<div class="card">
    <h2>All Items</h2>
    
    <?php if (mysqli_num_rows($items_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Supplier</th>
                <th>Current Stock</th>
                <th>Min Stock</th>
                <th>Unit Price</th>
                <th>Total Value</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = mysqli_fetch_assoc($items_result)): 
                $total_value = $item['current_stock'] * $item['unit_price'];
                $is_low_stock = $item['current_stock'] <= $item['minimum_stock'];
            ?>
            <tr <?php echo $is_low_stock ? 'class="low-stock"' : ''; ?>>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo htmlspecialchars($item['description']); ?></td>
                <td><?php echo htmlspecialchars($item['supplier_name'] ?? 'N/A'); ?></td>
                <td><?php echo $item['current_stock']; ?></td>
                <td><?php echo $item['minimum_stock']; ?></td>
                <td>₹<?php echo number_format($item['unit_price'], 2); ?></td>
                <td>₹<?php echo number_format($total_value, 2); ?></td>
                <td>
                    <?php if ($item['current_stock'] == 0): ?>
                        <span class="badge badge-danger">Out of Stock</span>
                    <?php elseif ($is_low_stock): ?>
                        <span class="badge badge-warning">Low Stock</span>
                    <?php else: ?>
                        <span class="badge badge-success">In Stock</span>
                    <?php endif; ?>
                </td>
                <td>
                    <form action="../actions/item_actions.php" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this item?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
        <p>No items found. Add your first item above!</p>
    </div>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>