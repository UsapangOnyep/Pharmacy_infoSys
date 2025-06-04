<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';
date_default_timezone_set('Asia/Manila');

$AccountID = isset($_GET['AccountID']) ? trim($_GET['AccountID']) : '';
$ShiftNumber = 1;

$sql = "CALL xReport(?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $AccountID, $ShiftNumber);
$stmt->execute();

$reportData = [];
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $reportData = $result->fetch_assoc();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>X Report</title>
    <link rel="stylesheet" href="css/receipt.css">
</head>
<body>
    <div id="receipt" class="receipt">
        <div class="sales-invoice-title">
            <span>X Report – Interim Cash Report</span>
        </div>

        <div class="logo">
            <img src="img/logo.png" alt="logo">
        </div>

        <table class="report-table">
            <tr>
                <td>Register</td>
                <td><?php echo $reportData["CashierName"]; ?></td>
            </tr>
            <tr>
                <td>Report Date</td>
                <td><?php echo $reportData["ReportDate"]; ?></td>
            </tr>
            <tr>
                <td>Opening Balance</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
            <tr>
                <td>Opened At</td>
                <td></td>
            </tr>
            <tr>
                <td>Opened By</td>
                <td><?php echo $reportData["CashierName"]; ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Tendered Payments</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Cash</td>
                <td><?php echo number_format($reportData["CashTentered"], 2); ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo number_format($reportData["CashTentered"], 2); ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Refund Payments</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Total</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Net Payments</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Cash</td>
                <td><?php echo number_format($reportData["CashTentered"], 2); ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo number_format($reportData["CashTentered"], 2); ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Taxes Report</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Exempt</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Expected Counts</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Cash</td>
                <td><?php echo number_format($reportData["ExpectedCounts"], 2); ?></td>
            </tr>
            <tr>
                <td>Total</td>
                <td><?php echo number_format($reportData["ExpectedCounts"], 2); ?></td>
            </tr>
        </table>

        <div class="sales-invoice-title">
            <span>Stats</span>
        </div>

        <table class="report-table">
            <tr>
                <td>Total Transactions</td>
                <td><?php echo number_format($reportData["TotalTransaction"], 0); ?></td>
            </tr>
            <tr>
                <td>Paid Invoices</td>
                <td><?php echo number_format($reportData["ExpectedCounts"], 2); ?></td>
            </tr>
            <tr>
                <td>Partial Invoices</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
            <tr>
                <td>Returns</td>
                <td><?php echo number_format(0, 2); ?></td>
            </tr>
        </table>

        
    </div>
</body>
</html>
<script>
    window.print();
</script>