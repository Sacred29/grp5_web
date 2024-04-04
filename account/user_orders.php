<?php
session_start();

$userTransactions = [];
$errorMsg = '';
$success = true;

include_once "./../db_config.php";

// Check if user is logged in and get their userID
if ($success && isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];

    // SQL to fetch user transactions and their products
    $sql = "SELECT t.transactionID, t.orderDate, t.totalPrice, 
                   o.productID, o.quantity, o.price,
                   p.productName, p.productImage
            FROM transactionTable t
            LEFT JOIN orderTable o ON t.transactionID = o.transactionID
            LEFT JOIN productTable p ON o.productID = p.productID
            WHERE t.userID = ?
            ORDER BY t.orderDate DESC";

    // Prepare SQL and bind parameters
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $transactionID = $row['transactionID'];
            $userTransactions[$transactionID]['orderDate'] = $row['orderDate'];
            $userTransactions[$transactionID]['totalPrice'] = $row['totalPrice'];
            $userTransactions[$transactionID]['products'][] = [
                'productName' => $row['productName'],
                'productImage' => $row['productImage'],
                'quantity' => $row['quantity'],
                'price' => $row['price']
            ];
        }
        $stmt->close();
    } else {
        $errorMsg = "Unable to prepare statement.";
        $success = false;
    }
} else {
    $errorMsg = "You must be logged in to view your orders.";
    $success = false;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Your Orders</title>
</head>
<body>
    <main class="container">
        <h1>Your Past Transactions</h1>
        <?php if ($success && !empty($userTransactions)): ?>
            <?php foreach ($userTransactions as $transactionID => $transaction): ?>
                <div class="transaction-section">
                    <h3>Transaction ID: <?= htmlspecialchars($transactionID); ?></h3>
                    <p>Order Date: <?= htmlspecialchars($transaction['orderDate']); ?></p>
                    <p>Total Price: $<?= htmlspecialchars(number_format((float)$transaction['totalPrice'], 2)); ?></p>
                    <table class="transactions-table" cellpadding="10" cellspacing="1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_price = 0; ?>
                            <?php foreach ($transaction['products'] as $product): ?>
                                <?php $total_price += $product['price'] * $product['quantity']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['productName']); ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($product['productImage']); ?>" alt="<?= htmlspecialchars($product['productName']); ?>" class="product-image" />
                                    </td>
                                    <td>$<?= htmlspecialchars(number_format((float)$product['price'], 2)); ?></td>
                                    <td><?= htmlspecialchars($product['quantity']); ?></td>
                                    <td>$<?= htmlspecialchars(number_format((float)$product['price'] * $product['quantity'], 2)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="4" align="right">Total:</td>
                                <td align="right">$<?= number_format($total_price, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= $errorMsg; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
