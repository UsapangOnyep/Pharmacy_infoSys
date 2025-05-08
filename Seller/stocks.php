<div class="stocks-container">

    <div class="stocks-list">
        <div class="stocks-header">
            <h2>Inventory / Stocks</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.location.href='?page=stocks-disposal';">Dispose
                    Stocks</button>

                <button class="btn-default"
                    onclick="window.open('../reports/All-Stocks.php', '_blank', 'width=800,height=600');">Print</button>

                <button class="btn-default" onclick="showModal()">Add</button>
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
                        <th hidden>ID</th>
                        <th>Image</th>
                        <th>Barcode</th>
                        <th>Item Name</th>
                        <th>Item Description</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Reorder Level</th>
                        <th>Stocks</th>
                        <th>Expiry Date</th>
                        <th>Selling Price</th>
                        <th>Action</th>
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
    <div class="wide-modal-content">
        
        <!-- Fixed Modal Header -->
        <div class="modal-header">
            <h2>Add New Stocks</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>

        <!-- Scrollable Content -->
        <div class="modal-body">
            <form id="addStocksForm">
                <div class="table-container1">
                    <table id="stocksTable" class="table-default1">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Barcode</th>
                                <th>Long Description</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>No Expiry</th>
                                <th>Expiry Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
                <button type="button" onclick="addRow()">Add Row</button>
                <button type="submit" class="btn-default">Save</button>
            </form>
        </div>
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
            <input type="text" id="editStockName" name="editStockName" readonly class="readonly-field"/>
            <div class="form-row">
                <div>
                    <!-- Old Price -->
                    <label for="editCurrentPrice">Current Price:</label>
                    <input type="text" id="editCurrentPrice" name="editCurrentPrice" readonly class="readonly-field"/>
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