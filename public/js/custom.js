document.addEventListener("DOMContentLoaded", function () {
  console.log("Sistem Penilaian Kinerja siap digunakan.");

  const searchInput = document.getElementById("searchEmployee");
  const employeeTableBody = document.getElementById("employeeTableBody");

  if (searchInput && employeeTableBody) {
    searchInput.addEventListener("input", function () {
      const keyword = searchInput.value.toLowerCase().trim();
      const rows = employeeTableBody.querySelectorAll("tr");

      rows.forEach(function (row) {
        // Lewati baris pesan ketika data kosong
        if (row.cells.length === 1) {
          return;
        }

        const rowText = row.textContent.toLowerCase();

        row.style.display = rowText.includes(keyword) ? "" : "none";
      });
    });
  }
});
