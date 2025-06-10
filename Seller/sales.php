<div class="sales-container">
    <div class="card-container">
        <div class="card">
            <div class="card-header">
                <h2>Top 5 Item sold this Month</h2>
            </div>
            <hr>
            <div class="card-body-number">
                <canvas class="charts" id="trendChart"></canvas>
            </div>
            <!-- <div class="card-footer">Footer</div> -->
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Sales Summary <?php echo date("Y"); ?></h2>
            </div>
            <hr>
            <div class="card-body-number">
                <canvas class="charts-wide" id="salesSummary"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Sales Performance – Past 7 Days</h2>
            </div>
            <hr>
            <div class="card-body-number">
                <canvas class="charts-wide" id="last7DaysSalesChart"></canvas>
            </div>
        </div>
    </div>

    <hr>
    <div class="sales-header">
        <h2>Sales Summary Report</h2>
        <div class="action-buttons">
            <button class="btn-default">Print</button>
        </div>
    </div>
</div>


<script src="../node_modules/chart.js/dist/chart.umd.js"></script>