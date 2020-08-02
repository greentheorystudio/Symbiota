<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Special Status Species of the Indian River Lagoon</title>
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
            <h2>Special Status Species of the Indian River Lagoon</h2>
            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="6" class="table-border">
                <tr>
                    <td colspan="2">
                        <p class="heading">Key to Listing Status</p></td>
                </tr>
                <tr>
                    <td align="center">E</td>
                    <td><span>Endangered: According to the Endangered Species Act of 1973, any species which is in danger of extinction throughout all or a significant portion of its range.</span></td>
                </tr>
                <tr>
                    <td align="center">T</td>
                    <td><span>Threatened: According to the Endangered Species Act of 1973, any species which is likely to become an endangered species within the foreseeable future throughout all or a significant portion of its range.</span></td>
                </tr>
                <tr>
                    <td align="center">T1</td>
                    <td><span>Threatened due to similarity of appearance</span></td>
                </tr>
                <tr>
                    <td align="center">CE</td>
                    <td><span>Commercially Exploited</span></td>
                </tr>
                <tr>
                    <td align="center">SSC</td>
                    <td><span>Florida State Species of Special Concern</span></td>
                </tr>
            </table>
            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td><p class="body"><em>Note:</em> These species are listed by the Florida Fish &amp; Wildlife Conservation Commission under one or more of the following criteria:</p>
                        <p class="body"><strong>1</strong>: Species has a significant vulnerability to habitat modification, environmental alteration, human disturbance, or human exploitation which, in the foreseeable future, may result in its becoming a threatened species unless appropriate protective or management techniques are initiated or maintained.</p>

                        <p class="body"><strong>2</strong>: Species may already meet certain criteria for designation as a threatened species, but for which conclusive data are limited or lacking.</p>
                        <p class="body"><strong>3</strong>: Species may occupy such an unusually vital or essential ecological niche that, should it decline significantly in numbers or distribution, other species would be adversely affected to a significant degree.</p>
                        <p class="body"><strong>4</strong>: Species has not sufficiently recovered from past population depletion.</p>
                        <p class="body"><strong>5</strong>: Species occurs as a population either intentionally introduced or being experimentally managed to attain specific objectives, and the species of special concern prohibitions in Rule 68A-27.002, F.A.C., shall not apply to species so designated, provided that the intentional killing, attempting to kill, posession or sale of such species is prohibited.</p></td>
                </tr>
            </table>

            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="4" class="table-border no-border alternate">
                <tr>
                    <th>Species Name</th>
                    <th>Common Name</th>
                    <th>FL State Status</th>
                    <th>US Federal Status</th>
                </tr>
                <tr class="heading">
                    <td colspan="4"><p class="label">Plants</p></td>
                </tr>
                <tr>
                    <td><span><em><a href="../taxa/index.php?taxon=Acrostichum aureum">Acrostichum aureum</a></em></span></td>
                    <td><span>Golden Leather Fern</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><span><em>Argusia gnaphalodes</em></span></td>
                    <td><span>Sea Rosemary</span></td>
                    <td>E</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><span><em><a href="../taxa/index.php?taxon=Asimina tertramera">Asimina tertramera</a></em></span></td>
                    <td><span>Four-petal Pawpaw</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><span><i>Cladonia perforata</i></span></td>
                    <td><span>Cup Lichen</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><span><em><a href="../taxa/index.php?taxon=Conradina grandiflora">Conradina grandiflora</a></em></span></td>
                    <td><span>Largeflower False Rosemary</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Dicerandra immaculata">Dicerandra immaculata</a></i></span></td>
                    <td><span>Lakela's Mint</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><span><a href="../taxa/index.php?taxon=Halophila johnsonii"><em>Halophila johnsonii</em></a></span></td>
                    <td><span>Johnson's Seagrass</span></td>
                    <td>---</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><span><i>Harrisia fragrans</i></span></td>
                    <td><span>Caribbean Apple Cactus</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Opuntia stricta">Opuntia stricta</a></span></em></td>
                    <td><span>Erect Prickly Pear</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><span><i>Osmunda regalis</i></span></td>
                    <td><span>Royal Fern</span></td>
                    <td>CE</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Polygala smallii">Polygala smallii</a></span></em></td>
                    <td><span>Tiny Polygala</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><span><i>Tillandsia balbisiana</i></span></td>
                    <td><span>Northern Needleleaf</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Tillandsia fasciculata</i></td>
                    <td><span>Giant Airplant</span></td>
                    <td>E</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><span><i>Tillandsia utriculata</i></span></td>
                    <td><span>Spreading Airplant</span></td>
                    <td>E</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Warea carteri">Warea carteri</a></span></em></td>
                    <td><span>Carter's Mustard</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><p class="label">Fishes</p></td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Acipenser brevirostrum"><em>Acipenser brevirostrum</em></a></td>
                    <td><span>Shortnose Sturgeon</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Pristis pectinata"><em>Pristis pectinata</em></a></td>
                    <td><span>Smalltooth Sawfish</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><em><a href="../taxa/index.php?taxon=Rivulus marmoratus">Rivulus marmoratus</a></em></td>
                    <td><span>Mangrove Rivulus</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><p class="label">Reptiles &amp; Amphibians</p></td>
                </tr>
                <tr>
                    <td><i>Alligator mississippiensis</i></td>
                    <td><span>American Alligator</span></td>
                    <td>SSC 1, 3</td>
                    <td>T1</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Caretta caretta"><em>Caretta caretta</em></a></td>
                    <td><span>Loggerhead Sea Turtle</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Chelonia mydas"><em>Chelonia mydas</em></a></td>
                    <td><span>Green Sea Turtle</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><i>Crocodylus acutus</i></td>
                    <td><span>American Crocodile</span></td>
                    <td>E</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><i>Dermochelys coriacea</i></td>
                    <td><span>Leatherback Sea Turtle</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Drymarchon couperi">Drymarchon couperi</a></span></em></td>
                    <td><span>Eastern Indigo Snake</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><i>Eretmochelys imbricata imbricata</i></td>
                    <td><span>Atlantic Hawksbill Sea Turtle</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Gopherus polyphemus"><em>Gopherus polyphemus</em></a></td>
                    <td><span>Gopher Tortoise</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Lepidochelys kempii</i></td>
                    <td><span>Kemp's Ridley Sea Turtle</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><i>Lithobates capito</i></td>
                    <td><span>Gopher Frog</span></td>
                    <td>SSC 1, 2</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Macrochelys temminckii</i></td>
                    <td><span>Alligator Snapping Turtle</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Nerodia clarkii taeniata</i></td>
                    <td><span>Atlantic Salt Marsh Snake</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><i>Pituophis melanoleucus mugitus</i></td>
                    <td><span>Florida Pine Snake</span></td>
                    <td>SSC 2</td>
                    <td>---</td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><p class="label">Birds</p></td>
                </tr>
                <tr>
                    <td><i>Aphelocoma coerulescens</i></td>
                    <td><span>Florida Scrub Jay</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Aramus guarauna"><em>Aramus guarauna</em></a></td>
                    <td><span>Limpkin</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Athene cunicularia</i></td>
                    <td><span>Burrowing Owl</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Campephilus principalis</i></td>
                    <td><span>Ivory-billed Woodpecker</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><i>Caracara cheriway</i></td>
                    <td><span>Crested Caracara</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Charadrius melodus"><em>Charadrius melodus</em></a></td>
                    <td><span>Piping Plover</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><i>Dendroica kirtlandii</i></td>
                    <td><span>Kirtland's Warbler</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Egretta caerulea"><em>Egretta caerulea</em></a></td>
                    <td><span>Little Blue Heron</span></td>
                    <td>SSC 1, 4</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Egretta rufescens"><em>Egretta rufescens</em></a></td>
                    <td><span>Reddish Egret</span></td>
                    <td>SSC 1, 4</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Egretta thula"><em>Egretta thula</em></a></td>
                    <td><span>Snowy Egret</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Egretta tricolor"><em>Egretta tricolor</em></a></td>
                    <td><span>Tricolored Heron</span></td>
                    <td>SSC 1, 4</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Eudocimus albus"><em>Eudocimus albus</em></a></td>
                    <td><span>White Ibis</span></td>
                    <td>SSC 2</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Falco sparverius paulus</i></td>
                    <td><span>Southeastern American Kestrel</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Grus canadensis pratensis</i></td>
                    <td><span>Florida Sandhill Crane</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><em>Haematopus palliatus</em></td>
                    <td><span>American Oystercatcher</span></td>
                    <td>SSC 1, 2</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Mycteria americana</i></td>
                    <td><span>Wood Stork</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><a href="../taxa/index.php?taxon=Pelecanus occidentalis"><em>Pelecanus occidentalis</em></a></td>
                    <td><span>Brown Pelican</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Picoides borealis</i></td>
                    <td><span>Red-cockaded Woodpecker</span></td>
                    <td>SSC</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Ajaia ajaja">Ajaia ajaja</a></span></em></td>
                    <td><span>Roseate Spoonbill</span></td>
                    <td>SSC 1, 4</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Rostramus sociabilis plumbeus</i></td>
                    <td><span>Everglade Snail Kite</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><i>Rynchops niger</i></td>
                    <td><span>Black Skimmer</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Sterna antillarum</i></td>
                    <td><span>Least Tern</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Sterna dougallii</i></td>
                    <td><span>Roseate Tern</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr class="heading">
                    <td colspan="4"><p class="label">Mammals</p></td>
                </tr>
                <tr>
                    <td><i>Oryzomys palustris natator</i></td>
                    <td><span>Silver Rice Rat</span></td>
                    <td>E</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><i>Peromyscus polinotus niveiventris</i></td>
                    <td><span>Southeastern Beach Mouse</span></td>
                    <td>T</td>
                    <td>T</td>
                </tr>
                <tr>
                    <td><i>Podomys floridanus</i></td>
                    <td><span>Florida Mouse</span></td>
                    <td>SSC 1</td>
                    <td>---</td>
                </tr>
                <tr>
                    <td><em><span><a href="../taxa/index.php?taxon=Trichechus manatus latirostris">Trichechus manatus latirostris</a></span></em></td>
                    <td><span>Florida Manatee</span></td>
                    <td>E</td>
                    <td>E</td>
                </tr>
                <tr>
                    <td><i>Ursus americanus floridanus</i></td>
                    <td><span>Florida Black Bear</span></td>
                    <td>T</td>
                    <td>---</td>
                </tr>
            </table>

            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td><p class="title">References</p>
                        <p class="body"><a href="http://www.fao.org/fishery/topic/16310/en">CITES</a>. Convention on International Trade in Endangered Species of Wild Fauna and Flora. Food &amp; Agriculture Organization of the United Nations.</p>
                        <p class="body"><a href="http://www.epa.gov/lawsregs/laws/esa.html">Endangered Species Act of 1973</a>. Public Law 93-205. United States Environmental Protection Agency.</p>
                        <p class="body"><a href="http://ecos.fws.gov/tess_public/pub/stateListingAndOccurrenceIndividual.jsp?state=FL">Endangered &amp; Threatened Species in Florida</a>. U.S. Fish &amp; Wildlife Species Reports. Environmental Conservation Online System.</p>
                        <p class="body"><a href="https://www.flrules.org/gateway/ruleno.asp?id=5B-40.0055">Florida Regulated Plant Index</a>. 5B-40.0055. Department of Agriculture &amp; Consumer Services. Division of Plant Industry.</p>
                        <p class="body"><a href="http://myfwc.com/wildlifehabitats/imperiled/">Florida's Endangered &amp; Threatened Species List</a>. May 2011. Florida Fish &amp; Wildlife Conservation Commission.</p>
                        <p class="body"><a href="http://www.fl-dof.com/forest_management/plant_conserve_list.html">Florida's Federally Listed Plant Species</a>. Florida Department of Agriculture &amp; Consumer Services. Florida Forest Service.</p></td>
                </tr>
            </table>
            <table style="width:700px;margin-left:auto;margin-right:auto;" cellpadding="4">
                <tr>
                    <td><p class="footer_note">Compiled  and edited by LH Sweat <br />
                            For questions, comments or contributions, <br />
                            please contact us at:<br>
                            <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu</a></p></td>
                </tr>
            </table>
        </div>
		<?php
        include(__DIR__ . '/../footer.php');
		?>
	</body>
</html>
