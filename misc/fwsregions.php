<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

?>
<html lang="en">
	<head>
		<title>FWS Regions</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">

		</script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/shared.js?ver=140310" type="text/javascript"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt; 
			<b>FWS Regions</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/US-FWS-logo.png" style="height:125px;border:0;" />
			</div>
			<h1>FWS Regions</h1>
			<ul>
				<li><a href="">Headquarters</a></li>
				<li><a href="">Region 1 - Pacific</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=24">Region 2 - Southwest</a></li>
				<li><a href="">Region 3 - Midwest</a></li>
				<li><a href="">Region 4 - Southeast</a></li>
				<li><a href="">Region 5 - Northeast</a></li>
				<li><a href="">Region 6 - Mountain-Prairie</a></li>
				<li><a href="">Region 7 - Alaska</a></li>
				<li><a href="">Region 8 - Pacific Southwest</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
