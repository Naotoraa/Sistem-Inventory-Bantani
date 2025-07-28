document.addEventListener("DOMContentLoaded", function () {
  const rowsPerPage = 10;
  const tableRows = document.querySelectorAll("#tabel-data tr");
  const paginationContainer = document.getElementById("pagination-barang");
  const handIcon = document.getElementById("hand-icon");
  const totalPages = Math.ceil(tableRows.length / rowsPerPage);
  let currentPage = 1;

  function showPage(page) {
    currentPage = page;
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    // Tampilkan baris yang sesuai
    tableRows.forEach((row, index) => {
      row.style.display = index >= start && index < end ? "" : "none";
    });

    // Render ulang tombol pagination
    renderPagination();

    // Tampilkan ikon tangan hanya di halaman pertama
    if (handIcon) {
      handIcon.style.display = page === 1 ? "inline-block" : "none";
    }
  }

  function renderPagination() {
    paginationContainer.innerHTML = "";

    // Tombol Prev
    const prev = document.createElement("div");
    prev.className = "pag-link";
    prev.innerText = "Prev";
    if (currentPage === 1) {
      prev.setAttribute("disabled", true);
      prev.style.opacity = "0.5";
      prev.style.cursor = "not-allowed";
    } else {
      prev.addEventListener("click", () => showPage(currentPage - 1));
    }
    paginationContainer.appendChild(prev);

    // Nomor Halaman
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement("div");
      pageBtn.className = "pag-link";
      pageBtn.innerText = i;
      if (i === currentPage) {
        pageBtn.classList.add("active");
      }
      pageBtn.addEventListener("click", () => showPage(i));
      paginationContainer.appendChild(pageBtn);
    }

    // Tombol Next
    const next = document.createElement("div");
    next.className = "pag-link";
    next.innerText = "Next";
    if (currentPage === totalPages) {
      next.setAttribute("disabled", true);
      next.style.opacity = "0.5";
      next.style.cursor = "not-allowed";
    } else {
      next.addEventListener("click", () => showPage(currentPage + 1));
    }
    paginationContainer.appendChild(next);
  }

  if (tableRows.length > 0) {
    showPage(1);
  }
});
