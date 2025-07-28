document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-btn");
  const deleteSound = document.getElementById("deleteSound");

  deleteButtons.forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();

      const targetUrl = btn.getAttribute("data-link");

      Swal.fire({
        title: "Yakin mau hapus?",
        text: "Data tidak bisa dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          if (deleteSound) {
            deleteSound.currentTime = 0;
            deleteSound.play();
          }

          setTimeout(() => {
            window.location.href = targetUrl;
          }, 300);
        }
      });
    });
  });
});
