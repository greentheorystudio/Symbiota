<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/TaxonomyDynamicListManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$listManager = new TaxonomyDynamicListManager();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Animal Biodiversity in the Indian River Lagoon</title>
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
            <h2>Animal Biodiversity in the Indian River Lagoon</h2>
            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td align="center">
                        <img border="0" src="../content/imglib/Anim_Phylum.gif" hspace="2" vspace="2" width="352" height="206"></td>
                </tr>
                <tr>
                    <td><p class="body">Of those taxa documented, 20 animal phyla,
                            comprising 1779 species, were listed in the original IRL Species
                            Inventory.</p>
                        <p class="title">Phylum Chordata:</p>
                        <p class="body">Chordate groups (38.7%) consisted of the protochordates and 6 classes
                            of organisms comprising 689 different species. The
                            diversity among fishes and birds accounted for a large amount of the total
                            biodiversity among the chordate group.</p>
                        <table class="table-border">
                            <tr>
                                <td align="center" width="175"><p class="heading">Group</p></td>
                                <td align="center" width="94"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Subphylum
                                        Urochordata</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(137); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class
                                        Chondrichthyes</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(501); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class
                                        Osteichthyes</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(11155); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class Amphibia</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(1011); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class Reptilia</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(1016); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class Aves</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(1012); ?></span></td>
                            </tr>
                            <tr>
                                <td width="175" align="left"><p class="label">Class Mammalia</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(1015); ?></span></td>
                            </tr>
                        </table>
                        <p class="title">Phylum Mollusca:</p>
                        <p class="body">Mollusks accounted for 24.1% of the total
                            animal biodiversity in the IRL. This group consisted of 428 species
                            in 4 classes:</p>

                        <table class="table-border">
                            <tr>
                                <td align="center" width="179"><p class="heading">Group</p></td>
                                <td align="center" width="98"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Gastropoda</p></td>
                                <td width="98" align="center"><span><?php echo $listManager->getSpAmtByParent(269); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Polyplacophora</p></td>
                                <td width="98" align="center"><span><?php echo $listManager->getSpAmtByParent(277); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Bivalvia</p></td>
                                <td width="98" align="center"><span><?php echo $listManager->getSpAmtByParent(263); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Cephalopoda</p></td>
                                <td width="98" align="center"><span><?php echo $listManager->getSpAmtByParent(264); ?></span></td>
                            </tr>
                        </table>
                        <p class="title">Phylum Arthropoda:</p>
                        <p class="body">Arthropods accounted for 20.2%
                            of the animal biodiversity in the IRL. This groups consists of 360
                            species in 7 classes:</p>

                        <table class="table-border">
                            <tr>
                                <td align="center" width="185"><p class="heading">Group</p></td>
                                <td align="center" width="93"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Xiphosura</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(1018); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Pycnogonida</td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(514); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Maxillopoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(510); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Copepoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(1019); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Ostracoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(511); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Insecta</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(505); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Class Malacostraca</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(509); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Stomatopoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(1604); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Peracarida</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(1534); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Tanaidacea</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(2582); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Cumacea</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(2563); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Decapoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(2566); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Isopoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(2569); ?></span></td>
                            </tr>
                            <tr>
                                <td width="185"><p class="label">Order Amphipoda</p></td>
                                <td width="93" align="center"><span><?php echo $listManager->getSpAmtByParent(2554); ?></span></td>
                            </tr>
                        </table>
                        <p class="title">Phylum Annelida:</p>
                        <p class="body">Annelid worms accounted for 8.2% of IRL biodiversity, and consisted of 145 species in 2 classes:</p>

                        <table class="table-border">
                            <tr>
                                <td align="center" width="190"><p class="heading">Group</p></td>
                                <td align="center" width="92"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="190"><p class="label">Class Clitellata</p></td>
                                <td width="92" align="center"><span><?php echo $listManager->getSpAmtByParent(248); ?></span></td>
                            </tr>
                            <tr>
                                <td width="190"><p class="label">Class Polychaeta</p></td>
                                <td width="92" align="center"><span><?php echo $listManager->getSpAmtByParent(253); ?></span></td>
                            </tr>
                        </table>

                        <p class="title">Echinodermata:</p>
                        <p class="body">Echinoderms</p>
                        <table class="table-border">
                            <tr>
                                <td width="179">

                                    <p align="center"><p class="heading">Group</p></td>
                                <td width="94"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Holothuroidea</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(272); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Echinoidea</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(267); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Asteroidea</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(261); ?></span></td>
                            </tr>
                            <tr>
                                <td width="179"><p class="label">Class Ophiuroidea</p></td>
                                <td width="94" align="center"><span><?php echo $listManager->getSpAmtByParent(274); ?></span></td>
                            </tr>
                        </table>
                        <p class="title">Sipuncula:</p>
                        <p class="body">Sipunculans accounted for 1.0% of IRL biodiversity, with 2 classes of
                            organisms and 18 species:</p>

                        <table class="table-border">
                            <tr>
                                <td width="180" align="center"><p class="heading">Group</p></td>
                                <td width="92" align="center"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="180"><p class="label">Class Sipunculidea</p></td>
                                <td width="92" align="center"><span><?php echo $listManager->getSpAmtByParent(279); ?></span></td>
                            </tr>
                            <tr>
                                <td width="180"><p class="label">Class Phascolosomatidea</p></td>
                                <td width="92" align="center"><span><?php echo $listManager->getSpAmtByParent(275); ?></span></td>
                            </tr>
                        </table>
                        <p class="title">Other Phyla:</p>
                        <p class="body">The remaining 6% of total biodiversity in the
                            IRL consisted of phyla having at
                            least 1 representative species:</p>

                        <table class="table-border">
                            <tr>
                                <td width="189" align="center"><p class="heading">Group</p></td>
                                <td width="89" align="center"><p class="heading"># Species</p></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Porifera</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(34); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Cnidaria</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(31); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Ctenophora</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(33); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Platyhelmithes</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(11156); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Nemertea</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(126); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Kinorhyncha</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(124); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Entoprocta</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(246); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Tardigrada</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(132); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Chaetognatha</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(55); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Bryozoa</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(121); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Phoronida </p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(128); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Brachiopoda</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(120); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Echiura</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(245); ?></span></td>
                            </tr>
                            <tr>
                                <td width="189"><p class="label">Phylum Hemichordata</p></td>
                                <td width="89" align="center"><span><?php echo $listManager->getSpAmtByParent(63); ?></span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
