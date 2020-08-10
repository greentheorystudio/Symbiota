<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$beachPlantArr = $IRLManager->getChecklistTaxa(1);
$beachAnimalArr = $IRLManager->getChecklistTaxa(2);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Beach Habitats</title>
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
    <h2>Beach Habitats</h2>
    <table cellpadding="5" cellspacing="3" style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img border="0" src="../content/imglib/Beach2.jpg" hspace="15" vspace="15" width="535">
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="5">
        <tr>
            <td>
                <p class="body">The Florida peninsula is bordered by 1900 km (1181 miles)
                    of tidal coastline, second only to Alaska. Of this total, approximately 1200 km
                    (746 miles), primarily along Florida's east coast, consists of sandy beaches
                    where high energy waves constantly break along the shoreline. In northern areas
                    along the east coast of Florida, including the northern Indian River Lagoon
                    area, sands are composed principally of quartz that originated in the
                    Appalachian Mountains, and calcium carbonate from rock and shell deposits.
                    Further south, the amount of quartz in sand decreases steadily, and sand
                    composition becomes primarily calcium carbonate.</p>
                <p class="body">Beaches lie at the interface between the land and the
                    ocean. East coast beaches in Florida, especially those in the Indian River
                    Lagoon area, are generally dynamic, high-energy, areas. The unique topography
                    and slope of any beach area is the result of interactions between both abiotic
                    and biotic factors. Key physical processes in beach and dune formation are wave
                    action, erosion, sand accretion by winds, overwash, and the deposition of salt
                    spray (Stalter 1976; Tyndall 1985). Important biotic factors generally center
                    around the ability of plants to colonize and grow while withstanding the adverse
                    effects of being buried in sand and inundated by sea water. Additionally,
                    colonizing species of plants must also be able to tolerate the xeric conditions which result from
                    sand being generally well drained
                    with low nutrient availability.</p>
                <p class="body">The slope of a beach and the shape of its dunes are
                    heavily influenced by tides, wind patterns, storm events and the movement of
                    sand that often accompanies these events. Sand is typically deposited on beaches
                    as waves break on the shoreline and their energy dissipates. Whatever
                    particulates that had been suspended in the wave are deposited on the beach and
                    then dragged down the face of the beach again in the wave's backwash. Since
                    the energy of backwash tends to be far less than the initial energy of the wave,
                    there is typically a net onshore transport of sand. However, hurricanes and
                    their accompanying storm surges often have the effect of either eroding sands
                    offshore, or overwashing and destabilizing dune systems, redepositing sands
                    inland.</p>
                <p class="body">Wave action tends to shape the beach slope as well,
                    with high-energy waves tending to increase the steepness of the slope, and
                    lower-energy waves resulting in flatter beach profiles. On high-energy beaches
                    in the IRL region, beach profiles change seasonally. In summer, waves tend to
                    occur as swells that move sediments up the beach, building berms and providing
                    sands for dunes. However, during fall and winter, the steep waves that accompany
                    storms erode beaches and flatten out the beach profile, depositing eroded sands
                    seaward on longshore bars (Bearman {ed.} 1989).</p>
                <p class="title">Beach Plants:</p>
                <p class="body">Due to sometimes intense wave action, strong winds, and the presence of sea
                    water, most plants are unable to successfully colonize beach areas directly
                    along the shoreline. However, several species are able to become established in
                    the upper beach zone, thus enabling sand stabilization and subsequent
                    development of dune systems. Plants occurring on beaches and dunes tend to
                    occupy specific regions according to their individual growth patterns and
                    environmental tolerances. Most beach plants occupy the area closest to the
                    shoreline in the pioneering zone, which extends landward from the wrack line on the upper beach
                    through the dune area. Pioneering species must be
                    highly specialized to tolerate the severe environmental challenges they face.
                    The most successful pioneering species in coastal zones are halophytic, meaning
                    that they are able to thrive under highly saline conditions. Many of these same
                    plants also have high growth rates, with some plants actually stimulated to grow
                    faster as they become buried in sand.</p>
                <p class="body">Most pioneering species are also able to withstand
                    xeric conditions, low nutrient availability, heavy winds,
                    inundation by sea water, high soil temperatures, and burial in sand. Pioneering
                    species are generally vine-like or succulent, having waxy or hairy coverings on
                    their stems and leaves. They produce many seeds that are widely disbursed,
                    helping them to become quickly established or recolonized on beach areas.
                    Pioneering species also tend to spread rapidly as they grow, creating a network
                    of creeping stems so if one part of the plant is uprooted or buried in shifting
                    sand, other portions can continue to grow. Their roots also help to anchor sand,
                    and thus assist in subsequent dune building and stabilization.</p>
                <p class="body">Beyond the pioneering zone, in the shelter of swales
                    and secondary dunes, plants are generally more protected from the effects of
                    salt spray, seawater and sand burial, and the resulting communities can be much
                    more diverse. When dunes become established and remain stable over time, plants
                    continue to grow and reproduce, eventually enriching the sandy soil with humus
                    from leaf litter and decaying plants. As humus accumulates, soils become richer
                    and hold more water. This allows other types of vegetation to take root, and
                    begins the process of succession whereby vine-like or herbaceous pioneering
                    species are eventually replaced by shrubs and trees.</p>
                <p class="body">Key species of plants that colonize the upper beach
                    zone include salt-tolerant pioneering species such as shoreline sea purslane (<i>Sesuvium
                        portulacastrum</i>), seashore dropseed (<i>Sporobolus virginicus</i>), beach peanut
                    (<i>Okenia hypogaea</i>), railroad vine (<i>Ipomoea pes-caprae</i>), West Indian
                    sedge (<i>Remirea maritima</i>) and knotgrass (<i>Paspalum distichum</i>).</p>
                <p class="title">Beach Animals:</p>
                <p class="body">At first glance, beaches may appear to support comparatively few animal
                    species; however, beaches are complex habitats that support many species of
                    animals unique to shorelines, many of them too small to notice. Successful
                    animal inhabitants of beaches include the often overlooked but highly abundant
                    meiofauna that live
                    between sand grains, and the more familiar species of annelid worms that
                    burrow into the substratum. Various bivalve and snail species, as well as many
                    species of small crustaceans such as isopods and amphipods inhabit the wrack
                    line along the shore. Surf clams and mole crabs are 2 species that stand out as
                    inhabitants of the surf zone. Both of these animals are extremely fast
                    burrowers, able to rebury themselves almost as fast as they become exposed in
                    shifting sands. The surf clam, also known as the variable coquina (<i>Donax
                        variabilis</i>), is a filter feeder that uses its gills to filter microalgae,
                    tiny zooplankton, and small particulates out of seawater. The mole crab (<i>Emerita
                        talpoida</i>) is a suspension feeder that feeds by capturing zooplankton with
                    its antennae. Further up the beach, somewhat removed from intense wave action,
                    is where the ghost crab (<i>Ocypode quadrata</i>) makes its home by burrowing into the
                    sand.</p>
                <p class="body">Although many species of birds are often observed on
                    beaches, only 5 species of shorebirds: 1) the snowy plover (<i>Charadrius
                        alexandrinus</i>), 2) the black skimmer (<i>Rynchops niger</i>), 3) the least
                    tern (<i>Sterna antillarum</i>), 4) the royal tern (<i>Sterna maxima</i>), and
                    5) the sandwich tern (<i>Sterna sandvicensis</i>) prefer nesting sites on bare
                    sands in the upper beach zone. Snowy plovers, however, are known to nest only
                    along the Gulf coast of Florida.</p>
                <p class="body">Additionally, of the 7 species of sea turtles, 6 are
                    dependent on Florida beaches for nesting during the summer. In fact, the Florida
                    coastline is the most important nesting area for sea turtles in the western
                    Atlantic, though sites in Natal, South Africa, and the islands of the Red Sea
                    are also utilized (Johnson and Barbour 1990). In Florida, loggerhead turtles and
                    green turtles are by far the most commonly observed, with loggerheads laying an
                    average of 3000-4000 nests per year on Florida beaches, and green turtles laying
                    approximately 300 nests per year. Highest sea turtle nest densities are observed
                    in southern Brevard County, from south of Cape Canaveral to Sebastian Inlet,
                    though sea turtles nest even along the highly developed beaches of Broward and
                    Dade counties. In human-impacted areas, it is often necessary to dig up turtle
                    nests and rebury the eggs in other areas to insure successful hatching.</p>
                <p class="body">Many species utilize beaches as feeding areas.
                    Sandpipers and other shorebirds, wading birds, and even some fish such as the
                    Florida pompano employ the surf zone to prey on animals that either wash out of
                    the sand due to wave action, or come close enough to the shore to be captured.
                    Some mammals are also known to utilize beaches as feeding grounds. Among these
                    are raccoons, feral cats and foxes, which are known to patrol the wrack line at
                    the high water mark and scavenge eggs from sea turtle nests (Myers and Ewel,
                    1990).</p>
                <p class="title">Human Impacts:</p>
                <p class="body">Florida's barrier islands have been extensively developed and support a
                    large human population, leaving little of the original landscape unaltered.
                    Florida's beaches are rated among the finest in the U.S. and draw tourists
                    from all over the globe. Florida's visitors come to swim, surf, bask in the
                    sun, snorkel, fish and sail; and as a result, tourism has become the premier
                    industry in Florida, providing more than 845,000 jobs. In 1999 alone,
                    approximately 58.9 million tourists visited Florida, spending 46.7 billion
                    dollars. While development and tourism have been an economic boon to Florida,
                    they have also brought associated problems that must be continually addressed.</p>
                <p class="title">Select a highlighted link below to learn more about
                    that species:</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="5"
           class="table-border no-border alternate">
        <tr>
            <th width="162">
                Species Name
            </th>
            <th width="157">
                Common Name
            </th>
            <th>
                Habitat Notes
            </th>
        </tr>
        <?php
        if($beachPlantArr){
            ?>
            <tr class="heading">
                <td><p class="label">Beach Plants:</p></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            foreach($beachPlantArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['habitat'].'</span></td>';
                echo '</tr>';
            }
        }
        if($beachAnimalArr){
            ?>
            <tr class="heading">
                <td><p class="label">Beach Animals:</p></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            foreach($beachAnimalArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '<td><span>'.$taxArr['habitat'].'</span></td>';
                echo '</tr>';
            }
        }
        ?>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td>
                <p class="title">Further Reading:</p>
                <p class="body">Austin
                    1998. Classification of plant communities in south Florida. Internet
                    document.
                </p>
                <p class="body">Carter, R.W.G., T.G.F. Curtis, and M.J.
                    Sheehy-Skeffington. 1992. Coastal dunes geomorphology, ecology and
                    management for conservation. A.A. Balkema/Rotterdam/Brookfield.</p>
                <p class="body">Florida
                    Natural Areas Inventory, Department of Natural Resources.
                    1990. Guide to the Natural Communities of Florida.
                    Publication. 11pp. Tallahassee, FL.</p>
                <p class="body">Komar, P.D. and Moore, J.R., editors.
                    1983. CRC handbook of coastal processes and erosion. CRC Press, Inc.
                    Boca Raton, Florida.</p>
                <p class="body">Komar, P.D. 1998. Beach processes and
                    sedimentation, 2<sup>nd</sup> edition. Prentice Hall, Upper Saddle
                    River, New Jersey.</p>
                <p class="body">Myers, R.L. and J.J. Ewel, eds. 1990.
                    Ecosystems of Florida. University of Central Florida Press, Orlando, FL.
                    765 pp.</p>
                <p class="body">Oertel, G.F. and M. Lassen. 1976.
                    Developmental sequences in Georgia coastal dunes and distribution of
                    dune plants. Bull. GA. Acad. Sci. 34: 35 - 48.</p>
                <p class="body">Otvos, E.G. 1981. Barrier island
                    formation through nearshore aggradation
                    stratigraphic and field
                    evidence. Mar. Geol. 43:195-243.</p>
                <p class="body">Packham, J.R. and A.J. Willis. 1997.
                    Ecology of dunes, salt marsh and shingle. Chapman and Hall, London.</p>
                <p class="body">Pethick, J. 1984. An introduction to
                    coastal geomorphology. Edward Arnold, London.</p>
                <p class="body">Pilkey, O.H. and M.E. Feld. 1972.
                    Onshore transport of continental shelf sediment: Atlantic southeastern
                    United States. In: Swift, D.J.P., D.B. Duane and O.H. Pilkey, eds. Shelf
                    Sediment Transport: Process and Pattern. Dowden, Hutchinson, Ross. Stroudsburg, PA</p>
                <p class="body">Schmalzer, P.A., B.W. Duncan, V.L.
                    Larson, S. Boyle, and M. Gimond. 1996.<br>
                    Reconstructing historic
                    landscapes of the Indian River Lagoon. Proceedings of Eco-Informa '96.
                    11:849 - 854. Global Networks for Environmental Information, Environmental Research Institute of
                    Michigan (ERIM), Ann Arbor, MI</p>
                <p class="body">Stalter, R. 1976. Factors affecting
                    vegetational zonation on coastal dunes, Georgetown County, SC. In: R.R.
                    Lewis, and D.P. Cole, eds. 3<sup>rd</sup> Proc. Annu. Conf. Restoring Coastal Veg. Fla. Hillsborough
                    Comm. Coll., Tampa, FL</p>
                <p class="body">Stalter, R. 1993. Dry coastal
                    ecosystems of the eastern United States of America. In: Ecosystems of
                    the World. Volume 2. Elsevier Science Publications, New York, NY.</p>
                <p class="body">Tyndall, R.W. 1985. Role of seed
                    burial, salt spray, and soil moisture deficit in plant distribution on
                    the North Carolina Outer Banks. Ph.D. Thesis, University of Maryland,
                    College Park, MD.</p>
                <p class="body">Wagner, R.H. 1964. The ecology of <i>Uniola
                        paniculata</i> L. in the dune-strand habitat of North Carolina. Ecol.
                    Monogr. 34: 79 - 96.</p>
                <br/>
                <p class="footer_note">
                    Report by: K. Hill, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu</a>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
