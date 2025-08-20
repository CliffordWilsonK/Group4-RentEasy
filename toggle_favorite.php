<?php
	require_once __DIR__ . '/config.php';
	require_login();
	$user_id = current_user()['id'];
	$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
	$stmtCheck = $conn->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ? LIMIT 1");
	$stmtCheck->bind_param("ii", $user_id, $property_id);
	$stmtCheck->execute();
	$existsRes = $stmtCheck->get_result();
	$exists = $existsRes && $existsRes->fetch_assoc();
	if ($exists) {
		$stmtDel = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
		$stmtDel->bind_param("ii", $user_id, $property_id);
		$stmtDel->execute();
	} else {
		$stmtIns = $conn->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
		$stmtIns->bind_param("ii", $user_id, $property_id);
		$stmtIns->execute();
	}
	header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
	exit;
?>


