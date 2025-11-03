<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title>Contact</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="mainContainer">
    <h1>Contact Us</h1>
    <table style="width:450px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">Please contact us with comments, suggestions and/or photographs:</p>
                <p class="body">Smithsonian Marine Station at Fort Pierce<br/>
                    701 Seaway Drive<br/>
                    Fort Pierce, FL 34949<br/>
                    <br/>
                    Phone: 772.462.6220&nbsp;FAX: 772.461.8154<br/>
                    <br/>
                    Send Comments to: <a href="mailto:IRLWebmaster@si.edu" tabindex="0">IRLWebmaster@si.edu</a></font></p>
            </td>
        </tr>
    </table>
    <br/>
    <table style="width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <img src="https://irlspecies.org/content/imglib/Stone Crab.jpg" alt="Person holding a stone crab with its claws raised." width="500"/>&nbsp;
            </td>
        </tr>
    </table>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
