<?php
require_once '../config/database.php';
include '../includes/header.php';

// Get all suppliers
$suppliers_query = "SELECT s.*, COUNT(i.id) as item_count 
                    FROM suppliers s 
                    LEFT JOIN items i ON s.id = i.supplier_id 
                    GROUP BY s.id 
                    ORDER BY s.name";
$suppliers_result = mysqli_query($conn, $suppliers_query);

// Handle messages
$message = '';
if (isset($_GET['success'])) {
    $message = '<div class="alert alert-success">Operation completed successfully!</div>';
} elseif (isset($_GET['error'])) {
    $message = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
}
?>

<h1>Manage Suppliers</h1>

<?php echo $message; ?>

<!-- Add New Supplier Form -->
<div class="card">
    <h2>Add New Supplier</h2>
    <form action="../actions/supplier_actions.php" method="POST">
        <input type="hidden" name="action" value="add">
        
        <div class="form-group">
            <label for="name">Supplier Name *</label>
            <input type="text" id="name" name="name" required>
        </div>
        
        <div class="form-group">
            <label for="contact_person">Contact Person</label>
            <input type="text" id="contact_person" name="contact_person">
        </div>
        
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone">
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email">
        </div>
        
        <button type="submit" class="btn btn-success">Add Supplier</button>
    </form>
</div>

<!-- Suppliers List -->
<div class="card">
    <h2>All Suppliers</h2>
    
    <?php if (mysqli_num_rows($suppliers_result) > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Person</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Items Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($supplier = mysqli_fetch_assoc($suppliers_result)): ?>
            <tr>
                <td><?php echo $supplier['id']; ?></td>
                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                <td><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                <td><?php echo htmlspecialchars($supplier['phone']); ?></td>
                <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                <td>
                    <span class="badge badge-info"><?php echo $supplier['item_count']; ?> items</span>
                </td>
                <td>
                    <form action="../actions/supplier_actions.php" method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $supplier['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Are you sure? This will unlink all items from this supplier.')">
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
        <p>No suppliers found. Add your first supplier above!</p>
    </div>
    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>