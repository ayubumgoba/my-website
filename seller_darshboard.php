<?php
session_start();
if(!isset($_SESSION['username'])){
    header('Location: login.php');
    exit;
}
include 'connection.php';

// Seller anaweza kusasisha status ya order (mfano: Kupo -> Imethibitishwa / Imesafirishwa / Imefika)
if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// Pata orders zote kutoka kwa wateja
$all_orders = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
?>
<!DOCTYPE html>
<html lang="sw">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Dashboard - PERTUDONA COMPANY</title>
<style>
body{font-family:Arial,sans-serif;background:#eef2f3;margin:0;padding:0;}
header{background:#0077cc;color:white;padding:15px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 5px rgba(0,0,0,0.1);}
.company-name{font-size:28px;font-weight:bold;flex:1;text-align:center;letter-spacing:2px;}
.logout{color:white;text-decoration:none;font-weight:bold;}
.container{padding:20px;}
.card{background:white;padding:20px;border-radius:10px;box-shadow:0 5px 15px rgba(0,0,0,0.1);margin-bottom:20px;}
h2{margin-top:0;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
table, th, td{border:1px solid #ccc;}
th, td{padding:10px;text-align:left;}
th{background:#0077cc;color:white;}
form select, form button{padding:5px;border-radius:5px;}
form button{background:#0077cc;color:white;border:none;cursor:pointer;}
form button:hover{background:#005fa3;}
</style>
</head>
<body>
<header>
<div class="company-name">PERTUDONA COMPANY</div>
<a href="logout.php" class="logout">Logout</a>
</header>

<div class="container">
<div class="card">
<h2>Karibu Seller, <?php echo $_SESSION['username']; ?></h2>
<p>Hapa unaweza kuona order zote kutoka kwa wateja wako na kusasisha status yake.</p>
</div>

<div class="card">
<h3>Orodha ya Orders Zote</h3>
<table>
<tr>
<th>ID</th>
<th>Mteja</th>
<th>Kiasi</th>
<th>Payment Method</th>
<th>Status</th>
<th>Tarehe ya Kuagiza</th>
<th>Sasisha</th>
</tr>
<?php while($row = $all_orders->fetch_assoc()): ?>
<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['client_name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['status']; ?></td>
<td><?php echo $row['order_date']; ?></td>
<td>
    <form method="POST" style="display:flex;gap:5px;">
        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
        <select name="status">
            <option value="Kupo" <?php if($row['status']=="Kupo") echo "selected"; ?>>Kupo</option>
            <option value="Imethibitishwa" <?php if($row['status']=="Imethibitishwa") echo "selected"; ?>>Imethibitishwa</option>
            <option value="Imesafirishwa" <?php if($row['status']=="Imesafirishwa") echo "selected"; ?>>Imesafirishwa</option>
            <option value="Imefika" <?php if($row['status']=="Imefika") echo "selected"; ?>>Imefika</option>
        </select>
        <button type="submit" name="update_status">Sasisha</button>
    </form>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>