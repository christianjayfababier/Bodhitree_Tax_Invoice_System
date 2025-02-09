<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../config.php';

// Get Invoice ID
$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    header("Location: request_list.php");
    exit;
}

// Fetch Invoice Details
$query = $conn->prepare("SELECT * FROM invoice_list WHERE id = ?");
$query->bind_param("i", $invoice_id);
$query->execute();
$result = $query->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    header("Location: request_list.php");
    exit;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $admin_note = $_POST['admin_note'];
    $admin_name = $_SESSION['username']; // Admin username from session

    // Update query
    $update_query = $conn->prepare("UPDATE invoice_list SET approval_status = ?, review_notes = ?, admin_reviewer_name = ? WHERE id = ?");
    $update_query->bind_param("sssi", $status, $admin_note, $admin_name, $invoice_id);

    if ($update_query->execute()) {
        $_SESSION['message'] = "Invoice updated successfully.";
        header("Location: request_list.php");
        exit;
    } else {
        $error = "Failed to update invoice. Please try again.";
    }
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1>Review Invoice</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="invoiceNumber" class="form-label">Invoice Number</label>
                <input type="text" class="form-control" id="invoiceNumber" value="<?php echo htmlspecialchars($invoice['tax_invoice_number']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="invoiceType" class="form-label">Invoice Type</label>
                <input type="text" class="form-control" id="invoiceType" value="<?php echo htmlspecialchars($invoice['invoice_type']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <input type="text" class="form-control" id="priority" value="<?php echo htmlspecialchars($invoice['priority']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="Pending" <?php echo $invoice['approval_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo $invoice['approval_status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="Denied" <?php echo $invoice['approval_status'] == 'Denied' ? 'selected' : ''; ?>>Denied</option>
                    <option value="Cancelled" <?php echo $invoice['approval_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="Further Review" <?php echo $invoice['approval_status'] == 'Further Review' ? 'selected' : ''; ?>>Further Review</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="adminNote" class="form-label">Admin Note</label>
                <textarea name="admin_note" id="adminNote" class="form-control" rows="5"><?php echo htmlspecialchars($invoice['review_notes']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="verifiedBy" class="form-label">Verified By (Admin)</label>
                <input type="text" class="form-control" id="verifiedBy" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="pdfInvoice" class="form-label">PDF Invoice</label>
                <?php if (!empty($invoice['pdf_invoice_path'])): ?>
                    <embed src="../dist/invoices/<?php echo htmlspecialchars($invoice['pdf_invoice_path']); ?>" type="application/pdf" width="100%" height="500px">
                <?php else: ?>
                    <p class="text-danger">No PDF uploaded.</p>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-success">Save Changes</button>
            <a href="request_list.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
