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

    // CEK DATA TABEL
    const dataRows = Array.from(document.querySelectorAll("#Table tbody tr"));
    const hasValidData = dataRows.some((row) => {
      const cells = Array.from(row.querySelectorAll("td"));
      return (
        cells.length > 1 &&
        cells.some((td, i) => i > 0 && td.textContent.trim() !== "")
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

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    // LOGO
    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => {
      logo.onload = resolve;
    });
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    // HEADER PDF
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text(
      "LAPORAN BIAYA SERVICE",
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

    // PERIODE
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

    // HEADER TABEL
    const headers = [
      ["No", "ID Service", "Nama Barang", "Keterangan", "Tanggal", "Biaya"],
    ];
    const rows = [];
    let totalBiaya = 0;

    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 6) {
        const biayaRaw = cells[5].textContent.trim().replace(/[^0-9]/g, "");
        const biayaValue = parseInt(biayaRaw, 10) || 0;
        totalBiaya += biayaValue;

        rows.push([
          cells[0].textContent.trim(),
          cells[1].textContent.trim(),
          cells[2].textContent.trim(),
          cells[3].textContent.trim(),
          cells[4].textContent.trim(),
          "Rp. " + biayaValue.toLocaleString("id-ID"),
        ]);
      }
    });

    const totalFormatted = "Rp. " + totalBiaya.toLocaleString("id-ID");

    // AUTO TABLE
    doc.autoTable({
      head: headers,
      body: rows,
      startY: 120,
      theme: "plain",
      headStyles: {
        fillColor: [0, 102, 102],
        textColor: 255,
        fontStyle: "bold",
        halign: "center",
      },
      bodyStyles: { halign: "center", fontSize: 10, cellPadding: 6 },
      alternateRowStyles: { fillColor: [230, 245, 233] },
      foot: [
        [
          {
            content: "TOTAL",
            colSpan: 5,
            styles: { halign: "center", fontStyle: "bold" },
          },
          {
            content: totalFormatted,
            styles: { halign: "center", fontStyle: "bold" },
          },
        ],
      ],
      footStyles: {
        fillColor: [0, 102, 102],
        textColor: 255,
        fontStyle: "bold",
      },
      margin: { bottom: 120 },
    });

    // FOOTER & TTD
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

    // SIMPAN FILE
    const fileName = `Laporan_Biaya_Service_${bulanText.replace(
      /\s+/g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  });
});
