<?php
require_once "db_connect.php";
$db = PDOFactory::getConnection();

/** Deletes an entry in the database. Once again, this script is generic and can be used for whatever in the whole database. **/

$table = $_POST["table"];
$target_id = $_POST["target_id"];

$primary_key = $db->query("SHOW INDEX FROM $table WHERE Key_name = 'PRIMARY'")->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("DELETE FROM $table WHERE $primary_key[Column_name] = ?");
$stmt->bindParam(1, $target_id, PDO::PARAM_INT);
$stmt->execute();
?>
