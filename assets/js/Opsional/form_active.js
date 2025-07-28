const editButtons = document.querySelectorAll(".edit-btn");

editButtons.forEach((button) => {
  button.addEventListener("click", function () {
    const form = this.closest(".row-form");
    const inputs = form.querySelectorAll("input");

    inputs.forEach((input) => {
      if (input.name !== "id_barang") {
        input.removeAttribute("readonly");
        input.style.background = "#fff"; // opsional: kasih warna putih pas editable
        input.style.border = "1px solid #ccc"; // opsional: kasih border
      }
    });
  });
});
