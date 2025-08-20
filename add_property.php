<?php
require_once 'config.php';
require_login();
if (!is_landlord() && !is_admin()) { header('Location: dashboard.php'); exit; }

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
    $landlord_id = current_user()['id'];

    $stmt = $conn->prepare("INSERT INTO properties 
        (title, description, type, purpose, price, bedrooms, bathrooms, size_sqft, location, furnished, images, landlord_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdiiisssi", 
        $title, $description, $type, $purpose, $price, 
        $bedrooms, $bathrooms, $size_sqft, $location, 
        $furnished, $images, $landlord_id
    );
    $stmt->execute();

    header('Location: dashboard.php');
    exit;
}

$title = 'Add Property';
?>

<div class="container" style="max-width:760px;">
    <h1>Add New Property</h1>
    <form class="stack" method="post">
        <input name="title" placeholder="Title" required />
        <textarea name="description" placeholder="Description" style="min-height:120px; padding:10px; border:1px solid #d1d5db; border-radius:8px;"></textarea>
        <div class="grid" style="grid-template-columns: repeat(2, 1fr); gap:10px;">
            <select name="type">
                <option>Apartment</option>
                <option>House</option>
                <option>Land</option>
                <option>Office</option>
            </select>
            <select name="purpose">
                <option>Rent</option>
                <option>Sale</option>
            </select>
            <input type="number" step="0.01" name="price" placeholder="Price" />
            <input type="number" name="size_sqft" placeholder="Size (sqft)" />
            <input type="number" name="bedrooms" placeholder="Bedrooms" />
            <input type="number" name="bathrooms" placeholder="Bathrooms" />
            <input name="location" placeholder="Location" />
            <select name="furnished">
                <option>Unfurnished</option>
                <option>Furnished</option>
            </select>
        </div>
        <input name="images" placeholder="Image URLs (comma separated)" />
        <button class="btn" type="submit">Create Listing</button>
    </form>
</div>

<?php require 'layout.php'; ?>
