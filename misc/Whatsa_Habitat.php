<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>The Indian River Lagoon: A Mosaic of Habitats</title>
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
    <h2 class="subheading">The Indian River Lagoon: A Mosaic of Habitats</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr valign="top">
            <td colspan="2">
                <map name="FPMap0">
                    <area href="Beaches.php" shape="rect" coords="399, 0, 530, 202">
                    <area href="Dunes.php" shape="rect" coords="344, 0, 398, 203">
                    <area href="Scrub.php" shape="rect" coords="229, 0, 343, 203">
                    <area href="Hammock_Habitat.php" shape="rect" coords="163, 0, 227, 203">
                    <area href="Mangroves.php" shape="rect" coords="126, 1, 161, 203">
                    <area href="Seagrass_Habitat.php" shape="rect" coords="86, 0, 124, 203">
                    <area href="Oyster_reef.php" shape="rect" coords="55, 0, 85, 203">
                    <area href="Mangroves.php" shape="rect" coords="1,-1,33,202">
                </map>
                <img border="0" src="../content/imglib/REVxs.gif" vspace="5" usemap="#FPMap0" width="531"
                     height="204"><br>
                <font size="2" color="#036"><span class="caption">Topographic profile of representative
              Indian River Lagoon habitats: (1) mangrove/salt marsh; (2) submerged 
              habitats within the Indian River Lagoon; (3) mangrove fringe; (4) 
              oak forest/maritime hammock; (5) oak scrub; (6) saw palmetto scrub; 
              (7) sea oats foredune; (8) beach.</span></td>
        </tr>
        <tr valign="top">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="50%"><p class="title"><a href="Barrierislnd.php">Barrier Island/Intertidal Habitats</a>: </p>
                <p class="body"><a href="Beaches.php">Beaches</a></p>
                <p class="body"><a href="Dunes.php">Dunes</a></p>
                <p class="body"><a href="Scrub.php">Scrub</a></p>
                <p class="body"><a href="Hammock_Habitat.php">Maritime Hammocks</a></p>
                <p class="body"><a href="Mangroves.php">Mangroves</a></p>
                <p class="body"><a href="impoundments.php">Mosquito Impoundments</a></p>
                <p class="body"><a href="Saltmarsh.php">Salt Marshes</a></p>
                <p class="body"><a href="Tidal_Flats.php">Tidal Flats</a></p>
                <br>
            </td>
            <td width="209" valign="top"><p class="title">Submerged Habitats:</p>
                <p class="body"><a href="Oyster_reef.php">Oyster Reefs</a></p>
                <p class="body"><a href="Seagrass_Habitat.php">Seagrass Beds</a></p>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="2"><p class="title">Other Useful Habitat Links:</p>
                <p class="body"><a href="Habitat_Threats.php">Threats to Habitats</a></p></td>
        </tr>
        <tr valign="top">
            <td colspan="2"><p class="title">
                    What is an ecosystem?</p>
                <p class="body">Ecosystems are defined as communities
                    of plants, animals and microorganisms found within a particular area,
                    interacting with each other and with the environment. Hence, the term
                    &quot;ecosystem&quot; encompasses both the biotic and abiotic (living
                    and non-living) components of a particular environment. Ecosystems
                    are complex and dynamic entities that use energy, produce wastes and
                    recycle nutrients. All ecosystems, whether marine or terrestrial,
                    are interconnected. What occurs in one ecosystem affects the dynamics
                    of another. Collectively, all ecosystems make up the biosphere, or
                    zone of life, which occurs in the thin outer layer of the Earth's
                    surface.</p>

                <p class="body">In most ecosystems, energy from
                    the sun is the initial power source that promotes growth in plants
                    and algae. Plants are autotrophic, meaning they are self-feeding.
                    The process of photosynthesis allows plants to take in light energy
                    from the sun and convert it into the chemical energy stored in sugars
                    and other carbohydrates produced by plants. Because production by
                    plants forms the base of all food webs in an ecosystem, and provides
                    food for other organisms, it is also called primary production.
                    Thus, plants are the producers in ecosystems. Consumers cannot produce
                    their own food, so must rely on ingesting other organisms in order
                    to obtain their energy. Consumers that eat only plants are called
                    herbivores; carnivores eat only animals; and omnivores consume a
                    combination of both plants and animals. Decomposers, such as bacteria
                    and fungi, assist in recycling nutrients by breaking down the complex
                    organic molecules in dead plant and animal tissues into simpler
                    substances that can be made available for reuse.</p>
                <p class="body">
                    Each time one organism consumes another, some of the energy from
                    the ingested organism is then taken up by the consumer. This transfer
                    of food energy from one organism to another is commonly referred
                    to as the food chain. Rarely, however, is this energy transfer a
                    simple, direct process. Rather, it can be highly dynamic and complex,
                    since most organisms eat more than one type of food and are therefore
                    involved in more than one food chain. The term food web more accurately
                    describes the complexity of food energy transfers between organisms
                    and the process of nutrient recycling within ecosystems.</p>

                <p class="title">What is a habitat?</p>
                <p class="body">A habitat is defined in general terms
                    as the specific place in an environment where an organism lives.
                    Terrestrial and marine environments each have distinct characteristics
                    that determine whether they can support specific organisms. A close
                    look at any area along the Florida coast reveals a number of different
                    habitats. In deep offshore waters, a unique <i>Oculina</i> reef
                    found nowhere else in the world runs from Ft. Pierce to Daytona.
                    Nearshore reefs composed of coquina rock and sabellarid wormrock
                    are quite common in some coastal areas. Along the barrier island
                    system in east central Florida, sand dunes along the shoreline abound,
                    which can be further subdivided into foredunes, dune crests, swales
                    and secondary dunes. Inland of the dune system lie the scrub zones
                    and maritime hammocks that have been built upon stable backdunes.
                    Beyond hammocks, the land begins to fall toward the Indian River
                    Lagoon where the mangrove fringe is located. Mangrove areas border
                    both the east and west margins of the lagoon along most of its length.
                    Within the lagoon itself are various submerged aquatic habitats
                    such as seagrass beds and oyster reefs, as well as the many spoil
                    islands which arose as the result of dredging in the lagoon. Past
                    the mangrove fringe are the fresh water swamps, hardwood hammocks
                    and upland forests that characterize interior Florida.</p>
                <p class="body">The Indian River Lagoon stretches approximately
                    156 miles along the east central Florida coast. Biodiversity in
                    the Indian River Lagoon is so vast due to both its diversity of
                    habitats and its unique geographical position. East central Florida
                    is fortuitously located in the transition area between the temperate
                    Carolinean Province to the north, and the subtropical Caribbean
                    Province to the south. Temperate species of plants and animals exist
                    in the Indian River Lagoon at the southernmost extent of their ranges,
                    while subtropical and tropical species exist at their northernmost
                    extents. Generally, the area around Cape Canaveral in northern Brevard
                    County is where vegetation patterns begin to shift from primarily
                    warm-temperate shrubs and trees, to more subtropical and tropical
                    varieties.</p>
                <p class="body footer_note">
                    Report by: K Hill, Smithsonian Marine Station at Fort Pierce<br>
                    Edited &amp; Updated by: LH Sweat, Smithsonian Marine Station at
                    Fort Pierce<br/>
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
