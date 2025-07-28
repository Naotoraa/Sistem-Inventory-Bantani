document
  .getElementById("generateSKUBtn")
  .addEventListener("click", function () {
    const nama = document.querySelector("input[name='nama']").value.trim();
    const tanggal = document.querySelector("input[name='tanggal']").value;

    if (!nama || !tanggal) {
      Swal.fire({
        icon: "warning",
        title: "Oops!",
        text: "Isi tanggal & nama terlebih dahulu.",
      });
      return;
    }

    const hurufNama = nama.replace(/\s+/g, "").substring(0, 1).toUpperCase(); // huruf pertama nama
    const tglObj = new Date(tanggal);
    const hari = String(tglObj.getDate()).padStart(2, "0");
    const bulan = String(tglObj.getMonth() + 1).padStart(2, "0");

    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const rand1 = letters.charAt(Math.floor(Math.random() * letters.length));
    const rand2 = letters.charAt(Math.floor(Math.random() * letters.length));

    const sku = `OP-${hurufNama}${hari}${bulan}${rand1}${rand2}`; // Contoh: OP-B0907XQ
    document.getElementById("sku").value = sku;

    Swal.fire({
      icon: "success",
      title: "SKU berhasil dibuat!",
      text: sku,
      showConfirmButton: false,
      timer: 2000,
    });
  });

function formatRupiah(input) {
  let angka = input.value.replace(/\D/g, "");
  input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function hitungJumlah() {
  let qty =
    parseInt(document.getElementById("qty").value.replace(/\D/g, "")) || 0;
  let harga =
    parseInt(document.getElementById("harga").value.replace(/\D/g, "")) || 0;
  let jumlah = qty * harga;
  document.getElementById("jumlah").value = jumlah
    .toString()
    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
