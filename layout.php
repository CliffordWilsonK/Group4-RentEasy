<?php
	require_once __DIR__ . '/config.php';
	$title = isset($title) ? $title : 'Property Listing';
?>


<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?php echo h($title); ?></title>
	<link rel="stylesheet" href="styles.css" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<div class="container nav">
			<a href="index.php" class="brand">RentEasy</a>
			<nav class="nav-links">
				<a href="listings.php">Browse</a>
				<?php if (is_logged_in()): ?>
					<a href="dashboard.php">Dashboard</a>
					<?php if (is_admin()): ?><a href="admin.php">Admin</a><?php endif; ?>
					<form method="post" action="logout.php" style="margin:0;">
						<button class="btn ghost" style="padding:8px 12px;">Logout</button>
					</form>
				<?php else: ?>
					<a href="login.php" class="btn ghost">Login</a>
					<a href="signup.php" class="btn cta">Sign Up</a>
				<?php endif; ?>
				<?php if (is_logged_in() && (is_landlord() || is_admin())): ?>
					<a href="add_property.php" class="btn cta">List Your Property</a>
				<?php endif; ?>
			</nav>
		</div>
	</header>

	<main class="container">
		<?php if (isset($content)) { echo $content; } ?>
	</main>

	<footer>
		<div class="container">
			<p class="muted">© <?php echo date('Y'); ?> RentEasy. All rights reserved.</p>
			<div class="row">
				<a href="#">About</a>
				<a href="#">Contact</a>
				<a href="#">Terms</a>
				<a href="#">Privacy</a>
			</div>
		</div>
	</footer>

	<script>
	(function() {
		var header = document.querySelector('header');
		var hero = document.querySelector('.hero');
		function updateHeaderState() {
			if (!header) return;
			var scrolled = window.scrollY > 10;
			if (hero && !scrolled) {
				header.classList.add('on-top');
				header.classList.remove('scrolled');
			} else {
				header.classList.add('scrolled');
				header.classList.remove('on-top');
			}
		}
		window.addEventListener('scroll', updateHeaderState, { passive: true });
		window.addEventListener('load', updateHeaderState);
		updateHeaderState();
	})();
	</script>
</body>
</html>


