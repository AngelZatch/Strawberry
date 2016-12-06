<?php
include "db_connect.php";
$db = PDOFactory::getConnection();

$box_token = $_POST["box_token"];
if(isset($_POST["user_power"]))
	$user_power = $_POST["user_power"];
else
	$user_power = -1;
$load = $db->query("SELECT video_index, history_start, link, video_name, user_pseudo FROM roomHistory_$box_token rh
					JOIN song_base sb ON rh.video_index = sb.song_base_id
					JOIN user u ON rh.history_user = u.user_token
					WHERE video_status = 1
					ORDER BY room_history_id DESC
					LIMIT 1");
if($load->rowCount() != 0){
	$loaded = $load->fetch(PDO::FETCH_ASSOC);
	$n = array(
		"index" => $loaded["video_index"],
		"link" => $loaded["link"],
		"title" => stripslashes($loaded["video_name"]),
		"timestart" => $loaded["history_start"],
		"submitter" => $loaded["user_pseudo"]
	);
	echo json_encode($n);
} else {
	// Loaded the oldest non-played video
	$load = $db->query("SELECT video_index, room_history_id, history_user, user_pseudo, link, video_name
						FROM roomHistory_$box_token rh
						JOIN song_base sb ON rh.video_index = sb.song_base_id
						JOIN user u ON rh.history_user = u.user_token
						WHERE video_status = '0'
						AND (room_history_id = (SELECT room_history_id
												FROM roomHistory_$box_token
												WHERE video_status = '2'
												ORDER BY room_history_id DESC LIMIT 1) +1)
						OR (room_history_id = '1' AND video_status = '0')");
	$loaded = $load->fetch(PDO::FETCH_ASSOC);
	$time = date_create('now', new datetimezone('UTC'))->format('Y-m-d H:i:s');
	$n = array(
		"index" => $loaded["video_index"],
		"link" => $loaded["link"],
		"title" => stripslashes($loaded["video_name"]),
		"timestart" => $time,
		"submitter" => $loaded["user_pseudo"]
	);
	if($user_power == 2){
		$playing = $db->query("UPDATE roomHistory_$box_token
							SET video_status='1',
							history_start = '$time'
							WHERE room_history_id='$loaded[room_history_id]'");
		$incrementSongs = $db->query("UPDATE user_stats
								SET stat_songs_submitted = stat_songs_submitted + 1
								WHERE user_token = '$loaded[history_user]'");
	}
	echo json_encode($n);
}
?>
