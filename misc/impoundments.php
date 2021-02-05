<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$mangrovePlantArr = $IRLManager->getChecklistTaxa(42);
$mangroveAlgaeArr = $IRLManager->getChecklistTaxa(43);
$mangroveAnimalArr = $IRLManager->getChecklistTaxa(44);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Mosquito Impoundments</title>
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
    <h2>Mosquito Impoundments</h2>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center">
                <img border="0" src="../content/imglib/impound.jpg" hspace="10" vspace="10" width="450"></td>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Mosquito control impoundments are areas of salt marsh or mangrove forest that have been
                    diked to allow control of water levels for purposes of mosquito control. Within the dikes, perimeter
                    ditches are flooded artificially in order to control breeding and reproduction of salt marsh
                    mosquitoes without the use of pesticides. Florida's mangroves and salt marshes have historically
                    been problem areas in one important respect: they are preferred breeding habitat for salt marsh
                    mosquitoes (Ochlerotatus taeniorrhynchus and O. sollicitans). These mosquitoes are an important
                    nuisance species that affect the health of humans and domestic animals. Salt marsh mosquitoes do not
                    reproduce by laying their eggs in standing water. Rather, they deposit eggs in the moist soils of
                    high marsh above the water line in tidal wetlands (Provost 1976). Eggs will remain dormant, often
                    for long periods of time, until water levels rise in response to rains or tides.</p>
                <p class="body">Eggs hatch in the water and undergo several larval stages before developing into adult
                    mosquitoes within 5 days. In the vicinity of the Indian River Lagoon, concerted efforts aimed at
                    controlling salt marsh mosquitoes began in the mid-1920s (Platt et al. 1943) with construction of
                    miles of hand-dug, parallel ditches. These efforts were not highly successful because of the heavy
                    maintenance required to maintain the ditches, and because tidal effects in the ditched areas were
                    generally of such low amplitude that little mosquito control was effected (Rey and Rutledge
                    2001).</p>
                <p class="body">
                    In the 193's field experiments demonstrated that controlling water levels through impoundment would
                    provide source reduction of mosquito populations by effectively controlling mosquito reproduction
                    (Hull and Dove 1939). However, this experimental program was abandoned as water losses within the
                    impoundment through seepage and evaporation became problematic. Attention then turned toward the use
                    of pesticides such as DDT. However, by the 1950s, concerns over pesticide resistance in insects
                    began to emerge, and the focus of mosquito control again shifted back to source reduction.</p>

                <p class="body">
                    The first impoundments in Florida were built in Brevard County in 1954, with other counties soon
                    following. By the 1970's, in excess of 40,000 acres of Florida's coastal wetlands had been impounded
                    (Rey and Kain 1990). The majority of impoundments were constructed at the mean high water level and
                    then flooded year round, closed off from adjacent estuarine waters. Some, however, were allowed to
                    drain during the winter months, but were flooded again as mosquito breeding season approached.</p>

                <p class="title">Negative effects of closed impoundments: </p>
                <p class="body">Although impoundment for mosquito control is an effective method of controlling mosquito
                    populations, there are often severe environmental impacts on impounded wetlands isolated from
                    adjacent estuaries. Particularly important are issues of water quality degradation, isolation of
                    important fishery species from critical nursery habitats, interruption of nutrient flow between
                    wetlands and estuarine waters, creation of unnaturally high water levels, and hypersaline conditions
                    that may develop in closed impoundments when evaporation of water occurs. Any negative changes in
                    any of these physical parameters may lead to the elimination of vegetation from such areas (Rey and
                    Rutledge 2001).
                </p>

                <p class="title">Water levels:</p>
                <p class="body">Excessively high water levels brought on by overflooding impoundments eliminated some
                    species from salt marsh and mangrove communities altogether. While only a thin film of water is
                    enough to prevent oviposition by salt marsh mosquitoes; impoundments are typically flooded to depths
                    of 15 - 50 cm above the surface to compensate for evaporation effects (Rey et al. 1991). In closed
                    impoundments, this practice eliminated some species such as saltwort (Batis maritima), and glasswort
                    (Salicornia bigelovii, and Salicornia virginica), and also impacted black mangroves due to their
                    short pneumatophores not being able to withstand prolonged flooding (Rey and Rutledge 2001).</p>

                <p class="title">Water quality:</p>
                <p class="body">
                    Closed impoundments showed significant changes in both water quality and soil chemistry. In many
                    areas, isolation of flooded impoundments resulted in decreased dissolved oxygen concentrations and
                    increases in both nitrogen and sulfide concentrations in soils. Some impoundments flooded by use of
                    artesian wells showed ecological turnovers from having communities composed of predominantly
                    halophytic species, to communities characteristic of fresh water habitats. Other impoundments were
                    subject to hypersaline conditions when estuarine waters were pumped in to flood them during warm
                    summer months. Because these impoundments were closed to adjacent waters, lack of flushing and
                    evaporation resulted in extremely high salinities, which caused local extinctions of some species
                    (Rey and Rutlege 2001).</p>

                <p class="title">Effects on fish and invertebrates:</p>
                <p class="body">Fish species were greatly affected by closed impoundments, with numbers of some species
                    being significantly reduced in species that utilized salt marsh or mangrove areas as nursery grounds
                    (Harrington and Harrington 1961, Snelson 1976, Gilmore et al. 1982, Rey et al 1990). Tarpon
                    (Megalops atlanticus), ladyfish (Elops saurus), common snook (Centropomus undecimalis), mullet
                    (Mugil cephalus), and other species important to commercial and recreation fisheries were adversely
                    impacted by closed impoundments. Marine invertebrates were also impacted by isolation of impounded
                    wetlands, with biodiversity and species abundance changing dramatically in some areas. In some
                    areas, the invertebrate community became more characteristic of freshwater wetlands than marine or
                    estuarine wetlands (Brockmeyer et al. 1997).</p>

                <p class="title">Nutrient flow:</p>
                <p class="body">In closed impoundments, natural patterns of nutrient flow are interrupted between
                    mangrove areas and adjacent waters. In unaltered systems, nutrients from mangrove leaf fall, which
                    are decomposed into particulate and dissolved forms, are utilized in a variety of ways by many
                    different organisms as mangroves are flushed by tides. In closed impoundments, however, nutrients
                    are never flushed from mangrove areas because there is no connection to estuarine waters, and thus
                    remain confined within impoundments.</p>

                <p class="title">Improved Impoundment Strategies:</p>

                <p class="body">An improved strategy for impoundments was experimented with as early as the mid-1960s.
                    This involved seasonal flooding of impoundments during peak mosquito breeding season. For the
                    remainder of the year, impoundments were opened via culverts penetrating the dike so that water
                    levels within the impoundment could fluctuate naturally with tides. In 1974, seasonal impoundment
                    was combined with active water management. This strategy of allowing for impoundments to be
                    adequately flushed by tides not only controlled salt marsh mosquitoes, but also helped to retain
                    black mangroves and other vegetation, and allowed the return of juvenile fishes to nursery areas
                    unavailable to them in closed impoundments. This management strategy is currently referred to as
                    Rotational Impoundment Management (RIM).</p>
                <p class="body">
                    Under RIM, estuaries retain many of their natural functions, and their primary productivity can
                    rival that of unaltered wetlands (Lahmann 1998, Rey et al. 1990b). Culverts remain open between the
                    impoundment and the estuary from October to May to allow water exchange and use of impoundments by
                    transient fish species and invertebrates. Then, during the summer months, culverts are closed and
                    impoundments flooded to the minimum levels needed to prevent oviposition in salt marsh mosquitoes.
                    Low areas of the surrounding dike, called spillways, insure that water levels do not exceed
                    prescribed levels, thus preventing injury to vegetation. RIM has proven to be an effective strategy
                    for controlling mosquitoes while minimizing serious environmental impacts to estuaries. Data from
                    Rey et al. (1991) shows that RIM is currently the most commonly employed management strategy in 3 of
                    the 5-counties adjacent to the Indian River Lagoon. St. Lucie County has 1,371 hectares (ha) (3386.4
                    acres) of wetlands under RIM, Brevard County has 1,037 ha (2561.4 acres), and Indian River County
                    has 448 ha (1106.5 acres). </p>

                <p class="title">Select a highlighted link below to learn more about that species:</p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="5"
           class="table-border no-border alternate">
        <tr>
            <th>Species Name</th>
            <th>Common Name</th>
            <th>Habitat Useage</th>
        </tr>
        <?php
        if($mangrovePlantArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Mangrove Plants:</p></td>
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
                echo '<td><span>'.$taxArr['habitat'].'</span></td>';
                echo '</tr>';
            }
        }
        if($mangroveAlgaeArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Mangrove Algae, Diatoms, and Other Protists:</p></td>
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
                echo '<td><span>'.$taxArr['habitat'].'</span></td>';
                echo '</tr>';
            }
        }
        if($mangroveAnimalArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Mangrove Animals:</p></td>
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
                echo '<td><span>'.$taxArr['habitat'].'</span></td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td width="767">
                <p class="title">Further Reading</p>
                <p class="body">
                    Brockmeyer, R.E., J.R. Rey, R.W. Virnstein, R.G. Gilmore, Jr., and L. Earnest. 1997. Rehabilitation
                    of impounded estuarine wetlands
                    by hydrologic reconnection to the Indian River Lagoon, Florida. Journal of Wetlands<br> Ecology and
                    Management. 4:93-109.&nbsp;</p>

                <p class="body">Carlton, J.M. 1975. A guide to common salt marsh and mangrove vegetation. Florida Marine
                    Resources Publications, No. 6. Carlton, 1977. A survey of selected coastal vegetation communities of
                    Florida. Florida Marine Research Publications, No. 30.</p>

                <p class="body"> Feller, I. C., Ed. 1996. Mangrove Ecology Workshop Manual. A Field Manual for the
                    Mangrove Education and Training Programme for
                    Belize. Marine Research Center, University College of Belize, Calabash Cay, Turneffe Islands.
                    Smithsonian Institution, Washington DC.</p>
                <p class="body">Gilmore, R.G. Jr., D.W. Cooke, and C.J. Donahue. 1982. A comparison of the fish
                    populations and habitat in open and closed salt
                    marsh impoundments in east central Florida. Northeast Gulf Science,
                    5:25-37.</p>
                <p class="body">Gilmore, R.G. Jr. and S.C. Snedaker. 1993. Chapter 5: Mangrove Forests. In: W.H. Martin,
                    S.G. Boyce and A.C. Echternacht, eds.
                    Biodiversity of the Southeastern United States: Lowland Terrestrial
                    Communities. John Wiley and Sons, Inc. Publishers. New York, NY. 502 pps.</p>

                <p class="body">Harrington, R.W. and E.S. Harrington. 1961. Food selection among fishes invading a high
                    subtropical salt marsh; from onset of
                    flooding through the progress of a mosquito brood. Ecology, 42:646-666.</p>

                <p class="body">Heald, E.J. and W.E. Odum. 1970. The contribution of mangrove swamps to Florida
                    fisheries. Proceedings Gulf and Caribbean
                    Fisheries Institute, 22:130-135.</p>
                <p class="body">Heald, E.J., M.A. Roessler, and G.L. Beardsley. 1979. Litter production in a southwest
                    Florida black mangrove community.
                    Proceedings of the Florida Anti-Mosquito Association 50<sup>th</sup> Meeting. Pp. 24-33.</p>
                <p class="body">Hull, J.B. and W.E. Dove. 1939. Experimental diking for control of sand fly and mosquito
                    breeding in Florida saltwater marshes.
                    Journal of Economic Entomology, 32:309-312. </p>
                <p class="body">Lahmann, E. 1988. Effects of different hydrologic regimes on the productivity
                    of Rhizophora mangle L. A case study of mosquito control
                    impoundments in Hutchinson Island, St. Lucie County, Florida. Ph.D.
                    dissertation, University of Miami, Coral Gables, Florida.</p>
                <p class="body">Lewis, R.R., III, R.G. Gilmore, Jr., D.W. Crewz, and W.E. Odum. 1985. Mangrove habitat
                    and fishery resources of Florida. In: W.
                    Seaman, Jr. (ed.). Florida Aquatic Habitat and Fishery Resources. American Fisheries Society,
                    Florida Chapter, Kissimmee, FL. </p>
                <p class="body">Lugo, A.E. and S.C. Snedaker. 1974. The ecology of mangroves. Annual Review of Ecology
                    and Systematics 5:39-64.</p>
                <p class="body">Lugo,
                    A.E., M. Sell, and S.C. Snedaker. 1976. Mangrove ecosystem analysis. In: Systems Analysis and
                    Simulation in Ecology, B.C.
                    Patten, ed. Pp. 113-145. Academic Press, New York, NY.</p>
                <p class="body">Odum, W.E. and C.C. McIvor. 1990. Mangroves. In: Ecosystems of Florida, RL. Myers and
                    J.J. Ewel, eds. Pp. 517 - 548. University of Central Florida Press, Orlando, FL. </p>
                <p class="body">Odum, W.E., C.C. McIvor, and T.J. Smith III. 1982. The ecology of the mangroves of south
                    Florida: a community profile. U.S.
                    Fish and Wildlife Service, Office of Biological Services, FWS/OBS-81-24.</p>
                <p class="body">Odum, W.E. and E.J. Heald. 1972. Trophic analyses of an estuarine mangrove community.
                    Bulletin of Marine Science, 22(3):671-738.</p>
                <p class="body">Onuf, C.P., J.M. Teal, and I. Valiela. 1977. Interactions of nutrients, plant growth and
                    herbivory in a mangrove ecosystem. Ecology,
                    58:514-526.</p>
                <p class="body">Platts, N.G., S.E. Shields, and J.B. Hull. 1943. Diking and pumping for
                    control of sand flies and mosquitoes in Florida salt marshes. Journal of Economic Entomology,
                    36:409-412.</p>
                <p class="body">Pool, D.J., A.E. Lugo, and S.C. Snedaker.1975. Litter production in mangrove forests of
                    southern Florida and Puerto Rico. Proceeding of the International Symposium on Biological Management
                    of Mangroves, G. Walsh, S. Snedaker and H. Teas, eds. Pp. 213-237. University of Florida Press,
                    Gainesville, FL.</p>
                <p class="body">Provost, M.W. 1976. Tidal datum planes circumscribing salt marshes. Bulletin of Marine
                    Science, 26:558-563.</p>
                <p class="body">Rey, J.R. and T. Kain. 1990. Guide to the salt marsh impoundments of Florida. Florida
                    Medical Entomology Laboratory Publications, Vero Beach, FL. </p>
                <p class="body">Rey, J.R., J. Schaffer, D. Tremain, R.A. Crossman, and T. Kain. 1990. Effects of
                    reestablishing tidal connections in two impounded
                    tropical marshes on fishes and physical conditions. Wetlands. 10:27-47. </p>
                <p class="body">Rey, J.R. M.S. Peterson, T. Kain, F.E. Vose, and R.A. Crossman. 1990. Fish populations
                    and physical conditions in ditched and
                    impounded marshes in east-central Florida. N.E. Gulf Science, 11:163-170.
                    </font></p>
                <p class="body">Rey,
                    J.R., R.A. Crossman, M. Peterson, J. Shaffer and F. Vose. 1991. Zooplankton of impounded marshes and
                    shallow areas of a
                    subtropical lagoon. Florida Scientist, 54:191-203.</p>
                <p class="body">Rey,
                    J.R., R.A. Crossman, T. Kain, and J. Schaffer. 1991. Surface water chemistry of wetlands and the
                    Indian River Lagoon,
                    Florida, USA. Journal of the Florida Mosquito Control Association, 62:25-36.</p>
                <p class="body">Rey,
                    J.R., T. Kain and R. Stahl. 1991. Wetland impoundments of east-central Florida. Florida Scientist,
                    54:33-40.</p>
                <p class="body">Rey,
                    J.R. and C.R. Rutledge, 2001. Mosquito Control Impoundments. Document # ENY-648, Entomology and
                    Nematology
                    Department, Florida Cooperative Extension Service, Institute of Food and
                    Agricultural Sciences, University of Florida. Available on the Internet at :&nbsp;
                    <a href="http://edis.ifas.ufl.edu">http://edis.ifas.ufl.edu</a>.
                </p>
                <p class="body">
                    Simberloff, D.S. 1983. Mangroves. In: Costa Rican Natural History. D.H. Janzen, ed. Pp. 273-276.
                    University of Chicago Press,
                    Chicago, IL. </p>
                <p class="body">
                    Snedaker, S.C. 1989. Overview of mangroves and information needs for Florida Bay. Bulletin of Marine
                    Science, 44(1):341-347.&nbsp;
                </p>
                <p class="body">
                    Snedaker, S. C., and A.E. Lugo. 1973. The role of mangrove ecosystems in the maintenance of
                    environmental quality and a high
                    productivity of desirable fisheries. Final report to the Bureau of Sport
                    Fisheries and Wildlife in fulfillment of Contract no. 14-16-008-606. Center for
                    Aquatic Sciences, Gainesville, FL.</p>
                <p class="body">
                    Snelson, F.F. 1976. A study of a diverse coastal ecosystem on the Atlantic
                    coast of Florida, Vol. 1., Ichthyological Studies.
                    NGR-10-019-004 NASA, Kennedy Space Center, Florida.</p>
                <p class="body">Thayer, G.W., D.R. Colby, and W.F.
                    Hettler Jr. 1987. Utilization of the red mangrove prop roots habitat by fishes in South Florida.
                    Marine Ecology progress Series, 35:25-38.</p>
                <p class="body">
                    Tomlinson, P.B. 1986. The botany of mangroves. Cambridge University Press, London. </p>
                <p class="body">
                    Waisel, Y. 1972. The biology of halophytes. Academic Press, New York, NY.</p>

                <p class="drkbluenorm"><br>
                    &nbsp;</p>
                <p align="center" class="drkbluenorm"><font size="2" color="#036">Report by: K. Hill,
                        Smithsonian Marine Station<br>
                        Submit additional information, photos or comments to:<br>
                        <span lang="en-us"><a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></span></font>
                </p></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
