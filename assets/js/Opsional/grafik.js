// 1. GRAFIK STOK
let isChartDark =
  localStorage.getItem("theme") === "dark" ||
  document.body.classList.contains("dark-mode");
let bgTooltip = isChartDark
  ? "rgba(26, 26, 26, 0.95)"
  : "rgba(255, 255, 255, 0.95)";
let textTooltip = isChartDark ? "#f8fafc" : "#1e293b";

Highcharts.chart("bar-chart", {
  chart: {
    type: "cylinder",
    options3d: {
      enabled: true,
      alpha: 15,
      beta: 0,
      depth: 50,
      viewDistance: 25,
    },
    fontFamily: "Montserrat, sans-serif",
    backgroundColor: "transparent",
  },
  title: { text: null },
  credits: { enabled: false },
  xAxis: {
    categories: ["Masuk", "Keluar", "Migrasi", "Error", "Stok Akhir"],
    labels: {
      style: { fontSize: "12px", fontWeight: "700", color: "#64748b" },
    },
    gridLineWidth: 0,
    lineColor: "#e2e8f0",
  },
  yAxis: {
    title: { text: null },
    labels: {
      style: { color: "#64748b", fontWeight: "600" },
      formatter: function () {
        return this.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      },
    },
    gridLineColor: "#e2e8f0",
    gridLineDashStyle: "Dash",
  },
  tooltip: {
    backgroundColor: bgTooltip,
    style: { color: textTooltip }, // Kasih warna dasar teks sesuai tema
    borderRadius: 8,
    borderWidth: 0,
    shadow: true,
    useHTML: true,
    formatter: function () {
      let listKategori = ["Masuk", "Keluar", "Migrasi", "Error", "Stok Akhir"];
      let namaAktivitas = listKategori[this.x];

      return (
        // Warnanya udah di-handle sama 'style' di atas, jadi di dalem div pake inherit aman
        '<div style="font-family: Montserrat; padding: 5px; color: inherit;">' +
        "<b>" +
        namaAktivitas +
        "</b><br/>" +
        'Jumlah: <b style="color: inherit;">' +
        this.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") +
        " Unit</b>" +
        "</div>"
      );
    },
  },
  plotOptions: {
    cylinder: {
      depth: 50,
      colorByPoint: true,
      pointWidth: 45,
      animation: {
        duration: 1800,
      },
      dataLabels: {
        enabled: true,
        crop: false,
        overflow: "none",
        y: -20,
        formatter: function () {
          return this.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },
        style: {
          fontSize: "13px",
          fontWeight: "800",
          color: "#1e293b",
          textOutline: "none",
        },
      },
    },
    spline: {
      animation: { duration: 2200 },
    },
  },
  colors: ["#689f38", "#ffb300", "#039be5", "#e53935", "#1e293b"],
  series: [
    {
      name: "Total",
      type: "cylinder",
      data: [dataMasuk, dataKeluar, dataMigrasi, dataError, dataTotalStok],
      showInLegend: false,
    },
    {
      name: "Tren",
      type: "spline",
      data: [dataMasuk, dataKeluar, dataMigrasi, dataError, dataTotalStok],
      color: "#cbd5e1",
      lineWidth: 2,
      marker: {
        radius: 5,
        fillColor: "#ffffff",
        lineWidth: 2,
        lineColor: "#689f38",
      },
      showInLegend: false,
      enableMouseTracking: false,
    },
  ],
});

// 2. GRAFIK PENGELUARAN (PREMIUM CORPORATE - LURUS)
Highcharts.chart("donut-chart", {
  chart: {
    type: "pie",
    backgroundColor: "transparent",
    style: { fontFamily: "Montserrat, sans-serif" },
    options3d: { enabled: false },
  },
  title: { text: null },
  credits: { enabled: false },
  tooltip: {
    backgroundColor: bgTooltip, // Panggil variabel background dinamis
    style: { color: textTooltip }, // Panggil variabel warna teks dinamis
    borderRadius: 12,
    borderWidth: 0,
    shadow: true,
    useHTML: true,
    formatter: function () {
      return (
        // Pakai color: inherit biar teksnya otomatis ngikutin tema
        '<div style="padding: 6px; font-family: Montserrat; color: inherit;">' +
        '<span style="color:' +
        this.point.color + // Warna titik kategori tetap pakai warna bawaan chart
        '">\u25CF</span> ' +
        "<b>" +
        this.point.name +
        "</b><br/>" +
        // Hapus hardcode #1e293b, ganti jadi inherit
        'Total: <b style="color: inherit;">Rp ' +
        this.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") +
        "</b>" +
        "</div>"
      );
    },
  },
  plotOptions: {
    pie: {
      innerSize: "65%",
      borderColor: "#ffffff",
      borderWidth: 2,
      slicedOffset: 15,
      animation: { duration: 1500 },
      shadow: {
        color: "rgba(0,0,0,0.12)",
        width: 10,
        offsetX: 2,
        offsetY: 8,
      },
      dataLabels: {
        enabled: true,
        distance: 25,
        useHTML: true,
        format:
          '<div style="text-align: center; font-family: Montserrat;">' +
          '<b style="color:#475569; font-size:12px;">{point.name}</b><br/>' +
          '<b style="color:{point.color}; font-size:14px; font-weight:800;">{point.percentage:.0f}%</b>' +
          "</div>",
        style: { textOutline: "none" },
        connectorColor: "#cbd5e1",
        connectorWidth: 1.5,
        connectorPadding: 5,
      },
      colors: [
        "#0d9488", // Teal
        "#d97706", // Gold/Amber
        "#ea580c", // Orange
        "#7c2d12", // Deep Brown
      ],
    },
  },
  series: [
    {
      name: "Expenses",
      data: [
        ["Operasional", expOps],
        ["Service", expSvc],
        ["Cicilan", expCic],
        ["Utilitas", expUtl],
      ],
    },
  ],
});
