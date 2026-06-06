// ==========================================
// GLOBAL DARK MODE SYSTEM (Berlaku di semua halaman)
// ==========================================

// 1. Saat halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
  const isDark = localStorage.getItem("theme") === "dark";

  // Terapin mode gelap kalau sebelumnya aktif
  if (isDark) {
    document.body.classList.add("dark-mode");
  }

  // Samakan posisi semua switch & label di halaman
  const allSwitches = document.querySelectorAll(".theme-toggle-checkbox");
  allSwitches.forEach((btn) => (btn.checked = isDark));

  const allLabels = document.querySelectorAll(".theme-label-text");
  allLabels.forEach(
    (label) => (label.textContent = isDark ? "Aktif" : "Tidak aktif"),
  );

  // Update grafik kalau fungsinya ada di halaman ini (contoh: Dashboard)
  if (typeof updateGrafikTheme === "function") {
    setTimeout(() => updateGrafikTheme(isDark), 100);
  }
});

// 2. Ngawasin setiap klik tombol switch di seluruh halaman
document.addEventListener("change", function (e) {
  if (e.target && e.target.classList.contains("theme-toggle-checkbox")) {
    const isDarkNow = e.target.checked;

    if (isDarkNow) {
      document.body.classList.add("dark-mode");
      localStorage.setItem("theme", "dark");
    } else {
      document.body.classList.remove("dark-mode");
      localStorage.setItem("theme", "light");
    }

    // Sinkronisasi otomatis ke semua switch lain di halaman
    const allSwitches = document.querySelectorAll(".theme-toggle-checkbox");
    allSwitches.forEach((btn) => {
      if (btn !== e.target) {
        btn.checked = isDarkNow;
      }
    });

    const allLabels = document.querySelectorAll(".theme-label-text");
    allLabels.forEach(
      (label) => (label.textContent = isDarkNow ? "Aktif" : "Tidak aktif"),
    );

    // Update grafik kalau fungsinya ada
    if (typeof updateGrafikTheme === "function") {
      updateGrafikTheme(isDarkNow);
    }
  }
});
