<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
<title><?php echo $DEFAULT_TITLE; ?> Digital Collections</title>
<link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
<link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	<?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
</script>
</head>
<body>
	<!-- Header -->
	<?php include(__DIR__ . '/header.php'); ?>

	<main id="innertext">
		<h1><?php echo $DEFAULT_TITLE; ?></h1>
		<p>Browse through our featured digital collections.</p>
		<p>Selected specimens available at the UW-Madison museums were selected by our curators to showcase the diversity of material that are hosted here.</p>

	</main>

	<?php
	include(__DIR__ . '/footer.php');
	?>
	
</body>
</html>
