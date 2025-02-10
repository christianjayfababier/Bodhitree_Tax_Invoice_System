<?php
session_start();
require_once '../config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Get the invoice details
$invoice_id = $_GET['id'] ?? null;
if (!$invoice_id) {
    $_SESSION['error'] = "Invoice ID is required.";
    header("Location: request_list.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT invoice_list.*, CONCAT(users.firstname, ' ', users.lastname) AS staff_name 
    FROM invoice_list 
    LEFT JOIN users ON invoice_list.staff_id = users.id 
    WHERE invoice_list.id = ?
");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    $_SESSION['error'] = "Invoice not found.";
    header("Location: request_list.php");
    exit;
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Review Invoice</h1>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <form method="POST" action="../controller/update_invoice_status.php">
                <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">

                <div class="mb-3">
                    <label for="taxInvoiceNumber" class="form-label">Tax Invoice Number</label>
                    <input type="text" class="form-control" id="taxInvoiceNumber" value="<?php echo htmlspecialchars($invoice['tax_invoice_number']); ?>" readonly>
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
                    <label for="priority" class="form-label">Uploader Note</label>
                    <input type="text" class="form-control" id="priority" value="<?php echo htmlspecialchars($invoice['notes']); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="requestedBy" class="form-label">Requested By</label>
                    <input type="text" class="form-control" id="requestedBy" value="<?php echo htmlspecialchars($invoice['staff_name'] ?? 'Unknown'); ?>" readonly>
                </div>

                <hr style="color: #335E53;">

                <div class="mb-3">
                    <label for="approvalStatus" class="form-label"><b>Status</b></label>
                    <select name="approval_status" id="approvalStatus" class="form-select">
                        <option value="Pending" <?php echo $invoice['approval_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $invoice['approval_status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Denied" <?php echo $invoice['approval_status'] === 'Denied' ? 'selected' : ''; ?>>Denied</option>
                        <option value="Further Review" <?php echo $invoice['approval_status'] === 'Further Review' ? 'selected' : ''; ?>>Further Review</option>
                        <option value="Cancelled" <?php echo $invoice['approval_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="reviewNotes" class="form-label"><b>Admin Note</b></label>
                    <textarea name="review_notes" id="reviewNotes" class="form-control" rows="5"><?php echo htmlspecialchars($invoice['review_notes']); ?></textarea>
                </div>

                <?php if (!empty($invoice['admin_reviewer_name']) && $invoice['approval_status'] !== 'Pending'): ?>
                    <div class="mb-3">
                        <label for="verifiedBy" class="form-label"><b>Verified By (Admin)</b></label>
                        <input type="text" class="form-control" id="verifiedBy" value="<?php echo htmlspecialchars($invoice['admin_reviewer_name']); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="dateReviewed" class="form-label"><b>Date Reviewed</b></label>
                        <input type="text" class="form-control" id="dateReviewed" value="<?php echo htmlspecialchars($invoice['date_updated']); ?>" readonly>
                    </div>
                <?php endif; ?>

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