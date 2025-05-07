<?php
include("../../../include/db.php");

$id = $_POST['id'];

$sql = "DELETE FROM locales WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: gestion-locales.php");
exit();
