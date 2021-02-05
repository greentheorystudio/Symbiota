<?php
include_once(__DIR__ . '/../config/symbini.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $CHARSET);

$IRLManager = new IRLManager();

$plantsArr = $IRLManager->getChecklistTaxa(33);
$algaeArr = $IRLManager->getChecklistTaxa(34);
$insectsArr = $IRLManager->getChecklistTaxa(35);
$aquaticInvertsArr = $IRLManager->getChecklistTaxa(36);
$hemichordatesArr = $IRLManager->getChecklistTaxa(37);
$reptilesArr = $IRLManager->getChecklistTaxa(38);
$fishesArr = $IRLManager->getChecklistTaxa(39);
$birdsArr = $IRLManager->getChecklistTaxa(40);
$mammalsArr = $IRLManager->getChecklistTaxa(41);
$vernacularArr = $IRLManager->getChecklistVernaculars();
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
<head>
    <title>Tidal Flat Habitats</title>
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
    <h2>Tidal Flat Habitats</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center"><img src="../content/imglib/Tidal_Flat_1.jpg" alt="" width="528" height="433"/><br/>
                <span class="caption">Fig. 1. Indian River Lagoon tidal flat: (A) view of flat on eastern edge of Coon Island, Ft. Pierce, FL.; (B) exposed area of flat with Ft. Pierce Inlet in background; (C) protected area of flat with Tucker Cove in background.</span>
            </td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">What are Tidal Flats?</p>
                <p class="body">Tidal flats are intertidal, non-vegetated, soft sediment habitats, found between mean
                    high-water and mean low-water spring tide datums (Dyer et al. 2000) and are generally located in
                    estuaries and other low energy marine environments. They are distributed widely along coastlines
                    world-wide, accumulating fine-grain sediments on gently slopping beds, forming the basic structure
                    upon which coastal wetlands build. Although tidal flats comprise only about 7% of total coastal
                    shelf areas (Stutz and Pikey 2002), they are highly productive components of shelf ecosystems
                    responsible for recycling organic matter and nutrients from both terrestrial and marine sources and
                    are also areas of high primary productivity. In the Indian River Lagoon (IRL), tidal flats can
                    occupy significant areas (Schmalzer 1995) but are most prominent and abundant in the vicinity of
                    inlets where tidal influence is strongest.</p>
                <p class="title">Why are Tidal Flats Important?</p>
                <p class="body">Tidal flats are highly productive areas and although biological diversity may be
                    relatively low, tidal flats support a high biomass of micro- and infaunal organisms, support large
                    fin and shellfish stocks and play an important role in intertidal nutrient chemistry. Tidal flats
                    provide enormous water carrying capacity, protecting areas of the IRL from storm surge as well as
                    storm water runoff. Tidal flats along with intertidal salt marshes and mangrove forests constitute
                    IRL wetlands and are a vital part of the lagoon ecosystem. Tidal flats will often form the buffer
                    zone between deeper reaches of the lagoon thereby protecting intertidal habitats by dissipating wave
                    energy, thus reducing erosion of mangroves and salt marshes. Collectively these intertidal habitats
                    are of great importance to large numbers of invertebrates and fish, supporting complex estuarine
                    food webs and provide resting and feeding areas to large numbers indigenous and migratory birds.</p>

                <p class="title">The Physical Setting</p>
                <p class="body">Zonation of tidal flats may be divided into three parts (Amos 1995): 1) the supratidal
                    zone, located above high water; 2) the intertidal zone, located between high and low water; and 3)
                    the subtidal zone which occurs below low water and is rarely exposed to the atmosphere. Most studies
                    of tidal flats concentrate on the intertidal region. </p>
                <p class="body">Tidal flats are highly dynamic systems, in constant motion. The ETDC, refers to the
                    Erosion, Transport, Deposition and Consolidation cycle whereby sediments within intertidal flats are
                    transported continuously. Although the physical aspects of this cycle are well understood,
                    predictions of sedimentation are difficult because of site-specific differences (Black et al.
                    2002).</p>

                <p class="body">Depending on sediment grain size, tidal flats may be generally categorized as either mud
                    or sandflats. Generally, mudflats are located in the upper part of the intertidal zone, sandflats
                    are located in the lower part. Mixed sand-mud flats can occur between the two systems but this
                    zonation may be modified by the presence of tidal creeks and the vagaries of sediment flux. The
                    distribution of mud and sandflats along a shoreline is primarily due to the relative strength of
                    prevailing water currents.</p>
                <p class="body">Rapidly moving water will tend to carry larger, heavier sediment particles, washing away
                    smaller particles and preventing their deposition. Hence tidal flats with low energy water movement
                    are characterized by more muddy sediments whereas in higher energy regimes, with stronger currents
                    and moderate wave action, flats are generally composed of courser sandy sediments. For example, Coon
                    Island is located in the Indian River Lagoon along the north side of Fort Pierce Inlet. The eastern
                    edge of the island gives way to a relatively large tidal flat (Fig. 1A). Sediments on the southern
                    end of the tidal flat, in close proximity to the inlet where currents are relatively strong, are
                    predominantly sandy (Fig. 1B). On the northern and western portions of the flat, more protected from
                    inlet currents, tidal flat sediments are more muddy (Fig. 1C). Nearby, in the vicinity of Jack
                    Island, but further distanced and protected from inlet currents, low tide exposes a mud flat (Fig.
                    2) surrounding a small mangrove island. </p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_2.jpg" alt="" width="542" height="407"/><br>
                <span class="caption">Fig. 2. Indian River Lagoon mudflat, Ft. Pierce, FL.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body">Mudflats have higher organic loads than sandflats. Organic material may be derived from
                    in situ production or come from adjacent coastal sources (salt marshes, mangrove forests, seagrass
                    beds). Muddy systems are composed of sediments containing > 80 % silt and clay (particle size < 63
                    &micro;m) (Dyer 1979). Silts are fine inorganic particles held in suspension by slight water
                    movement, while clay particles are colloids of hydrated aluminum silicate with iron and other
                    impurities.</p>

                <p class="body">Sediments of sandflats are comprised of larger, independent grains, mostly quartz
                    (silica) derived from erosion. Sandy sediments can be divided into three size categories: 1) course
                    (0.5 - 1.0 mm); medium (0.25 - 0.5 mm); and fine (0.063 - 0.25 mm). In southern Florida systems,
                    mud-sand and combinations of coral rock (Ca CO3) soft-bottom types are common.The CaCO3 sediments of
                    southeastern Florida are composed primarily of codacean algal plates (Myers and Ewel 1990).</p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_3.jpg" alt="" width="543" height="132"/><br/>
                <span class="caption">Fig. 3. Burrow openings of infaunal organisms: (A) polychaete worm; (B &amp; C) stomatopods</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">
                    Topographic features of tidal flats in different flow regimes can also differ. In low flow areas,
                    i.e., areas more characteristic of mudflats, surfaces are generally smooth, occasionally interrupted
                    by burrow openings (Fig. 3 A, B & C), pellets (Fig. 11), and fecal casts (Fig. 13) created by
                    infaunal organisms. Sandflat surfaces can be noticeably carved by prominent ripple structures (Fig.
                    4) caused by wave action on the sandy bed.</p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_4.JPG" alt="" width="265" height="204"/><br>
                <span class="caption">Fig. 4. Tidal flat ripples in Indian River Lagoon, FL.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br>
                <p class="body">Mud and sand flats also differ in their vertical concentration of oxygen content which
                    influences microbial activity. Microbial activity in tidal flats is significant: it stabilizes
                    organic fluxes by reducing seasonal variation in primary productivity thereby ensuring a more
                    constant food supply (Robertson 1988); and, the sheer bacterial biomass in these environments rivals
                    that of the animals living within the sediment. In muddy sediments, several factors contribute to
                    more extensive anoxic areas below the surface because of higher oxygen uptake. The lower
                    permeability (i.e., the amount of water flowing though sediment) in fine sediments tends to trap
                    detritus. Higher microbial numbers, due to the increased surface attachment area of fine grains,
                    leads to increased anaerobic degradation of the detrital matter, producing hydrogen sulfide, methane
                    and/or ammonia. The resulting black, anoxic, reducing layer, occurring &lt; 1 cm below the surface
                    can be strikingly differentiated from the relatively thin oxygenated layer occurring above it.
                    Between the two is a grayish layer in which the redox potential (Eh) (a measure reflecting the
                    balance between oxidation and reduction processes) decreases rapidly. This layer is referred to as
                    the redox potential discontinuity layer (RPD) (Little 2000).</p>
                <p class="body">With increased depth, microbial activity becomes chemosynthetic (producing energy from
                    chemical bonds).&nbsp;In contrast, on sandflats, water easily percolates through the sediment,
                    resulting in oxygen penetrating as far as 10 to 20 cm below the surface. The relatively large sized
                    sand grains also provide enough void space to allow for occupancy by a group of organisms called
                    meiofauna (muticellular organisms &lt; 1 mm in length) &ndash; see below. In addition, light
                    penetration is much deeper in sandy as opposed to muddy environments, allowing for prolonged
                    photosynthesis by the microphytobenthos (organism inhabiting interstitial spaces between sediment
                    particles) &ndash; also see below - even during tidal submersion.</p>
                <p class="title">Tidal Flat Organisms</p>
                <p class="body">Tidal flats play host to a diverse biotic assemblage ranging from microscopic organisms
                    found adhering to and living within interstitial spaces of sediment particles to large epibenthic
                    forms such as crabs, fish and wading birds. Paterson et al. (2009) classify benthic organisms
                    associated with tidal flats into essentially 5 categories based on size and lifestyle: 1) the
                    Microbenthos includes bacteria, diatoms, euglenoids and ciliates; 2) the Meiobenthos (aka Meiofauna)
                    are comprised of multicellular organisms less than 1 mm, occurring within the interstices of the
                    sediment grains; 3) the Hyperbenthos are small (a few mm in length) organisms occurring in the water
                    column just above the surface but may also be found within the sediment; 4) the Macrobenthos
                    includes organisms over 1 mm in length that move freely through soft sediments, e.g., polychaete
                    worms, bivalves and amphipods; and 5) the Epibenthos are comprised of large, active predatory and
                    grazing species such as crabs, molluscs, fish, rays, wading birds and mammals.
                </p>

                <p class="body"><strong>1. Microbenthos</strong>:<br/>
                    Paterson et al. (2009) further subdivide the microbenthos into 4 subcategories: i) the
                    Picoheterobenthos which includes bacteria and viruses; ii) the Picophytobenthos which includes
                    photosynthetic cells < 2 &micro;m; iii) the Microphytobenthos which are comprised of unicellular
                    photosynthetic organisms > 2 &micro;m; and iv) the Microheterobenthos which are unicellular
                    heterotrophic organisms > 2 &micro;m.</p>
                <p class="body">Of these subgroups, the microphytobenthos is perhaps the most extensively studied and
                    represent an interesting and ecologically significant group. The microphytobenthos include
                    unicellular, eukaryotic algae (primarily benthic diatoms), cyanobacteria and flagellates. This
                    assemblage of organisms often imparts a brown, green (Fig. 5) and/or golden brown film on the
                    surfaces of tidal flats during daytime low-tide periods as they migrate vertically from depths of 1
                    to 2 mm (Little 2000). Growing within the illuminated surface tidal flat sediments, the
                    microphytobenthos play a significant role in system productivity, trophodynamics and sediment
                    stability (MacIntyre et al. 1996).&nbsp;In fact, the microphytobenthos can be the most important
                    primary producers in coastal ecosystems with large intertidal flats and can provide a substantial
                    food source for the meio- and macrobenthos. Dense, rigid, microbial mats on fine sand sediments
                    result from cyanobacterial activity whereas biofilms of epipelic diatoms are generally found on
                    mudflats (Stal 2003).</p></td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/Tidal_Flat_5.JPG" alt="" width="265" height="168"/><br/>
                <span class="caption">Fig. 5. Tidal flat surface colored green by epipelic&nbsp;microalgae.</span></td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">Benthic diatoms may be classified into 2 categories: 1) the epipelon; and 2) the
                    epipsammon. Diatoms that belong to the epipelon move actively in the surface layers while those
                    belong to the epipsammon are attached to sediment grains and have limited mobility. Vertical
                    migratory behavior of epipelic diatoms is an adaptive strategy controlled more by light than tides
                    (Mitbavkar and Anil 2004). In mudflats, the cohesive nature of silt particles, due to their charged
                    nature and organic coating, provide some surface stability to the sediment flat. Equally important
                    in the stability of mudflat surfaces is the production of extracellular polymeric substances (EPS)
                    by epipelic diatoms.&nbsp;EPS consist mostly of polysaccharides, are independent of photosynthesis,
                    and are produced by epipelic diatoms in association with their motility.<br/>
                    Thus extensive surface biofilms on intertidal mudflats, resulting from EPS matrix production,
                    produces a protective micro-environment embedded with biofilm organisms (de Brouwer and Stal 2000,
                    Stal and de Brouwer 2003). This biofilm has been thought to increase the sediment erosion threshold
                    although this relationship has been questioned (Stal 2003). Recent studies using remote sensing have
                    found that the microphytobenthos combined with sediment characteristics provide a reliable predictor
                    of the distribution and dynamics of intertidal macrobenthos (van der Wal et al. 2008).</p>

                <p class="body"><strong>2. Meiobenthos</strong>:<br/>
                    The Meiobenhtos (or meiofauna) (from the Greek word &ldquo;meio&rdquo; meaning smaller) include a
                    host of multi-cellular organisms, less than 1 mm in length, living interstitially among sediment
                    particles in a wide range of marine and freshwater habitats including estuarine sand and mudflats.
                    Meiofauna are entirely aquatic, requiring water within interstitial spaces to survive. Average
                    densities of meiofaunal organisms are approximately 106 per square meter of substratum but represent
                    only a few grams of biomass. Higher densities usually occur in softer, muddy, sheltered areas. This
                    is thought to be, in part, a consequence of the increased bacterial food supply, i.e., the smaller
                    mud particles providing more surface area for increased bacterial attachment and growth. Predators
                    as well as physical disturbances can also affect population densities of meiofauna (Bell and Coull
                    1978), but since most meiofaunal organisms reproduce so rapidly, predators cannot significantly
                    reduce their abundances.</p>
                <p class="body">Temporary meiofauna are represented by macrofaunal larvae and juveniles and are part of
                    the meiobenthos only during a portion of their life history. Permanent meiofauna are part of the
                    meiobenthos throughout their entire life cycle (McIntyre 1968), e.g., nematodes, harpacticoid
                    copepods, ostracods. Nematods are usually the most abundant member of meiofaunal assemblages with
                    harpacticoid copepods second in abundance. Although nematodes, copepods and turbellarians (Fig. 6 A)
                    usually comprise more than 95% of the meiofaunal community, most phyla have meiofaunal
                    representatives. Minor phyla represented in the meiofauna include the gastrotrichs (Fig. 6 B),
                    kinorhynchs, rotifers, tardigrades, priapulids and loriciferans. See Nielsen (2001) for a taxonomic
                    listing of meiofaunal organisms. Grain size is important in determining the size and types of
                    meiofaunal organisms present. For example, coarse grain sediments have greater interstitial volume
                    accommodating relatively larger meiofauna as opposed to fine grain sediments where burrowing forms
                    (e.g., kinorhynchs) are more likely to be present.</p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_6.jpg" alt="" width="534" height="164"/><br/><span
                        class="caption">Fig. 6. IRL meiofauna: (A) <em>Lehardyia spp. </em>(Phylum: Platyhelminthes); (B) <em>Tetranchyroderma bunti </em>(Phylum: Gastrotricha). Photos by Rick Hochberg.</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">In intertidal flats with a relatively high mud content, the majority of mieofauna are
                    found in the upper 2 cm of sediment, usually dictated by the relative depth of the RPD (redox
                    potential discontinuity). However, in coarse, well oxygenated sediments, meiobenthos can be found at
                    deeper depths. Meiofauna of upper, more exposed layers of sediment include forms with greater
                    tolerance to salinity and temperature fluctuations. Because of the stability and complexity of
                    interstitial habitats, the diversity of the meiofaunal community far outnumbers that of the
                    associated macrofauna. It has been shown that meiofauna can also affect the densities of macrofaunal
                    larvae and juveniles recruiting to the benthos (Watzin 1983).</p>

                <p class="body">Meiofauna play an integral role in estuarine food web dynamics. As mentioned above, by
                    feeding on bacteria as well as benthic diatoms and protozoans, meiofauna provide a link to higher
                    trophic level consumers. For example, meiofaunal copepods serve as a food source for several
                    predators especially juvenile fish. Copepods are high in essential fatty acids required by fish. In
                    turn, copepods fatty acid make up is similar to that of the microphytobenthos that they consume
                    (Coull 2009). Meiofauna are also important in nutrient recycling because they facilitate
                    biomineralization of organic matter. They are also good indicators of estuarine health because of
                    their high sensitivity to anthropogenic inputs. For further information on meiofauna, please see
                    Higgins and Thiel (1988) and Giere (2009).</p>

                <p class="body"><strong>3. Hyperbenthos</strong>:<br/>
                    As the name implies, the hyperbenthos live just above the sediment and occur there in greater
                    densities than in either the adjacent sediment or water column. Distinctions have been made between
                    truly hyperbenthic organisms and immigrants that could be endobenthic (living within the sediment),
                    epibenthic (living on the surface of the sediment) or planktonic (drifting in the water column)
                    (Mees and Jones 1997). Hyperbenthic community structure can fluctuate seasonally due to temporary
                    immigrants. The term hyperbenthos was first used by Beyer (1958) and applies to the association of
                    small sized, bottom dependent animals (mainly crustaceans) that are capable of migrating daily or
                    seasonally above the sea floor. Hyperbenthic organisms can play a significant role in both tropical
                    and temperate estuarine food webs (Sibert 1981, Winkler & Greve 2004).</p>
                <p class="body">Terms such as "dermersal zooplankton", "benthopelagic plankton" and "benthic boundary
                    layer fauna" are generally applied to hyperbenthos in tropical areas. Many demersal fish and
                    epibenthic crustaceans feed on the hyperbenthos during at least part of their life cycle. Studies of
                    benthic pelagic coupling related to energy fluxes have underestimated the role of the hyperbenthic
                    community (Koulouri 2010) most likely due to inadequate sampling methods.</p>

                <p class="body"><strong>4. Macrobenthos:</strong><br/>
                    This group of organisms are often referred to as ecosystem engineers (Paterson et al. 2009) or
                    bioturbators because they are large (> 1 mm), infaunal organisms that affect the structure and
                    chemistry of their own microenvironment (Little 2000) by burrowing activity. Macrobenthic organisms
                    include molluscs, worms, crustaceans, echinoderms and hemichordates. The yabby pump (Fig. 7) is a
                    suction device often used to extract large, intact, infaunal organisms from soft sediment habitats.
                </p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_7.JPG" width="265" height="188"><br>
                <span class="caption">Fig. 7. Yabby pump being used to extract infaunal macrobenthos.</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">
                    Trophic modes of bioturbators include filter feeding, deposit feeding and predation (Bertness 1999).
                    Most bivalves are filter feeders and burrow into the sediment using their muscular foot. Bivalve
                    shell sculpturing (ribbing) is thought to increase friction and burrowing efficiency (Stanley 1970).
                    Filter feeding bivalves use their incurrent siphons to draw water into the body and pass it over the
                    gills where tiny food particles such as diatoms, small zooplankton and detritus are extracted. Cilia
                    then move the food toward the mouth. Water drawn in through the incurrent siphon also serves as a
                    source of oxygen enabling the bivalve to respire. Filtered water, waste products and gametes are
                    passed out into the water column through the excurrent siphon.</p>
                <p class="body">Filter feeding bivalves on Indian River Lagoon tidal flats include the angelwing clam,
                    Cyrtopleura costata (Fig. 8 A), the Atlantic giant cockle, Dinocardium robustum (Fig. 8 B),the
                    southern hard clam, Mercenaria campechiensis, the hard clam, Mercenaria mercenaria, and the lucinid
                    bivalve, Phacoides pectinata (Fig. 8 C) among others. These bivalves not only provide a vital
                    trophic link between the water column and benthic production, but are also an important and abundant
                    prey item of large, predatory, tidal flat species such as snails, crabs, fish and wading birds. </p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_8.jpg" width="544" height="127"><br>
                <span class="caption">Fig. 8. Tidal flat bivalves: (A) Angelwing clam, <em>Cyrtopleura costata; </em>(B) Atlantic giant cockle, <em>Dinocardium robustum</em>; (C) the lucinid bivalve, <em>Phacoides pectinata</em>.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body">Filter feeding polychates include the parchment worm, Chaetopteris variopedatus (Fig.
                    9), noted for its tough membranous tube. It has developed paddles to pump water through the head and
                    out the tail of its u-shaped burrow, thus effectively enabling the animal to rise above the redox
                    potential discontinuity (RPD) (Little 2000). C variopedatus is abundant on IRL mud flats. </p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_9.JPG" width="265" height="226"><br>
                <span class="caption">Fig. 9. Parchment worm, <em>Chaetopteris variopedatus</em>.</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">The southern Indian River Lagoon supports an unusually high assemblage of infaunal
                    decapod and stomatopod crustaceans (Felder and Manning 1986) that both filter and deposit feed. Two
                    genera of thalassinidean shrimp, Callianassa (Fig. 10 A) - the ghost shrimp, and Upogebia - the mud
                    shrimp, as well as the stomatopods Coronis excavatrix (Fig. 10 B), Lysiosquilla scabricauda and
                    Lysiosquilla spp. are abundant macrobenthic crustaceans in the Inidan River Lagoon and are capable
                    of extensive biotubation when constructing their elaborate, branching burrows. In the IRL, burrowing
                    thalassinidean shrimp are represented by two species of Callianassa (C. guassutinga & C. rathbunae)
                    and one species of Upogebia (U. affinis).</p>
                <p class="body">Borrows have more than one entrance (Fig. 2 B) and shrimp are often found near an
                    entrance pumping water into the burrow by beating their pleopods. These burrowing shrimp are
                    considered to be both filter and deposit feeders with often one or the other trophic mode being more
                    dominant, depending on the species (Coelho et al. 2000). Upogebiids generally feed on suspended
                    material filtered from the water, while callianassids mainly feed on sediment taken up within the
                    burrow by the second and third pereiopods. Both ghost and mud shrimp can have substantial effects on
                    the abundance of co-occurring macro-infauna (Posey 1991). Commensals in the burrows of these
                    infaunal shrimp may include polychaete worms, snapping shrimp and pea crabs.</p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_10.jpg" width="531" height="164"><br>
                <span class="caption">Fig. 10. Burrowing crustaceans: (A) Ghost shrimp, <em>Callianassa spp.</em>; (B) stomatopod,<em> Coronis excavatrix. </em>Photos by Sabine Alshuth.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body">The burrowing, protobranch bivalve Macoma is also capable of both deposit and filter
                    feeding. For example, in low flow situations, Macoma will remove sediment from the surface with its
                    oral palps but will switch to filter feeding in high flow situations (Olafsson et al. 1994). Several
                    species of Macoma are present in the IRL.</p>
                <p class="body">Deposit feeders on tidal flats include surface deposit feeders that generally affect the
                    upper 2 to 3 cm of sediment and burrowing deposit feeders whose effects on the sediments have deeper
                    repercussions, i.e. up to 30 cm (Bertness 1999). Nonselective deposit feeders ingest both organic
                    and sediment particles and then digest the organic material, e.g., bacteria growing on the sediment
                    particles. Selective deposit feeders separate organic material from sediments prior to ingestion.
                    Deposit feeders are an important link between the benthos and the sediment. They enhance sediment
                    resuspension and nutrient exchange with the water column and increase productivity by increasing
                    oxygen and nutrient levels in the benthos (Bertness 1999).</p>
                <p class="body">Surface deposit feeders include mud snails, fiddler crabs, echinoderms, certain pelagic
                    fish and shrimp. An abundant, deposit feeding fiddler crab on IRL mudflats is Uca pugilator (Fig.
                    11). Fiddler crabs form two types of characteristic pellets affecting sediment surface topography.
                    The larger of the two pellets are formed during burrow excavation. Smaller pellets are formed during
                    deposit feeding when the crab removes organic matter then rolls the remaining sediment into small
                    balls and deposits them on the substratum. </p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_11.jpg" width="265" height="185"><br/>
                <span class="caption">Fig. 11. Fiddler crabs, <em>Uca pugilator</em>, on IRL mud flat.</span></td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body">The nine-armed starfish, <em><a href="../taxa/index.php?taxon=Luidia_senegalensis">Luidia
                            senegalensis</a></em>, is a striking macrobenthic echinoderm in the IRL occurring on
                    intertidal flats as well as subtidally. When buried, <em>L. senegalensis </em>(Fig. 12) will invert
                    its stomach to feed on detritus (Hendler et al. 1995). Burrowing deposit feeders include polychaete
                    and sipunculan worms (e. g., Siphonosoma cumanense, and Sipunculus nudus) (Rice 1995) bivalves and
                    amphipods among others. The lugworm, <em>Arenicola cristata</em>, lives in extensive u-shaped tubes
                    excavated in muddy tidal flat habitats. After sediment ingestion, the lugworm deposits large fecal
                    casts at the posterior end of the burrow (Fig. 13). </p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_12.jpg" width="527" height="190"><br>
                <span class="caption">Fig. 12. Nine-armed starfish, <em>Luidia senegalensis</em>: (A) on surface of mudflat, dorsal view; (B) ventral view.</span>
            </td>
        </tr>
        <tr>
            <td align="center"><br/>
                <img src="../content/imglib/Tidal_Flat_13.JPG" width="265" height="192"><br>
                <span class="caption">Fig. 13. Burrow opening and fecal cast of the lugworm, <em>Arenicola cristata</em>.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body">
                    Examples of predatory macrobenthic organisms on IRL tidal flats include the moon snail (or shark
                    eye), Polinices duplicatus (Fig. 14) and the onuphid polychaete, Diopatra cuprea (Fig. 15). P.
                    duplicatus crawls along the sediment feeding on infaunal bivalves by drilling a hole into the
                    bivalve with its radula. It then inserts its proboscis to rasp out the flesh from inside the bivalve
                    shell. Diopatra builds extensive mucous/sand tubes extending 50 - 60 cm below the sediment. The tube
                    cap which extends several centimeters above the sediment surface is in the form of a decorated
                    inverted hook thought to aid in food capture. Diopatra feed on the epibiota of its own as well as
                    its neighbor's tube caps.</p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_14.JPG" width="265" height="208"> <br>
                <span class="caption">Fig. 14. Moon snail, <em>Polinices duplicatus</em></span>
            </td>
        </tr>
        <tr>
            <td align="center"><br/>
                <img src="../content/imglib/Tidal_Flat_15.JPG" width="265" height="269"><br/><span class="caption">Fig. 15. Burrowing polychaete, <em>Diopatra <br/>
                      cuprea</em>.</span>
            </td>
        </tr>
        <tr>
            <td>
                <br/>
                <p class="body"><strong>5. Epibenthos:</strong><br/>
                    The epibenthos are large, mobile, species that make up a substantial proportion of tidal flat
                    biomass. IRL epibenthos include horseshoe crabs, crabs, shrimp, molluscs, rays, skates, bottom fish,
                    gulls, terns, wading birds, reptiles and mammals. Most epibenthic organisms are predatory, some are
                    grazers. For example, raccoons, Procyon lotor elucus (Fig. 16) are opportunistic tidal flat
                    predators, foraging on fiddler and blue crabs, snails, fish, snakes and eggs of birds, turtles and
                    alligators and will dig into the sediment for infaunal bivales.</p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_16.JPG" width="265" height="181"><br/>
                <span class="caption">Fig. 16. Raccoons, <em>Procyon lotor elucus</em>, foraging on IRL mud flat.</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="body">The ragged sea hare, Bursatella leachii, (Fig. 17) is a grazing benthic
                    detritivore/herbivore that feeds primarily on cyanophytes and diatom mats and films found on sand,
                    mud and other benthic substrata. These epibenthic species can have enormous effects on the
                    abundance, diversity and distribution of microbenthic and infaunal tidal flat organisms and can also
                    severely disturb the substratum while foraging for prey, e.g. the cow-nosed ray (Orth 1975). Early
                    studies clearly demonstrated the effects of epibenthic predators on diminishing prey abundances with
                    predator exclusion cage experiments (Virnstein 1977; Peterson 1979). Further experimentation has
                    demonstrated a more complex picture of predator prey dynamics in soft bottom habitats particularly
                    when interactions between and among infaunal and epibenthic predators as well as physical parameters
                    of the habitat are considered (Quammen 1982, Ambrose 1984, Bottom 1984, Commito and Ambrose 1985,
                    Thrush et al. 1997).</p></td>
        </tr>
        <tr>
            <td align="center">
                <img src="../content/imglib/Tidal_Flat_17.JPG" width="265" height="194"> <br>
                <span class="caption">Fig. 17. Ragged sea hare, <em>Bursatella leachii</em>.</span>
            </td>
        </tr>
        <tr>
            <td><br/>
                <p class="title">Ecological Interactions</p>
                <p class="body"> In terms of understanding ecological interactions, tidal flats have been contrasted
                    with rocky intertidal habitats (Bertness 1999, Little 2000, Nybakken and Bertness 2005). The three
                    dimensionality of soft bottom habitats as opposed to hard, rocky, mostly two dimensional substarta
                    affords soft bottom infaunal organisms several advantages: they can retreat into deeper sediments or
                    burrows when threatened by predation and, in addition, many infaunal bivalves can survive partial
                    predation, i.e., siphon nipping; having the ability to move around in the sediment, many infaunal
                    organisms can avoid direct competition with neighbors and escape other predatory burrowers;
                    desiccation does not pose the threat to infaunal organisms during low tide as it does on rocky
                    shores, particularly in fine sediments that retain moisture; and finally, organic materials
                    collecting on sediments provide a ready, constant food source. Perhaps the biggest draw back to the
                    infaunal lifestyle is lack of a securing "anchor" in the sediment. For example, in the rocky
                    intertidal, organisms are securely attached to the rock surface by utilizing such mechanisms as
                    cement, byssal threads, a muscular foot, etc.. During periods of severe storm erosion (Little 2000),
                    larger infauna in soft bottom habitats may become easily dislodged and subsequently displaced.</p>
                <p class="title">Threats to Tidal Flats</p>
                <p class="body">Water and sediment quality characteristics are important factors in maintaining healthy
                    lagoon habitats. Tidal flat areas face a number of anthropogenic and natural threats including
                    predicted sea level rise, loss of habitat, salinity fluctuations, pollution, erosion and invasive
                    species.</p>

                <p class="body">Of major concern to all coastal areas worldwide and particularly threatening to coastal
                    wetlands, including tidal flats, are predicted sea level rises in response to global climate change.
                    Current estimates put predicted levels of sea rise at 60 cm in the next one hundred years. Although
                    over geological time, estuaries are thought of as ephemeral, most present day estuaries have been
                    relatively stable for approximately 6,000 years. Past changes in sea level have greatly affected
                    estuarine outlines and could have rapid and significant present-day effects (Holligan and Reiners
                    1992). Rising sea levels could render intertidal flats into subtidal habitat and inundate adjoining
                    mangrove and salt marsh areas (Little 2000).</p>
                <p class="body">Ever increasing population growth and development along Florida's coastline, coupled
                    with alterations caused by mosquito impoundments, have led to changes and degradation in Florida's
                    wetland and coastal areas. It is estimated that since the 1950's, 75% of the mangrove forests and
                    salt marshes bordering the Indian River Lagoon have been destroyed, altered or functionally
                    isolated. These changes in mangrove and salt marsh areas have direct repercussions for bordering
                    sand and mudflats.</p>

                <p class="body">Excessive freshwater flows from storms or the construction of agricultural and urban
                    drainage projects can lead to extreme salinity fluctuations in the IRL estuary and can affect
                    community structure and stability of tidal flats. Continuous exposure to lower salinity regimes can
                    compromise stenohaline, shallow burrowing infaunal organisms as well as the microphytobenthos,
                    resulting in deleterious effects on food web dynamics.</p>

                <p class="body">Point and non-point sources of pollution pose direct threat to IRL habitats including
                    tidal flats. Excessive nutrients can increase the proliferation of cyanobacterial mats covering IRL
                    tidal flats as well as promote excessive phytoplankton growth that can interfere with normal filter
                    feeding processes of infaunal oragnisms. In addition, excessive nutrients can cause the appearance
                    and proliferation of macroalgal species such as Ulva and Enteromorpha interfering with the normally
                    unvegatated status of the tidal flat.</p>
                <p class="body">Storm water runoff (non-point pollution), draining both urban and agricultural areas,
                    contains suspended sediments as well as industrial, automotive and household chemicals, pesticides,
                    and animal wastes. Turbidity levels are affected by the amount of total suspended organic and
                    inorganic solids (TSS) in the water column. Increased turbidity reduces light penetration and can
                    affect the photosynthetic capacity of tidal flat epipelic microalgae. Chemical pollutants are
                    incorporated into benthic sediments and adhere to sediment grains. Although the high bacterial
                    biomass associated with tidal flats, particularly mudflats, can break down these pollutants
                    somewhat, when excessive, these contaminants can accumulate in tidal flat/estuarine food webs.</p>

                <p class="body">IRL sediments are mostly made up from sands, silts and shell fragments. However, about
                    10% of the lagoon bottom is covered with muck - a loose, black, organic-rich mud. Although most muck
                    occurs in deeper areas of the lagoon, e.g., the intracoastal waterway, it is also found at the
                    mouths of most of the IRL major tributaries. When disturb, for example during intentional removal or
                    by storms or boat activity, etc., muck particles can be carried with currents and deposited in
                    shallower, near shore areas such as tidal flats. Muck displacement can potentially interfere with
                    infaunal filter and deposit feeding, as well as change the depth of the redox potential
                    discontinuity (RPD) layer.</p>
                <p class="body">Sources of IRL tidal flat erosion are many. Storms, wind induced waves, hurricanes,
                    epibenthic bioturbation, prop scarring, etc., can singly and sometimes synergistically contribute to
                    the erosion of tidal flats. Because most of IRL tidal flat areas are located in the vicinity of
                    inlets, they are further subjected to fluctuations in tidal current velocities. As mentioned above,
                    since most infaunal organisms burrowing on the tidal flat lack an anchoring structure, severe rapid
                    erosion, i.e. that which outpaces the ability of these organisms to burrow more deeply, can lead to
                    substantial changes in infaunal abundance.</p>

                <p class="body">Invasive species pose yet another threat to estuarine tidal flats. Since invasive
                    species do not normally occur in an area, they may lack natural predators and pathogens, allowing
                    them to proliferate and out-compete native species. Estuaries and shallow-water muddy sediments have
                    proportionately more invasive species than rocky shores and open coast sandy shores. This difference
                    probably results from the fact that most introductions, intentional or not, take place within the
                    estuary (Ruiz et al. 1997, Little 2000).</p>
                <p class="body">The following table is an abbreviated list of tidal flat organisms. Select available
                    links to learn more. Additional species reports can be found in the alphabetized lists of this site.
                </p></td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" class="table-border no-border alternate">
        <tr>
            <th>Scientific
                Name
            </th>
            <th>Common Name</th>
        </tr>
        <?php
        if($plantsArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">PLANTS</p></td>
            </tr>
            <?php
            foreach($plantsArr as $id => $taxArr){
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
        if($algaeArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">ALGAE & OTHER PROTISTS</p></td>
            </tr>
            <?php
            foreach($algaeArr as $id => $taxArr){
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
        if($insectsArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">INSECTS</p></td>
            </tr>
            <?php
            foreach($insectsArr as $id => $taxArr){
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
        if($aquaticInvertsArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">AQUATIC INVERTEBRATES</p></td>
            </tr>
            <?php
            foreach($aquaticInvertsArr as $id => $taxArr){
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
        if($hemichordatesArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">HEMICHORDATES</p></td>
            </tr>
            <?php
            foreach($hemichordatesArr as $id => $taxArr){
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
        if($reptilesArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">REPTILES & AMPHIBIANS</p></td>
            </tr>
            <?php
            foreach($reptilesArr as $id => $taxArr){
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
        if($fishesArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">FISHES</p></td>
            </tr>
            <?php
            foreach($fishesArr as $id => $taxArr){
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
        if($birdsArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">BIRDS</p></td>
            </tr>
            <?php
            foreach($birdsArr as $id => $taxArr){
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
        if($mammalsArr){
            ?>
            <tr class="heading">
                <td colspan="2"><p class="label">MAMMALS</p></td>
            </tr>
            <?php
            foreach($mammalsArr as $id => $taxArr){
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


    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="title">REFERENCES
                    &amp; FURTHER READING</p>
                <p class="body">Ambrose, W. G. 1984. Role of predatory infauna in structuring marine soft-bottom
                    communities. Mar. Ecol. Prog. Ser. 17(2): 109-115.</p>
                <p class="body">Amos, C. L. 1995. Siliciclastic tidal flats. In: Perillo, G. M. (Ed.), Geomorphology and
                    Sedimentology of Estuaries. Elsevier, Amsterdam. pp. 273-306.</p>
                <p class="body">Bell, S. and B. Coull 1978. Field evidence that shrimp predation regulates meiofauna.
                    Oecologia 35: 141-148.</p>
                <p class="body">Beyer, F. 1958. A new, bottom-living trachymedusa from the Oslo fjord. Nytt Mag. Zool.
                    6: 121-143.</p>
                <p class="body">Bertness, M. D. 1999. The Ecology of Atlantic Shorelines. Sinauer Associates, Inc.,
                    Sunderland. 417 pp.</p>
                <p class="body">Black, K. S., T. J. Tolhurst, S. E. Hagerthey and D. M. Paterson. 2002. Working with
                    natural cohesive sediments. J. Hydraulic Eng. Forum 128: 1-7. </p>
                <p class="body">Bottom, M. L. 1984. The importance of predation by horseshoe crabs, <em>Limulus
                        polyphemus</em>, to an intertidal sand flat community. J. Mar. Res. 42: 139-161.</p>
                <p class="body">Coelho, V. D., R. A. Cooper and S. Rodrigues. 2000. Burrow morphology and behavior of
                    the mud shrimp <em>Upogebia omissa</em> (Decapoda: Thalassinidea: Upogebiidae). Mar. Ecol. Prog.
                    Ser. 200: 229-240.</p>
                <p class="body">Commito, J. A. and W. G. Ambrose. 1985. Multiple trophic levels in soft-bottom
                    communities. Mar. Ecol. Prog. Ser. 26: 289-293.</p>
                <p class="body">Coull, B. C. 2009. Role of meiofauna in estuarine soft-bottom habitats. Austral Ecol.
                    24(4): 327-343.</p>
                <p class="body">de Brouwer, J. F. and L. J. Stal. 2001. Short-term dynamics in microphytobenthos
                    distribution and associated extracellular carbohydrates in surface sediments of an intertidal
                    mudflat. Mar. Ecol. Prog. Ser. 218: 33-44.</p>
                <p class="body">Dyer, K. R. (Ed.), 1979. Estuarine Hydrography and Sedimentation. Estuarine and Brackish
                    Water Sciences Association. Cambridge University Press, Cambridge. 230 pp.</p>
                <p class="body">Dyer, K .R., M.C. Christe and E. W. Wright. 2000. The classification of mudflats. Cont.
                    Shelf Res. 20: 1061-1078.</p>
                <p class="body">Felder, D. L. and R. B. Manning. 1986. A new genus and two new species of Alpheid
                    shrimps (Decapoda: Caridea) from south Florida. J. Crust. Biol. 6(3): 497-508.</p>
                <p class="body">Giere, O. 2009. Meiobenthology. The microscopic motile fauna of aquatic sediments.
                    Springer-Verlag, Berlin. 527 pp.</p>
                <p class="body">Hendler G., J. E. Miller, D. L. Pawson , and P. M. Kier. 1995. Sea Stars, Sea Urchins,
                    and Allies. Smithsonian Institution Press, Washington, D. C. 390 pp.</p>
                <p class="body">Higgins, R. P. and H. Thiel. 1988. Introduction to the study of meiofauna. Smithsonian
                    Institution Press, Washington, D. C. 488 pp.</p>
                <p class="body">Holligan, P. M. and W. A. Reiners. 1992. Predicting the responses of the coastal zone to
                    global change. Adv. Ecol. Res. 22: 211-215.</p>
                <p class="body">Koulouri, P. Preliminary study of hyperbenthos in Heraklion Bay (Cretan Sea). Accessed 5
                    April 2010. Available at: http://www.biomareweb.org/3.6.html.</p>
                <p class="body">Little, C. 2000. The Biology of Soft Shores and Estuaries. Oxford University Press,
                    Oxford. 252 pp.</p>
                <p class="body">MacIntyre, H. L., R. J. Geider and D. C. Miller. 1996. Microphytobenthos: the ecological
                    role of the &ldquo;Secret Garden&rdquo; of unvegetated, shallow-water marine habitats. I.
                    Distribution, abundance and primary production. Estuaries 19: 186-201.</p>
                <p class="body">McIntyre, A. D. 1968. The macrofauna and meiofauna of some tropical beaches. J. Zool.
                    156: 377-392.</p>
                <p class="body">Mees, J. and M. B. Jones. 1997. The hyperbenthos. Ocean. Mar. Biol.Ann. Rev.35:
                    221-255.</p>
                <p class="body">Mitbavkar, S. and A. C. Anil. Diatoms of the microphytobenthic community: population
                    structure in a tropical intertidal sand flat. Mar. Bio. 140: 41-57.</p>
                <p class="body">Myers, R. L. and J. J. Ewel (Eds.), 1990. Ecosystems of Florida. U. of Central Florida
                    Press, Orlando. 765 pp.</p>
                <p class="body">Nielsen, C. 2001. Animal Evolution: Interrelationships of the Living Phyla. Oxford
                    University Press, Oxford. 578 pp.</p>
                <p class="body">Nybaken, J. W. and M. D. Bertness. 2005. Marine Biology: an Ecological Approach.
                    Benjamin Cummings Publishers, San Francisco. 579 pp.</p>
                <p class="body">Olafsson, E. B. , C. W. Peterson and W. G. Ambrose. 1994. Does recruitment limitation
                    structure populations and communities of macro-invertebrates in marine soft sediments? The relative
                    significance of pre- and post-settlement processes. Ocean. Mar. Biol. Ann. Rev. 32: 65-109.</p>
                <p class="body">Orth, R. J. 1975. Destruction of eelgrass, <em>Zostera bonasus</em>, in Cesapeake Bay.
                    Chesapeake Sci. 16: 205-208.</p>
                <p class="body">Paterson, D. M., R. J. Aspden and K. S. Black. 2009. Intertidal flats: ecosystem
                    functioning of soft sediment systems. In: Perillo, G. M., E. Wolanski, D. R. Cahoon and M. M.
                    Brinson (Eds.), Coastal Wetlands An Integrated Approach. Elsevier, Amsterdam. Pp. 317-343.</p>
                <p class="body">Peterson, C. H. 1979. Predation, competitive exclusion, and diversity in the
                    soft-sediment benthic communities of estuaries and lagoons. In: Livingston, R. J. (Ed.), Ecological
                    Processes in Coastal Marine Systems. Plenum Press, New York. 548 pp.</p>
                <p class="body">Posey, M. H., B. R. Dumbauld and D. A. Armstrong. 1991. Effects of burrowing mud shrimp,
                    <em>Upogebia pugettensis</em> (Dana), on abundances of macro-infauna. J. Exp. Mar. Biol. Ecol. 148:
                    283-294.</p>
                <p class="body">Quammen, M. L. 1982. Influence of subtle substrate differences on feeding by shorebirds
                    on intertidal mudflats. Mar. Biol. 71: 339-343.</p>
                <p class="body">Rice, M. E., J. Piraino &amp; H. F. Reicherdt. 1995. A survey of the Sipunculaof the
                    Indian River Lagoon. Bu.. Mar. Sci. 57(1): 128-135.</p>

                <p class="body">Robertson, A. I. 1988 Decomposition of mangrove leaf litter in tropical Australia. J.
                    Exp. Mar. Biol. Ecol. 116: 236-247.</p>
                <p class="body">Ruiz, G. M., J. T. Carlton, E. D. Grosholz, and A. H. Hines. 1997. Global invasions of
                    marine and estuarine habitats by non-indigenous species: mechanisms, extent, and consequences. Am.
                    Zool. 37: 621-632.</p>

                <p class="body">Schmalzer, P.A. 1995. Biodiversity of saline and brackish marshes of the Indian River
                    Lagoon: historic and current patterns. Bull. Mar. Sci. 57(1): 37-48.</p>
                <p class="body">Sibert, J. R. 1981. Intertidal hyperbenthic populations in the Nanaimo Estuary. Mar.
                    Biol. 64: 259-265.</p>

                <p class="body">Stal, L. J. 2003. Microphytobenthos, their extracellular polymeric substances, and the
                    morphogenesis of intertidal sediments. Geomicrobio. J. 20 (5): 463-478.</p>
                <p class="body">Stal, L. J. and F. C. de Brouwer. 2003. Biofilm formation by benthic diatoms and their
                    influence on the stabilization of intertidal mudflats. Berichte -Forschungszentrum Terramare 12:
                    109-111.</p>
                <p class="body">Stanley, S. M., 1970. Relation of shell form to life habits of the bivalve molluscs.
                    Geol. Soc. Am. Monographs. 125: 1-296.</p>

                <p class="body">Stutz, M. L. and O. H. Pilkey. 2002. Global distribution and morphology of deltaic
                    barrier island systems. J. Coast. Res. 36: 694-707.</p>

                <p class="body">Thrush, S. F., R. D. Pridmore, R. G. Bell, V. J. Cummings, P. K. Dayton, R. Ford, J.
                    Grant, M. O. Green, J. E. Hewitt, A. H. Hines, M. T. Hume, S. M. Lawrie, P. Legendre, B. H. McArdle,
                    D. Morrisey, D. C, Schneider, S. J. Turner, R. A. Walters, R. B. Whitlatch and M. R. Wilkinson.
                    1997. The sandflat habitat: scaling from experiments to conclusions. J. Exp. Mar. Biol. Ecol. 216:
                    1-9.</p>

                <p class="body">Van der Wal, D., P. M. Herman, R. M. Forster, T. Ysebaret, F. Rossi, E. Knaeps, Y. M.
                    Plancke and S. J. Ides. 2008. Distribution and dynamics of intertidal macrobenthos predicted from
                    remote sensing: response to microphytobenthos and environment. Mar. Ecol. Prog. Ser. 367: 57-72.</p>

                <p class="body">Virnstein, R. W. 1977. The importance of predation by crabs and fishes on benthic
                    infauna in Chesapeake Bay. Ecol. 58: 1199-1217.</p>

                <p class="body">Watzin, M. 1983. The effects of meiofauna on settling macrofauna: meiofauna may
                    structure macrofaunal communities. Oecologia 59: 163-166.</p>

                <p class="body">Winkler, G. and W. Greve. 2004. Trophodynamics of two interacting species of estuarine
                    mysids, <em>Praunus flexuosus</em> and <em>Neomysis integer, </em>and their predation on the
                    calanoid copepod <em>Eurytemora affinis. </em>J. Exp. Mar. Biol. Ecol. 308: 127-146.</p>
            </td>
        </tr>
    </table>

    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td>
                <p class="footer_note">Report by: Joseph Dineen,
                    Smithsonian Marine Station at Fort Pierce<br>
                    Photos by: Joseph Dineen, unless otherwise specified. <br>
                    Photographic assistance in the field was graciously provided by Sherry Reed. <br>
                    Submit additional information, photos or comments
                    to:<br>
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
