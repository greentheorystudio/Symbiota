<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>2008 Indian River Lagoon Photo Gallery</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <table style="width:612px;margin-left:auto;margin-right:auto;">
        <tr>
            <td width="606"><p align="center"><br>
                    <img src="../content/imglib/08PettyL1cover.jpg" width="436" height="295"></p>
                <p align="justify"><font color="#000080">Welcome to the 2008 Indian
                        River Lagoon Photo Gallery.&nbsp; We are pleased to highlight the
                        creativity of the many local photographers who contributed their
                        work to the annual Indian River Lagoon Photo Contest, sponsored
                        by the St. Johns River and South Florida Water Management Districts
                        and Environmental Consulting and Technology, Inc.&nbsp; Thanks to
                        everyone who participated!<br>
                        <br>
                        Choose a category below to enter the gallery and view this year's entries:</font></p>
                <p align="center"><font color="#000080"><a href="../content/imglib/calpics08.php">Winning
                            Photos</a></font></p>
                <p align="center"><font color="#000080"><a href="SpeciesPics08.php">Animals
                            of the IRL</a></font></p>
                <p align="center"><font color="#000080">
                        <a href="IRLRecreation08.php">Lagoon Recreation and Other
                            Uses</a></font></p>
                <p align="center"><font color="#000080"><a href="LandmarksEtc08.php">Landmarks
                            and Landscapes</a></font></p>
                <p align="center"><font color="#000080"><a href="SunriseSunset08.php">Sunrise
                            and Sunset</a></font></p>
                <p align="justify">&nbsp;</p>
                <p align="center"><b><font color="#000080" size="2">&nbsp;All photographs
                            in this gallery are the property of the individual photographer
                            and can not be duplicated in any way without express permission.</font></b><font
                            color="#000080" size="2"><br>
                    </font>
                <p align="center"><font color="#000080" size="2">Titles of all photographs
                        were supplied by participating photographers. Errors in species
                        identification were corrected where necessary.</font>
                <p align="center">&nbsp;
            </td>
        </tr>
        <tr>
            <td><p align="center"><font color="#000080">Want to contribute a photograph to
                        the Gallery?&nbsp; Contact</font><font size="2" color="#000080">:<br>
                        <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
                </p>>&nbsp;
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>