document.addEventListener("DOMContentLoaded", function () {
  const pdfBtn = document.querySelector("#pemakaian");

  pdfBtn.addEventListener("click", async function () {
    const bulanInput = document.getElementById("bulan");
    const mingguInput = document.getElementById("minggu");

    const bulanValue = bulanInput?.value;
    const mingguValue = mingguInput?.value;

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
      "LAPORAN PEMAKAIAN BARANG (BARANG KELUAR)",
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
    const bulanText = namaBulan[parseInt(bulan) - 1];

    // Mapping minggu ke tanggal
    const mingguMap = {
      1: "1–7",
      2: "8–14",
      3: "15–21",
      4: "22–akhir",
    };

    let periodeText;
    if (mingguValue) {
      const tanggalRange = mingguMap[mingguValue] || "";
      periodeText = `Periode: Minggu ${mingguValue} (${tanggalRange}) ${bulanText} ${tahun}`;
    } else {
      periodeText = `Periode: ${bulanText} ${tahun}`;
    }

    doc.text(periodeText, 40, 100);

    // HEADER TABEL
    const headers = [["No", "ID Barang", "Nama Barang", "Jumlah", "Satuan"]];

    // AMBIL DATA DARI TABEL HTML DAN GABUNG YANG SAMA
    const groupedData = {}; // key = ID Barang, value = { nama, jumlah, satuan }
    let totalJumlah = 0;

    dataRows.forEach((tr) => {
      const cells = tr.querySelectorAll("td");
      if (cells.length >= 6) {
        const idBarang = cells[1].innerText.trim();
        const namaBarang = cells[2].innerText.trim();
        const jumlahRaw = cells[4].innerText.replace(/[^0-9]/g, "");
        const jumlahValue = parseInt(jumlahRaw, 10) || 0;
        const satuan = cells[5].innerText.trim();

        totalJumlah += jumlahValue;

        if (groupedData[idBarang]) {
          groupedData[idBarang].jumlah += jumlahValue;
        } else {
          groupedData[idBarang] = {
            nama: namaBarang,
            jumlah: jumlahValue,
            satuan: satuan,
          };
        }
      }
    });

    // Konversi ke array untuk autoTable
    const rows = [];
    let no = 1;
    for (const id in groupedData) {
      rows.push([
        no++, // No
        id, // ID Barang
        groupedData[id].nama, // Nama Barang
        groupedData[id].jumlah.toLocaleString("id-ID"), // Jumlah
        groupedData[id].satuan, // Satuan
      ]);
    }

    // BUAT TABEL PDF
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
      bodyStyles: {
        fillColor: [255, 255, 255],
        textColor: [0, 0, 0],
        halign: "center",
      },
      alternateRowStyles: { fillColor: [230, 245, 233] },
      styles: {
        fontSize: 11,
        cellPadding: 7,
        lineWidth: 0.1,
        lineColor: [200, 200, 200],
      },
      foot: [
        [
          {
            content: "TOTAL",
            colSpan: 3,
            styles: { halign: "center", fontStyle: "bold" },
          },
          {
            content: totalJumlah.toLocaleString("id-ID"),
            colSpan: 1,
            styles: { fontStyle: "bold", halign: "center" },
          },
          {
            content: "", // kolom satuan kosong
            colSpan: 1,
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
    const fileName = `Laporan Pemakaian - ${
      mingguValue ? "Minggu_" + mingguValue + " - " : ""
    }${bulanText.replace(/\s+/g, "_")}.pdf`;
    doc.save(fileName);
  });
});
