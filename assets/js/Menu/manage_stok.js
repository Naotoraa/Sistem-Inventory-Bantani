const links = document.querySelectorAll(".pag-link");
const hand = document.getElementById("hand-icon");

window.addEventListener("DOMContentLoaded", () => {
  const firstLink = links[0];
  const rect = firstLink.getBoundingClientRect();
  const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
  const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

  hand.style.transform = `translate(${rect.left + scrollLeft}px, ${
    rect.top + scrollTop - -40
  }px)`;

  firstLink.classList.add("active");
});

links.forEach((link) => {
  link.addEventListener("click", (e) => {
    e.preventDefault();

    const rect = link.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft =
      window.pageXOffset || document.documentElement.scrollLeft;

    hand.style.transform = `translate(${rect.left + scrollLeft}px, ${
      rect.top + scrollTop - -40
    }px)`;

    links.forEach((l) => l.classList.remove("active"));
    link.classList.add("active");
  });
});
