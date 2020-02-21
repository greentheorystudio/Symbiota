<?php
include_once(__DIR__ . '/../config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);

?>
<html lang="en">
	<head>
		<title>USFS Regions</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">

		</script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/symb/shared.js?ver=140310" type="text/javascript"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt; 
			<b>USFS Regions</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/USFS_LOGO.fw.png" style="height:125px;border:0;" />
			</div>
			<h1>USFS Regions</h1>
			<ul>
				<li><a href="">Alaska</a></li>
				<li><a href="">Eastern</a></li>
				<li><a href="">Intermountain</a></li>
				<li><a href="">Northern</a></li>
				<li><a href="">Pacific Northwest</a></li>
				<li><a href="<?php echo $CLIENT_ROOT; ?>/misc/usfspacificsouthwest.php">Pacific Southwest</a></li>
				<li><a href="">Rocky Mountain</a></li>
				<li><a href="">Southern</a></li>
				<li><a href="<?php echo $CLIENT_ROOT; ?>/misc/usfssouthwest.php">Southwest</a></li>
            </ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
