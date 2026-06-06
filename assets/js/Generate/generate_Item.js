document.addEventListener("DOMContentLoaded", function () {
  const idSelectEl = document.getElementById("id_barang");
  const nameSelectEl = document.getElementById("name");
  const categoryInput = document.getElementById("category");
  const satuanInput = document.getElementById("satuan");

  if (!idSelectEl || !nameSelectEl || !categoryInput || !satuanInput) return;

  const idSelect = idSelectEl.tomselect;
  const nameSelect = nameSelectEl.tomselect;

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  // === LISTENER UNTUK ID BARANG ===
  idSelectEl.addEventListener("change", function () {
    const selected = idSelectEl.options[idSelectEl.selectedIndex];
    // PERBAIKAN UTAMA: Ganti data-name jadi data-nama (sesuai HTML)
    const name = selected.getAttribute("data-nama");
    const category = selected.getAttribute("data-category");
    const satuan = selected.getAttribute("data-satuan");

    if (name) nameSelect.setValue(name, true);
    categoryInput.value = category || "";
    if (satuan) satuanInput.value = capitalize(satuan);
  });

  // === LISTENER UNTUK NAMA BARANG ===
  nameSelectEl.addEventListener("change", function () {
    const selected = nameSelectEl.options[nameSelectEl.selectedIndex];
    const id = selected.getAttribute("data-id");
    const category = selected.getAttribute("data-category");
    const satuan = selected.getAttribute("data-satuan");

    if (id) idSelect.setValue(id, true);
    categoryInput.value = category || "";
    if (satuan) satuanInput.value = capitalize(satuan);
  });

  // === AUTO-FILL SAAT HALAMAN UPDATE DIBUKA ===
  const currentId = idSelectEl.getAttribute("data-current-id");
  const currentName = nameSelectEl.getAttribute("data-current-name");
  const currentSatuan = satuanInput.getAttribute("value");

  // Tom Select butuh perlakuan khusus pakai .setValue()
  if (currentId) idSelect.setValue(currentId, true);
  if (currentName) nameSelect.setValue(currentName, true);
  if (currentSatuan) satuanInput.value = capitalize(currentSatuan);

  // Pancing event 'change' biar Kategori & Satuan juga otomatis keisi
  if (currentId) idSelectEl.dispatchEvent(new Event("change"));
});
