<?php
$PAGE_TITLE = 'Map Search';
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE; ?> Spatial Module</title>
    <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <script type="text/javascript">
		<?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php include(__DIR__ . '/header.php'); ?>

	<main id="innertext">
		<h1><?php echo $DEFAULT_TITLE; ?></h1>
		<p>The spatial module shows only those specimens that have been georeferenced, therefore might not yield complete results.</p>
		<button><a href="<?php echo $CLIENT_ROOT.'/spatial/index.php' ?>" class="uw-button"  target="_blank">Open Spatial Module</a></button>
		<p>Note: map search opens in new tab.</p>
	</main>

	<?php
	include(__DIR__ . '/footer.php');
	?>
	
</body>
</html>
