// Fungsi untuk Edit Data langsung di Modal
function editData(id, field, oldValue) {
  const input = document.getElementById(field + "_" + id);
  // Mengambil elemen tombol (karena di HTML posisinya tepat setelah div.form-floating)
  const btn = input.parentElement.nextElementSibling;

  if (input.readOnly) {
    // Ubah ke mode edit
    input.readOnly = false;
    input.focus();

    // Ganti icon ke save dan ubah warna tombol
    btn.innerHTML = '<i class="fas fa-save fa-sm"></i>';
    btn.classList.remove("text-success");
    btn.classList.add("text-primary");
  } else {
    // Simpan perubahan ke database via AJAX
    const newValue = input.value;

    fetch("../../config/update_modal.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id_barang=${id}&field=${field}&value=${encodeURIComponent(newValue)}`,
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "ok") {
          alert("Data berhasil diupdate!");
          // Kembalikan ke mode view
          input.readOnly = true;
          btn.innerHTML = '<i class="fas fa-pen fa-sm"></i>';
          btn.classList.remove("text-primary");
          btn.classList.add("text-success");
        } else {
          alert("Gagal update: " + data);
          // Kembalikan ke value lama jika gagal
          input.value = oldValue;
        }
      });
  }
}
