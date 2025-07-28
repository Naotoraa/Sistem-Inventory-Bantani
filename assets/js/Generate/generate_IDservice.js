document.getElementById("generateIDBtn").addEventListener("click", function () {
  const nama = document.getElementById("nama_barang").value.trim();
  const tanggal = document.getElementById("tanggal_service").value;

  if (!nama || !tanggal) {
    Swal.fire({
      icon: "warning",
      title: "Oops!",
      text: "Isi tanggal & nama terlebih dahulu.",
    });
    return;
  }

  const namaSingkat = nama.replace(/\s+/g, "").substring(0, 2).toUpperCase(); // LA
  const tanggalObj = new Date(tanggal);
  const day = String(tanggalObj.getDate()).padStart(2, "0"); // 08
  const angkaAcak = Math.floor(10 + Math.random() * 90); // 27
  const hurufAcak = String.fromCharCode(65 + Math.floor(Math.random() * 26)); // X

  const id = `SV-${namaSingkat}${day}${angkaAcak}${hurufAcak}`;

  document.getElementById("id_service").value = id;

  Swal.fire({
    icon: "success",
    title: "ID berhasil dibuat!",
    text: id,
    showConfirmButton: false,
    timer: 2000,
  });
});
function formatRupiah(input) {
  let angka = input.value.replace(/[^,\d]/g, "").toString();
  let split = angka.split(",");
  let sisa = split[0].length % 3;
  let rupiah = split[0].substr(0, sisa);
  let ribuan = split[0].substr(sisa).match(/\d{3}/g);

  if (ribuan) {
    let separator = sisa ? "." : "";
    rupiah += separator + ribuan.join(".");
  }

  rupiah = split[1] !== undefined ? rupiah + "," + split[1] : rupiah;
  input.value = rupiah;
}
