document.addEventListener("DOMContentLoaded", function () {
  const editButtons = document.querySelectorAll(".btn-edit");

  editButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const field = this.dataset.field;
      const input = document.getElementById(field + "_" + id);

      if (input.readOnly) {
        // ubah ke mode edit
        input.readOnly = false;
        input.focus();
        this.innerHTML = '<i class="fas fa-save"></i>'; // icon save
        this.classList.remove("btn-primary");
        this.classList.add("btn-success");
      } else {
        // simpan perubahan ke database via AJAX
        const newValue = input.value;

        fetch("../../config/update_modal.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `id_barang=${id}&field=${field}&value=${encodeURIComponent(
            newValue
          )}`,
        })
          .then((res) => res.text())
          .then((data) => {
            if (data.trim() === "ok") {
              alert("Data berhasil diupdate!");
              input.readOnly = true;
              this.innerHTML = '<i class="fas fa-edit"></i>';
              this.classList.remove("btn-success");
              this.classList.add("btn-primary");
            } else {
              alert("Gagal update: " + data);
            }
          });
      }
    });
  });
});
