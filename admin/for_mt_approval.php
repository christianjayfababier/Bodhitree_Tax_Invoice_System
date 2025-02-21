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
    header("Location: for_mt_approval_list.php");
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
    header("Location: for_mt_approval_list.php");
    exit;
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

<style>
    /* Ensure the right sidebar sticks properly */
    .sticky-admin-bar {
        position: sticky;
        top: 80px; /* Adjust based on navbar height */
        height: fit-content; /* Ensures it does not expand unnecessarily */
        max-height: calc(100vh - 100px); /* Prevents it from exceeding viewport */
        overflow-y: auto; /* Enables scrolling inside if needed */
        background-color: white; /* Keeps it visible */
        z-index: 10; /* Ensures it stays below navbar */
        padding: 15px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

</style>

<div class="content" >
    <div class="container mt-4">
        <h1>Review Invoice: <?php echo htmlspecialchars($invoice['tax_invoice_number']); ?></h1>
        <p>Masters Review Sheet - Please check all the details if correct</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="row" >
            <!-- Left Side - Invoice Details -->
            <div class="col-md-8" >
                <div class="card p-3" ">

                <div class="row">



                    <div class="col">
                        <div class="mb-3">
                            <label for="verifiedBy" class="form-label"><b>Verified By Manager</b></label>
                            <input type="text" class="form-control" id="verifiedBy" value="<?php echo htmlspecialchars($invoice['admin_reviewer_name']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label for="dateReviewed" class="form-label"><b>Date Reviewed</b></label>
                            <input type="text" class="form-control" id="dateReviewed" value="<?php echo htmlspecialchars($invoice['date_updated']); ?>" readonly>
                        </div>
                    </div>

                    <div class="col">
                        <div class="mb-3">
                            <label for="payor" class="form-label"><b>Responsible for Payment</b></label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['payor']); ?>" readonly>
                        </div>
                    </div>

                </div>




                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label for="reviewNotes" class="form-label"><b>Admin Note</b></label>
                            <textarea class="form-control" rows="3" readonly><?php echo htmlspecialchars($invoice['review_notes']); ?></textarea>
                        </div>
                    </div>
                </div>
            <hr>
                <div class="col text-white" style="background-color: #335E53; padding: 10px;margin-bottom: 10px">
                    <h5 style="padding-left: 10px;">Invoice Details</h5>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Tax Invoice Number</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['tax_invoice_number']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['priority']); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_type']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Invoice Class</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_class']); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Property Reference</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['property_reference']); ?>" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Payment Status</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['payment_status']); ?>" readonly>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['amount']); ?>" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Uploader Note</label>
                        <textarea class="form-control" rows="5" readonly><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Requested By</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['staff_name'] ?? 'Unknown'); ?>" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label class="form-label">Date Requested</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($invoice['date_requested'] ?? 'Unknown'); ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">PDF Invoice</label>
                            <?php if (!empty($invoice['pdf_invoice_path'])): ?>
                                <embed src="../dist/invoices/<?php echo htmlspecialchars($invoice['pdf_invoice_path']); ?>" type="application/pdf" width="100%" height="800px">
                            <?php else: ?>
                                <p class="text-danger">No PDF uploaded.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>

            </div>

            <!-- Right Side - Review Form -->
            <div class="col-md-4" >

                            <div class="card p-3 sticky-admin-bar" style="background-color: #335E53; color: #f8f9fa">
                                <h5>Masters Approval</h5>
                                <hr>
                                <form method="POST" action="../controller/for_mt_approval.php">
                                    <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">

                                    <div class="mb-3">
                                        <label for="approvalStatus" class="form-label"><b>Approval Status</b></label>
                                        <select name="approval_status" id="approvalStatus" class="form-select" required>
                                            <option value="" disabled selected>-Select-</option>
                                            <option value="Lodge Payment"  <?php echo $invoice['approval_status'] === 'Lodge Payment' ? 'selected' : ''; ?>>Approve</option>
                                            <option value="Denied"  <?php echo $invoice['approval_status'] === 'Denied' ? 'selected' : ''; ?>>Deny</option>
                                        </select>
                                    </div>

                                    <?php if ($invoice['approval_status'] !== 'MT Approval'): ?>
                                        <div class="mb-3">
                                            <label for="mtDate" class="form-label"><b>Date MT Approved</b></label>
                                            <input type="text" class="form-control" id="mtDate" value="<?php echo htmlspecialchars($invoice['mt_date']); ?>" readonly>
                                        </div>
                                    <?php endif; ?>


                                    <button type="submit" class="btn btn-success">Proceed</button>
                                    <a href="lodge_payment.php" class="btn btn-secondary">Cancel</a>
                                </form>
                            </div>



            </div>




        </div>
    </div>


<?php include 'includes/footer.php'; ?>
