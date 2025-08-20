<?php
	require_once __DIR__ . '/config.php';
	require_login();
	if (!is_admin()) { header('Location: index.php'); exit; }
	$title = 'Admin Panel';

	if (isset($_GET['approve'])) {
		$id = (int)$_GET['approve'];
		$stmt = $conn->prepare("UPDATE properties SET approved = 1 WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		header('Location: admin.php'); exit;
	}
	if (isset($_GET['remove'])) {
		$id = (int)$_GET['remove'];
		$stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		header('Location: admin.php'); exit;
	}

	ob_start();
?>

<h1>Admin Panel</h1>

<h2>Users</h2>
<table class="table">
	<tr><th>Name</th><th>Email</th><th>Role</th></tr>
	<?php $stmtUsers = $conn->prepare("SELECT * FROM users ORDER BY id DESC"); $stmtUsers->execute(); $users = $stmtUsers->get_result(); while ($users && $u = $users->fetch_assoc()) {
		echo '<tr><td>'.h($u['name']).'</td><td>'.h($u['email']).'</td><td>'.h($u['role']).'</td></tr>';
	} ?>
</table>

<h2>Pending Listings</h2>
<table class="table">
	<tr><th>Title</th><th>Landlord</th><th>Price</th><th>Actions</th></tr>
	<?php $stmtPending = $conn->prepare("SELECT p.*, u.name as landlord FROM properties p LEFT JOIN users u ON p.landlord_id=u.id WHERE IFNULL(p.approved,0)=0 ORDER BY p.id DESC");
	$stmtPending->execute(); $pending = $stmtPending->get_result();
	while ($pending && $p = $pending->fetch_assoc()) {
		echo '<tr><td>'.h($p['title']).'</td><td>'.h($p['landlord']).'</td><td>'.h(price_display($p['price'])).'</td><td><a class="btn" href="admin.php?approve='.(int)$p['id'].'">Approve</a> <a class="btn danger" href="admin.php?remove='.(int)$p['id'].'" onclick="return confirm(\'Remove listing?\')">Remove</a></td></tr>';
	}
	?>
</table>

<h2>Analytics</h2>
<div class="grid">
	<div class="card"><div class="content"><div class="muted">Total Users</div><div style="font-size:28px; font-weight:800;">
		<?php $stmtC1 = $conn->prepare("SELECT COUNT(*) c FROM users"); $stmtC1->execute(); echo (int)$stmtC1->get_result()->fetch_assoc()['c']; ?></div></div></div>
	<div class="card"><div class="content"><div class="muted">Total Listings</div><div style="font-size:28px; font-weight:800;">
		<?php $stmtC2 = $conn->prepare("SELECT COUNT(*) c FROM properties"); $stmtC2->execute(); echo (int)$stmtC2->get_result()->fetch_assoc()['c']; ?></div></div></div>
	<div class="card"><div class="content"><div class="muted">Messages Sent</div><div style="font-size:28px; font-weight:800;">
		<?php $stmtC3 = $conn->prepare("SELECT COUNT(*) c FROM messages"); $stmtC3->execute(); echo (int)$stmtC3->get_result()->fetch_assoc()['c']; ?></div></div></div>
</div>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


