<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$listManager = new TaxonomyDynamicListManager();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Protist Biodiversity in the Indian River Lagoon</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
        include(__DIR__ . '/../header.php');
		?>
		<div id="innertext">
            <h2>Protist Biodiversity in the Indian River Lagoon</h2>
            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td align="center"><img src="../irlspec/images/ProtistBiodiv.gif" width="284" height="231" hspace="2" vspace="2" border="0" class="centered"></td>
                </tr>
                <tr>
                    <td>
                        <p class="body">Of the protists documented in the
                            original Inventory, over half (53.4%) were Chrysophyta (diatoms). The Rhizopoda (amoebas and forams) comprised 21.9% of the species
                            diversity among Protists, while the Ciliophora (ciliates) and the
                            Dinoflagellata (dinoflagellates) comprised 19.3% and 5.4%
                            respectively.
                        </p>
                    </td>
                </tr>
            </table>

            <table style="border:0;width:350px;margin-left:auto;margin-right:auto;" class="table-border">
                <tr>
                    <td><p class="heading">Group</p></td>
                    <td><p class="heading"># Species</p></td>
                </tr>
                <tr>
                    <td><span>Class Chrysophyceae</span></td>
                    <td align="center"><span><b><?php echo $listManager->getSpAmtByParent(11158); ?></b></span></td>
                </tr>
                <tr>
                    <td><span>Phylum Rhizopoda</span></td>
                    <td align="center"><span><b><?php echo $listManager->getSpAmtByParent(36); ?></b></span></td>
                </tr>
                <tr>
                    <td>Phylum Ciliophora</span></td>
                    <td align="center"><span><b><?php echo $listManager->getSpAmtByParent(15); ?></b></span></td>
                </tr>
                <tr>
                    <td>Superclass Dinoflagellata</span></td>
                    <td align="center"><span><b><?php echo $listManager->getSpAmtByParent(11160); ?></b></span></td>
                </tr>
            </table>
        </div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
