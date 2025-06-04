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
    fetchitemsData();
  }
}

function searchitems() {
  currentPage = 1;
  fetchitemsData();
}

function fetchitemsData() {
  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/Items/load.php?page=${currentPage}&search=${encodeURIComponent(
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
        updateTable(data.items);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching items data:", err));
}

function updateTable(items) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (items.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
            <td colspan="9" style="text-align: center; padding: 10px;">No records found</td>
        `;
    tableBody.appendChild(row);
  } else {
    items.forEach((item) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                <td hidden>${item.id}</td>
                 <td>
                    <img src="${
                      item.ItemPath
                    }" style="max-width: 100px; height: auto; cursor: pointer;" 
                         onclick="openFullScreen('${item.ItemPath}', '${
        item.ItemName
      }')">
                </td>
                <td>${item.Category}</td>
                <td>${item.Brand}</td>
                <td>${item.Model}</td>
                <td>${item.ItemName}</td>
                <td>${item.ItemDesc}</td>
                <td>${item.ReorderLevel}</td>
                <td>${item.Status}</td>
                <td>
                    ${
                      item.Status === "Inactive"
                        ? `<button class="btn-form-restore" onclick="RestoreItem(${item.id})">Restore</button>`
                        : `<button class="btn-form-edit" onclick="editItem(${item.id})">Edit</button>
                        <button class="btn-form-delete" onclick="RemoveItem(${item.id})">Remove</button>`
                    }
                </td>
            `;
      tableBody.appendChild(row);
    });

    console.log(items);
  }
}

// Function to show image in full screen modal
function openFullScreen(imageSrc, itemName) {
  const modal = document.getElementById("imageModal");
  const modalImg = document.getElementById("modalImage");
  const captionText = document.getElementById("caption");
  modal.style.display = "flex"; // Use flex to center content
  modalImg.src = imageSrc;
  captionText.innerHTML = itemName;
}

function closeImageModal() {
  const modal = document.getElementById("imageModal");
  modal.style.display = "none";
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

fetchitemsData();

/* ###################################################
        CRUD CRUD CRUD CRUD CRUD CRUD CRUD CRUD
   ###################################################*/
document.querySelector("#addItemForm").addEventListener("submit", AddItem);

function AddItem(event) {
  event.preventDefault(); // Prevent the default form submission

  const form = document.querySelector("#addItemForm");

  const itemName = document.querySelector("#ItemName").value.trim();
  const itemDesc = document.querySelector("#ItemLongDesc").value.trim();
  let itemReorderLevel = document.querySelector("#ReorderLevel").value.trim();

  // Check if required fields are filled
  if (!itemName || !itemDesc || !itemReorderLevel) {
    Swal.fire({
      position: "top",
      toast: true,
      showConfirmButton: false,
      timer: 1500,
      icon: "error",
      title: "Error",
      text: "All required fields must be filled!",
    });
    return;
  }

  // Convert itemReorderLevel to a number
  itemReorderLevel = Number(itemReorderLevel);

  // Check if reorder level is a valid positive whole number
  if (
    isNaN(itemReorderLevel) ||
    !Number.isInteger(itemReorderLevel) ||
    itemReorderLevel <= 0
  ) {
    Swal.fire({
      position: "top",
      toast: true,
      showConfirmButton: false,
      timer: 1500,
      icon: "error",
      title: "Error",
      text: "Reorder level must be a positive whole number!",
    });
    return;
  }
  const formData = new FormData(form);

  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  const url = "Actions/Items/create.php";

  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("HTTP error: " + response.message);
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
          showConfirmButton: false,
          timer: 1500,
          icon: "success",
          title: data.message,
        }).then(() => {
          window.location.reload();
        });
      } else {
        throw new Error(data.message || "Failed to save item.");
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
// Function to Remove Employee
function RestoreItem(ID) {
  Swal.fire({
    position: "top",
    title: "Are you sure?",
    text: "This will restore the item.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, restore it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Items/restore.php", {
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
              fetchitemsData(); // Reload employee data
            });
          } else {
            Swal.fire({
              position: "top",
              toast: true,
              showConfirmButton: false,
              timer: 1500,
              icon: "error",
              title: "Error",
              text: data.message || "Failed to restore item.",
            });
          }
        })
        .catch((error) => {
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

// Function to Restore Employee
function RemoveItem(ID) {
  Swal.fire({
    title: "Are you sure?",
    text: "This will remove the item from the system.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, remove it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("Actions/Items/remove.php", {
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
              // Reload the employee data or update the table after removal
              fetchitemsData();
            });
          } else {
            Swal.fire({
              position: "top",
              toast: true,
              showConfirmButton: false,
              timer: 1500,
              icon: "error",
              title: "Error",
              text: data.message || "Failed to remove item.",
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

document
  .getElementById("editProductForm")
  .addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the form from submitting in the traditional way

    if (!hasChanges()) {
      Swal.fire({
        position: "top",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
        icon: "info",
        title: "No Changes Detected",
        text: "You have not made any changes.",
      });
      return;
    }

    let reorderLevel = Number(
      document.getElementById("editReorderLevel").value.trim()
    );

    if (
      isNaN(reorderLevel) ||
      !Number.isInteger(reorderLevel) ||
      reorderLevel <= 0
    ) {
      Swal.fire({
        position: "top",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
        icon: "error",
        title: "Error",
        text: "Reorder level must be a positive whole number!",
      });
      return;
    }

    const form = document.querySelector("#editProductForm");
    const formData = new FormData(form);

    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);

    const url = "Actions/Items/edit.php";

    fetch(url, {
      method: "POST", // Ensure the method is POST
      body: formData, // Send the form data including files
    })
      .then((response) => response.json()) // Expecting a JSON response
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            position: "top",
            toast: true,
            showConfirmButton: false,
            timer: 1500,
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
            text: data.message, // Show error message from the response
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
          text: "Something went wrong!", // Handle any other errors
        });
      });
  });

const form = document.querySelector("#editProductForm");
let originalData = {};
let originalImagePath = "";

function storeOriginalData() {
  originalData = {};
  form.querySelectorAll("input, textarea").forEach((input) => {
    if (input.type !== "file") {
      originalData[input.name] = input.value.trim();
    }
  });

  const imgPreview = document.getElementById("editImagePreview");
  originalImagePath = imgPreview.src || "";
}

// ✅ Function to Check if Form Data Has Changed
function hasChanges() {
  let isChanged = false;
  form.querySelectorAll("input, textarea").forEach((input) => {
    if (input.type !== "file") {
      if (input.value.trim() !== (originalData[input.name] || "")) {
        isChanged = true;
      }
    }
  });

  const fileInput = document.getElementById("editProductImageUpload");
  if (fileInput.files.length > 0) {
    isChanged = true;
  }

  return isChanged;
}

// Show edit modal and populate fields
function editItem(ItemId) {
  clearEditImage();
  fetch("Actions/Items/get-Item-Details.php", {
    method: "POST",
    body: new URLSearchParams({ ID: ItemId }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Populate fields with existing data
        document.getElementById("editItemId").value = data.Item.ItemID;
        document.getElementById("editCategory").value = data.Item.Category;
        document.getElementById("editItemBrand").value = data.Item.Brand;
        document.getElementById("editItemModel").value = data.Item.Model;
        document.getElementById("editItemName").value = data.Item.ItemName;
        document.getElementById("editItemDescription").value =
          data.Item.ItemDesc;
        document.getElementById("editReorderLevel").value =
          data.Item.ReorderLevel;

        if (data.Item.ItemPath) {
          document.getElementById("editImagePreview").src = data.Item.ItemPath;
          document.getElementById("editImagePreview").style.display = "block";
          document.getElementById("editClearImage").style.display = "block";
        } else {
          document.getElementById("editImagePreview").style.display = "none";
          document.getElementById("editClearImage").style.display = "none";
        }

        storeOriginalData();

        document.getElementById("editModal").style.display = "block";
      } else {
        console.log(data);
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
      console.log(error);
      Swal.fire({
        position: "top",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
        icon: "error",
        title: "Error",
        text: error.message,
      });
    });
}

/* ###################################################
        PICTURE PICTURE PICTURE PICTURE PICTURE
   ###################################################*/

function clearImage() {
  document.getElementById("ItemImageUpload").value = ""; // Clear the file input
  document.getElementById("imagePreview").classList.add("hidden"); // Hide the preview
}

var fileInput = document.querySelector("#ItemImageUpload");
var imagePreview = document.querySelector("#imagePreview");
var clearButton = document.querySelector("#clearImage");

if (fileInput) {
  fileInput.addEventListener("change", function (event) {
    var file = event.target.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function (e) {
        if (imagePreview) {
          imagePreview.src = e.target.result;
          imagePreview.style.display = "block";
        }
        if (clearButton) {
          clearButton.style.display = "block"; // Show the clear button
        }
      };
      reader.readAsDataURL(file);
    } else {
      if (imagePreview) {
        imagePreview.src = "";
        imagePreview.style.display = "none";
      }
      if (clearButton) {
        clearButton.style.display = "none"; // Hide the clear button
      }
    }
  });
}

// Clear image preview and reset file input
if (clearButton) {
  clearButton.addEventListener("click", function () {
    if (imagePreview) {
      imagePreview.src = "";
      imagePreview.style.display = "none";
    }
    if (fileInput) {
      fileInput.value = ""; // Clear the file input
    }
    clearButton.style.display = "none"; // Hide the clear button
  });
}

function clearEditImage() {
  document.getElementById("editProductImageUpload").value = "";
  document.getElementById("editImagePreview").style.display = "none";
  document.getElementById("editClearImage").style.display = "none";
}

document
  .getElementById("editProductImageUpload")
  .addEventListener("change", function (event) {
    const file = event.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("editImagePreview").src = e.target.result;
        document.getElementById("editImagePreview").style.display = "block";
        document.getElementById("editClearImage").style.display = "block";
      };
      reader.readAsDataURL(file);
    } else {
      document.getElementById("editImagePreview").style.display = "none";
      document.getElementById("editClearImage").style.display = "none";
    }
  });
