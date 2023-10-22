var mainListDiv = document.getElementById("mainListDiv"),
    mediaButton = document.getElementById("mediaButton");

mediaButton.onclick = function () {

    "use strict";

    mainListDiv.classList.toggle("show_list");
    mediaButton.classList.toggle("active");

};

document.addEventListener("DOMContentLoaded", function () {
    const userDropdown = document.getElementById("userDropdown");
    const userDropdownMenu = document.getElementById("userDropdownMenu");

    userDropdown.addEventListener("click", function (e) {
        e.preventDefault(); // Evita que el enlace se active

        if (userDropdownMenu.style.display === "block") {
            userDropdownMenu.style.display = "none";
        } else {
            userDropdownMenu.style.display = "block";
        }
    });

    // Cerrar el menú cuando se hace clic en cualquier parte del documento
    document.addEventListener("click", function (e) {
        if (e.target !== userDropdown && e.target !== userDropdownMenu) {
            userDropdownMenu.style.display = "none";
        }
    });
});
