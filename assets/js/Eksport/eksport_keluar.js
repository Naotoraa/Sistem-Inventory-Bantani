document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".pdf").addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const bulanValue = bulanInput?.value;
    const mingguValue = document.querySelector("select[name='minggu']")?.value;

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

    // === CEK DATA TERSEDIA ===
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

    // === SIAPKAN PDF ===
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    // === LOGO ===
    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => {
      logo.onload = resolve;
    });
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    // === HEADER PDF ===
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text(
      "LAPORAN BARANG KELUAR",
      doc.internal.pageSize.getWidth() / 2,
      40,
      { align: "center" }
    );

    doc.setFont("helvetica", "normal");
    doc.setFontSize(10);
    doc.text(
      "Sistem Inventory PT Bantani Media Utama",
      doc.internal.pageSize.getWidth() / 2,
      60,
      { align: "center" }
    );

    // === BULAN + MINGGU TAMPILAN ===
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

    let periodeText = `Periode: ${bulanText}`;
    if (mingguValue) {
      const mingguMap = {
        1: "Minggu 1 (1–7)",
        2: "Minggu 2 (8–14)",
        3: "Minggu 3 (15–21)",
        4: "Minggu 4 (22–akhir)",
      };
      const labelMinggu = mingguMap[mingguValue] || "";
      periodeText = `Periode: ${labelMinggu} ${bulanText}`;
    }

    doc.text(periodeText, 40, 100);

    // === TABEL ===
    const headers = [
      [
        "No",
        "ID Barang",
        "Nama Barang",
        "Kategori",
        "QTY",
        "Satuan",
        "Tanggal Keluar",
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

    // === FOOTER & TTD ===
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

    // === SIMPAN PDF ===
    const fileName = `Laporan Barang Keluar - ${
      mingguValue ? "Minggu" + mingguValue + " - " : ""
    }${bulanText.replace(/\s+/g, "_")}.pdf`;
    doc.save(fileName);
  });
});
