<?php
include_once(__DIR__ . '/../config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);

?>
<html lang="en">
	<head>
		<title>BLM New Mexico Offices</title>
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
			<a href="<?php echo $CLIENT_ROOT; ?>/misc/blmstates.php">BLM State Holdings</a> &gt;&gt;
			<b>BLM New Mexico Offices</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $CLIENT_ROOT; ?>/images/layout/BLM_logo.fw.png" style="height:125px;border:;" />
			</div>
			<h1>BLM New Mexico Offices</h1>
			<ul>
				<li><a href="">Carlsbad Field Office</a></li>
				<li><a href="">Farmington Field Office</a></li>
				<li><a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=103">Las Cruces District Office</a></li>
				<li><a href="">Rio Puerco Field Office</a></li>
				<li><a href="">Roswell Field Office</a></li>
				<li><a href="">Socorro Field Office</a></li>
				<li><a href="<?php echo $CLIENT_ROOT; ?>/checklists/checklist.php?cl=4004&emode=0">Taos Field Office</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
