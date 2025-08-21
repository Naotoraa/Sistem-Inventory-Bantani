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

    // CEK DATA VALID
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

    // SIAPKAN PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    // LOGO
    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => {
      logo.onload = resolve;
    });
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    // HEADER
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text(
      "LAPORAN BIAYA OPERASIONAL",
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
        "SKU",
        "Tanggal",
        "Nama",
        "QTY",
        "Satuan",
        "Harga",
        "Jumlah",
        "Keterangan",
      ],
    ];

    // AMBIL DATA DARI TABEL HTML
    const rows = [];
    let totalJumlah = 0;
    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 9) {
        const jumlahRaw = cells[7].innerText.trim().replace(/[^0-9]/g, "");
        const jumlahValue = parseInt(jumlahRaw, 10) || 0;
        totalJumlah += jumlahValue;

        rows.push([
          cells[0].innerText.trim(),
          cells[1].innerText.trim(),
          cells[2].innerText.trim(),
          cells[3].innerText.trim(),
          cells[4].innerText.trim(),
          cells[5].innerText.trim(),
          cells[6].innerText.trim(),
          cells[7].innerText.trim(),
          cells[8].innerText.trim(),
        ]);
      }
    });

    // FORMAT RUPIAH
    const totalFormatted =
      "Rp. " +
      totalJumlah.toLocaleString("id-ID", {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

    // BUAT TABEL PDF (DESAIN MIRIP GAMBAR)
    doc.autoTable({
      head: headers,
      body: rows,
      startY: 120,
      theme: "plain",
      margin: { bottom: 120 },
      headStyles: {
        fillColor: [0, 102, 102],
        textColor: 255,
        fontStyle: "bold",
        halign: "center",
      },
      bodyStyles: {
        fillColor: [255, 255, 255],
        textColor: [0, 0, 0],
        halign: "center",
      },
      alternateRowStyles: {
        fillColor: [230, 245, 233],
      },
      styles: {
        fontSize: 11,
        cellPadding: 7,
        lineWidth: 0.1,
        lineColor: [200, 200, 200],
      },

      // FOOTER TOTAL
      foot: [
        [
          {
            content: "TOTAL",
            colSpan: 7, // sampai kolom Keterangan
            styles: { halign: "center", fontStyle: "bold" }, // rata TENGAH di kolom Keterangan
          },
          {
            content: totalFormatted,
            styles: { fontStyle: "bold", halign: "center" }, // angka tetap di kolom Harga
          },
        ],
      ],

      footStyles: {
        fillColor: [0, 102, 102],
        textColor: 255,
        fontStyle: "bold",
      },
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
    const fileName = `Laporan Operasional - ${bulanText.replace(
      /\s+/g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  });
});
