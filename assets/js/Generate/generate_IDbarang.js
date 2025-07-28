function generateIDBarang() {
  const nama = document.getElementById("nama_barang").value.trim();
  const kategori = document.getElementById("kategori").value.trim();

  if (!nama || !kategori) {
    Swal.fire({
      icon: "warning",
      title: "Oops!",
      text: "Isi dulu Nama Barang dan Kategori!",
    });
    return;
  }

  const hurufNama =
    nama
      .match(/[A-Za-z]/g)
      ?.slice(0, 2)
      .join("")
      .toUpperCase() || "XX";
  const angkaNama = nama.match(/\d+/g)?.join("").slice(0, 2) || "00";
  const cleanKategori = kategori
    .replace(/[^A-Za-z]/g, "")
    .substring(0, 3)
    .toUpperCase();
  const kodeUnik = Date.now().toString().slice(-2);

  const id = `${hurufNama}${angkaNama}-${cleanKategori}-${kodeUnik}`;
  document.getElementById("id_barang").value = id;

  // Swal.fire({
  //   icon: "success",
  //   title: "Berhasil!",
  //   text: "ID Barang berhasil digenerate.",
  //   timer: 1500,
  //   showConfirmButton: false,
  // });
}


document.getElementById("generateBtn").addEventListener("click", () => {
  generateIDBarang();
});
