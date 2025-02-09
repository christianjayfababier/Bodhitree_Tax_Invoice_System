<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Request List";
require_once '../config.php';

// Filters
$status_filter = $_GET['status'] ?? '';
$search_query = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT * FROM invoice_list WHERE 1";

if (!empty($status_filter)) {
    $query .= " AND approval_status = '$status_filter'";
}

if (!empty($search_query)) {
    $query .= " AND (tax_invoice_number LIKE '%$search_query%' OR invoice_type LIKE '%$search_query%')";
}

$result = $conn->query($query);
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1>All Requests</h1>

        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Approved" <?php echo $status_filter == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Denied" <?php echo $status_filter == 'Denied' ? 'selected' : ''; ?>>Denied</option>
                        <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
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

        <!-- Request Table -->
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>TX ID</th>
                <th>Status</th>
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Priority</th>
                <th>Staff Note</th>
                <th>Date Submitted</th>
                <th>Admins Remark</th>
                <th>Signed Admin</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td ><?php echo $row['id']; ?></td>
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
                    <td style="text-align: center">
                        <?php if ($row['priority'] == 'Low'): ?>
                            <span style="font-size: .8em;">Low</span>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        <?php elseif ($row['priority'] == 'Medium'): ?>
                            <span style="font-size: .8em;">Medium</span>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        <?php elseif ($row['priority'] == 'High'): ?>
                            <span style="font-size: .8em;">High</span>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        <?php elseif ($row['priority'] == 'Urgent'): ?>
                            <span style="font-size: .8em;">Urgent</span>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    <td><?php echo date("Y-m-d H:i", strtotime($row['date_requested'])); ?></td>
                    <td><?php echo htmlspecialchars($row['review_notes']); ?></td>
                    <td><?php echo htmlspecialchars($row['admin_reviewer_name']); ?></td>
                    <td>
                        <a href="review_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Review</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
