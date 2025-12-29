<?php
require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Get form data
        $item_id = intval($_POST['item_id']);
        $adjustment_type = mysqli_real_escape_string($conn, $_POST['adjustment_type']);
        $quantity = intval($_POST['quantity']);
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $adjusted_by = mysqli_real_escape_string($conn, $_POST['adjusted_by']);
        
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Insert adjustment record
            $insert_query = "INSERT INTO stock_adjustments (item_id, adjustment_type, quantity, reason, adjusted_by) 
                            VALUES ($item_id, '$adjustment_type', $quantity, '$reason', '$adjusted_by')";
            
            if (!mysqli_query($conn, $insert_query)) {
                throw new Exception("Failed to insert adjustment");
            }
            
            // Update item stock
            if ($adjustment_type == 'in') {
                $update_query = "UPDATE items SET current_stock = current_stock + $quantity WHERE id = $item_id";
            } else {
                $update_query = "UPDATE items SET current_stock = current_stock - $quantity WHERE id = $item_id";
            }
            
            if (!mysqli_query($conn, $update_query)) {
                throw new Exception("Failed to update stock");
            }
            
            // Commit transaction
            mysqli_commit($conn);
            header("Location: ../pages/adjustments.php?success=1");
            
        } catch (Exception $e) {
            // Rollback on error
            mysqli_rollback($conn);
            header("Location: ../pages/adjustments.php?error=1");
        }
        exit();
    }
}

// If accessed directly, redirect
header("Location: ../pages/adjustments.php");
exit();
?>