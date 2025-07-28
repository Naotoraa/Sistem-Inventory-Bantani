const fileInput = document.getElementById("foto");
const labelText = document.getElementById("fileLabelText");

fileInput.addEventListener("change", function () {
  if (fileInput.files.length > 0) {
    labelText.textContent = fileInput.files[0].name;
  } else {
    labelText.textContent = "Pilih Foto";
  }
});
