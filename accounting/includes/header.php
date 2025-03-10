<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get current page name
?>
<div class="topbar" style="z-index: 1000">

    <!-- Logo and System Title -->
    <div style="flex-grow: 1; display: flex; align-items: center;">
<!--        <img src="../dist/img/logo.png" alt="System Logo" style="height: 40px; margin-right: 10px;">-->
        <span style="font-size: 1.25rem;">Bodhitree Group Tax Invoice System</span>
    </div>

    <!-- User Account Menu -->
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="accountMenu" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-shield"></i> <?php echo $_SESSION['username']; ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountMenu">
            <li><a class="dropdown-item" href="account.php"><i class="fas fa-user"></i> My Account</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <h2 class="text-center" style="font-size: 1.1rem;"> Welcome, <?php echo $_SESSION['firstname']; ?>!</h2>
    <h6 class="text-center" style="font-size: 0.8rem;margin-bottom: 20px"><?php echo "Today is " . date("l");?> <?php echo date("d-m-Y");?></h6>

    <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <hr>
    <span style="padding-left: 10px"><b>Main Menu</b></span>
    <a href="pending_list.php" class="<?php echo $current_page == 'pending_list.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> Pending List</a>
    <hr>
    <span style="padding-left: 10px"><b>Other Access</b></span>
    <a href="request_list.php" class="<?php echo $current_page == 'request_list.php' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> All Request List</a>
    <a href="reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Reports</a>
     <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>