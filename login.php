<?php
	require_once __DIR__ . '/config.php';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$email = $_POST['email'];
		$password = $_POST['password'];
		$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$res = $stmt->get_result();
		$user = $res ? $res->fetch_assoc() : null;
		if ($user && $user['password'] === $password) {
			$_SESSION['user'] = [
				'id' => $user['id'],
				'name' => $user['name'],
				'email' => $user['email'],
				'role' => $user['role']
			];
			header('Location: dashboard.php');
			exit;
		} else {
			$error = 'Invalid email or password';
		}
	}
	$title = 'Login';
	ob_start();
?>

<div class="container" style="max-width:480px;">
	<h1>Login</h1>
	<?php if (!empty($error)): ?><div class="muted" style="color:var(--danger);"><?php echo h($error); ?></div><?php endif; ?>
	<form class="stack" method="post">
		<input required type="email" name="email" placeholder="Email" />
		<input required type="password" name="password" placeholder="Password" />
		<button class="btn" type="submit">Login</button>
		<div class="muted">No account? <a href="signup.php" style="color:var(--primary);">Sign up</a></div>
	</form>
</div>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


