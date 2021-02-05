<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>IRL Education and Conservation Links</title>
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
    <h2>IRL Education and Conservation Links</h2>
    <table style="width:500px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="5">
        <tr>
            <td align="center"><img src="../content/imglib/Barrier I S.jpg" width="65" height="65"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.brevardcounty.us/EELProgram/Areas/BarrierIslandSanctuary">Barrier
                        Island Sanctuary, Melbourne Beach, FL</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/National P S.jpg" width="50" height="70"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.nps.gov/cana/index.htm">Canaveral National Seashore</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/ELC.jpg" width="65" height="67"/></td>
            <td><p class="body"><a href="http://www.discoverelc.org">Environmental Learning Center, Vero Beach, FL</a>
                </p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/Martin C Schools.jpg" width="65" height="65"/>&nbsp;</td>
            <td><p class="body"><a href="http://esc.martinschools.org/pages/Environmental_Studies_Center">Environmental
                        Studies Center, Jensen Beach, FL</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/EOL.jpg" width="103" height="50"/>&nbsp;</td>
            <td><p class="body"><a href="http://eol.org">Encyclopedia of Life</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/FFWCC.jpg" width="55" height="63"/>&nbsp;</td>
            <td><p class="body"><a href="http://myfwc.com">Florida Fish and Wildlife Conservation Commission</a></p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/FOS.jpg" width="65" height="65"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.floridaocean.org">Florida Oceanographic Society, Stuart, FL</a></p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/2010HBOIlogoNoTagLinePMS877(metallic silver).jpg" width="80"
                                    height="65"/>&nbsp;
            </td>
            <td><p class="body"><a href="http://www.fau.edu/hboi/">Harbor Branch - Florida Atlantic University</a></p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/Manatee obs ctr.jpg" width="130" height="39"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.manateecenter.com">Manatee Observation and Education Center</a></p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/MDC.jpg" width="100" height="50"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.marinediscoverycenter.org">Marine Discovery Center, New Smyrna
                        Beach, FL</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/MRC.jpg" width="60" height="55"/>&nbsp;</td>
            <td><p class="body"><a href="http://mrcirl.org">Marine Resources Council, Palm Bay, FL</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/MSC Ponce.jpg" width="66" height="56"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.marinesciencecenter.com">Marine Science Center, Ponce Inlet, FL</a>
                </p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/NWRefugeS.jpg" width="58" height="70"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.fws.gov/merrittisland/">Merit Island National Wildlife Refuge</a>
                </p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/Nature cons.jpg" width="65" height="65"/>&nbsp;</td>
            <td><p class="body"><a
                            href="http://www.nature.org/ourinitiatives/regions/northamerica/unitedstates/florida/">Nature
                        Conservancy: Florida Chapter</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/ORCA.jpg" width="107" height="36"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.teamorca.org/orca/index.cfm">Ocean Research and Conservation
                        Associaiton</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/save manatee clb.jpg" width="65" height="65"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.savethemanatee.org">Save the Manatee Club</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/Sierra clb.jpg" width="50" height="65"/>&nbsp;</td>
            <td><p class="body"><a href="http://florida.sierraclub.org">The Sierra Club: Florida Chapter</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/SFWMD.jpg" width="60" height="60"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.sfwmd.gov/portal/page/portal/sfwmdmain/home%20page">South Florida
                        Water Management District</a></p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/SJRWMD.jpg" width="62" height="62"/>&nbsp;</td>
            <td><p class="body"><a href="http://floridaswater.com">St. Johns River Water Management District</a></p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/USFWS.jpg" width="50" height="60"/>&nbsp;</td>
            <td><p class="body"><a href="http://www.fws.gov/endangered/">U. S. Fish and Wildlife Service - Endangered
                        Species Program</a></p></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
