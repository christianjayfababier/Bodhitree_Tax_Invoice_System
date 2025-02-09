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
    $admin_reviewer_name = $_SESSION['username'];
    $date_updated = date('Y-m-d H:i:s');

    // Update the invoice record
    $stmt = $conn->prepare("
        UPDATE invoice_list 
        SET approval_status = ?, review_notes = ?, admin_reviewer_name = ?, date_updated = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi", $new_status, $review_notes, $admin_reviewer_name, $date_updated, $invoice_id);

    if ($stmt->execute()) {
        // Get the staff ID associated with the invoice
        $stmt2 = $conn->prepare("SELECT staff_id FROM invoice_list WHERE id = ?");
        $stmt2->bind_param("i", $invoice_id);
        $stmt2->execute();
        $result = $stmt2->get_result();
        $invoice = $result->fetch_assoc();

        if ($invoice) {
            $staff_id = $invoice['staff_id'];
            $message = "The status of your invoice #{$invoice_id} has been changed to '{$new_status}'.";

            // Insert a notification for the staff member
            $stmt3 = $conn->prepare("
                INSERT INTO notifications (staff_id, message) 
                VALUES (?, ?)
            ");
            $stmt3->bind_param("is", $staff_id, $message);
            $stmt3->execute();
        }

        $_SESSION['success'] = "Invoice status updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update invoice status. Please try again.";
    }

    header("Location: ../admin/review_invoice.php?id=" . $invoice_id);
    exit;
} else {
    // If accessed without POST, redirect to the admin dashboard
    $_SESSION['error'] = "Invalid request.";
    header("Location: ../admin/dashboard.php");
    exit;
}
?>
