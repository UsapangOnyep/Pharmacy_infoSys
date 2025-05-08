<div class="stocks-disposal-container">

    <div class="stocks-disposal-list">
        <div class="stocks-disposal-header">
            <h2>Inventory / Stocks / Disposal</h2>
            <div class="action-buttons">
                <button class="btn-default" id="Submit">Save</button>
                <button class="btn-default"
                    onclick="window.location.href='?page=stocks';">Cancel</button>
            </div>
        </div>
        <hr>
        <div class="table-container">
            <table class="table-default">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Item Description</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Expiry Date</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <form>
                    <label for="disposalReason">Disposal Reason: </label>
                    <select id ="disposalReason" class="form-control" onchange="onDisposalReasonChange()" required>
                        <option value="" disabled selected>Select Disposal Reason</option>
                        <option value="Expired">Expired</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Lost">Lost</option>
                    </select>

                    <tbody id="tableBody">
                        <!-- inventory rows will be loaded here via AJAX -->
                    </tbody>

                </form>
            </table>
            <button class="btn-default" id="AddRow">Add Row</button>
        </div>
    </div>
</div>