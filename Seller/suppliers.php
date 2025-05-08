<div class="supplier-container">

    <div class="supplier-list">
        <div class="suppliers-header">
            <h2>Suppliers</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../Reports/All-suppliers.php', '_blank', 'width=800,height=600');">Print</button>
                <button class="btn-default" onclick="showModal()">Add</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search supplier..."
                oninput="searchsupplier()" />
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
                        <th>Supplier Name</th>
                        <th>Supplier Address</th>
                        <th>TIN</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- supplier rows will be loaded here via AJAX -->
                </tbody>
            </table>

            <!-- Pagination Controls -->
            <div id="paginationControls">
                <div class="paginationControls-buttons">
                    <button onclick="loadPage(1)">First</button>
                    <button onclick="loadPage(currentPage - 1)">Prev</button>
                    <button onclick="loadPage(currentPage + 1)">Next</button>
                    <button onclick="loadPage(totalPages)">Last</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- The Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Supplier</h2>
        <form id="addSupplierForm" enctype="multipart/form-data">
            <input type="hidden" id="ID" name="ID" />

            <!-- Name -->
            <label for="supplierName">Supplier Name:</label>
            <input type="text" id="supplierName" name="supplierName" required />

            <!-- Address -->
            <label for="supplierAddress">Supplier Address:</label>
            <input type="text" id="supplierAddress" name="supplierAddress" required />

            <!-- TIN -->
            <label for="supplierTIN">TIN:</label>
            <input type="text" id="supplierTIN" name="supplierTIN" required />

            <!-- Submit Button -->
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- The Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Supplier</h2>
        
        <form id="editSupplierForm" enctype="multipart/form-data">
            <input type="hidden" id="editID" name="editID" /> <!-- Hidden ID for updating -->

            <!-- Name -->
            <label for="editsupplierName">Supplier Name:</label>
            <input type="text" id="editsupplierName" name="editsupplierName" required />

            <!-- Address -->
            <label for="editsupplierAddress">Supplier Address:</label>
            <input type="text" id="editsupplierAddress" name="editsupplierAddress" required />

            <!-- TIN -->
            <label for="editsupplierTIN">TIN:</label>
            <input type="text" id="editsupplierTIN" name="editsupplierTIN" required />

            <!-- Submit Button -->
            <button type="submit" class="btn-default">Update</button>
        </form>
    </div>
</div>