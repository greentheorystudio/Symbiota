<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Sunrise and Sunset on the IRL</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <table cellpadding="0" cellspacing="0"
           style="border-collapse:collapse;border:0;border-color:#111111;width:556px;margin-left:auto;margin-right:auto;">
        <tr>
            <td height="70" colspan="4" align="center"><p align="center"><font size="5" color="#008080"><b>&nbsp;Sunrise
                            and Sunset on the IRL&nbsp; </b></font></p></td>
        </tr>
        <tr valign="middle">
            <td width="139" height="108" align="center">
                <p align="center"><font size="2" color="#000080">&nbsp;&nbsp;<a
                                href="../content/imglib/09BarriosL1.jpg"><img
                                    src="../content/imglib/09BarriosL1_small.jpg" width="125" height="94"
                                    border="0"></a></font></td>
            <td width="137" height="108" align="center">
                <p align="center"><a href="../content/imglib/05BesanconC1.jpg"> </a><a
                            href="../content/imglib/09BatsisN1.jpg"><img src="../content/imglib/09BatsisN1_small.jpg"
                                                                         width="125" height="83" border="0"></a></td>
            <td width="135" height="108" align="center">
                <p align="center"><a href="../content/imglib/05BesanconC2.jpg"> </a><a
                            href="../content/imglib/09BatsisN3.jpg"><img src="../content/imglib/09BatsisN3_small.jpg"
                                                                         width="125" height="83" border="0"></a></td>
            <td width="134" height="108" align="center"><a href="../content/imglib/05BonannoA1.jpg">
                </a><a href="../content/imglib/09BiegaD3.jpg"><img src="../content/imglib/09BiegaD3_small.jpg"
                                                                   width="125" height="83" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="94" align="center"><font size="2" color="#000080">
                    &quot;Heron Silhouette at Dawn&quot;<br>
                    Louis Barrios<br>
                    Sebastian, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="94" align="center"><font size="2" color="#000080">
                    &quot;A Peek Through the Clouds&quot;<br>
                    Natalie Batsis<br>
                    Palm Bay, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="94" align="center"><font size="2" color="#000080">
                    &quot;Pastels in the Sky&quot;<br>
                    Natalie Batsis<br>
                    Palm Bay, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="94" align="center"><font size="2" color="#000080">
                    &quot;Sunrise&quot;<br>
                    David Biega<br>
                    Merritt Island, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="103"><a href="../content/imglib/05BonnanoA3.jpg">
                </a><a href="../content/imglib/09CareyJ1.jpg"><img src="../content/imglib/09CareyJ1_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="103"><a href="../content/imglib/05BoudreauxM2.jpg">
                </a><a href="../content/imglib/09CastilloR1.jpg"><img src="../content/imglib/09CastilloR1_small.jpg"
                                                                      width="125" height="89" border="0"></a></td>
            <td width="135" align="center" height="103"><a href="../content/imglib/05BouvierM1.jpg">
                </a><a href="../content/imglib/09ChatzkyD1.jpg"><img src="../content/imglib/09ChatzkyD1_small.jpg"
                                                                     width="125" height="83" border="0"></a></td>
            <td width="134" align="center" height="103"><a href="../content/imglib/05BrooksA1.tif.jpg">
                </a><a href="../content/imglib/09CoesterS1.jpg"><img src="../content/imglib/09CoesterS1_small.jpg"
                                                                     width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="94" align="center"><font size="2" color="#000080">
                    &quot;Island Sunset&quot;<br>
                    Joy Carey<br>
                    Sebastian, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="94" align="center"><font size="2" color="#000080">
                    &quot;Retirement on the Lagoon&quot;<br>
                    Rudy Castillo<br>
                    Titusville, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="94" align="center"><font size="2" color="#000080">
                    &quot;Marina Reflections&quot;<br>
                    David Chatzky<br>
                    Indian Harbor Beach, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="94" align="center"><font size="2" color="#000080">
                    &quot;Sunrise at Viera Wetlands&quot;<br>
                    Stephen Coester<br>
                    Rockledge, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="100"><a href="../content/imglib/05BrownD1.jpg">
                </a><a href="../content/imglib/09CoxJ2.jpg"><img src="../content/imglib/09CoxJ2_small.jpg" width="125"
                                                                 height="94" border="0"></a></td>
            <td width="137" align="center" height="100"><a href="../content/imglib/05BrownD2.jpg">
                </a><a href="../content/imglib/09CoxJ4.jpg"><img src="../content/imglib/09CoxJ4_small.jpg" width="125"
                                                                 height="94" border="0"></a></td>
            <td width="135" align="center" height="100"><a href="../content/imglib/05BrownD3.jpg">
                </a><a href="../content/imglib/09CoxJ3.jpg"><img src="../content/imglib/09CoxJ3_small.jpg" width="125"
                                                                 height="94" border="0"></a></td>
            <td width="134" align="center" height="100"><a href="../content/imglib/05BudlangK1.jpg">
                </a><a href="../content/imglib/09EnglandG1.jpg"><img src="../content/imglib/09EnglandG1_small.jpg"
                                                                     width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="94" align="center"><font size="2" color="#000080">
                    &quot;Dawn's Approach&quot;<br>
                    Judith Cox<br>
                    Edgewater, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="94" align="center"><font size="2" color="#000080">
                    &quot;Twilight in Edgewater&quot;<br>
                    Judith Cox<br>
                    Edgewater, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="94" align="center"><font size="2" color="#000080">
                    &quot;Sensational Sunrise&quot;<br>
                    Judith Cox<br>
                    Edgewater, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="94" align="center"><font size="2" color="#000080">
                    &quot;Thousand Islands Sunset&quot;<br>
                    Gordon England<br>
                    Cocoa Beach, FL<br>
                    &nbsp;<br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="95"><a href="../content/imglib/05BurdetteL2.jpg">
                </a><a href="../content/imglib/09ErgleB3.jpg"><img src="../content/imglib/09ErgleB3_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="95"><a href="../content/imglib/05CarterC1.jpg">
                </a><a href="../content/imglib/09FiorilloT3.jpg"><img src="../content/imglib/09FiorilloT3_small.jpg"
                                                                      width="125" height="100" border="0"></a></td>
            <td width="135" align="center" height="95"><a href="../content/imglib/05ClintonR1.jpg">
                </a><a href="../content/imglib/09FitzpatrickD1.jpg"><img
                            src="../content/imglib/09FitzpatrickD1_small.jpg" width="125" height="83" border="0"></a>
            </td>
            <td width="134" align="center" height="95"><a href="../content/imglib/05ClintonR2.jpg">
                </a><a href="../content/imglib/09FosselmanK3.jpg"><img src="../content/imglib/09FosselmanK3_small.jpg"
                                                                       width="125" height="100" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="83" align="center">&quot;<font size="2" color="#000080">Painting
                    a Sunrise&quot;<br>
                    Bonnie Ergle<br>
                    Ft. Pierce, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="83" align="center"><font size="2" color="#000080">
                    &quot;New Day Dawning&quot;<br>
                    Teresa Fiorillo<br>
                    Titusville, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="83" align="center"><font size="2" color="#000080">
                    &quot;Sunset&quot;<br>
                    Denise Fitzpatrick<br>
                    Melbourne, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="83" align="center"><font size="2" color="#000080">
                    &quot;Stuart Sunrise&quot;<br>
                    Kathleen Fosselman<br>
                    Boynton Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="101"><a href="../content/imglib/05ColvinB1.jpg">
                </a><a href="../content/imglib/09FreemanN2.jpg"><img src="../content/imglib/09FreemanN2_small.jpg"
                                                                     width="125" height="102" border="0"></a></td>
            <td width="137" align="center" height="101"><a href="../content/imglib/05ColvinB2.jpg">
                </a><a href="../content/imglib/09FreemanN3.jpg"><img src="../content/imglib/09FreemanN3_small.jpg"
                                                                     width="125" height="101" border="0"></a></td>
            <td width="135" align="center" height="101"><a href="../content/imglib/05DeanS1.tif.jpg">
                </a><a href="../content/imglib/09FrenchD3.jpg"><img src="../content/imglib/09FrenchD3_small.jpg"
                                                                    width="125" height="100" border="0"></a></td>
            <td width="134" align="center" height="101"><a href="../content/imglib/05EnnisR1.jpg">
                </a><a href="../content/imglib/09FryW1.jpg"><img src="../content/imglib/09FryW1_small.jpg" width="125"
                                                                 height="83" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="84" align="center"><font color="#000080" size="2">&quot;End
                    of the Day 1&quot;<br>
                    Nichole Freeman<br>
                    Titusville, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="84" align="center"><font size="2" color="#000080">
                    &quot;End of the Day 2&quot;<br>
                    Nichole Freeman<br>
                    Titusville, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="84" align="center"><font size="2" color="#000080">
                    &quot;Osprey on Guard&quot;<br>
                    Dennis French<br>
                    Palm Bay, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="84" align="center"><font size="2" color="#000080">
                    &quot;Driftwood&quot;<br>
                    William Fry<br>
                    Indialantic, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="104"><a href="../content/imglib/05FiorilloT1.jpg">
                </a><a href="../content/imglib/09GordianG3.jpg"><img src="../content/imglib/09GordianG3_small.jpg"
                                                                     width="125" height="80" border="0"></a></td>
            <td width="137" align="center" height="104"><a href="../content/imglib/05GagnierB1.jpg">
                </a><a href="../content/imglib/09GuilesB2.jpg"><img src="../content/imglib/09GuilesB2_small.jpg"
                                                                    width="125" height="94" border="0"></a></td>
            <td width="135" align="center" height="104"><a href="../content/imglib/05HilligK1.jpg">
                </a><a href="../content/imglib/09HechtV1.jpg"><img src="../content/imglib/09HechtV1_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="134" align="center" height="104"><a href="../content/imglib/05HipsonD3.jpg">
                </a><a href="../content/imglib/09HechtV2.jpg"><img src="../content/imglib/09HechtV2_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="94" align="center"><font color="#000080" size="2">
                    &quot;Untitled&quot;<br>
                    Gil Gordian<br>
                    Sebastian, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="94" align="center"><font size="2" color="#000080">
                    &quot;Dawn's Desire&quot;<br>
                    Brenda Guiles<br>
                    Palm Bay, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="94" align="center"><font size="2" color="#000080">
                    &quot;Calm Waters&quot;<br>
                    Valerie Hecht<br>
                    Cocoa, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="94" align="center"><font size="2" color="#000080">&quot;Mystical
                    Sunrise&quot;<br>
                    Valerie Hecht<br>
                    Cocoa, FL <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="103"><a href="../content/imglib/05HodginD1.jpg">
                </a><a href="../content/imglib/09HigginbothamT1.jpg"><img
                            src="../content/imglib/09HigginbothamT1_small.jpg" width="125" height="94" border="0"></a>
            </td>
            <td width="137" align="center" height="103"><a href="../content/imglib/05HodginD2.jpg">
                </a><a href="../content/imglib/09HigginbothamT3.jpg"><img
                            src="../content/imglib/09HigginbothamT3_small.jpg" width="125" height="83" border="0"></a>
            </td>
            <td width="135" align="center" height="103"><a href="../content/imglib/05HodginD3.jpg">
                </a><a href="../content/imglib/09HighsmithW2.jpg"><img src="../content/imglib/09HighsmithW2_small.jpg"
                                                                       width="125" height="94" border="0"></a></td>
            <td width="134" align="center" height="103"><a href="../content/imglib/05HoffmanG1.jpg">
                </a><a href="../content/imglib/09HillM1.jpg"><img src="../content/imglib/09HillM1_small.jpg" width="125"
                                                                  height="83" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="74" align="center"><font size="2" color="#000080">
                    &quot;Golden Fishing Morning&quot;<br>
                    Timothy Higginbotham<br>
                    Ft. Pierce, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="74" align="center"><font size="2" color="#000080">
                    &quot;Into the Light&quot;<br>
                    Timothy Higginbotham<br>
                    Ft. Pierce, FL<br>
                    &nbsp;</font></td>
            <td width="135" height="74" align="center"><font size="2" color="#000080">
                    &quot;Eau Gallie Dawn&quot;<br>
                    William Highsmith<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="74" align="center"><font size="2" color="#000080">
                    &quot;Ft. Pierce Inlet, North Channel&quot;<br>
                    Mark Hill<br>
                    Stuart, FL <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="100"><a href="../content/imglib/05HoggardR1.jpg">
                </a><a href="../content/imglib/09HillM2.jpg"><img src="../content/imglib/09HillM2_small.jpg" width="125"
                                                                  height="83" border="0"></a></td>
            <td width="137" align="center" height="100"><a href="../content/imglib/05HoldenJ1.jpg">
                </a><a href="../content/imglib/09HillsR3.jpg"><img src="../content/imglib/09HillsR3_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="135" align="center" height="100"><a href="../content/imglib/05HuckB1.jpg">
                </a><a href="../content/imglib/09HillsR2.jpg"><img src="../content/imglib/09HillsR2_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="134" align="center" height="100"><a href="../content/imglib/05KappelZ2.jpg">
                </a><a href="../content/imglib/09HoranM1.jpg"><img src="../content/imglib/09HoranM1_small.jpg"
                                                                   width="125" height="83" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="94" align="center"><font size="2" color="#000080">
                    &quot;Untitled&quot;<br>
                    Mark Hill<br>
                    Stuart, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="94" align="center"><font size="2" color="#000080">
                    &quot;Stormy Sunset&quot;<br>
                    Ruth Hills<br>
                    Sebastian, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="94" align="center"><font size="2" color="#000080">
                    &quot;Morning is Braking&quot;<br>
                    Ruth Hills<br>
                    Sebastian, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="94" align="center"><font size="2" color="#000080">
                    &quot;Cormorant Condo&quot;<br>
                    Melissa Horan<br>
                    Melbourne, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="35"><a href="../content/imglib/05KappelZ3.jpg">
                </a><a href="../content/imglib/09HoranM3.jpg"><img src="../content/imglib/09HoranM3_small.jpg"
                                                                   width="125" height="83" border="0"></a></td>
            <td width="137" align="center" height="35"><a href="../content/imglib/05KnottS2.jpg">
                </a><a href="../content/imglib/09HuffK2.jpg"><img src="../content/imglib/09HuffK2_small.jpg" width="125"
                                                                  height="94" border="0"></a></td>
            <td width="135" align="center" height="35"><a href="../content/imglib/05MartelC1.jpg">
                </a><a href="../content/imglib/09HuffK3.jpg"><img src="../content/imglib/09HuffK3_small.jpg" width="125"
                                                                  height="94" border="0"></a></td>
            <td width="134" align="center" height="35"><a href="../content/imglib/05MelloneJ3.jpg">
                </a><a href="../content/imglib/09HuffK1.jpg"><img src="../content/imglib/09HuffK1_small.jpg" width="125"
                                                                  height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="35" align="center"><font size="2" color="#000080">
                    &quot;Another Day in Paradise&quot;<br>
                    Melissa Horan<br>
                    Melbourne, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="35" align="center"><font size="2" color="#000080">
                    &quot;Green Grass of Home&quot;<br>
                    Keith Huff<br>
                    Palm Bay, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="35" align="center"><font size="2" color="#000080">&quot;Tranquility&quot;<br>
                    Keith Huff<br>
                    Palm Bay, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="35" align="center"><font size="2" color="#000080">
                    &quot;Morning Serenity&quot;<br>
                    Keith Huff<br>
                    Palm Bay, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="35"><a href="../content/imglib/05MoseleyR1.jpg">
                </a><a href="../content/imglib/09KiesinerR1.jpg"><img src="../content/imglib/09KiesinerR1_small.jpg"
                                                                      width="125" height="87" border="0"></a></td>
            <td width="137" align="center" height="35"><a href="../content/imglib/05MoseleyR2.jpg">
                </a><a href="../content/imglib/09KontrasP3.jpg"><img src="../content/imglib/09KontrasP3_small.jpg"
                                                                     width="125" height="83" border="0"></a></td>
            <td width="135" align="center" height="35"><a href="../content/imglib/05OdlumL1.jpg">
                </a><a href="../content/imglib/09KontrasP1.jpg"><img src="../content/imglib/09KontrasP1_small.jpg"
                                                                     width="125" height="83" border="0"></a></td>
            <td width="134" align="center" height="35"><a href="../content/imglib/05PichardD1.jpg">
                </a><a href="../content/imglib/09LeffelD2.jpg"><img src="../content/imglib/09LeffelD2_small.jpg"
                                                                    width="125" height="96" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="35" align="center"><font size="2" color="#000080">
                    &quot;Morning Sunrise&quot;<br>
                    Rosalie Kiesiner<br>
                    Palm Bay, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="35" align="center"><font size="2" color="#000080">
                    &quot;Sunrise Lagoon&quot;<br>
                    Pamela Kontras<br>
                    Malabar, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="35" align="center"><font size="2" color="#000080">
                    &quot;Untitled&quot;<br>
                    Pamela Kontras<br>
                    Malabar, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="35" align="center"><font size="2" color="#000080">
                    &quot;Calm&quot;<br>
                    Dawkins Leffel<br>
                    Cape Canaveral, FL <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="94"><a href="../content/imglib/05ReadA1.jpg">
                </a><a href="../content/imglib/09LeffelD3.jpg"><img src="../content/imglib/09LeffelD3_small.jpg"
                                                                    width="125" height="92" border="0"></a></td>
            <td width="137" align="center" height="94"><a href="../content/imglib/05ReadA2.tif.jpg">
                </a><a href="../content/imglib/09McAvoyM1.jpg"><img src="../content/imglib/09McAvoyM1_small.jpg"
                                                                    width="125" height="99" border="0"></a></td>
            <td width="135" align="center" height="94"><a href="../content/imglib/05SlempL1.jpg">
                </a><a href="../content/imglib/09McAvoyM2.jpg"><img src="../content/imglib/09McAvoyM2_small.jpg"
                                                                    width="125" height="100" border="0"></a></td>
            <td width="134" align="center" height="94"><a href="../content/imglib/05SlempL2.jpg">
                </a><a href="../content/imglib/09McCafferyD1.jpg"><img src="../content/imglib/09McCafferyD1_small.jpg"
                                                                       width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="35" align="center"><font size="2" color="#000080">
                    &quot;And the Lord Said...&quot;<br>
                    Dawkins Leffel<br>
                    Cape Canaveral, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="35" align="center"><font size="2" color="#000080">
                    &quot;Indian River&quot;<br>
                    Michael McAvoy<br>
                    Cocoa, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="35" align="center"><font size="2" color="#000080">
                    &quot;Sunrise&quot;<br>
                    Michael McAvoy<br>
                    Cocoa, FL<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="35" align="center"><font size="2" color="#000080">
                    &quot;Million Dollar View&quot;<br>
                    David McCaffery<br>
                    Titusville, FL<br>
                    <br>
                    <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="35"><a href="../content/imglib/05SlempL3.jpg">
                </a><a href="../content/imglib/09McCafferyD2.jpg"><img src="../content/imglib/09McCafferyD2_small.jpg"
                                                                       width="125" height="94" border="0"></a></td>
            <td width="137" align="center" height="35"><a href="../content/imglib/05SlempL4.jpg">
                </a><a href="../content/imglib/09McCafferyD3.jpg"><img src="../content/imglib/09McCafferyD3_small.jpg"
                                                                       width="125" height="94" border="0"></a></td>
            <td width="135" align="center" height="35"><a href="../content/imglib/05SlempL5.jpg">
                </a><a href="../content/imglib/09McClungS3.jpg"><img src="../content/imglib/09McClungS3_small.jpg"
                                                                     width="125" height="94" border="0"></a></td>
            <td width="134" align="center" height="35"><a href="../content/imglib/05SmithR2.jpg">
                </a><a href="../content/imglib/09McEwenE1.jpg"><img src="../content/imglib/09McEwenE1_small.jpg"
                                                                    width="125" height="155" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="1" align="center"><font size="2" color="#000080">
                    &quot;Untitled&quot;<br>
                </font><font size="2" color="#000080">David McCaffery<br>
                    Titusville, FL</font>
                <p>&nbsp;</td>
            <td width="137" height="1" align="center"><font size="2" color="#000080">
                    &quot;Untitled&quot;<br>
                </font><font size="2" color="#000080">David McCaffery<br>
                    Titusville, FL</font>
                <p>&nbsp;</td>
            <td width="137" height="1" align="center"><font size="2" color="#000080">
                    &quot;Sunrise, Ballard Park&quot;<br>
                    Suzanne McClung<br>
                    Melbourne, FL</font>
                <p>&nbsp;</td>
            <td width="134" height="1" align="center"><font size="2" color="#000080">
                    &quot;Sails in the Sunset&quot;<br>
                    Edward McEwen<br>
                    Palm Bay, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="35"><a href="../content/imglib/05StoneJ1.jpg">
                </a><a href="../content/imglib/09McGowenT1.jpg"><img src="../content/imglib/09McGowenT1_small.jpg"
                                                                     width="125" height="83" border="0"></a></td>
            <td width="137" align="center" height="35"><a href="../content/imglib/05ThomsonR1.jpg">
                </a><a href="../content/imglib/09MihalichR1.jpg"><img src="../content/imglib/09MihalichR1_small.jpg"
                                                                      width="125" height="96" border="0"></a></td>
            <td width="135" align="center" height="35"><a href="../content/imglib/05WakemanJ1.jpg">
                </a><a href="../content/imglib/09MinorR2.jpg"><img src="../content/imglib/09MinorR2_small.jpg"
                                                                   width="125" height="94" border="0"></a></td>
            <td width="134" align="center" height="35"><a href="../content/imglib/05WalshK3.jpg">
                </a><a href="../content/imglib/09MoyC3.jpg"><img src="../content/imglib/09MoyC3_small.jpg" width="125"
                                                                 height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td width="139" height="30" align="center"><font color="#000080" size="2">
                    &quot;Sunrise Over IRL&quot;<br>
                    Ted McGowen<br>
                    Stuart, FL <br>
                    <br>
                    &nbsp;</font></td>
            <td width="137" height="30" align="center"><font size="2" color="#000080">
                    &quot;Untitled&quot;<br>
                    Richard Mihalich<br>
                    Merritt Island, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="135" height="30" align="center"><font size="2" color="#000080">
                    &quot;Morning Star&quot;<br>
                    Robert Minor<br>
                    Mims, FL <br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="134" height="30" align="center"><font size="2" color="#000080">
                    &quot;Sunrise Over Indian River&quot;<br>
                    Caroline Moy<br>
                    Micco, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td width="139" align="center" height="30"><a href="../content/imglib/05WhiticarJ1.jpg"><font
                            color="#000080" size="2">
                    </font></a><a href="../content/imglib/09MurphyS1.jpg"><img
                            src="../content/imglib/09MurphyS1_small.jpg" width="125" height="175" border="0"></a></td>
            <td width="137" align="center" height="30"><a href="../content/imglib/09MurphyS2.jpg"><img
                            src="../content/imglib/09MurphyS2_small.jpg" width="125" height="175" border="0"></a></td>
            <td width="135" align="center" height="30"><a href="../content/imglib/09MurrayS3.jpg"><img
                            src="../content/imglib/09MurrayS3_small.jpg" width="125" height="84" border="0"></a></td>
            <td width="134" align="center" height="30"><a href="../content/imglib/09NewstedtS2.jpg"><img
                            src="../content/imglib/09NewstedtS2_small.jpg" width="125" height="69" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    Over Pelican Island&quot;<br>
                    Skip Murphy<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Kaleidoscope
                    Sunrise&quot;<br>
                    Skip Murphy<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Good
                    Morning in the Bay&quot;<br>
                    Sharon Murray<br>
                    Malabar, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    at Wabasso Boat Ramp&quot;<br>
                    Sandy Newstedt Sr.<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09O%27BrienL1.jpg"><img
                            src="../content/imglib/09O%27BrienL1_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09O%27BrienL2.jpg"><img
                            src="../content/imglib/09O%27BrienL2_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09O%27BrienL3.jpg"><img
                            src="../content/imglib/09O%27BrienL3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09PaffM1.jpg"><img
                            src="../content/imglib/09PaffM1_small.jpg" width="125" height="88" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;November
                    Sunrise &quot;<br>
                    Lisa O'Brien<br>
                    Indian Harbor Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Indian
                    River Sunrise&quot;<br>
                    Lisa O'Brien<br>
                    Indian Harbor Beach, FL<br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Space
                    Coast Sunrise&quot;<br>
                    Lisa O'Brien<br>
                    Indian Harbor Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Fishin'
                    Time&quot;<br>
                    Mary Paff<br>
                    Beaver Falls, PA<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09PalmerJ2.jpg"><img
                            src="../content/imglib/09PalmerJ2_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09PetrikK2.jpg"><img
                            src="../content/imglib/09PetrikK2_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09PichonK1.jpg"><img
                            src="../content/imglib/09PichonK1_small.jpg" width="125" height="100" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09RichardsR2.jpg"><img
                            src="../content/imglib/09RichardsR2_small.jpg" width="125" height="100" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Senset
                    Lagoon&quot;<br>
                    John Palmer<br>
                    Edgewater, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    on the River&quot;<br>
                    Kristina Petrik<br>
                    Melbourne, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    on Indian River&quot;<br>
                    Ken Pichon<br>
                    Melbourne, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Early
                    Morning Fly-In&quot;<br>
                    Renee Richards<br>
                    New Smyrna Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09RichardsR3.jpg"><img
                            src="../content/imglib/09RichardsR3_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09RingM3.jpg"><img
                            src="../content/imglib/09RingM3_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09RittenhouseW3.jpg"><img
                            src="../content/imglib/09RittenhouseW3_small.jpg" width="125" height="93" border="0"></a>
            </td>
            <td align="center" height="30"><a href="../content/imglib/09RittenhouseW1.jpg"><img
                            src="../content/imglib/09RittenhouseW1_small.jpg" width="125" height="93" border="0"></a>
            </td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Fishing
                    in God's Spotlight&quot;<br>
                    Renee Richards<br>
                    New Smyrna Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Driftwood
                    at Sunset&quot;<br>
                    Mike Ring<br>
                    Edgewater, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Golden
                    Sunrise&quot;<br>
                    Wendi Rittenhouse<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Dawn
                    on the Lagoon&quot;<br>
                    Wendi Rittenhouse<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09RittenhouseW2.jpg"><img
                            src="../content/imglib/09RittenhouseW2_small.jpg" width="125" height="93" border="0"></a>
            </td>
            <td align="center" height="30"><a href="../content/imglib/09SchroederG3.jpg"><img
                            src="../content/imglib/09SchroederG3_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SeibertJ1.jpg"><img
                            src="../content/imglib/09SeibertJ1_small.jpg" width="125" height="81" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SimanekP1.jpg"><img
                            src="../content/imglib/09SimanekP1_small.jpg" width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Fire
                    Sky&quot;<br>
                    Wendi Rittenhouse<br>
                    Sebastian, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    Over St. Lucie Inlet Preserve&quot;<br>
                    Georgia Schroeder<br>
                    Stuart, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    on the Indian River&quot;<br>
                    Jessica Seibert<br>
                    Jensen Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;The
                    Break of Dawn&quot;<br>
                    Pat Simanek<br>
                    Rockledge, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09SimanekP2.jpg"><img
                            src="../content/imglib/09SimanekP2_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SimanekP3.jpg"><img
                            src="../content/imglib/09SimanekP3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SpaldingL1.jpg"><img
                            src="../content/imglib/09SpaldingL1_small.jpg" width="125" height="81" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SpaldingL2.jpg"><img
                            src="../content/imglib/09SpaldingL2_small.jpg" width="125" height="84" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Untitled&quot;<br>
                    Pat Simanek<br>
                    Rockledge, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Untitled&quot;<br>
                    Pat Simanek<br>
                    Rockledge, FL<br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;The
                    Indian River at Rest&quot;<br>
                    Lyndsey Spalding<br>
                    Rockledge, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;River
                    at Rest&quot;<br>
                    Lyndsey Spalding<br>
                    Rockledge, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09SpaldingL3.jpg"><img
                            src="../content/imglib/09SpaldingL3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SpradlinS1.jpg"><img
                            src="../content/imglib/09SpradlinS1_small.jpg" width="125" height="82" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09SpradlinS2.jpg"><img
                            src="../content/imglib/09SpradlinS2_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09StarkeyJ1.jpg"><img
                            src="../content/imglib/09StarkeyJ1_small.jpg" width="125" height="83" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    in February&quot;<br>
                    Lyndsey Spalding<br>
                    Rockledge, FL<br>
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    Silhouette&quot;<br>
                    Sara Spradlin<br>
                    Cocoa, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunrise
                    Across the River&quot;<br>
                    Sara Spradlin<br>
                    Cocoa, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Evening
                    on the Indian River&quot;<br>
                    Jean Starkey<br>
                    Merritt Isalnd, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09StarkeyJ2.jpg"><img
                            src="../content/imglib/09StarkeyJ2_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09StempelS3.jpg"><img
                            src="../content/imglib/09StempelS3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09StempelS2.jpg"><img
                            src="../content/imglib/09StempelS2_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09StrupatB3.jpg"><img
                            src="../content/imglib/09StrupatB3_small.jpg" width="125" height="100" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Honeymoon
                    Lake &amp; IRL at Sunset&quot;<br>
                    Jean Starkey<br>
                    Merritt Isalnd, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    at the Pier&quot;<br>
                    Suzie Stempel<br>
                    Melbourne Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Melbourne
                    Beach Sunset&quot;<br>
                    Suzie Stempel<br>
                    Melbourne Beach, FL<br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Getting
                    Together After Work&quot;<br>
                    Bob Strupat<br>
                    Vero Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09ThomanR2.jpg"><img
                            src="../content/imglib/09ThomanR2_small.jpg" width="125" height="83" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09ThomsonE1.jpg"><img
                            src="../content/imglib/09ThomsonE1_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09ToddM1.jpg"><img
                            src="../content/imglib/09ToddM1_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09ToddM2.jpg"><img
                            src="../content/imglib/09ToddM2_small.jpg" width="125" height="94" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Salt
                    Flat Sunrise&quot;<br>
                    Roy Thoman<br>
                    Titusville, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    Sky&quot;<br>
                    Eugene Thomson<br>
                    Melbourne Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Good
                    Morning Beautiful&quot;<br>
                    Melissa Todd<br>
                    Micco, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;A
                    Fresh Start&quot;<br>
                    Melissa Todd<br>
                    Micco, FL<br>
                    &nbsp;</font></td>
        </tr>
        <tr>
            <td align="center" height="30"><a href="../content/imglib/09ToddM3.jpg"><img
                            src="../content/imglib/09ToddM3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09TownshendA1.jpg"><img
                            src="../content/imglib/09TownshendA1_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09TownshendA3.jpg"><img
                            src="../content/imglib/09TownshendA3_small.jpg" width="125" height="94" border="0"></a></td>
            <td align="center" height="30"><a href="../content/imglib/09VerderameK2.jpg"><img
                            src="../content/imglib/09VerderameK2_small.jpg" width="125" height="66" border="0"></a></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;A
                    New Day&quot;<br>
                    Melissa Todd<br>
                    Micco, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset&quot;<br>
                    Art Townshend II<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Anvil
                    Sunset&quot;<br>
                    Art Townshend II<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Indian
                    River Anvil Sunset&quot;<br>
                    Ken Verderame<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr valign="middle">
            <td height="30" align="center"><a href="../content/imglib/09VerderameK3.jpg"><img
                            src="../content/imglib/09VerderameK3_small.jpg" width="125" height="66" border="0"></a></td>
            <td height="30" align="center"><a href="../content/imglib/09WellsJ1.jpg"><img
                            src="../content/imglib/09WellsJ1_small.jpg" width="125" height="82" border="0"></a></td>
            <td height="30" align="center"><font size="2" color="#000080"><a href="../content/imglib/09WellsV2.jpg"><img
                                src="../content/imglib/09WellsV2_small.jpg" width="125" height="82"
                                border="0"></a></font></td>
            <td height="30" align="center"><font size="2" color="#000080"><a href="../content/imglib/09WellsV1.jpg"><img
                                src="../content/imglib/09WellsV1_small.jpg" width="125" height="82"
                                border="0"></a></font></td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Indian
                    River Red Sunset&quot;<br>
                    Ken Verderame<br>
                    Indialantic, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    in Paradise&quot;<br>
                    Jerry Wells<br>
                    Cocoa Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    Swirl&quot;<br>
                    Valerie Wells<br>
                    Cocoa Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Backyard
                    Sunset&quot;<br>
                    Valerie Wells<br>
                    Cocoa Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
        </tr>
        <tr valign="middle">
            <td height="30" align="center"><font size="2" color="#000080"><a href="../content/imglib/09WellsV3.jpg"><img
                                src="../content/imglib/09WellsV3_small.jpg" width="125" height="82"
                                border="0"></a></font></td>
            <td height="30" align="center">&nbsp;</td>
            <td height="30" align="center">&nbsp;</td>
            <td height="30" align="center">&nbsp;</td>
        </tr>
        <tr valign="top">
            <td height="30" align="center"><font size="2" color="#000080"> &quot;Sunset
                    of Fire&quot;<br>
                    Valerie Wells<br>
                    Cocoa Beach, FL<br>
                    <br>
                    &nbsp;</font></td>
            <td width="139" height="30" align="center"><font size="2" color="#000080">
                    <br>
                    <br>
                    &nbsp;</font></td>
            <td width="139" height="30" align="center"><font size="2" color="#000080">
                    <br>
                    &nbsp;</font></td>
            <td width="139" height="30" align="center"><font size="2" color="#000080">
                    <br>
                    <br>
                    &nbsp;</font></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>