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
SELECT i.ItemName, sii.Qty, sii.Price, ROUND(((sii.Qty * sii.Price) * (sii.Discount / 100)),2) as Discount
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
                    foreach ($orderItems as $item) {
                        $amount = $item['Qty'] * $item['Price'];
                        $formattedAmount = number_format($amount, 2);
                        $formattedPrice = number_format($item['Price'], 2);
                        $discount = 0.00;
                        if ($item['Discount'] > 0) {
                            $discount = number_format($item['Discount'], 2);
                        } else {
                            $discount = "0.00";
                        }
                        ?>
                        <tr>
                            <td colspan="3"> <?php echo $item['ItemName']; ?></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center;"><?php echo $item['Qty']; ?></td>
                            <td style="text-align: right;"><?php echo $formattedPrice; ?></td>
                            <td><?php echo $formattedAmount; ?></td>
                        </tr>
                        
                        <?php if ($discount > 0) { ?>
                            <tr>
                                <td colspan="3" style="text-align: center; font-size: 7pt;"><i>*** less <?php echo $discount; ?> ***</i></td>
                                <td><?php echo number_format($formattedAmount - $discount, 2); ?></td>
                            </tr>
                        <?php } ?>

                        <?php 
                        if ($item['Discount'] > 0) {
                            $amount -= $item['Discount'];
                        }

                        $totalAmount += $amount;
                        
                    }
                    ?>


                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">Sub Total</td>
                        <td><?php echo number_format($totalAmount, 2); ?></td>
                    </tr>


                    <tr style="font-weight: bold; font-size: 10pt;">
                        <td colspan="3">TOTAL DUE</td>
                        <td><?php echo number_format($totalAmount, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Discount</td>
                        <td><?php echo number_format($orderDetails["Discount"], 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">CASH</td>
                        <td><?php echo number_format($orderDetails["CashTendered"], 2); ?></td>
                    </tr>
                    <tr style="font-weight: bold; font-size: 10pt;">
                        <td colspan="3"><strong>CHANGE</strong></td>
                        <td>
                            <?php echo number_format($orderDetails["CashChange"], 2); ?></td>
                    </tr>

                    <tr>
                        <td colspan="3">VATable Sales</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="3">Vat-Exempt Sales</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="3">VAT Zero-Rated Sales</td>
                        <td>0.00</td>
                    </tr>
                    <tr>
                        <td colspan="3">VAT Amount</td>
                        <td>0.00</td>
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
                <tr>
                    <td>Customer Name:</td>
                    <td><?php echo ""; ?></td>
                </tr>
                <tr>
                    <td>Address:</td>
                    <td><?php echo ""; ?></td>
                </tr>
                <tr>
                    <td>TIN:</td>
                    <td><?php echo ""; ?></td>
                </tr>
                <tr>
                    <td>Business Style:</td>
                    <td><?php echo ""; ?></td>
                </tr>
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
        window.onload = function () {
            window.print();
            setTimeout(() => window.close(), 1000);
        };
    </script>
</body>

</html>