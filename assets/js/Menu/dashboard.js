document.addEventListener("DOMContentLoaded", function () {
  const dataPie = {
    labels: ["Barang Masuk", "Barang Keluar", "Barang Migrasi", "Barang Error"],
    datasets: [
      {
        label: "Distribusi Barang",
        data: [290, 145, 500, 120],
        backgroundColor: ["#4CAF50", "#F44336", "#2196F3", "#9C27B0"],
        hoverOffset: 6,
      },
    ],
  };

  const configPie = {
    type: "pie",
    data: dataPie,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
    },
  };

  new Chart(document.getElementById("pieChart"), configPie);

  // BAR CHART
  const dataBar = {
    labels: ["Barang A", "Barang B", "Barang C", "Barang D"],
    datasets: [
      {
        label: "Stok Akhir",
        data: [500, 200, 350, 120],
        backgroundColor: "#FF9800",
      },
    ],
  };

  const configBar = {
    type: "bar",
    data: dataBar,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  };

  new Chart(document.getElementById("barChart"), configBar);

  // LINE CHART
  const dataLine = {
    labels: ["Jan", "Feb", "Mar", "Apr", "Mei"],
    datasets: [
      {
        label: "Stok Akhir per Bulan",
        data: [400, 420, 390, 450, 470],
        fill: false,
        borderColor: "#03A9F4",
        tension: 0.3,
      },
    ],
  };

  const configLine = {
    type: "line",
    data: dataLine,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "bottom",
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
    },
  };

  new Chart(document.getElementById("lineChart"), configLine);
});
