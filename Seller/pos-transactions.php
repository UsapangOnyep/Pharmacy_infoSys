<div id="inventory-transaction-container" class="modal">

    <div class="modal-content">
        <div class="inventory-transaction-header">
            <h2>Transaction History</h2>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search stocks..."
                oninput="searchStocks()" />
            <button class="btn-search">
                <img src="Assets/img/icons/search.png" alt="">
            </button>
        </div>

        <hr>
        <div class="table-container">
            <table class="table-default">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Transaction Date</th>
                        <th>Action Taken</th>
                        <th>QTY</th>
                        <th>Item Name</th>
                        <th>Item Description</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Expiry Date</th>
                        <th>Remarks</th>
                        <td hidden>Action</td>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- inventory rows will be loaded here via AJAX -->
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div id="paginationControls">
                <div class="paginationControls-buttons">
                    <button onclick="loadPage(1)">First</button>
                    <button onclick="loadPage(${currentPage - 1})">Prev</button>
                    <button onclick="loadPage(${currentPage + 1})">Next</button>
                    <button onclick="loadPage(${totalPages})">Last</button>
                </div>
            </div>
        </div>
    </div>
</div>