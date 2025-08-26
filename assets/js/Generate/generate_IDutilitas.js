document
  .getElementById("generateIdUtilitasBtn")
  .addEventListener("click", function () {
    const pembayaran = document.getElementById("pembayaran")?.value || "";
    const tanggal = document.getElementById("tanggal")?.value || "";

    if (!pembayaran || !tanggal) {
      Swal.fire({
        icon: "warning",
        title: "Oops...",
        text: "Harap isi field Pembayaran dan Tanggal terlebih dahulu!",
      });
      return;
    }

    // Ambil 3 huruf pertama pembayaran, uppercase
    const prefix = pembayaran.substring(0, 3).toUpperCase();

    // Format tanggal ke DDMMYY
    const dateObj = new Date(tanggal);
    const year = String(dateObj.getFullYear()).slice(-2);
    const month = String(dateObj.getMonth() + 1).padStart(2, "0");
    const day = String(dateObj.getDate()).padStart(2, "0");
    const formattedDate = day + month + year;

    // Generate 2 huruf random A-Z
    const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    const randomSuffix =
      letters[Math.floor(Math.random() * 26)] +
      letters[Math.floor(Math.random() * 26)];

    // Gabung
    const idUtilitas = `${prefix}-${formattedDate}-${randomSuffix}`;

    // Set ke input
    document.getElementById("id_utilitas").value = idUtilitas;

    // Notif sukses
    Swal.fire({
      icon: "success",
      title: "ID berhasil dibuat!",
      text: "ID Utilitas: " + idUtilitas,
      timer: 2000,
      showConfirmButton: false,
    });
  });
