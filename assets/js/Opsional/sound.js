document.addEventListener("DOMContentLoaded", function () {
  const saveBtn = document.querySelector(".save-btn");
  const saveSound = document.getElementById("saveSound");

  if (saveBtn) {
    saveBtn.addEventListener("click", function (e) {
      const form = saveBtn.closest("form");

      if (form) {
        if (form.checkValidity()) {
          // mainkan suara, tapi biarkan form submit secara normal
          saveSound.currentTime = 0;
          saveSound.play();
          // jangan preventDefault, biarkan PHP yang handle SweetAlert
        } else {
          form.reportValidity();
          e.preventDefault(); // hanya prevent jika form tidak valid
        }
      }
    });
  }
});
