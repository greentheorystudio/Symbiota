<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

?>
<html lang="en">
	<head>
		<title>NPS Regions</title>
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
			<b>NPS Regions</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/NPS_AH.png" style="height:125px;border:0;" />
			</div>
			<h1>NPS Regions</h1>
			<ul>
				<li><a href="">Alaska</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=5">Intermountain</a></li>
				<li><a href="">Midwest</a></li>
				<li><a href="">National Capital</a></li>
				<li><a href="">Northeast</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?proj=104">Pacific West</a></li>
				<li><a href="">Southeast</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
