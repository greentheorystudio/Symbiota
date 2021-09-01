<?php
include_once(__DIR__ . '/../config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
	<head>
		<title>Take a tour</title>
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
        include(__DIR__ . '/../header.php');
		?>
		<div style="display:flex;flex-direction:column;justify-content:center;width:100%;">
            <div class="statictext" style="width:80%;margin: 20 auto;text-align: center;">
                Discover the history, geography and ecology of Florida’s Indian River Lagoon from anywhere in the world.<br />
                Click the photo to explore the biodiverse estuary and its watershed via our interactive StoryMap,<br />
                <a href="https://storymaps.arcgis.com/stories/6370ba882c26422987b91030f588c100" >A River in Name Only.</a>
            </div>
            <a style="border:0;width:90%;margin: 0 auto;" href="https://storymaps.arcgis.com/stories/6370ba882c26422987b91030f588c100">
                <img style="width:100%;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/StoryMapCover.jpg" />
            </a>
        </div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
