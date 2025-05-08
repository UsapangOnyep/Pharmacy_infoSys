<div class="account-container">

    <div class="account-list">
        <div class="account-header">
            <h2>Account</h2>
            <div class="action-buttons">
                <button class="btn-default"
                    onclick="window.open('../reports/All-Accounts.php', '_blank', 'width=800,height=600');">Print</button>
                <button class="btn-default" onclick="showModal()">Add</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" id="searchInput" class="search-input" placeholder="Search account..."
                oninput="searchaccount()" />
            <button class="btn-search">
                <img src="Assets/img/icons/search.png" alt="">
            </button>
        </div>

        <hr>

        <table class="table-default">
            <thead>
                <tr>
                    <th hidden>ID</th>
                    <th>Full Name<thh>
                    <th>Username</th>
                    <th>Account Type</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- account rows will be loaded here via AJAX -->
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

<!-- The Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Account</h2>
        <form id="addaccountForm" enctype="multipart/form-data">
            <!-- Employee Name -->
            <label for="accountName">Employee Name:</label>
            <select id="accountName" name="accountName" required>
                <option value="">Select an Employee</option>
                <!-- Employee options here -->
            </select>

            <!-- Email -->
            <label for="accountEmail">Email:</label>
            <input type="email" id="accountEmail" name="accountEmail" required />

            <!-- Position -->
            <label for="accountPosition">Position:</label>
            <input type="text" id="accountPosition" name="accountPosition" required />

            <!-- Username -->
            <label for="accountUsername">Username:</label>
            <input type="text" id="accountUsername" name="accountUsername" required />

            <!-- Password -->
            <label for="accountPassword">Password:</label>
            <input type="password" id="accountPassword" name="accountPassword" required />

            <!-- Confirm Password -->
            <label for="accountConfirmPassword">Confirm Password:</label>
            <input type="password" id="accountConfirmPassword" name="accountConfirmPassword" required />

            <!-- Account Type -->
            <label for="accountType">Account Type:</label>
            <select id="accountType" name="accountType" required>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="btn-default">Save</button>
        </form>

    </div>
</div>

<!-- The Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Account</h2>
        <form id="editaccountForm" enctype="multipart/form-data">
            <input type="hidden" id="editaccountID" name="accountID" />

            <!-- Employee Name (Read-Only) -->
            <label for="editaccountName">Employee Name:</label>
            <input type="text" id="editaccountName" name="accountName" required readonly class="readonly-field"/>

            <!-- Email (Editable) -->
            <label for="editaccountEmail">Email:</label>
            <input type="email" id="editaccountEmail" name="accountEmail" required class="editable-field"/>

            <!-- Position (Editable) -->
            <label for="editaccountPosition">Position:</label>
            <input type="text" id="editaccountPosition" name="accountPosition" required class="editable-field"/>

            <!-- Username (Read-Only) -->
            <label for="editaccountUsername">Username:</label>
            <input type="text" id="editaccountUsername" name="accountUsername" required readonly class="readonly-field"/>

            <!-- Account Type (Editable) -->
            <label for="editaccountType">Account Type:</label>
            <select id="editaccountType" name="accountType" required class="editable-field">
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

            <!-- Status (Editable) -->
            <label for="editaccountStatus">Status:</label>
            <select id="editaccountStatus" name="accountStatus" required class="editable-field">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>

            <!-- Submit Button -->
            <button type="submit" class="btn-default">Update</button>
        </form>
    </div>
</div>
