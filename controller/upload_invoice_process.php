<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_number = $_POST['invoice_number'];
    $invoice_type = $_POST['invoice_type'];
    $invoice_class = $_POST['invoice_class'];
    $priority = $_POST['priority'];
    $property_reference = $_POST['property_reference'];
    $amount = floatval($_POST['amount']);
    $payment_status = $_POST['payment_status'];
    $notes = $_POST['notes'];
    $staff_id = $_SESSION['user_id'];

    // File upload logic
    $upload_dir = '../dist/invoices/';
    $file_name = basename($_FILES['invoice_file']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $target_file)) {
        // Save invoice data in the database
        $stmt = $conn->prepare("INSERT INTO invoice_list (tax_invoice_number, invoice_type, invoice_class, priority, property_reference, amount , payment_status, notes, pdf_invoice_path, staff_id, date_requested, admin_reviewer, approval_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL, 'Pending')");
        $stmt->bind_param("sssssdsssi", $invoice_number, $invoice_type, $invoice_class, $priority, $property_reference, $amount, $payment_status, $notes, $file_name, $staff_id);


        if ($stmt->execute()) {
            $_SESSION['success'] = "Tax invoice submitted successfully!";
            header("Location: ../staff/upload_invoice.php");
            exit;
        } else {
            $_SESSION['error'] = "Error submitting invoice. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Error uploading file. Please try again.";
    }
}

header("Location: ../staff/upload_invoice.php");
exit;
?>
