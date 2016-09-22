<?php
include "db_connect.php";
session_start();
$db = PDOFactory::getConnection();

$box_token = $_POST["box_token"];
$prev = $_POST["lastPlayed"];
$user_power = $_POST["user_power"];

if($user_power == 2){
	// Get ID of previous video
	$playedID = $db->query("SELECT room_history_id, playlist_order
							FROM roomHistory_$box_token rh
							WHERE video_index = '$prev'
							AND video_status = '1'")->fetch(PDO::FETCH_ASSOC);

	// Update status of previous video to 'played' (2)
	$played = $db->query("UPDATE roomHistory_$box_token
					SET video_status = '2'
					WHERE room_history_id = '$playedID[room_history_id]'");

	// Check if next is a video to ignore (video_status to 3)
	$nextIgnored = true;
	$i = 0;
	while($nextIgnored){
		$i++;
		$nextVideoState = $db->query("SELECT video_status, room_history_id, playlist_order
									FROM roomHistory_$box_token
									WHERE room_history_id = '$playedID[playlist_order]' +$i")->fetch(PDO::FETCH_ASSOC);

		if($nextVideoState["video_status"] == '3'){
			// If it is, the video is indicated as played.
			$confirmIgnore = $db->query("UPDATE roomHistory_$box_token
										SET video_status = '2'
										WHERE room_history_id = '$nextVideoState[room_history_id]'");
		} else {
			$nextIgnored = false;
		}
	}
}

// Get next video
$next = $db->query("SELECT room_history_id, video_index, history_user, link, video_name
					FROM roomHistory_$box_token rh
					JOIN song_base sb ON rh.video_index = sb.song_base_id
					WHERE video_status = '0'
					ORDER BY playlist_order ASC
					LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if($next["link"] != null){
	if($user_power == 2){
		$time = date_create('now', new datetimezone('UTC'))->format('Y-m-d H:i:s');
		// Set status of next video to 'playing' (1)
		$playing = $db->query("UPDATE roomHistory_$box_token
							SET video_status='1',
							history_start = '$time'
							WHERE room_history_id='$next[room_history_id]'");
		$incrementSongs = $db->query("UPDATE user_stats
								SET stat_songs_submitted = stat_songs_submitted + 1
								WHERE user_token = '$next[history_user]'");
	}
	$n = array();
	$n["index"] = $next["video_index"];
	$n["link"] = $next["link"];
	$n["title"] = stripslashes($next["video_name"]);
	echo json_encode($n);
} else {
	echo null;
}
?>
