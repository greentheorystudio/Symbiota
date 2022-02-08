<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);

?>
<html lang="en">
	<head>
		<title>BLM State Holdings</title>
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
			<b>BLM State Holdings</b>
		</div>
		<div id="innertext">
			<div style="float:right;margin-right:20px;">
				<img src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/BLM_logo.fw.png" style="height:125px;border:0;" />
			</div>
			<h1>BLM State Holdings</h1>
			<ul>
				<li><a href="">Alaska</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/blmarizona.php">Arizona</a></li>
				<li><a href="">California</a></li>
				<li><a href="">Colorado</a></li>
				<li><a href="">Idaho</a></li>
				<li><a href="">Montana</a></li>
				<li><a href="">Nevada</a></li>
				<li><a href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/misc/blmnewmexico.php">New Mexico</a></li>
				<li><a href="">Oregon</a></li>
				<li><a href="">Utah</a></li>
				<li><a href="">Washington</a></li>
				<li><a href="">Wyoming</a></li>
			</ul>
		</div>
		<?php
			include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
