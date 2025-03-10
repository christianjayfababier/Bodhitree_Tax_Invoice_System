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
$date_filter = $_GET['date_filter'] ?? '';

// Sorting
$sort_order = $_GET['sort'] ?? 'DESC'; // Default to newest-first
$sort_icon = $sort_order === 'DESC' ? '🔽' : '🔼'; // Toggle icon

$query = "SELECT * FROM invoice_list WHERE staff_id = $staff_id";

if ($status_filter) {
    $query .= " AND approval_status = '$status_filter'";
}

if ($search_query) {
    $query .= " AND (tax_invoice_number LIKE '%$search_query%' OR invoice_type LIKE '%$search_query%')";
}

if (!empty($date_filter)) {
    $query .= " AND DATE(date_requested) = '$date_filter'";
}

// Add sorting
$query .= " ORDER BY date_requested $sort_order";
$result = $conn->query($query);

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
                    <div class="col-md-2">
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
                        <input type="date" name="date_filter" class="form-control" value="<?php echo htmlspecialchars($_GET['date_filter'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>

            <!-- Records Table -->
            <table class="table table-bordered">
                <thead style="background-color: #335E53; color: #f8f9fa">
                <tr style="font-size: 0.85em">
                    <th>Status</th>
                    <th>Invoice Number</th>
                    <th>Invoice Type</th>
                    <th>Priority</th>
                    <th>My Note</th>
                    <th>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => $sort_order === 'DESC' ? 'ASC' : 'DESC'])); ?>">
                            Date Submitted <?php echo $sort_icon; ?>
                        </a>
                    </th>
                    <th>Admins Remark</th>
                    <th>Signed Admin</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr style="font-size: 0.85em">
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