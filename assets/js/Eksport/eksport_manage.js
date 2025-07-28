document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".pdf").addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const bulanValue = bulanInput?.value;

    // === CEK BULAN DIPILIH ===
    if (!bulanValue) {
      Swal.fire({
        icon: "warning",
        title: "Periode belum dipilih",
        text: "Silakan pilih bulan terlebih dahulu sebelum export.",
        confirmButtonText: "OK",
      });
      bulanInput.focus();
      return;
    }

    // === CEK APAKAH ADA DATA ===
    const tbody = document.getElementById("tabel-data");
    const dataRows = Array.from(tbody.querySelectorAll("tr"));

    const hasValidData = dataRows.some((row) => {
      const cells = Array.from(row.querySelectorAll("td"));

      // Skip baris yang kurang dari 11 kolom (bukan baris data nyata)
      if (cells.length < 11) return false;

      // Cek apakah ada kolom selain 'No' yang berisi nilai nyata
      return cells.slice(1).some((td) => {
        const text = td.innerText.trim().toLowerCase();
        return text !== "" && text !== "-" && text !== "0";
      });
    });

    if (!hasValidData) {
      Swal.fire({
        icon: "info",
        title: "Tidak ada data",
        text: "Data stok barang untuk periode ini kosong.",
        confirmButtonText: "OK",
      });
      return;
    }

    // === SIAPKAN PDF ===
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => (logo.onload = resolve));
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("LAPORAN STOK BARANG", doc.internal.pageSize.getWidth() / 2, 40, {
      align: "center",
    });

    doc.setFont("helvetica", "normal");
    doc.setFontSize(10);
    doc.text(
      "Sistem Inventory PT Bantani Media Utama",
      doc.internal.pageSize.getWidth() / 2,
      60,
      { align: "center" }
    );

    const [tahun, bulan] = bulanValue.split("-");
    const namaBulan = [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ];
    const bulanText = `${namaBulan[parseInt(bulan) - 1]} ${tahun}`;
    doc.text(`Periode: ${bulanText}`, 40, 100);

    // === AMBIL DATA TABEL ===
    const headers = [
      [
        "No",
        "ID Barang",
        "Nama Barang",
        "Kategori",
        "Stok Awal",
        "Barang Masuk",
        "Barang Keluar",
        "Barang Migrasi",
        "Barang Eror",
        "Stok Akhir",
        "Satuan",
      ],
    ];

    const rows = [];
    dataRows.forEach((tr) => {
      const cells = Array.from(tr.querySelectorAll("td"));
      if (cells.length >= 11) {
        rows.push(cells.map((td) => td.innerText.trim()));
      }
    });

    doc.autoTable({
      head: headers,
      body: rows,
      startY: 120,
      theme: "striped",
      margin: { bottom: 120 },
      headStyles: {
        fillColor: [104, 159, 56],
        textColor: 255,
        fontStyle: "bold",
        halign: "center",
      },
      alternateRowStyles: {
        fillColor: [240, 240, 240],
      },
      styles: {
        fontSize: 10,
        cellPadding: 6,
        halign: "center",
      },
    });

    // === FOOTER ===
    const pageHeight = doc.internal.pageSize.getHeight();
    const pageWidth = doc.internal.pageSize.getWidth();

    doc.setFontSize(10);
    doc.text(
      "Exported: " + new Date().toLocaleDateString("id-ID"),
      40,
      pageHeight - 40
    );

    const baseY = pageHeight - 100;
    const rightMargin = 80;
    doc.text("Disetujui oleh,", pageWidth - rightMargin - 120, baseY);
    doc.text(
      "________________________",
      pageWidth - rightMargin - 160,
      baseY + 45
    );
    doc.setFont("helvetica", "bold");
    doc.text("Dede Irfan", pageWidth - rightMargin - 115, baseY + 60);
    doc.setFont("helvetica", "normal");
    doc.text("Manager Gudang", pageWidth - rightMargin - 130, baseY + 73);

    // === SIMPAN FILE ===
    const fileName = `Laporan Stok Barang - ${bulanText.replace(
      /\s+/g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  });
});
