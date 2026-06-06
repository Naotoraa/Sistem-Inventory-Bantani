document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".card-header h3");
  const speed = 25;

  counters.forEach((counter) => {
    // PERBAIKAN: Hapus semua karakter SELAIN angka (biar koma/titik dari PHP hilang)
    const targetStr = counter.innerText.replace(/[^0-9]/g, "");
    const target = parseInt(targetStr, 10);

    // Kalau kebetulan targetnya kosong atau bukan angka, lewati aja biar nggak error
    if (isNaN(target)) return;

    counter.innerText = "0";

    const updateCount = () => {
      // PERBAIKAN JUGA: Pastikan pembacaan angka saat ini juga kebal tanda baca
      const current =
        parseInt(counter.innerText.replace(/[^0-9]/g, ""), 10) || 0;

      const inc = target / speed;

      if (current < target) {
        // Tulis balik angkanya dan kasih titik ribuan ala Indonesia
        counter.innerText = Math.ceil(current + inc)
          .toString()
          .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        setTimeout(updateCount, 15);
      } else {
        counter.innerText = target
          .toString()
          .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      }
    };

    setTimeout(updateCount, 400);
  });
});
