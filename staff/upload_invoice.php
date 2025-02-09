<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Upload Tax Invoice";
?>

<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1 class="mb-4">Upload New Tax Invoice</h1>

        <!-- Display Flash Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <!-- Success Modal -->
            <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="successModalLabel">Success</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?php echo $_SESSION['success']; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <!-- Error Alert -->
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="../controller/upload_invoice_process.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="invoice_number" class="form-label">Tax Invoice Number</label>
                <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                <div class="invalid-feedback">Please provide the tax invoice number.</div>
            </div>

            <div class="mb-3">
                <label for="invoice_type" class="form-label">Invoice Type</label>
                <select class="form-select" id="invoice_type" name="invoice_type" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="House Cleaning Tax">House Cleaning Tax</option>
                    <option value="Service Tax">Service Tax</option>
                    <option value="Sales Tax">Sales Tax</option>
                </select>
                <div class="invalid-feedback">Please select an invoice type.</div>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority" required>
                    <option value="Low">Low</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="High">High</option>
                    <option value="Urgent">Urgent</option>
                </select>
                <div class="invalid-feedback">Please select a priority level.</div>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label for="invoice_file" class="form-label">Upload PDF Invoice</label>
                <input type="file" class="form-control" id="invoice_file" name="invoice_file" accept="application/pdf" required>
                <div class="invalid-feedback">Please upload a valid PDF file.</div>
            </div>

            <button type="submit" class="btn btn-primary">Submit Invoice</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    // Show the success modal if it exists
    document.addEventListener("DOMContentLoaded", function() {
        const successModal = document.getElementById('successModal');
        if (successModal) {
            const bootstrapModal = new bootstrap.Modal(successModal);
            bootstrapModal.show();
        }
    });
</script>
