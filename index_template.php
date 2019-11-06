<?php
include_once('config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title><?php echo $DEFAULT_TITLE; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
    <meta name='keywords' content='' />
    <script type="text/javascript">
        <?php include_once('config/googleanalytics.php'); ?>
    </script>
</head>
<body>
<?php
include($SERVER_ROOT. '/header.php');
?>
<div  id="innertext">
    <h1></h1>

    <div style="padding: 0 10px;">
        Description and introduction of project
    </div>
</div>

<?php
include($SERVER_ROOT. '/footer.php');
?>

</body>
</html>
