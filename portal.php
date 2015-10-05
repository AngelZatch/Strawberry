<?php
require_once "functions/db_connect.php";
include "functions/tools.php";
include "functions/login.php";
if(isset($_GET["lang"])){
	$lang = $_GET["lang"];
	$_SESSION["lang"] = $lang;

	include_once "languages/lang.".$lang.".php";
} else {
	header("Location:portal.php?lang=en");
}
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Strawberry Music Streamer</title>
		<?php include "styles.php";?>
	</head>
	<body>
		<?php include "nav.php";?>
		<div class="main">
			<div class="col-lg-7 col-lg-offset-2">
				<form action="" method="post">
					<div class="form-group">
						<label for="login_name" class="control-label"><?php echo $lang["username"];?></label>
						<input type="text" placeholder="Username" class="form-control" name="login_name">
					</div>
					<div class="form-group">
						<label for="login_pwd" class="control-label"><?php echo $lang["password"];?></label>
						<input type="password" class="form-control" name="login_pwd">
					</div>
					<input type="submit" class="btn btn-primary btn-block" name="login" value="<?php echo $lang["log_in"];?>">
				</form>
			</div>
		</div>
		<?php include "scripts.php";?>
	</body>
</html>
