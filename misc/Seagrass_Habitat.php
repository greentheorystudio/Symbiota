<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);

$IRLManager = new IRLManager();

$seagrassArr = $IRLManager->getChecklistTaxa(30);
$associatedInvertsArr = $IRLManager->getChecklistTaxa(31);
$associatedVertsArr = $IRLManager->getChecklistTaxa(32);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Seagrass Habitats</title>
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
    <h2>Seagrass Habitats</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/SeaGrass1.jpg" alt="" width="417" height="230"/></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">What Is Seagrass?</p>
                <p class="body">Seagrasses are a type of submerged aquatic vegetation (SAV) have
                    evolved from terrestrial plants and have become specialized to live in
                    the marine environment. Like terrestrial plants, seagrasses have leaves,
                    roots, conducting tissues, flowers and seeds, and manufacture their own
                    food via photosynthesis. Unlike terrestrial plants, however, seagrasses
                    do not possess the strong, supportive stems and trunks required to
                    overcome the force of gravity on land. Rather, seagrass blades are
                    supported by the natural buoyancy of water, remaining flexible when
                    exposed to waves and currents.</p>

                <p class="body">Due to their morphology and growth habit, seagrasses are also
                    sometimes confused with marine macroalgae; however <a href="ComparAlgae_Seagr.php"> closer
                        examination</a> reveals significant differences. Structurally, seagrasses are
                    more closely related to terrestrial plants and, like terrestrial plants,
                    possess specialized tissues that perform specific tasks within each
                    plant. Conversely, algae are relatively simple and unspecialized in
                    structure. While algae possess only a tough holdfast that assists in
                    anchoring the plant to a hard substratum, seagrasses possess true roots
                    that not only hold plants in place, but also are specialized for
                    extracting minerals and other nutrients from the sediment. All algal
                    cells possess photosynthetic structures capable of utilizing sunlight to
                    produce chemical energy. <br/>
                    In seagrasses, however, chloroplasts occur only
                    in leaves, thus confining photosynthesis to leaves. Further, algae are
                    able to take up minerals and other nutrients directly from the water
                    column via diffusion. Seagrasses however, transport minerals and
                    nutrients in xylem and phloem. Finally, while most algae lack specialized
                    reproductive structures, most seagrasses have separate sexes and produce
                    flowers and seeds, with embryos developing inside ovaries.</p>

                <p class="title">The Value of Seagrasses</p>
                <p class="body"> Within seagrass communities, a single acre of seagrass can produce
                    over 10 tons of leaves per year. This vast biomass provides food,
                    habitat, and nursery areas for a myriad of adult and juvenile
                    vertebrates and invertebrates. Further, a single acre of seagrass may
                    support as many as 40,00 fish, and 50 million small invertebrates.
                    Because seagrasses support such high biodiversity, and because of their
                    sensitivity to changes in water quality, they have become recognized as
                    important indicator species that reflect the overall health of coastal
                    ecosystems.</p>
                <p class="body">Seagrasses perform a variety of functions within ecosystems, and have
                    both economic and ecological value. The high level of productivity,
                    structural complexity, and biodiversity in seagrass beds has led some
                    researchers to describe seagrass communities as the marine equivalent of
                    tropical rainforests. While nutrient cycling and primary production in
                    seagrasses tends to be seasonal, annual production in seagrass
                    communities rivals or exceeds that of terrestrially cultivated areas. In
                    Florida, <i>Halodule wrightii</i>, has an estimated annual production
                    (as measured in grams of carbon per square meter) of 182 - 730 g/C/m<sup>-2</sup>; <i>Syringodium
                        filiforme</i> has an estimated annual production of 292 -
                    1095 g/C/m<sup>-2</sup>; and <i>Thalassia testudinum</i> has an
                    estimated annual production 329 - 5840 g/C/m<sup>-2</sup>. Blade
                    elongation in seagrasses averages 2-5&nbsp;mm per day in <i>Thalassia
                        testudinum</i>, 8.5 mm in <i>Syringodium filiforme</i>, and as much as
                    3.1&nbsp;mm in <i>Halodule wrightii</i>. In the Indian River Lagoon, <i> Halodule wrightii</i> has
                    been shown to produce one new leaf every 9 days during
                    spring - the season of highest productivity (Virnstein 1982).</p>
                <p class="body">As habitat, seagrasses offer food, shelter, and essential nursery
                    areas to commercial and recreational fishery species, and to the
                    countless invertebrates that are produced within, or migrate to
                    seagrasses. The complexity of seagrass habitat is increased when several
                    species of seagrasses grow together, their leaves concealing juvenile
                    fish, smaller finfish, and benthic invertebrates such as crustaceans,
                    bivalves, echinoderms, and other groups. Juvenile stages of many fish
                    species spend their early days in the relative safety and protection of
                    seagrasses. Additionally, seagrasses provide both habitat and protection
                    to the infaunal organisms living within the substratum as seagrass
                    rhizomes intermingle to form dense networks of underground runners that
                    deter predators from digging infaunal prey from the substratum.</p>

                <p class="body">Seagrass meadows also help dampen the effects of strong currents, providing
                    protection to fish and invertebrates, while also preventing the scouring
                    of bottom areas. Finally, seagrasses provide attachment sites to small
                    macroalgae and epiphytic organisms such as sponges, bryozoans, forams,
                    and other taxa that use seagrasses as habitat. A number of studies have
                    found epiphytes to be highly productive components of seagrass habitats
                    (Penhale 1977, Heijs 1984, Tomasko &amp; Lapointe 1991), with epiphytes
                    in some systems accounting for up to 30% of ecosystem productivity, and
                    more than 30% of the total above ground biomass (Penhale 1977, Morgan
                    &amp; Kitting 1984, Heijs 1984). Seagrass epiphytes also contribute to
                    food webs, either directly via organisms grazing on seagrasses, or
                    indirectly following the deaths of epiphytes, which then enter the food
                    web as a detrital carbon source (Fry &amp; Parker 1979, Kitting et al.
                    1984).</p>
                <p class="body">Economically, Florida's 2.7 million acres of seagrass supports both
                    commercial and recreational fisheries that provide a wealth of benefits
                    to the state's economy. Florida's Department of Environmental
                    Protection (FDEP) reported that in 2000, Florida's seagrass
                    communities supported commercial harvests of fish and shellfish valued
                    at over 124 billion dollars. Adding the economic value of the nutrient
                    cycling function of seagrasses, and the value of recreational fisheries
                    to this number, FDEP has estimated that each acre of seagrass in Florida
                    has an economic value of approximately $20,500 per year, which
                    translates into a statewide economic benefit of 55.4 billion dollars
                    annually. In Fort Pierce, Florida alone, the 40 acres of seagrass in the
                    vicinity of Fort Pierce Inlet are valued at over $800,000 annually. When
                    projected across St. Lucie County's estimated 80,000 acres of
                    seagrass, this figure increases to 1.6 billion dollars per year.</p>

                <p class="title">Threats to Seagrass Communities</p>
                <p class="body">Seagrasses are subject to a number of biotic and abiotic stresses
                    such as storms, excessive grazing by herbivores, disease, and anthropogenic threats due to point and
                    non-point sources of pollution,
                    decreasing water clarity, excessive nutrients in runoff, sedimentation
                    and prop scarring. What effect these stresses have on seagrasses is
                    dependent on both the nature and severity of the particular
                    environmental challenge. Generally, if only leaves and above-ground
                    vegetation are impacted, seagrasses are generally able to recover from
                    damage within a few weeks; however, when damage is done to roots and
                    rhizomes, the ability of the plant to produce new growth is severely
                    impacted, and plants may never be able to recover (Zieman et al. 1984,
                    Fonseca et al. 1988). Some of the major environmental challenges to
                    seagrass health are discussed below.</p>

                <p class="title">Anthropogenic Threats</p>
                <p class="body">[A more detailed look at some emerging human-induced threats facing the seagrasses of
                    the IRL is <a href="Seagrass_Emerging_Issues.php">available here</a>.]</p>
                <p class="body">The health of seagrass communities obviously relies heavily upon the
                    amount of sunlight that penetrates the water column to reach submerged
                    blades. Water clarity, heavily affected by the amount and composition of
                    stormwater runoff and other non-point sources of pollution, is the
                    primary influence that determines how much light ultimately reaches
                    seagrass blades. Stormwater runoff drains both urban and agricultural
                    areas, and carries with it household chemicals, oils, automotive
                    chemicals, pesticides, animal wastes, and other debris. Under normal
                    conditions, seagrasses maintain water clarity by trapping silt, dirt,
                    and other sediments suspended in the water column.<br/>
                    These materials are
                    then incorporated into the benthic substratum, where they are stabilized
                    by seagrass roots. However, when sediment loading becomes excessive,
                    turbidity in the water column increases and the penetration of sunlight
                    is inhibited. In extreme cases, excessive sediment loading can actually
                    smother seagrasses.</p>
                <p class="body">When heavy volumes of stormwater runoff carrying excessive amounts of
                    nitrogen and phosphorous from fertilizers and animal wastes drains into
                    canals, and eventually empties into estuaries, it accelerates the growth
                    rate of phytoplankton. Under normal nutrient conditions, microalgae grow
                    at manageable levels, and are an important food source for many filter feeding and suspension
                    feeding organisms. However, excess nutrient loading in
                    water bodies causes massive blooms of algae that reduce water clarity by
                    blocking the amount of sunlight available. Reduction in light levels, as
                    well as depletion of the nutrient supply, leads to the death and
                    decomposition of these microalgal blooms. The process of decomposition
                    further degrades water quality by depleting much of the dissolved oxygen
                    available in the water column, sometimes leading to hypoxic conditions
                    and fish kills.</p>
                <p class="body">A number of other anthropogenic factors often affect the health of
                    seagrass meadows. Dredging churns up seagrass beds, increasing turbidity
                    and suspended sediments in the water column. This period of poor water
                    quality may be temporary, and have few long-term impacts on seagrasses.
                    However, if dredging affects hydrodynamic properties of the area, such
                    as the depth profile, current direction, or current velocity, seagrasses
                    may be severely threatened. Prop scarring is another factor that
                    threatens seagrasses. Accidental or intentional groundings of boats in
                    shallow areas may lead to significant, localized impacts on seagrasses.
                    Scarring occurs in water that is shallower than the draft of the boat.
                    Boaters entering these shallows often dig up the seagrass beds as they
                    motor, cutting not only the blades, but more catastrophically, slashing
                    underground rhizomes and roots as well. Prop scarring often results in a
                    continuous line of seagrass damage, which acts to fragment the habitat,
                    especially in areas where seagrass coverage is sparse. Seagrasses that
                    remain in fragmented areas are then susceptible to erosion effects and
                    are vulnerable to increased damage as boaters continue to scar the
                    meadow.</p>

                <p class="title">Natural Threats</p>
                <p class="body">Threats to seagrasses are not limited to anthropogenic factors.
                    There are also a number of natural factors that damage or threaten
                    seagrasses. A wasting disease, thought to be caused by a marine slime
                    mold, caused extensive damage to eelgrass beds (<i>Zostera</i> spp.) in
                    temperate coastal areas during the 1930s, diminishing seagrass coverage
                    by over 90%. Storms can also cause widespread damage to established
                    seagrass meadows, sometimes on a regular basis. Wind-driven waves may
                    break or uproot seagrasses, having minimal effects when leaves and
                    vegetative structures are damaged; and more lasting effects when
                    rhizomes and roots are damaged. In addition, a number of small and large
                    marine animals disturb seagrasses while foraging, including sea urchins
                    and the endangered West Indian Manatee (<i>Trichechus manatus</i>).
                    Other species, such as crabs, fishes, skates, and rays disturb rhizomes
                    and roots, and can tear apart seagrass leaves as they forage for
                    concealed or buried prey.</p>
                <p class="title">Management of Seagrasses</p>
                <p class="body">The Indian River Lagoon has approximately 80,000 acres of seagrass
                    coverage at the present time, a decline of approximately 18% overall
                    from seagrass coverage estimated from aerial photos taken during the
                    1950s. Some areas of the lagoon have experienced alarming declines in
                    seagrass coverage. For example, in the 50 mile stretch of the IRL
                    between the NASA Causeway and Grant, Florida, seagrass coverage has
                    decreased by over 70% in the last 50 years. However, in other areas,
                    seagrasses have maintained their historic coverage levels, or have
                    actually increased. In the area encompassing the protected zones of
                    NASA, Merritt Island Wildlife Refuge, and Canaveral National Seashore,
                    seagrass coverage has remained unchanged over the last 50 years.<br/>
                    In the central Indian River Lagoon, near Sebastian Inlet, seagrass coverage has
                    increased markedly from historic levels, though much of this increase is
                    due to the opening of the inlet at its present location. As a general
                    rule, seagrass coverage has been observed to remain steady or increase
                    in areas retaining relatively pristine environmental conditions, and has
                    declined in areas heavily impacted by overdevelopment of shoreline areas
                    and wetlands.</p>
                <p class="body">St. Johns River Water Management District (SJRWMD) and South Florida
                    Water Management District (SFWMD) are 2 of the organizations charged
                    with managing water quality within the Indian River Lagoon. These
                    organizations have actively pursued the goal of managing the lagoon in
                    order to preserve and restore seagrass coverage to historic levels. Two
                    main focus areas for improving water quality in the lagoon have been
                    addressed: 1) to assist local governments in controlling and managing
                    stormwater runoff; and 2) to purchase, and to the extent possible,
                    restore, fringing wetland areas.</p>
                <p class="body">Managing water quality for seagrass
                    health has improved overall water quality within the lagoon; has
                    increased habitat quality and quantity; and over the long-term, is
                    expected to increase biodiversity within seagrass meadows. Enriching
                    biodiversity within the Indian River Lagoon will make large
                    contributions to the economy of the area by enhancing commercial and
                    recreational fisheries stocks, increasing tourism and recreational
                    opportunities, increasing property values, and potentially creating
                    additional jobs. Outreach and education efforts undertaken by SJRWMD and
                    SFWMD have improved public awareness and support of seagrass restoration
                    as an effective management strategy.</p>
                <p class="body">Click a highlighted link to read more about individual species:</p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="table-border no-border alternate">
        <tr>
            <th>Species Name</th>
            <th>Common name</th>
            <th>Comments</th>
        </tr>
        <?php
        if($seagrassArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">IRL Seagrasses</p></td>
            </tr>
            <?php
            foreach($seagrassArr as $id => $taxArr){
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
        if($associatedInvertsArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Associated Invertebrates</p></td>
            </tr>
            <?php
            foreach($associatedInvertsArr as $id => $taxArr){
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
        if($associatedVertsArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Associated Vertebrates</p></td>
            </tr>
            <?php
            foreach($associatedVertsArr as $id => $taxArr){
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
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">FURTHER READING</p>
                <p class="body">Almasi, M. N., C. M. Hoskin, J. K. Reed and J. Milo. 1987. Effects of
                    natural and artificial<br>
                    <i>Thalassia</i> on rates of sedimentation. J.
                    Sedimentary Petrology 57 (5): 901-906.</p>
                <p class="body">Applied Biology, Inc. and Ray L. Lyerly &amp; Associates. 1980.
                    Biological and environmental<br>
                    studies at the Florida Power &amp; Light
                    Company Cape Canaveral Plant and the Orlando<br>
                    Utilities Commission Indian
                    River Plant, Volume II, Part I: Biological studies. Applied<br>
                    Biology,
                    Inc., Atlanta, GA and Ray L. Lyerly &amp; Assoc., Dunedin, FL. 272
                    pp.</p>
                <p class="body">Aspden, William Clarkson. 1980. Aspects of photosynthetic carbon
                    metabolism in<br>
                    seagrasses. Master's Thesis, Fla. Inst. of Tech.,
                    Melbourne, FL. 75 pp.</p>
                <p class="body">Barile, Diane D. 1986. The Indian River Lagoon - seventy years of
                    cumulative impacts. In:<br>
                    Proceedings Of The Conference: Managing
                    Cumulative Effects In Florida Wetlands,<br>
                    Oct 17-19, 1985, New College of
                    Univ. S. Fla., Sarasota, FL, E.D. Esteves, J. Miller,<br>
                    J. Morris and R.
                    Hamman, eds., E.S.P. Publ. #38, Omnipress, Madison, WI, pp.<br>
                    193-218.</p>
                <p class="body">Barile, Diane D., Christine A. Panico, Mary Beth Corrigan and Michael
                    Dombrowski.<br>
                    1987. Estuarine management - the Indian River Lagoon. In:
                    Coastal Zone '87:<br>
                    Proceedings Of The Fifth Symposium On Coastal And
                    Ocean Management, Volume 3,<br>
                    Orville T. Magoon, et al, eds., WW Div/ASCE,
                    Seattle, WA/ May 26-29, 1987,<br>
                    Amer. Soc. of Civil Engineers, New York,
                    NY. Pp. 237-250.</p>
                <p class="body">Brevard County, Florida. Office of Natural Resources Management.
                    1986. Seagrass maps<br>
                    of the Indian and Banana Rivers. Brevard County
                    Office Natural Resources<br>
                    Management, Merritt Island, FL. 20
                    pp., maps, charts.</p>
                <p class="body">Brevard County, Florida. Water Resources Department. 1981. Review and
                    update of the<br>
                    wasteload allocations for the Indian and Banana Rivers in
                    Brevard County, Florida.<br>
                    Unpubl. Rep., Brevard County Water Resources
                    Dep., Merritt Island, FL.</p>
                <p class="body">Carroll, Joseph D., Jr. 1983. Letter to District Engineer, U. S. Army
                    Corps of Engineers,<br>
                    Jacksonville, Florida. Re: Seagrass mapping of
                    central Indian River Lagoon region,<br>
                    Sebastian area. Letter
                    Correspondence, U.S. Fish &amp; Wildlife Serv., Vero Beach, FL.<br>
                    7 pp., maps.</p>
                <p class="body">Clark, K. B. 1975. Benthic community structure and function. In: an
                    ecological study of the<br>
                    lagoons surrounding the John F. Kennedy Space
                    Center, Brevard County Florida, April<br>
                    1972 to September 1975. Volume 1,
                    experimental results and conclusions, NGR<br>
                    10-015-008, Fla. Inst. of
                    Tech., Melbourne, FL.</p>
                <p class="body">Darovec, J. E. Jr., J. M. Carlton, T. R. Pulver, M. D. Moffler, G. B.
                    Smith, W. K.<br>
                    Whitfield, Jr., C. A. Willis, K. A. Steidinger and E. A.
                    Joyce, Jr. 1975. Techniques for<br>
                    coastal restoration and fishery
                    enhancement in Florida. Fla. Marine Res. Publ. No. 15,<br>
                    Fla. Dep. of
                    Natural Resources, Marine Res. Laboratory, St. Petersburg, FL. 27 pp.</p>
                <p class="body">Dawes, Clinton J. 1987. The dynamic seagrasses of the Gulf of Mexico
                    and Florida coasts.<br>
                    Fla. Marine Research Publ. No. 42, Proc. of Symp. on
                    Subtropical Seagrasses of the<br>
                    S.E. U. S., Aug 12 1985, Michael J. Durako,
                    Ronald C. Phillips &amp; Roy R. Lewis, III,<br>
                    eds., Fla. Dep. Natural
                    Resources, Bur. Marine Research, St. Petersburg, FL.</p>
                <p class="body">Down, C. and R. Withrow. 1978. Vegetation and other parameters in the
                    Brevard County<br>
                    bar-built estuaries. NASA-CR-158242, REPT-06-73, Brevard
                    County Health Dep.,<br>
                    Titusville, FL. 90 pp.</p>
                <p class="body">Down, Cherie. 1978. Vegetation and other parameters in the Brevard
                    County bar-built<br>
                    estuaries. Rep. No. 06-73, Brevard County Health Dep.,
                    Environ. Eng. Dep., Brevard<br>
                    County, FL. 85 pp.</p>
                <p class="body">Down, C. 1983. Use of Aerial Imagery in Determining Submerged
                    Features in Three<br>
                    East-Coast Florida Lagoons. Florida Sci. 46(3/4), 355-362.</p>
                <p class="body">Durako, Michael J. 1988. The seagrass bed a community under assault.
                    Fla. Naturalist, Fall<br>
                    1988, pp. 6-8.</p>
                <p class="body">Durako, Michael J., Ronald C. Phillips and Roy R. Lewis III, eds.
                    1987. Proceedings of<br>
                    the symposium on subtropical-tropical seagrasses of
                    the southeastern United States. Fla.<br>
                    Marine Res. Publ. No. 42, Fla. Dep.
                    of Natural Resources, Bur. Marine Res., St.<br>
                    Petersburg, FL. 209 pp.</p>
                <p class="body">Eiseman, N. J. 1980. An illustrated guide to the seagrasses of the
                    Indian River region of<br>
                    Florida. Tech. Rep. No. 31, Harbor Branch Found.,
                    Inc., Fort Pierce, FL.</p>
                <p class="body">Eiseman, N. J. and Calvin McMillan. 1980. A new species of seagrass, <i>Halophila<br>
                        johnsonii</i>, from the Atlantic coast of Florida. Aquatic Botany 9:
                    15-19.</p>
                <p class="body">Eiseman, N. J. and M. C. Benz. 1975. Studies of the benthic plants of
                    the Indian River<br>
                    region. In: Indian River Coastal Zone Study, Second
                    Annual Report, 1974-1975,<br>
                    Volume I, David K. Young, ed., Harbor Branch
                    Consortium, Fort Pierce, FL, pp.<br>
                    89-103.</p>
                <p class="body">Eiseman, N. J., M. C. Benz, and D. E. Serbousek. 1976. Studies of the
                    benthic plants of<br>
                    the Indian River region. In: Indian River Coastal Zone
                    Study, Third Annual Report,<br>
                    1975-1976, Volume I, David K. Young, ed.,
                    Harbor Branch Consortium, Fort Pierce,<br>
                    FL. Pp. 72-86.</p>
                <p class="body">Eiseman, N. J., M. C. Benz, and D. E. Serbousek. 1976. Studies on the
                    benthic plants of<br>
                    the Indian River region. In: Indian River Coastal Zone
                    Study, Third Annual Report,<br>
                    1975-1976, Volume 1, David K. Young, ed.,
                    Harbor Branch Consortium, Ft. Pierce,<br>
                    FL. Pp. 71-86.</p>
                <p class="body">Eiseman, N. J., Martha Meagher, Reubin Richards and Gregg Stanton.
                    1974. Chapter 8.<br>
                    Studies on the benthic and shoreline plants of the
                    Indian River region. In: Indian River<br>
                    Study, First Annual Report, 1973-
                    1974, Volume II, David R. Young, ed., Harbor<br>
                    Branch Consortium, Fort
                    Pierce, FL. Pp. 256-289.</p>
                <p class="body">Eiseman, N.J. 1980. An Illustrated Guide to the Sea Grasses of the Indian River Region
                    of<br>
                    Florida. Harbor Branch Foundation, Inc. Technical Report No. 31.&nbsp;
                    24 pages.</p>
                <p class="body">Fenchel, T. 1970. Studies on the decomposition of organic matter derived from turtle
                    grass,<br>
                    <i>Thalassia testudinum</i>. Limnol. Oceanogr.
                    15: 14-20</p>
                <p class="body">Fletcher, S.W. and W.W. Fletcher. 1995. Factors Affecting Changes in Seagrass<br>
                    Distribution and Diversity Patterns in the Indian River Lagoon Complex Between
                    1940<br>
                    and 1992. Bulletin of Marine Science 57(1), 49-58.</p>
                <p class="body">Florida (State of). Department of Natural Resources. 1985. Banana
                    River Aquatic<br>
                    Preserve management plan. Fla. Dep. of Natural Resources,
                    Bur. of Environ. Land<br>
                    Management, Division of Recreation and Parks,
                    Tallahassee, FL. 129 pp.</p>
                <p class="body">Fonseca, M.S., W.J. Kenworthy, and G.W. Thayer. 1998. Guidelines for
                    the conservation<br>
                    and restoration of seagrasses in the United States and
                    adjacent waters. NOAA Coastal<br>
                    Ocean Program Decision Analysis Series No.
                    12. NOAA Coastal Ocean Office. Silver<br>
                    Spring, MD.</p>
                <p class="body">French, Thomas D. and John R. Montgomery. 1983. Temporal dynamics of
                    copper<br>
                    chemistry in the shoal grass, <i>Halodule
                        wrightii</i> Aschers.
                    Fla. Sci. 46 (3/4): 135-145.</p>
                <p class="body">French, Thomas Daniel. 1980. Temporal dynamics of copper chemistry in
                    the shoal grass,<br>
                    <i>Halodule wrightii</i> Aschers. Master's Thesis, Fla.
                    Inst. of Tech., Melbourne, FL. 58 pp.</p>
                <p class="body">Fry, B. and P.L Parker. 1979. Animal diet in Texas seagrass meadows:
                    evidence for the<br>
                    importance of benthic plants. Est. Coast. Mar. Sci. 8:
                    499-509</p>
                <p class="body">Fuss, C.M. Jr, and J.A. Kelly, Jr. 1969. Survival and Growth of Sea Grasses Transplanted<br>
                    Under Artificial Conditions.
                    Bulletin of Marine Science 19(2), 351-365.</p>
                <p class="body">Fry, Brian and Robert W. Virnstein. 1988. Leaf production and export of the seagrass<br>
                    <i>Syringodium filiforme</i> Kutz. in Indian River
                    Lagoon, Florida. Aquatic Botany&nbsp;<br>
                    30:261-266.</p>
                <p class="body">Fry, Brian. 1983. Leaf growth in the seagrass <i>Syringodium
                        filiforme</i> Kutz. Aquatic<br>
                    Botany 16 (4): 361-368.</p>
                <p class="body">Gilbert, Steve and Kerry B. Clark. 1981. Seasonal variation in
                    standing crop of the<br>
                    seagrass <i>Syringodium filiforme</i> and
                    associated macrophytes in the northern Indian<br>
                    River, Florida. Estuaries
                    4 (3): 223-225.</p>
                <p class="body">Gilmore, R. G. 1987. Tropical-subtropical seagrasses of the
                    southeastern United States:<br>
                    Fishes and fish communities. Fla. Marine
                    Research Publ. 42: 117-137.</p>
                <p class="body">Gilmore, R. Grant, George R. Kulczycki, Philip A. Hastings and Wayne
                    C. Magley. 1976.<br>
                    Studies of fishes of the Indian River Lagoon and
                    vicinity. In: Indian River Coastal Zone<br>
                    Study, Third Annual Report,
                    1975-1976, Volume 1, David K. Young, ed., Harbor<br>
                    Branch Consortium, Fort
                    Pierce, FL. Pp. 133-147.</p>
                <p class="body">Gilmore, R. Grant, John K. Holt, Robert S. Jones, George R. Kulczycki,
                    Louis G.<br>
                    MacDowell III and Wayne C. Magley. 1978. Portable tripod drop
                    net for estuarine fish<br>
                    studies. Fishery Bulletin 76 (1):285-289.</p>
                <p class="body">Gore, Robert H., Edward E. Gallaher, Liberta E. Scotto and Kim A.
                    Wilson. 1981.<br>
                    Studies on decapod Crustacea from the Indian River region
                    of Florida. XI. Community<br>
                    composition, structure, biomass and species-areal
                    relationships of seagrass and drift<br>
                    algae-associated macrocrustaceans.
                    Estuarine, Coastal and Shelf Sci. 12 (4): 485-508.<br>
                    <br>
                    Gore, Robert H., Linda J. Becker, Nina Blum and Liberta E. Scotto.
                    1976. Studies of<br>
                    decapod Crustacea in the Indian River region of
                    Florida. In: Indian River Coastal Zone<br>
                    Study, Third Annual Report,
                    1975-1976, Volume 1, David K. Young, ed., Harbor<br>
                    Branch Consortium, Fort
                    Pierce, FL. Pp. 148-161.</p>
                <p class="body">Haddad, Kenneth D. 1985. Habitats of the Indian River. In: The Indian
                    River Lagoon:<br>
                    Proceedings Of The Indian River Resources Symposium, Diane
                    D. Barile, ed., Marine<br>
                    Resources Council of E. Central Fla., Fla. Inst.
                    of Tech., Melbourne, FL. Pp. 23-26.</p>
                <p class="body">Hall, M.O., and N.J. Eiseman. 1981. The Seagrass Epiphytes of the
                    Indian River, Florida<br>
                    I. Species List with Descriptions and Seasonal
                    Occurrences. Botanica Marina 24,<br>
                    139-146.</p>
                <p class="body">Hall, M.O. and S.S. Bell. 1988. Response of small motile epifauna to
                    a complexity of<br>
                    epiphytic algae on seagrass blades. J. Marine Research
                    46 (3): 613-630.</p>
                <p class="body">Hanlon, Roger and Gilbert Voss. 1975. Guide to the sea grasses of
                    Florida, the Gulf of<br>
                    Mexico and the Caribbean region. Sea Grant Field
                    Guide Ser. No. 4, Univ. of Miami<br>
                    Sea Grant, Univ. of Miami, Miami, FL. 30 pp.</p>
                <p class="body">Harbor Cities Watershed Action Committee. 1991. Seagrass restoration
                    in the Harbor<br>
                    Cities Watershed. Final rep., Harbor Cities Watershed
                    Action Committee, Conrad<br>
                    White, ed., Melbourne, FL. 7 pp.</p>
                <p class="body">Harrison, P.G. 1989. Detrital processing in seagrass systems: A
                    review of factors affecting<br>
                    decay rates, remineralization, and
                    detritivory. Aquat. Bot. 35: 263-288</p>
                <p class="body">Heffernan, J. J., R. A. Gibson, S. F. Treat, J. L. Simon, R. R. Lewis
                    III, R. L. Whitman,<br>
                    eds. 1985. Seagrass productivity in Tampa Bay: A
                    comparison with other subtropical<br>
                    communities. Proc. Tampa Bay Area Sci.
                    Info. Symp. p. 247.</p>
                <p class="body">Heffernan, John J. and Robert A. Gibson. 1983. A comparison of
                    primary production rates<br>
                    in Indian River, Florida seagrass systems. Fla.
                    Sci. 46 (3/4): 295-306.</p>
                <p class="body">Heijs, F.M.L. 1984. Annual biomass and production of epiphytes in
                    three monospecific<br>
                    seagrass communities of <i>Thalassia hemprichii</i> (Ehrenb.)
                    Aschers. Aquat. Bot. 20:<br>
                    195-218</p>
                <p class="body">Howard, R. K. 1983. Short term turnover of epifauna in small patches
                    of seagrass beds<br>
                    within the Indian River, Florida. Rep. presented at
                    Benthic Ecology Meeting, Fla. Inst.<br>
                    of Tech., Melbourne, FL.</p>
                <p class="body">Howard, R. K. 1987. Diel variation in the abundance of epifauna
                    associated with<br>
                    seagrasses of the Indian River Florida. Marine Biol. 96
                    (1): 137-142.</p>
                <p class="body">Howard, Robert K. 1985. Measurements of short-term turnover of
                    epifauna within<br>
                    seagrass beds using an in situ staining method. Marine
                    Ecology-Progress Ser. 22:<br>
                    163-168.</p>
                <p class="body">Howard, Robert K. and Frederick T. Short. 1986. Seagrass growth and
                    survivorship<br>
                    under the influence of epiphyte grazers. Aquatic Botany 24:
                    287-302.</p>
                <p class="body">Humm, Harold J. 1964. Epiphytes of the seagrass Thalassia testudinum,
                    in Florida. Bulletin<br>
                    Marine Sci. Gulf and Caribbean 14 (2): 306-341.</p>
                <p class="body">Jensen, Paul R. and Robert A. Gibson. 1986. Primary production in
                    three subtropical<br>
                    seagrass communities: A comparison of four autotrophic
                    components. Fla. Sci. 49 (3):<br>
                    129-141.</p>
                <p class="body">Kenworthy, W. J., M. S. Fonseca, D. E. McIvor and G. W. Thayer. 1989.
                    The submarine<br>
                    light regime and ecological status of seagrasses in Hobe
                    Sound, Florida. Annual Rep.<br>
                    National Marine Fisheries Serv., NOAA, S.E.
                    Fisheries Cent., Beaufort Laboratory,<br>
                    Beaufort, NC.</p>
                <p class="body">Kulczycki, George R., Robert W. Virnstein and Walter G. Nelson. 1981.
                    The relationship<br>
                    between fish abundance and algal biomass in a
                    seagrass-drift algae community.<br>
                    Estuarine, Coastal and Shelf Sci. 12
                    (3): 341-347.</p>
                <p class="body">Lewis, R.R. III. 1987. The Restoration and Creation of Seagrass
                    Meadows in the<br>
                    Southeast United States. Florida Marine Research
                    Publications 42, 153-173.</p>
                <p class="body">Livingston, R.J. 1987. Historic Trends of Human Impacts on
                    Seagrass Meadows in<br>
                    Florida. Florida Marine Research Publications 42, 139-151.</p>
                <p class="body">Marine Resources Council of East Florida. 1987. Marine Resources
                    Council, third annual<br>
                    meeting, land and water planning. Symposium abstr.,
                    Marine Resources Council, Fla.<br>
                    Inst. Tech., Melbourne, FL. 17 pp.</p>
                <p class="body">Martin County Conservation Alliance. 1992. The environmental health
                    of the estuarine<br>
                    waters of Martin County. Martin County Conserv.
                    Alliance, Stuart, FL. 53 pp.</p>
                <p class="body">McMillan, C. 1982. Reproductive Physiology of Tropical Seagrasses.
                    Aquatic Botany 14,<br>
                    245-258.</p>
                <p class="body">McMillan, C. and F.N. Moseley. 1967. Salinity Tolerances of
                    Five Marine<br>
                    Spermatophytes of Redfish Bay, Texas. Ecology 48(3), 503-506.</p>
                <p class="body">McRoy, C.P. and S. Williams-Cowper. 1978. Seagrasses of the United
                    States: an<br>
                    ecological review in relation to human activities. US Fish
                    and Wildlife Service<br>
                    FWS/OBS.</p>
                <p class="body">Mendonca, M.T. 1983. Movements and feeding ecology of immature green
                    turtles<br>
                    <i>Chelonia mydas</i> in a Florida lagoon. <i>Copeia</i> 4:
                    1013-1023.</p>
                <p class="body">Moffler, M.D. and M.J. Durako. 1987. Reproductive Biology of
                    the Tropical-Subtropical<br>
                    Seagrasses of the Southeastern United States. Florida Marine
                    Research Publications 42,<br>
                    77-88.</p>
                <p class="body">Moore, Donald R. 1963. Distribution of the sea grass, <i>Thalassia</i>,
                    in the United States.<br>
                    Bulletin Marine Sci. Gulf and Caribbean 13(2):
                    329-342.</p>
                <p class="body">Morgan, M.D. and C.L. Kitting. 1984. Productivity and utilization of
                    the seagrass<br>
                    <i>Halodule wrightii</i> and its attached epiphytes. Limnol.
                    Oceanogr. 29: 1099-1176</p>
                <p class="body">Nelson, Walter G. 1980. A comparative study of amphipods in
                    seagrasses from Florida to<br>
                    Nova Scotia. Bulletin Marine Sci. 30 (1):
                    80-89.</p>
                <p class="body">Nelson, Walter G. 1981. Experimental studies of decapod and fish
                    predation on seagrass<br>
                    macrobenthos. Marine Ecology-Progress Ser. 5 (2):
                    141-149.</p>
                <p class="body">Odum, E.P. and A.A. de la Cruz. 1963. Detritus as a major component
                    of ecosystems.<br>
                    Bull. Am. Inst. Biol. Sci. 13: 39-40</p>
                <p class="body">Packard, J. M. 1984. Impact of manatees <i>Trichecus manatus</i> on seagrass communities
                    in<br>
                    eastern Florida. Acta Zoological Fennica No. 172, pp. 21-22.</p>
                <p class="body">Penhale, P.A. 1977. Macrophyte-epiphyte biomass and productivity in
                    an eelgrass<br>
                    (<i>Zostera marina</i>) L. community. J. Exp. Mar. Biol.
                    Ecol. 26: 211-224</p>
                <p class="body">Phillips, R. C. 1960. Observations on the ecology and distribution of
                    the Florida<br>
                    seagrasses. Prof. Paper Ser. No. 2, Fla. State Board of
                    Conserv., Marine Laboratory,<br>
                    St. Petersburg, FL. 72 pp.</p>
                <p class="body">Phillips, R.C. 1967. On species of the seagrass, <i>Halodule</i>, in
                    Florida. Bulletin of Marine<br>
                    Sci. 17 (3): 672-676.</p>
                <p class="body">Phillips, R.C. 1976. Preliminary Observations on
                    Transplanting and A Phenological Index<br>
                    of Seagrasses. Aquatic Botany 2, 93-101.</p>
                <p class="body">Phillips R.C. and E.G. Menez. 1988. Seagrasses. Smithsonian
                    Institution Press.<br>
                    Washington, D.C.</p>
                <p class="body">Post, Buckley, Schuh and Jernigan, Inc. 1982. Environmental and
                    cost-benefit analyses of<br>
                    discharge alternatives for Harris Corporation
                    facilities in Palm Bay, Florida. Unpubl.<br>
                    Rep., Post, Buckley, Schuh and
                    Jernigan, Inc., Orlando, FL. 122 pp. Maps, figures,<br>
                    refs.</p>
                <p class="body">Rice, John D., Robert P. Trocine and Gary N. Wells. 1983. Factors
                    influencing seagrass<br>
                    ecology in the Indian River Lagoon. Fla. Sci. 46 (3/4): 276-286.</p>
                <p class="body">Salituri, Jeff Robert. 1975. A study of thermal effects on the growth
                    of manatee grass,<br>
                    <i>Cymodoceum manatorum</i>. Master's Thesis, Fla.
                    Inst. of Tech., Melbourne, FL.<br>
                    67 pp.</p>
                <p class="body">Sargent, F.J., T.J. Leary, D.W. Crewz, and C.R. Kruer. 1995. Scarring
                    of Florida's<br>
                    seagrasses: assessment and management options. Florida
                    Marine Research Institute<br>
                    Technical report TR-1. St. Petersburg,
                    Florida.</p>
                <p class="body">Short, F. T. and C. Zimmermann. 1983. The daylight cycle of a
                    seagrass environment.<br>
                    Unpubl. Rep., presented at Benthic Ecology
                    Meeting, Fla. Inst. Tech., Melbourne, FL.</p>
                <p class="body">Short, Frederick T. 1985. A method for the culture of tropical
                    seagrasses. Aquatic Botany<br>
                    22 (2): 187-193.</p>
                <p class="body">Snodgrass, Joel W. 1990. Comparison of fishes occurring in
                    monospecific stands of algae<br>
                    and seagrass. Master's Thesis, Univ. of
                    Central Fla., Orlando, FL. 51 pp.</p>
                <p class="body">Stephens, F. Carol and Robert A. Gibson. 1976. Studies of epiphytic
                    diatoms in the Indian<br>
                    River, Florida. In: Indian River Coastal Zone
                    Study, Third Annual Report, 1975-1976,<br>
                    Volume 1, David K. Young, ed.,
                    Harbor Branch Consortium, Ft. Pierce, FL. Pp. <br>
                    61-70.</p>
                <p class="body">Stoner, A. W. 1980. Perception and choice of substratum by epifaunal
                    amphipods<br>
                    associated with seagrasses. Marine Ecology-Progress Ser. 3:
                    105-111.</p>
                <p class="body">Stoner, Allan W. 1982. The influence of benthic macrophytes on the
                    foraging behavior of<br>
                    pinfish, <i>Lagodon rhomboides</i> (Linnaeus). J.
                    of Experimental Marine Biol. and Ecology<br>
                    58: 271-284.</p>
                <p class="body">Stoner, Allan W. 1983. Distribution of fishes in sea grass meadows:
                    Role of macrophyte<br>
                    biomass and species composition. Fishery Bulletin 81
                    (4): 837-846.</p>
                <p class="body">Stoner, Allan W. 1983. Distributional ecology of amphipods and
                    tanaidaceans associated<br>
                    with three sea grass species. J. Crustacean
                    Biol. 3 (4): 505-518.</p>
                <p class="body">Thompson, M. John. 1976. Photomapping and species composition of the
                    seagrass beds in<br>
                    Florida's Indian River estuary. Tech. Rep. No. 10,
                    Harbor Branch Found., Inc., Fort<br>
                    Pierce, FL. 34 pp, maps.</p>
                <p class="body">Thompson, M. John. 1978. Species composition and distribution of
                    seagrass beds in the<br>
                    Indian River lagoon, Florida. Fla. Sci. 41 (2):
                    90-96.</p>
                <p class="body">Thorhaug, A. 1990. Restoration of mangroves and seagrasses: economic
                    benefits for<br>
                    fisheries and mariculture. In: Environmental restoration:
                    science and strategies for<br>
                    restoring the Earth. Island Press.
                    Washington, D.C. Volume 265.</p>
                <p class="body">Tomasko, D.A. and B.E. Lapointe. 1991. Productivity and biomass of <i>Thalassia<br>
                        testudinum</i> as related to water column nutrient availability and
                    epiphyte levels: field<br>
                    observations and experimental studies. Mar. Ecol.
                    Prog. Ser. 75: 9-16</p>
                <p class="body">van Breedveld, J. F. 1975. Transplanting of seagrass with emphasis on
                    the importance of<br>
                    substrate. Fla. Marine Res. Publ. No. 17, Fla. Dep. of
                    Natural Resources, Marine Res.<br>
                    Laboratory, St. Petersburg, FL. 26
                    pp.</p>
                <p class="body">Virnstein, R.W., P.S. Mikkelsen, K.D. Cairns, and M.A. Capone. 1983.
                    Seagrass Beds<br>
                    Versus Sand Bottoms: The Trophic Importance of their
                    Associated Benthic<br>
                    Invertebrates. Florida Sci. 46(3/4), 363-381.</p>
                <p class="body">Virnstein, Robert W. and Patricia A. Carbonara. 1985. Seasonal
                    abundance and<br>
                    distribution of drift algae and seagrasses in the
                    mid-Indian River lagoon, Florida. Aquatic<br>
                    Botany 23 (1): 67-82.</p>
                <p class="body">Virnstein, R.W. and K.D. Cairns. 1986. Seagrass Maps of the
                    Indian River Lagoon. Final<br>
                    Report to DER, September 1986. Seagrass Ecosystems Analysts, 805 E.
                    46th Place,<br>
                    Vero Beach, Florida. 27 Pages.</p>
                <p class="body">Virnstein, R.W. 1987. Seagrass-associated Invertebrate
                    Communities of the Southeastern<br>
                    U.S.A.: A Review. Florida Marine Research Publications 42, 89-116.</p>
                <p class="body">Virnstein, R.W. 1995a. Seagrass Landscape Diversity in the Indian
                    River Lagoon, Florida:<br>
                    The Importance of Geographic Scale and Pattern. Bulletin of
                    Marine Science 57(1):<br>
                    67-74.</p>
                <p class="body">Virnstein, R.W. 1995b. Anomalous Diversity of Some
                    Seagrass-Associated Fauna in the<br>
                    Indian River Lagoon, Florida. Bulletin of Marine Science
                    57(1):
                    75-78.</p>
                <p class="body">Virnstein, R.W. and C. Curran. 1983. Epifauna of artificial seagrass:
                    Colonization patterns<br>
                    in time and space. Unpubl. Rep. presented at
                    Benthic Ecology Meeting, Fla. Inst. Tech.,<br>
                    Melbourne, FL.</p>
                <p class="body">Virnstein, R.W., K.D. Cairns, M.A. Capone and P.S. Mikkelsen. 1985.
                    Harbortown<br>
                    Marina seagrass study - a report to Old Park Investments,
                    Inc. Unpubl. Tech. Rep. No.<br>
                    55, Harbor Branch Found., Inc., Fort Pierce,
                    FL. 5 pp., 8 tables.</p>
                <p class="body">Virnstein, Robert W. 1978. Why there are so many animals in seagrass
                    beds, and does<br>
                    abundance imply importance? Fla. Sci. 41 (Suppl. 1): 24. (abstract)<br>
                    <br>
                    Virnstein, Robert W. 1982. Leaf growth rate of the seagrass <i>Halodule
                        wrightii<br>
                    </i> photographically measured in situ. Aquatic Botany 12 (3):
                    209-218.</p>
                <p class="body">Virnstein, Robert W. 1990. Seagrasses as a barometer of ecosystem
                    health. Abstract,<br>
                    Eighth Annual Coastal Management Seminar, Dec 1990,
                    Univ. Fla., Inst. Food &amp;<br>
                    Agricultural Sci., Cooperative Extension
                    Serv., Ft. Pierce, FL.</p>
                <p class="body">Virnstein, Robert W. and Mary Carla Curran. 1986. Colonization of
                    artificial seagrass<br>
                    versus time and distance from source. Marine
                    Ecology-Progress Ser. 29: 279-288.</p>
                <p class="body">Virnstein, Robert W. and Robert K. Howard. 1987. Motile epifauna of
                    marine<br>
                    macrophytes in the Indian River Lagoon, Florida. I. Comparisons
                    among three species<br>
                    of seagrasses from adjacent beds. Bulletin of Marine
                    Sci. 41 (1): 1-12.</p>
                <p class="body">Virnstein, Robert W. and Robert K. Howard. 1987. Motile epifauna of
                    marine<br>
                    macrophytes in the Indian River lagoon, Florida. II. Comparisons
                    between drift algae<br>
                    and three species of seagrasses. Bulletin Marine Sci.
                    41 (1): 13-26.</p>
                <p class="body">Virnstein, Robert W., John R. Montgomery and Wendy A. Lowery. 1987.
                    Effects of<br>
                    nutrients on seagrass. In: CM167 Final Report, Impoundment
                    Management, Indian<br>
                    River County Mosquito Control Dist., Vero Beach, FL,
                    Sep 30 1987, pp. 56-71.</p>
                <p class="body">White, C.B. 1986. Seagrass Maps of the Indian &amp; Banana
                    Rivers. Brevard County Office<br>
                    of Natural Resources Management, Merritt Island, Florida.</p>
                <p class="body">Young, D.K. and M.W. Young. 1977. Community structure of the
                    macrobenthos<br>
                    associated with seagrass of the Indian River estuary,
                    Florida. In: Ecology of Marine<br>
                    Benthos, B.C. Coull, ed., Univ. of S.
                    Carolina Press, Columbia, SC. Pp. 359-381.</p>
                <p class="body">Young, D.K., K.D. Cairns, MA. Middleton, J. E. Miller and M.W. Young.
                    1976. Studies<br>
                    of seagrass-associated macrobenthos of the Indian River.
                    In: Indian River Coastal Zone<br>
                    Study, Third Annual Report, 1975-1976,
                    Volume 1, David K. Young, ed., Harbor<br>
                    Branch Consortium, Fort Pierce, FL. Pp. 93-108.</p>
                <p class="body">Young, David K., Martin A. Buzas and Martha W. Young. 1976. Species
                    densities of<br>
                    macrobenthos associated with seagrass: A field experimental
                    study of predation. J.<br>
                    Marine Res. 34 (4): 577-592.</p>
                <p class="body">Young, David K., ed. 1976. Indian River coastal zone study. Third
                    annual report.<br>
                    1975-1976. A report on research progress October
                    1975-October 1976. Harbor<br>
                    Branch Consortium, Fort Pierce, FL. 187
                    pp.</p>
                <p class="body">Zieman, J.C. 1982. The Ecology of the Seagrasses of South
                    Florida: A Community Profile.<br>
                    U.S. Fish and Wildlife Services, Office of Biological Services,
                    Washington, D.C.<br>
                    FWS/OBS-82/25. 158 Pages.</p>
                <p class="body">Zieman, J.C., R. Orth, R. Phillips, G. Thayer, and A.
                    Thorhaug. 1984. The effects of oil on<br>
                    seagrass ecosytems. In: Recovery and Restortion of Marine
                    Ecosystems, edited by J.<br>
                    Cairns and A. Buikema. Butterworth Publications, Stoneham, MA. Pps. 37
                    - 64.</p>
                <p class="body">Zieman, J.C. 1987. A Review of Certain Aspects of the Life,
                    Death, and Distribution of the<br>
                    Seagrasses of the Southeastern United States 1960-1985.
                    Florida Marine Research<br>
                    Publications 42, pp. 53-76.</p>
                <p class="body">Zimmerman, R.J., R.A. Gibson and J.B. Harrington. 1976. The food and
                    feeding of<br>
                    seagrass-associated Gammaridean amphipods in the Indian
                    River. In: Indian River<br>
                    Coastal Zone Study, Third Annual Report,
                    1975-1976, Volume 1, David K. Young,<br>
                    ed., Harbor Branch Consortium, Fort
                    Pierce, FL. Pp. 87-92.</p>
                <p class="body">Zimmermann, Carl F. and John R. Montgomery. 1984. Effects of a
                    decomposing drift algal<br>
                    mat on sediment pore water nutrient
                    concentrations in a Florida seagrass bed. Marine<br>
                    Ecology Progress Ser.
                    19 (3): 299-302.</p>
                <p class="body">Zimmermann, Carl F., John R. Montgomery and Paul R. Carlson. 1985.
                    Variability of<br>
                    dissolved reactive phosphate flux rates in nearshore
                    estuarine sediments: Effects of<br>
                    groundwater flow. Estuaries 8 (2B):
                    228-236.</p>
                <p class="body">Zimmermann, Carl F., Thomas D. French and John R. Montgomery. 1981.
                    Transplanting<br>
                    and survival of the seagrass <i>Halodule wrightii</i> under controlled conditions. N.E. Gulf<br>
                    Sci. 4 (2): 131-136.</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="footer_note"> Report by: K. Hill, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
