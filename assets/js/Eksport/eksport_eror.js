document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".pdf").addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const bulanValue = bulanInput?.value;

    // CEK BULAN
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

    // CEK DATA VALID (baris dengan minimal 1 isi selain nomor urut)
    const dataRows = Array.from(document.querySelectorAll("table tbody tr"));
    const hasValidData = dataRows.some((row) => {
      const cells = Array.from(row.querySelectorAll("td"));
      return (
        cells.length > 1 &&
        cells.some((td, i) => i > 0 && td.innerText.trim() !== "")
      );
    });

    if (!hasValidData) {
      Swal.fire({
        icon: "info",
        title: "Tidak ada data",
        text: "Data untuk periode ini kosong, tidak bisa diexport.",
        confirmButtonText: "OK",
      });
      return;
    }

    // ======== LANJUT EXPORT PDF ========
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    // Logo
    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => {
      logo.onload = resolve;
    });
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    // Header
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("LAPORAN BARANG EROR", doc.internal.pageSize.getWidth() / 2, 40, {
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

    // Periode
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

    // Table
    const headers = [
      [
        "No",
        "ID Barang",
        "Nama Barang",
        "Kategori",
        "QTY",
        "Satuan",
        "Keterangan",
        "Tanggal",
      ],
    ];
    const rows = [];

    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 7) {
        rows.push([
          cells[0].innerText.trim(),
          cells[1].innerText.trim(),
          cells[2].innerText.trim(),
          cells[3].innerText.trim(),
          cells[4].innerText.trim(),
          cells[5].innerText.trim(),
          cells[6].innerText.trim(),
          cells[7].innerText.trim(), // Keteranga
        ]);
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
        fontSize: 11,
        cellPadding: 7,
        halign: "center",
      },
    });

    // Footer & tanda tangan
    const pageHeight = doc.internal.pageSize.getHeight();
    const pageWidth = doc.internal.pageSize.getWidth();
    doc.setFontSize(10);
    doc.text(
      "Exported: " + new Date().toLocaleDateString("id-ID"),
      40,
      pageHeight - 40
    );

    const rightMargin = 80;
    const baseY = pageHeight - 100;
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

    const fileName = `Laporan Barang Eror - ${bulanText.replace(
      /\s+/g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  });
});
