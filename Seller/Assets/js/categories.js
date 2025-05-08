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
    fetchcategoriesData();
  }
}

function searchcategories() {
  currentPage = 1;
  fetchcategoriesData();
}

function fetchcategoriesData() {
  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/Category/load.php?page=${currentPage}&search=${encodeURIComponent(
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
        updateTable(data.categories);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching categories data:", err));
}

function updateTable(categories) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (categories.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
               <td colspan="3" style="text-align: center; padding: 10px;">No records found</td>
           `;
    tableBody.appendChild(row);
  } else {
    categories.forEach((category) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                   <td hidden>${category.id}</td>
                   <td>${category.Description}</td>
                   <td>${category.Status}</td>
                   <td>
              ${
                category.Status === "Inactive"
                  ? `<button class="btn-form-restore" onclick="RestoreCategory(${category.ID})">Restore</button>`
                  : `<button class="btn-form-edit" onclick="editCategory(${category.ID})">Edit</button>
                   <button class="btn-form-delete" onclick="RemoveCategory(${category.ID})">Remove</button>`
              }
          </td>
               `;
      tableBody.appendChild(row);
    });

    console.log(categories);
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


fetchcategoriesData();

function searchcategory() {
  currentPage = 1;
  fetchcategoriesData();
}

/* ###################################################
        CRUD CRUD CRUD CRUD CRUD CRUD CRUD CRUD 
   ###################################################*/

document
  .querySelector("#addCategoryForm")
  .addEventListener("submit", AddCategory);

function AddCategory(event) {
  event.preventDefault(); // Prevent the default form submission

  const form = document.querySelector("#addCategoryForm");
  const formData = new FormData(form);
  
  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  const url = "Actions/Category/create.php";

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
          icon: "success",
          title: data.message,
          showConfirmButton: false,
          timer: 1000,
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.message || "Failed to save category.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message || "Something went wrong!",
      });
    });
}

function editCategory(CategoryID) {
  console.log("Edit Category", CategoryID);
  document.getElementById("editModal").style.display = "block";

  fetch("Actions/Category/getCategory.php", {
    method: "POST",
    body: new URLSearchParams({ ID: CategoryID }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("editID").value = data.category.ID;
        document.getElementById("editCategoryName").value = data.category.Description; // Corrected here
      } else {
        alert("Error loading category data.");
      }
    })
    .catch((error) => console.error("Error:", error));
}

document
  .getElementById("editCategoryForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.querySelector("#editCategoryForm");
    const formData = new FormData(form);

    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);
  
    const url = "Actions/Category/edit.php";

    fetch(url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: data.message,
            showConfirmButton: false,
            timer: 1500,
          }).then(() => {
            window.location.reload();
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.message,
          });
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Something went wrong!",
        });
      });
  });

  
// Function to Remove Employee
function RestoreCategory(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will restore the category.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, restore it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Category/restore.php", {
        method: "POST",
        body: new URLSearchParams({ ID: ID }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: data.message,
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              fetchcategoriesData(); 
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Failed to restore employee.",
            });
          }
        })
        .catch((error) => {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
          });
        });
    }
  });
}

// Function to Restore Category
function RemoveCategory(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will remove the category from the system.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Category/remove.php", {
        method: "POST",
        body: new URLSearchParams({ ID: ID }),
      })
        .then((response) => response.json())
        .then((data) => {
          console.log("Server response:", data);
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: data.message,
              showConfirmButton: false,
              timer: 1000,
            }).then(() => {
              // Reload the Category data or update the table after removal
              fetchcategoriesData();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Failed to remove category.",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: error.message || "Something went wrong!",
          });
        });
    }
  });
}