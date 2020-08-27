<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$foredunePlantsArr = $IRLManager->getChecklistTaxa(3);
$backdunePlantsArr = $IRLManager->getChecklistTaxa(4);
$duneAnimalsArr = $IRLManager->getChecklistTaxa(5);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Dune Habitats</title>
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
    <h2>Dune Habitats</h2>
    <table style="width:700px;margin-left:auto;margin-right:auto;" cellpadding="5" cellspacing="3">
        <tr>
            <td>
                <center><img border="0" src="../content/imglib/Dune4.jpg" hspace="20" vspace="5" width="445"
                             height="108"></center>
            </td>
        </tr>
    </table>
    <br/>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Dune Formation:</p>

                <p class="body">On virtually any barrier island, wind and sand combine to create sand dunes.
                    Dunes play a vital role in protecting coastlines and property. They act as
                    buffers against severe storms, protecting the lands beyond the dune from salt
                    water intrusion, high wind and storm surges. Dunes also act as sand reservoirs,
                    which are important for replenishing coastlines after tropical storms,
                    hurricanes, intense wave action, or other erosional events.</p>

                <p class="body">The process of dune formation begins with the transport
                    of sand landward via saltation, surface creep, or suspension. Saltation occurs
                    when medium sized grains of sand are transported up the slope of a beach as the
                    result of winds blowing them along the beach surface. Surface creep is the
                    movement of larger sized grains that are rolled along the beach surface due to
                    collisions with bouncing mid-sized sand grains as they are blown up the beach
                    during the saltation process.</p>
                <p class="body">Perhaps the most common transport process is
                    suspension, in which small sand grains are picked up by winds and blown landward
                    in onshore breezes. This sand is deposited on the upper beach when the flow of
                    wind is impeded by some obstruction (plants, driftwood, flotsam, etc.) that
                    causes the wind to lose speed and momentum. Suspended sand grains then fall out
                    of the air and are deposited on the slip face, or lee side of the obstruction. Over time, sand
                    builds up behind
                    obstructions, creating a series of elongate, elevated spits of sand called wind
                    shadows, which lie at right angles to the shoreline. As they increase in size,
                    wind shadows present an even larger barrier to wind, thus more and more sand
                    accretes quickly. Plants are able to colonize wind shadow areas because they are
                    significantly more stable than other areas on the beach. As plants begin to
                    grow, their roots then assist in further stabilizing and anchoring deposited
                    sands. Later, as more plants colonize the upper beach, wind shadows are joined
                    together laterally to form dunes which lie parallel to the shoreline.</p>

                <center>
                    <img border="0" src="../content/imglib/Dune2.gif" hspace="3" vspace="3" width="295"
                         height="120"><br>
                    <span class="caption">Profile of a coastal dune system showing 1)
              stable backdune;&nbsp;&nbsp;<br>
              2) secondary dunes; 3) primary dune; 4) foreshore.&nbsp;</span><br/>
                </center>

                <br/>
                <p class="body">Depending on local wind and wave patterns, a single
                    dune, or a system of dunes may be created over time. Within dune systems, which
                    resemble a series of low peaks and valleys, the first dune above the intertidal
                    zone is called the primary dune. This is the area of active colonization by
                    plants, and the area most affected by waves and heavy winds. Over the crest of
                    the primary dune is the swale: a low, somewhat wet area that separates primary
                    dunes from secondary dunes. In swales, winds generally scour the sand nearly
                    down to the water table, and plant communities may consist of more freshwater
                    species that show some salinity tolerance.</p>
                <p class="body">It is in the shelter of swales that
                    scrub communities and maritime forests first become established. In many dune
                    systems, secondary dunes are also observed. These dunes form when severe storms
                    breach primary dunes and deposit sand further inland. Deposition of sand onto
                    secondary dunes also occurs as winds blow fine-grained sand inland over the
                    primary dune to secondary dunes. Due to their relative stability over time, and
                    because they are generally protected by primary dunes, secondary dunes support a
                    significantly broader variety of vegetation than primary dunes.</p>

                <p class="title">Dune Plants:</p><br/>
                <p class="body">Vegetation colonizing the upper beach and
                    foredune must be well adapted to periodic disturbance, and is generally
                    characterized by the presence of salt-adapted, grassy species. Growth of these
                    colonizing species must keep pace with the rate of sand build-up along the
                    foredune if the plants are to survive. On the foredune, beach pioneers such as
                    railroad vine (<i>Ipomoea pes-caprae</i>) and shoreline sea purslane (<i>Sesuvium
                        portulacastrum</i>) meet the primary species of dune colonizers. South of Cape
                    Hatteras, sea oats (<i>Uniola paniculata</i>), a coarse grass that grows
                    as tall as 6 feet and spreads laterally via rhizomes is the principal dune
                    colonizer (Stalter 1993). Sea oats and 2 other dune-building species, bitter
                    panic grass (<i>Panicum amarum</i>) and beach cordgrass (<i>Spartina
                        patens</i>), have growth patterns in which upward growth is actually stimulated
                    by burial in sand.</p>
                <p class="body">Subsequent lateral growth in these plants allows for the
                    construction and stabilization of a continuous dune ridge. Other plant species
                    that colonize foredunes must be able to grow at a relatively fast rate to
                    prevent their burial in sand (Wagner 1964; Oertel and Lassen 1976; Myers and
                    Ewel 1990).></p>
                <p class="body">The dune crest is the area where herbaceous vines and
                    grasses begin to be replaced by shrubby or woody species. Common herbaceous
                    plants of the dune crest include sea ox-eye daisy (<i>Borrichia
                        frutescens</i>), beach sunflower (<i>Helianthus debilis</i>), firewheel (<i>Gaillardia
                        pulchella</i>), and annual phlox (<i>Phlox drummondii</i>). Also common on dune
                    crests are several woody species including sea grape (<i>Coccoloba uvifera</i>),
                    saw palmetto (<i>Serenoa repens</i>), and the invasive Brazilian pepper (<i>Schinus
                        terebinthifolius</i>).</p>
                <p class="body">Many of the woody species growing on dune crests are
                    often observed to be low-growing and shrubby, despite their growing as robust
                    shrubs or trees in areas inland of the dunes. Much of the reason for this growth
                    habit is due to the well-drained, low nutrient soils of dunes, as well as to the
                    effects of high winds and salt spray. Though most grasses and vines found on
                    dune crests are well adapted to saline conditions, the tender terminal buds of
                    many trees and shrubs growing on dune crests and in swales are killed upon
                    contact with salt spray, resulting in the salt-pruned, windswept canopies
                    commonly seen in the low, stunted trees of Florida's dune communities.</p>
                <p class="body">Swales located between dunes gain an increased measure
                    of protection from winds and salt spray as the dune system builds over time.
                    Because swales can be scoured down nearly to the water table, they are able to
                    support freshwater plants, though most plants that grow in swales have some
                    degrees of salinity tolerance as well. Stands of sea grape (<i>Coccoloba uvifera</i>),
                    saw palmetto (<i>Serenoa repens</i>), and the <a href="#invasive"
                                                                     onClick="popDef('non-native', '#invasive');"
                                                                     name="invasive">invasive</a> Brazilian pepper (<i>Schinus
                        terebinthifolius</i>) are common woody species on dune crests and in swales.</p>
                <p class="body">Backdunes and secondary dunes generally support a wider
                    variety of vegetation than do foredunes. Additionally, the same species that
                    grow as low shrubs or stunted trees on dune crests, grow in backdune areas as
                    well; though in these more protected locales, they are often able to attain full
                    height. Saw palmetto (<i>Serenoa repens</i>), cabbage palm (<i>Sabal palmetto</i>),
                    live oak (<i>Quercus virginiana</i>), and prickly pear cactus (<i>Opuntia
                        stricta</i>), are all common inhabitants of backdunes and secondary dunes.</p>
                <p class="title">Dune animals:</p>
                <p class="body">A number rodents, some of which are becoming
                    increasingly rare, utilize dune habitats. The threatened southeastern beach
                    mouse (<i>Peromyscus polionotus niveiventris</i>) can be found in disjunct
                    populations from Cape Canaveral to Sebastian Inlet. Other rodents that inhabit
                    dunes include the cotton mouse (<i>Peromyscus gossypinus palmarius</i>), cotton
                    rat (<i>Sigmodon hispidus littoralis</i> ), and rice rat<i> (Oryzomys palustris)</i>.&nbsp;&nbsp;
                    Rabbits, including the eastern cottontail rabbit (<i>Sylvilagus floridanus</i>), and the marsh
                    rabbit
                    (<i>Sylvilagus palustris paludicola</i>), are also observed on dunes. Several other mammals such as
                    gray
                    foxes (<i>Urocyon cinereoargenteus</i>), raccoons (<i>Procyon lotor</i>), feral
                    pigs (<i>Sus scrofa</i>), and feral cats (<i>Felis catus</i>) also use dunes for
                    feeding.</p>
                <p class="body">Many species of shorebirds utilize dunes for feeding;
                    and several species also nest in dune habitats. Among the nesting species are
                    the willet (<i>Catoptrophorus semipalmatus</i>), American oystercatcher (<i>Haematopus
                        palliatus), </i>and Wilson's plover (<i>Charadrius wilsonia</i>), which prefer
                    nest sites in dune areas with sparse grass or herbaceous cover. The laughing
                    gull (<i>Larus atricilla</i>), Caspian tern (<i>Sterna caspia</i>), and the
                    gull-billed tern (<i>Sterna nilotica</i>) also nest in dunes, but prefer
                    areas with somewhat more dense coverage.</p>

                <p class="body">Reptiles are also common inhabitants of dunes. Several
                    species of anoles, among them the green anole (<i>Anolis carolinensis</i>), and
                    the brown anole (<i>Anolis sagrei</i>), are quite common. Gopher
                    tortoises (<i>Gopherus polyphemus</i>), while not plentiful, can often be
                    observed in stable backdune areas. Many different types of snakes also live and
                    feed in dune systems. Eastern diamondback rattlesnakes (<i>Crotalus adamanteus</i>),
                    yellow rat snakes (<i>Elaphe obsoleta quadrivittata</i>), eastern coachwhip
                    snakes (<i>Masticophis flagellum</i>), Florida rough green snakes (<i>Opheodrys
                        aestivus carinatus</i>), and coastal dunes crowned snakes (<i>Tantilla relicta
                        pamlica</i>) all utilize grassy dunes or more woody areas of backdunes as
                    habitat.</p>

                <p class="title">Human Impacts:</p>
                <p class="body">In spite of the stabilizing ability of dune
                    plants, dunes are highly susceptible to human impacts. Vehicles traversing
                    beaches, as well as heavy foot traffic, damage vegetation by shifting sand and
                    roots, thus destabilizing the dune community. Coastal development can also
                    impact the natural process of dune replenishment by adversely influencing
                    natural erosion patterns.</p>

                <p class="title">Select a highlighted link below to learn more about that species:</p>

                <table border="0" class="table-border no-border alternate">
                    <tr>
                        <th>Species Name</th>
                        <th>Common Name</th>
                        <th>Habitat Usage</th>
                        <th>Special Status</th>
                    </tr>
                    <?php
                    if($foredunePlantsArr){
                        ?>
                        <tr class="heading">
                            <td><p class="label">Foredune Plants:</p></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                        foreach($foredunePlantsArr as $id => $taxArr){
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
                            echo '<td><span>'.$taxArr['notes'].'</span></td>';
                            echo '</tr>';
                        }
                    }
                    if($backdunePlantsArr){
                        ?>
                        <tr class="heading">
                            <td><p class="label">Backdune Plants:</p></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                        foreach($backdunePlantsArr as $id => $taxArr){
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
                            echo '<td><span>'.$taxArr['notes'].'</span></td>';
                            echo '</tr>';
                        }
                    }
                    if($duneAnimalsArr){
                        ?>
                        <tr class="heading">
                            <td><p class="label">Dune Animals:</p></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                        foreach($duneAnimalsArr as $id => $taxArr){
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
                            echo '<td><span>'.$taxArr['notes'].'</span></td>';
                            echo '</tr>';
                        }
                    }
                    ?>
                </table>

                <p class="body"><sup>1</sup> Found throughout the IRL<br/>
                    <sup>2 </sup> Most common in Northern IRL in Cape Canaveral area</td>
        </tr>
    </table>
    <table style="width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="title">Further Reading:</p>
                <p class="body">Austin
                    1998. Classification of plant communities in south Florida. Internet
                    document.
                <p class="body">Carter, R.W.G., T.G.F. Curtis, and M.J.
                    Sheehy-Skeffington. 1992. Coastal dunes<br>
                    &nbsp;&nbsp;&nbsp; geomorphology, ecology and
                    management for conservation. A.A.<br>
                    &nbsp;&nbsp;&nbsp; Balkema/Rotterdam/Brookfield.</p>
                <p class="body">Florida Natural Areas Inventory, Department of Natural Resources. 1990. Guide to the<br>
                    &nbsp;&nbsp;&nbsp; Natural Communities of Florida.
                    Publication. 11pp. Tallahassee, FL.</p>
                <p class="body">Komar, P.D. and Moore, J.R., editors.
                    1983. CRC handbook of coastal processes and<br>
                    &nbsp;&nbsp;&nbsp; erosion. CRC Press, Inc.
                    Boca Raton, Florida.</p>
                <p class="body">Komar, P.D. 1998. Beach processes and
                    sedimentation, 2<sup>nd</sup> edition. Prentice Hall,<br>
                    &nbsp;&nbsp;&nbsp; Upper Saddle
                    River, New Jersey.</p>
                <p class="body">Myers, R.L. and J.J. Ewel, eds. 1990.
                    Ecosystems of Florida. University of Central<br>
                    &nbsp;&nbsp;&nbsp; Florida Press, Orlando, FL.
                    765 pp.</p>
                <p class="body">Oertel, G.F. and M. Lassen. 1976.
                    Developmental sequences in Georgia coastal dunes<br>
                    &nbsp;&nbsp;&nbsp; and distribution of
                    dune plants. Bull. GA. Acad. Sci. 34: 35 - 48.</p>
                <p class="body">Otvos, E.G. 1981. Barrier island
                    formation through nearshore aggradation -<br>
                    &nbsp;&nbsp;&nbsp; stratigraphic and field
                    evidence. Mar. Geol. 43:195-243.</p>
                <p class="body">Packham, J.R. and A.J. Willis. 1997.
                    Ecology of dunes, salt marsh and shingle.<br>
                    &nbsp;&nbsp;&nbsp; Chapman and Hall, London.</p>
                <p class="body">Pethick, J. 1984. An introduction to
                    coastal geomorphology. Edward Arnold, London.<br>
                    <br>
                    Pilkey, O.H. and M.E. Feld. 1972.
                    Onshore transport of continental shelf sediment:<br>
                    &nbsp;&nbsp;&nbsp; Atlantic southeastern
                    United States. In: Swift, D.J.P., D.B. Duane and O.H. Pilkey,<br>
                    &nbsp;&nbsp;&nbsp; eds. Shelf
                    Sediment Transport: Process and Pattern. Dowden, Hutchinson, Ross.<br>
                    &nbsp;&nbsp;&nbsp; Stroudsburg, PA</p>
                <p class="body">Schmalzer, P.A. 1995. Biodiversity of
                    saline and brackish marshes of the Indian River<br>
                    &nbsp;&nbsp;&nbsp; Lagoon: historic and
                    current patterns. Bulletin of Marine Science 57(1): 37-48</p>
                <p class="body">Schmalzer, P.A., B.W. Duncan, V.L.
                    Larson, S. Boyle, and M. Gimond. 1996.<br>
                    &nbsp;&nbsp;&nbsp; Reconstructing historic
                    landscapes of the Indian River Lagoon. Proceedings of<br>
                    &nbsp;&nbsp;&nbsp; Eco-Informa '96.
                    11:849 - 854. Global Networks for Environmental Information,<br>
                    &nbsp;&nbsp;&nbsp; Environmental Research Institute of Michigan (ERIM), Ann Arbor, MI</p>
                <p class="body">Stalter, R. 1976. Factors affecting
                    vegetational zonation on coastal dunes, Georgetown<br>
                    &nbsp;&nbsp;&nbsp; County, SC. In: R.R.
                    Lewis, and D.P. Cole, eds. 3<sup>rd</sup> Proc. Annu. Conf. Restoring<br>
                    &nbsp;&nbsp;&nbsp; Coastal Veg. Fla. Hillsborough Comm. Coll., Tampa, FL</p>
                <p class="body">Stalter, R. 1993. Dry coastal
                    ecosystems of the eastern United States of America. In:<br>
                    &nbsp;&nbsp;&nbsp; Ecosystems of
                    the World. Volume 2. Elsevier Science Publications, New York, NY.</p>
                <p class="body">Tyndall, R.W. 1985. Role of seed
                    burial, salt spray, and soil moisture deficit in plant<br>
                    &nbsp;&nbsp;&nbsp; distribution on
                    the North Carolina Outer Banks. Ph.D. Thesis, University of<br>
                    &nbsp;&nbsp;&nbsp; Maryland,
                    College Park, MD.</p>
                <p class="body">Wagner, R.H. 1964. The ecology of <i>Uniola
                        paniculata</i> L. in the dune-strand habitat of<br>
                    &nbsp;&nbsp;&nbsp; North Carolina. Ecol.
                    Monogr. 34: 79 - 96.</p>
                <p>&nbsp;
                <p class="footer_note">
                    Report by: K. Hill, Smithsonian Marine Station<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
