<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

require_once '../config.php';

// Get Invoice ID
$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    header("Location: my_records.php");
    exit;
}

// Fetch Invoice Details
$query = $conn->prepare("SELECT * FROM invoice_list WHERE id = ?");
$query->bind_param("i", $invoice_id);
$query->execute();
$result = $query->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    header("Location: my_records.php");
    exit;
}
?>

<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Invoice Details</h1>
            <div class="card">
                <div class="card-body">
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
                        <input type="text" class="form-control" id="status" value="<?php echo htmlspecialchars($invoice['approval_status']); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="verifiedBy" class="form-label">Verified By</label>
                        <input type="text" class="form-control" id="verifiedBy" value="<?php echo htmlspecialchars($invoice['admin_reviewer_name'] ?? 'Not Verified'); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="notes" rows="5" readonly><?php echo htmlspecialchars($invoice['review_notes']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="dateRequested" class="form-label">Date Requested</label>
                        <input type="text" class="form-control" id="dateRequested" value="<?php echo date('Y-m-d H:i', strtotime($invoice['date_requested'])); ?>" readonly>
                    </div>

                    <a href="my_records.php" class="btn btn-secondary">Back to Records</a>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>