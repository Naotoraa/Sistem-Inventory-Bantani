document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".pdf").addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const bulanValue = bulanInput?.value;

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

    const dataRows = Array.from(document.querySelectorAll("#tabel-data tr"));
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

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    // LOGO
    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => (logo.onload = resolve));
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    // HEADER PDF
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text(
      "LAPORAN CICILAN ASET / KENDARAAN",
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
      [
        "No",
        "No Cicilan",
        "Nama Barang",
        "Tanggal",
        "Pokok",
        "Bunga",
        "Total",
        "Keterangan",
      ],
    ];
    const rows = [];
    let totalSemua = 0;

    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 8) {
        const total =
          parseInt(cells[6].innerText.trim().replace(/[^0-9]/g, "")) || 0;
        totalSemua += total;

        rows.push([
          cells[0].innerText.trim(),
          cells[1].innerText.trim(),
          cells[2].innerText.trim(),
          cells[3].innerText.trim(),
          cells[4].innerText.trim(),
          cells[5].innerText.trim(),
          "Rp. " + total.toLocaleString("id-ID"),
          cells[7].innerText.trim(),
        ]);
      }
    });

    const totalSemuaFmt = "Rp. " + totalSemua.toLocaleString("id-ID");

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
            colSpan: 6,
            styles: { halign: "center", fontStyle: "bold" },
          },
          {
            content: totalSemuaFmt,
            styles: { halign: "center", fontStyle: "bold" },
          },
          { content: "", styles: {} },
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
    doc.text("Manager Keuangan", pageWidth - rightMargin - 130, baseY + 73);

    // SIMPAN FILE
    const fileName = `Laporan_Cicilan_${bulanText.replace(/\s+/g, "_")}.pdf`;
    doc.save(fileName);
  });
});
