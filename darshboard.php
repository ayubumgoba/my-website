<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location: login.php');
    exit;
}
include 'connection.php';

$message = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $client_name = $_SESSION['username'];
    $quantity = $_POST['quantity'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';

    if($quantity>0 && $payment_method){
        $stmt = $conn->prepare("INSERT INTO orders (client_name, quantity, payment_method, status, order_date) VALUES (?, ?, ?, 'Kupo', NOW())");
        $stmt->bind_param("sis", $client_name, $quantity, $payment_method);
        if($stmt->execute()){
            $message = "Order yako imefika kikamilifu!";
        } else {
            $message = "Kuna tatizo, jaribu tena.";
        }
        $stmt->close();
    } else {
        $message = "Tafadhali jaza fomu yote!";
    }
}

// Retrieve orders for current user
$user_orders = $conn->prepare("SELECT * FROM orders WHERE client_name=? ORDER BY order_date DESC");
$user_orders->bind_param("s", $_SESSION['username']);
$user_orders->execute();
$result = $user_orders->get_result();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard - PERTUDONA COMPANY</title>
<style>
body{font-family:Arial,sans-serif;background:#eef2f3;margin:0;padding:0;}
header{background:#0077cc;color:white;padding:15px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
.company-name{font-size:28px;font-weight:bold;flex:1;text-align:center;letter-spacing:2px;}
.logout{color:white;text-decoration:none;font-weight:bold;}
.container{padding:20px;}
.card{background:white;padding:20px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);margin-bottom:20px;}
form input, form select, form button{width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ccc;}
form button{background:#0077cc;color:white;border:none;cursor:pointer;}
form button:hover{background:#005fa3;}
.message{color:green;font-weight:bold;margin-bottom:15px;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
table, th, td{border:1px solid #ccc;}
th, td{padding:10px;text-align:left;}
th{background:#0077cc;color:white;}
</style>
</head>
<body>
<header>
<div class="company-name">PERTUDONA COMPANY</div>
<a href="logout.php" class="logout">Logout</a>
</header>

<div class="container">
<div class="card">
<h2>Karibu, <?php echo $_SESSION['username']; ?></h2>
<p>Hapa unaweza kuagiza bidhaa na kuona order zako.</p>
</div>

<div class="card">
<h3>Fomu ya Kuagiza</h3>
<div class="message"><?php echo $message; ?></div>
<form method="POST">
<label>Kiasi / Quantity</label>
<input type="number" name="quantity" min="1" required>

<label>Njia ya Malipo</label>
<select name="payment_method" required>
<option value="">--Chagua--</option>
<option value="Cash">Cash</option>
<option value="Mobile Money">Mobile Money</option>
<option value="Bank Transfer">Bank Transfer</option>
</select>

<button type="submit">Weka Order</button>
</form>
</div>

<div class="card">
<h3>Orders Zako</h3>
<table>
<tr>
<th>ID</th>
<th>Kiasi</th>
<th>Payment Method</th>
<th>Status</th>
<th>Tarehe ya Kuagiza</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['order_date']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>