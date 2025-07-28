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

  idSelectEl.addEventListener("change", function () {
    const selected = idSelectEl.options[idSelectEl.selectedIndex];
    const name = selected.getAttribute("data-name");
    const category = selected.getAttribute("data-category");
    const satuan = selected.getAttribute("data-satuan");

    if (name) nameSelect.setValue(name, true);
    categoryInput.value = category || "";
    if (satuan) satuanInput.value = capitalize(satuan);
  });

  nameSelectEl.addEventListener("change", function () {
    const selected = nameSelectEl.options[nameSelectEl.selectedIndex];
    const id = selected.getAttribute("data-id");
    const category = selected.getAttribute("data-category");
    const satuan = selected.getAttribute("data-satuan");

    if (id) idSelect.setValue(id, true);
    categoryInput.value = category || "";
    if (satuan) satuanInput.value = capitalize(satuan);
  });

  const currentId = idSelectEl.getAttribute("data-current-id");
  const currentName = nameSelectEl.getAttribute("data-current-name");
  const currentSatuan = satuanInput.getAttribute("value");

  if (currentId) idSelect.setValue(currentId, true);
  if (currentName) nameSelect.setValue(currentName, true);
  if (currentSatuan) satuanInput.value = capitalize(currentSatuan);

  if (currentId) idSelectEl.dispatchEvent(new Event("change"));
});
