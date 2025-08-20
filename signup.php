<?php
	require_once __DIR__ . '/config.php';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$role = $_POST['role']; // tenant | landlord
		$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("ssss", $name, $email, $password, $role);
		$stmt->execute();
		header('Location: login.php');
		exit;
	}
	$title = 'Sign Up';
	ob_start();
?>

<div class="container" style="max-width:520px;">
	<h1>Create account</h1>
	<form class="stack" method="post">
		<input required name="name" placeholder="Full name" />
		<input required type="email" name="email" placeholder="Email" />
		<input required type="password" name="password" placeholder="Password" />
		<select name="role">
			<option value="tenant">Renter / Buyer</option>
			<option value="landlord">Landlord / Agent</option>
		</select>
		<button class="btn" type="submit">Sign Up</button>
	</form>
</div>

<?php
	$content = ob_get_clean();
	require __DIR__ . '/layout.php';
?>


