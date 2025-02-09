document.addEventListener("DOMContentLoaded", function () {
    const bellIcon = document.querySelector(".bell-icon");
    const notificationDropdown = document.querySelector(".notification-dropdown");

    if (bellIcon && notificationDropdown) {
        bellIcon.addEventListener("click", function () {
            notificationDropdown.classList.toggle("d-none");
        });
    }
});
