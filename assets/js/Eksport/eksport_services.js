document.addEventListener("DOMContentLoaded", function () {
  document.querySelector(".pdf").addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const bulanValue = bulanInput?.value;

    if (!bulanValue) {
      Swal.fire({
        icon: "warning",
        title: "Periode belum dipilih",
        text: "Silakan pilih bulan terlebih dahulu sebelum export.",
      });
      bulanInput.focus();
      return;
    }

    const dataRows = Array.from(document.querySelectorAll("#Table tbody tr"));
    const hasValidData = dataRows.some((row) => {
      const cells = row.querySelectorAll("td");
      return (
        cells.length > 1 &&
        Array.from(cells).some((td, i) => i > 0 && td.textContent.trim() !== "")
      );
    });

    if (!hasValidData) {
      Swal.fire({
        icon: "info",
        title: "Tidak ada data",
        text: "Data untuk periode ini kosong, tidak bisa diexport.",
      });
      return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("l", "pt", "a4");

    const logo = new Image();
    logo.src = "../../assets/img/Bantani 1.png";
    await new Promise((resolve) => {
      logo.onload = resolve;
    });
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

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
      {
        align: "center",
      }
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

    const headers = [
      ["No", "ID Service", "Nama Barang", "Keterangan", "Tanggal", "Biaya"],
    ];
    const rows = [];

    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 6) {
        rows.push([
          cells[0].textContent.trim(),
          cells[1].textContent.trim(),
          cells[2].textContent.trim(),
          cells[3].textContent.trim(),
          cells[4].textContent.trim(),
          cells[5].textContent.trim(),
        ]);
      }
    });

    doc.autoTable({
      head: headers,
      body: rows,
      startY: 120,
      theme: "striped",
      margin: { bottom: 100 },
      headStyles: {
        fillColor: [104, 159, 56], // ✅ Warna hijau seperti operasional
        textColor: 255,
        fontStyle: "bold",
        halign: "center",
      },
      styles: {
        fontSize: 10,
        cellPadding: 6,
        halign: "center",
      },
      alternateRowStyles: {
        fillColor: [240, 240, 240],
      },
    });

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

    const fileName = `Laporan_Biaya_Service_${bulanText.replace(
      /\s+/g,
      "_"
    )}.pdf`;
    doc.save(fileName);
  });
});
