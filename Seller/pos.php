<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pharmacy InfoSys - POS</title>

  <link rel="stylesheet" href="Assets/css/shared/main.css">
  <link rel="stylesheet" href="Assets/css/shared/tables.css">
  <link rel="stylesheet" href="Assets/css/pos.css">
</head>

<body>
  <div class="POS-container">
    <div class="POS-header">
      <h2 id="user-name">{{ Name-of-user }}</h2>
      <hr />
    </div>

    <section class="List-of-orders">
      <table class="table-default">
        <thead>
          <tr>
            <th hidden>Stock ID</th>
            <th>Item Name</th>
            <th>Item Description</th>
            <th>Price</th>
            <th>QTY</th>
            <th>Discount (%)</th>
            <th>Total Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dynamic List of Orders here -->
        </tbody>
      </table>
    </section>

    <section class="POS-Add-Order">
      <h3>Order Form</h3>
      <hr />
      <div class="POS-form">
        <form id="addSupplierForm" enctype="multipart/form-data">
          <label for="ItemName">Item Name:</label>
          <input type="text" id="ItemName" name="ItemName" required oninput="setSelectedItemInfo()" />
          <div id="suggestion-box"></div>
        </form>
      </div>
    </section>

    <section class="POS-order-info">
      <div class="POS-form">
        <form id="addSupplierForm" enctype="multipart/form-data">
          <label for="orderTotal">Total:</label>
          <input type="text" id="orderTotal" name="orderTotal" readonly />

          <label for="orderDiscount">Discount (%):</label>
          <input type="text" id="orderDiscount" name="orderDiscount" />

          <label for="orderGrandTotal">Grand Total:</label>
          <input type="text" id="orderGrandTotal" name="orderGrandTotal" readonly />

          <label for="orderPayment">Payment:</label>
          <input type="text" id="orderPayment" name="orderPayment" />

          <label for="orderChange">Change:</label>
          <input type="text" id="orderChange" name="orderChange" readonly />
        </form>
      </div>
    </section>

    <section class="POS-button-container"></section>
  </div>

  <div id="logout-animation">
    <div class="spinner"></div>
    <p id="logout-message">Logging out...</p>
  </div>

  <div id="endShiftModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>End of Shift</h2>
      <form id="endShiftForm" enctype="multipart/form-data">
        <div class="form-row">
          <div>
            <label for="txt1k">1000</label>
            <input type="number" id="txt1k" name="txt1k" required />
          </div>
          <div>
            <label for="txt5H">500</label>
            <input type="number" id="txt5H" name="txt5H" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label for="txt2H">200</label>
            <input type="number" id="txt2H" name="txt2H" required />
          </div>
          <div>
            <label for="txt1H">100</label>
            <input type="number" id="txt1H" name="txt1H" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label for="txt50">50</label>
            <input type="number" id="txt50" name="txt50" required />
          </div>
          <div>
            <label for="txt20">20</label>
            <input type="number" id="txt20" name="txt20" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label for="txt10">10</label>
            <input type="number" id="txt10" name="txt10" required />
          </div>
          <div>
            <label for="txt5">5</label>
            <input type="number" id="txt5" name="txt5" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label for="txt1">1</label>
            <input type="number" id="txt1" name="txt1" required />
          </div>
          <div>
            <label for="txt25c">25c</label>
            <input type="number" id="txt25c" name="txt25c" required />
          </div>
        </div>

        <button type="submit" class="btn-default" id="Submit_ChangeofShip">Submit</button>
      </form>

    </div>
  </div>

  <script src="Assets/js/SweetAlert/sweetalert2.js"></script>
  <script src="Assets/js/pos.js"></script>
  <script src="Assets/js/logout.js"></script>
  <!-- <script src="Assets/js/disabler-dev-tool.js"></script> -->
</body>

</html>