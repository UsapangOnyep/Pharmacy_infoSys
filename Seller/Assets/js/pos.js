//##########################################
//  UI FOR POS PAGE    UI FOR POS PAGE
//##########################################

let items = [];
let debounceTimer;

// Load stocks data from the backend
function loadStocks() {
  const url = `Actions/Stocks/getStocks.php`;

  fetch(url)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Network response was not ok: ${response.statusText}`);
      }
      return response.json();
    })
    .then((response) => {
      try {
        const data = response.stocks;

        items = data.map((stock) => {
          return {
            id: stock.ID,
            Barcode: stock.Barcode,
            ItemName: stock.ItemName,
            ItemDesc: stock.ItemDesc,
            PriceCurrent: stock.PriceCurrent,
            QTY: stock.QTY,
          };
        });

        console.log("Items populated:", items);


      } catch (err) {
        console.error("Error processing stocks data:", err);
      }
    })
    .catch((err) => console.error("Error fetching stocks data:", err));
}

function setSelectedItemInfo() {
  const itemName = document
    .getElementById("ItemName")
    .value.trim()
    .toLowerCase();
  const suggestionBox = document.getElementById("suggestion-box");

  if (itemName === "") {
    suggestionBox.style.display = "none";
    return;
  }

  // Filter items by ItemName or Barcode
  const filteredItems = items.filter(
    (item) =>
      item.ItemName.toLowerCase().startsWith(itemName) ||
      (item.Barcode && item.Barcode.toLowerCase().startsWith(itemName))
  );

  suggestionBox.innerHTML = "";

  if (filteredItems.length > 0) {
    suggestionBox.style.display = "block";
    filteredItems.forEach((item) => {
      const suggestionItem = document.createElement("div");
      suggestionItem.classList.add("suggestion-item");
      suggestionItem.textContent =
        item.ItemName + " (Barcode: " + item.Barcode + ")";

      suggestionItem.onclick = function () {
        document.getElementById("ItemName").value = item.ItemName;

        suggestionBox.style.display = "none";

        const tableBody = document.querySelector(".List-of-orders tbody");
        let existingRow = null;
        const rows = tableBody.querySelectorAll("tr");

        rows.forEach((row) => {
          const rowItemId = row.querySelector("td").textContent;
          if (rowItemId == item.id) {
            existingRow = row;
          }
        });

        if (existingRow) {
          const qtyInput = existingRow.querySelector(".qty");
          qtyInput.value = parseInt(qtyInput.value) + 1;

          updateRowTotal(qtyInput);
        } else {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td hidden>${item.id}</td>
            <td>${item.ItemName}</td>
            <td>${item.ItemDesc}</td>
            <td><span class="item-price">${item.PriceCurrent}</span></td>
            <td><input type="number" class="qty" value="1" min="1" onchange="updateRowTotal(this)" /></td>
            <td><input type="number" class="discount" value="0" min="0" max="100" onchange="updateRowTotal(this)" /></td>
            <td><span class="total-price">${item.PriceCurrent}</span></td>
            <td>
              <button onclick="removeOrder(this)">Remove</button>
            </td>
          `;
          tableBody.appendChild(row);
        }

        document.getElementById("ItemName").value = "";
        setTimeout(() => {
          document.getElementById("ItemName").focus();
          document.getElementById("ItemName").value = "";
        }, 0);
      };

      suggestionBox.appendChild(suggestionItem);
    });
  } else {
    suggestionBox.style.display = "none";
  }
}

function validateDiscountInput(input) {
  const value = parseFloat(input.value);
  if (isNaN(value) || value < 0) {
    input.value = 0;
  } else if (value > 100) {
    input.value = 100;
  }
}

// Debounce function to limit the number of calls to the setSelectedItemInfo function
function debounce(func, delay) {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(func, delay);
}

function updateRowTotal(inputElement) {
  validateDiscountInput(inputElement);
  const row = inputElement.closest("tr");
  const priceCell = row.querySelector(".total-price");
  const qty = parseFloat(row.querySelector(".qty").value);
  let discountPercent = parseFloat(row.querySelector(".discount").value);

  const price = parseFloat(row.querySelector("td:nth-child(4)").textContent); // Get the item's current price
  const totalBeforeDiscount = price * qty;

  // Ensure the discount does not exceed 100%
  discountPercent = Math.min(discountPercent, 100);

  // Calculate the discount as a percentage of the total before discount
  const discountAmount = (totalBeforeDiscount * discountPercent) / 100;
  const total = totalBeforeDiscount - discountAmount;

  priceCell.textContent = total.toFixed(2); // Update total price cell

  computeTotal();
}

// Remove the order row when clicking the "Remove" button
function removeOrder(button) {
  const row = button.closest("tr");
  row.remove();
}

// Initialize the page
document.addEventListener("DOMContentLoaded", function () {
  //=====================
  //  START OF AUTH
  //=====================
  const usertype = localStorage.getItem("usertype");
  const shiftData = localStorage.getItem("shiftData");

  let validShift = false;

  try {
    const parsedShift = JSON.parse(shiftData);
    validShift = parsedShift && parsedShift.shiftNumber; // Make sure shift has ID or some known key
  } catch (e) {
    validShift = false;
  }

  // if (usertype !== "admin" && usertype !== "user") {
  //   if (validShift === null || validShift === false) {
  //     window.location.href = "../Auth/startshift.php";
  //     return;
  //   } else {
  //     window.location.href = "../Auth/";
  //     return;
  //   }
  // }

  if (usertype === "admin" || usertype === "user") {
    if (!validShift) {
      window.location.href = "../Auth/startshift.php";
      return;
    }
  } else {
    window.location.href = "../Auth/";
  }

  window.history.pushState(null, "", window.location.href);
  window.history.replaceState(null, "", window.location.href);
  //=====================
  //  END OF AUTH
  //=====================

  loadStocks(); // Load stocks on page load

  document.getElementById("ItemName").addEventListener("input", function () {
    debounce(() => setSelectedItemInfo(), 300);
  });

  setTimeout(() => {
    document.getElementById("ItemName").focus();
    document.getElementById("ItemName").value = "";
  }, 0);
});

function addItemToTableByNameOrBarcode(itemIdentifier) {
  // Find the item in the items array by either ItemName or Barcode
  const item = items.find(
    (item) =>
      item.ItemName.toLowerCase() === itemIdentifier.toLowerCase() ||
      item.Barcode.toLowerCase() === itemIdentifier.toLowerCase()
  );

  if (item) {
    const tableBody = document.querySelector(".List-of-orders tbody");
    let existingRow = null;
    const rows = tableBody.querySelectorAll("tr");

    // Check if the item is already in the table
    rows.forEach((row) => {
      const rowItemId = row.querySelector("td").textContent;
      if (rowItemId == item.id) {
        existingRow = row;
      }
    });

    if (existingRow) {
      // If the item is already in the table, increment the quantity
      const qtyInput = existingRow.querySelector(".qty");
      qtyInput.value = parseInt(qtyInput.value) + 1;
      updateRowTotal(qtyInput);
    } else {
      // If the item is not in the table, create a new row
      const row = document.createElement("tr");
      row.innerHTML = `
        <td hidden>${item.id}</td>
        <td>${item.ItemName}</td>
        <td>${item.ItemDesc}</td>
        <td><span class="item-price">${item.PriceCurrent}</span></td>
        <td><input type="number" class="qty" value="1" min="1" onchange="updateRowTotal(this)" /></td>
        <td><input type="number" class="discount" value="0" min="0" max="100" onchange="updateRowTotal(this)" /></td>
        <td><span class="total-price">${item.PriceCurrent}</span></td>
        <td>
          <button onclick="removeOrder(this)">Remove</button>
        </td>
      `;
      tableBody.appendChild(row);

      setTimeout(() => {
        document.getElementById("ItemName").focus();
        document.getElementById("ItemName").value = "";
        computeTotal();
      }, 0);
    }
  } else {
    console.log("Item not found.");
  }
}

document.getElementById("ItemName").addEventListener("keydown", function (e) {
  if (e.key === "Enter") {
    e.preventDefault();
    addItemToTableByNameOrBarcode(this.value);
    document.getElementById("ItemName").value = "";
  }
});

function formatAsCurrency(amount) {
  return new Intl.NumberFormat("en-PH", {
    style: "currency",
    currency: "PHP",
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  }).format(amount);
}

function computeTotal() {
  const rows = document.querySelectorAll(".List-of-orders tbody tr");
  let orderTotal = 0;
  let totalDiscount = 0;

  rows.forEach((row) => {
    const price = parseFloat(
      row.querySelector(".total-price").textContent.replace("₱", "").trim()
    ) || 0;

    orderTotal += price;

    const qty = parseFloat(row.querySelector(".qty").value) || 0;
    const pricePerItem = parseFloat(
      row.querySelector("td:nth-child(4)").textContent
    ) || 0;

    const totalBeforeDiscount = pricePerItem * qty;

    const discountPercent = parseFloat(row.querySelector(".discount").value) || 0;
    const discountAmount = (totalBeforeDiscount * discountPercent) / 100;

    totalDiscount += discountAmount;
  });

  // Display total without additional discount
  document.getElementById("orderTotal").value = formatAsCurrency(orderTotal);

  // Handle overall discount (e.g., loyalty discount, promo)
  let overallDiscountPercent = parseFloat(
    document.getElementById("orderDiscount").value.replace("₱", "").trim()
  );
  if (isNaN(overallDiscountPercent) || overallDiscountPercent < 0) {
    overallDiscountPercent = 0;
  }

  const orderDiscountAmount = (orderTotal * overallDiscountPercent) / 100;
  const grandTotal = orderTotal - orderDiscountAmount;

  document.getElementById("orderGrandTotal").value = formatAsCurrency(grandTotal);

  const payment = parseFloat(document.getElementById("orderPayment").value) || 0;
  if (payment >= grandTotal) {
    const change = payment - grandTotal;
    document.getElementById("orderChange").value = formatAsCurrency(change);
  } else {
    document.getElementById("orderChange").value = "";
  }
}


document.getElementById("orderPayment").addEventListener("input", function () {
  computeTotal();
});

document.getElementById("orderDiscount").addEventListener("input", function () {
  const discountInput = parseFloat(this.value);
  if (!isNaN(discountInput) && discountInput >= 0 && discountInput <= 100) {
    const grandTotal = parseFloat(
      document.getElementById("orderGrandTotal").value.replace("₱", "").trim()
    );
    const discountAmount = (grandTotal * discountInput) / 100;
    document.getElementById("orderGrandTotal").value = formatAsCurrency(
      grandTotal - discountAmount
    );
    computeTotal();
  } else {
    this.value = "";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const employee = localStorage.getItem("employee")
    ? JSON.parse(localStorage.getItem("employee"))
    : null;
  const userName = employee ? `${employee.LName}, ${employee.Fname}` : "Guest";
  document.getElementById("user-name").textContent = userName;

  const buttonContainer = this.querySelector(".POS-button-container");
  if (localStorage.getItem("usertype") === "admin") {
    buttonContainer.innerHTML = `
      <button id="btnVoid">Void</button>
      <button id="btnVoidHistory">Void History</button>
      <button id="btnRePrint">Re-Print</button>
      <button id="btnTransactionHistory">Transaction History</button>
      <button id="btnCheckout">Checkout</button>
      <button id="btnXReport">X Report</button>
      <button id="btnZReport">Z Report</button>
      <button id="btnAdminPanel">Admin Panel</button>`;
  } else {
    buttonContainer.innerHTML = `
      <button id="btnVoid">Void</button>
      <button id="btnVoidHistory">Void History</button>
      <button id="btnRePrint">Re-Print</button>
      <button id="btnTransactionHistory">Transaction History</button>
      <button id="btnCheckout">Checkout</button>
      <button id="btnChangeShift">Change Shift</button>
      <button id="logout-link">Logout</button>`;
  }

  document.getElementById("btnVoid")?.addEventListener("click", () => Checkout(true));
  document.getElementById("btnCheckout")?.addEventListener("click", () => Checkout(false));
  // document.getElementById("btnVoidHistory")?.addEventListener("click", showVoidHistory);
  // document.getElementById("btnRePrint")?.addEventListener("click", reprintReceipt);
  // document.getElementById("btnTransactionHistory")?.addEventListener("click", showTransactionHistory);
  // document.getElementById("btnXReport")?.addEventListener("click", generateXReport);
  // document.getElementById("btnZReport")?.addEventListener("click", generateZReport);
  document.getElementById("btnAdminPanel")?.addEventListener("click", goToAdmin);
  document.getElementById("btnChangeShift")?.addEventListener("click", showModal);
  // document.getElementById("btnLogout")?.addEventListener("click", logoutUser);
});

//##########################################
//  CRUD OPERATIONS FOR POS PAGE
//##########################################

const goToAdmin = () => {
  window.location.href = "index.php";
};

function Checkout(isVoid) {
  const rows = document.querySelectorAll(".List-of-orders tbody tr");
  let orderItems = [];

  rows.forEach((row) => {
    const itemId = row.querySelector("td").textContent.trim();
    const qty = parseInt(row.querySelector(".qty").value, 10) || 0;
    const priceElement = row.querySelector(".item-price");

    if (priceElement) {
      const price = parseFloat(priceElement.textContent.replace(/[^\d.-]/g, ""));
      if (!isNaN(price) && qty > 0) {
        orderItems.push({ StockID: itemId, Qty: qty, Price: price });
      }
    }
  });

  const payment = parseFloat(document.getElementById("orderPayment").value) || 0;
  let discount = parseFloat(document.getElementById("orderDiscount").value) || 0;
  let orderTotal = parseFloat(document.getElementById("orderTotal").value.replace(/[^\d.-]/g, "")) || 0;
  let Change = parseFloat(document.getElementById("orderChange").value.replace(/[^\d.-]/g, "")) || 0;

  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";

  const url = "Actions/POS/checkout.php";
  const data = {
    orderItems,
    payment,
    discount,
    orderTotal,
    Change,
    orderStatus: isVoid ? "0" : "1",
    CreatedBy
  };

  if (!isVoid) {
    if (orderItems.length === 0) {
      Swal.fire({ icon: "error", title: "Error", text: "No items in the order!" });
      return;
    }
    if (payment <= 0) {
      Swal.fire({ icon: "error", title: "Error", text: "Payment must be greater than 0!" });
      return;
    }
    if (payment < orderTotal) {
      Swal.fire({ icon: "error", title: "Error", text: "Payment is less than the total amount!" });
      return;
    }
    if (discount < 0 || discount > 100) {
      Swal.fire({ icon: "error", title: "Error", text: "Discount must be between 0 and 100!" });
      return;
    }
    if (Change < 0) {
      Swal.fire({ icon: "error", title: "Error", text: "Change cannot be negative!" });
      return;
    }
  }
  
  fetch(url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: { "Content-Type": "application/json" }
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        Swal.fire({ icon: "success", title: data.message, timer: 500 }).then(() => {
          if (!isVoid) {
            printReceipt(data.TransactionNo);
          }
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Error", text: data.message });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({ icon: "error", title: "Error", text: "Something went wrong!" });
    });
}


//##########################################
//  PRINT OUTS FOR POS PAGE
//##########################################

function printReceipt(TransactionNo) {
  const printWindow = window.open(`../Reports/Receipt.php?TransactionNo=${encodeURIComponent(TransactionNo)}`, '_blank', 'width=800,height=600');
  if (printWindow) {
      printWindow.print();
  } else {
      Swal.fire({ icon: "error", title: "Error", text: "Pop-up blocked! Please allow pop-ups for this site." });
  }
}


function printEndOfShift(AccountID) {
  const printWindow = window.open(`../Reports/zReport.php?AccountID=${encodeURIComponent(AccountID)}`, '_blank', 'width=800,height=600');
  if (printWindow) {
      printWindow.focus();
  } else {
      Swal.fire({ icon: "error", title: "Error", text: "Pop-up blocked! Please allow pop-ups for this site." });
  }
}

/* ###################################################
        MODAL MODAL MODAL MODAL MODAL MODAL MODAL
   ###################################################*/
function showModal() {
  document.getElementById("endShiftModal").style.display = "block";
}

function closeModal() {
  document.getElementById("endShiftModal").style.display = "none";
}

document.getElementById("Submit_ChangeofShip").addEventListener("click", function (event) {
  event.preventDefault(); 

  const form = document.querySelector("#endShiftForm");
  if (!form) {
    console.error("Error: Shift form not found!");
    return;
  }

  const user = JSON.parse(localStorage.getItem("user"));
  const CreatedBy = user ? user.ID : "";
  const shiftData = JSON.parse(localStorage.getItem("shiftData"));
  const shiftNumber = shiftData ? shiftData.shiftNumber : null;
  
  if (!CreatedBy || !shiftNumber) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "You must be logged in and have a valid shift to end."
    });
    return;
  }

  const data = {
    CreatedBy,
    shiftNumber,
    txt1k: parseInt(document.getElementById("txt1k").value) || 0,
    txt5H: parseInt(document.getElementById("txt5H").value) || 0,
    txt2H: parseInt(document.getElementById("txt2H").value) || 0,
    txt1H: parseInt(document.getElementById("txt1H").value) || 0,
    txt50: parseInt(document.getElementById("txt50").value) || 0,
    txt20: parseInt(document.getElementById("txt20").value) || 0,
    txt10: parseInt(document.getElementById("txt10").value) || 0,
    txt5: parseInt(document.getElementById("txt5").value) || 0,
    txt1: parseInt(document.getElementById("txt1").value) || 0,
    txt25c: parseInt(document.getElementById("txt25c").value) || 0
  };

  fetch("Actions/POS/endshift.php", {
    method: "POST",
    body: JSON.stringify(data),
    headers: { "Content-Type": "application/json" }
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        Swal.fire({
          icon: "success",
          title: data.message,
          timer: 1000
        }).then(() => {

          printEndOfShift(CreatedBy);

          setTimeout(() => {
            document.getElementById("logout-link").click();
          }, 2000);
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: data.message
        });
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Something went wrong!"
      });
    });
});



