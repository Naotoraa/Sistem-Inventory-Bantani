document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("button[data-link]").forEach(function (button) {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      const link = this.getAttribute("data-link");

      if (this.classList.contains("delete-btn")) {
        Swal.fire({
          title: "Yakin ingin menghapus?",
          text: "Data yang dihapus tidak bisa dikembalikan!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonColor: "#3085d6",
          confirmButtonText: "Ya, hapus!",
          cancelButtonText: "Batal",
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = link;
          }
        });
      } else {
        window.location.href = link;
      }
    });
  });
});
