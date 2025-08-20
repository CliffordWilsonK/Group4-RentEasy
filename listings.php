<?php
	require_once __DIR__ . '/config.php';
	$title = 'Browse Properties';
	ob_start();

	// Build filter query using prepared statements
	$conditions = ['approved = 1'];
	$params = [];
	$types = '';
	if (!empty($_GET['location'])) { $conditions[] = "location LIKE ?"; $params[] = "%".$_GET['location']."%"; $types .= 's'; }
	if (!empty($_GET['type'])) { $conditions[] = "type = ?"; $params[] = $_GET['type']; $types .= 's'; }
	if (!empty($_GET['purpose'])) { $conditions[] = "purpose = ?"; $params[] = $_GET['purpose']; $types .= 's'; }
	if (!empty($_GET['max_price'])) { $conditions[] = "price <= ?"; $params[] = (int)$_GET['max_price']; $types .= 'i'; }
	if (!empty($_GET['bedrooms'])) { $conditions[] = "bedrooms >= ?"; $params[] = (int)$_GET['bedrooms']; $types .= 'i'; }
	if (!empty($_GET['bathrooms'])) { $conditions[] = "bathrooms >= ?"; $params[] = (int)$_GET['bathrooms']; $types .= 'i'; }
	$whereSql = implode(' AND ', $conditions);

	$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
	$perPage = 9;
	$offset = ($page - 1) * $perPage;

	$sqlCount = "SELECT COUNT(*) as c FROM properties WHERE $whereSql";
	$stmtCount = $conn->prepare($sqlCount);
	if (!empty($params)) { $stmtCount->bind_param($types, ...$params); }
	$stmtCount->execute();
	$countRes = $stmtCount->get_result();
	$total = $countRes ? (int)$countRes->fetch_assoc()['c'] : 0;
	$order = 'id DESC';
	if (!empty($_GET['sort']) && $_GET['sort'] === 'price') { $order = 'price ASC'; }
	$sql = "SELECT * FROM properties WHERE $whereSql ORDER BY $order LIMIT ? OFFSET ?";
	$stmt = $conn->prepare($sql);
	$typesWithPage = $types . 'ii';
	$paramsWithPage = array_merge($params, [$perPage, $offset]);
	$stmt->bind_param($typesWithPage, ...$paramsWithPage);
	$stmt->execute();
	$res = $stmt->get_result();
?>

<div class="row" style="align-items: flex-start; gap: 16px;">
	<aside class="sidebar" style="min-width:260px;">
		<h3>Filters</h3>
		<form class="stack" method="get" action="listings.php">
			<input name="location" placeholder="Location" value="<?php echo h(@$_GET['location']); ?>" />
			<select name="type">
				<option value="">Any type</option>
				<?php $types = ['Apartment', 'House', 'Land', 'Office']; foreach ($types as $t): ?>
					<option <?php echo (@$_GET['type']===$t?'selected':''); ?>><?php echo $t; ?></option>
				<?php endforeach; ?>
			</select>
			<select name="purpose">
				<option value="">Rent or Sale</option>
				<option <?php echo (@$_GET['purpose']==='Rent'?'selected':''); ?>>Rent</option>
				<option <?php echo (@$_GET['purpose']==='Sale'?'selected':''); ?>>Sale</option>
			</select>
			<input type="number" name="max_price" placeholder="Max price" value="<?php echo h(@$_GET['max_price']); ?>" />
			<input type="number" name="bedrooms" placeholder="Min bedrooms" value="<?php echo h(@$_GET['bedrooms']); ?>" />
			<input type="number" name="bathrooms" placeholder="Min bathrooms" value="<?php echo h(@$_GET['bathrooms']); ?>" />
			<button class="btn" type="submit">Apply</button>
		</form>
	</aside>

	<section style="flex:1;">
		<div class="row" style="justify-content: space-between;">
			<h2><?php echo h($total); ?> properties</h2>
			<form class="row" method="get">
				<?php foreach ($_GET as $k=>$v) { if ($k==='q' || $k==='sort') continue; echo '<input type="hidden" name="'.h($k).'" value="'.h($v).'" />'; } ?>
				<select name="sort" onchange="this.form.submit()">
					<option value="new" <?php echo (@$_GET['sort']!=='price'?'selected':''); ?>>Newest</option>
					<option value="price" <?php echo (@$_GET['sort']==='price'?'selected':''); ?>>Price low to high</option>
				</select>
			</form>
		</div>

		<div class="grid">
			<?php while ($res && ($row = $res->fetch_assoc())): $imgs = explode_images($row['images']); ?>
				<a class="card" href="property.php?id=<?php echo h($row['id']); ?>">
					<img src="<?php echo h($imgs[0]); ?>" alt="property" />
					<div class="content">
						<div class="price"><?php echo h(price_display($row['price'])); ?> - <?php echo h($row['purpose']); ?></div>
						<div><?php echo h($row['title']); ?></div>
						<div class="muted"><?php echo h($row['location']); ?> • <?php echo h($row['type']); ?> • <?php echo h($row['bedrooms']); ?> bd • <?php echo h($row['bathrooms']); ?> ba</div>
					</div>
				</a>
			<?php endwhile; ?>
		</div>

		<div class="row" style="justify-content:center; margin-top:16px; gap:8px;">
			<?php $totalPages = max(1, ceil($total / $perPage));
			for ($i=1;$i<=$totalPages;$i++):
				$params = $_GET; $params['page']=$i; $href = 'listings.php?' . http_build_query($params);
			?>
				<a class="badge" href="<?php echo h($href); ?>" style="<?php echo ($i===$page?'background:var(--primary);color:#fff;':''); ?>"><?php echo $i; ?></a>
			<?php endfor; ?>
		</div>
	</section>
</div>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


