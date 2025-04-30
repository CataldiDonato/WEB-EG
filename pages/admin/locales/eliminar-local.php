<?php
include("../../../include/db.php");

$codLocal = $_POST['codLocal'];

$sql = "DELETE FROM locales WHERE codLocal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $codLocal);
$stmt->execute();

header("Location: gestion-locales.php");
exit();
