
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PRICING CHART</title>
  <style>
    body {
      margin: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      background-color: #ffffff;
    }

    nav {
      background-color: #3f3d40;
      padding: 10px 20px;
      display: flex;
      justify-content: flex-start;
      align-items: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      margin-right: 20px;
      font-weight: bold;
      font-family: sans-serif;
    }

    nav a:hover {
      text-decoration: underline;
    }

    .table-container {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    table {
      border-collapse: collapse;
      border: 2px solid rgb(79, 22, 122);
      font-family: sans-serif;
      font-size: 0.8rem;
      letter-spacing: 1px;
    }

    caption {
      caption-side: bottom;
      padding: 10px;
      font-weight: bold;
    }

    thead,
    tfoot {
      background-color: rgb(176, 194, 202);
    }

    th,
    td {
      border: 1px solid rgb(131, 25, 25);
      padding: 8px 10px;
    }

    td:last-of-type {
      text-align: center;
    }

    tbody > tr:nth-of-type(even) {
      background-color: rgb(146, 197, 184);
    }

    tbody > tr:nth-of-type(odd) {
      background-color: rgb(240, 238, 225);
    }

    tfoot th {
      text-align: right;
    }

    tfoot td {
      font-weight: bold;
    }
  </style>
</head>
<body>

  <nav>
    <a href="index.php">Home</a>
    <a  onclick="history.back()">Return</a>
  </nav>

  <div class="table-container">
    <table>
      <caption>© PARKING ALLOCATION BY R~S</caption>
      <thead>
        <tr>
          <th scope="col">VEHICLE</th>
          <th scope="col">COST PER HOUR</th>
          <th scope="col">COST PER DAY</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">BIKE</th>
          <td style="text-align: center;">10</td>
          <td>50</td>
        </tr>
        <tr>
          <th scope="row">E RICKSHAW</th>
          <td style="text-align: center;">10</td>
          <td>70</td>
        </tr>
        <tr>
          <th scope="row">CAR</th>
          <td style="text-align: center;">20</td>
          <td>100</td>
        </tr>
      </tbody>
    </table>
  </div>

</body>
</html>
