function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

function showModal() {
  document.getElementById("addModal").style.display = "block";

  // Delete the existing rows in the table body
  // const tableBody = document.getElementById("stocksTable");
  // while (tableBody.rows.length > 0) {
  //   tableBody.deleteRow(0);
  // }

  const tableBody = document.querySelector("#stocksTable tbody");
  tableBody.innerHTML = "";

  addRow();
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
    fetchStockssData();
  }
}

function searchStocks() {
  currentPage = 1;
  fetchStockssData();
}

function fetchStockssData() {
  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/Stocks/load.php?page=${currentPage}&search=${encodeURIComponent(
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
        updateTable(data.stocks);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching stocks data:", err));
}

function updateTable(stocks) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (stocks.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
              <td colspan="10" style="text-align: center; padding: 10px;">No records found</td>
          `;
    tableBody.appendChild(row);
  } else {
    stocks.forEach((stock) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                  <td hidden>${stock.ID}</td>
                  <td><img src="${
                    stock.ItemPath
                  }" style="max-width: 100px; height: auto;"></td>
                  <td>${stock.Barcode}</td>
                  <td>${stock.ItemName}</td>
                  <td>${stock.ItemDesc}</td>
                  <td>${stock.Category}</td>
                  <td>${stock.Brand}</td>
                  <td>${stock.Model}</td>
                  <td>${stock.ReorderLevel}</td>
                  <td>${stock.QTY}</td>
                  <td>${stock.ExpiryDate}</td>
                  <td>${formatAsCurrency(stock.PriceCurrent)}</td>
                  <td> <button class="btn-form-edit" onclick="getItemDataForPriceUpdate(this)">Update Price</button></td>
              `;
      tableBody.appendChild(row);
    });

    console.log(stocks);
  }
}

function formatAsCurrency(amount) {
  return new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount);
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

fetchStockssData();

/* ###################################################
        CRUD CRUD CRUD CRUD CRUD CRUD CRUD CRUD
   ###################################################*/
document.querySelector("#addStocksForm").addEventListener("submit", AddItem);

function AddItem(event) {
  event.preventDefault();

  const form = document.querySelector("#addStocksForm");
  const itemSelects = document.querySelectorAll('select[name="ItemName[]"]');
  const Barcodes = document.querySelectorAll('input[name="Barcode[]"]');
  const QTYs = document.querySelectorAll('input[name="QTY[]"]');
  const Prices = document.querySelectorAll('input[name="Price[]"]');
  const ExpiryDates = document.querySelectorAll('input[name="ExpiryDate[]"]');
  const noExpirationChecks = document.querySelectorAll(
    'input[name="noExpiration[]"]'
  );

  for (let i = 0; i < itemSelects.length; i++) {
    const itemID = itemSelects[i].value.trim();
    const Barcode = Barcodes[i].value.trim();
    const QTY = parseInt(QTYs[i].value.trim(), 10);
    const Price = parseFloat(Prices[i].value.trim());
    const ExpiryDate = ExpiryDates[i].value.trim();
    const noExpiration = noExpirationChecks[i].checked;

    if (noExpiration && ExpiryDate !== "") {
      Swal.fire({
        position: "top",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
        icon: "error",
        title: "Error",
        text: "Expiry date should be empty if 'No Expiration' is checked.",
      });
      return;
    }

    if (
      !itemID ||
      !Barcode ||
      !QTY ||
      !Price ||
      (!noExpiration && !ExpiryDate)
    ) {
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
  }

  const formData = new FormData(form);
  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);

  for (let pair of formData.entries()) {
    console.log(pair[0] + ": " + pair[1]);
  }

  fetch("Actions/Stocks/create.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) throw new Error("HTTP error: " + response.status);
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        Swal.fire({
          position: "top",
          toast: true,
          icon: "success",
          title: data.message,
          showConfirmButton: false,
          timer: 1000,
        }).then(() => window.location.reload());
      } else {
        throw new Error(data.message || "Failed to save stocks.");
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

function getItemDataForPriceUpdate(button) {
  document.getElementById("editModal").style.display = "block";
  const row = button.closest("tr");
  console.log(row);

  const id = row.children[0].textContent;
  const name = row.children[3].textContent;
  let price = row.children[11].textContent;

  document.getElementById("editID").value = id;
  document.getElementById("editStockName").value = name;
  document.getElementById("editCurrentPrice").value = price;
  document.getElementById("editNewPrice").value = "0.00";
}

document
  .getElementById("UpdatePriceForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const form = document.querySelector("#UpdatePriceForm");
    const formData = new FormData(form);

    const user = JSON.parse(localStorage.getItem("user"));
    const CreatedBy = user ? user.ID : "";
    formData.append("CreatedBy", CreatedBy);

    let price = formData.get("editNewPrice").trim();
    price = Number(price);

    if (isNaN(price) || !Number.isInteger(price) || price <= 0) {
      Swal.fire({
        position: "top",
        toast: true,
        showConfirmButton: false,
        timer: 1500,
        icon: "error",
        title: "Error",
        text: "Price must be a positive whole number!",
      });
      return;
    }

    const url = "Actions/Stocks/priceUpdate.php";

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
          icon: "error",
          title: "Error",
          text: "Something went wrong!",
        });
      });
  });

/* ###################################################
  TABLE TABLE TABLE TABLE TABLE TABLE TABLE TABLE
   ###################################################*/

function addRow() {
  let table = document
    .getElementById("stocksTable")
    .getElementsByTagName("tbody")[0];
  let newRow = table.insertRow();

  newRow.innerHTML = `
        <td>
            <select name="ItemName[]" required onchange="setSelectedItemInfo(this)">
                <option value="">Select an Item</option>
            </select>
        </td>
        <td><input type="text" name="Barcode[]" required /></td>
        <td><textarea name="ItemLongDesc[]" readonly class="readonly-field"></textarea></td>
        <td><input type="text" name="ItemCategory[]" readonly class="readonly-field"/></td>
        <td><input type="text" name="ItemBrand[]" readonly class="readonly-field"/></td>
        <td><input type="text" name="ItemModel[]" readonly class="readonly-field"/></td>
        <td><input type="number" name="QTY[]" required min="1" value="1"/></td>
        <td>
        <input type="number" name="Price[]" required step="0.01" min="0" value="0.00" 
              onblur="this.value = parseFloat(this.value || 0).toFixed(2)" />
        </td>
        <td><input type="checkbox" name="noExpiration[]" onchange="toggleExpiryDate(this)" /></td>
        <td><input type="date" name="ExpiryDate[]" required /></td>
        <td><button type="button" onclick="removeRow(this)">Remove</button></td>
    `;

  // Fetch and populate the item select dropdown for the new row
  fetchItemData(newRow.querySelector("select[name='ItemName[]']"));
}

// Function to remove a row
function removeRow(button) {
  let row = button.closest("tr");
  row.remove();
}

// Toggle Expiry Date field based on No Expiration checkbox
function toggleExpiryDate(checkbox) {
  let expiryInput = checkbox
    .closest("tr")
    .querySelector('input[name="ExpiryDate[]"]');
  expiryInput.disabled = checkbox.checked;
}

// Ensure setSelectedItemInfo updates the correct row based on selection
function setSelectedItemInfo(select) {
  let row = select.closest("tr");
  let selectedItemID = select.value;

  if (!selectedItemID) {
    row.querySelector('textarea[name="ItemLongDesc[]"]').value = "";
    row.querySelector('input[name="ItemCategory[]"]').value = "";
    row.querySelector('input[name="ItemBrand[]"]').value = "";
    row.querySelector('input[name="ItemModel[]"]').value = "";
    return;
  }

  fetch(`Actions/Items/fetchItemById.php?id=${selectedItemID}`)
    .then((response) => response.json())
    .then((data) => {
      row.querySelector('textarea[name="ItemLongDesc[]"]').value =
        data.ItemDesc;
      row.querySelector('input[name="ItemCategory[]"]').value = data.Category;
      row.querySelector('input[name="ItemBrand[]"]').value = data.Brand;
      row.querySelector('input[name="ItemModel[]"]').value = data.Model;
    })
    .catch((error) => {
      console.error("Error fetching item data:", error);
    });
}

function fetchItemData(selectElement) {
  fetch("Actions/Items/fetchItems.php")
    .then((response) => response.json())
    .then((data) => {
      if (!selectElement) return; // Avoid errors if element is null

      selectElement.innerHTML = '<option value="">Select an Item</option>'; // Reset options

      data.items.forEach((item) => {
        const option = document.createElement("option");
        option.value = item.ID;
        option.textContent = item.ItemName;
        selectElement.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching item data:", error);
    });
}
