<?php
session_start();
include "db_connect.php";
$db = PDOFactory::getConnection();

$box_token = $_GET["box_token"];

date_default_timezone_set('UTC');
$time = date('Y-m-d H:i:s', time() - 15);

$box_status = $db->query("SELECT user_pseudo, stat_visitors, stat_followers, room_creator, user_pp, room_name, room_active, room_submission_rights, room_play_type, room_protection, room_description
					FROM rooms r
					JOIN user u ON r.room_creator = u.user_token
					JOIN user_stats us ON r.room_creator = us.user_token
					WHERE box_token = '$box_token'")->fetch(PDO::FETCH_ASSOC);

$box_status["present_watchers"] = $db->query("SELECT COUNT(room_user_token) AS present_watchers FROM roomUsers_$box_token WHERE presence_stamp > '$time'")->fetch(PDO::FETCH_COLUMN);

if(isset($_SESSION["username"]) && $box_status["user_pseudo"] != $_SESSION["username"]){
	$userFollow = $db->query("SELECT * FROM user_follow uf
								WHERE user_following = '$_SESSION[token]'
								AND user_followed = '$box_status[room_creator]'")->rowCount();
	$box_status["following_creator"] = $userFollow;
}

echo json_encode($box_status);
?>
