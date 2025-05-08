function Addaccount(event) {
  event.preventDefault(); // Prevent the default form submission

  const form = document.querySelector("#addaccountForm");
  const formData = new FormData(form);
  
  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  const url = "Actions/account/create.php";

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
        throw new Error(data.message || "Failed to save account.");
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

// Function to Remove account
function Restoreaccount(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will restore the account.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, restore it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/account/restore.php", {
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
              fetchaccountData(); // Reload account data
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Failed to restore account.",
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

// Function to Restore account
function Removeaccount(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will remove the account from the system.",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/account/remove.php", {
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
              // Reload the account data or update the table after removal
              fetchaccountData();
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data.message || "Failed to remove account.",
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

function editaccount(accountID) {
  document.getElementById("editModal").style.display = "block";

  fetch("Actions/account/getaccount.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({ ID: accountID }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        document.getElementById("editaccountID").value = data.account.ID;
        document.getElementById("editaccountName").value = data.account.FullName;
        document.getElementById("editaccountEmail").value = data.account.Email;
        document.getElementById("editaccountPosition").value = data.account.Position;
        document.getElementById("editaccountUsername").value = data.account.Username;
        document.getElementById("editaccountType").value = data.account.UserType;
        document.getElementById("editaccountStatus").value = data.account.Status;
      } else {
        alert("Error loading account data.");
      }
    })
    .catch((error) => console.error("Error:", error));
}

function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

document
  .getElementById("editaccountForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.querySelector("#editaccountForm");
    const formData = new FormData(form);

    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);
  
    const url = "Actions/account/edit.php";

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
  .querySelector("#addaccountForm")
  .addEventListener("submit", Addaccount);

function showModal() {
  document.getElementById("addModal").style.display = "block";
  fetchEmployeeData();
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
  fetchaccountData();
}

function searchaccount() {
  currentPage = 1;
  fetchaccountData();
}

function fetchaccountData() {
  console.log(totalRows);

  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/account/load.php?page=${currentPage}&search=${encodeURIComponent(
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
        updateTable(data.accounts);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching items data:", err));

  console.log(totalRows);
}

function updateTable(accounts) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (accounts.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td colspan="7" style="text-align: center; padding: 10px;">No records found</td>
        `;
    tableBody.appendChild(row);
  } else {
    accounts.forEach((account) => {
      const row = document.createElement("tr");
      row.innerHTML = `
          <td hidden>${account.ID}</td>
          <td >${account.Fullname}</td>
          <td >${account.Username}</td>
          <td >${account.UserType}</td>
          <td >${account.Email}</td>
          <td >${account.Position}</td>
          <td >${account.Status}</td>
          <td>
              ${
                account.Status === "Inactive"
                  ? `<button class="btn-form-restore" onclick="Restoreaccount(${account.ID})">Restore</button>`
                  : `<button class="btn-form-edit" onclick="editaccount(${account.ID})">Edit</button>
                   <button class="btn-form-delete" onclick="Removeaccount(${account.ID})">Remove</button>`
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

fetchaccountData();

/* ###################################################
      GET THE LIST OF EMPLOYEE WITH NO ACCOUNT YET
   ###################################################*/

function fetchEmployeeData() {
  fetch("Actions/Employee/fetchEmployee.php")
    .then((response) => response.json())
    .then((data) => {
      const employeeSelect = document.getElementById("accountName");
      employeeSelect.innerHTML = '<option value="">Select an Employee</option>';

      data.employees.forEach((employee) => {
        const option = document.createElement("option");
        option.value = employee.ID;
        option.textContent = employee.FullName;
        employeeSelect.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching employee data:", error);
    });
}

document.addEventListener("DOMContentLoaded", fetchEmployeeData);
