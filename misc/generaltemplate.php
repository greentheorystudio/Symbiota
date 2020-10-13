<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Page Title</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
        include(__DIR__ . '/../header.php');
		?>
		<div id="innertext">

			Add static, dynamic and form content here.<br/>
			
		</div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
