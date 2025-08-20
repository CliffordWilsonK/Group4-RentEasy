<?php
	$title = 'Find Properties | RentEasy';
	ob_start();
?>



<section class="hero">
	<div class="container">
		<h1>Find your next home with confidence</h1>
		<p class="muted">Search houses, apartments, lands, and offices. Rent or buy with trusted agents.</p>
		<form class="search-bar" method="get" action="listings.php">
			<input type="text" name="location" placeholder="Location (e.g., Accra)" />
			<select name="type">
				<option value="">Any Type</option>
				<option>Apartment</option>
				<option>House</option>
				<option>Land</option>
				<option>Office</option>
			</select>
			<select name="purpose">
				<option value="">For Rent or Sale</option>
				<option>Rent</option>
				<option>Sale</option>
			</select>
			<select name="max_price">
				<option value="">Max Price</option>
				<option value="500">500</option>
				<option value="1000">1,000</option>
				<option value="5000">5,000</option>
				<option value="10000">10,000</option>
			</select>
			<button class="btn" type="submit">Search</button>
		</form>
		<div class="row" style="margin-top:10px; gap:8px;">
			<a class="badge" href="listings.php?type=Apartment">Apartments</a>
			<a class="badge" href="listings.php?type=House">Houses</a>
			<a class="badge" href="listings.php?type=Office">Offices</a>
			<a class="badge" href="listings.php?type=Land">Land</a>
		</div>
	</div>
</section>

<section class="section container">
	<div class="row" style="justify-content: space-between;">
		<h2>Featured Properties</h2>
		<a href="listings.php" class="btn ghost">View all</a>
	</div>
	<div class="grid">
		<?php
		require_once __DIR__ . '/config.php';
		$stmt = $conn->prepare("SELECT * FROM properties WHERE approved = 1 ORDER BY id DESC LIMIT 6");
		$stmt->execute();
		$res = $stmt->get_result();
		while ($res && ($row = $res->fetch_assoc())) {
			$images = explode_images($row['images']);
			echo '<a class="card" href="property.php?id=' . h($row['id']) . '">';
			echo '<img src="' . h($images[0]) . '" alt="property" />';
			echo '<div class="content">';
			echo '<div class="price">' . h(price_display($row['price'])) . ' - ' . h($row['purpose']) . '</div>';
			echo '<div>' . h($row['title']) . '</div>';
			echo '<div class="muted">' . h($row['location']) . ' • ' . h($row['type']) . '</div>';
			echo '</div></a>';
		}
		?>
	</div>
</section>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


