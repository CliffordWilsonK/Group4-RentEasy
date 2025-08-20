<?php
	require_once __DIR__ . '/config.php';
	require_login();
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$prop = get_property_by_id($id);
	if ($prop) {
		$user = current_user();
		if (is_admin() || (int)$prop['landlord_id'] === (int)$user['id']) {
			$stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
		}
	}
	header('Location: dashboard.php');
	exit;
?>


