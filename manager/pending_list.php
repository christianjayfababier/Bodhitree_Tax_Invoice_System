<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Pending List";
require_once '../config.php';

// Filters
$search_query = $_GET['search'] ?? '';
$date_filter = $_GET['date_filter'] ?? '';

// Sorting
$sort_order = $_GET['sort'] ?? 'DESC'; // Default to newest-first
$sort_icon = $sort_order === 'DESC' ? '🔽' : '🔼'; // Toggle icon

// Build query with filters
$query = "SELECT * FROM invoice_list WHERE approval_status = 'Pending' AND invoice_class = 'Repair and Maintenance'";



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
        <h1>Pending List</h1>

        <!-- Filter Form -->
        <form method="GET" class="mb-4">
            <div class="row">
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
                <th>Invoice Number</th>
                <th>Invoice Type</th>
                <th>Priority</th>
                <th>Staff Note</th>
                <th>Invoice Class</th>
                <th>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sort' => $sort_order === 'DESC' ? 'ASC' : 'DESC'])); ?>">
                        Date Submitted <?php echo $sort_icon; ?>
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr style="font-size: 0.8em; vertical-align: middle;" >
                    <td>
                        <?php if ($row['approval_status'] == 'Pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['tax_invoice_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['invoice_type']); ?></td>
                    <td><b><i><u><?php echo htmlspecialchars($row['priority']); ?></b></i></u></td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                    <td><?php echo htmlspecialchars($row['invoice_class']); ?></td>
                    <td><?php echo date("d-m-Y H:i", strtotime($row['date_requested'])); ?></td>
                    <td>
                        <a href="review_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" style="width: 100%">Review</a>
                        <a href="generate_report.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm" style="margin-top:2px;width: 100%">Download PDF Report</a>


                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
