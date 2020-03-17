<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
    <head>
        <title><?php echo $DEFAULT_TITLE; ?> Home</title>
        <link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <link href="css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
        <meta name='keywords' content='' />
        <script type="text/javascript">
            <?php include_once('config/googleanalytics.php'); ?>
        </script>
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div  id="innertext">
            <h1></h1>

            <div style="padding: 0 10px;">
                Description and introduction of project
            </div>
        </div>

        <?php
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
