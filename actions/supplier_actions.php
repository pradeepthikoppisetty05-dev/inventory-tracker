<?php
require_once '../config/database.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        // Get form data
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact_person = mysqli_real_escape_string($conn, $_POST['contact_person']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        // Insert query
        $query = "INSERT INTO suppliers (name, contact_person, phone, email) 
                  VALUES ('$name', '$contact_person', '$phone', '$email')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../pages/suppliers.php?success=1");
        } else {
            header("Location: ../pages/suppliers.php?error=1");
        }
        exit();
    }
    
    elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        
        // Delete supplier
        $query = "DELETE FROM suppliers WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            header("Location: ../pages/suppliers.php?success=1");
        } else {
            header("Location: ../pages/suppliers.php?error=1");
        }
        exit();
    }
}

// If accessed directly, redirect
header("Location: ../pages/suppliers.php");
exit();
?>