<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="en">
	<head>
		<title>BLM Arizona Offices</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">

		</script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/shared.js?ver=140310" type="text/javascript"></script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/symb/misc.generaltemplate.js?ver=140310" type="text/javascript"></script>
	</head>
	<body>
		<?php
		include(__DIR__ . '/../header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/index.php">Home</a> &gt;&gt;
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/blmstates.php">BLM State Holdings</a> &gt;&gt;
			<b>BLM Arizona Offices</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/BLM_logo.fw.png" style="height:125px;border:0;" />
			</div>
			<h1>BLM Arizona Offices</h1>
			<ul>
				<li><a href="">Arizona Strip Field Office</a></li>
				<li><a href="">Grand Canyon-Parashant National Monument</a></li>
				<li><a href="">Hassayampa Field Office</a></li>
				<li><a href="">Kingman Field Office</a></li>
				<li><a href="">Lake Havasu Field Office</a></li>
				<li><a href="">Lower Sonoran Field Office</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=82">Safford Field Office</a></li>
				<li><a href="">Tucson Field Office</a></li>
				<li><a href="">Yuma Field Office</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
