<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$mangrovePlantArr = $IRLManager->getChecklistTaxa(15);
$mangroveAlgaeArr = $IRLManager->getChecklistTaxa(16);
$mangroveAnimalArr = $IRLManager->getChecklistTaxa(17);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Mangrove Habitats</title>
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
    <h2>Mangrove Habitats</h2>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/R_mangle_Main.jpg" width="500" height="667"></td>
        </tr>
    </table>
    <br/>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/mangrove_page1.jpg" width="250" height="188"><img
                        src="../content/imglib/mangrove_page3.jpg" width="250" height="188"/></td>
        </tr>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td valign="top"><p class="title">Mangroves of Florida</p>
                <p class="body">
                    The term mangrove is loosely used to describe a wide variety of
                    often unrelated tropical and subtropical trees and shrubs which
                    share common characteristics. Globally, more than 50 species
                    in 16 different families are considered mangroves (Tomlinson 1986).
                    In Florida, the mangrove community consists of three main species
                    of true mangroves: the red mangrove, <i><a href="../taxa/index.php?taxon=Rhizophora mangle">Rhizophora
                            mangle</a></i>, the black mangrove, <i><a
                                href="../taxa/index.php?taxon=Avicennia germinans">Avicennia
                            germinans</a></i>, and the white mangrove, <i><a
                                href="../taxa/index.php?taxon=Laguncularia racemosa">Laguncularia racemosa</a></i>.
                <p class="body">The buttonwood, <i>Conocarpus erectus</i>, is often considered a
                    fourth mangrove species, however, it is classified as a mangrove
                    associate because it lacks any morphological specialization common
                    in true mangrove species, and because it generally inhabits the
                    upland fringe of many mangrove communities.</p>
                <p class="body">Red
                    mangroves dominante the shoreline from the upper subtidal to the
                    lower intertidal zones (Davis 1940, Odum and McIvor 1990), and are
                    distinguished from other mangroves by networks of prop roots that
                    originate in the trunk of the tree and grow downward towards the
                    substratum. Red mangroves may attain heights of 25 m, with
                    leaves a glossy, bright green at the upper surface, with somewhat
                    more pale undersides. Trees flower throughout the year, peaking
                    in spring and summer. Propagules of the red mangrove are pencil-shaped
                    and may reach 30 cm in length as they mature on the parent tree
                    (Savage 1972, Carlton 1975).</p>
                <p class="body">Black
                    mangroves typically are found growing immediately inland of red
                    mangroves and may reach 20 m high. They are characterized
                    by their conspicuous pneumatophores, vertical branches that may
                    extend upward in excess of 20 cm from cable roots lying below the
                    soil. Pneumatophores develop into extensive networks of fingerlike
                    projections that surround the bases of black mangroves to provide
                    them with proper aeration. The leaves of black mangroves tend
                    to be somewhat narrower than those of red mangroves and are often
                    found encrusted with salt. Black mangroves flower throughout
                    spring and early summer, producing bean-shaped propagules (Savage
                    1972, Carlton 1975, Odum and McIvor 1990).</p>
                <p class="body">White
                    mangroves are more prominent in high marsh areas, typically growing upland of
                    both red and black mangroves. White mangroves are significantly shorter than
                    red or black mangroves, generally reaching 15 m in height. Their leaves are
                    oval in shape, and somewhat flattened. Trees flower in spring and early summer,
                    and produce small propagules which measure only 1 cm.</p>
                <p class="body">Mangroves occur in dense, brackish
                    swamps along coastal and tidally influenced, low energy shorelines.
                    In Florida, mangrove forests extend from the Florida Keys to St.
                    Augustine on the Atlantic coast, and Cedar Key on the Gulf coast.
                    Factors such as climate, salt tolerance, water level fluctuation,
                    nutrient runoff, and wave energy influence the composition, distribution,
                    and extent of mangrove communities. Temperature also plays
                    a major role in mangrove distribution. Typically, mangroves
                    occur in areas where mean annual temperatures do not drop below
                    19&deg;C (66&deg;F)
                    (Waisel 1972). Mangroves are damaged under conditions where
                    temperatures fluctuate more than 10&deg;C
                    within short periods of time, or when they are subject to freezing
                    conditions for even a few hours. Further, Lugo and Patterson-Zucca
                    (1977) showed that stress induced by low temperatures leads to decreasing
                    structural complexity in black mangroves, with tree height, leaf
                    area, leaf size and tree density within a forest all negatively
                    impacted.</p>
                <p class="title">Mangrove Adaptations</p>
                <p class="body">In general, mangrove species share 4
                    important traits that allow them to live successfully under environmental
                    conditions that often exclude other species. Some of these adaptations
                    include: morphological specialization, i.e., aerial prop roots, cable roots,
                    vivipary, and other features that enable mangroves to adapt and thrive in their
                    environments; the ability to excrete or exclude salts; habitat specificity
                    within estuaries, with no extension into upland terrestrial communities; and
                    taxonomic isolation from other generically related species inhabiting upland
                    communities (Tomlinson 1986).</p>
                <p class="title"><em>Root Aeration</em></p>
                <p class="body">Another adaptation exhibited by mangroves is observed in root aeration.
                    Soils in mangrove areas tend to be fairly axoxic, preventing many
                    types of plants from taking root. Mangroves have adapted to
                    this condition by evolving shallow root systems rather than deep
                    taproots. Red mangroves aerate their roots by way of drop
                    roots and prop roots which develop from lower stems and branches,
                    and penetrate the soil only a few centimeters. Prop roots act to
                    both stabilize the tree, and provide critical aeration to the roots.
                    The above-ground areas of these roots are perforated by many small
                    pores called lenticels that allow oxygen to diffuse first into cortical
                    air spaces called aerenchyma, and then into underground roots (Scholander
                    et al 1955, Odum and McIvor 1990). Water is prevented from
                    entering the tree via lenticels due to their highly hydrophobic
                    nature which allows the red mangrove to exclude water from prop
                    roots and drop roots even during high tides (Waisel 1972).</p>
                <p class="body">Black
                    mangroves utilize a different strategy for aeration of root tissues. Black
                    mangroves have cable roots which lie only a few centimeters below the soil
                    surface, and raditate outward from the stem of the tree (Odum and McIvor 1990).
                    A network of erect aerial roots extends upward from the cable roots to penetrate
                    the soil surface. These erect roots, called pneumatophores, contain lenticels
                    and aerenchyma for gas exchange, and may form dense mats around the base of
                    black mangrove trees, with pneumatophores attaining as much as 20 cm or more in
                    height depending on the depth of flood tides (Odum and McIvor 1990).</p>

                <p class="title"><i>Salt Balance</i></p>
                <p class="body">Mangroves are facultative halophytes, meaning they have the ability
                    to grow in either fresh or salt water depending on which is available.
                    However, despite the fact that mangroves are able to grow in fresh
                    water, they are largely confined to estuaries and upland fringe
                    areas that are at least periodically flooded by brackish or salt
                    water (Gilmore and Snedaker 1993). Mangroves are rarely found
                    growing in upland communities. Simberloff (1983) and Tomlinsion
                    (1986) suggested that one reason mangroves do not develop in strictly
                    freshwater communities is due to space competition from freshwater
                    vascular plants. By growing in saline water, mangroves reduce
                    competitive threat, and thus are able to dominate the areas they
                    grow in.</p>
                <p class="body">As
                    facultative halophytes, mangroves not only tolerate, but thrive under saline
                    conditions. They accomplish this either by preventing salts from entering their
                    tissues, or by being able to excrete excess salts that are taken in. Red
                    mangroves (<i>Rhizophora mangle</i>), for example, exclude salts at their root
                    surfaces. This is accomplished nonmetabolically via a reverse osmosis process
                    driven by transpiration at leaf surfaces in which water loss from leaves
                    produces high negative pressure in xylem tissue. This, in turn, allows water to
                    freely diffuse into plant tissues. In addition to excluding salts, red
                    mangroves also have the ability to exclude sulfides from their tissues. This
                    sometimes results in elevated pore water concentrations of sulfides in
                    localities where poor flushing of the mangrove area is common (Carlson and
                    Yarbro 1987).</p>

                <p class="body">In
                    contrast to salt exclusion observed in red mangroves, other species
                    such as black mangroves, white mangroves and buttonwoods each utilize
                    salt excretion as a salt-balancing mechanism. Salt concentrations
                    in the sap of these species may be up to ten times higher than in
                    species that exclude salts (Odum and McIvor 1990). Salt-excreting
                    species are able to take in high salinity pore water, and then excrete
                    excess salts using specialized salt glands located in the leaves.
                    Atkinson <i>et al.</i> (1967) suggested this process involved active
                    transport, and thus required energy input from mangroves to drive
                    the process.</p>
                <p class="title">Reproduction &amp; Dispersal</p>
                <p class="body">Reproductive
                    adaptions in mangroves include vivipary and hydrochory (DEF=dispersal
                    of propagules via water). &nbsp;Red and black mangroves are considered
                    to be viviparous because once seeds are produced, they undergo continuous
                    development rather than entering a resting stage to await germination
                    in appropriate soil. White mangroves are not considered to
                    be viviparous; however, germination in this species often
                    occurs during the dispersal period (Feller 1996). Mangrove
                    reproductive structures, called propagules rather than seeds, germinate
                    and develop embryonic tissue while still attached to the parent.
                    Propagules eventually detach from the parent and float in water
                    for a certain period of time before completing embryonic development
                    (Rabinowitz 1978a, Odum and McIvor 1990) and taking root in new
                    areas. For germination to be completed, propagules must remain
                    in water for extended periods of time. The obligate
                    dispersal period in red mangroves is approximated to be 40 days;
                    in black mangroves, it is estimated at 14 days; and in white
                    mangroves it is estimated at 8 days (Rabinowitz 1978a). This combined
                    strategy of vivipary and long-lived, floating propagules allows
                    not only wide dispersal of mangroves, but also allows for seedlings
                    to establish themselves quickly once appropriate substrata are encountered
                    (Odum and McIvor 1990).</p>
                <p class="body">Productivity &amp; Nutrient FLux</p>
                <p class="body">
                    Mangrove forests are among the world's most highly productive ecosystems, with
                    gross primary production estimated at 3 - 24 g C/m<sup>-2</sup> day <sup> -1</sup>,
                    and net production estimated at 1 - 12 g
                    C/m<sup>-2</sup> day <sup>-1</sup> (Lugo and Snedaker 1974, Lugo <i>et al.</i> 1976). Red mangroves
                    have the
                    highest production rates, followed by black mangroves and white mangroves (Lugo <i>et al.</i> 1976).
                    Black mangroves have been shown to have higher respiration
                    rates, and thus lower primary production, &nbsp;in comparison to the red mangroves,
                    due perhaps, to the higher salinity stress red mangrove trees come under (Miller
                    1972, Lugo and Snedaker 1974).</p>
                <p class="body"> Mangrove communities, like many tidal wetlands, accumulate nutrients such as
                    nitrogen and phosphorus, as well as heavy metals and trace elements that are
                    deposited into estuarine waters from terrestrial sources, and thus act as
                    nutrient "sinks" for these materials. Mangrove roots, epiphytic algae, bacteria
                    and other microorganisms, as well a wide variety of invertebrates take up and
                    sequester nutrients in their tissues, often for long periods of time. Mangroves
                    also continually act as sources for carbon, nitrogen, and other elements as
                    living material dies and is decomposed into dissolved, particulate and gaseous
                    forms. Tidal flushing then assists in distributing this material to areas where
                    other organisms may utilize it.&nbsp;&nbsp;&nbsp;</p>
                <p class="body">Leaf
                    litter, including leaves, twigs, propagules, flowers, small braches and insect
                    refuse, is a major nutrient source to consumers in mangrove systems (Odum
                    1970). Generally, leaf litter is composed of approximately 68 - 86 % leaves, 3
                    - 15 % twigs, and 8 - 21 % miscellaneous material (Pool <i>et al.</i> 1975).
                    Leaf fall in Florida mangroves was estimated to be 2.4 dry g m<sup>-2</sup> day <sup>-1</sup> on
                    average, with significant variation depending on the site (Heald
                    1969, Odum 1970). Typically, black mangrove leaf fall rates are only those
                    of the red mangrove (Lugo <i>et al.</i> 1980).</p>
                <p class="body">Once
                    fallen, leaves and twigs decompose fairly rapidly, with black mangrove
                    leaves decomposing faster than red mangrove leaves (Heald <i>et
                        al.</i> 1979). Areas experiencing high tidal flushing rates,
                    or which are flooded frequently, have faster rates of decomposition
                    and export than other areas. Heald (1969) also showed that
                    decomposition of red mangrove litter proceeds faster under saline
                    conditions than under fresh water conditions, and also reported
                    that as the decay process proceeds, nitrogen, protein, and caloric
                    content within the leaf all increase.<br/>
                </p>
                <p class="title">Types of Mongrove Forests</p>
                <p class="body">Gilmore
                    and Snedaker (1993) described 5 distinct types of mangrove forests based on
                    water level, wave energy, and pore water salinity: 1) mangrove fringe forests,
                    2) overwash mangrove islands, 3) riverine mangrove forests, 4) basin mangrove
                    forests, and 5) dwarf mangrove forests.</p>
                <p class="title"><em>Mangrove Fringe</em></p>
                <p class="body">
                    Mangrove fringe forests occur along protected coastlines and the exposed open
                    waters of bays and lagoons. These forests typically have a vertical profile,
                    owing to full-sun exposure. Red mangroves dominate fringe forests, but when
                    local topology rises toward the uplands, other species may be included in zones
                    above the water line. Tides are the primary physical factor in fringing
                    forests, with daily cycles of tidal inundation and export transporting buoyant
                    materials such as leaves, twigs and propagules from mangrove areas to adjacent
                    shallow water areas. This export of organic material provides nutrition to a
                    wide variety of organisms and provides for continued growth of the fringing
                    forest.</p>
                <p class="title"><em>Overwash Islands</em></p>
                <p class="body">Like fringe forests, mangrove overwash islands are also subject to tidal
                    inundation, and are dominated by red mangroves. The major difference between
                    mangrove fringe forests and overwash islands is that, in the latter, the entire
                    island is typically inundated on each tidal cycle. Because overwash islands are
                    unsuitable for human habitation, and because the water surrounding them may act
                    as a barrier to predatory animals such as raccoons, rats, feral cats, etc.,
                    overwash islands are often the site of bird rookeries.</p>

                <p class="title"><em>Riverine Mangrove Forests</em></p>
                <p class="body">Riverine mangrove forests occur on seasonal floodplains in areas where natural
                    patterns of freshwater discharge remain intact. Salinity drops during the wet
                    season, when rains cause extensive freshwater runoff; &nbsp;however, during the dry
                    season, estuarine waters are able to intrude more deeply into river systems, and
                    salinity increases as a result. This high seasonal salinity may aid primary
                    production by excluding space competitors from mangrove areas. Further,
                    nutrient availability in these systems becomes highest during periods when
                    salinity is lowest, thus promoting optimal mangrove growth. This alternating
                    cycle of high runoff/low salinity followed by low runoff/high salinity led Pool <i>et al.</i> (1977)
                    to suggest that riverine mangrove forests are the most
                    highly productive of the mangrove communities.</p>
                <p class="title"><em>Basin Mangrove Forests</em></p>
                <p class="body">Basin mangrove forests are perhaps the most common community type, and thus are
                    the most commonly altered wetlands. Basin mangrove forests occur in inland
                    depressions which are irregularly flushed by tides. Because of irregular tidal
                    action in these forests, hypersaline conditions are likely to occur
                    periodically. Cintron <i>et al.</i> (1978) observed that the physiological
                    stress induced by extreme hypersalinity may severely limit growth, or induce
                    mortality in mangroves. Black mangroves tend to dominate in basin communities,
                    but certain exotic trees such as Brazilian pepper (<i>Schinus terebinthifolius</i>)
                    and Australian pine (<i>Casuarina</i> spp.) are also successful invaders. Basin
                    mangrove forests contribute large amounts of organic debris to adjacent waters,
                    with the majority being exported as whole leaves, particulates, or dissolved
                    organic substances typical of waters containing high tannin concentrations.</p>
                <p class="title"><em>Dwarf Mangrove Forests</em></p>
                <p class="body">Dwarf mangrove forests occur in areas where nutrients, freshwater,
                    and inundation by tides are all limited. Any mangrove species
                    can be dwarfed, with trees generally limited in height to approximately
                    1 meter or less. Dwarf forests are most commonly observed
                    in South Florida, around the vicinity of the Everglades, but occur
                    in all portions of the range where physical conditions are suboptimal,
                    especially in drier transitional areas. Despite their small
                    size and relatively low area to biomass ratios, dwarf mangroves
                    typically have higher leaf litter production rates; thus primary
                    production in dwarf forests is disproportionately high when compared
                    with normal mangrove forests.</p>

                <p class="title">Ecological Role of Mangroves</p>
                <p class="body">Mangroves perform a vital ecological role providing habitat for a wide variety
                    of species. Odum <i>et al.</i> (1982) reported 220 fish species, 24 reptile
                    species, 18 mammal species, and 181 bird species that all utilize mangroves as
                    habitat during some period of life. Additionally many species, though not
                    permanent mangrove inhabitants, make use of mangrove areas for foraging,
                    roosting, breeding, and other activities.&nbsp;<br/>
                    <br/>
                    Mangrove canopies and aerial roots
                    offer a wealth of habitat opportunities to many species of estuarine
                    invertebrates. Barnacles, sponges, mollusks, segmented worms, shrimp,
                    insects, crabs, and spiny lobsters all utilize mangrove prop roots as habitat
                    for at least part of their life cycles (Gillet1996 <i>In:</i> Feller 1996).
                    Additionally, mangrove roots are particularly suitable for juvenile fishes.
                    A study by Thayer et al. (1987) in the Florida Everglades showed that
                    comparitively more fishes were sampled from mangrove areas than from adjacent
                    seagrass beds. In this study, 75% of the number of fishes sampled were
                    taken from mangrove areas, while only 25% were sampled from nearby seagrass
                    beds. Further, when fish densities in each habitat were examined, fish
                    density in mangroves was 35 times higher than in adjacent seagrass beds.</p>

                <p class="body">In addition to providing vital
                    nursery and feeding habitat to fishes, mangroves also assist in shoreline
                    protection and stabilization. Prop roots of red mangroves trap sediments
                    in low-energy estuarine waters, and thus assist in preventing coastal erosion.
                    Mangroves also assist in buffering the coastal zone when tropical storms and
                    hurricanes strike. Because mangroves encounter damaging winds and waves
                    before inland areas do, the branches in their canopies, and their many prop
                    roots create friction that opposes and reduces the force of winds and waves.
                    Thus, coastlines are protected from severe wave damage, shoreline erosion and
                    high winds (Gillet1996 <i>In:</i> Feller 1996).</p>
                <p class="body">A
                    number of spatial guilds for mangrove-associated species were identified by
                    Gilmore and Snedaker (1993). The sublittoral/littoral guild utilizes the prop
                    root zone of red mangroves associated with fringe forests, riverine forests, and
                    overwash forests. The prop root zone provides sessile filter feeding organisms
                    such as bryozoans, tunicates, barnacles, and mussels with an ideal environment.
                    Mobile organisms such as crabs, shrimp, snails, boring crustaceans, polychaete
                    worms, many species of juvenile fishes, and other transient species also utilize
                    the prop root zone of mangroves as both a refuge and feeding area.</p>

                <p class="body">The
                    arboreal canopy guild consists of species able to migrate from the water's
                    surface to the mangrove canopy. Lagoonal snails such as the coffee bean snail (<i>Melamphus
                        coffeus</i>), angulate periwinkle (<i>Littorina anguilifera</i>), and ladderhorn
                    snail (<i>Cerithidea scalariformis</i>) are among the most common of the
                    invertebrate species in this guild. Also common are many species of crustaceans
                    such as the common mangrove crabs <i>Aratus pisoni</i>, <i>Goniopsis cruentata,
                        Pachygrapsus transverses, and Sesarma </i>spp., the isopod <i>Ligea exotica,</i> and many
                    species of insects. Birds also constitute a major component of this
                    spatial guild.</p>
                <p class="body">When
                    compared with species that inhabit adjacent seagrass areas, the benthic infaunal
                    guild is generally considered to exist under somewhat impoverished conditions,
                    primarily due to the reducing conditions which often exist in mangrove
                    sediments. Despite this, the benthic infaunal community in mangrove areas is
                    highly productive, especially when microbial activity is taken into
                    consideration.</p>
                <p class="body">The
                    upland arboreal guild includes those species associated with tropical hardwoods
                    such as mahogany (<i>Swietenia</i> spp.), cabbage palms (<i>Sabal palmetto</i>),
                    dogwoods (<i>Piscidia</i> spp.), oaks (<i>Quercus</i> spp.), red bay (<i>Persea</i> sp.), gumbo
                    limbo (<i>Bersera simaruba</i>), mastic (<i>Mastichodendron</i> sp.), figs (<i>Ficus</i> spp.) and
                    stoppers (<i>Eugenia</i> spp.). Also
                    included are the various species of bromeliads, orchids, ferns, and other
                    epiphytes that utilize upland trees for support and shelter. Animals of this
                    spatial guild, primarily birds and winged insects, often reside in the upland
                    community, but migrate to feeding areas located in mangroves. Common upland
                    arboreal animals include jays, wrens, woodpeckers, warblers, gnatcatchers,
                    skinks, anoles, snakes, and tree snails.</p>
                <p class="body">Finally,
                    the upland terrestrial community is associated with the understory
                    of tropical hardwood forests. The most common members of this
                    guild include various snakes, hispid cotton rats (<i>Sigmodon</i> sp.), raccoons (<i>Procyon
                        lotor</i>), white-tailed deer (<i>Odocoileusus
                        virginianus</i>), bobcats (<i>Felis rufus</i>), gray fox (<i>Urocyon
                        cinereoargenteus</i>), and many insect species. Many of the
                    animals in this spatial guild enter mangrove forests daily for feeding,
                    but return to the upland community at other times.></p>
                <p class="body">The following table is an abbreviated list of mangrove species.</p>
                <p class="title">Select
                    highlighted links below to learn more about individual species.</p>
                &nbsp;
            </td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="5"
           class="table-border no-border alternate">
        <tr>
            <th>Scientific Name</th>
            <th>Common Name</th>
        </tr>
        <?php
        if($mangrovePlantArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">Plants</p></td>
            </tr>
            <?php
            foreach($mangrovePlantArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '</tr>';
            }
        }
        if($mangroveAlgaeArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">Algae & Other Protists</p></td>
            </tr>
            <?php
            foreach($mangroveAlgaeArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '</tr>';
            }
        }
        if($mangroveAnimalArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">Animals</p></td>
            </tr>
            <?php
            foreach($mangroveAnimalArr as $id => $taxArr){
                echo '<tr>';
                echo '<td><span><i><a href="../taxa/index.php?taxon='.$id.'">'.$taxArr['sciname'].'</a></i></span></td>';
                if(array_key_exists($id,$vernacularArr)){
                    $vernacularStr = implode(', ', $vernacularArr[$id]);
                    echo '<td><span>'.wordwrap($vernacularStr,60,"<br />\n",true).'</span></td>'."\n";
                }
                else{
                    echo '<td><span></span></td>'."\n";
                }
                echo '</tr>';
            }
        }
        ?>
    </table>

    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="title">References &amp; Further Reading</p>
                <p class="body">Atkinson, MR, Findlay, GP, Hope, AB, Pitman, MG, Sadler, HDW &amp;
                    HR West. 1967. Salt regulation in the mangroves <em>Rhizophora mangle</em> Lam. and <em>Aerialitis
                        annulata</em> R. <em>Australian J. Biol.
                        Sci.</em> 20: 589-599.</p>
                <p class="body">Brockmeyer, RE, Rey, JR, Virnstein, RW, Gilmore,
                    Jr., RG &amp; L Earnest. 1997. Rehabilitation of impounded estuarine
                    wetlands by hydrologic reconnection to the Indian River Lagoon,
                    Florida. <em>J. Wetlands Ecol. Manag.</em> 4: 93-109. </p>
                <p class="body">Carlson, PR &amp; LA Yarbro. 1987. Physical
                    and biological control of mangrove pore water chemistry. <em>In</em>:
                    Hook, DD et al., eds. <em>The Ecology and Management of Wetlands.</em> 112-132. Croom Helm. London,
                    UK.</p>
                <p class="body">Carlton, JM. 1974. Land-building and stabilization
                    by mangroves. <em>Env. Conserv.</em> 1: 285-294.</p>
                <p class="body">Carlton, JM. 1975. <em>A guide to common salt
                        marsh and mangrove vegetation.</em> Florida Marine Resources Publications
                    6.</p>
                <p class="body">Carlton,JM. 1977. <em>A survey of selected
                        coastal vegetation communities of Florida.</em> Florida Marine Research
                    Publications 30. </p>
                <p class="body">Cintron, G, Lugo, AE, Pool, DJ, &amp; G Morris.
                    1978. Mangroves of arid environments in Puerto Rico and adjacent
                    islands. <em>Biotropica</em>. 10: 110-121.</p>
                <p class="body">Feller, IC, ed. 1996. <em>Mangrove Ecology
                        Workshop Manual. A Field Manual for the Mangrove Education and Training
                        Programme for Belize.</em> Marine Research Center, University College
                    of Belize. Calabash Cay, Turneffe Islands. Smithsonian Institution,
                    Washington DC. </p>
                <p class="body">Gilmore, Jr., RG, Cooke, DW &amp; CJ Donahue.
                    1982. A comparison of the fish populations and habitat in open and
                    closed salt marsh impoundments in east central Florida. <em>NE Gulf
                        Sci.</em> 5: 25-37. </p>
                <p class="body">Gilmore, Jr., RG &amp; SC Snedaker. 1993.
                    Chapter 5: Mangrove Forests. <em>In</em>: Martin, WH, Boyce, SG
                    &amp; AC Echternacht, eds. <em>Biodiversity of the Southeastern
                        United States: Lowland Terrestrial Communities.</em> John Wiley
                    &amp; Sons, Inc. Publishers. New York, NY. 502 pp. </p>
                <p class="body">Harrington, RW &amp; ES Harrington. 1961.
                    Food selection among fishes invading a high subtropical salt marsh;
                    from onset of flooding through the progress of a mosquito brood. <em>Ecology.</em> 42: 646-666.</p>
                <p class="body">Heald, EJ. 1969.<em> The production of organic
                        detritus in a south Florida estuary.</em> Ph.D. Thesis, University
                    of Miami. Coral Gables, FL.</p>
                <p class="body">Heald, EJ &amp; WE Odum. 1970. The contribution
                    of mangrove swamps to Florida fisheries. <em>Proc. Gulf Caribbean
                        Fish. Inst.</em> 22: 130-135.</p>
                <p class="body">Heald, EJ, Roessler, MA &amp; GL Beardsley.
                    1979. Litter production in a southwest Florida black mangrove community. <em>Proc. FL Anti-Mosquito
                        Assoc. 50th Meeting.</em> 24-33. </p>
                <p class="body">Hull, JB &amp; WE Dove. 1939. Experimental
                    diking for control of sand fly and mosquito breeding in Florida
                    saltwater marshes. <em>J. Econ. Entomology.</em> 32: 309-312. </p>
                <p class="body">Lahmann, E. 1988. <em>Effects of different
                        hydrologic regimes on the productivity of </em>Rhizophora mangle<em> L. A case study of mosquito
                        control impoundments in Hutchinson Island,
                        St. Lucie County, Florida.</em> Ph.D. dissertation, University of
                    Miami. Coral Gables, FL.</p>
                <p class="body">Lewis, III, RR, Gilmore, Jr., RG, Crewz, DW
                    &amp; WE Odum. 1985. Mangrove habitat and fishery resources of Florida. <em>In</em>: Seaman, Jr., W,
                    ed. <em>Florida Aquatic Habitat and
                        Fishery Resources</em>. American Fisheries Society, Florida Chapter.
                    Kissimmee, FL. </p>
                <p class="body">Lugo, AE. 1980. Mangrove ecosystems: successional
                    or steady state? <em>Biotropica.</em> 12:65-73. </p>
                <p class="body">Lugo, AE &amp; SC Snedaker. 1974. The ecology
                    of mangroves. <em>Ann. Rev. Ecol. Syst.</em> 5: 39-64.</p>
                <p class="body">Lugo, AE, Sell, M &amp; SC Snedaker. 1976.
                    Mangrove ecosystem analysis. <em>In</em>: Patten, BC, ed. <em>Systems
                        Analysis and Simulation in Ecology.</em> 113-145. Academic Press.
                    New York, NY. USA</p>
                <p class="body">Lugo, AE &amp; Patterson-Zucca, C. 1977. The
                    impact of low temperature stress on mangrove structure and growth. <em> Trop. Ecol.</em> 18:
                    149-161.</p>
                <p class="body">Miller, PC. 1972. Bioclimate, leaf temperature,
                    and primary production in red mangrove canopies in South Florida. <em>Ecology.</em> 53: 22-45. </p>
                <p class="body">Odum, WE. 1970. <em>Pathways of energy flow
                        in a south Florida estuary.</em> Ph.D. Thesis, University of Miami.
                    Coral Gables, FL.</p>
                <p class="body">Odum, WE &amp; CC McIvor. 1990. Mangroves. <em>In</em>: Myers, RL &amp; JJ Ewel, eds.
                    <em>Ecosystems of Florida</em>.
                    517 - 548. University of Central Florida Press. Orlando, FL. </p>
                <p class="body">Odum, WE, McIvor, CC &amp; TJ Smith III. 1982. <em>The ecology of the mangroves of south
                        Florida: a community profile.</em> U.S. Fish and Wildlife Service, Office of Biological
                    Services. FWS/OBS-81-24.</p>
                <p class="body">Odum, WE &amp; EJ Heald. 1972. Trophic analyses
                    of an estuarine mangrove community. <em>Bull. Mar. Sci.</em> 22:
                    671-738.</p>
                <p class="body">Onuf, CP, Teal, JM &amp; I Valiela. 1977.
                    Interactions of nutrients, plant growth and herbivory in a mangrove
                    ecosystem. <em>Ecology.</em> 58: 514-526. </p>
                <p class="body">Platts, NG, Shields, SE &amp; JB Hull. 1943.
                    Diking and pumping for control of sand flies and mosquitoes in Florida
                    salt marshes. <em>J. Econ. Entomology. </em>36: 409-412.</p>
                <p class="body">Pool, DJ, Lugo, AE &amp; SC Snedaker.1975.
                    Litter production in mangrove forests of southern Florida and Puerto
                    Rico. <em>Proc. Int. Symp. Biol. Manag. Mangroves.</em> 213-237.
                    University of Florida Press, Gainesville, FL.</p>
                <p class="body">Pool, DJ, Snedaker, SC &amp; AE Lugo. 1977.
                    Structure of mangrove forests in Florida, Puerto Rico, Mexico, and
                    Central America. <em>Biotropica</em>. 9: 195-212.</p>
                <p class="body">Provost, MW. 1976. Tidal datum planes circumscribing
                    salt marshes. <em>Bull. Mar. Sci</em>. 26: 558-563.</p>
                <p class="body">Rabinowitz, D. 1978a. Dispersal properties
                    of mangrove propagules. <em>Biotropica.</em> 10: 47-57. </p>
                <p class="body">Rabinowitz, D. 1978b. Early growth of mangrove
                    seedlings in Panama, and a hypothesis concerning the relationship
                    of dispersal and zonation. <em>J. Biogeography.</em> 5: 113-133. </p>
                <p class="body">Rey, JR &amp; T Kain. 1990. <em>Guide to the
                        salt marsh impoundments of Florida.</em> Florida Medical Entomology
                    Laboratory Publications. Vero Beach, FL. </p>
                <p class="body">Rey, JR, Schaffer, J, Tremain, D, Crossman,
                    RA &amp; T Kain. 1990. Effects of reestablishing tidal connections
                    in two impounded tropical marshes on fishes and physical conditions. <em>Wetlands.</em> 10: 27-47.
                </p>
                <p class="body">Rey, JR, Peterson, MS, Kain, T, Vose, FE &amp;
                    RA Crossman. 1990. Fish populations and physical conditions in ditched
                    and impounded marshes in east-central Florida. <em>N.E. Gulf Science.</em> 11: 163-170. </p>
                <p class="body">Rey, JR, Crossman, RA, Peterson, M, Shaffer,
                    J &amp; F Vose. 1991. Zooplankton of impounded marshes and shallow
                    areas of a subtropical lagoon. <em>FL Sci.</em> 54: 191-203.</p>
                <p class="body">Rey, JR, Crossman, RA, Kain, T &amp; J Schaffer.
                    1991. Surface water chemistry of wetlands and the Indian River Lagoon,
                    Florida, USA. <em>J. FL Mosquito Con. Assoc.</em> 62: 25-36.</p>
                <p class="body">Rey, JR, Kain, T &amp; R Stahl. 1991. Wetland
                    impoundments of east-central Florida. <em>FL Sci.</em> 54: 33-40.</p>
                <p class="body">Rey, JR &amp; CR Rutledge. 2001. <em>Mosquito
                        Control Impoundments.</em> Document # ENY-648, Entomology and Nematology
                    Department, Florida Cooperative Extension Service, Institute of
                    Food and Agricultural Sciences, University of Florida. Available
                    online at: http://edis.ifas.ufl.edu. </p>
                <p class="body">Savage, T. 1972. <em>Florida mangroves as
                        shoreline stabilizers.</em> Florida Department of Natural Resources
                    Professional Papers 19. </p>
                <p class="body">Scholander, PF, van Dam, L &amp; SI Scholander.
                    1955. Gas exchange in the roots of mangroves. <em>Amer. J. Botany.</em> 42: 92-98. </p>
                <p class="body">Simberloff, DS. 1983. Mangroves. <em>In</em>:
                    Janzen, DH., ed. <em>Costa Rican Natural History.</em> 273-276.
                    University of Chicago Press. Chicago, IL. </p>
                <p class="body">Snedaker, SC. 1989. Overview of mangroves
                    and information needs for Florida Bay. <em>Bull. Mar. Sci.</em> 44: 341-347. </p>
                <p class="body">Snedaker, S C &amp; AE Lugo. 1973. <em>The
                        role of mangrove ecosystems in the maintenance of environmental
                        quality and a high productivity of desirable fisheries.</em> Final
                    report to the Bureau of Sport Fisheries and Wildlife in fulfillment
                    of Contract no. 14-16-008-606. Center for Aquatic Sciences.<br/>
                    Gainesville, FL.</p>
                <p class="body">Snelson, FF. 1976. <em>A study of a diverse
                        coastal ecosystem on the Atlantic coast of Florida. Vol. 1: Ichthyological
                        Studies.</em> NGR-10-019-004 NASA. Kennedy Space Center, Florida.
                    USA.</p>
                <p class="body">Thayer, GW, Colby, DR &amp; WF Hettler Jr.
                    1987. Utilization of the red mangrove prop roots habitat by fishes
                    in South Florida. <em>Mar. Ecol. Prog. Ser.</em> 35: 25-38.</p>
                <p class="body">Tomlinson, PB. 1986. <em>The botany of mangroves</em>.
                    Cambridge University Press. London. </p>
                <p class="body">Waisel, Y. 1972. <em>The biology of halophytes</em>.
                    Academic Press. New York, NY.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="footer_note">Report by: K Hill,
                    Smithsonian Marine Station at Fort Pierce<br/>
                    Updates &amp; Photos by: LH Sweat, Smithsonian Marine Station at
                    Fort Pierce<br/>
                    Submit additional information, photos or comments
                    to:<br/>
                    <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu</a></p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
