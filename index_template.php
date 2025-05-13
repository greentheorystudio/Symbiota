<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
        <meta name="description" content="Welcome to the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
        <meta name='keywords' content='' />
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="main-container">
            <h1>Welcome to your portal!</h1>

            <p>
                Here's where all the great stuff on your homepage goes.
            </p>
        </div>

        <?php
        include_once(__DIR__ . '/config/footer-includes.php');
        include(__DIR__ . '/footer.php');
        ?>
    </body>
</html>
