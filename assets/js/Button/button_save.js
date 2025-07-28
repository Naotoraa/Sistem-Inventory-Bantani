document.addEventListener("DOMContentLoaded", function () {
  const saveBtn = document.querySelector(".save-btn");
  const saveSound = document.getElementById("saveSound");

  if (saveBtn) {
    saveBtn.addEventListener("click", function (e) {
      const form = saveBtn.closest("form");

      if (form) {
        if (form.checkValidity()) {
          if (saveSound) {
            saveSound.currentTime = 0;
            saveSound.play();
          }

          if (saveBtn.type !== "submit") {
            form.submit();
          }
        } else {
          e.preventDefault();
          form.reportValidity();
        }
      }
    });
  }

  const params = new URLSearchParams(window.location.search);
  const status = params.get("status");

  const alertMessages = {
    inserted: {
      icon: "success",
      title: "Sukses!",
      text: "Data berhasil disimpan!",
      timer: 2000,
      showConfirmButton: false,
    },
    updated: {
      icon: "success",
      title: "Sukses!",
      text: "Data berhasil diupdate!",
      timer: 2000,
      showConfirmButton: false,
    },
    error: {
      icon: "error",
      title: "Gagal!",
      text: "Terjadi kesalahan saat menyimpan data.",
      showConfirmButton: true,
    },
  };

  if (status && alertMessages[status]) {
    Swal.fire(alertMessages[status]);

    window.history.replaceState({}, document.title, window.location.pathname);
  }
});
