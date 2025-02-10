<?php
session_start();
require_once '../config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $invoice_id = $_POST['invoice_id'];
    $new_status = $_POST['approval_status'];
    $review_notes = $_POST['review_notes'];
    $admin_reviewer_name = $_SESSION['username']; // Admin's username from session
    $date_updated = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare the SQL query to update the invoice
    $stmt = $conn->prepare("
        UPDATE invoice_list 
        SET 
            approval_status = ?, 
            review_notes = ?, 
            admin_reviewer_name = ?, 
            date_updated = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $new_status, $review_notes, $admin_reviewer_name, $date_updated, $invoice_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        $_SESSION['success'] = "Invoice status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update invoice status. Please try again.";
    }

    // Redirect back to the review invoice page
    header("Location: ../admin/review_invoice.php?id=" . $invoice_id);
    exit;
} else {
    // If accessed without POST, redirect to the admin dashboard
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../admin/dashboard.php");
    exit;
}
?>
