<?php
include "connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM table1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $new_image = $_FILES['image']['name'];
    $current_image = $_POST['current_image'];

    if ($new_image) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($new_image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

        // Delete the old image if it exists
        if ($current_image && file_exists("uploads/$current_image")) {
            unlink("uploads/$current_image");
        }
    } else {
        $new_image = $current_image;
    }

    // Use a prepared statement to update the record
    $stmt = $conn->prepare("UPDATE table1 SET first_name = ?, last_name = ?, email = ?, contact = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $first_name, $last_name, $email, $contact, $new_image, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Record</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Edit Record</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $record['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $record['image'] ?>">
            <div class="mb-3">
                <label>First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?= $record['first_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?= $record['last_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $record['email'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Contact</label>
                <input type="text" name="contact" class="form-control" value="<?= $record['contact'] ?>" required>
            </div>
            <div class="mb-3">
                <label>Current Image</label><br>
                <?php if ($record['image']): ?>
                    <img src="uploads/<?= $record['image'] ?>" alt="Image" width="100"><br>
                <?php else: ?>
                    No Image Uploaded
                <?php endif; ?>
                <label>Upload New Image</label>
                <input type="file" name="image" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Update Record</button>
        </form>
    </div>
</body>
</html>
