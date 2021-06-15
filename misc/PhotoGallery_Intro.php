<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Indian River Lagoon Photo Gallery</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Indian River Lagoon Photo Gallery</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Welcome to the Indian River Lagoon Photo Gallery. We are pleased to highlight the
                    creativity and imagination of the many photographers who contributed their work to the annual Indian
                    River Lagoon Photo Contest, sponsored by the St. Johns River and South Florida Water Management
                    Districts and Environmental Consulting and Technology, Inc. Thanks to everyone who participated!</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/14DubinA17.gif" alt="" width="400" height="320"/><br/>
                <span class="caption">2014 Calendar Cover Photo Photographer - Arnold Dubin</span></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Choose a year below to start viewing these remarkable photographs. You will note that
                    for each year, photographs are placed into five categories as follows: Winning Photos; Lagoon
                    Wildlife; Work &amp; Play on the IRL; Landmarks &amp; Landscapes; and Sunrise &amp; Sunset.</p></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:100px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body"><a href="14GalleryMain.php">2014</a></p>
                <p class="body"><a href="12GalleryMain.php">2012</a></p>
                <p class="body"><a href="11GalleryMain.php">2011</a></p>
                <p class="body"><a href="10GalleryMain.php">2010</a></p>
                <p class="body"><a href="09GalleryMain.php">2009</a></p>
                <p class="body"><a href="GalleryMain08.php">2008</a></p>
                <p class="body"><a href="GalleryMain07.php">2007</a></p>
                <p class="body"><a href="GalleryMain06.php">2006</a></p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
