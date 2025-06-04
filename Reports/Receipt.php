<?php
include $_SERVER['DOCUMENT_ROOT'] . '../Database/connection.php';

date_default_timezone_set('Asia/Manila');

$TransactionNo = isset($_GET['TransactionNo']) ? trim($_GET['TransactionNo']) : '';

// First query to fetch sales invoice details
$sql = "
SELECT st.SINo, st.Amount,  ROUND((st.Amount * (st.Discount / 100)),2) AS Discount, st.DateTimeCreated, emp.Fname, st.CashTendered, st.CashChange 
FROM salestransaction st
INNER JOIN user_account acc ON acc.ID = st.CreatedBy
INNER JOIN lEmployee emp ON acc.EmployeeID = emp.ID
WHERE st.TransactionNo = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $TransactionNo);
$stmt->execute();
$orderDetails = [];
$result = $stmt->get_result();  // Get the result of the first query
if ($result->num_rows > 0) {
    $orderDetails = $result->fetch_assoc();  // Fetch the first row of invoice details
}

// Second query to fetch invoice items
$sql = "
SELECT i.ItemName, sii.Qty, sii.Price, ROUND(((sii.Qty * sii.Price) * (sii.Discount / 100)),2) as Discount, VATType
FROM salestransactionitems sii 
INNER JOIN stocks s ON s.ID = sii.StockID 
INNER JOIN items i ON i.id = s.ItemID
WHERE TransactionNo = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $TransactionNo);
$stmt->execute();
$orderItems = [];
$result = $stmt->get_result();  // Get the result of the second query
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;  // Fetch each row of items and add to the array
    }
}

$conn->close();


$totalAmount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Receipt</title>
    <link rel="stylesheet" href="css/receipt.css">
</head>

<body>
    <div id="receipt" class="receipt">
        <div class="pos-details">
            <table>
                <thead>
                    <tr>
                        <td>SCIENCE BIOTECH SPECIALTIES, INC.</td>
                    </tr>
                    <tr>
                        <td>
                            <address>6023 Sacred Heart, Cor Kamagong</address>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <address>Makati, 1203 Metro Manila</address>
                        </td>
                    </tr>
                    <tr>
                        <td>TIN: 201-841-917-0000</td>
                    </tr>
                    <tr>
                        <td>Accreditation#: 000-000000000-000000000</td>
                    </tr>
                    <tr>
                        <td>Date Issued: ' . date("F j, Y") . '</td>
                    </tr>
                    <tr>
                        <td>Final PTU#: FPMM' . date('Y') . '-000-0000000-00000</td>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="sales-invoice-title">
            <span>SALES INVOICE</span>
        </div>

        <table class="receipt-data">
            <tr>
                <td>MIN No</td>
                <td>:</td>
                <td>00000000000000000</td>
            </tr>
            <tr>
                <td>Terminal</td>
                <td>:</td>
                <td>SBSI POS1</td>
            </tr>
            <tr>
                <td>POS Serial No</td>
                <td>:</td>
                <td>00000000000000000</td>
            </tr>
            <tr>
                <td>Location</td>
                <td>:</td>
                <td>Makati City</td>
            </tr>
            <tr>
                <td>Date/Time</td>
                <td>:</td>
                <td><?php echo date("Y-m-d H:i:s") ?></td>
            </tr>
            <tr>
                <td>Cashier</td>
                <td>:</td>
                <td><?php echo $orderDetails["Fname"]; ?></td>
            </tr>
            <tr>
                <td>Transaction No</td>
                <td>:</td>
                <td><?php echo $TransactionNo; ?></td>
            </tr>
            <tr>
                <td>Invoice No</td>
                <td>:</td>
                <td><?php echo $orderDetails["SINo"]; ?></td>
            </tr>
        </table>

        <div class="receipt-items" id="receipt-items">
            <table class="receipt-item">
                <thead>
                    <tr>
                        <td><strong>Item</strong></td>
                        <td style="text-align: center;"><strong>Qty</strong></td>
                        <td style="text-align: right;"><strong>Price</strong></td>
                        <td style="text-align: right;"><strong>Amount</strong></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $vatableSales = 0;
                    $vatExemptSales = 0;
                    $vatZeroRatedSales = 0;
                    $totalAmount = 0;

                    foreach ($orderItems as $item) {
                        $amount = $item['Qty'] * $item['Price'];
                        $discount = ($item['Discount'] > 0) ? $item['Discount'] : 0.00;
                        $netAmount = $amount - $discount;

                        switch ($item['VATType']) {
                            case 'VATable':
                                $vatableSales += $netAmount / 1.12;
                                break;
                            case 'VATExempt':
                                $vatExemptSales += $netAmount;
                                break;
                            case 'VATZeroRated':
                                $vatZeroRatedSales += $netAmount;
                                break;
                        }

                        $totalAmount += $netAmount;
                    ?>
                        <tr>
                            <td colspan="3"><?php echo $item['ItemName']; ?></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center;"><?php echo $item['Qty']; ?></td>
                            <td style="text-align: right;"><?php echo number_format($item['Price'], 2); ?></td>
                            <td><?php echo number_format($amount, 2); ?></td>
                        </tr>
                        <?php if ($discount > 0) { ?>
                            <tr>
                                <td colspan="3" style="text-align: center; font-size: 7pt;"><i>*** less <?php echo number_format($discount, 2); ?> ***</i></td>
                                <td><?php echo number_format($netAmount, 2); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>

                    <?php
                    // Apply transaction-level discount proportionally
                    $transactionDiscount = $orderDetails['Discount'];
                    $grossTotal = $vatableSales + $vatExemptSales + $vatZeroRatedSales;

                    if ($grossTotal > 0 && $transactionDiscount > 0) {
                        $ratio = $transactionDiscount / $grossTotal;
                        $vatableSales -= $vatableSales * $ratio;
                        $vatExemptSales -= $vatExemptSales * $ratio;
                        $vatZeroRatedSales -= $vatZeroRatedSales * $ratio;
                    }

                    // Final VAT computation
                    $vatAmount = $vatableSales * 0.12;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Sub Total</td>
                        <td><?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Discount</td>
                        <td><?php echo number_format($transactionDiscount, 2); ?></td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="3">TOTAL DUE</td>
                        <td><?php echo number_format($totalAmount - $transactionDiscount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">CASH</td>
                        <td><?php echo number_format($orderDetails["CashTendered"], 2); ?></td>
                    </tr>
                    <tr style="font-weight: bold;">
                        <td colspan="3">CHANGE</td>
                        <td><?php echo number_format($orderDetails["CashChange"], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">VATable Sales</td>
                        <td><?php echo number_format($vatableSales, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Vat-Exempt Sales</td>
                        <td><?php echo number_format($vatExemptSales, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">VAT Zero-Rated Sales</td>
                        <td><?php echo number_format($vatZeroRatedSales, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">VAT Amount</td>
                        <td><?php echo number_format($vatAmount, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="receipt-footer">
            <p><?php echo count($orderItems); ?> Item(s).</p>
            <p>Thank you for your purchase!</p>
            <p class="receipt-note">
                This serves as your Sales Invoice and Proof of Payment
            </p>
        </div>

        <div class="cx-data">
            <table>
                <!-- AUTO GENERATE CUSTOMER INFORMATION HERE -->
            </table>
        </div>

        <div class="pos-details">
            <table>
                <thead>
                    <tr style="height: 30px">
                        <td>
                            <strong>POS PROVIDER</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            SCIENTIFIC BIOTECH SPECIALTIES, INC.
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <address>6023 Sacred Heart, Cor Kamagong</address>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <address>Makati, 1203 Metro Manila</address>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            TIN: 201-841-917-0000
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Accreditation#: 000-000000000-000000000
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Date Issued: MMMM dd, YYYY
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Final PTU#: FPMMYYYY-000-0000000-00000
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <script>
        window.onload = function() {

            const customerInfo = JSON.parse(localStorage.getItem("customerInfo")) || {};

            const cxData = document.querySelector('.cx-data table');
            cxData.innerHTML = `
                <tr>
                    <td>Customer Name:</td>
                    <td>${customerInfo.name || ''}</td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td>${customerInfo.address || ''}</td>
                </tr>
                <tr>
                    <td>TIN:</td>
                    <td>${customerInfo.tin || ''}</td>  
                </tr>
                <tr>
                    <td>Business Style:</td>
                    <td>${customerInfo.businessType || ''}</td>
                </tr>`;

            window.print();
            setTimeout(() => {
                // Clear Customer data from localStorage
                localStorage.removeItem("customerInfo");
                // Close the window after printing
                window.close();
            }, 1000);
        };
    </script>
</body>

</html>