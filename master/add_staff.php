<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'master') {
    header("Location: ../login.php");
    exit;
}

$pageTitle = "Register User";
require_once '../config.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'staff'; // Default to 'staff'

    // Validate input
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } else {
        // Hash the password using md5
        $hashed_password = md5($password);

        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, email, password, role, created_at, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_message = "User registered successfully!";
        } else {
            $error_message = "Failed to register user. Username or email might already exist.";
        }
    }
}
?>

<?php include 'includes/head.php'; ?>
<?php include 'includes/header.php'; ?>

<div class="content">
    <div class="container mt-4">
        <h1>Register Staff</h1>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="firstname" class="form-label">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Register</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
