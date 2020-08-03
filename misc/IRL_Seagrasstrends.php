<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Historic Trends in IRL Seagrasses</title>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Historic Trends in IRL Seagrasses</h2>
    <table style="border:0;width:320px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/seagrassissues2.jpg" alt="" width="320" height="250"/></td>
        </tr>
    </table>
    <table style="width:550px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td><p class="body"> A lagoon-wide seagrass monitoring program led by scientists from the St.
                    Johns River Water Management District is responsible for monitoring
                    long-term trends in IRL seagrass population status, health, and coverage
                    trends. Data from this program and associated water quality monitoring
                    programs indicates that the amount of light reaching the benthic habitat is
                    the primary factor limiting seagrass coverage in the lagoon. Water column
                    turbidity, chlorophyll a concentration, and color are the principal
                    determinants of water clarity in the lagoon, with turbidity being by far
                    the most important (Christian and Shang 2003). <br>
                    <br>
                    Comparison with historic data and aerial photographs from the 1940s reveals
                    that there has been a significant decline in IRL seagrass coverage over the
                    years. Seagrass biologists estimated that total acreage in the early 1990s
                    was estimated to be approximately 80% of the acreage 60 years earlier. The
                    actual extent of seagrass loss in specific portions of the estuary has been
                    highly variable, however, and this lagoon-wide snapshot doesn't tell the
                    whole story. In some areas, once expansive seagrass meadows had almost
                    entirely disappeared. <br>
                    <br>
                    In general, the areas of the IRL with the healthiest seagrass beds are
                    adjacent to relatively undeveloped watersheds or near ocean inlets.
                    Conversely, areas exhibiting the greatest seagrass loss are typically
                    located near extensively developed shorelines and watersheds. <br>
                    <br>
                    On the positive side, the last few years have seen measurable recovery of
                    seagrass habitat in the lagoon, with expanded acreage in many segments.
                    Some of this rebound is believed to be the result of a favorable
                    spring-summer growing season climate the last few years in which
                    lower-than-average rainfall has kept the lagoon water clear and at
                    salinities that are optimum for seagrass growth. If we start to again see
                    more typical wet season patterns in coming years, some of the recent gains
                    in seagrass acreage may be reversed (Virnstein et al. 2007, Morris and
                    Virnstein 2008). <br>
                    <br>
                    Nevertheless, interventions in the form of proactive natural resource and
                    human footprint management have also played an important part in allowing
                    seagrass habitat recovery in the IRL. These interventions encompass all of
                    the projects discussed here, including elimination of wastewater treatment
                    plant discharges, management of the volume and timing of freshwater
                    discharge from large drainage networks, muck removal, and installation of
                    stormwater management structures (SJRWMD 2007).</p>

                <p class="title">References:</p>

                <p class="body">Christian D and YP Sheng. 2003. Relative influence of various water quality
                    parameters on light attenuation in Indian River Lagoon. Estuarine, Coastal
                    and Shelf Science 57: 961-971.</p>
                <p class="body">Morris LJ and RW Virnstein. 2008. The demise and recovery of seagrass in
                    the northern Indian River Lagoon, Florida. Estuaries and Coasts 27:915-922.</p>
                <p class="body">Saint Johns River Water Management District (SJRWMD). 2007. Indian River
                    Lagoon, An Introduction to a Natural Treasure. Saint Johns River Water
                    Management District. 36 p.</p>
                <p class="body">Virnstein RW, Steward JS, and LJ Morris. 2007. Seagrass Coverage Trends in
                    the Indian River Lagoon System. Florida Scientist 70:397-404. </p>

                <p class="footer_note">Report by: J. Masterson, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu</a>
                </p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
