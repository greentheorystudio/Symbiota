<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>2012 Work and Play on the IRL</title>
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
    <h2>2012 Work and Play on the IRL</h2>
    <table style="width:550px;margin-left:auto;margin-right:auto;" cellpadding="5">
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/12ArnoldJ086A_LG.jpg"><img
                            src="../content/imglib/12AnnamR036A_SM.jpg" width="125" height="100"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12BottJ039B_LG.jpg"><img
                            src="../content/imglib/12BottJ039B_SM.jpg" width="125" height="84"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12CurlB029A_LG.jpg"><img
                            src="../content/imglib/12CurlB029A_SM.jpg" width="125" height="84"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12FriendL074C_LG.jpg"><img
                            src="../content/imglib/12FriendL074C_SM.jpg" width="125" height="83"/></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Kayker at Dawn</i><br/>
           <br/>
           Jerry Arnold
           Rockledge, FL</span></td>
            <td align="center" valign="top"><span class="caption"><i>Flats Fishin' </i><br/>
           <br/>
           Jon Bott<br/>
           Palm Bay, FL<br/>

           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Anchored Sunrise</i><br/>
           <br/>
           Brian Curl<br/>
           Palm Bay, FL <br/>

           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Artist</i><br/>
           <br/>
           Lorelle Friend<br/>
           New Smyrna Beach, FL<br/>
           <br/>
           <br/>
           </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/12KrissakR022BLG.jpg"><img
                            src="../content/imglib/12KrissakR022B_SM.jpg" width="125" height="83"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12LinderD051A_LG.jpg"><img
                            src="../content/imglib/12LinderD051A_SM.jpg" width="125" height="83"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12MisthynP031A_LG.jpg"><img
                            src="../content/imglib/12MisthynP031A_SM.jpg" width="125" height="83"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12MitchellD043B_LG.jpg"><img
                            src="../content/imglib/12MitchellD043B_SM.jpg" width="125" height="93"/></a></td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Happiness is a Blue Lagoon</i><br/>
           <br/>
           Bob Krissak<br/>
           Melbourne, FL<br/>
           <br/>
           <br/>
           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Kayak River View</i><br/>
           <br/>
           Daniel Linder<br/>
           Titusville, FL<br/>
           <br/>
           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Eventide Fishermen</i><br/>
           <br/>
           Pamela Mistyhn<br/>
           Mims, FL <br/>
           <br/>
           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Heading Out</i><br/>
           <br/>
           Diane Mitchell<br/>
           Cocoa, FL <br/>
           <br/>
           </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/12MitchellD043C_LG.jpg"><img
                            src="../content/imglib/12MitchellD043C_SM.jpg" width="125" height="72"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12TherrienP088A_LG.jpg"><img
                            src="../content/imglib/12TherrienP088A_SM.jpg" width="125" height="94"/></a></td>
            <td align="center" valign="top"><a href="../content/imglib/12VirgilioM001B_LG.jpg"><img
                            src="../content/imglib/12VirgilioM001B_SM.jpg" width="125" height="94"/></a></td>
            <td align="center" valign="top">&nbsp;</td>
        </tr>
        <tr>
            <td align="center" valign="top"><span class="caption"><i>Fishing Birds</i><br/>
           <br/>
           Diane Mitchell<br/>
           Cocoa, FL <br/>
           <br/>
           </span></td>
            <td align="center" valign="top"><span class="caption"><i>Cool Dude</i><br/>
           <br/>
           Patty Therrien<br/>
           <br/>
           <br/>
           </span></td>
            <td align="center" valign="top"><span class="caption"><i>BC47 Sunset</i><br/>
           <br/>
           Marc Virgilio<br/>
           West Melbourne, FL<br/>
           </span></td>
            <td align="center" valign="top">&nbsp;</td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
