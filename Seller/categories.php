<div class="category-container">

    <div class="category-list">
        <div class="categories-header">
            <h2>Categories</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../reports/All-Categories.php', '_blank', 'width=800,height=600');">Print</button>
                <button class="btn-default" onclick="showModal()">Add</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search category..."
                oninput="searchcategory()" />
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
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- category rows will be loaded here via AJAX -->
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
        <h2>Add New Cagegory</h2>
        <form id="addCategoryForm" enctype="multipart/form-data">
            <input type="hidden" id="ID" name="ID" />

            <!-- Description -->
            <label for="CategoryName">Category Name:</label>
            <input type="text" id="CategoryName" name="CategoryName" required />

            <!-- Submit Button -->
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- The Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Category</h2>
        <form id="editCategoryForm" enctype="multipart/form-data">
            <input type="hidden" id="editID" name="editID" /> <!-- Hidden ID for updating -->

            <!-- Description -->
            <label for="editCategoryName">Category Name:</label>
            <input type="text" id="editCategoryName" name="editCategoryName" required /> <!-- Name field -->

            <!-- Submit Button -->
            <button type="submit" class="btn-default">Update</button>
        </form>
    </div>
</div>
