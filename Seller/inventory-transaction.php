<div class="inventory-transaction-container">

    <div class="inventory-transaction-list">
        <div class="inventory-transaction-header">
            <h2>Inventory / Transaction History</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../reports/All-Inventory-Transaction.php', '_blank', 'width=800,height=600');">Print</button>
            </div>
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



<!-- The Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Item</h2>

        <form id="addStocksForm">
            <label for="ItemName">Item Name:</label>
            <select id="ItemName" name="ItemName" required onchange="setSelectedItemInfo()">
                <option value="">Select an Item</option>
                <!-- Item options here -->
            </select>

            <label for="ItemLongDesc">Long Description:</label>
            <textarea id="ItemLongDesc" name="ItemLongDesc" readonly></textarea>

            <label for="ItemCategory">Category:</label>
            <input type="text" id="ItemCategory" name="ItemCategory" readonly />

            <div class="form-row">
                <div>
                    <label for="ItemBrand">Brand:</label>
                    <input type="text" id="ItemBrand" name="ItemBrand" readonly />
                </div>
                <div>
                    <label for="ItemModel">Model:</label>
                    <input type="text" id="ItemModel" name="ItemModel" readonly />
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="QTY">Quantity:</label>
                    <input type="number" id="QTY" name="QTY" required />
                </div>
                <div>
                    <label for="Price">Selling Price:</label>
                    <input type="number" id="Price" name="Price" required step="0.01" />
                </div>
                <div>
                    <label for="noExpiration">No Expiration:</label>
                    <input type="checkbox" id="noExpiration" name="noExpiration" />
                </div>
                <div>
                    <label for="ExpiryDate">Expiry Date:</label>
                    <input type="date" id="ExpiryDate" name="ExpiryDate" required />
                </div>
            </div>

            <button type="submit" class="btn-default">Save</button>
        </form>
    </div>
</div>

<!-- The Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Price</h2>

        <form id="UpdatePriceForm" enctype="multipart/form-data">
            <input type="hidden" id="editID" name="editID" /> <!-- Hidden ID for updating -->

            <!-- Item Name -->
            <label for="editStockName">Item Name:</label>
            <input type="text" id="editStockName" name="editStockName" readonly />
            <div class="form-row">
                <div>
                    <!-- Old Price -->
                    <label for="editCurrentPrice">Current Price:</label>
                    <input type="text" id="editCurrentPrice" name="editCurrentPrice" readonly />
                </div>
                <div>
                    <!-- New Price -->
                    <label for="editNewPrice">New Price:</label>
                    <input type="text" id="editNewPrice" name="editNewPrice" required />
                </div>
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn-default">Update</button>
        </form>
    </div>
</div>
