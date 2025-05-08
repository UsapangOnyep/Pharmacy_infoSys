function AddEmployee(event) {
  event.preventDefault(); // Prevent the default form submission

  const form = document.querySelector("#addEmployeeForm");
  const formData = new FormData(form);
  
  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  const url = "Actions/Employee/create.php";

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
        throw new Error(data.message || "Failed to save employee.");
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

// Function to Remove Employee
function RestoreEmployee(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will restore the employee.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, restore it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Employee/restore.php", {
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
              fetchEmployeeData(); // Reload employee data
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

// Function to Restore Employee
function RemoveEmployee(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will remove the employee from the system.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Employee/remove.php", {
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
              // Reload the employee data or update the table after removal
              fetchEmployeeData();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Failed to remove employee.",
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

function editEmployee(employeeID) {
  document.getElementById("editModal").style.display = "block";

  fetch("Actions/Employee/getEmployee.php", {
    method: "POST",
    body: new URLSearchParams({ ID: employeeID }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("editEmployeeID").value = data.employee.ID;
        document.getElementById("editEmployeeFName").value =
          data.employee.Fname;
        document.getElementById("editEmployeeMName").value =
          data.employee.MName;
        document.getElementById("editEmployeeLName").value =
          data.employee.Lname;
        document.getElementById("editEmployeeSuffix").value =
          data.employee.Suffix;
      } else {
        alert("Error loading employee data.");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

document
  .getElementById("editEmployeeForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.querySelector("#editEmployeeForm");
    const formData = new FormData(form);
    
    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);
  
    const url = "Actions/Employee/edit.php";

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

document
  .querySelector("#addEmployeeForm")
  .addEventListener("submit", AddEmployee);

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
  if (page < 1 || page > totalPages) return;
  currentPage = page;
  fetchEmployeeData();
}

function searchEmployee() {
  currentPage = 1;
  fetchEmployeeData();
}

function fetchEmployeeData() {
    console.log(totalRows);
  
    const searchQuery = document.getElementById("searchInput").value;
    const url = `Actions/Employee/load.php?page=${currentPage}&search=${encodeURIComponent(
      searchQuery
    )}`;
  
    fetch(url)
      .then((response) => {
        console.log("Raw response:", response);
        return response.text(); 
      })
      .then((text) => {
        try {
          const data = JSON.parse(text);
          totalPages = data.totalPages;
          currentPage = data.currentPage;
          totalRows = data.totalRows;
          updateTable(data.employees);
          updatePagination();
        } catch (err) {
          console.error("Error parsing JSON:", err);
          console.error("Response text:", text);
        }
      })
      .catch((err) => console.error("Error fetching items data:", err));
  
    console.log(totalRows);
  }

function updateTable(employees) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (employees.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td colspan="7" style="text-align: center; padding: 10px;">No records found</td>
        `;
    tableBody.appendChild(row);
  } else {
    employees.forEach((employee) => {
      const row = document.createElement("tr");
      row.innerHTML = `
          <td hidden>${employee.ID}</td>
          <td>${employee.Fname}</td>
          <td>${employee.MName}</td>
          <td>${employee.Lname}</td>
          <td>${employee.Suffix}</td>
          <td>${employee.Status}</td>
          <td>
              ${
                employee.Status === "Inactive"
                  ? `<button class="btn-form-restore" onclick="RestoreEmployee(${employee.ID})">Restore</button>`
                  : `<button class="btn-form-edit" onclick="editEmployee(${employee.ID})">Edit</button>
                   <button class="btn-form-delete" onclick="RemoveEmployee(${employee.ID})">Remove</button>`
              }
          </td>
      `;
      tableBody.appendChild(row);
    });
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
  
  fetchEmployeeData();
  
  