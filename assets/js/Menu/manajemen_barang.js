const ctx = document.getElementById("barangMasukChart").getContext("2d");
new Chart(ctx, {
  type: "bar",
  data: {
    labels: ["Barang Masuk"],
    datasets: [
      {
        label: "Jumlah",
        data: [36],
        backgroundColor: "#3b82f6",
        borderRadius: 10,
        barThickness: 40,
      },
    ],
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { enabled: true },
    },
    scales: {
      x: { display: false },
      y: { display: false },
    },
  },
});
