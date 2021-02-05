<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Documented Biodiversity in the Indian River Lagoon</title>
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
    <h2>Documented Biodiversity in the Indian River Lagoon</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><p class="body"><b>(based on Swain et al. 1995)*</b></p>
                <img border="0" src="../content/imglib/TotalBiodiv.gif" width="256" height="262"></td>
        </tr>
        <tr>
            <td>
                <p class="body">The original IRL Species
                    Inventory, first compiled in 1995, listed a total of 2493 different
                    species of plants, animals and protists.&nbsp;Of these, animals comprised
                    the greatest proportion of species in the Inventory (71.4%),
                    with 1779 species grouped into 20 phyla.&nbsp;Plants were grouped into 4
                    phyla, consisting of 289 different species;&nbsp;and the Protista (17.0%) consisted of 425 species
                    in 4 phlya.&nbsp;No data are available
                    for Kingdom Monera (bacteria).</p>
            </td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <div class="highlight"><p>*
                        &quot;There
                        is no doubt that some taxa are more complete and thoroughly
                        documented than others........&nbsp;Taxa for which contributors
                        had a high degree of confidence with complete lists include
                        fishes, birds, mollusks, chrysophytes, dinoflagellates, rhizopods,
                        ectoprocts, sipunculids, echinoderms, and mammals.&nbsp;Other
                        taxonomic groups, including vascular plants, amphibians and
                        reptiles, and marine macroalgae are relatively complete but could
                        benefit from increased sampling over wider areas of the
                        lagoon.&nbsp;Other taxa are, at the very best, partial lists, for
                        example sponges and chaetognaths. &quot;&nbsp;(Swain et al. 1995)</p></div>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
