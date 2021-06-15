<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Winning Photos &amp; Runners-Up</title>
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
    <table border="0" cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border-color:#111111;width:545px;margin-left:auto;margin-right:auto;">
        <tr>
            <td height="57" colspan="4" align="center"><p align="center"><font size="5" color="#008080"><b>&nbsp;Winning
                            Photos &amp; Runners-Up</b></font></p></td>
        </tr>
        <tr>
            <td width="139" align="center" height="109"><p align="center"><a href="../content/imglib/05AngyJ3.jpg"><font
                                size="2">
                        </font></a><a href="../content/imglib/09BolonM1.jpg"><img
                                src="../content/imglib/09BolonM1_small.jpg" width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="109"><p align="center"><a
                            href="../content/imglib/05BowmanM3.jpg"><font size="2">
                        </font></a><a href="../content/imglib/09CarverT1.jpg"><img
                                src="../content/imglib/09CarverT1_small.jpg" width="125" height="83" border="0"></a>
            </td>
            <td width="135" align="center" height="109"><p align="center"><a
                            href="../content/imglib/05CarterC3.jpg"><font size="2">
                        </font></a><a href="../content/imglib/09CorapiP1.jpg"><img
                                src="../content/imglib/09CorapiP1_small.jpg" width="125" height="83" border="0"></a>
            </td>
            <td width="134" align="center" height="109"><a href="../content/imglib/05CohenG1.jpg"><font size="2">
                    </font></a><img src="../content/imglib/09CorbeilC1_small.jpg" width="125" height="87"></td>
        </tr>
        <tr valign="top">
            <td width="139" height="79" align="center"><font color="#000080" size="2">
                    &quot;Picnic on the Lagoon&quot;<br>
                    Min Bolon<br>
                    Micco, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="79" align="center"><font color="#000080" size="2">
                    &quot;Tailing Reds&quot;<br>
                    Tom Carver<br>
                    Orlando, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="79" align="center"><font size="2" color="#000080">
                    &quot;Gotcha!&quot;<br>
                    Patricia Corapi<br>
                    Indialantic, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="79" align="center"><font size="2" color="#000080">&quot;Tri-Colored
                    Heron Fishing&quot;<br>
                    Charles Corbiel<br>
                    Melbourne, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="103"><a href="../content/imglib/05CohenG2.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09CoxJ1.jpg"><img src="../content/imglib/09CoxJ1_small.jpg"
                                                                            width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="103"><a href="../content/imglib/05CohenG3.jpg"><font size="2">
                    </font></a><img src="../content/imglib/09EbaughT1_small.jpg" width="125" height="100"></td>
            <td width="135" align="center" height="103"><a href="../content/imglib/05FiorilloT3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09ErgleB1.jpg"><img
                            src="../content/imglib/09ErgleB1_small.jpg" width="125" height="93" border="0"></a></td>
            <td width="134" align="center" height="103"><a href="../content/imglib/05HamblinM2.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09FrenchD1.jpg"><img
                            src="../content/imglib/09FrenchD1_small.jpg" width="125" height="100" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="79" align="center"><font size="2" color="#000080">&quot;Osprey
                    Lunch&quot;<br>
                    Jack Lamarr Cox<br>
                    Melbourne, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="79" align="center"><font size="2" color="#000080">
                    &quot;Sunset Kiss&quot;<br>
                    Tim Ebaugh<br>
                    Melbourne, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="79" align="center"><font size="2" color="#000080">
                    &quot;X Marks the Spot&quot;<br>
                    Bonnie Ergle<br>
                    Ft. Pierce, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="79" align="center"><font size="2" color="#000080">&quot;Orb&quot;<br>
                    Dennis French<br>
                    Palm Bay, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="105"><a href="../content/imglib/05HoffmanG4.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09FrenchD2.jpg"><img
                            src="../content/imglib/09FrenchD2_small.jpg" width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="105"><a href="../content/imglib/05HolmP3.jpg">
                    <font size="2"> </font></a><a href="../content/imglib/09GeigerB1.jpg"><img
                            src="../content/imglib/09GeigerB1_small.jpg" width="125" height="99" border="0"></a></td>
            <td width="135" align="center" height="105"><a href="../content/imglib/05JarvisK3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09HigginbothamT1.jpg"><img
                            src="../content/imglib/09HigginbothamT1_small.jpg" width="125" height="94" border="0"></a>
            </td>
            <td width="134" align="center" height="105"><a href="../content/imglib/05LambertC1.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09HughesA1.jpg"><img
                            src="../content/imglib/09HughesA1_small.jpg" width="125" height="100" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="82" align="center"><font size="2" color="#000080">
                    &quot;Listing at Low Water&quot;<br>
                    Dennis French<br>
                    Palm Bay, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="82" align="center"><font size="2" color="#000080">
                    &quot;Fishing at Sunrise&quot;<br>
                    Bridget Geiger<br>
                    Melbourne, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="82" align="center"><font size="2" color="#000080">
                    &quot;Golden Fishing Morning&quot;<br>
                    Timothy Higginbotham<br>
                    Ft. Pierce, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="82" align="center"><font size="2" color="#000080">&quot;Ponce
                    Inlet Light&quot;<br>
                    Allan Hughes<br>
                    Melbourne Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="104"><a href="../content/imglib/09KendrickB1.jpg"><img
                            src="../content/imglib/09KendrickB1_small.jpg" width="125" height="93" border="0"></a></td>
            <td align="center" height="104"><a href="../content/imglib/09LawrenceL1.jpg"><img
                            src="../content/imglib/09LawrenceL1_small.jpg" width="125" height="100" border="0"></a></td>
            <td align="center" height="104"><a href="../content/imglib/09McMurtreyO1.jpg"><img
                            src="../content/imglib/09McMurtreyO1_small.jpg" width="125" height="89" border="0"></a></td>
            <td align="center" height="104"><a href="../content/imglib/09MoyC1.jpg"><img
                            src="../content/imglib/09MoyC1_small.jpg" width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="79" align="center"><font size="2" color="#000080">&quot;Bait
                    Fishing&quot;<br>
                    Ben Kendrick<br>
                    Melbourne Beach, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font size="2" color="#000080">&quot;Sunset
                    Snack&quot;<br>
                    Lola Lawrence<br>
                    New Smyrna Beach, FL<br>
                    <br>
                    <br>
                </font></td>
            <td height="79" align="center"><font size="2" color="#000080">&quot;Zebra
                    Heliconian&quot;<br>
                    Owen McMurtrey<br>
                    Chicago, IL<br>
                    <br>
                </font></td>
            <td height="79" align="center"><font size="2" color="#000080">&quot;Treasures
                    in the Lagoon&quot;<br>
                    Caroline Moy<br>
                    Micco, FL <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="104"><a href="../content/imglib/05LawrenceL3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09RichardsR1.jpg"><img
                            src="../content/imglib/09RichardsR1_small.jpg" width="125" height="83" border="0"></a></td>
            <td width="137" align="center" height="104"><a href="../content/imglib/05LilianthalP2.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09RingM1.jpg"><img src="../content/imglib/09RingM1_small.jpg"
                                                                             width="125" height="83" border="0"></a>
            </td>
            <td width="135" align="center" height="104"><a href="../content/imglib/05McVayH3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09RingM2.jpg"><img src="../content/imglib/09RingM2_small.jpg"
                                                                             width="125" height="83" border="0"></a>
            </td>
            <td width="134" align="center" height="104"><a href="../content/imglib/05MetzJ1.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09RogersJ1.jpg"><img
                            src="../content/imglib/09RogersJ1_small.jpg" width="125" height="90" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Mist
                    on a Winter Morning&quot;<br>
                    Renee Richards<br>
                    New Smyrna Beach, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font size="2" color="#000080"> &quot;Morning
                    Rush Hour&quot;<br>
                    Mike Ring<br>
                    Edgewater, FL<br>
                    <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font size="2" color="#000080"> &quot;Lagoon
                    Sunset&quot;<br>
                    Mike Ring<br>
                    Edgewater, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font size="2" color="#000080"> &quot;Snowy
                    Egret on the Go&quot;<br>
                    Jack Rogers<br>
                    Oviedo, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="79"><a href="../content/imglib/05MurrayR3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09RogersR1.jpg"><img
                            src="../content/imglib/09RogersR1_small.jpg" width="125" height="100" border="0"></a></td>
            <td width="137" align="center" height="79"><a href="../content/imglib/05NicksonR3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09StewartF1.jpg"><img
                            src="../content/imglib/09StewartF1_small.jpg" width="125" height="100" border="0"></a></td>
            <td width="135" align="center" height="79"><a href="../content/imglib/05PerryE3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09ThomanR1.jpg"><img
                            src="../content/imglib/09ThomanR1_small.jpg" width="125" height="83" border="0"></a></td>
            <td width="134" align="center" height="79"><a href="../content/imglib/05PlachterE2.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09VerderameK1.jpg"><img
                            src="../content/imglib/09VerderameK1_small.jpg" width="125" height="100" border="0"></a>
            </td>
        </tr>
        <tr valign="top">
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Dinner
                    Time&quot;<br>
                    Rance Rogers<br>
                    Perrysburg, OH<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Making
                    Waves&quot;<br>
                    Fran Stewart<br>
                    Melbourne Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Marina
                    Reflection&quot;<br>
                    Roy Thoman<br>
                    Titusville, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Causeway
                    Fireworks&quot;<br>
                    Ken Vanderame<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="79"><a href="../content/imglib/05RoseJ1.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09WaiteJ1.jpg"><img
                            src="../content/imglib/09WaiteJ1_small.jpg" width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="79"><a href="../content/imglib/05SautnerG3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09WakemanJ1.jpg"><img
                            src="../content/imglib/09WakemanJ1_small.jpg" width="125" height="100" border="0"></a></td>
            <td width="135" align="center" height="79"><a href="../content/imglib/05SavaryL3.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09WhiticarJ1.jpg"><img
                            src="../content/imglib/09WhiticarJ1_small.jpg" width="125" height="91" border="0"></a></td>
            <td width="134" align="center" height="79"><a href="../content/imglib/05ShaferC3.jpg">
                    <font size="2"> </font></a><a href="../content/imglib/09WhiticarJ2.jpg"><img
                            src="../content/imglib/09WhiticarJ2_small.jpg" width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Egg
                    &amp; I&quot;<br>
                    John Waite<br>
                    Vero Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Stormy
                    Sunset Paddle&quot;<br>
                    John Wakeman<br>
                    Stuart, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Hutchinson
                    Island Waterspout&quot;<br>
                    John Whiticar<br>
                    Jensen Beach, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Red
                    Mangroves, Summer Clouds&quot;<br>
                    John Whiticar<br>
                    Jensen Beach, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="77"><a href="../content/imglib/05VaughanJ2.jpg"><font size="2">
                    </font></a><a href="../content/imglib/09WhiticarJ3.jpg"><img
                            src="../content/imglib/09WhiticarJ3_small.jpg" width="125" height="95" border="0"></a></td>
            <td width="137" align="center" height="77"><a href="../content/imglib/05WakemanJ2.jpg">
                    <font size="2"> </font></a><a href="../content/imglib/09WinegarP1.jpg"><img
                            src="../content/imglib/09WinegarP1_small.jpg" width="125" height="89" border="0"></a></td>
            <td width="135" align="center" height="77"><a href="../content/imglib/05WakemanJ3.jpg"><font size="2">
                    </font></a></td>
            <td width="134" align="center" height="77"><a href="../content/imglib/05WhiticarJ2.jpg"><font size="2">
                    </font></a></td>
        </tr>
        <tr valign="top">
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Whelk
                    Eggs on the Sandbar&quot;<br>
                    John Whiticar<br>
                    Jensen Beach, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="79" align="center"><font color="#000080" size="2"> &quot;Great
                    Egret Displaying&quot;<br>
                    Pam Winegar<br>
                    Malabar, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="79" align="center">&nbsp;</td>
            <td width="134" height="79" align="center">&nbsp;</td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
