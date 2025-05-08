<div class="employee-container">

    <div class="employee-list">
        <div class="employee-header">
            <h2>Employee</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../reports/All-Employees.php', '_blank', 'width=800,height=600');">Print</button>
                <button class="btn-default" onclick="showModal()">Add</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search employee..."
                oninput="searchEmployee()" />
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
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Employee rows will be loaded here via AJAX -->
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
        <h2>Add New Employee</h2>
        <form id="addEmployeeForm" enctype="multipart/form-data">
            <!-- First Name -->
            <label for="employeeFName">First Name:</label>
            <input type="text" id="employeeFName" name="employeeFName" required />

            <!-- Middle Name -->
            <label for="employeeMName">Middle Name:</label>
            <input type="text" id="employeeMName" name="employeeMName" />

            <!-- Last Name -->
            <label for="employeeLName">Last Name:</label>
            <input type="text" id="employeeLName" name="employeeLName" required />

            <!-- Suffix -->
            <label for="employeeSuffix">Suffix:</label>
            <input type="text" id="employeeSuffix" name="employeeSuffix" />

            <!-- Submit Button -->
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- The Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Employee</h2>
        <form id="editEmployeeForm" enctype="multipart/form-data">
            <input type="hidden" id="editEmployeeID" name="employeeID" />

            <!-- First Name -->
            <label for="editEmployeeFName">First Name:</label>
            <input type="text" id="editEmployeeFName" name="employeeFName" required />

            <!-- Middle Name -->
            <label for="editEmployeeMName">Middle Name:</label>
            <input type="text" id="editEmployeeMName" name="employeeMName" />

            <!-- Last Name -->
            <label for="editEmployeeLName">Last Name:</label>
            <input type="text" id="editEmployeeLName" name="employeeLName" required />

            <!-- Suffix -->
            <label for="editEmployeeSuffix">Suffix:</label>
            <input type="text" id="editEmployeeSuffix" name="employeeSuffix" />

            <!-- Submit Button -->
            <button type="submit" class="btn-default">Update</button>
        </form>
    </div>
</div>