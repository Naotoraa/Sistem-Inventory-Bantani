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

    const dataRows = Array.from(document.querySelectorAll("#tabel-data tr"));
    const hasValidData = dataRows.some((row) => {
      const cells = row.querySelectorAll("td");
      return (
        cells.length > 1 &&
        [...cells].some((td, i) => i > 0 && td.innerText.trim() !== "")
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
    await new Promise((resolve) => (logo.onload = resolve));
    doc.addImage(logo, "PNG", 40, 25, 60, 60);

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text(
      "LAPORAN CICILAN ASET / KENDARAAN",
      doc.internal.pageSize.getWidth() / 2,
      40,
      {
        align: "center",
      }
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

    // Ambil isi tabel
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
    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 8) {
        rows.push([
          cells[0].innerText.trim(),
          cells[1].innerText.trim(),
          cells[2].innerText.trim(),
          cells[3].innerText.trim(),
          cells[4].innerText.trim(),
          cells[5].innerText.trim(),
          cells[6].innerText.trim(),
          cells[7].innerText.trim(),
        ]);
      }
    });

    doc.autoTable({
      head: headers,
      body: rows,
      startY: 120,
      theme: "striped",
      headStyles: {
        fillColor: [104, 159, 56],
        textColor: 255,
        fontStyle: "bold",
        halign: "center",
      },
      alternateRowStyles: {
        fillColor: [245, 245, 245],
      },
      styles: {
        fontSize: 10,
        cellPadding: 6,
        halign: "center",
      },
      margin: { bottom: 120 },
    });

    // Footer
    const pageHeight = doc.internal.pageSize.getHeight();
    const pageWidth = doc.internal.pageSize.getWidth();
    doc.setFontSize(10);
    doc.text(
      `Exported: ${new Date().toLocaleDateString("id-ID")}`,
      40,
      pageHeight - 40
    );

    doc.text("Disetujui oleh,", pageWidth - 170, pageHeight - 100);
    doc.text("________________________", pageWidth - 210, pageHeight - 55);
    doc.setFont("helvetica", "bold");
    doc.text("Dede Irfan", pageWidth - 170, pageHeight - 40);
    doc.setFont("helvetica", "normal");
    doc.text("Manager Keuangan", pageWidth - 185, pageHeight - 27);

    const fileName = `Laporan_Cicilan_${bulanText.replace(/\s/g, "_")}.pdf`;
    doc.save(fileName);
  });
});
