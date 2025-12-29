<?php
require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Get form data
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $supplier_id = !empty($_POST['supplier_id']) ? intval($_POST['supplier_id']) : 'NULL';
        $current_stock = intval($_POST['current_stock']);
        $minimum_stock = intval($_POST['minimum_stock']);
        $unit_price = floatval($_POST['unit_price']);
        
        // Insert query
        $query = "INSERT INTO items (name, description, supplier_id, current_stock, minimum_stock, unit_price) 
                  VALUES ('$name', '$description', " . ($supplier_id === 'NULL' ? 'NULL' : $supplier_id) . ", 
                          $current_stock, $minimum_stock, $unit_price)";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../pages/items.php?success=1");
        } else {
            header("Location: ../pages/items.php?error=1");
        }
        exit();
    }
    
    elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        
        // Delete item
        $query = "DELETE FROM items WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../pages/items.php?success=1");
        } else {
            header("Location: ../pages/items.php?error=1");
        }
        exit();
    }
}

// If accessed directly, redirect
header("Location: ../pages/items.php");
exit();
?>