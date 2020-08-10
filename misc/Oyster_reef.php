<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$invertebrateArr = $IRLManager->getChecklistTaxa(18);
$vertebrateArr = $IRLManager->getChecklistTaxa(19);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Oyster Reef Habitats</title>
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
    <h2>Oyster Reef Habitats</h2>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center">
                <img border="0" src="../content/imglib/OysterFlat1.jpg" hspace="5" vspace="5" width="542" height="237">
            </td>
        </tr>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Oyster reefs, often referred to as oyster bars, are common
                    submerged habitats in the southern United States. Oyster reefs in Florida are
                    found in nearshore areas and estuaries of both coasts, but grow especially
                    vigorously near estuarine river mouths where waters are brackish and less than
                    10 meters deep. For example, the Apalachicola River in northern Florida is a
                    particularly productive area for oysters, and supplies over 90% of the state's
                    annual oyster catch. Within the Indian River Lagoon, oyster reefs may be found
                    in the vicinity of spoil islands and impounded areas. In addition to being
                    commercially valuable, oyster reefs serve a number of important ecological roles
                    in coastal systems: providing important habitat for a large number of species;
                    improving water quality; stabilizing bottom areas, and influencing water
                    circulation patterns within estuaries.</p>

                <table class="image_left">
                    <tr>
                        <td><img border="5" src="../content/imglib/Crasso_virgin2.jpg" align="left" style="margin: 5px"
                                 width="228" height="218"></td>
                    </tr>
                </table>
                <p class="body">Oyster reefs are built primarily by the eastern oyster,
                    <i>Crassostrea virginica,</i> through successive reproduction and
                    settlement of larvae onto existing reef structure. Oysters in Florida spawn from
                    late spring through the fall. The planktonic larvae that develop require a hard
                    substratum to settle upon in order to complete development to the juvenile
                    stage, and prefer to settle on the shells of other oysters. Thus, over time,
                    continued settlement and subsequent growth of generations of oysters may form
                    massive reef structures consisting of staggering numbers of individuals. Luntz
                    (1960), estimated that 5,895 oysters, the equivalent of 45 bushels, occurred
                    within a single square yard of oyster reef.</p>
                <p class="body">As successive generations of oysters settle and grow,
                    reefs become highly complex, with many structural irregularities and infoldings
                    that provide a wealth of microhabitats for many different species of animals.
                    Wells (1961) listed 303 different species utilizing oyster reef as habitat in
                    North Carolina. Common Indian River Lagoon species associated with oyster reefs
                    include bivalves such as the hard clam (<i>Mercenaria mercenaria</i>) and bay
                    scallop (<i>Argopecten irradians concentricus</i>); space competitors such as
                    the scorched mussel (<i>Brachidontes exustus</i>), ribbed mussel (<i>Geukensia
                        demissa</i>), the jingle shell (<i>Anomia simplex</i>), and barnacles of the <i>Balanus</i>
                    genus; gastropod mollusks such as the conchs (<i>Melongena</i> spp. and <i>Strombas</i>
                    spp.) and rocksnails (<i>Thais</i> spp.); numerous sponge species; flatworms;
                    polychaete worms; amphipods; isopods; shrimp; and fishes such as blennies,
                    gobies, spadefish, snappers, drum, and seatrout, among others.</p>

                <p class="body">Beyond providing smaller organisms with habitat, oyster
                    reefs also provide food to a wide variety of secondary consumers. Many species
                    of fish prey upon oyster reef associates; while others such as the black drum (<i>Pogonias
                        cromis</i>) and cow-nosed ray (<i>Rhinoptera bonasus</i>) prey upon oysters
                    themselves. Other species that utilize oyster reefs for foraging and feeding
                    include the xanthid crabs, also known as mud crabs; swimming crabs of the genus <i>Callinectes</i>;
                    mollusks such as the thick lipped oyster drill (<i>Eupleura caudata</i>), the
                    sharp-rib drill (<i>E. sulcidentata</i>), the Atlantic oyster drill (<i>Urosalpinx
                        cinerea</i>), the Tampa drill (<i>U. tampaensis</i>), the knobbed whelk (<i>Busycon
                        carica</i>), the lighthire whelk (<i>B. contrarium</i>), and the pear whelk (<i>B.
                        spiratum pyruloides</i>); flatworms such as oyster leeches (<i>Stylochus </i>spp.);
                    boring sponges (C<i>liona</i> spp.); and annelid worms (P<i>olydora</i> spp.).</p>

                <p class="body">Oyster reefs also contribute to improved water quality
                    in areas where they occur. Oysters are filter feeders which strain microalgae,
                    suspended particulate organic matter, and possibly dissolved organic matter from
                    the water column over their gills in order to feed. Under optimal temperature
                    and salinity conditions, a single oyster may filter as much as 15 liters of
                    water per hour, up to 1500 times its body volume. Spread over an entire reef,
                    for an entire day, the potential for oysters to improve water clarity is
                    immense. Additionally, since oysters are <a href="#sessile"
                                                                onClick="popDef('non-motile;  living permanently attached to a substratum', '#sessile');"
                                                                name="sessile">sessile</a>, and bioaccumulate some
                    potential toxins and pollutants found in the water column, they have been used
                    to assess the environmental health of some areas.</p>

                <p class="body">Over-harvesting, as well as persistent diseases such as
                    MSX and Dermo have taken a devastating toll on many oyster populations along the
                    east and Gulf coasts. In recent years, oyster reef restoration has been a
                    concern for resource managers all along the East Coast of the United States, but
                    especially in areas where oyster harvesting has historically been commercially
                    important. In the late 1800s, for example, annual oyster harvests in the
                    southeastern United States routinely topped 10 million pounds per year, and
                    peaked in 1908 when the harvest was nearly 20 million pounds. However, annual
                    harvests since that time have declined steadily. Today, annual harvests for
                    oysters in the southeast averages approximately 3 million pounds per year. In
                    many areas, efforts are underway to revitalize depleted oyster reefs and
                    encourage growth of new reefs. For example, the Florida Department of
                    Agriculture has stockpiled calico scallop shells from processors and placed
                    these on depleted oyster reefs from the spring through the fall spawning
                    periods, when larvae are most abundant in the water column. Oyster larvae,
                    having a preference for settling on shell material, then attach themselves onto
                    the newly placed shells and metamorphose to the juvenile stage. These young
                    oysters, under optimal conditions, will grow to marketable size in as little as
                    18 - 24 months.</p>

                <p class="body"><i>A more detailed look at some emerging human-induced threats facing the oyster reefs
                        of the IRL is <a href="Oystereef_Emerging_Issues.php">available here</a></i></p>
                <p class="title">Click a highlighted link to read more about individual species:</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="2"
           class="table-border no-border alternate">
        <tr>
            <th>Species Name</th>
            <th>Common name</th>
            <th>Comments</th>
        </tr>
        <?php
        if($invertebrateArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Invertebrates:</p></td>
            </tr>
            <?php
            foreach($invertebrateArr as $id => $taxArr){
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
        if($vertebrateArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Vertebrates:</p></td>
            </tr>
            <?php
            foreach($vertebrateArr as $id => $taxArr){
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
            <td>
                <p class="title">Further Reading</p>
                <p class="body">Bahr, L.M. and W.P. Lanier. 1981. The Ecology
                    of Intertidal Oyster Reefs of the South Atlantic Coast: a <br/>&nbsp;&nbsp; Community
                    Profile. U.S. Fish and Wildlife Service, Blot. Program, Washington D.C. FWS/OBS 81/15. 105 pp.</p>
                <p class="body">Burrell, V.G. 1986. Species Profiles: Life
                    Histories and Environmental Requirements of Coastal Fishes and <br/>
                    &nbsp;&nbsp; Invertebrates (South Atlantic):
                    American Oyster. U.S. Fish and Wildlife Service. Biological Report 82(11.57). <br/>&nbsp;&nbsp; U.S.
                    Army Corps of Engineers. TR EL-82-4. 17 pp.</p>
                <p class="body">Kumari, Siva, and C. Solis. 1995. The State of
                    the Bay: a Characterization of the Galveston Bay Ecosystem. Rice university, Houston, TX. Accessed
                    on-line at: <a HREF="http://galvbaydata.org/StateoftheBay/tabid/1846/Default.aspx">www.rice.edu/armadillo/Galveston/Chap3/oyster.html</a>.
                </p>
                <p class="body">Livingston, Robert J. 1990. Inshore Marine Habitats. In: Ecosystems of Florida, Ronald
                    L. Myers and John J. <br/>&nbsp;&nbsp; Ewel, Eds. University of Central Florida Press, Orlando, FL.
                    Pp. 549-573.</p>
                <p class="body">Lunz, G.R., Jr. 1960. Intertidal Oysters. Wards
                    Natl. Sci. Bull. 34(1): 3-7</p>
                <p class="body">Wells, H.W. 1961. The Fauna of Oyster Beds with Special Reference to the Salinity
                    Factor. Ecological <br/>&nbsp;&nbsp; Monographs 31(3): 239-266.</p>
            </td>
        </tr>
        <tr>
            <td>
                <p class="footer_note">Report by K. Hill,
                    Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:irl_webmaster@si.edu">irl_webmaster@si.edu</a>
                </p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
