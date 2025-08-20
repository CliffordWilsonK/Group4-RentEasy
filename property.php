<?php
	require_once __DIR__ . '/config.php';
	if (empty($_GET['id'])) { header('Location: listings.php'); exit; }
	$property = get_property_by_id((int)$_GET['id']);
	if ($property && (int)$property['approved'] !== 1) {
		if (!(is_admin() || (is_logged_in() && (int)$property['landlord_id'] === (int)current_user()['id']))) {
			header('Location: listings.php');
			exit;
		}
	}
	if (!$property) { header('Location: listings.php'); exit; }
	$title = h($property['title']) . ' | RentEasy';
	ob_start();
?>

<div class="property-layout">
	<section>
		<h1><?php echo h($property['title']); ?></h1>
		<div class="row" style="justify-content: space-between;">
			<div class="price"><?php echo h(price_display($property['price'])); ?> - <?php echo h($property['purpose']); ?></div>
			<div class="badge"><?php echo h($property['type']); ?></div>
		</div>
		<div class="muted" style="margin:8px 0;">Location: <?php echo h($property['location']); ?></div>
		<?php $images = explode_images($property['images']); ?>
		<div class="gallery">
			<?php foreach ($images as $img): ?>
				<img src="<?php echo h($img); ?>" alt="image" />
			<?php endforeach; ?>
		</div>
		<div class="property-meta" style="margin-top: 12px;">
			<div class="badge">Bedrooms: <?php echo h($property['bedrooms']); ?></div>
			<div class="badge">Bathrooms: <?php echo h($property['bathrooms']); ?></div>
			<div class="badge">Size: <?php echo h($property['size_sqft']); ?> sqft</div>
			<div class="badge">Furnished: <?php echo h($property['furnished']); ?></div>
		</div>
		<h3>Description</h3>
		<p><?php echo nl2br(h($property['description'])); ?></p>

		<h3>Location Map</h3>
		<div style="border-radius:12px; overflow:hidden;">
			<iframe
				width="100%"
				height="300"
				frameborder="0" style="border:0"
				src="https://www.google.com/maps?q=<?php echo urlencode($property['location']); ?>&output=embed" allowfullscreen>
			</iframe>
		</div>
	</section>

	<aside class="sidebar">
		<h3>Contact Landlord</h3>
		<div class="stack">
			<div><strong><?php echo h($property['landlord_name']); ?></strong></div>
			<div class="muted"><?php echo h($property['landlord_email']); ?></div>
			<?php if (is_logged_in()): ?>
				<form class="stack" method="post" action="send_message.php">
					<input type="hidden" name="property_id" value="<?php echo h($property['id']); ?>" />
					<input type="hidden" name="receiver_id" value="<?php echo h($property['landlord_id']); ?>" />
					<textarea name="message" placeholder="Write your message" style="min-height: 100px; padding: 10px; border-radius: 8px; border:1px solid #d1d5db;"></textarea>
					<button class="btn" type="submit">Send Message</button>
				</form>
				<form method="post" action="toggle_favorite.php">
					<input type="hidden" name="property_id" value="<?php echo h($property['id']); ?>" />
					<button class="btn ghost" type="submit">Save to Favorites</button>
				</form>
			<?php else: ?>
				<a class="btn" href="login.php">Login to message</a>
			<?php endif; ?>
		</div>
	</aside>
</div>

<section class="section">
	<h3>Similar Properties</h3>
	<div class="grid">
		<?php
		$stmt = $conn->prepare("SELECT * FROM properties WHERE type = ? AND id <> ? ORDER BY id DESC LIMIT 3");
		$stmt->bind_param("si", $property['type'], $property['id']);
		$stmt->execute();
		$res = $stmt->get_result();
		while ($res && ($row = $res->fetch_assoc())) { $imgs = explode_images($row['images']);
			echo '<a class="card" href="property.php?id=' . h($row['id']) . '">';
			echo '<img src="' . h($imgs[0]) . '" alt="property" />';
			echo '<div class="content">';
			echo '<div class="price">' . h(price_display($row['price'])) . ' - ' . h($row['purpose']) . '</div>';
			echo '<div>' . h($row['title']) . '</div>';
			echo '<div class="muted">' . h($row['location']) . '</div>';
			echo '</div></a>';
		}
		?>
	</div>
</section>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


