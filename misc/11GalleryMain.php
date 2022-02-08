<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>2011 Indian River Lagoon Photo Gallery</title>
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
    <h2>Welcome to the 2011 Indian River Lagoon Photo Gallery</h2>
    <table style="width:550px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/11DunkertonT1_Main.jpg" alt="" width="550"/></td>
        </tr>
    </table>
    <table style="width:550px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Choose a category below to view 2011 entries! </p>
                <p class="body"><a href="11calpics.php">Winning Photos &amp; Runners-Up</a></p>
                <p class="body"><a href="11SpeciesPICS.php">Lagoon Wildlife </a></p>
                <p class="body"><a href="11IRLRecreation.php">Work &amp; Play on the IRL</a></p>
                <p class="body"><a href="11IRLLandscapesEtc.php">Landmarks &amp; Landscapes</a></p>
                <p class="body"><a href="11SunriseSunset.php">Sunrise &amp; Sunset</a></p>
                <p class="body">All photographs in this gallery are the property of the individual photographer and
                    cannot be duplicated in any way without express permission.</p></td>
        </tr>
        <tr>
            <td>
                <p class="footer_note">
                    Want to contribute a photograph to the Gallery? Contact:<br/>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
