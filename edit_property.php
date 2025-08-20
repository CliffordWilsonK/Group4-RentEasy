<?php
	require_once __DIR__ . '/config.php';
	require_login();
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$prop = get_property_by_id($id);
	if (!$prop) { header('Location: dashboard.php'); exit; }
	$user = current_user();
	if (!is_admin() && (int)$prop['landlord_id'] !== (int)$user['id']) { header('Location: dashboard.php'); exit; }

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$title = $_POST['title'];
		$description = $_POST['description'];
		$type = $_POST['type'];
		$purpose = $_POST['purpose'];
		$price = (float)$_POST['price'];
		$bedrooms = (int)$_POST['bedrooms'];
		$bathrooms = (int)$_POST['bathrooms'];
		$size_sqft = (int)$_POST['size_sqft'];
		$location = $_POST['location'];
		$furnished = $_POST['furnished'];
		$images = $_POST['images'];
		$stmt = $conn->prepare("UPDATE properties SET title=?, description=?, type=?, purpose=?, price=?, bedrooms=?, bathrooms=?, size_sqft=?, location=?, furnished=?, images=? WHERE id=?");
		$stmt->bind_param("ssssdiiisssi",
			$title, $description, $type, $purpose, $price,
			$bedrooms, $bathrooms, $size_sqft, $location,
			$furnished, $images, $id
		);
		$stmt->execute();
		header('Location: dashboard.php');
		exit;
	}
	$title = 'Edit Property';
	ob_start();
?>

<div class="container" style="max-width:760px;">
	<h1>Edit Property</h1>
	<form class="stack" method="post">
		<input name="title" value="<?php echo h($prop['title']); ?>" />
		<textarea name="description" style="min-height:120px; padding:10px; border:1px solid #d1d5db; border-radius:8px;"><?php echo h($prop['description']); ?></textarea>
		<div class="grid" style="grid-template-columns: repeat(2, 1fr);">
			<select name="type">
				<?php foreach (['Apartment','House','Land','Office'] as $t): ?>
					<option <?php echo ($prop['type']===$t?'selected':''); ?>><?php echo $t; ?></option>
				<?php endforeach; ?>
			</select>
			<select name="purpose">
				<option <?php echo ($prop['purpose']==='Rent'?'selected':''); ?>>Rent</option>
				<option <?php echo ($prop['purpose']==='Sale'?'selected':''); ?>>Sale</option>
			</select>
			<input type="number" step="0.01" name="price" value="<?php echo h($prop['price']); ?>" />
			<input type="number" name="size_sqft" value="<?php echo h($prop['size_sqft']); ?>" />
			<input type="number" name="bedrooms" value="<?php echo h($prop['bedrooms']); ?>" />
			<input type="number" name="bathrooms" value="<?php echo h($prop['bathrooms']); ?>" />
			<input name="location" value="<?php echo h($prop['location']); ?>" />
			<select name="furnished">
				<option <?php echo ($prop['furnished']==='Unfurnished'?'selected':''); ?>>Unfurnished</option>
				<option <?php echo ($prop['furnished']==='Furnished'?'selected':''); ?>>Furnished</option>
			</select>
		</div>
		<input name="images" value="<?php echo h($prop['images']); ?>" />
		<button class="btn" type="submit">Save Changes</button>
	</form>
</div>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


