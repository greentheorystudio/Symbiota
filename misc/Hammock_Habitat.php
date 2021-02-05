<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$plantArr = $IRLManager->getChecklistTaxa(6);
$animalArr = $IRLManager->getChecklistTaxa(7);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Maritime Hammock Habitats</title>
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
    <h2>Maritime Hammock Habitats</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td align="center"><img border="0" src="../content/imglib/Mar_Hammock1.jpg" hspace="5" vspace="5"
                                    width="393" height="323"></td>
        </tr>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td>
                <p class="body">Maritime hammocks, also known as maritime forests,
                    tropical hammocks or coastal hammocks, are characterized as narrow bands
                    of forest that develop almost exclusively on stabilized backdunes of
                    barrier islands, inland of primary dunes and scrub. Maritime forests
                    occur discontinuously along the entire Atlantic coast of the United
                    States, interrupted by natural features such as inlets and bays, and
                    anthropogenic activities such as coastal development and agriculture.
                    Adjacent maritime forests tend to be vegetatively similar to one
                    another, but overall vegetation profiles change with latitude. <br/>
                    Florida,
                    which has the longest coastline in the contiguous United States, has
                    approximately 468,000 acres (Bellis 1995) of barrier islands, with
                    maritime forests occupying the highest, most stable areas of these
                    islands. <br/>
                    The present location and extent of today's maritime forests
                    were established approximately 5000 years ago, becoming stabilized as
                    sea level rise declined from 0.3 m to 0.1 m per century (Bellis 1995).</p>

                <p class="body">Generally dominated by species of broad-leaved
                    evergreen trees and shrubs, maritime hammocks are climax communities
                    influenced heavily by salt spray. Soils are predominantly composed of
                    either sand or peat. Sandy soils are more common along forested dune
                    ridges, while peat is more common among interdune swales and wetlands (Bellis
                    1995). Chapman (1976) described the progress of sandy soil formation and
                    dune stabilization through four stages: embryo dunes, yellow dunes, gray
                    dunes, and mature, vegetated dunes. Embryo dunes are formed by newly
                    deposited sands accreting on beaches. <br/>
                    Over time, sea oats and other
                    coastal plants may colonize the dune and increase its stability. Once
                    this occurs, the dune is called a yellow dune. Gray dunes are
                    characterized by the presence of woody species and shrubs. At this
                    stage, a soil microfauna, consisting largely of mycorrhizae has developed, and organic material from
                    dead leaves
                    and stems begins to accumulate in the substrata. Should these gray dunes
                    remain stable over long periods of time, the climax community of a
                    maritime forest develops. <br/>
                    Mature vegetated dunes are characterized by
                    distinct soil profiles: an upper horizon consisting of leaf litter and
                    twigs; a deeper, ashy white horizon that results from leeching of
                    organic materials deeper into the soil; and beneath this, a tan or
                    orange horizon which receives substances leeched from above.</p>


                <p class="body">Many factors influence whether particular
                    species will be successful colonizers of the maritime forest. Strong
                    winds, low nutrients, unpredictable supplies of freshwater, erosion,
                    sand-blasting, storm exposure, sand migration, and overwash from the
                    ocean during storm events, are all major influences; however, tolerance
                    to salt spray has been found to be the principal factor that controls
                    vegetative cover in maritime forests (Oosting and Billings 1942, Boyce
                    1954, Proffitt 1977, Seneca and Broome 1981). <br/>
                    Trees closest to the ocean
                    are subject to onshore winds carrying sand and salt spray, which acts
                    not only to prune terminal buds in the canopy top, but also encourages
                    growth of lateral buds, producing over time, the familiar windswept
                    shape of maritime forest canopies. Streamlining of the canopy profile
                    assists growth of maritime forests in several ways. <br/>
                    First, the windswept
                    profile of the maritime forest canopy helps to deflect winds up and over
                    the forest, preventing trees from being uprooted during intense storms.
                    Second, dense canopies provide shelter to understory plants and protect
                    the understory from large temperature fluctuations, reducing warming of
                    the soil during the day, and preventing heat loss at night. Third,
                    because trees on the windward edges of the forest show increased growth
                    in their lateral buds, they are somewhat denser overall than more
                    interior trees. As winds blow across the dense canopy, salt spray is
                    deposited. <br/>
                    Interior trees are thus protected from the effects of salt
                    spray by the windward trees. This feature allows trees in the interior
                    forest to assume characteristic heights and growth patterns resembling
                    those of mainland forests.</p>

                <p class="body">Fire is also considered an
                    &quot;organizer&quot; of forest cover patterns on barrier islands in
                    Florida (Bellis 1995), and has long been a traditional agricultural tool
                    for maintaining open areas, improving grazing lands, and eliminating
                    pest species. Fire characteristics differ between oak-dominated
                    hammocks, and pine-dominated hammocks. <br/>
                    In oak forests, a dense evergreen
                    canopy is usually coupled with a sparse, shade-tolerant understory and a
                    somewhat moist litter layer. In pine forests, dense understory
                    vegetation is coupled with a tall, sparse canopy, and significantly
                    drier soils. Thus, fires in pine forests are likely to have a large fuel
                    source close to the ground, resulting in the increased likelihood of
                    intense crown fires. Conversely, oak forests have less fuel at ground
                    level due to a sparsely grown understory. <br/>
                    When fire occurs, oak forests
                    tend to smolder close to the ground, consequently making intense crown
                    fires more rare. An examination of fire temperatures in pine vs. oak
                    forests illustrates these characteristics. <br/>
                    A study by Williamson and
                    Black (1981) documented that during a fire, air temperatures from the
                    seedling zone to approximately 0.5 m above the soil in pine forests
                    averaged 290&deg;C, while oak forests averaged
                    175&deg;C. This is significant because pines
                    are often considered to be inferior long-term competitors to oaks.
                    However, Williamson and Black (1981) concluded that maximum temperatures
                    during fires in mixed forests were high enough to eliminate oaks from an
                    area entirely. Thus, even though pines may be inferior competitors to
                    oaks, they may gain competitive advantage over oaks in areas where fires
                    occur.</p>

                <p class="body">Maritime forests also have distinctive
                    hydrological features that affect a barrier island's natural
                    communities, as well as help determine whether development can be
                    sustained. Rainfall is generally the only source of fresh water on
                    barrier islands, and the maritime forest community acts as the primary
                    watershed. Precipitation entering the watershed is rapidly drawn deep
                    into a <a href="Hammock_FWLens.php"> freshwater lens</a>, which floats above
                    the denser salt water in the permeable sediments beneath barrier
                    islands. <br/>
                    A counter-flow is established at the area of contact between
                    fresh and salt water, allowing freshwater at the periphery of the lens
                    to seep upward to the surface and into the ocean or lagoon. Hydrological
                    models show that under ideal conditions, the freshwater lens on a
                    barrier island contains approximately 40 meters of freshwater for each
                    meter of free water table above mean sea level (Ward 1975). Water in the
                    lens is generally fairly low in salts (Proffitt 1977), in spite of the
                    fact that salt spray is a major ecological influence. However, excessive
                    pumping of freshwater from the lens for residential and commercial
                    purposes can lead to loss of the hydrostatic head in the freshwater
                    lens, which could, in turn, increase the rate of salt water intrusion
                    into surface waters on the island (Ward 1975, Winner 1975, 1979; Bellis
                    1995).</p>
                <p class="body">Beyond effective water management, there are a
                    variety of other development considerations regarding maritime forest
                    communities, with habitat fragmentation perhaps being the largest issue.
                    Because maritime forests occur on the most stable areas of barrier
                    islands, they are attractive building sites. Clearing lots for houses
                    involves disturbing or destroying most, if not all, the natural
                    vegetative cover to make space for homes, parking areas, drainage
                    fields, and septic systems. Following construction, native vegetation is
                    often replaced by lawns and ornamental shrubs, many of which are exotic.
                    <br/>
                    Another issue regarding the development of barrier islands is road
                    construction. Generally, at least one main road is constructed along the
                    entire length of a barrier island, above the dune ridge at the perimeter
                    of maritime forests, to permit easy access to beaches. Other roads are
                    built laterally to the trunk road for access to developments and private
                    residences. While roads themselves may minimally impact existing
                    forests, they do threaten their growth patterns and species composition
                    because opening the forest canopy allows increased salt penetration to
                    the forest interior.</p>

                <p class="body">Several studies have confirmed that road building
                    on barrier islands affects salt transport patterns into the interior of
                    maritime forests (Eaton 1979, Seneca and Broome 1981). In these studies,
                    floristic composition, tree viability and canopy height remained nearly
                    constant along the ocean-side perimeter of maritime forests where newly
                    established roads had been constructed. However, along the bay-side of
                    the forests, it was observed that significant die back due to increased
                    salt penetration occurred. Over the 4 years of the study, 57% of the
                    original above-ground vegetation died within 2.5 to 3 m from the bayside
                    edge of the forests, with the most severely affected areas showing
                    complete elimination of the forest canopy. However, die-back in trees
                    was observed to have ceased after approximately 27 months, with 43% of
                    trees able to recover to some degree, showing signs of basal sprouting,
                    stump sprouting, and sprouting from underground stems and roots. At the
                    end of the 4 year study, a new but somewhat lower canopy had begun to
                    develop.</p>
                <p class="body">The vegetative composition of maritime forests
                    is diverse, and depends heavily on prevailing physical conditions.
                    Greller (1980) mapped the distribution of maritime forests in Florida,
                    and determined that the upland broad-leaved forests of barrier islands
                    fell into 3 major types: temperate broad-leaved forest, also known as
                    evergreen forest; southern mixed hardwood forest; and tropical forest.
                    Temperate broad-leaved forests are dominated by <i>Quercus virginiana</i>
                    (live oak), and <i>Sabal palmetto</i> (sabal palm) communities. Southern
                    mixed hardwood forests are dominated by <i>Magnolia grandiflora</i>
                    (Southern magnolia), <i>Ilex opaca</i> (American holly), <i>Cornus
                        florida</i> (flowering dogwood), <i>Carya glabra</i> (pignut hickory),
                    and <i>Fagus grandiflora</i> (American beech). Tropical forests are
                    dominated by both evergreen and deciduous species such as <i>Mastichodendron
                        foetidissimum</i> (mastic), <i>Eugenia</i> spp. (stoppers), <i>Lysiloma
                        latisiliqua</i> (wild tamarind), and <i>Bersera simaruba</i> (gumbo
                    limbo).</p>
                <p class="body">Many different animal species inhabit Florida's
                    barrier island communities. In maritime hammocks, insects, small
                    mammals, reptiles and birds dominate the fauna. Common inhabitants
                    include wading birds such as great blue herons (<i>Ardea herodias</i>),
                    great egrets (<i>Casmerodius albus</i>), snowy egrets (<i>Egretta thula</i>),
                    little blue herons (<i>Egretta caerulea</i>), tricolored herons (Egretta
                    tricolor), night herons (<i>Nycticorax</i> spp.), brown pelicans (<i>Pelicanus
                        occidentalis</i>), various ducks, warblers, and others. Birds of prey
                    such as red-shouldered hawks (<i>Buteo lineatus</i>), Cooper's hawks (<i>Accipiter
                        cooperii</i>), sharp-shinned hawks (<i>Accipiter striatus</i>), and bald
                    eagles (<i>Haliaetus leucocephalus</i>), also utilize hammocks for
                    feeding, roosting and nesting. Small mammals such as eastern cottontails
                    (<i>Sylvilagus palustris</i>), mice (<i>Mus</i> spp.), Norway rats (<i>Rattus
                        norvegicus</i>); and larger mammals such as river otters (<i>Lontra
                        canadensis</i>), and wild boar (<i>Sus scrofa</i>), may thrive in
                    hammock habitats. Reptiles include softshelled turtles (<i>Frionyx ferox</i>),
                    gopher tortoises (<i>Gopherus polyphemus</i>), cottonmouth snakes (<i>Agkistodon
                        piscivorus</i>), southern black racers<i> (Coluber constrictor priapus</i>),
                    Atlantic saltmarsh snakes (<i>Nerodia</i> spp.), eastern diamondback
                    rattlesnakes (<i>Crotalus adamantus</i>), indigo snakes (<i>Drymarchon
                        corais couperi</i>), as well as a variety of skinks and lizards which
                    prey on the abundant insect, frog, and small mammal population.</p>
                <p class="title">Click a highlighted link to read more about individual species:</p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="7" cellspacing="1"
           class="table-border no-border alternate">
        <tr>
            <th>Species Name</th>
            <th>Common Name</th>
            <th>Community Type</th>
        </tr>
        <?php
        if($plantArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Maritime Hammock Plants:</p></td>
            </tr>
            <?php
            foreach($plantArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        if($animalArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Maritime Hammock Animals:</p></td>
            </tr>
            <?php
            foreach($animalArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['notes'].'</span></td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">
                    <sup>1</sup> Found throughout the IRL<br/>
                    <sup>2</sup> Most common in Northern IRL, Volusia through Brevard Counties<br/>
                    <sup>3</sup> Most common in Central/Southern IRL<br/>
                    <sup>4</sup> Found from Cape Canaveral to Ft. Pierce Inlet; to south is replaced with tropical
                    shrubs and trees</font></p></td>
        </tr>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="title">Further Reading:</p>
                <p class="body">Art, H., F.H. Bormann, G.K. Voigt, and G.M.
                    Woodwell. 1974. Barrier island forest ecosystem: role of meteorological inputs.
                    Science 184:60 - 62.</p>
                <p class="body">Bagur, J.D. 1978. Barrier islands of the
                    Atlantic and Gulf coasts of the united States: an annotated bibliography. U.S. Fish and Wildlife
                    Service FWS/OBS - 77/56. 215 pp.</p>
                <p class="body">Barrick, W.E. 1973. Salt tolerant plants for
                    Florida landscapes. Proceedings of the Florida State Horticultural Society 91:82 - 84.</p>
                <p class="body">Bellis, V.J. 1992. Floristic continuity among
                    the maritime forests of the Atlantic coast of the United States. Pages 21-29 <i>in</i>: C.A. Cole
                    and F.K. Turner, editors. Barrier island Ecology of the Mid-Atlantic Coast: A Symposium.
                    U.S. Department of the Interior, National Park Service, Atlanta, GA.</p>
                <p class="body">Bellis, V.J. 1995. Ecology of maritime forests
                    of the southern Atlantic coast: a community profile. Biological report 30, May 1995.
                    National Biological Service, U.S. Department of the Interior. Washington, D.C. 89 pp.</p>
                <p class="body">Bellis, V.J. and C.E. Proffitt. 1976. Maritime
                    forests. Pages 22 - 28 in: D. Brower, D. Frankenberg, and F. Parker, editors. Ecological
                    Determinants of Coastal Area Management: Vol. 2. University of North
                    Carolina Sea Grant Publication UNC-GS-76-05. 392 pp.</p>
                <p class="body">Bourdeau, P.F. an H.J. Oosting. 1959. The
                    maritime live oak forest in North Carolina. Ecology 40:148-152.</p>
                <p class="body">Boyce, S.G. 1954. The salt spray community.
                    Ecological Monographs 24:29-68.</p>
                <p class="body">Cockfield, B.A. J.B. Tormey, and D.M. Forsythe.
                    1980. Barrier island maritime forest. American Birds 34: 29.</p>
                <p class="body">Cowardin, L.M., V. Carter, F.C. Golt, and E.T.
                    LaRoe. 1979. Classification of wetlands and deep water habitats of the United States.
                    U.S. Fish and Wildlife Service FWS/OBS-79-31. 103 pp.</p>
                <p class="body">Davison, K. and S.P. Bratton. 1986. The
                    vegetation history of Canaveral National Seashore, Florida. University of Georgia
                    Institute of Ecology. Cooperative Park Studies Unit Technical Report 22. 75 pp.</p>
                <p class="body">Doutt, J.K. 1941. Wind pruning and salt spray
                    as a factor in ecology. Ecology 22:195-196.</p>
                <p class="body">Eaton, T.E. 1979. Natural and artificially
                    altered patterns of salt spray across a forested barrier island. Atmospheric Environment
                    13:705-709.</p>
                <p class="body">Gehlhausen, S. and M.G. Harper. 1998.
                    Management of maritime communities for threatened and endangered species. U.S. Army
                    Corps of Engineers, Construction Engineering Research Laboratories Technical
                    Report 98/79, May 1998.</p>
                <p class="body">Greller, A.M. 1980. Correlation of some climate
                    statistics with distribution of broadleaved forest zones in Florida, USA. Bulletin of the
                    Torrey Botanical Club 107: 189-219.</p>
                <p class="body">Johnson, A.F. and M.G. Barbour. 1990. Dunes and
                    maritime forests. Pages 429-480 <i>in</i>: R.L. Myers and J.J. Ewell, editors. Ecosystems
                    of Florida. University Press of Florida, Gainesville, FL. 765 pp.</p>
                <p class="body">Oosting, H.J. 1945. Tolerance to salt spray of
                    plants of coastal dunes. Ecology 26:85-89.</p>
                <p class="body">Oosting, H.J. 1954. Ecological processes and
                    vegetation of the maritime strand in the southeastern United States. Botanical Review
                    20:226-262.</p>
                <p class="body">Oosting, H.J. and W.D. Billings. 1942. Factors
                    affecting vegetation zonation on coastal dunes. Ecology 23:131-142.</p>
                <p class="body">Proffitt, C.E. 1977. Atmospheric inputs and
                    flux of chloride, calcium and magnesium in a maritime forest on Bogue Bank, NC. M.A. Thesis,
                    East Carolina University, Greenville, NC. 123 pp.</p>
                <p class="body">Seneca, E.D. and S.W. Broome. 1981. The effect
                    of highway construction on maritime vegetation in North Carolina. A research report
                    submitted to the North Carolina Department of Transportation, Division of
                    Highways, Raleigh, NC. 73 pp.</p>
                <p class="body">Simon, D.M. 1986. Fire effects in coastal
                    habitats of east central Florida. University of Georgia Institute of Ecology. Cooperative Park
                    Studies Unit Technical Report 27.&nbsp;140 pp.</p>
                <p class="body">Ward, R.C. 1975. Principles of hydrology.
                    McGraw-Hill Ltd., Maidenhead, Berkshire, England. 367 pp.</p>
                <p class="body">Wells, B.W. 1942. Ecological problems of the
                    Southeastern United States coastal plain. Botanical Review 8:533-561.</p>
                <p class="body">Williamson, R.B. and E.M. Black. 1981. High
                    temperature of forest fires under pines as a selective advantage over oaks. Nature
                    293:643-644.</p>
                <p class="body">Winner, M.D. Jr. 1975. Groundwater resources of
                    the Cape Hatteras National Seashore, North Carolina. U.S. Geological survey Atlas
                    HA-540, Reston, VA. 2 maps.</p>
                <p class="body">Winner, M.D. Jr. 1979. Freshwater availability
                    of an offshore barrier island. U.S. Geological Survey Professional Paper 1150, U.S.
                    Geological Survey. 117 pp.</p>
                <p class="body">Zucchino, L.R. 1990. A guide to protecting
                    maritime forests through planning and design. North Carolina Department of Environment,
                    Health, and <span lang="en-us">N</span>atural Resources,
                    <span lang="en-us">D</span>ivision of Coastal Management. 24 pp.</p>

                <p class="footer_note">Report by: K. Hill, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></p></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
