<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Staff List";
require_once '../config.php';

// Fetch all staff accounts
$result = $conn->query("SELECT * FROM users WHERE role = 'admin'");
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

    <div class="content">
        <div class="container mt-4">
            <h1>Admin List</h1>
            <p>Manage all admin accounts below.</p>

            <a href="add_admin.php" class="btn btn-primary mb-3">Add New Admin</a>

            <table class="table table-bordered">
                <thead style="background-color: #335E53; color: #f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname'] ); ?> <?php echo htmlspecialchars($row['lastname'] ); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo date("Y-m-d", strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="edit_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_admin.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>