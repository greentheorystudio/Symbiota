<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>2010 Indian River Lagoon Photo Gallery</title>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Welcome to the 2010 Indian River Lagoon Photo Gallery</h2>
    <table width="700">
        <tr>
            <td><p align="center"><img src="../content/imglib/10DubrickU499text.jpg" width="499" height="451"/></p>&nbsp;
            </td>
        </tr>
    </table>
    <table style="width:480px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="title">Choose a category below to view 2010 entries!</p>
                <p class="body"><a href="10calpics.php">Winning Photos &amp; Runners-Up</a></p>
                <p class="body"><a href="10SpeciesPICS.php">Lagoon
                        Wildlife </a></p>
                <p class="body"><a href="10IRLRecreation.php">Work &amp; Play on the IRL</a></p>
                <p class="body"><a href="10IRLLandscapesEtc.php">Landmarks &amp; Landscapes</a></p>
                <p class="body"><a href="10SunriseSunset.php">Sunrise &amp; Sunset</a></font></p>

                <p class="body">All photographs
                    in this gallery are the property of the individual photographer
                    and cannot be duplicated in any way without express permission.</p>
                <p class="footer_note">Want to contribute a photograph to
                    the Gallery? Contact:<br/>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
