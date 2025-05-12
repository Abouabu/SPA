<!DOCTYPE html>
<html>
<head>
<title>Invoice</title>
<style>
  body {
    font-family: sans-serif;
  }
  .invoice-container {
    width: 80%;
    margin: 50px auto;
    border: 1px solid #ddd;
    padding: 20px;
  }
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }
  .logo {
    max-width: 200px;
  }
  .details {
    text-align: right;
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
  }
  .total {
    text-align: right;
    font-weight: bold;
    margin-top: 20px;
  }
</style>
</head>
<body>
  <div class="invoice-container">
    <div class="header">
    <img src="images/logo.png" alt="Beauty Saloon Logo" class="logo">

      <div class="details">
        <h2>Beauty Book</h2>
        <p>Mombasa, Kenya</p>
        <p>Invoice Number: 12345</p>
        <p>Invoice Date: 2025-03-10</p>
      </div>
    </div>

    <h2>Invoice</h2>

    <p>Bill to:</p>
    <p>Jane Doe</p>
    <p>Mombasa, Kenya</p>

    <table>
      <thead>
        <tr>
          <th>Description</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Haircut</td>
          <td>KSh 1000</td>
        </tr>
        <tr>
          <td>Hair Coloring</td>
          <td>KSh 2500</td>
        </tr>
        <tr>
          <td>Manicure</td>
          <td>KSh 1500</td>
        </tr>
      </tbody>
    </table>

    <div class="total">Total: KSh 5000</div>

    <p>Thank you for your business!</p>
  </div>
</body>
</html>