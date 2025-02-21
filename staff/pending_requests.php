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
            <thead style="background-color: #335E53; color: #f8f9fa">
            <tr style="font-size: 0.85em; vertical-align: middle;">
                <th>Request Status</th>
                <th>Priority</th>
                <th>Amount (AUD)</th>
                <th>Payment Status</th>
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Invoice Class</th>
                <th>Date Submitted</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php if ($row['approval_status'] == 'Pending'): ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php elseif ($row['approval_status'] == 'Approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif ($row['approval_status'] == 'Denied'): ?>
                                <span class="badge bg-danger">Denied</span>
                            <?php endif; ?>
                        </td>
                        <td><b><i><u><?php echo htmlspecialchars($row['priority']); ?></u></i></b></td>
                        <td><?php echo htmlspecialchars($row['amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['tax_invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['invoice_class']); ?></td>
                        <td><?php echo date("d-m-Y H:i", strtotime($row['date_requested'])); ?></td>
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td>
                            <!-- Button to open modal -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#viewInvoiceModal"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-invoice-number="<?php echo htmlspecialchars($row['tax_invoice_number']); ?>"
                                    data-payment-status="<?php echo htmlspecialchars($row['payment_status']); ?>"
                                    data-amount="<?php echo htmlspecialchars($row['amount']); ?>"
                                    data-invoice-type="<?php echo htmlspecialchars($row['invoice_type']); ?>"
                                    data-invoice-class="<?php echo htmlspecialchars($row['invoice_class']); ?>"
                                    data-property-reference="<?php echo htmlspecialchars($row['property_reference']); ?>"
                                    data-priority="<?php echo htmlspecialchars($row['priority']); ?>"
                                    data-date="<?php echo date("d-m-Y H:i", strtotime($row['date_requested'])); ?>"
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
            <div class="modal-header" style="background-color: #335E53; color: #f8f9fa">
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <h5 class="modal-title" id="viewInvoiceModalLabel">Invoice Details</h5>
                            <p style="font-size: 0.7em "><strong>Date Submitted:</strong> <span id="modalDate"></span></p>
                        </div>
                    </div>

                </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                             <p><strong>Tax Invoice Number:</strong> <span id="modalInvoiceNumber"></span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Priority:</strong> <span id="modalPriority"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Category:</strong> <span id="modalInvoiceType"></span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Class:</strong> <span id="modalInvoiceClass"></span></p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Property Reference:</strong> <span id="modalPropertyReference"></span></p>
                        </div>
                    </div>

                </div>
            <hr>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Payment Status:</strong> <span id="modalPaymentStatus"></span></p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <p><strong>Amount:</strong> <span id="modalAmount"></span></p>
                        </div>
                    </div>
                </div>
            <hr>
                <div class="row">

                    <p><strong>Notes:</strong> <i><span id="modalNotes"></span></i></p>
                </div>





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
            const paymentStatus = button.getAttribute('data-payment-status');
            const amount = button.getAttribute('data-amount');
            const invoiceType = button.getAttribute('data-invoice-type');
            const invoiceClass = button.getAttribute('data-invoice-class');
            const propertyReference = button.getAttribute('data-property-reference');
            const priority = button.getAttribute('data-priority');
            const date = button.getAttribute('data-date');
            const notes = button.getAttribute('data-notes');
            const pdfPath = button.getAttribute('data-pdf');

            // Populate modal fields
            document.getElementById('modalInvoiceNumber').textContent = invoiceNumber;
            document.getElementById('modalPaymentStatus').textContent = paymentStatus;
            document.getElementById('modalAmount').textContent = amount;
            document.getElementById('modalInvoiceType').textContent = invoiceType;
            document.getElementById('modalInvoiceClass').textContent = invoiceClass;
            document.getElementById('modalPropertyReference').textContent = propertyReference;
            document.getElementById('modalPriority').textContent = priority;
            document.getElementById('modalDate').textContent = date;
            document.getElementById('modalNotes').textContent = notes;
            document.getElementById('modalPDF').setAttribute('src', `../dist/invoices/${pdfPath}`);
        });
    });
</script>