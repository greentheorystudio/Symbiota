<?php
include_once(__DIR__ . '/../config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);

?>
<html lang="en">
	<head>
		<title>USFS Southwest Region</title>
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
			<a href="<?php echo $CLIENT_ROOT; ?>/misc/usfsregions.php">USFS Regions</a> &gt;&gt;
			<b>USFS Southwest Region</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/USFS_LOGO.fw.png" style="height:125px;border:0;" />
			</div>
			<h1>USFS Southwest Region</h1>
			<ul>
				<li><a href="">Apache-Sitgreaves National Forest (Arizona)</a></li>
				<li><a href="">Carson National Forest (New Mexico)</a></li>
				<li><a href="">Cibola National Forest (New Mexico)</a></li>
				<li><a href="">Coconino National Forest (Arizona)</a></li>
				<li><a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?proj=83">Coronado National Forest (Arizona)</a></li>
				<li><a href="">Gila National Forest (New Mexico)</a></li>
				<li><a href="">Kaibab National Forest (Arizona)</a></li>
				<li><a href="">Lincoln National Forest (New Mexico)</a></li>
				<li><a href="">Prescott National Forest (Arizona)</a></li>
				<li><a href="">Santa Fe National Forest (New Mexico)</a></li>
				<li><a href="">Tonto National Forest (Arizona)</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
