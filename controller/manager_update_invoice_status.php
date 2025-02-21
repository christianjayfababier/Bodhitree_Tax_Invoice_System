<?php
session_start();
require_once '../config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $invoice_id = $_POST['invoice_id'];
    $new_manager_status = $_POST['approval_status'];
    $payor = $_POST['payor'];
    $review_notes = $_POST['review_notes'];
    $admin_reviewer_name = $_SESSION['username']; // Admin's username from session
    date_default_timezone_set('Asia/Manila');
    $manager_date = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare the SQL query to update the invoice
    $stmt = $conn->prepare("
        UPDATE invoice_list 
        SET 
            approval_status = ?, 
            payor = ?, 
            review_notes = ?, 
            admin_reviewer_name = ?, 
            manager_date = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("sssssi", $new_manager_status,$payor, $review_notes, $admin_reviewer_name, $manager_date, $invoice_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        $_SESSION['success'] = "Invoice status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update invoice status. Please try again.";
    }

    // Redirect back to the review invoice page
    header("Location: ../manager/review_invoice.php?id=" . $invoice_id);
    exit;
} else {
    // If accessed without POST, redirect to the admin dashboard
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../manager/dashboard.php");
    exit;
}
?>
