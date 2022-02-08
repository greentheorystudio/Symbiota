<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

?>
<html lang="en">
	<head>
		<title>USFS Pacific Southwest Region</title>
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
			<a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/usfsregions.php">USFS Regions</a> &gt;&gt;
			<b>USFS Southwest Region</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/USFS_LOGO.fw.png" style="height:125px;border:0px;" />
			</div>
			<h1>USFS Pacific Southwest Region</h1>
			<ul>
				<li><a href="">Angeles National Forest (California)</a></li>
                <li><a href="">Cleveland National Forest (California)</a></li>
                <li><a href="">Eldorado National Forest (California)</a></li>
                <li><a href="">Inyo National Forest (California)</a></li>
                <li><a href="">Klamath National Forest (California)</a></li>
                <li><a href="">Lake Tahoe Basin Management Unit (California)</a></li>
                <li><a href="">Lassen National Forest (California)</a></li>
                <li><a href="">Los Padres National Forest (California)</a></li>
                <li><a href="">Mendocino National Forest (California)</a></li>
                <li><a href="">Modoc National Forest (California)</a></li>
                <li><a href="">Plumas National Forest (California)</a></li>
                <li><a href="">San Bernardino National Forest (California)</a></li>
                <li><a href="">Sequoia National Forest (California)</a></li>
                <li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/projects/index.php?pid=114">Shasta-Trinity National Forest (California)</a></li>
                <li><a href="">Sierra National Forest (California)</a></li>
                <li><a href="">Six Rivers National Forest (California)</a></li>
                <li><a href="">Stanislaus National Forest (California)</a></li>
                <li><a href="">Tahoe National Forest (California)</a></li>
            </ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
