<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'master') {
    header("Location: ../login.php");
    exit;
}
$pageTitle = "Request List";
require_once '../config.php';

// Filters
$status_filter = $_GET['status'] ?? '';
$search_query = $_GET['search'] ?? '';
$date_filter = $_GET['date_filter'] ?? '';

// Sorting
$sort_order = $_GET['sort'] ?? 'DESC'; // Default to newest-first
$sort_icon = $sort_order === 'DESC' ? '🔽' : '🔼'; // Toggle icon

// Build query with filters
$query = "SELECT * FROM invoice_list WHERE 1";

if (!empty($status_filter)) {
    $query .= " AND approval_status = '$status_filter'";
}

if (!empty($search_query)) {
    $query .= " AND (tax_invoice_number LIKE '%$search_query%' OR invoice_type LIKE '%$search_query%')";
}

if (!empty($date_filter)) {
    $query .= " AND DATE(date_requested) = '$date_filter'";
}

// Add sorting
$query .= " ORDER BY date_requested $sort_order";
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
                <div class="col-md-2">
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
                    <input type="date" name="date_filter" class="form-control" value="<?php echo htmlspecialchars($_GET['date_filter'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <!-- Request Table -->
        <table class="table table-bordered" >
            <thead style="background-color: #335E53; color: #f8f9fa">
            <tr style="font-size: 0.85em; vertical-align: middle;">

                <th>Status</th>
                <th>Priority</th>
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Invoice Class</th>
                <th>Property Reference</th>
                <th>Payment Responsible</th>
                <th>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => $sort_order === 'DESC' ? 'ASC' : 'DESC'])); ?>">
                        Date Submitted <?php echo $sort_icon; ?>
                    </a>
                </th>
                <th>Admins Remark</th>
                <th>Signed Admin</th>

            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="font-size: 0.8em; vertical-align: middle;" >
                    <td>
                        <?php if ($row['approval_status'] == 'Pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($row['approval_status'] == 'MT Approval'): ?>
                            <span class="badge text-white" style="background-color: #335E53;">For MT Approval</span>
                        <?php elseif ($row['approval_status'] == 'Approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php elseif ($row['approval_status'] == 'Lodge Payment'): ?>
                            <span class="badge bg-secondary">Lodge Payment</span>
                        <?php elseif ($row['approval_status'] == 'Payment Lodged'): ?>
                            <span class="badge bg-info text-dark">For Final Approval</span>
                        <?php elseif ($row['approval_status'] == 'Denied'): ?>
                            <span class="badge bg-danger">Denied</span>
                        <?php endif; ?>
                    </td>
                    <td><b><u><i><?php echo htmlspecialchars($row['priority']); ?></i></u></b></td>
                    <td><?php echo htmlspecialchars($row['tax_invoice_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['invoice_class']); ?></td>
                    <td><?php echo htmlspecialchars($row['property_reference']); ?></td>
                    <td><?php echo htmlspecialchars($row['payor']); ?></td>
                    <td><?php echo date("d-m-Y H:i", strtotime($row['date_requested'])); ?></td>
                    <td><?php echo htmlspecialchars($row['review_notes']); ?></td>
                    <td><?php echo htmlspecialchars($row['admin_reviewer_name']); ?></td>

                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
