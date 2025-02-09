<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'staff') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "My Records";

// Include database connection
require_once '../config.php';

// Get logged-in staff ID
$staff_id = $_SESSION['user_id'];

// Handle filters and search
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM invoice_list WHERE staff_id = $staff_id";

if ($status_filter) {
    $query .= " AND approval_status = '$status_filter'";
}

if ($search_query) {
    $query .= " AND (tax_invoice_number LIKE '%$search_query%' OR invoice_type LIKE '%$search_query%')";
}

$result = $conn->query($query);
?>

<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1>My Records</h1>
        <p>View all your submitted tax invoices below. Use the filters or search to find specific records.</p>

        <!-- Filter and Search Form -->
        <form method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $status_filter == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Denied" <?php echo $status_filter == 'Denied' ? 'selected' : ''; ?>>Denied</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by Invoice Number or Type" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Records Table -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>TX ID</th>
                <th>Priority</th>
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Staff Notes</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Admins Remark</th>
                <th>Signed Admin</th>
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
                        <td><?php echo htmlspecialchars($row['notes']); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_requested'])); ?></td>
                        <td><?php echo htmlspecialchars($row['review_notes']); ?></td>
                        <td><?php echo htmlspecialchars($row['admin_reviewer_name']); ?></td>
                        <td>
                            <a href="../dist/invoices/<?php echo $row['pdf_invoice_path']; ?>" class="btn btn-primary btn-sm" target="_blank">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No records found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
