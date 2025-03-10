<?php
// Get the current page name (e.g., "dashboard.php")
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="topbar" style="z-index: 99!important;">

    <!-- Logo and System Title -->
    <div style="flex-grow: 1; display: flex; align-items: center;">
<!--        <img src="../dist/img/logo.png" alt="System Logo" style="height: 40px; margin-right: 10px;">-->
        <span style="font-size: 1.25rem;">Bodhitree Group Tax Invoice System</span>
    </div>

    <!-- User Account Menu -->
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['username']; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
            <li><a class="dropdown-item" href="../staff/account.php"><i class="fas fa-user"></i> My Account</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2 class="text-center" style="font-size: 1.1rem;"> Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <h6 class="text-center" style="font-size: 0.8rem;margin-bottom: 20px"><?php echo "Today is " . date("l");?> <?php echo date("Y-m-d");?></h6>
    <a href="../staff/dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="../staff/upload_invoice.php" class="<?php echo $current_page == 'upload_invoice.php' ? 'active' : ''; ?>"><i class="fa-solid fa-file-arrow-up"></i> Upload New Tax Invoice</a>
    <a href="../staff/pending_requests.php" class="<?php echo $current_page == 'pending_requests.php' ? 'active' : ''; ?>"><i class="fa-solid fa-clock-rotate-left"></i> Pending Requests</a>
    <a href="../staff/approved_requests.php" class="nav-link <?php echo ($current_page == 'approved_requests') ? 'active' : ''; ?>" ><i class="fa-solid fa-square-check"></i> Approved Requests</a>
    <a href="../staff/denied_requests.php" class="nav-link <?php echo ($current_page == 'denied_requests') ? 'active' : ''; ?>" ><i class="fa-solid fa-circle-xmark"></i> Denied Requests</a>
    <a href="../staff/all_records.php" class="<?php echo $current_page == 'all_records.php' ? 'active' : ''; ?>"><i class="fa-solid fa-folder-open"></i> My Records</a>
    <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>