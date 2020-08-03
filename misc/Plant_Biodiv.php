<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$listManager = new TaxonomyDynamicListManager();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Plant Biodiversity in the Indian River Lagoon</title>
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
    <h2>Plant Biodiversity in the Indian River Lagoon</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/PlantBiodiv.gif" width="248" height="221" hspace="2"
                                    vspace="2" border="0" class="centered"></td>
        </tr>
        <tr>
            <td>
                <p class="body">Plants comprised 11.6% of the total species
                    biodiversity documented in the original IRL Species Inventory. The Tracheophyta (vascular
                    plants), accounted for over half (50.9%) of all plants inventoried.&nbsp;
                    The Rhodophyta (red algae) accounted for 25.6% of species diversity, while
                    the Chlorophyta (green algae), and the Phaeophyta (brown algae) accounted
                    for 15.6% and 7.9% respectively.
                </p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;" class="table-border">
        <tr>
            <td><p class="heading">Group</p></td>
            <td align="center"><p class="heading"># Species</p></td>
        </tr>
        <tr>
            <td><p class="label">Division Tracheophyta</p></td>
            <td align="center"><span><?php echo $listManager->getSpAmtByParent(133); ?></span></td>
        </tr>
        <tr>
            <td><p class="label">Division Rhodophyta</p></td>
            <td align="center"><span><?php echo $listManager->getSpAmtByParent(35); ?></span></td>
        </tr>
        <tr>
            <td><p class="label">Division Chlorophyta</p></td>
            <td align="center"><span><?php echo $listManager->getSpAmtByParent(24); ?></span></td>
        </tr>
        <tr>
            <td><p class="label">Division Phaeophyta</p></td>
            <td align="center"><span><?php echo $listManager->getSpAmtByParent(16); ?></span></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
