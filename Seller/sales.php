<div class="sales-container">

    <div class="sales-list">
        <div class="sales-header">
            <h2>Sales</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../reports/All-Items.php', '_blank', 'width=800,height=600');">Print</button>
                <button class="btn-default" onclick="showModal()">Add</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search inventory..."
                oninput="searchitems()" />
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
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Item Name</th>
                        <th>Item Description</th>
                        <th>Reorder Level</th>
                        <th>Status</th>
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
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Item</h2>

        <form id="addItemForm">
            <label for="ItemImageUpload">Upload Image:</label>
            <input type="file" id="ItemImageUpload" name="ItemImageUpload" />

            <div class="modal-image-container">
                <img id="imagePreview" />
                <span id="clearImage" class="clearImage" onclick="clearImage()">&times;</span>
            </div>

            <label for="ItemName">Item Name:</label>
            <input type="text" id="ItemName" name="ItemName" required />

            <label for="ItemLongDesc">Long Description:</label>
            <textarea id="ItemLongDesc" name="ItemLongDesc" required></textarea>

            <label for="ItemCategory">Category:</label>
            <input type="text" id="ItemCategory" name="ItemCategory" required />

            <div class="form-row">
                <div>
                    <label for="ItemBrand">Brand:</label>
                    <input type="text" id="ItemBrand" name="ItemBrand" required />
                </div>
                <div>
                    <label for="ItemModel">Model:</label>
                    <input type="text" id="ItemModel" name="ItemModel" required />
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="ReorderLevel">Reorder Level:</label>
                    <input type="text" id="ReorderLevel" name="ReorderLevel" required />
                </div>
            </div>

            <button type="submit" class="btn-default">Save</button>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Item</h2>

        <form id="editProductForm" enctype="multipart/form-data">
            <input type="hidden" id="editItemId" name="editItemId" />

            <label for="editProductImageUpload">Upload Image:</label>
            <input type="file" id="editProductImageUpload" name="ItemImageUpload" />
            <!-- Corrected name for PHP backend -->

            <div class="modal-image-container">
                <img id="editImagePreview" />
                <span id="editClearImage" class="clearImage" onclick="clearEditImage()">&times;</span>
            </div>

            <label for="editItemName">Item Name:</label>
            <input type="text" id="editItemName" name="editItemName" required />

            <label for="editItemDescription">Item Description:</label>
            <textarea id="editItemDescription" name="editItemDescription" required></textarea>

            <label for="editCategory">Category:</label>
            <input type="text" id="editCategory" name="editCategory" required />

            <div class="form-row">
                <div>
                    <label for="editItemBrand">Brand:</label>
                    <input type="text" id="editItemBrand" name="editItemBrand" required />
                </div>
                <div>
                    <label for="editItemModel">Model:</label>
                    <input type="text" id="editItemModel" name="editItemModel" required />
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="editReorderLevel">Reorder Level:</label>
                    <input type="text" id="editReorderLevel" name="editReorderLevel" required />
                </div>
            </div>

            <button type="submit" class="btn-default">Update</button> <!-- Removed onclick attribute -->
        </form>
    </div>
</div>