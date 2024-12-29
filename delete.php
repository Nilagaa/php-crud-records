<?php
include "connection.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $result = $conn->query("SELECT * FROM table1 WHERE id=$id");
    $record = $result->fetch_assoc();

    if ($record['image'] && file_exists("uploads/" . $record['image'])) {
        unlink("uploads/" . $record['image']);
    }

    $sql = "DELETE FROM table1 WHERE id=$id";

    if ($conn->query($sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>