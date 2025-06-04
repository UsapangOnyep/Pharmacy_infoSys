/* ###################################################
        MODAL MODAL MODAL MODAL MODAL MODAL MODAL
   ###################################################*/

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

function showModal() {
  document.getElementById("addModal").style.display = "block";
}

function closeModal() {
  document.getElementById("addModal").style.display = "none";
}

/* ###################################################
          PAGINATION PAGINATION PAGINATION PAGINATION
     ###################################################*/
let currentPage = 1;
let totalPages = 1;
let totalRows = 0;
let recordsPerPage = 15;

function loadPage(page) {
  if (page >= 1 && page <= totalPages) {
    currentPage = page;
    fetchSuppliersData();
  }
}

function searchsuppliers() {
  currentPage = 1;
  fetchSuppliersData();
}

function fetchSuppliersData() {
  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/Suppliers/load.php?page=${currentPage}&search=${encodeURIComponent(
    searchQuery
  )}`;

  fetch(url)
    .then((response) => response.text())
    .then((text) => {
      try {
        const data = JSON.parse(text);
        totalPages = data.totalPages;
        currentPage = data.currentPage;
        totalRows = data.totalRows;
        updateTable(data.suppliers);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching suppliers data:", err));
}

function updateTable(suppliers) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (suppliers.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
                 <td colspan="3" style="text-align: center; padding: 10px;">No records found</td>
             `;
    tableBody.appendChild(row);
  } else {
    suppliers.forEach((supplier) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                     <td hidden>${supplier.id}</td>
                     <td>${supplier.Name}</td>
                     <td>${supplier.Address}</td>
                     <td>${supplier.TIN}</td>
                     <td>${supplier.Status}</td>
                     <td>
                ${
                  supplier.Status === "Inactive"
                    ? `<button class="btn-form-restore" onclick="RestoreSupplier(${supplier.ID})">Restore</button>`
                    : `<button class="btn-form-edit" onclick="editsupplier(${supplier.ID})">Edit</button>
                     <button class="btn-form-delete" onclick="RemoveSupplier(${supplier.ID})">Remove</button>`
                }
            </td>
                 `;
      tableBody.appendChild(row);
    });

    console.log(suppliers);
  }
}

function updatePagination() {
  const paginationControls = document.getElementById("paginationControls");

  if (totalRows === 0) {
    paginationControls.innerHTML = `
           <div>
             Showing 0 out of 0 | Page 0
           </div>
         `;
  } else {
    paginationControls.innerHTML = `
           <div>
             Showing ${(currentPage - 1) * recordsPerPage + 1} - ${Math.min(
      currentPage * recordsPerPage,
      totalRows
    )} out of ${totalRows} | Page ${currentPage}
           </div>
           <div>
             <button onclick="loadPage(1)" ${
               currentPage === 1 ? "disabled" : ""
             }>First</button>
             <button onclick="loadPage(${currentPage - 1})" ${
      currentPage === 1 ? "disabled" : ""
    }>Prev</button>
             <button onclick="loadPage(${currentPage + 1})" ${
      currentPage === totalPages ? "disabled" : ""
    }>Next</button>
             <button onclick="loadPage(${totalPages})" ${
      currentPage === totalPages ? "disabled" : ""
    }>Last</button>
           </div>
         `;
  }
}

fetchSuppliersData();

function searchsupplier() {
  currentPage = 1;
  fetchSuppliersData();
}

/* ###################################################
          CRUD CRUD CRUD CRUD CRUD CRUD CRUD CRUD 
     ###################################################*/

document
  .querySelector("#addSupplierForm")
  .addEventListener("submit", Addsupplier);

function Addsupplier(event) {
  event.preventDefault(); // Prevent the default form submission

  const form = document.querySelector("#addSupplierForm");
  const formData = new FormData(form);

  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  const url = "Actions/Suppliers/create.php";

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("HTTP error: " + response.status);
      }

      return response.text().then((text) => {
        try {
          return JSON.parse(text);
        } catch (e) {
          throw new Error("Invalid JSON response: " + text);
        }
      });
    })
    .then((data) => {
      console.log("Server response:", data);
      if (data.status === "success") {
        Swal.fire({
          position: "top",
          toast: true,
          icon: "success",
          title: data.message,
          showConfirmButton: false,
          timer: 1000,
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.message || "Failed to save supplier.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        position: "top",
        toast: true,
        icon: "error",
        title: "Error",
        text: error.message || "Something went wrong!",
      });
    });
}

function editsupplier(supplierID) {
  console.log("Edit supplier", supplierID);
  document.getElementById("editModal").style.display = "block";

  fetch("Actions/Suppliers/getSupplier.php", {
    method: "POST",
    body: new URLSearchParams({ ID: supplierID }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("editID").value = data.supplier.ID;
        document.getElementById("editsupplierName").value = data.supplier.Name; // Corrected here
        document.getElementById("editsupplierAddress").value =
          data.supplier.Address; // Corrected here
        document.getElementById("editsupplierTIN").value = data.supplier.TIN; // Corrected here
      } else {
        alert("Error loading supplier data.");
      }
      console.log(data);
    })
    .catch((error) => console.error("Error:", error));
}

document
  .getElementById("editSupplierForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.querySelector("#editSupplierForm");
    const formData = new FormData(form);

    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);

    const url = "Actions/Suppliers/edit.php";

    fetch(url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            position: "top",
            toast: true,
            icon: "success",
            title: data.message,
            showConfirmButton: false,
            timer: 1500,
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            position: "top",
            toast: true,
            showConfirmButton: false,
            timer: 1500,
            icon: "error",
            title: "Error",
            text: data.message,
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          position: "top",
          toast: true,
          showConfirmButton: false,
          timer: 1500,
          icon: "error",
          title: "Error",
          text: "Something went wrong!",
        });
      });
  });

// Function to Remove Employee
function RestoreSupplier(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will restore the supplier.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, restore it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Suppliers/restore.php", {
        method: "POST",
        body: new URLSearchParams({ ID: ID }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              position: "top",
              toast: true,
              icon: "success",
              title: data.message,
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              fetchSuppliersData();
            });
          } else {
            Swal.fire({
              position: "top",
              toast: true,
              showConfirmButton: false,
              timer: 1500,
              icon: "error",
              title: "Error",
              text: data.message || "Failed to restore supplier.",
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            showConfirmButton: false,
            timer: 1500,
            position: "top",
            toast: true,
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
          });
        });
    }
  });
}

// Function to Restore supplier
function RemoveSupplier(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will remove the supplier from the system.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Suppliers/remove.php", {
        method: "POST",
        body: new URLSearchParams({ ID: ID }),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Server response:", data);
          if (data.status === "success") {
            Swal.fire({
              position: "top",
              toast: true,
              icon: "success",
              title: data.message,
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              // Reload the supplier data or update the table after removal
              fetchSuppliersData();
            });
          } else {
            Swal.fire({
              position: "top",
              toast: true,
              showConfirmButton: false,
              timer: 1500,
              icon: "error",
              title: "Error",
              text: data.message || "Failed to remove supplier.",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            position: "top",
            toast: true,
            showConfirmButton: false,
            timer: 1500,
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
          });
        });
    }
  });
}
