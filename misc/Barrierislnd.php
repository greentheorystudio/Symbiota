<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Barrier Island Habitats</title>
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
    <h2>Barrier Island Habitats</h2>
    <table style="width:550px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="5">
        <tr>
            <td align="center">
                <img border="0" src="../content/imglib/Beach1.gif" hspace="5" vspace="5" width="130" height="95"></td>
            <td align="center"><img border="0" src="../content/imglib/Dune1.gif" hspace="5" vspace="5" width="130"
                                    height="96"></td>
            <td align="center"><img border="0" src="../content/imglib/Scrub1.gif" hspace="5" vspace="5" width="130"
                                    height="97"></td>
        </tr>
        <tr>
            <td align="center"><span class="caption">Beach</span></td>
            <td align="center"><span class="caption">Dune</span></td>
            <td align="center"><span class="caption">Scrub</span></td>
        </tr>
    </table>
    <table style="width:550px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="5">
        <tr>
            <td align="center"><img border="0" src="../content/imglib/Hammock1.gif" hspace="5" vspace="5" width="129"
                                    height="106"></td>
            <td align="center"><img border="0" src="../content/imglib/MangrFrng1.gif" hspace="5" vspace="5" width="136"
                                    height="103"></td>
        </tr>
        <tr>
            <td align="center"><span class="caption">Hammock</span></td>
            <td align="center"><span class="caption">Mangrove</span></td>
        </tr>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">The extensive barrier island system along
                    the east coast of Florida is the largest in the United States, consisting of
                    approximately 189,300 hectares (467,700 acres) (Kaplan 1988). In east central
                    Florida, barrier islands separate the Atlantic Ocean from the Indian River
                    Lagoon (IRL) and are an important defense against hurricanes and major storms,
                    buffering mainland Florida against large waves, heavy winds, and storm surges.
                </p>
                <p class="body">Barrier islands are dynamic features of the landscape that form in two ways: 1)
                    from longshore drift currents move sands southward along the coast; and 2)
                    from the emergence of underwater shoals (Otvos 1981). It is believed that
                    Florida's barrier islands originated during the Pleistocene epoch (1.75
                    million to 11,000 years ago) when rises in ancient beach and their associated
                    sediments lithified into coquina (rock). Traces of this coquina system, called
                    the Anastasia Formation, can be found from St. Augustine to Boca Raton, Florida
                    (Tanner 1960b).</p>
                <p class="body">Whether barrier islands begin as simple sandbars, or as
                    emerged shoals, they gradually accumulate sand due to wave action and winds. It
                    is this build up of sand along the coast that forms the well developed beaches,
                    dunes and maritime forests along Florida's coast. Wave action is constantly at
                    work eroding sand from some areas of the barrier island system, while
                    simultaneously depositing this eroded sand into different areas via longshore
                    drift, storms and hurricanes. <br/>
                    Accretion of eroded sediments
                    generally occurs either parallel to the coast, or at the ends of barrier island
                    systems, rather than seaward. In general, sands tend to be carried southward
                    along the coast, and are deposited as they encounter the northern ends of
                    barrier islands or other structures such as jetties. Data from Pilkey et al.
                    (1984) suggests the Florida coastline has been eroding landward at a rate of 0.3
                    - 0.6 mm per year.</p>
                <p class="body">The substantial system of barrier islands in the area
                    of the Indian River Lagoon encompasses a variety of habitat types. In the
                    immediate vicinity of the shoreline are beaches, dunes, and swales. Beyond the
                    beach zone are coastal strand, also called scrub, maritime hammocks, spoil
                    islands, and the mangrove fringes that border the Indian River Lagoon.</p>
                <p class="title">Select a barrier island habitat to explore:</p>
                <table class="table-border">
                    <tr>
                        <td><p class="label">Barrier Island Habitats</p></td>
                    </tr>
                    <tr>
                        <td><a href="Beaches.php">Beaches</a></td>
                    </tr>
                    <tr>
                        <td><a href="Dunes.php">Dunes</a></td>
                    </tr>
                    <tr>
                        <td><a href="Scrub.php">Scrub</a></td>
                    </tr>
                    <tr>
                        <td><a href="Hammock_Habitat.php">Maritime hammocks</a></td>
                    </tr>
                    <tr>
                        <td><a href="Mangroves.php">Mangrove fringes</a></td>
                    </tr>
                </table>
                <p class="footer_note">
                    Report by:&nbsp;K. Hill, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
