<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

// Set page title
$pageTitle = "Pending Requests";

// Include database connection
require_once '../config.php';

// Fetch pending invoices for the logged-in staff
$staff_id = $_SESSION['user_id']; // Logged-in staff's ID
$query = $conn->prepare("SELECT * FROM invoice_list WHERE approval_status = 'Pending' AND staff_id = ?");
$query->bind_param("i", $staff_id);
$query->execute();
$result = $query->get_result();
?>

<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1>Pending Requests</h1>
        <p>Here are all your invoices that are pending approval.</p>

        <!-- Table to display pending invoices -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>TX ID</th>
                <th>Status</th>
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Priority</th>
                <th>Date Submitted</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php if ($row['approval_status'] == 'Pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif ($row['approval_status'] == 'Approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($row['approval_status'] == 'Denied'): ?>
                                <span class="badge bg-danger">Denied</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['tax_invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['priority']); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_requested'])); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td>
                            <!-- Button to open modal -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewInvoiceModal"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-invoice-number="<?php echo htmlspecialchars($row['tax_invoice_number']); ?>"
                                    data-invoice-type="<?php echo htmlspecialchars($row['invoice_type']); ?>"
                                    data-priority="<?php echo htmlspecialchars($row['priority']); ?>"
                                    data-date="<?php echo date("Y-m-d H:i", strtotime($row['date_requested'])); ?>"
                                    data-notes="<?php echo htmlspecialchars($row['notes']); ?>"
                                    data-pdf="<?php echo htmlspecialchars($row['pdf_invoice_path']); ?>">
                                View
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No pending invoices found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewInvoiceModalLabel">Invoice Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Invoice Number:</strong> <span id="modalInvoiceNumber"></span></p>
                <p><strong>Invoice Type:</strong> <span id="modalInvoiceType"></span></p>
                <p><strong>Priority:</strong> <span id="modalPriority"></span></p>
                <p><strong>Date Submitted:</strong> <span id="modalDate"></span></p>
                <p><strong>Notes:</strong> <span id="modalNotes"></span></p>
                <hr>
                <embed id="modalPDF" src="" type="application/pdf" width="100%" height="500px">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
    // Populate modal with invoice data
    document.addEventListener('DOMContentLoaded', () => {
        const viewInvoiceModal = document.getElementById('viewInvoiceModal');
        viewInvoiceModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;

            // Extract data attributes from the clicked button
            const invoiceNumber = button.getAttribute('data-invoice-number');
            const invoiceType = button.getAttribute('data-invoice-type');
            const priority = button.getAttribute('data-priority');
            const date = button.getAttribute('data-date');
            const notes = button.getAttribute('data-notes');
            const pdfPath = button.getAttribute('data-pdf');

            // Populate modal fields
            document.getElementById('modalInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('modalInvoiceType').textContent = invoiceType;
            document.getElementById('modalPriority').textContent = priority;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalNotes').textContent = notes;
            document.getElementById('modalPDF').setAttribute('src', `../dist/invoices/${pdfPath}`);
        });
    });
</script>