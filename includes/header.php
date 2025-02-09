<div class="topbar">
    <div class="topbar-left">
        <span class="text-white">System Logo</span>
    </div>
    <div class="topbar-right">
        <div class="notifications">
            <i class="fas fa-bell bell-icon"></i>
            <div class="notification-dropdown d-none bg-white shadow rounded">
                <ul class="list-group">
                    <?php if (isset($notifications) && count($notifications) > 0): ?>
                        <?php foreach ($notifications as $notification): ?>
                            <li class="list-group-item">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">No new notifications.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <span class="text-white ml-3"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>
</div>
