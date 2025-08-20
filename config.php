<?php
	// Basic configuration: database connection and auth helpers
	// NOTE: For simplicity, minimal security; no SQL injection protection per request

	session_start();

	$DB_HOST = 'sql311.infinityfree.com';
	$DB_USER = 'if0_39747861';
	$DB_PASS = '4qoX0WFzZ9Lmp';
	$DB_NAME = 'if0_39747861_rental';

	$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	if (!$conn) {
		die('Database connection failed: ' . mysqli_connect_error());
	}

	function h($value) {
		return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
	}

	function current_user() {
		return isset($_SESSION['user']) ? $_SESSION['user'] : null;
	}

	function is_logged_in() {
		return current_user() !== null;
	}

	function is_admin() {
		return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
	}

	function is_landlord() {
		return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'landlord';
	}

	function is_tenant() {
		return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'tenant';
	}

	function require_login() {
		if (!is_logged_in()) {
			header('Location: login.php');
			exit;
		}
	}

	function get_user_by_id($userId) {
		global $conn;
		$stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1");
		$stmt->bind_param("i", $userId);
		$stmt->execute();
		$res = $stmt->get_result();
		return $res ? $res->fetch_assoc() : null;
	}

	function get_property_by_id($propertyId) {
		global $conn;
		$stmt = $conn->prepare("SELECT p.*, u.name AS landlord_name, u.email AS landlord_email FROM properties p LEFT JOIN users u ON p.landlord_id = u.id WHERE p.id = ? LIMIT 1");
		$stmt->bind_param("i", $propertyId);
		$stmt->execute();
		$res = $stmt->get_result();
		return $res ? $res->fetch_assoc() : null;
	}

	function price_display($amount) {
		return 'GH₵' . number_format((float)$amount, 2);
	}

	function explode_images($imagesCsv) {
		$images = array_filter(array_map('trim', explode(',', (string)$imagesCsv)));
		if (empty($images)) {
			$images = ['https://via.placeholder.com/800x500?text=No+Image'];
		}
		return $images;
	}

?>


