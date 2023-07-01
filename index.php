<?php

ini_set('display_errors', 1);

require_once('db/database-handler.php');
require_once('db/repository.php');

$databaseHandler = new DatabaseHandler();
$repository = new Repository();

$customers = $repository->getCustomers();

$products = $repository->getProducts();

// Initialize variables to store form input values
$selectedCustomer = $_POST['customer'] ?? '';
$selectedProduct = $_POST['product'] ?? '';
$enteredPrice = $_POST['price'] ?? '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filteredSales = $repository->getFilteredSales($_POST);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .total-row {
            font-weight: bold;
        }

        select, input[type="text"] {
            padding: 5px;
            width: 40%;
        }
    </style>
</head>
<body>
    <h1>Sales Reporter</h1>

    <form method="POST" action="">
        <label for="customer">Customer:</label><br>
        <select id="customer" name="customer" required>
            <option value="">-- Select Customer --</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?php echo $customer['id']; ?>"
                    <?php if ($selectedCustomer == $customer['id']) echo 'selected'; ?>>
                    <?php echo $customer['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="product">Product:</label><br>
        <select id="product" name="product" required>
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $product): ?>
                <option value="<?php echo $product['id']; ?>"
                    <?php if ($selectedProduct == $product['id']) echo 'selected'; ?>>
                    <?php echo $product['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label for="price">Price:</label><br>
        <input type="text" id="price" name="price" value="<?php echo $enteredPrice; ?>" required>

        <br><br>

        <input type="submit" value="Filter">
    </form>

    <?php if (isset($filteredSales)): ?>
        <h2>Filtered Results:</h2>
        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filteredSales as $sale): ?>
                    <tr>
                        <td><?php echo $sale['id']; ?></td>
                        <td><?php echo $sale['customer_name']; ?></td>
                        <td><?php echo $sale['product_name']; ?></td>
                        <td><?php echo $sale['price']; ?></td>
                        <td><?php echo $sale['sale_date']; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3"><b>Total Price</b></td>
                    <td colspan="2"><?php echo array_sum(array_column($filteredSales, 'price')); ?></td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
