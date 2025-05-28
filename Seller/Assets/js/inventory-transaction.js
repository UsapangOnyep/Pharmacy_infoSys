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
    fetchTransactionData();
  }
}

function searchStocks() {
  currentPage = 1;
  fetchTransactionData();
}

function fetchTransactionData() {
  const searchQuery = document.getElementById("searchInput").value;
  const url = `Actions/Inventory-Transaction/load.php?page=${currentPage}&search=${encodeURIComponent(
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
        updateTable(data.transactions);
        updatePagination();
      } catch (err) {
        console.error("Error parsing JSON:", err);
        console.error("Response text:", text);
      }
    })
    .catch((err) => console.error("Error fetching transactions data:", err));
}

function updateTable(transactions) {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  if (transactions.length === 0) {
    const row = document.createElement("tr");
    row.innerHTML = `
              <td colspan="10" style="text-align: center; padding: 10px;">No records found</td>
          `;
    tableBody.appendChild(row);
  } else {
    transactions.forEach((transaction) => {
      const row = document.createElement("tr");
      row.innerHTML = `
                  <td>${transaction.ID}</td>
                  <td>${transaction.TransactionDate}</td>
                  <td>${transaction.ActionTaken}</td>
                  <td>${transaction.QTY}</td>
                  <td>${transaction.ItemName}</td>
                  <td>${transaction.ItemDesc}</td>
                  <td>${transaction.Category}</td>
                  <td>${transaction.Brand}</td>
                  <td>${transaction.Model}</td>
                  <td>${transaction.ExpiryDate}</td>
                  <td>${transaction.Remarks}</td>
                  <td hidden></td>
              `;
      tableBody.appendChild(row);
    });

    console.log(transactions);
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

fetchTransactionData();
