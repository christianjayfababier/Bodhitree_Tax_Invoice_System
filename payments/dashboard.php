<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'payments') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Payments Dashboard";

// Include database connection
require_once '../config.php';

// Fetch counts for dashboard tiles
$lodge_payment_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE approval_status = 'Lodge Payment'")->fetch_assoc()['count'];
$approved_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE approve_true = 'Approved'")->fetch_assoc()['count'];
$total_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list")->fetch_assoc()['count'];
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Dashboard</h1>
            <p>This is your dashboard. Below are the counts for tax invoice records and payment lodging requests.</p>
            <hr>
            <!-- Dashboard Tiles -->
            <div class="row">

                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-4">
                        <div class="card-body">
                            <h5 class="card-title">For Lodge Payment</h5>
                            <p class="card-text fs-3"><?php echo $lodge_payment_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Approved Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $approved_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-secondary mb-4">
                        <div class="card-body">
                            <h5 class="card-title">All Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $total_count; ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Pending Invoices Table -->
            <h2 class="mt-4">Pending Lodge Payment Request</h2>
            <span>Ordered from Oldest to Newest to always review Tax Invoices that has been submitted first</span>
            <hr>
            <table class="table table-bordered">
                <thead style="background-color: #335E53; color: #f8f9fa">
                <tr>
                    <th>TX ID</th>
                    <th>Invoice Number</th>
                    <th>Invoice Type</th>
                    <th>Priority</th>
                    <th>Date Submitted</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $lodge_pending = $conn->query("SELECT * FROM invoice_list WHERE approval_status = 'Lodge Payment'");
                if ($lodge_pending->num_rows > 0):
                    while ($row = $lodge_pending->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['tax_invoice_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                            <td><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($row['priority']); ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($row['date_requested'])); ?></td>
                            <td>
                                <a href="lodge_payment.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Review</a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No pending invoices found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>