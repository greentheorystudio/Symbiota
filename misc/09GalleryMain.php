<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>2009 Indian River Lagoon Photo Gallery</title>
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
    <table style="border:0;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p align="center"><img src="../content/imglib/09ThomanR1Main.jpg" width="499" height="331"></p>
                <p align="justify"><font color="#000080">Welcome to the 2009 Indian
                        River Lagoon Photo Gallery.&nbsp; We are pleased to highlight the
                        creativity of the many local photographers who contributed their
                        work to the annual Indian River Lagoon Photo Contest, sponsored
                        by the St. Johns River and South Florida Water Management Districts
                        and Environmental Consulting and Technology, Inc.&nbsp; Thanks to
                        everyone who participated!<br>
                        <br>
                        Choose a category below to enter the gallery and view this year's
                        entries:</font></p>
                <p align="justify"><font color="#000080"><a href="09calpics.php">Winning
                            Photos &amp; Runners-Up</a></font></p>
                <p align="justify"><font color="#000080"><a href="09SpeciesPICS.php">Lagoon
                            Wildlife </a></font></p>
                <p align="justify"><font color="#000080"><a href="09IRLRecreation.php">Work
                            &amp; Play on the IRL</a></font></p>
                <p align="justify"><font color="#000080"><a href="09IRLLandscapesEtc.php">Landmarks &
                            Landscapes</a></font></p>
                <p align="justify"><font color="#000080"><a href="09SunriseSunset.php">Sunrise
                            &amp; Sunset</a></font></p>
                <p align="justify">&nbsp;</p>
                <p align="center"><b><font color="#000080" size="2">&nbsp;All photographs
                            in this gallery are the property of the individual photographer
                            and cannot be duplicated in any way without express permission.</font></b></td>
        </tr>
        <tr>
            <td>
                <p align="center">&nbsp;
                <p align="center"><font color="#000080">Want to contribute a photograph to
                        the Gallery?&nbsp; Contact</font><font size="2" color="#000080">:<br>
                        <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
                <p align="center">&nbsp;</td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
