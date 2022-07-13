<?php
include_once(__DIR__ . '/../config/symbbase.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Marine Invertebrates</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <table style="border:1px;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/MI.jpg" width="550" height="174"></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">It is estimated that 97% of all animal species on earth are invertebrates. The vast
                    majority of animals that live in the ocean also fall into this category. Invertebrates are animals
                    without a backbone (vertebral column). <br/>
                    Aside from lacking a backbone, invertebrates share many fundamental traits with other animals. They
                    are multi-cellular, have no cell walls (a characteristic of plants), most have tissues (organized
                    groups of cells), and all are heterotrophic, i.e., they cannot manufacture their own food and depend
                    on other organisms for nutrition. <br/>
                    Three groups of marine invertebrates that most of us will have seen but many of us will know little
                    about are the sponges, the cnidarians and the echinoderms. Let&rsquo;s learn more about these
                    diverse, often colorful and always ecologically important animals.
                </p>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:1px;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <img src="../content/imglib/sponge500.jpg" width="500" height="125"><br/>
                <span class="caption">
                            Sponges probably provided the first non-edible product derived from the ocean, and are a fascinating group of primitive animals. They have played a role in shaping the economic and cultural history of several Florida coastal communities, their natural products are investigated for pharmacological potential, and they perform essential functions in Florida ecosystems.
                        </span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">By the mid 1800's, Florida sponges comprised a substantial portion of the worldwide
                    sponge market, and were harvested initially in the Keys. The harvest migrated north along the Gulf
                    coast to Tarpon Springs, FL, where descendents of pioneering Greek sponge fishermen still harvest
                    sponges today. Although the sponge market has declined from historical highs, over 60,000 lbs of
                    sponge are still harvested from Florida waters annually.</p>
                <p class="body">Heralding their value in the pharmaceutical industry, one of the first anti-cancer
                    drugs, cytosine arabinoside, was isolated from the sponge, Tectitethya crypta. In addition to direct
                    services to people, sponges filter enormous quantities of water and provide habitat for a host of
                    invertebrates, small fish and bacteria. It is not uncommon to find thousands of small crustaceans,
                    e.g., shrimp and amphipods, living within one sponge. Sponge communities also provide cryptic
                    habitat (hiding places) for many larger marine creatures, including Florida's commercially valuable
                    spiny lobster.</p>

                The Indian River Lagoon (IRL) Species Inventory documents fourteen species of sponges that occur in the
                IRL. These species are:
            </td>
        </tr>
    </table>
    <table style="border:0;width:469px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><span class="captioncenter"><img src="../content/imglib/Table469.jpg" width="469"
                                                                height="134" align="middle"></span></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Sponge Taxonomy</p>
                <p class="body">All sponges belong to the phylum Porifera (&ldquo;pore bearing&rdquo;), and are divided
                    into classes based on the composition of their &ldquo;skeletal&rdquo; material. All species of
                    sponges occurring in the Indian River Lagoon belong to the class Demospongia, which is discussed
                    below.</p>
                <p class="body">Sponge morphology and color are not reliable indicators of taxonomic affiliations
                    because these characteristics are plastic and readily influenced by environmental variables. The
                    question often will arise, are two different looking but closely related sponges different species
                    or ecotypes of the same species? Sponge taxonomists rely more heavily on the composition and
                    morphology of a sponge&rsquo;s skeletal elements, particularly spicules, for species identification.
                    Nevertheless, numerous discrepancies still exist in today&rsquo;s sponge taxonomy. Relatively recent
                    genetic approaches using biomolecular techniques to identify signature sequences in the DNA of
                    sponges (i.e., barcoding) have proven useful (http://www.spongebarcoding.org/). Perhaps, these
                    techniques along with more traditional approaches will hold the key to more definitive answers about
                    sponge taxonomy.</p>
                <br>
                <b>The major classes of sponges are:</b></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Class Calcarea:</p>
                <p>Sponges in this class are relatively small, tubular or vase shaped with spicules composed of calcium
                    carbonate. Their diversity is greatest in shallow, marine tropical water</p>

                <p class="title">Class Hexactinellida:</p>
                <p class="body">Referred to as the &ldquo;glass sponges,&rdquo; sponges in this class are characterized
                    by fused, silica spicules forming elaborate networks. They are thought to be an early evolutionary
                    branch within the Porifera</p>
                <p class="title">Class Demospongia:</p>
                <p class="body">Ninety percent of all living sponges belong to this class. Spicules, when present, are
                    siliceous. All are leuconoid (see below). Many of them are harvested for the bath industry.
                    Non-synthetic bath sponges are composed of spongin material left after other elements (e.g.,
                    spicules) have been removed. </p>
                <p class="title">Class Sclerospogia:</p>
                <p class="body">Represent a small group of sponges that live in shaded, dark, environments. They
                    resemble corals in that they secrete massive calcareous skeletons and are thought to have preceded
                    corals as the original reef formers. Their skeletons have siliceous spicules and spongin on a thick
                    basal layer of calcium carbonate.</p>
                <p class="body">The following website, <a
                            href="http://www.nova.edu/ncri/sofla_sponge_guide/species_class.html">http://www.nova.edu/ncri/sofla_sponge_guide/species_class.html</a>
                    presents an interactive guide to the identification of South Florida sponges using diagnostic
                    characteristics such as external form, spicules, fibers and skeletal architecture.</p>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/SPONGE5_500X612.jpg" width="500" height="582"><br/><span class="caption">Five sponges beloning to the class Dermospongia: A. Halichodria melanodocia; B. a member of the family Niphatidae; C. Chondrilla nucula; D. Hymeniacidon sp; E. Tedania ignis. Photos courtesy of John Reed.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Sponge Structure and Function</p>
                <p class="body">Sponges are sessile (i.e., permanently attached or unable to move), mostly marine
                    animals lacking a tissue level of organization. They exhibit a wide variety of sizes and shapes from
                    encrusting sheets to elaborate upright forms. Their morphology or shape often is determined by
                    environmental conditions (i.e., substratum or water currents).<br/>
                    The body plan of all sponges, regardless of shape, is built around a system of water canals that
                    enable the sponge to feed, exchange gases and reproduce. The body plan of sponges occurs in three
                    basic forms:<br/>
                    <br>
                    Asconoid - simple and tubular body plan characteristic of small sponges.<br/>
                    <br>
                    Syconoid &ndash; body wall folded to form internal pockets.</p>
                <p class="body">Leuconoid &ndash;complex invagination of body pockets that increases the efficiency of
                    water movement. The largest species of sponge have this body type.</p>
                <p class="body">Incoming water provides nutrients and oxygen to the sponge as it is circulated, and
                    water eliminated through the osculum carries away wastes. An animated illustration of water
                    circulation in sponges can be found at
                    www.biology.ualberta.ca/courses.hp/zool250/animations/Porifera.swf.</p>
                <p class="body"><b>The major components of a sponge and its water canal system are:</b></p>
                <p class="body">Ostium (Ostia plural) &ndash; tiny pores (holes) covering the sponge body where water
                    enters.<br/>
                    <br>
                    Spongocoel &ndash; spacious cavity(ies) into which water flows.</p>
                <p class="body">Osculum (Oscula plural) &ndash; large opening where water exits the sponge.</p>
                <p class="body">Mesohyl (mesenchyme) &ndash; a proteinaceous matrix containing spicules, spongin, and
                    some cells that is located between the exterior layer of pinocytes and the interior choanocytes.</p>
                <p class="body">Spiicules &ndash; can be siliceous or calcareous and of various morphologies or shapes,
                    including simple rods (monaxons) to more complex forms with three (triaxons), four (tetraxons), or
                    more (polyaxons) axes. Spicules, along with spongin, provide structural support, and are considered
                    to be an anti-predator mechanism. They are also a helpful diagnostic tool for sponge
                    identification.</p>
                <p class="body">Spongin &ndash; a collagenous, fibrous protein that, along with the spicules, forms the
                    &ldquo;skeleton&rdquo; of most sponges.</p>
                <p class="title">Sponge Cells</p>
                <p class="body">Pinocyte &ndash; forms the exterior layer of the sponge body.</p>
                <p class="body">Choanocyte (collar cell) &ndash; lines the spongocoel and other chambers where its
                    single flagellum beats in coordination with other choanocytes to circulate water and its collar of
                    hairlike projections filters food from the passing water.</p>
                <p class="body">Archaeocyte (amoeboid cell) &ndash;found in the mesohyl where it moves throughout the
                    sponge&rsquo;s body and is capable of becoming other types of specialized cells as needed (that is
                    archaeocytes are totipotent). Archaeocytes serve a variety of functions from engulfing large food
                    particles to transporting nutrients, and in some sponges, they play a pivotal role in
                    reproduction.</p>

                <p class="title">Sponge Nutrition</p>
                <p class="body">Most of the food utilized by a sponge is trapped by the choanocytes (collar cells).
                    Water pumped by the choanocytes through the sponge body contains suspended material such as
                    microplankotn and bacteria as well as dissolved organic matter. As this food-laden water passes
                    through the sponge, food particles are filtered from the water by the choanocytes and ultimately
                    transferred into the sponge mesohyl. Very small food particles are trapped by the projections in the
                    collar cells. In the mesohyl, larger food particles are phagocytized by specialized amoebocytes,
                    digested and the nutritional material is transferred to other areas of the sponge.</p>

                <p class="title">Reproduction in Sponges</p>
                <p class="body">Sponges reproduce both asexually and sexually. Asexual reproduction is accomplished
                    either through fragmentation or, more rarely, through a process called budding. Fragmentation occurs
                    when a piece of sponge is mechanically broken from the adult and regenerates into a new individual.
                    Budding occurs when a group of cells forms on the exterior of the sponge, attains a certain size,
                    and then drops off. The new sponge may settle near the &ldquo;parent&rdquo; sponge or be carried
                    away in the current and settle elsewhere if/when suitable substratum is found. A few marine sponges,
                    when dying, are capable of producing gemmules that can become dormant and survive adverse
                    environmental conditions. In sexual reproduction, male gametes, i.e., sperm, are released into the
                    water column (http://www.ucmp.berkeley.edu/porifera/poriferalh.html) to be captured by specialized
                    choanocytes of another individual. These choanocytes become motile, metamorphose into amoeboid
                    cells, and transport sperm through the mesohyl where they can be engulfed by eggs. Most fertilized
                    eggs remain within the adult until they hatch into planktonic larvae that are released into the
                    water column through the osculum. Most sponges are hermaphroditic, producing both male and female
                    gametes, but some are dioecious and only produce either male or female gametes.</p>

                <p class="title">Microbial and Chemical Ecology of Sponges</p>
                <p class="body">Sponges have been found to contain a vast array of microbial organisms including
                    bacteria, cyanobacteria, archaea and unicellular protists, such as diatoms and dinoflagellates.
                    These microorganisms often are abundant in the mesohyl, and can form mutualistic relationships with
                    the sponge. They utilize the sponge for habitat but, in return, provide the host with a variety of
                    benefits, including a source of nutrition and benefits derived from their metabolic functions, such
                    a nitrification, photosynthesis, anaerobic metabolism and secondary metabolite production. Some of
                    these microbes, however, can be pathogenic or parasitic. Teasing apart the extent and exact nature
                    of these sponge-microbial associations is often difficult. For example, some secondary metabolites,
                    initially thought to be isolated from sponges, were later shown to be products of associated
                    microflora.</p>
                <p class="body">
                    The pharmacological potential of natural products isolated from sponges and other organisms is one
                    of the most actively pursued areas of marine chemical ecology. It is estimated that 70% of small
                    molecule drugs produced between 1981 and 2006 have a link to natural products. These natural
                    products, generally thought of as secondary metabolites, are chemicals produced by an organism with
                    no apparent role in primary metabolic functions, i.e., compounds not essential to growth and
                    development. Yet many of these compounds have been shown to be biologically active, with
                    anti-bacterial, anti-viral, anti-cancer, and anti-inflammatory properties. These compounds often are
                    produced in response to environmental stress. Why are sponges such a rich source of these compounds?
                    The answer most likely can be attributed to the sessile lifestyle of the sponge
                    (www.carsten-thoms.net/sponges/ecology/1_frames.html). Keep in mind that these soft-bodied
                    invertebrates cannot flee from predators, are unlikely to fully camouflage themselves, need to
                    compete aggressively for space to attach and grow, and must keep themselves free of biofouling
                    organisms that block water flow. It is not surprising that the highest incidence of secondary
                    metabolites is found in those sponge species occurring in coral reef environments, where competition
                    for space and feeding by carnivorous fish are intense. Hence, these sponges have developed an
                    arsenal of chemical weaponry, with extremely potent secondary metabolites that enable the sponge to
                    survive and compete in such an environment.</p>
            </td>
        </tr>
    </table>
    <br/><br/>
    <table style="border:0;width:400px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/cnid500.jpg" width="488" height="134" align="middle"></td>
        </tr>
    </table>
    <br/>
    <p style="width:700px;margin-left:auto;margin-right:auto;" class="body">Cnidarians (Phylum Cnidaria) are a diverse
        group of animals ranging from feathery, inconspicuous hydroids growing on pier pilings to large, majestic
        tropical reef-forming corals. Cnidarians are considered to be more &ldquo;advanced&rdquo; than the Porifera
        (sponges) because they have developed a tissue level of organization (although they lack organs) and they
        possess two distinct embryonic germ layers: the external ectoderm and the internal endoderm lining the gut. The
        gelatinous mesoglea occurs between these layers.</p>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">
                    The cnidocyte or stinging cell and radial symmetry are two characteristics common to cnidarians.
                    Many cnidarians also display two distinct body forms during their life cycle: the benthic,
                    cylindrical polyp and the free-floating, medusoid stage. Cnidocytes (stinging cells) enable
                    cnidarians to capture prey, and they also are used for defense. Housed within each cnidocyte is an
                    organelle called the nematocyst or cnidocyst. The nematocyst is a fluid filled capsule containing a
                    coiled, thread-like structure (tubule) that can be sticky, spiny, and/or elongated. This tubule can
                    be ejected quickly, i.e., within fractions of a second, when the external triggering mechanism, the
                    cnidocil, is stimulated tactilely or chemically. Perhaps the most venomous of nematocysts occur in
                    box jellyfish (Class: Cubozoa) common to Australian waters, e.g., the &ldquo;sea wasp&rdquo; <em>Chironex
                        fleckeri</em>, is considered to be the most toxic marine animal known. More familiar to
                    Floridians is another infamous cnidarian with potent nematocysts &ndash; the Portuguese man o&rsquo;
                    war, <em>Physalia physalis</em> (Class: Hydrozoa, Order: Siphonophora). In contrast, some
                    cnidarians, such as <em>Aurelia</em>, rely more heavily on mucus covering their bells to capture
                    prey items, and consequently, their nematocysts are much less potent.</p>
                <p class="body">
                    The bodies of Cnidarians exhibit radial symmetry, which means similar parts of the animal&rsquo;s
                    body are arranged and repeated around a central axis, i.e., the animal looks the same from all
                    sides. Radial symmetry often is seen in slow-moving or sessile animals, with one advantage being the
                    ability to reach out and respond to external stimuli (positive or negative) coming from all
                    directions.</p>
                <p class="body">
                    During the life cycle of most cnidarians, their body form alternates between polyp and medusa, with
                    one form often being dominant and more conspicuous than the other. In some cnidarians, however,
                    either the polyp or medusoid stage is absent altogether. The cylindrical polyp is sessile and
                    sac-like, with the aboral end attached by a holdfast organ. The free-floating, planktonic medusa can
                    be thought of as a bell-like, upside down polyp with tentacles located around the perimeter of the
                    bell. </p></td>
        </tr>
    </table>
    <center>
        <table width="350" border="1" align="center">
            <tr>
                <td><img src="../content/imglib/Anemonia bermudensis350.jpg" width="350" height="375"><br/><span
                            class="caption">Sea anemone,<em> Actinia bermudensis</em>. Note muscualr polyp attached to rock. Photo courtesy of Clay Cook</span>
                </td>
            </tr>
        </table>
        <br/><br/>
        <table width="350" border="1" align="center">
            <tr>
                <td><img src="../content/imglib/Chrysaora 350Tom Smoyer.jpg" width="350" height="235"><br/>
                    <span class="caption">The Atlantic sea nettle, <em>Chrysaora quinquecirrha</em>, showing medusa with attached tentacles. Photo courtesy of Tom Smoyer.</span>
                </td>
            </tr>
        </table>
    </center>
    <br/><br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">The mesoglea layer of the medusa is often thick and jelly-like. Both stages have a
                    centrally located orifice, i.e., mouth, surrounded by tentacles containing nematocysts that are used
                    to capture prey. The mouth opens into a gastrovascular cavity that is essentially a blind gut where
                    food is digested. Polyps extend their tentacles, particularly at night, and with the aid of the
                    nematocysts, capture prey items passing by in the current.</p>

                <p class="title">Taxonomy</p>
                <p class="body">Cnidarians were formerly placed in the phylum Coelenterata along with the ctenophores
                    (comb jellies). Discoveries of structural differences led taxonomists to create separate phyla, the
                    Cnidaria and the Ctenophora respectively. The phylum Cnidaria consists of four classes: 1) the
                    Hydozoa (diverse group including mostly hydrozoans but also fire coral and the colonial Portuguese
                    man o&rsquo; war); 2) the Scyphozoa (true jellyfish); 3) the Cubozoa (box jellies); and 4) the
                    Anthozoa (sea anemones and corals, among others).</p>

                <p class="title">Class: Hydrozoa:</p>
                <p class="body">
                    Hydrozoans are a complex and diverse group of mostly marine animals, with about 3,500 species
                    occurring worldwide. The Indian River Lagoon Species Inventory lists ten documented species that
                    occur in the IRL. Hydrozoans occur in a variety of shapes and sizes from inconspicuous, feathery or
                    bushy, benthic colonies of tiny, polyps (<em>Obelia bidentata</em> &ndash; double toothed hydroid)
                    to massive colonies resembling stony corals (<em>Millepora</em> spp.) Other hydrozoans, the
                    siphonophores, have developed a pelagic stage that is often confused with true jellyfish (Order:
                    Scyphozoa). <em>Physalia physalis</em> &ndash; Portuguese man o&rsquo; war and <em>Velella
                        velella</em> by-the-wind sailor are examples of these pelagic, tightly integrated, colonial
                    hydrozoans. Because of morphological variation and often complex life histories, hydrozoans are a
                    difficult group to categorize. In fact, there is much discrepancy concerning hydrozoan taxonomy, see
                    http://www.ville-ge.ch/mhng/hydrozoa/ for a current, authoritative, taxonomic source.</p>
                <span class="bemedium036">&nbsp;</span></td>
        </tr>
    </table>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td valign="top"><img src="../content/imglib/Physalia 300Tom Smoyer.jpg" width="300" height="450"><br/><span
                        class="caption">Portuguese man o&rsquo; war, <em>Physalia physalis</em>. <br/>Photo courtesy of Tom Smoyer.</span>
            </td>
            <td valign="top" width="50%"><img src="../content/imglib/Hydroids350.jpg" width="349"
                                              height="228"><br/><span class="caption">Delicate branching hydroid growing on Sargassum. <br/>Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Although hydrozoans show similarities to scyphozoans, they differ in several fundamental
                    respects. For example, most hydrozoans show an alternation between the polyp and medusoid stage
                    during their life cycle similar to that seen in the Scyphozoa. However, in the Hydrozoa, the polyp
                    stage usually predominates. Hydrozoan medusae have a velum, i.e., a thin, muscular ring of tissue
                    along the inner margin of the bell that enhances swimming ability. This structure is lacking in the
                    Scyphozoa. The mesoglea of hydrozoans is acellular, whereas the mesoglea in scyphozoans contains
                    amoeboid-like cells.</p>
                <p class="body">
                    In colonial hydrozoans, reproductive polyps asexually produce minute transparent medusae that
                    release gametes. The fertilized egg develops into a free-swimming planula larva that settles on the
                    bottom and metamorphoses into a polyp that matures into the colony. Polyps of colonial hydrozoans
                    called zooids are specialized for various functions: feeding is accomplished by gastrozooids;
                    dactylozoids provide defense; and gonozooids are used in reproduction.</p>
                <p class="title">Class: Scyphozoa</p>
                <p class="body">Members of the class Scyphozoa are the true jellyfish. They are strictly marine animals
                    and some can consist of up to 98% water. The dominant life history stage of scyphozoans is the
                    relatively large medusoid stage, comprising a gelatinous umbrella-shaped bell that can be contracted
                    for locomotion. The bell is equipped with tentacles used to capture prey.</p>
                <span class="bemedium036">&nbsp;</span></td>
        </tr>
    </table>
    <table style="border:0;width:450px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Stromolophus 350Tom Smoyer.jpg" width="450"><br/><span class="caption">The cannon ball jellyfish, <em>Stomolophus meleagris</em>. Photo courtesy of Tom Smoyer.</span>
            </td>
        </tr>
    </table>
    <p style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="body">In contrast to the tiny hydrozoan
        medusae, bells of scyphozoans are typically several inches in diameter, and in some species, e.g., <em>Cyanea
            capillata</em>, bell diameters can reach up to 6 to 7 feet with tentacles extending hundreds of feet from
        the bell.<br>
        Although scyphozoans are capable of weakly swimming by rhythmic contraction of the bell (facilitated by a ring
        of muscle fibers located in the mesoglea of the bell&rsquo;s rim), they still are considered to be planktonic
        because their swimming is not strong enough to overcome the vagaries of ocean currents. However, many jellyfish
        are capable of detecting bright light via photoreceptors, and they remain low in the water column during bright
        daylight hours and come to the surface at dusk and on cloudy days.</p>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">
                    Using their tentacles embedded with stinging cells, most scyphozoans actively prey upon small
                    crustaceans and fish. In contrast, some jellyfish are considered to be filter feeders that use their
                    tentacles to strain plankton from the water column.</p>
                <p class="body">
                    The mesoglea, complete with amoeboid cells, gives some structural integrity to jellyfish, but it
                    contains no specialized excretory or respiratory structures. The mouth of a scyphozoan medusa opens
                    into a central stomach often elaborated into compartments (i.e., diverticula and radiating canals)
                    to increase surface area for digestion. The lining of the digestive system contains additional
                    cnidocytes, along with cells that secrete digestive enzymes.</p>
                <p class="body">
                    Most jellyfish are gonochoristic (having separate sexes). Although a few scyphozoans brood their
                    young, most release gametes into the water where fertilization takes place. The planula larva is
                    usually a short-lived planktonic stage. After attaching to suitable substratum, the planula
                    metamorphoses into an inconspicuous sessile polyp, the scyphistoma, which then develops into a
                    colony of hydroid polyps, the strobila. The strobila asexually produces ephyra, or young, tiny
                    medusae, that are released into the water column where they mature into adult jellyfish to complete
                    the life cycle.<br>
                </p>
                <p class="title">Class: Cubozoa</p>
                <p class="body">Members of this class include some of the most dangerous marine animals known. They are
                    cube-shaped medusae that appear square when viewed from above (hence the name &ldquo;box jellies&rdquo;).
                    They possess four tentacles (or four bunches of tentacles) armed with stinging cells containing
                    notoriously potent venom that can inflict painful, sometimes fatal injuries to humans. They are
                    essentially transparent making them difficult to see in the water.</p>
                <p class="body">
                    In Australia, the most infamous of the Cubozoa is the largest species in the class, <em>Chironex
                        fleckeri</em>. It has been implicated in numerous fatal encounters with humans. Although the
                    most venomous species of cubozoans are located in the tropical Indo-Pacific, other species can be
                    found in the tropical and subtropical eastern Pacific and Atlantic. One species of cubozoan, <em>Chiropsalmus
                        quadrumanus</em>, has been documented in the Indian River Lagoon, FL.</p>
                <p class="body">
                    At one time, cubozoans were placed in the Class Scyphozoa along with the true jelly fish. However,
                    it was discovered that cubozoans differ in several fundamental respects, most notably because of the
                    presence of the velarium*. This structure is located on the underside of the umbrella and
                    concentrates and increases water flow leaving the bell, which enables cubozoans to swim with
                    relative agility and at faster speeds. Hence, cubozoans are able to swim against currents, are not
                    considered planktonic, and are rarely found washed up on beaches. Cubozoans also are considered to
                    have a more well developed nervous system than most scyphozoans, and they have true eyes, complete
                    with retinas, corneas and lenses.</p>
                <p class="body">
                    Cubozoans are ecologically important members of nearshore ecosystems. Their strong swimming ability,
                    along with their relatively well-developed eyesight enables cubozoans to pursue prey actively, and
                    they feed voraciously on fish, worms and crustaceans.</p>
                <p class="body">
                    Cubozoan planula larvae can develop inside females or be released into the water column, depending
                    on the species. After settlement, they develop into polyps, mature for several months and eventually
                    metamorphose directly into small medusae.</p>
                <p class="body">
                    *The cubozoan velarium differs structurally from the hydrozoan velum, and it is thought to be
                    derived from the scyphozoan-like marginal lobes.<span class="textbody"><br>
                </p>
                <p class="title">Class: Anthozoa</p>
                <p class="body">Many of us have had the privilege to dive or snorkel on a coral reef and have wondered
                    in astonishment at the biodiversity and topographic complexity of these amazing ecosystems. Many of
                    the magnificent reef-dwelling organisms making up the coral reef ecosystem are cnidarians belonging
                    to the class Anthozoa.</p>
                <p class="body">
                    Although there is debate over classification, most agree that the Anthozoa comprise three subclasses
                    (one of which is extinct). The two extant subclasses of anthozoans are the Octocorallia and the
                    Zoantharia. Octocorallia, as the name implies, are characterized by having eight tentacles and are
                    mostly colonial. The subclass includes, among others, soft corals (Order: Alcyonacea) and sea fans
                    (Order: Gorgonacea). Members of the subclass Zoantharia have six tentacles (or multiples thereof),
                    and can be either solitary or colonial. Zoantharians include stony corals (Order: Scleractinia) as
                    well as sea anemones (Order: Actiniaria) and black corals (Order: Antipatharia).</p></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Gorgonia ventalina350.jpg" width="350" height="263"><span class="caption">The purple sea fan, <em>Gorgonia ventalina </em>(Order: Gorgonacea). Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Diploria labyrinthiformis350.jpg" width="350" height="221"><span
                        class="caption">Brain coral, Diploria labyrinthiformis (Order: Scleractinia). Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <p style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="body">Anthozoans take on a variety of
        forms, occupy various habitats and play multiple ecological roles. Soft corals (Alcyonacea) lack the calcium
        carbonate skeleton of stony corals, and although they may be abundant on a reef, they do not contribute to its
        calcium carbonate structure. Sea fans (Gorgonacea) use a small space for attachment, but their elaborate,
        branching, rod-like colonies, with hard, protein skeletons extend out into surrounding waters. They are well
        adapted for exploiting the water column for food. In contrast, stony corals (Scleractinia) secrete hard,
        sometimes massive, calcium carbonate skeletons that along with calcareous algae build reef structure. Reef
        building corals are colonial animals made up of many polyps connected by a thin layer of tissue. Another
        colonial form, black corals (Antipatharia) are found in deep water, often in large tree-like formations. Their
        black or dark brown, hard protein skeletons of are covered with tiny spines. Unlike many other Anthozoans, sea
        anemones (Actiniaria) are solitary, with large muscular polyps that rely on complex mesenteries to digest large
        prey</p>

    <br/>
    <table style="border:1px;width:325px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Actini_bermud_325.jpg" width="328" height="305"><br/><span class="caption">Sea anemone, <em>Actinia bermudensis</em>, showing extended tentacles surrounding oral region. Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <p style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="body">Although corals can be found
        throughout the world&rsquo;s oceans, even in polar waters, reef-building (hermatypic) corals are confined to
        subtropical and tropical waters in the Western Atlantic and Indo-Pacific oceans. They require warm (23&ndash;25&deg;C),
        sunlit waters (rarely found below 200 feet) for optimal growth. They often do best in areas subject to wave
        action where nutrients and oxygen are constantly replenished and wastes are carried away. Florida is the only
        state in the continental U.S. to have established coral reefs occurring near its shoreline. These reefs provide
        extensive habitat, shelter and breeding ground for many species of fish and invertebrates that are commercially
        and recreationally significant.</p>
    <div class="clear"></div>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="body">
                    Coral reefs are one of the most productive, biodiverse ecosystems on earth, but they occur in
                    nutrient poor, tropical waters. They are able to thrive in these areas because of a symbiotic
                    relationship between the coral animal and microscopic dinoflagellates known as zooxanthellae found
                    within the coral polyp&rsquo;s tissue. More than 90% of the coral&rsquo;s nutrition is provided by
                    this mutualistic relationship. The coral animal provides suitable habitat, nutrients such as
                    nitrogen and phosphorus, and carbon dioxide to the micro-algae while the zooxanthellae, in turn,
                    provide the coral with oxygen and nutritious compounds derived from photosynthesis, such as glucose
                    and amino acids. This mutualistic relationship enhances coral growth and deposition of the coral&rsquo;s
                    calcium carbonate skeleton.</p>
                <p class="body">
                    Some corals occurring in deep water, where sunlight does not penetrate, lack symbiotic
                    zooxanthellae, and yet, they can form substantial, productive ecosystems. <em>Oculina</em> reefs
                    found at depths of 250 to 300 feet along Florida&rsquo;s east coast, and nowhere else in the world,
                    are an example. They are built from the delicate ivory tree coral, <em>Oculina varicosa</em>, which
                    can form impressive mounds and pinnacles reaching 100 feet high. Fortunately, these reefs,
                    stretching roughly from Ft. Pierce to Cape Canaveral, are protected from bottom fishing in what is
                    called the Oculina Bank Habitat Area of Particular Concern.</p></td>
        </tr>
    </table>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Oculina varicosa350.jpg" width="350" height="233"><br/><span
                        class="caption">Ivory tree coral, <em>Oculina varicosa</em>. Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Corals can reproduce either asexually through budding or fragmentation or sexually
                    through internal or external fertilization. Most corals are hermaphroditic so they release both eggs
                    and sperm into the water column. Some species are brooders that release sperm into the surrounding
                    water but retain fertilized eggs in their gastrovascular cavities until they develop into larvae.
                    Each planula larva remains in the water column for a few hours to days before settling out on
                    suitable substratum and subsequently metamorphosing into a &ldquo;founder&rdquo; polyp that
                    eventually develops into an adult colonial coral.</p>&nbsp;
            </td>
        </tr>
    </table>
    <table style="border:1px;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/echin500.jpg" width="500" height="121"></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">Echinoderms (Phylum: Echinodermata)</p>
                <p class="body">Echinoderms are probably some of the most familiar, marine invertebrate organisms, and
                    they capture the imagination and fascination of young and old alike. They occur at all depths in the
                    ocean from the intertidal to the abyss. The name echinoderm or &ldquo;spiny skinned&rdquo; refers to
                    the spiny projections on many species. Most adults display pentamerous radial symmetry (radial
                    symmetry based on five parts), although all echinoderm larvae exhibit bilateral symmetry (body
                    divided into similar right and left halves along a central axis). The water vascular system of
                    echinoderms is unique to this phylum, and it consists of a network of water-filled canals that
                    function in feeding, gas exchange and excretion. Many of these canals lead to structures called tube
                    feet that can end with suckers. Groups of tube feet allow echinoderms to either attach to the
                    substratum or move about, and in some species, they are used for feeding. In many echinoderms, the
                    water vascular system connects to the outside through a madreporite, a porous plate on the aboral
                    surface. Echinoderms possess an internal skeleton (endoskeleton) that is made of calcium carbonate
                    plates and covered by a thin layer of ciliated tissue. Spines and tubercles project outward from
                    these plates giving the animal a &ldquo;spiny&rdquo; appearance.</p>
                <p class="body">
                    Echinoderms are represented by four classes: 1) the Asteroidea (sea stars); 2) the Ophiuroidea
                    (brittle stars and basket stars, among others); 3) the Echinoidea (sea urchins and sand dollars,
                    among others); and 4) the Holothuroidea (sea cucumbers).</p>
                <p class="title">Class: Asteroidea</p>
                <p class="body">Sea stars typically consist of a central disk with five radiating arms (rays). Each arm
                    of a sea star contains an ambulacral groove located on the oral surface with tube feet used for
                    locomotion. When water is pumped through the water vascular system into the tube feet, the feet
                    project from the ambulacral groove. The tube feet attach and then shorten via muscular contraction
                    that forces water back into the groove to pull the animal in a specific direction.<br></p></td>
        </tr>
    </table>
    <table style="border:0;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/SeaStars500.jpg" width="500" height="209"><br/><span class="caption">Two sea stars that occur in the Indian River Lagoon: <em>Luidia clathrata</em> (left) and <em>Oreaster reticulatus</em> (right). From Hendler, G <em>et al.</em> 1995. Sea stars, sea urchins, and allies. Smithsonian Institution Press. With permission.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Sea stars are both scavengers and carnivores that can actively pursue prey. The sea star&rsquo;s
                    arms are flexible, and they can wrap around prey items such as a bivalve, and pry it open. The sea
                    star will then evert a portion of its stomach into the bivalve and consume it by secreting digestive
                    enzymes. Other sea stars are capable of swallowing their prey whole to digest it inside their
                    stomachs.</p>
                <p class="body">
                    Sea stars reproduce both sexually and asexually. Sexes are usually separate (dioecious), and eggs
                    and sperm are released into the water column where fertilization takes place resulting in planktonic
                    larvae. The free-swimming ciliated larval stage (bipinnaria) can be either lecithotrophic (yolk
                    feeding) or planktotrophic (plankton feeding). A few species of sea stars will brood their eggs.
                    Asexual reproduction is accomplished when a sea star divides itself into parts along its central
                    disk, i.e., fission, or by autotomy of its arms. Remarkably, the separated parts will then
                    regenerate missing portions of the disk and missing arms. Sea stars can regenerate new arms, and
                    some species can even regenerate an entire individual from one autotomized arm if a portion of the
                    central disk is present.</p>
                <p class="body">
                    A notable exception to the pentamerous (five part) radial symmetry displayed by most echinoderms is
                    the nine-armed sea star, <em>Luidia senegalensis</em>, which occurs in the Indian River Lagoon, FL.
                </p>
                <p class="title">Class: Ophiuroidea</p>
                <p class="body">Brittle stars (also known as serpent stars) and basket stars belong to the class
                    Ophiuroidea. Although brittle stars superficially resemble sea stars because of their pentamerous
                    radial symmetry, the central disk in brittle stars is distinctly delineated from the radiating arms.
                    Brittle star arms are slender, heavily spined, and easily detached from the central disk, hence the
                    name &ldquo;brittle&rdquo; star. When detached, their delicate, whip-like arms can undulate wildly
                    for some time. This behavior is thought to be an avoidance mechanism that distracts potential
                    predators while the somewhat compromised brittle star escapes. Most ophiuroids can autotomize either
                    part or all of an arm and regenerate it. Brittle stars are often cryptic, avoiding light by hiding
                    under rocks and in crevices. Basket stars are usually found in the deep ocean.<br>
                </p>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:500px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/brittlestars500.jpg" width="500" height="241"><br/><span class="caption">Brittle stars: <em>Amphipholis suamata</em> (left) and <em>Ophionereis reticulata</em> (right). Note delineation of the radiating arms from the central disk. From Hendler, G <em>et al.</em> 1995. Sea stars, sea urchins, and allies. Smithsonian Institution Press.&nbsp;With permission.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Ophiuroids differ from asteroids in several ways. Although ophiuroids are equipped with
                    tube feet, they assist mostly in feeding and, to a limited extent, mobility. Their tube feet lack
                    suckers unlike sea stars. Locomotion in brittle stars is accomplished primarily by swift, wave-like
                    movements of the arms. Also unlike asteroids, the central disk in ophiuroids contains all the
                    internal organs. The digestive and reproductive systems never enter the arms.</p>
                <p class="body">
                    Depending on the species, ophiuroids can be scavengers, deposit feeders, filter feeders or
                    carnivores. They tend to burrow in the sediment with their arms exposed on the surface to trap food.
                    Small organic particles can be moved into the mouth by the tube feet. Ophiuroids filter feed, i.e.,
                    capture small food particles from the water, by extending their arms into the water and forming a
                    mucous net between spines on adjacent arms.</p>
                <p class="body">
                    Most ophiuroids are dioecious (separate sexes) although a few are hermaphroditic. Some ophiuroids
                    brood their developing larvae and give birth to &ldquo;live young.&rdquo; Other species release
                    gametes into the water column, and after fertilization, a free-swimming larval stage called an
                    ophiopluteus hatches. The ophiopluteus metamorphoses into an adult while in the water column before
                    settling to the substratum. </p>
                <p class="title">Class: Echinoidea</p>
                <p class="body">The more familiar sea urchins and sand dollars, as well as heart urchins belong to the
                    class Echinoidea &ndash; meaning &ldquo;like a hedgehog.&rdquo; These echinoderms are benthic
                    dwelling organisms that occur at every depth in the ocean. Sea urchins are referred to as regular or
                    radial echinoids. The rounded endoskeleton or test of the sea urchin is covered with articulated,
                    elongated spines and pedicillaria. The spines are used for protection, and for sea urchins living in
                    rocky intertidal areas, they also serve to dissipate wave energy.</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Arbacia punctulata350.jpg" width="350" height="277"><br/><span
                        class="caption">Purple-spined sea urchin, <em>Arbacia punctulata</em>. <br/>Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <p style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="body">Spines of some species, e.g., <em>Diadema
            antillarum</em>, the long-spined sea urchin, contain venom that is injected into the victim when the tip of
        a spine is broken off. The role of the pedicillariae is less well understood, but they may function to keep the
        test free of debris, such as algae and encrusting organisms. Urchins move about with the aid of their spines and
        tube feet that end in suckers.<br/>
        <br>
        Most sea urchins are herbivores that tend to be found on hard substrata. They feed by scraping off algae, as
        well as bryozoans, sponges, other encrusting organisms, and dead organic matter, with their five teeth that form
        a structure known as Aristotle&rsquo;s lantern. Other urchins, e.g., <em>Lytechinus</em> sp., are found in
        seagrass beds where they consume substantial amounts of seagrass on a daily basis. <em>Diadema antillarum</em>,
        mentioned above, is one of the most important algal grazers on Atlantic and Caribbean reefs. Their critical
        grazing role came to light when a widespread loss of these urchins in the early 1980s was followed by dramatic
        overgrowth of reefs by algae.</p>
    <br/>
    <br/>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><img src="../content/imglib/Diadema antillarum50.jpg" width="350" height="263"></td>
        </tr>
    </table>
    <table style="border:0;width:350px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <span class="caption">The long-spined sea urchin, Diadema antillarum. Photo courtesy of L. Holly Sweat.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Sexes are usually separate in echinoids, and fertilization is external. After eggs and
                    sperm are released into the water column, the fertilized eggs release planktonic larvae that can
                    remain in the water column for several days to months before settling out.</p>
                <p class="body">
                    The &ldquo;irregular&rdquo; echinoids tend to be circular and flattened, and include heart urchins
                    and sand dollars. These species are well adapted for living on soft, sandy bottoms. Their tests are
                    covered with many minute spines that facilitate burrowing and locomotion. Most irregular urchins are
                    selective deposit feeders that ingest organic matter after separating it from inorganic
                    sediment. </p>
                <p class="title">Class: Holothuroidea</p>
                <p class="body">Sea cucumbers are members of the class Holothuroidea, and have elongated, leathery,
                    warty bodies containing a single gonad. They also are equipped with five rows of tube feet extending
                    from the oral to anal end. They do not have a test, and they lack spines. Their endoskeleton
                    comprises isolated microscopic ossicles, i.e., minute spicules of calcium carbonate joined by
                    connective tissue. Many are found in soft bottom areas, but some cling to rocky substratum in
                    high-energy environments. They usually lie on one side and move along slowly using their tube feet
                    and muscular body contractions. Sea cucumbers have specialized tube feet that are elaborated into
                    branched tentacles surrounding the mouth. Many species use these tentacles to feed directly on
                    sediment as non-selective deposit feeders, </p></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td valign="top" width="50%"><img src="../content/imglib/seacucumber275.jpg" width="275"
                                              height="184"><br/><span class="caption">Isostichopus bandionotus: sometimes referred to as the three-rowed sea cucumber.From Hendler, G <em>et al.</em> 1995. Sea stars, sea urchins, and allies. <br/>Smithsonian Institution Press. With permission.</span>
            </td>
            <td valign="top"><img src="../content/imglib/Holothuria grisea350.jpg" width="350" height="263"><br/><span
                        class="caption">Sea cucumber, Holothuria grises, displaying non-selective deposit feeding mode. Photo courtesy of L. Holly Sweat</span>
            </td>
        </tr>
    </table>

    <br/><br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">whereas other sea cucumbers are suspension feeders that burrow in the sand and extend
                    their tentacles to feed from the water column. Locally, sea cucumbers can be responsible for
                    recycling nutrients by breaking down detritus and organic matter to make it more available for
                    bacteria and fungi to decompose.</p>
                <p class="body">
                    Sexes are usually separate in sea cucumbers. Some sea cucumbers brood their eggs, and others will
                    incubate them externally on the body surface. In either case, planktonic larvae are released into
                    the water column when the eggs hatch.</p>
                <p class="body">
                    Lacking a hard test for defense, many sea cucumbers have evolved unique ways of deterring predators.
                    Not only will they secrete toxic substances, some sea cucumbers, when disturbed, will exude
                    spaghetti-like filaments (cuvierian tubules) from their anal region. These sticky tubules can
                    discourage potential predators because they feel the need to clean themselves, which gives the sea
                    cucumber time to escape. Other sea cucumbers will eviscerate their stomachs and other internal
                    organs through their mouth or anus. This sudden, explosive response deters predators, and the sea
                    cucumber eventually regenerates its lost body parts.</p>
                <br/>
                <p class="footer_note">Report by: Joseph Dineen, Smithsonian Marine Station at Fort Pierce<br>
                    Submit additional information, photos or comments to:<br>
                    <a href="mailto:IRLWebmaster@si.edu">IRLWebmaster@si.edu</a></p>&nbsp;
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
