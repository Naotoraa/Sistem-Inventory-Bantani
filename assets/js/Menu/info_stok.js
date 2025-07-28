partials.forEach((part) => {
  fetch(part.file)
    .then((res) => res.text())
    .then((html) => {
      document.getElementById(part.id).innerHTML = html;
    });
});
partials.forEach((part) => {
  fetch(part.file)
    .then((res) => res.text())
    .then((html) => {
      document.getElementById(part.id).innerHTML = html;

      if (part.id === "sidebar-placeholder") {
        activateSubmenuHover();
      }
    });
});

function activateSubmenuHover() {
  const navItem = document.querySelector(".nav-item");
  const submenu = document.querySelector(".submenu");

  if (navItem && submenu) {
    navItem.addEventListener("mouseenter", () => {
      submenu.style.display = "flex";
    });

    navItem.addEventListener("mouseleave", () => {
      submenu.style.display = "none";
    });
  }
}
