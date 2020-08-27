<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$scrubPlantArr = $IRLManager->getChecklistTaxa(28);
$scrubAnimalArr = $IRLManager->getChecklistTaxa(29);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Scrub Habitats</title>
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
    <h2>Scrub Habitats</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td align="center"><img border="0" src="../content/imglib/Scrub2.jpg" hspace="25" vspace="5" width="418"
                                    height="228"></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Stable backdune areas give rise to scrub communities built
                    upon sandy or well drained soils, with the predominant vegetation being
                    herbaceous shrubs, evergreen oaks, or pines. Coastal scrub communities, commonly
                    referred to as coastal strand, are becoming vanishing ecosystems due to
                    developmental pressures in the coastal zone. Most of the coastal habitats from
                    Cape Canaveral in Brevard County, to Miami in Dade County have been highly
                    fragmented due to development. In Brevard County alone, it has been estimated
                    that the natural scrub community was diminished by 69% during the period between
                    1943 - 1991 (Schmalzer 1995; <a href="Scrub_decline.php"> Robinson and Smith
                        1996</a>).</p>
                <p class="body">Scrub, except saw palmetto scrub, is a term often used
                    to describe well-drained xeric habitats (Woolfenden and Fitzpatrick 1984). Scrub
                    is generally characterized as open pineland with an oak or palmetto understory,
                    that is well adapted to dry conditions. However, scrub habitats fall into a
                    number of categories based on vegetation structure and composition: coastal
                    scrub, oak scrub, sand pine scrub, rosemary scrub, slash pine scrub, and scrubby
                    flatwoods. Each type of scrub is also characterized based on soil type,
                    geography, and fire patterns in the area. Leaf fall is minimal in scrub areas,
                    and ground cover is generally sparse due to shading effects from the overstory
                    trees. Open patches of sand are often present in scrub lands, and where they
                    occur, understory trees and woody shrubs benefit from the intense sunlight that
                    reaches the ground.</p>
                <p class="body">Florida's scrub and pine flatwoods consist of similar
                    shrub layers, with pine flatwoods differing by having an open canopy of slash
                    pine (<em>Pinus elliotii</em>) intermingled with pond pine (<em>P. serotina</em>).
                    Drier areas tend to be dominated by scrub oaks (<em>Quercus myrtiflolia</em>, <em>Q.
                        geminata</em>, <em>Q. chapmanii</em>), while less well-drained areas are
                    dominated by saw palmetto (<em>Serenoa repens)</em> (Schmalzer and Hinkle 1987,
                    1991, 1992; Breininger et al. 1988; Breininger and Schmalzer 1990). In many
                    Indian River Lagoon sites, a mixed oak/palmetto shrub layer occurs.</p>

                <p class="body">While coastal scrub communities are impacted more by
                    the strong winds and flooding brought on during storm events, most types of
                    scrub are maintained primarily by fires. Low leaf fall, coupled with sparse
                    ground vegetation insures that the risk of frequent fire is reduced. But, as
                    sand pines mature, retaining branches and increasing in size, their crowns build
                    up large fuel supplies for hot burning, fast moving fires. Fire, when it does
                    occur, regenerates the scrub community and prevents its succession to an oak
                    hammock or scrubby flatwoods community by dispersing pine seeds, recycling
                    minerals back to the earth as ash, and diminishing the oak or palmetto
                    understory.</p>
                <p class="body">Herbaceous scrub species, many of which are gap specialists,
                    are vulnerable to competition and eventual competitive
                    exclusion from scrub areas. These plants benefit from reduced competition in the
                    burn zone following a fire. Some studies indicate that gap specialists may be
                    more abundant in an area following fire, than they are when the area is
                    fire-free for long periods. Frequent fires are more beneficial to oak scrub and
                    scrubby flatwoods communities; while less frequent fires are more beneficial to
                    sand pine scrub and other pine-dominated scrub types.</p>

                <p class="title">Scrub Plants:</p>
                <p class="body">The scrub communities of east central Florida's
                    barrier islands typically consists of coastal scrub, also called strand. Coastal
                    scrub occurs immediately behind dune systems and is dominated primarily by saw
                    palmetto (<em>Serenoa repens</em>) and other common shrubs such as nakedwood (<em>Myrcianthes
                        fragrans</em>), tough buckthorn (<em>Bumelia tenax</em>), rapanea (<em>Rapanea
                        punctata</em>), hercules club (<em>Zanthoxylum clava-hercules</em>), bay (<em>Persea
                        borbonia),</em> sea grapes (<i>Coccoloba uvifera</i>) and snowberry (<em>Chiococca alba</em>).
                    Shrubby forms of live
                    oak (<em>Quercus virginana</em>) are also common in coastal scrub communities.
                    Indicator species for other types of scrub communities are: sand pine (<i>Pinus
                        clausa</i>), myrtle oak (<i>Quercus myrtifolia</i>), scrub live oak (<i>Q.
                        geminata</i>), Chapman's oak (<i>Q. chapmanii</i>), coastalplain goldenaster (<i>Chrysopsis
                        scabrella</i>), and narrowleaf silkgrass (<i>Pityopsis graminifolia</i>).</p>

                <p class="title">Scrub Animals:</p>
                <p class="body">A number of animals are found in scrub habitats,
                    including some of Florida's most threatened and endangered species. Among them
                    are the gopher tortoise (<i>Gopherus polyphemus</i>), the eastern indigo snake (<i>Masticophis
                        flagellum flagellum</i> ), the southeastern beach mouse (<i>Peromyscus
                        polionotus niveiventris</i>), and the Florida scrub jay<i> (Aphelocoma
                        coerulescens)</i>. Many other animals also utilize scrub areas for feeding
                    and for shelter.</p>

                <p class="body">Select a highlighted link below to learn more about that species:</p></td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3"
           class="table-border no-border alternate">
        <tr>
            <th>Species Name:</th>
            <th>Common Name:</th>
            <th>Habitat Notes:</th>
        </tr>
        <?php
        if($scrubPlantArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Scrub Plants:</p></td>
            </tr>
            <?php
            foreach($scrubPlantArr as $id => $taxArr){
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
        if($scrubAnimalArr){
            ?>
            <tr class="heading">
                <td colspan="3"><p class="label">Scrub Animals:</p></td>
            </tr>
            <?php
            foreach($scrubAnimalArr as $id => $taxArr){
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
            <td><p class="body"><sup>1</sup> Found throughout the IRL<br/>
                    <sup>2</sup> Most common in Northern IRL and Cape Canaveral area<br>
                    <sup>3</sup> Most common in Central/Southern IRL<br/>
                    <sup>4</sup> Found from Cape Canaveral to Ft. Pierce Inlet; to the south is replaced with tropical
                    shrubs and trees

                    <br/>
                    <br/>
                <p class="title">Further Reading:</p>
                <p class="body">Austin 1998. Classification of plant communities in south Florida. Internet
                    document.</p>

                <p class="body">Bergen, S. 1994. Characterization of
                    fragmentation in Florida scrub communities. M.S. thesis. Dept. Bio. Sci.,
                    Florida Institute of Tech., Melbourne, FL.</p>

                <p class="body">Carter, R.W.G., T.G.F. Curtis, and M.J.
                    Sheehy-Skeffington. 1992. Coastal dunes geomorphology, ecology and
                    management for conservation. A.A.<br>
                    Balkema/Rotterdam/Brookfield.</p>

                <p class="body">Chambliss K., D.D. Hott, and M.H.
                    Slotkin. 1998. Public Goods, Biodiversity, and Municipal Land
                    Acquisstion: Reflections of the Environmentally Endangered Lands (EEL)
                    Program in Brevard County, Florida. Presented at 23rd Annual Conference Association of Private
                    Enterprise Education, Dallas, Texas 11 pp. </p>

                <p class="body">Fernald, R.T. 1989. Coastal xeric
                    scrub communities of the Treasure Coast Region, Florida: A summary of
                    their distribution and ecology, with guidelines for their preservation
                    and management. Florida Game and Fresh Water Fish Comm. Nongame Wildlife
                    Pgm. Tech. Rep. No. 6. Tallahassee, FL. 113 pp.</p>

                <p class="body">Florida Natural Areas Inventory,
                    Department of Natural Resources. 1990. Guide to the Natural Communities
                    of Florida. Publication. 11pp. Tallahassee, FL.</p>

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
                    formation through nearshore aggradation -<br>
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

                <p class="body">Robinson, Tami L., and Lisa H. Smith.
                    1996. Regional conservation of the imperiled scrub ecosystem in Brevard
                    County, Florida. Brevard County Parks and Recreation Department,
                    Environmentally Endangered Lands Program, Viera, FL. Internet document
                    available at: <a href="http://www.brevardparks.com/eel/scb/index">www.brevardparks.com/eel/scb/index.htm</a>
                </p>

                <p class="body">Schmalzer, P.A. 1995. Biodiversity of
                    saline and brackish marshes of the Indian River Lagoon: historic and
                    current patterns. Bulletin of Marine Science 57(1): 37-48</p>

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

                <p class="body">Swain, H., P. A. Schamlzer, D. R.
                    Breininger, K. Root, S. Boyle, S. Bergen, S.<br>
                    MacCaffree. 1995. Appendix
                    B Biological Consultant's Report. Brevard County Scrub Conservation
                    and Development Plan. Dept. Bio. Sci., Florida Institute of Technology.,
                    Melbourne, FL.</p>

                <p class="body">Tyndall, R.W. 1985. Role of seed
                    burial, salt spray, and soil moisture deficit in plant distribution on
                    the North Carolina Outer Banks. Ph.D. Thesis, University of Maryland,
                    College Park, MD.</p>

                <p class="body">Wagner, R.H. 1964. The ecology of <i>Uniola
                        paniculata</i> L. in the dune-strand habitat of North Carolina. Ecol.
                    Monogr. 34: 79 - 96.</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="footer_note">Report by: K. Hill, Smithsonian Marine Station<br>
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
