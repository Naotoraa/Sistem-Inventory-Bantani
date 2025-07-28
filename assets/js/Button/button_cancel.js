function cancelAction(redirectUrl) {
  Swal.fire({
    title: "Yakin ingin membatalkan?",
    text: "Data yang sudah diisi tidak akan disimpan.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, batalkan",
    cancelButtonText: "Kembali",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
  }).then((result) => {
    if (result.isConfirmed && redirectUrl) {
      window.location.href = redirectUrl;
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const cancelButtons = document.querySelectorAll(".cancel-btn");

  if (cancelButtons.length > 0) {
    cancelButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const redirectUrl =
          btn.getAttribute("data-redirect") || "dashboard.html";
        cancelAction(redirectUrl);
      });
    });
  }
});
