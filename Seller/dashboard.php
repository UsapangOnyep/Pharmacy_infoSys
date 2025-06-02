<!-- Example HTML fragment -->
<div class="employee-container">

    <div class="card-container">
        <div class="card">
            <div class="card-header">
                <h2>Total Sales Today</h2>
            </div>
            <hr>
            <div class="card-body-number" id="incomeToday">loading . . .</div>
            <!-- <div class="card-footer">Footer</div> -->
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Total Sales This Month</h2>
            </div>
            <hr>
            <div class="card-body-number" id="incomeThisMonth">loading . . .</div>
            <!-- <div class="card-footer">Footer</div> -->
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Top Item Last Month</h2>
            </div>
            <hr>
            <div class="card-body" id="topItem">loading . . .</div>
            <div class="card-footer" id="topItemFooter">No Data</div>
        </div>
    </div>

    <div class="card-container">
        <div class="card-by2">
            <div class="card-header">
                <h2>List of Trend This Month</h2>
            </div>
            <hr>
            <div class="table-container" id="TrendThisMonth">
                <table class="table-default">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th style="text-align:right;">Total Dispensed Quantity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- TrendThisMonth rows will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-by2">
            <div class="card-header">
                <h2>List of Low Stock Items</h2>
            </div>
            <hr>
            <div class="table-container" id="lowStockItems">
                <table class="table-default">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th style="text-align:right;">Current Stock</th>
                        <th style="text-align:right;">Reorder Level</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- lowStockItems rows will be loaded here via AJAX -->
                </tbody>
            </table>
            </div>
        </div>

        <div class="card-by2">
            <div class="card-header">
                <h2>List of Near Expiry Items</h2>
            </div>
            <hr>
            <div class="table-container" id="nearExpiryItems">
                <table class="table-default">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th style="text-align:center;">Expiry Date</th>
                        <th style="text-align:right;">Quantity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- nearExpiryItems rows will be loaded here via AJAX -->
                </tbody>
            </table>
            </div>
        </div>
        <div class="card-by2">
            <div class="card-header">
                <h2>List of Expired Items</h2>
            </div>
            <hr>
            <div class="table-container" id="expiredItems">
                <table class="table-default">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th style="text-align:center;">Expiry Date</th>
                        <th style="text-align:right;">Quantity</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- expiredItems rows will be loaded here via AJAX -->
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>