<?php
	require_once __DIR__ . '/config.php';
	require_login();
	$user = current_user();
	$title = 'Dashboard';
	ob_start();
?>

<h1>Dashboard</h1>

<?php if (is_landlord()): ?>
	<div class="row" style="justify-content: space-between; align-items: center;">
		<h2>Your Listings</h2>
		<a class="btn" href="add_property.php">Add New Property</a>
	</div>
	<div class="grid">
		<?php
		$stmt = $conn->prepare("SELECT * FROM properties WHERE landlord_id = ? ORDER BY id DESC");
		$stmt->bind_param("i", $user['id']);
		$stmt->execute();
		$res = $stmt->get_result();
		while ($res && ($row = $res->fetch_assoc())) { $imgs = explode_images($row['images']); ?>
			<div class="card">
				<img src="<?php echo h($imgs[0]); ?>" alt="img" />
				<div class="content">
					<div class="row" style="justify-content: space-between;">
						<div class="price"><?php echo h(price_display($row['price'])); ?></div>
						<div class="muted"><?php echo h($row['purpose']); ?></div>
					</div>
					<div><?php echo h($row['title']); ?></div>
					<div class="row" style="gap:8px; margin-top:8px;">
						<a class="btn ghost" href="edit_property.php?id=<?php echo h($row['id']); ?>">Edit</a>
						<a class="btn danger" href="delete_property.php?id=<?php echo h($row['id']); ?>" onclick="return confirm('Delete this listing?')">Delete</a>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<h2>Inquiries</h2>
	<table class="table">
		<tr><th>From</th><th>Property</th><th>Message</th><th>Date</th><th>Reply</th></tr>
		<?php
		$stmtInq = $conn->prepare("SELECT m.*, u.name as sender_name, p.title as property_title FROM messages m LEFT JOIN users u ON m.sender_id=u.id LEFT JOIN properties p ON m.property_id=p.id WHERE m.receiver_id = ? ORDER BY m.id DESC");
		$stmtInq->bind_param("i", $user['id']);
		$stmtInq->execute();
		$inq = $stmtInq->get_result();
		while ($inq && $m = $inq->fetch_assoc()) {
			echo '<tr>'
				. '<td>'.h($m['sender_name']).'</td>'
				. '<td>'.h($m['property_title']).'</td>'
				. '<td>'.h($m['message']).'</td>'
				. '<td>'.h($m['created_at']).'</td>'
				. '<td>'
					. '<form class="row" method="post" action="send_message.php" style="gap:8px; margin:0;">'
						. '<input type="hidden" name="receiver_id" value="'.h($m['sender_id']).'" />'
						. '<input type="hidden" name="property_id" value="'.h($m['property_id']).'" />'
						. '<input name="message" placeholder="Reply..." style="flex:1; min-width:160px;" />'
						. '<button class="btn" type="submit">Send</button>'
					. '</form>'
				. '</td>'
			. '</tr>';
		}
		?>
	</table>

<?php else: ?>
	<h2>Saved Properties</h2>
	<div class="grid">
	<?php
	$stmtFav = $conn->prepare("SELECT p.* FROM favorites f LEFT JOIN properties p ON f.property_id=p.id WHERE f.user_id = ? ORDER BY f.id DESC");
	$stmtFav->bind_param("i", $user['id']);
	$stmtFav->execute();
	$fav = $stmtFav->get_result();
	while ($fav && ($row = $fav->fetch_assoc())) { $imgs = explode_images($row['images']); ?>
		<a class="card" href="property.php?id=<?php echo h($row['id']); ?>">
			<img src="<?php echo h($imgs[0]); ?>" />
			<div class="content">
				<div class="price"><?php echo h(price_display($row['price'])); ?></div>
				<div><?php echo h($row['title']); ?></div>
			</div>
		</a>
	<?php } ?>
	</div>

	<h2>Inbox</h2>
	<table class="table">
		<tr><th>From</th><th>Property</th><th>Message</th><th>Date</th></tr>
		<?php
		$stmtInbox = $conn->prepare("SELECT m.*, u.name as sender_name, p.title as property_title FROM messages m LEFT JOIN users u ON m.sender_id=u.id LEFT JOIN properties p ON m.property_id=p.id WHERE m.receiver_id = ? ORDER BY m.id DESC");
		$stmtInbox->bind_param("i", $user['id']);
		$stmtInbox->execute();
		$inbox = $stmtInbox->get_result();
		while ($inbox && $m = $inbox->fetch_assoc()) {
			echo '<tr><td>'.h($m['sender_name']).'</td><td>'.h($m['property_title']).'</td><td>'.h($m['message']).'</td><td>'.h($m['created_at']).'</td></tr>';
		}
		?>
	</table>

	<h2>Messages</h2>
	<table class="table">
		<tr><th>To</th><th>Property</th><th>Message</th><th>Date</th></tr>
		<?php
		$stmtMs = $conn->prepare("SELECT m.*, u.name as receiver_name, p.title as property_title FROM messages m LEFT JOIN users u ON m.receiver_id=u.id LEFT JOIN properties p ON m.property_id=p.id WHERE m.sender_id = ? ORDER BY m.id DESC");
		$stmtMs->bind_param("i", $user['id']);
		$stmtMs->execute();
		$ms = $stmtMs->get_result();
		while ($ms && $m = $ms->fetch_assoc()) {
			echo '<tr><td>'.h($m['receiver_name']).'</td><td>'.h($m['property_title']).'</td><td>'.h($m['message']).'</td><td>'.h($m['created_at']).'</td></tr>';
		}
		?>
	</table>
<?php endif; ?>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


