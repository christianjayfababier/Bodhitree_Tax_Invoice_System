<?php
session_start();
require_once '../config.php';

// Fetch staff notifications
$stmt = $conn->prepare("SELECT * FROM notifications WHERE staff_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $_SESSION['staff_id']);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

include '../includes/head.php';
include '../includes/header.php';
?>

<div class="d-flex">
    <nav class="sidebar bg-dark" style="width: 250px;">
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="upload_invoice.php">Upload Invoice</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="pending_requests.php">Pending Requests</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="approved_requests.php">Approved</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="../logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="content flex-grow-1 p-4">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
