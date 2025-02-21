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
    $new_payment_status = $_POST['payment_status'];
    $approve_true = $_POST['approve_true'];
    $master_signature = $_SESSION['username']; // Admin's username from session
    date_default_timezone_set('Asia/Manila');
    $master_date = date('Y-m-d H:i:s'); // Current timestamp

    // Prepare the SQL query to update the invoice
    $stmt = $conn->prepare("
        UPDATE invoice_list 
        SET 
            payment_status = ?, 
            approve_true = ?, 
            master_signature = ?, 
            master_date = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $new_payment_status,$approve_true,  $master_signature, $master_date, $invoice_id);

    // Execute the query and handle the result
    if ($stmt->execute()) {
        $_SESSION['success'] = "Status Updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update invoice status. Please try again.";
    }

    // Redirect back to the review invoice page
    header("Location: ../admin/for_approval_list.php?id=" . $invoice_id);
    exit;
} else {
    // If accessed without POST, redirect to the admin dashboard
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../admin/dashboard.php");
    exit;
}
?>
