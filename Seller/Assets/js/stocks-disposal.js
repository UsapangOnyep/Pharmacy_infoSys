// Fetch item data to populate the item select dropdown
function fetchItemData() {
  fetch("Actions/Items/fetchItems.php")
    .then((response) => response.json())
    .then((data) => {
      const itemData = data.items;
      return itemData; // Return item data for use in row population
    })
    .catch((error) => {
      console.error("Error fetching item data:", error);
    });
}

// Load item options dynamically in the select element of each row
function loadItemOptions(row) {

  let DisposalType = document.getElementById('disposalReason').value;
  let DataPath = (DisposalType === "Expired") 
    ? "Actions/Stocks/forDisposals.php" 
    : "Actions/Stocks/forDisposals-All.php";
  
  fetch(DataPath)
    .then((response) => response.json())
    .then((data) => {
      const ItemSelect = row.querySelector("select[name='ItemName[]']");
      ItemSelect.innerHTML = '<option value="">Select Item</option>'; // Clear previous options

      data.items.forEach((item) => {
        const option = document.createElement("option");
        option.value = item.ID;
        option.textContent = item.ItemName;
        ItemSelect.appendChild(option);
      });
    })
    .catch((error) => {
      console.error("Error fetching item data:", error);
    });
}

// Refresh each row's dropdown based on the new disposalReason
function onDisposalReasonChange() {
  const tableBody = document.getElementById('tableBody');
  const rows = tableBody.querySelectorAll('tr');
  rows.forEach(row => {
    loadItemOptions(row); 
  });
}


// Update item details when an item is selected from the dropdown
function setSelectedItemInfo(selectElement) {
  const selectedItemID = selectElement.value;
  const row = selectElement.closest('tr');

  if (!selectedItemID) {
    row.querySelector("#ItemDesc").value = "";
    row.querySelector("#Category").value = "";
    row.querySelector("#Brand").value = "";
    row.querySelector("#Model").value = "";
    row.querySelector("#ExpiryDate").value = "";
    return;
  }

  fetch(`Actions/Stocks/get-item-info.php?id=${selectedItemID}`)
    .then((response) => response.json())
    .then((data) => {
      row.querySelector("#ItemDesc").value = data.ItemDesc;
      row.querySelector("#Category").value = data.Category;
      row.querySelector("#Brand").value = data.Brand;
      row.querySelector("#Model").value = data.Model;
      row.querySelector("#ExpiryDate").value = data.ExpiryDate;
    })
    .catch((error) => {
      console.error("Error fetching item data:", error);
    });
}

// Add Row functionality
document.getElementById('AddRow').addEventListener('click', function(event) {
  event.preventDefault();

  const tableBody = document.getElementById('tableBody');
  const newRow = document.createElement('tr');

  // Create the table cells for the new row
  newRow.innerHTML = `
    <td>
      <select name="ItemName[]" onchange="setSelectedItemInfo(this)">
        <option value="">Select Item</option>
        <!-- Options will be populated dynamically by loadItemOptions() -->
      </select>
    </td>
    <td><input type="text" id="ItemDesc" name="ItemDesc[]" readonly></td>
    <td><input type="text" id="Category" name="Category[]" readonly></td>
    <td><input type="text" id="Brand" name="Brand[]" readonly></td>
    <td><input type="text" id="Model" name="Model[]" readonly></td>
    <td><input type="date" id="ExpiryDate" name="ExpiryDate[]" readonly></td>
    <td><input type="number" id="Quantity" name="Quantity[]" min="1" max="1000" required></td>
    <td><button class="btn-default" onclick="removeRow(event)">Remove</button></td>
  `;

  // Append the new row to the table body
  tableBody.appendChild(newRow);

  // Load item options for this row's dropdown
  loadItemOptions(newRow);
});

// Remove Row functionality
function removeRow(event) {
  event.preventDefault();
  const row = event.target.closest('tr');
  row.remove();
}

// Submit form data (optional example)
document.getElementById('Submit').addEventListener('click', function(event) {
  event.preventDefault();

  const tableBody = document.getElementById('tableBody');
  const rows = tableBody.querySelectorAll('tr');
  const formData = new FormData();
    
  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  formData.append("CreatedBy", CreatedBy);
  formData.append("Reason", document.getElementById('disposalReason').value);

  rows.forEach((row, index) => {
    const inputs = row.querySelectorAll('input');
    const select = row.querySelector('select');

    const itemId = select.value;  
    const qtyToDispose = inputs[5].value; 

    if (qtyToDispose > 0) {  
      formData.append(`itemID[${index}]`, itemId);
      formData.append(`qtyToDispose[${index}]`, qtyToDispose);
    }
  });

  const url = "Actions/Stocks/dispose.php";  // Updated URL for disposal action

  fetch(url, {
    method: "POST",
    body: formData,  // Send the FormData with itemID and qtyToDispose
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("HTTP error: " + response.status);
      }

      return response.text().then((text) => {
        try {
          return JSON.parse(text); // Parse JSON response from server
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
          window.location.href = "?page=stocks"; // Redirect to stock page after success
        });
      } else {
        throw new Error(data.message || "Failed to dispose stock.");
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
});
