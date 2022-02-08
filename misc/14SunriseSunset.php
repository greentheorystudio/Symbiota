<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>2014 Sunrise and Sunset on the IRL</title>
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
    <h2>2014 Sunrise and Sunset on the IRL</h2>
    <table cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:0;border-color:#111111;width:556px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center" valign="top">
                <p align="center"><font size="2" color="#000080">&nbsp;&nbsp;</font><a
                            href="../content/imglib/14ClarkN40_lg.jpg"><img src="../content/imglib/14ClarkN40_sm.jpg"
                                                                            width="125" height="83"></a></td>
            <td align="center" valign="top"><p align="center"><a href="../content/imglib/14EbaughT46_lg.jpg"><img
                                src="../content/imglib/14EbaughT46_sm.jpg" width="125" height="100"></a></td>
            <td align="center" valign="top">
                <p align="center"><a href="images/05BesanconC2.jpg"> </a><a
                            href="../content/imglib/14HillT56_lg.jpg"><img src="../content/imglib/14HillT56_sm.jpg"
                                                                           width="125" height="100"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14KrantzK664_lg.jpg"><img
                            src="../content/imglib/14KrantzK664_sm.jpg" width="125" height="89"></a></td>
        </tr>
        <tr valign="top">
            <td align="center" valign="top"><span class="caption"><i>Dawn of a New Flight</i><br>
              <br/>
              Nicholas Clark<br>
            Winter Springs, FL</span>
                <br/></td>
            <td align="center" valign="top"><span class="caption"><i>Nature's paintbrush</i><br>
              <br/>
              Tim Ebaugh<br>
            Melbourne, FL
            <br/>
          </span></td>
            <td align="center" valign="top"><span class="caption"><i>Rainy Sunset</i><br>
              <br/>
              Tony Hill<br>
            Sebastian, FL
            <br/>
          </span></td>
            <td align="center" valign="top"><span class="caption"><i>Spring Evening</i><br>
              <br/>
              Karen Krantz<br>
            Edgewater, FL<br/>
            <br>
            </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/14LeClairB68_lg.jpg"><img
                            src="../content/imglib/14LeClairB68_sm.jpg" width="125" height="100"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14LinderJ70_lg.jpg"><img
                            src="../content/imglib/14LinderJ70_sm.jpg" width="125" height="94"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14McEwenE74_lg.jpg"><img
                            src="../content/imglib/14McEwenE74_sm.jpg" width="125" height="100"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14McWilliamsC83_lg.jpg"><img
                            src="../content/imglib/14McWilliamsC83_sm.jpg" width="125" height="84"></a></td>
        </tr>
        <tr valign="top">
            <td align="center" valign="top"><span class="caption"><i>Lazy Bay Sunset</i><br>
              <br/>
              Bob LeClair<br>
            Port Orange, FL
            <br/>
            <br>
            </span></td>
            <td align="center" valign="top"><span class="caption"><i>Storm at Sunset</i><br>
              <br/>
              Jolynn Linder<br>
            Titusville, FL
          </span><br/></td>
            <td align="center" valign="top"><span class="caption"><i>Glorious Sunrise</i><br>
              <br/>
              Edward McEwen<br>
            Palm Bay, FL <br/>
          </span></td>
            <td align="center" valign="top"><span class="caption"><i>Shores of Shells</i><br>
              <br/>
              Celeste McWilliams<br>
            Vero Beach, FL
            <br/>
            <br>
            </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/14PichonK102_lg.jpg"><img
                            src="../content/imglib/14PichonK102_sm.jpg" width="125" height="96"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14ReedS105_lg.jpg"><img
                            src="../content/imglib/14ReedS105_sm.jpg" width="125" height="94"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14RichardsonD111_lg.jpg"><img
                            src="../content/imglib/14RichardsonD111_sm.jpg" width="125" height="70"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14RichardsonD113_lg.jpg"><img
                            src="../content/imglib/14RichardsonD113_sm.jpg" width="125" height="70"></a></td>
        </tr>
        <tr valign="top">
            <td align="center" valign="top"><span class="caption"><i>Sunrise Over the Indian River</i><br>
              <br/>
              Kenneth Pichon<br>
            Melbourne, FL</span>
            </td>
            <td align="center" valign="top"><span class="caption"><i>Ft.Pierce Sunrise</i><br>
              <br/>
              Sherry Reed<br>
            Ft. Pierce, FL
            <br>
              </span></td>
            <td align="center" valign="top"><span class="caption"><i>Goodmorning Brevard!</i><br>
              <br/>
              Dan Richardson<br>
            Cocoa, FL
            </span></td>
            <td align="center" valign="top"><span class="caption"><i>Reflections</i><br>
              <br/>
              Dan Richardson<br>
Cocoa, FL <br/>
<br>
            </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/14RichardsonP108_lg.jpg"><img
                            src="../content/imglib/14RichardsonP108_sm.jpg" width="125" height="70"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14RichardsonP110_lg.jpg"><img
                            src="../content/imglib/14RichardsonP110_sm.jpg" width="125" height="70"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14Stanley-QR124_lg.jpg"><img
                            src="../content/imglib/14Stanley-QR124_sm.jpg" width="125" height="83"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14TweedieD137_lg.jpg"><img
                            src="../content/imglib/14TweedieD137_sm.jpg" width="125" height="94"></a></td>
        </tr>
        <tr valign="top">
            <td align="center" valign="top"><span class="caption"><i>Our Driveway Sunrise</i><br>
              <br/>
              Peggy Richardson<br>
            Cocoa Beach, FL
            </span></td>
            <td align="center" valign="top"><span class="caption"><i>Sweet Summer Sunrise</i><br>
              <br/>
              Peggy Richardson<br>
Cocoa Beach, FL <br>
<br>
<br>
            </span></td>
            <td align="center" valign="top"><span class="caption"><i>The Coming of Day</i><br>
              <br/>
              Ruth Stanley-Quillian<br>
            Cocoa, FL
            </span></td>
            <td align="center" valign="top"><span class="caption"><i>Rivrside Sunset</i><br>
              <br/>
              Deborah Tweedie<br>
            Inidan Harbor Beach, FL
          </span></td>
        </tr>
        <tr>
            <td align="center" valign="top"><a href="../content/imglib/14WarnockJ147_lg.jpg"><img
                            src="../content/imglib/14WarnockJ147_sm.jpg" width="125" height="167"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14WilliamsJR149_lg.jpg"><img
                            src="../content/imglib/14WilliamsJR149_sm.jpg" width="125" height="91"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14WillinowA150_lg.jpg"><img
                            src="../content/imglib/14WillinowA150_sm.jpg" width="125" height="90"></a></td>
            <td align="center" valign="top"><a href="../content/imglib/14WillinowA151_lg.jpg"><img
                            src="../content/imglib/14WillinowA151_sm.jpg" width="125" height="94"></a></td>
        </tr>
        <tr valign="top">
            <td align="center" valign="top"><span class="caption"><i>River Sunset</i><br>
              <br/>
              Joshua Warnock<br>
            Edgewater, FL
            <br>
  <br>
  &nbsp;</span></td>
            <td align="center" valign="top"><span class="caption"><i>Royal Palm Point Sunrise</i><br>
              <br/>
              J. R. Williams<br>
            Vero Beach, FL
            <br>
  <br>
  <br>
  &nbsp;</span></td>
            <td align="center" valign="top"><span class="caption"><i>Daybreak in Vero</i><br>
              <br/>
              Arlene Willinow<br>
            Ft. Pierce, FL
            <br>
  <br>
  &nbsp;</span></td>
            <td align="center" valign="top"><span class="caption"><i>Sunrise at Wilcox</i><br>
              <br/>
              Arlene Willinow<br>
Ft. Pierce, FL <br>
          </span></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
