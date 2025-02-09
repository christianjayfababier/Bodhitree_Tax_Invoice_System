document.addEventListener("DOMContentLoaded", function () {
    const bellIcon = document.querySelector(".bell-icon");
    const notificationDropdown = document.querySelector(".notification-dropdown");

    if (bellIcon && notificationDropdown) {
        bellIcon.addEventListener("click", function () {
            notificationDropdown.classList.toggle("d-none");
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const toggleSidebar = document.getElementById("toggleSidebar");
    const sidebar = document.querySelector(".sidebar");

    toggleSidebar.addEventListener("click", () => {
        sidebar.style.width = sidebar.style.width === "0px" ? "250px" : "0px";
    });
});
// Enable Bootstrap validation
(function () {
    'use strict';

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission if they are invalid
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
})();
