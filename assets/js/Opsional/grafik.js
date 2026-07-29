// 1. GRAFIK STOK
let isChartDark =
  localStorage.getItem("theme") === "dark" ||
  document.body.classList.contains("dark-mode");
let bgTooltip = isChartDark
  ? "rgba(26, 26, 26, 0.95)"
  : "rgba(255, 255, 255, 0.95)";
let textTooltip = isChartDark ? "#f8fafc" : "#1e293b";

// 1. GRAFIK STOK (AESTHETIC MATCHING CARDS THEME)
Highcharts.chart("bar-chart", {
  chart: {
    type: "column",
    backgroundColor: "transparent",
    style: { fontFamily: "Montserrat, sans-serif" },
  },
  title: { text: null },
  credits: { enabled: false },
  xAxis: {
    categories: ["Masuk", "Keluar", "Migrasi", "Error", "Stok Akhir"],
    labels: {
      style: {
        fontSize: "13px",
        fontWeight: "700",
        color:
          typeof isChartDark !== "undefined" && isChartDark
            ? "#cbd5e1"
            : "#64748b",
      },
    },
    lineWidth: 0,
    tickWidth: 0,
  },
  yAxis: {
    title: { text: null },
    labels: {
      style: { color: "#94a3b8", fontWeight: "600", fontSize: "12px" },
      formatter: function () {
        return Highcharts.numberFormat(this.value, 0, ",", ".");
      },
    },
    gridLineColor:
      typeof isChartDark !== "undefined" && isChartDark
        ? "rgba(255,255,255,0.05)"
        : "rgba(0,0,0,0.04)",
    gridLineDashStyle: "LongDash",
  },
  tooltip: {
    backgroundColor:
      typeof bgTooltip !== "undefined"
        ? bgTooltip
        : "rgba(255, 255, 255, 0.95)",
    style: {
      color: typeof textTooltip !== "undefined" ? textTooltip : "#1e293b",
      fontFamily: "Montserrat, sans-serif",
    },
    borderRadius: 12,
    borderWidth: 0,
    shadow: { color: "rgba(0,0,0,0.08)", offsetX: 0, offsetY: 8, width: 16 },
    useHTML: false,
    formatter: function () {
      let listKategori = ["Masuk", "Keluar", "Migrasi", "Error", "Stok Akhir"];
      let jumlah = Highcharts.numberFormat(this.y, 0, ",", ".");
      return (
        "<b>" +
        listKategori[this.x] +
        "</b><br/>Jumlah: <b>" +
        jumlah +
        " Unit</b>"
      );
    },
  },
  plotOptions: {
    column: {
      borderRadius: 6, // Sudut membulat yang aesthetic
      colorByPoint: true,
      pointWidth: 46, // Lebar batang proporsional
      borderWidth: 0,
      dataLabels: {
        enabled: true,
        crop: false,
        overflow: "none",
        y: -15,
        formatter: function () {
          return Highcharts.numberFormat(this.y, 0, ",", ".");
        },
        style: {
          fontSize: "14px",
          fontWeight: "800",
          color:
            typeof isChartDark !== "undefined" && isChartDark
              ? "#f8fafc"
              : "#1e293b",
          textOutline: "none",
        },
      },
    },
    spline: {
      lineWidth: 3,
    },
  },
  // URUTAN WARNA DISESUAIKAN DENGAN CARD UI KAMU (Gradient)
  colors: [
    {
      linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
      stops: [
        [0, "#34d399"],
        [1, "#10b981"],
      ],
    }, // 1. Masuk (Hijau Card)
    {
      linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
      stops: [
        [0, "#fb7185"],
        [1, "#f43f5e"],
      ],
    }, // 2. Keluar (Merah Card)
    {
      linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
      stops: [
        [0, "#60a5fa"],
        [1, "#3b82f6"],
      ],
    }, // 3. Migrasi (Biru Card)
    {
      linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
      stops: [
        [0, "#a78bfa"],
        [1, "#8b5cf6"],
      ],
    }, // 4. Error (Ungu Card)
    {
      linearGradient: { x1: 0, x2: 0, y1: 0, y2: 1 },
      stops: [
        [0, "#fbbf24"],
        [1, "#f59e0b"],
      ],
    }, // 5. Stok Akhir (Kuning Card)
  ],
  series: [
    {
      name: "Total",
      type: "column",
      data: [dataMasuk, dataKeluar, dataMigrasi, dataError, dataTotalStok],
      showInLegend: false,
      shadow: {
        color: "rgba(0,0,0,0.1)", // Shadow tipis ngasih efek melayang
        width: 8,
        offsetX: 0,
        offsetY: 4,
      },
    },
    {
      name: "Tren",
      type: "spline",
      data: [dataMasuk, dataKeluar, dataMigrasi, dataError, dataTotalStok],
      color: "#cbd5e1",
      marker: {
        radius: 6,
        fillColor: "#ffffff",
        lineWidth: 2,
        // Warna titik garis tren ngikutin warna Total Stok (Kuning)
        lineColor: "#f59e0b",
      },
      showInLegend: false,
      enableMouseTracking: false,
    },
  ],
});

// 2. GRAFIK PENGELUARAN
Highcharts.chart("donut-chart", {
  chart: {
    type: "pie",
    backgroundColor: "transparent",
    style: { fontFamily: "Montserrat, sans-serif" },
    options3d: { enabled: false },
    marginLeft: 35,
    marginRight: 35,
  },
  title: { text: null },
  credits: { enabled: false },

  tooltip: {
    backgroundColor: typeof bgTooltip !== "undefined" ? bgTooltip : "#ffffff",
    style: {
      color: typeof textTooltip !== "undefined" ? textTooltip : "#333333",
      fontFamily: "Montserrat, sans-serif",
    },
    borderRadius: 12,
    borderWidth: 0,
    shadow: true,
    useHTML: false,
    formatter: function () {
      let rupiah = Highcharts.numberFormat(this.y, 0, ",", ".");
      return (
        '<span style="color:' +
        this.point.color +
        '">\u25CF</span> ' +
        "<b>" +
        this.point.name +
        "</b><br/>" +
        "Total: <b>Rp " +
        rupiah +
        "</b>"
      );
    },
  },

  plotOptions: {
    pie: {
      innerSize: "65%",
      size: "75%",
      borderColor: isChartDark ? "#1e293b" : "#ffffff",
      borderWidth: 2,
      slicedOffset: 15,
      animation: { duration: 1500 },
      shadow: {
        color: "rgba(0,0,0,0.08)",
        width: 10,
        offsetX: 2,
        offsetY: 8,
      },
      dataLabels: {
        enabled: true,
        distance: 10,
        useHTML: false,
        crop: false,
        overflow: "justify",
        format:
          '<span style="color:#64748b; font-size:12px">{point.name}</span><br><span style="color:{point.color}; font-size:14px; font-weight:bold">{point.percentage:.0f}%</span>',
        style: {
          textOutline: "none",
          fontFamily: "Montserrat, sans-serif",
          fontWeight: "normal",
        },
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
      colorByPoint: true,
      data: [
        { name: "Operasional", y: Number(expOps) || 0 },
        { name: "Service", y: Number(expSvc) || 0 },
        { name: "Cicilan", y: Number(expCic) || 0 },
        { name: "Utilitas", y: Number(expUtl) || 0 },
      ],
    },
  ],
});
