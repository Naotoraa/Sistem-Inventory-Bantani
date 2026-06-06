document.addEventListener("DOMContentLoaded", function () {
  function cancelAction(button) {
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
      if (result.isConfirmed) {
        const redirectUrl = button.getAttribute("data-redirect");

        if (redirectUrl) {
          window.location.href = redirectUrl;
          return;
        }

        // reset form
        const form = document.querySelector("form");
        if (form) form.reset();

        // reset message
        const message = document.getElementById("passwordMessage");
        if (message) message.innerHTML = "";

        // disable save button
        const saveBtn = document.getElementById("saveBtn");
        if (saveBtn) saveBtn.disabled = true;

        // hide form
        const formBox = document.getElementById("passwordForm");
        if (formBox) formBox.style.display = "none";
      }
    });
  }

  document.querySelectorAll(".cancel-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      cancelAction(this);
    });
  });
  /* =========================
     TOGGLE PASSWORD EYE
  ========================== */
  document.querySelectorAll(".toggle-password").forEach(function (btn) {
    btn.addEventListener("click", function () {
      let targetId = this.getAttribute("data-target");
      let input = document.getElementById(targetId);
      let icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    });
  });

  /* =========================
     VALIDASI CHANGE PASSWORD
  ========================== */
  let oldPass = document.getElementById("oldPassword");
  let newPass = document.getElementById("newPassword");
  let confirmPass = document.getElementById("confirmPassword");
  let message = document.getElementById("passwordMessage");
  let saveBtn = document.getElementById("saveBtn");

  if (oldPass && newPass && confirmPass) {
    function checkPassword() {
      if (newPass.value === "" || confirmPass.value === "") {
        message.innerHTML = "";
        confirmPass.classList.remove("is-valid", "is-invalid");
        saveBtn.disabled = true;
        return;
      }

      if (newPass.value === oldPass.value) {
        message.innerHTML =
          '<span class="badge bg-warning fs-6 p-2">⚠ Password baru tidak boleh sama dengan password lama</span>';
        confirmPass.classList.remove("is-valid");
        confirmPass.classList.add("is-invalid");
        saveBtn.disabled = true;
        return;
      }

      if (newPass.value !== confirmPass.value) {
        message.innerHTML =
          '<span class="badge bg-danger fs-6 p-2">✖ Password Tidak Sama</span>';
        confirmPass.classList.remove("is-valid");
        confirmPass.classList.add("is-invalid");
        saveBtn.disabled = true;
        return;
      }

      message.innerHTML =
        '<span class="badge bg-success fs-6 p-2">✔ Password Cocok</span>';
      confirmPass.classList.remove("is-invalid");
      confirmPass.classList.add("is-valid");
      saveBtn.disabled = false;
    }

    oldPass.addEventListener("input", checkPassword);
    newPass.addEventListener("input", checkPassword);
    confirmPass.addEventListener("input", checkPassword);
  }
});

/* =========================
   TOGGLE FORM (GLOBAL)
========================== */
function togglePasswordForm() {
  const form = document.getElementById("passwordForm");
  if (form) {
    form.style.display = form.style.display === "none" ? "block" : "none";
  }
}
