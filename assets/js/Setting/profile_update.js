const params = new URLSearchParams(window.location.search);
const status = params.get("updat_profile");

if (status === "success") {
  Swal.fire({
    icon: "success",
    title: "Berhasil!",
    text: "Data profil berhasil diperbarui",
    confirmButtonColor: "#6faa3f",
  });
}
