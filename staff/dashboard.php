<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Staff Dashboard";

// Include database connection
require_once '../config.php';

// Get logged-in staff ID
$staff_id = $_SESSION['user_id'];

// Fetch counts for dashboard tiles
$pending_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE approval_status = 'Pending' AND staff_id = $staff_id")->fetch_assoc()['count'];
$approved_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE approval_status = 'Approved' AND staff_id = $staff_id")->fetch_assoc()['count'];
$denied_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE approval_status = 'Denied' AND staff_id = $staff_id")->fetch_assoc()['count'];
$total_count = $conn->query("SELECT COUNT(*) as count FROM invoice_list WHERE staff_id = $staff_id")->fetch_assoc()['count'];
?>

<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Dashboard</h1>
            <p>This is your staff dashboard. Below are the counts for your tax invoice records.</p>

            <!-- Dashboard Tiles -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Pending Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $pending_count; ?></p>
                            <a href="pending_requests.php" class="btn btn-primary mt-4">View Pending Records</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Approved Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $approved_count; ?></p>
                            <a href="approved_requests.php" class="btn btn-primary mt-4">View Approved Records</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Rejected Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $denied_count; ?></p>
                            <a href="denied_requests.php" class="btn btn-primary mt-4">View Denied Records</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">All Tax Invoices</h5>
                            <p class="card-text fs-3"><?php echo $total_count; ?></p>
                            <a href="all_records.php" class="btn btn-primary mt-4">View All Records</a>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

<?php include '../includes/footer.php'; ?>