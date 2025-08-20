<?php
	require_once __DIR__ . '/config.php';
	require_login();
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$sender_id = current_user()['id'];
		$receiver_id = (int)$_POST['receiver_id'];
		$property_id = (int)$_POST['property_id'];
		$message = $_POST['message'];
		$stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, property_id, message) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("iiis", $sender_id, $receiver_id, $property_id, $message);
		$stmt->execute();
		header('Location: dashboard.php');
		exit;
	}
	header('Location: index.php');
	exit;
?>


