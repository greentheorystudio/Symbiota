<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
        <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
        <meta name='keywords' content='' />
        <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="innertext">
            <h1>Welcome to your Symbiota portal!</h1>

            <p>
                Here's where all the great stuff on your homepage goes.
            </p>
        </div>

        <?php
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
