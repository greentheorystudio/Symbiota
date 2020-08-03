<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Seagrass Habitats</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
        include(__DIR__ . '/../header.php');
		?>
		<div id="innertext">
            <h2>Seagrass Habitats</h2>
            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td align="center"><img src="../content/imglib/SeaGrass1.jpg" alt="" width="417" height="230" /></td>
                </tr>
            </table>
            <br />
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
                            sometimes confused with marine macroalgae; however <a href="ComparAlgae_Seagr.php"> closer examination</a> reveals significant differences. Structurally, seagrasses are
                            more closely related to terrestrial plants and, like terrestrial plants,
                            possess specialized tissues that perform specific tasks within each
                            plant. Conversely, algae are relatively simple and unspecialized in
                            structure. While algae possess only a tough holdfast that assists in
                            anchoring the plant to a hard substratum, seagrasses possess true roots
                            that not only hold plants in place, but also are specialized for
                            extracting minerals and other nutrients from the sediment. All algal
                            cells possess photosynthetic structures capable of utilizing sunlight to
                            produce chemical energy. <br />
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
                            (as measured in grams of carbon per square meter) of 182 - 730 g/C/m<sup>-2</sup>; <i>Syringodium filiforme</i> has an estimated annual production of 292 -
                            1095 g/C/m<sup>-2</sup>; and <i>Thalassia testudinum</i> has an
                            estimated annual production 329 - 5840 g/C/m<sup>-2</sup>. Blade
                            elongation in seagrasses averages 2-5&nbsp;mm per day in <i>Thalassia
                                testudinum</i>, 8.5 mm in <i>Syringodium filiforme</i>, and as much as
                            3.1&nbsp;mm in <i>Halodule wrightii</i>. In the Indian River Lagoon, <i> Halodule wrightii</i> has been shown to produce one new leaf every 9 days during
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
                            such as storms, excessive grazing by herbivores, disease, and anthropogenic threats due to point and non-point sources of pollution,
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
                        <p class="body">[A more detailed look at some emerging human-induced threats facing the seagrasses of the IRL is <a href="Seagrass_Emerging_Issues.php">available here</a>.]</p>
                        <p class="body">The health of seagrass communities obviously relies heavily upon the
                            amount of sunlight that penetrates the water column to reach submerged
                            blades. Water clarity, heavily affected by the amount and composition of
                            stormwater runoff and other non-point sources of pollution, is the
                            primary influence that determines how much light ultimately reaches
                            seagrass blades. Stormwater runoff drains both urban and agricultural
                            areas, and carries with it household chemicals, oils, automotive
                            chemicals, pesticides, animal wastes, and other debris. Under normal
                            conditions, seagrasses maintain water clarity by trapping silt, dirt,
                            and other sediments suspended in the water column.<br />
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
                            at manageable levels, and are an important food source for many filter feeding and suspension feeding organisms. However, excess nutrient loading in
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
                            seagrass coverage has remained unchanged over the last 50 years.<br />
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
                <tr class="heading">
                    <td colspan="3"><p class="label">IRL Seagrasses</p></td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Thalassia testudinum">Thalassia testudinum</a></i></span></td>
                    <td><span>Turtle grass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Halophila engelmannii">Halophila engelmannii</a></i></span></td>
                    <td><span>Star grass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Halophila decipiens">Halophila decipiens</a></i></span></td>
                    <td><span>Paddle grass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Halodule beaudettei">Halodule
                beaudettei</a></i></span></td>
                    <td><span>Shoal grass</span></td>
                    <td><span>formerly <i>H. wrightii</i></span></td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Halophila johnsonii">Halophila johnsonii</a></i></span></td>
                    <td><span>Johnson's seagrass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Syringodium filiforme">Syringodium filiforme</a></i></span></td>
                    <td><span>Manatee grass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Ruppia maritima">Ruppia maritima</a></i></span></td>
                    <td><span>Widgeon grass</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="heading">
                    <td colspan="3"><p class="label">Associated Invertebrates</p></td>
                </tr>
                <tr>
                    <td><span><i>Abra aequalis</i></span></td>
                    <td><span>Atlantic abra</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aceteocina atrata</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aceteocina canaliculata</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aequipecten muscosus</i></span></td>
                    <td><span>rough scallop</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus armillatus</i></span></td>
                    <td><span>banded snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus bouvieri</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus cristulifrons</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus floridanus</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus formosus</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Alpheus heterochaelis">Alpheus heterochaelis</a></i></span></td>
                    <td><span>common snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus normanni</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus nuttingi</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus paracinitus</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus thomasi</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Alpheus viridari</i></span></td>
                    <td><span>snapping shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Amphiodia pulchella</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Amphioplus thrombodes</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anadara brasiliana</i></span></td>
                    <td><span>incongruous ark</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anadara notabilis</i></span></td>
                    <td><span>eared ark</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anadara ovalis</i></span></td>
                    <td><span>blood ark</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anadara transversa</i></span></td>
                    <td><span>transverse ark</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anodontia alba</i></span></td>
                    <td><span>buttercup lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anomalocardia auberiana</i></span></td>
                    <td><span>pointed venus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anomia simplex</i></span></td>
                    <td><span>common jingle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anygdalum papyrium</i></span></td>
                    <td><span>Atlantic papermussel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aplysia brasiliana</i></span></td>
                    <td><span>sooty seahare</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aplysia dactylomela</i></span></td>
                    <td><span>spotted seahare</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Aplysia morio</i></span></td>
                    <td><span>Atlantic black seahare</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Arbacia punctulata</i></span></td>
                    <td><span>purple-spined sea urchin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Arenicola cristata</i></span></td>
                    <td><span>lugworm</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Argopecten irradians concentricus</i></span></td>
                    <td><span>bay scallop</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Asthenothaerus hemphilli</i></span></td>
                    <td><span>hemphill thracid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Astyris lunata</i></span></td>
                    <td><span>lunar dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Atrina rigida</i></span></td>
                    <td><span>stiff penshell</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Atrina seminuda</i></span></td>
                    <td><span>half-naked penshell</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Barleeia spp.</i></span></td>
                    <td><span>barleysnails</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Barnea truncata</i></span></td>
                    <td><span>Atlantic mud piddock</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Batillaria minima</i></span></td>
                    <td><span>West Indian false cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Bittiolum varium">Bittiolum varium</a></i></span></td>
                    <td><span>grass cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Boonea impressa</i></span></td>
                    <td><span>impressed odostome</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Brachidontes exustus</i></span></td>
                    <td><span>scorched mussel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Bulla striata">Bulla striata</a></i></span></td>
                    <td><span>striate bubble</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Bursatella leachii</i></span></td>
                    <td><span>ragged sea hare</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Busycon contrarium</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Busycon spiratum pyruloides</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Caecum cooperi</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Caecum pulchellum</i></span></td>
                    <td><span>beautiful caecum</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Callinectes sapidus">Callinectes sapidus</a></i></span></td>
                    <td><span>blue crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Capitella capitata">Capitella capitata</a></i></span></td>
                    <td><span>(polychaete)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Caprella penantis">Caprella penantis</a></i></span></td>
                    <td><span>(amphipod)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cardiomya gemma</i></span></td>
                    <td><span>precious cardiomya</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Carditamera floridana</i></span></td>
                    <td><span>broad-ribbed carditid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Cerithidea scalariformis">Cerithidea scalariformis</a></i></span></td>
                    <td><span>ladder hornsnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cerithiopsis greeni</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cerithium atratum</i></span></td>
                    <td><span>dark cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cerithium litteratum</i></span></td>
                    <td><span>stocky cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cerithium lutosum</i></span></td>
                    <td><span>variable cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cerithium muscarum</i></span></td>
                    <td><span>flyspeck cerith</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Chione cancellata</i></span></td>
                    <td><span>cross-barred venus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Chione grus</i></span></td>
                    <td><span>gray pygmy venus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Chione intapurpurea</i></span></td>
                    <td><span>lady-in-waiting venus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Circulus suppressus</i></span></td>
                    <td><span>suppressed vitrinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Circulus texanus</i></span></td>
                    <td><span>Texas vitrinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Codakia orbicularis</i></span></td>
                    <td><span>tiger lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Codakia orbiculata</i></span></td>
                    <td><span>dwarf tiger lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Corbula contracta</i></span></td>
                    <td><span>contracted corbula</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Corbula spp.</i></span></td>
                    <td><span>corbula</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Costoanachis avara</i></span></td>
                    <td><span>greedy dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Costoanachis floridana</i></span></td>
                    <td><span>Florida dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Costoanachis sparsa</i></span></td>
                    <td><span>sparse dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Crassinella spp.</i></span></td>
                    <td><span>crassinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Crassostrea virginica</i></span></td>
                    <td><span>Eastern oyster</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cratena pilata</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Crepidula convexa</i></span></td>
                    <td><span>convex slippersnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Crepidula fornicata</i></span></td>
                    <td><span>common Atlantic slippersnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Crepidula plana">Crepidula plana</a></i></span></td>
                    <td><span>Eastern white slippersnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cyclinella tenuis</i></span></td>
                    <td><span>thin cyclinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cyclostremiscus beauii</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cymadusa compta</i></span></td>
                    <td><span>(amphipod)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cymatium pileare</i></span></td>
                    <td><span>hairy triton</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Cyrtopleura costata">Cyrtopleura costata</a></i></span></td>
                    <td><span>angelwing clam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Diadema antillarum</i></span></td>
                    <td><span>longspine black sea urchin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Dinocaridium robustum</i></span></td>
                    <td><span>Atlantic giant cockle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Divaricella quadrisulcata</i></span></td>
                    <td><span>cross-hatched lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Divariscintilla luteocrinita</i></span></td>
                    <td><span>yellow-tentacled galeommatid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Divariscintilla octotentaculata</i></span></td>
                    <td><span>eight-tentacled galeommatid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Divariscintilla troglodytes</i></span></td>
                    <td><span>hole-dwelling galeommatid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Divariscintilla yoyo</i></span></td>
                    <td><span>yoyo galeommatid</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Donax variabilis</i></span></td>
                    <td><span>variable coquina</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Doridella obscura</i></span></td>
                    <td><span>obscure carambe</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Dosinia discus</i></span></td>
                    <td><span>disk dosinia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Dosinia elegans</i></span></td>
                    <td><span>elegant dosinia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Echinaster sentus</i></span></td>
                    <td><span>spiny sea star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Elysia chlorotica</i></span></td>
                    <td><span>eastern emerald elysia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Elysia serca</i></span></td>
                    <td><span>Caribbean seagrass elysia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Epitonium rupicola</i></span></td>
                    <td><span>brown-band wentletrap</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Erichsonella attenuata">Erichsonella attenuata</a></i></span></td>
                    <td><span>eelgrass isopod</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eupleura caudata</i></span></td>
                    <td><span>thick-lip drill</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eupleura sulcidentata</i></span></td>
                    <td><span>sharp-rib drill</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fasciolaria lilium hunteria</i></span></td>
                    <td><span>banded tulip</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fasciolaria tulipa</i></span></td>
                    <td><span>true tulip</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Finella dubia</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Gammarus mucronatus">Gammarus mucronatus</a></i></span></td>
                    <td><span>(amphipod)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gemma gemma</i></span></td>
                    <td><span>amethyst gemclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gouldia cerina</i></span></td>
                    <td><span>waxy gouldclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Grandidierella bonnieroides</i></span></td>
                    <td><span>amphipod</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Granulina ovuliformis</i></span></td>
                    <td><span>teardrop marginella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haminoea antillarum</i></span></td>
                    <td><span>Antilles glassy bubble</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haminoea elegans</i></span></td>
                    <td><span>elegant glassy bubble</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Hargeria rapax">Hargeria rapax</a></i></span></td>
                    <td><span>(tanaid)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Henrya morrisoni</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Henrya morrisoni</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Hippolyte pleuracantha</i></span></td>
                    <td><span>broken-back shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Holothuria arenicola</i></span></td>
                    <td><span>burrowing sea cucumber</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Holothuria cubana</i></span></td>
                    <td><span>cuban sea cucumber</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Holothuria grisea</i></span></td>
                    <td><span>gray sea cucumbers</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Hydatina physis</i></span></td>
                    <td><span>brown-line paperbubble</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Hydrobiidae unidentified spp.</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ilyanassa obsoleta</i></span></td>
                    <td><span>eastern mudsnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ircinia spp.</i></span></td>
                    <td><span>garlic sponges</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ischnochiton striolatus</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Istichopus badionotus</i></span></td>
                    <td><span>four-sided sea cucumber</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Laevicardium laevigatum</i></span></td>
                    <td><span>egg cockle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Laevicardium mortoni</i></span></td>
                    <td><span>morton eggcockle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Leptosynapta inhaerens</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Leptosynapta roseola</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Leptosynapta tenuis</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lima pellucida</i></span></td>
                    <td><span>Antillean fileclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lima spp.</i></span></td>
                    <td><span>fileclams</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Linga amiantus</i></span></td>
                    <td><span>miniature lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Linga pensylvanica</i></span></td>
                    <td><span>pennsylvania lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lioberus castaneus</i></span></td>
                    <td><span>chestnut mussel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lucina nassula</i></span></td>
                    <td><span>woven lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lucina pectinata</i></span></td>
                    <td><span>thick lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Luidia clathrata</i></span></td>
                    <td><span>gray seastar</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Luidia senegalenis</i></span></td>
                    <td><span>nine-armed sea star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lyonsia floridana</i></span></td>
                    <td><span>Florida lyonsia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lytechinus variegatus</i></span></td>
                    <td><span>short-spined sea urchin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Macoma spp.</i></span></td>
                    <td><span>macoma</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Macoma tenta</i></span></td>
                    <td><span>elongate macoma</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mactra fragilis</i></span></td>
                    <td><span>fragile surfclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Meioceras nitidum</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Melampus bidentatus</i></span></td>
                    <td><span>eastern melampus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Melanella spp.</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Melita nitida</i></span></td>
                    <td><span>amphipod</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Melongena sprucecreekensis</i></span></td>
                    <td><span>conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Melongena corona">Melongena corona</a></i></span></td>
                    <td><span>crown conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Menippe mercenaria</i></span></td>
                    <td><span>Stone crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mercenaria campechiensis</i></span></td>
                    <td><span>southern hard clam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Mercenaria mercenaria">Mercenaria mercenaria</a></i></span></td>
                    <td><span>northern hard clam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mercenaria mercenaria forma notata</i></span></td>
                    <td><span>northern hard clam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mitrella ocellata</i></span></td>
                    <td><span>whitespot dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Modiolus modiolus squamosus</i></span></td>
                    <td><span>horsemussel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Modulus modulus">Modulus modulus</a></i></span></td>
                    <td><span>buttonsnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mulinia lateralis</i></span></td>
                    <td><span>dwarf surfclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Musculus lateralis</i></span></td>
                    <td><span>lateral mussel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mysella planulata</i></span></td>
                    <td><span>plate mysella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mysella spp.</i></span></td>
                    <td><span>mysella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Nassarius acutus</i></span></td>
                    <td><span>sharp nassa</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Nassarius vibex</i></span></td>
                    <td><span>bruised nassa</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Natica livida</i></span></td>
                    <td><span>livid moonsnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Natica macrochinensis</i></span></td>
                    <td><span>Morocco moonsnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Natica pusilla</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Neanthes succinea">Neanthes succinea</a></i></span></td>
                    <td><span>clam worm</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Nerita fulgurans</i></span></td>
                    <td><span>Antillean nerite</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Neritina virginea</i></span></td>
                    <td><span>virgin nerite</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Noetia ponderosa</i></span></td>
                    <td><span>ponderous ark</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Nucula proxima</i></span></td>
                    <td><span>Atlantic nutclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Octopus vulgaris</i></span></td>
                    <td><span>common octopus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Odostomia engonia</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Oliva sayana</i></span></td>
                    <td><span>lettered olive</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Olivella floralia</i></span></td>
                    <td><span>rice olive</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Amphiodia pulchella</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Onuphis microcephala</i></span></td>
                    <td><span>parchment worm</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ophiactis savignyi</i></span></td>
                    <td><span>savigny's brittle star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ophionereis reticulata</i></span></td>
                    <td><span>reticulated brittle star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Ophiophragmus filograneus">Ophiophragmus filograneus</a></i></span></td>
                    <td><span>(brittlestar)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Ophiothrix angulata</i></span></td>
                    <td><span>angular brittle star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Oreaster reticulata</i></span></td>
                    <td><span>cushion star</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Oxynoe antillaum</i></span></td>
                    <td><span>Antilles oxynoe</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Oxynoe azuropunctata</i></span></td>
                    <td><span>Blue-spot oxynoe</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pagurus bonairensis</i></span></td>
                    <td><span>hermit crabs</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pagurus brevidactylus</i></span></td>
                    <td><span>short-clawed hermit crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pagurus carolinensis</i></span></td>
                    <td><span>hermit crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Pagurus longicarpus">Pagurus longicarpus</a></i></span></td>
                    <td><span>long-armed hermit crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pagurus maclaughlinae</i></span></td>
                    <td><span>hermit crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pagurus pollicaris</i></span></td>
                    <td><span>flat-clawed hermit crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Palaemontes intermedius</i></span></td>
                    <td><span>grass shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Palaemonetes pugio">Palaemonetes pugio</a></i></span></td>
                    <td><span>daggerblade grass shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Palaemonetes vulgaris</i></span></td>
                    <td><span>grass shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pandora spp.</i></span></td>
                    <td><span>pandora</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Panulirus argus</i></span></td>
                    <td><span>spiny lobster</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Papyridea soleniformis</i></span></td>
                    <td><span>spiny papercockle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Paracaudina chiliensis obesacauda</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Parastarte triquetra</i></span></td>
                    <td><span>brown gemclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Parvanachis obesa</i></span></td>
                    <td><span>fat dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Parvilucina multilineata</i></span></td>
                    <td><span>many-lined lucine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pecten ziczac</i></span></td>
                    <td><span>zigzag scallop</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Pectinaria gouldii">Pectinaria gouldii</a></i></span></td>
                    <td><span>ice cream cone worm</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Penaeus aztecus</i></span></td>
                    <td><span>Brown shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Penaeus duorarum</i></span></td>
                    <td><span>pink shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Penaeus setiferus</i></span></td>
                    <td><span>white shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Periclimenes americanus</i></span></td>
                    <td><span>cleaning shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Periclimenes chacei</i></span></td>
                    <td><span>cleaning shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Periclimenes longicaudatus</i></span></td>
                    <td><span>cleaning shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Periploma margaritaceum</i></span></td>
                    <td><span>unequal spoonclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Petricola pholadiformis</i></span></td>
                    <td><span>false angelwing clam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pholas campechiensis</i></span></td>
                    <td><span>Campeche angelwing</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Phyllaplysia smaragda</i></span></td>
                    <td><span>emerald leaf slug</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Phyllonotus pomum</i></span></td>
                    <td><span>apple murex</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pinctada imbricata</i></span></td>
                    <td><span>Atlantic pearl oyster</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pinna carnea</i></span></td>
                    <td><span>amber penshell</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pitar fulminatus</i></span></td>
                    <td><span>lightning pitar</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pleuroploca gigantea</i></span></td>
                    <td><span>Florida horse conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Polinices duplicatus</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Polycera hummi</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Prunum apicinum</i></span></td>
                    <td><span>common Atlantic marginella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pteria colymbus</i></span></td>
                    <td><span>Atlantic wing oyster</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pyramidella crenulata</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Pyrgocythara plicosa</i></span></td>
                    <td><span>plicate mangelia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Raeta plicatella</i></span></td>
                    <td><span>channeled duckclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Rictaxis punctostriatus</i></span></td>
                    <td><span>pitted baby bubble</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Rissoina catesbyana</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sayella crosseana</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Semele proficua</i></span></td>
                    <td><span>Atlantic semele</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sicyonia dorsalis</i></span></td>
                    <td><span>rock shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sicyonia laevigata</i></span></td>
                    <td><span>rock shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sinum perspecivum</i></span></td>
                    <td><span>white baby ear</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Siphonaria pectinata</i></span></td>
                    <td><span>striped false limpet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Smaragdia viridis</i></span></td>
                    <td><span>emerald nerite</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Smaragdia viridis viridemaris</i></span></td>
                    <td><span>emerald nerite</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Solemya occidentalis</i></span></td>
                    <td><span>West Indian awningclam</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sphenia antillensis</i></span></td>
                    <td><span>antillean sphenia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><a href="../taxa/index.php?taxon=Spirorbis"><em>Spirorbis</em> spp.</a></span></td>
                    <td><span>(polychaete)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Stellatoma stellata</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Streblospio benedicti">Streblospio benedicti</a></i></span></td>
                    <td><span>(polychaete)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strombus alatus</i></span></td>
                    <td><span>Florida fighting conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strombus costatus</i></span></td>
                    <td><span>milk conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strombus gigas</i></span></td>
                    <td><span>queen conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strombus raninus</i></span></td>
                    <td><span>hawkwing conch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Stylocheilus longicauda</i></span></td>
                    <td><span>blue-ring sea hare</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Suturoglypta iontha</i></span></td>
                    <td><span>lineate dovesnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Synaptula hydriformis">Synaptula hydriformis</a></i></span></td>
                    <td><span>(sea cucumber)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tagelus divisus</i></span></td>
                    <td><span>purplish tagelus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tagelus plebeius</i></span></td>
                    <td><span>stout tagelus</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Teinostoma biscaynense</i></span></td>
                    <td><span>Biscayne vitrinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellidora cristata</i></span></td>
                    <td><span>white-crest tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina aequistriata</i></span></td>
                    <td><span>striate tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina aequistriata</i></span></td>
                    <td><span>striate tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina alternata</i></span></td>
                    <td><span>altenate tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina fausta</i></span></td>
                    <td><span>favored tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina laevigata</i></span></td>
                    <td><span>smooth tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina listeri</i></span></td>
                    <td><span>speckled tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina magna</i></span></td>
                    <td><span>great tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina mera</i></span></td>
                    <td><span>pure tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina paramera</i></span></td>
                    <td><span>perfect tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina radiata</i></span></td>
                    <td><span>sunrise tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina tampaensis</i></span></td>
                    <td><span>Tampa tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tellina versicolor</i></span></td>
                    <td><span>many-colored tellin</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Thais haemastoma floridana</i></span></td>
                    <td><span>Florida rocksnail</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Thor dobkini</i></span></td>
                    <td><span>(shrimp)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Thor manningi</i></span></td>
                    <td><span>(shrimp)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Thyonella gemmata</i></span></td>
                    <td><span>green sea cucumber</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tozeuma carolinense</i></span></td>
                    <td><span>(shrimp)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Trachycardium egmontianum</i></span></td>
                    <td><span>Florida pricklycokle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Trachycardium muricatum</i></span></td>
                    <td><span>yellow pricklycockle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tricolia affinis pterocladica</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Triphora nigrocincta</i></span></td>
                    <td><span>black-line triphora</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tripneustes ventricosus</i></span></td>
                    <td><span>sea egg</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Truncatella pulchella</i></span></td>
                    <td><span>beautiful truncatella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Turbo castanea</i></span></td>
                    <td><span>chestnut turban</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Turbonilla dalli</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Turbonilla hemphilli</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Turbonilla incisa</i></span></td>
                    <td><span>none</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca burgersi</i></span></td>
                    <td><span>Burger's fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca phayeri</i></span></td>
                    <td><span>fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca pugillator</i></span></td>
                    <td><span>sand fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca pugnax rapax</i></span></td>
                    <td><span>mud fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca rapax</i></span></td>
                    <td><span>Caribbean fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca rapax rapax</i></span></td>
                    <td><span>Caribbean fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca speciosa</i></span></td>
                    <td><span>Ive's fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Uca thayeri</i></span></td>
                    <td><span>Thayer's fiddler crab</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Urosalpinx cinerea</i></span></td>
                    <td><span>Atlantic oyster drill</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Urosalpinx tampaensis</i></span></td>
                    <td><span>Tampa drill</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Vitrinella floridana</i></span></td>
                    <td><span>Florida vitrinella</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Zebina browniana</i></span></td>
                    <td><span>smooth risso</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr class="heading">
                    <td colspan="3"><p class="label">Associated Vertebrates</p></td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Achirus lineatus">Achirus lineatus</a></i></span></td>
                    <td><span>lined sole</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Albula vulpes">Albula vulpes</a></i></span></td>
                    <td><span>bonefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anchoa cubana</i></span></td>
                    <td><span>Cuban anchovy</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Anchoa hepsetus">Anchoa hepsetus</a></i></span></td>
                    <td><span>striped anchovy</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anchoa lamprotaenia</i></span></td>
                    <td><span>bigeye anchovy</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Anchoa lyolepis</i></span></td>
                    <td><span>dusky anchovy</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Anchoa mitchilli">Anchoa mitchilli</a></i></span></td>
                    <td><span>bay anchovy</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Archosargus probatocephalus">Archosargus probatocephalus</a></i></span></td>
                    <td><span>sheepshead</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Archosargus rhomboidalis</i></span></td>
                    <td><span>sea bream</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Ariopsis felis">Ariopsis felis</a></i></span></td>
                    <td><span>sea catfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Bairdiella chrysoura">Bairdiella chrysoura</a></i></span></td>
                    <td><span>silver perch</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Balistes capriscus">Balistes capriscus</a></i></span></td>
                    <td><span>Gray triggerfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Brevoortia tyrannus</i></span></td>
                    <td><span>Atlantic menhaden</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Brevoortia smithi">Brevoortia smithi</a></i></span></td>
                    <td><span>yellowfin menhaden</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Caretta caretta">Caretta caretta</a></i></span></td>
                    <td><span>loggerhead sea turtle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Centropomus undecimalis">Centropomus undecimalis</a></i></span></td>
                    <td><span>snook</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Chaetodipterus faber">Chaetodipterus faber</a></i></span></td>
                    <td><span>spadefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Chelonia mydas">Chelonia mydas</a></i></span></td>
                    <td><span>green sea turtle</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Chilomycterus schoepfii">Chilomycterus schoepfii</a></i></span></td>
                    <td><span>striped burrfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cynoscion arenarius</i></span></td>
                    <td><span>sand seatrout</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Cynoscion nebulosus">Cynoscion nebulosus</a></i></span></td>
                    <td><span>spotted seatrout</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Cyprinodon variegatus</i></span></td>
                    <td><span>sheepshead minnow</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Dasyatus americana">Dasyatus americana</a></i></span></td>
                    <td><span>southern stingray</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Dasyatus sabina">Dasyatus sabina</a></i></span></td>
                    <td><span>Atlantic stingray</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Dasyatis sayi</i></span></td>
                    <td><span>bluntnose stingrays</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Diapterus auratus</i></span></td>
                    <td><span>Irish pompano</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Elops saurus">Elops saurus</a></i></span></td>
                    <td><span>ladyfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus argentus</i></span></td>
                    <td><span>spotfin mojarra</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus gula</i></span></td>
                    <td><span>silver jenny</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus havana</i></span></td>
                    <td><span>bigeye mojara</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus jonesii</i></span></td>
                    <td><span>slender mojarra</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus lefroyi</i></span></td>
                    <td><span>mottled mojara</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Eucinostomus melanopteus</i></span></td>
                    <td><span>flagfin mojarra</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Floridichthys caprio</i></span></td>
                    <td><span>goldspot killifish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fundulus chrysotus</i></span></td>
                    <td><span>golden topminnow</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fundulus confluentus</i></span></td>
                    <td><span>marsh killifish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fundulus grandis</i></span></td>
                    <td><span>Gulf killifish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fundulus heteroclitus</i></span></td>
                    <td><span>mummichog</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Fundulus similis</i></span></td>
                    <td><span>longnose killifish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Gambusia affinis">Gambusia affinis</a></i></span></td>
                    <td><span>mosquitofish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gambusia holbrooki</i></span></td>
                    <td><span>eastern mosquitofish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus boleosoma</i></span></td>
                    <td><span>darter goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus fasciatus</i></span></td>
                    <td><span>blackbar goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus oceanicus</i></span></td>
                    <td><span>highfin goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus pseudofasciatus</i></span></td>
                    <td><span>slashcheek goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionelus schufeldti</i></span></td>
                    <td><span>freshwater goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellussmaragdus</i></span></td>
                    <td><span>emerald goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus stigmaticus</i></span></td>
                    <td><span>marked goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobionellus stigmaturus</i></span></td>
                    <td><span>spotfin goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobiosoma macrodon</i></span></td>
                    <td><span>tiger goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobiosoma bosc</i></span></td>
                    <td><span>naked goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Gobiosoma ginsburgi</i></span></td>
                    <td><span>seaboard goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Gobiosoma robustrum">Gobiosoma robustrum</a></i></span></td>
                    <td><span>code goby</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon album</i></span></td>
                    <td><span>margate</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Haemulon aurolineatum">Haemulon aurolineatum</a></i></span></td>
                    <td><span>tomtate</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon carbonarium</i></span></td>
                    <td><span>Caesar grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon chrysargyreum</i></span></td>
                    <td><span>smallmouth grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon flavolineatum</i></span></td>
                    <td><span>French grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon macrostomum</i></span></td>
                    <td><span>Spanish grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon melanurum</i></span></td>
                    <td><span>cottonwick</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Haemulon plumierii">Haemulon plumierii</a></i></span></td>
                    <td><span>white grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon sciurus</i></span></td>
                    <td><span>bluestriped grunt</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Haemulon parra</i></span></td>
                    <td><span>sailor's choice</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Harengula clupeola</i></span></td>
                    <td><span>false pilchard</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Harengula jaguana">Harengula jaguana</a></i></span></td>
                    <td><span>scaled sardine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Hippocampus erectus</i></span></td>
                    <td><span>lined seahorse</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Hippocampus reidi</i></span></td>
                    <td><span>longsnout seahorse</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Hippocampus zosterae">Hippocampus zosterae</a></i></span></td>
                    <td><span>dwarf seahorse</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lachnolaimus maximus">Lachnolaimus maximus</a></i></span></td>
                    <td><span>hogfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lactophyrs polygonia</i></span></td>
                    <td><span>honeycomb trunkfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lactophyrs quadricornis</i></span></td>
                    <td><span>scrawled trunkfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lactophyrs trigonus</i></span></td>
                    <td><span>trunkfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lactophyrs triqueter</i></span></td>
                    <td><span>smooth trunkfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lagodon rhomboides">Lagodon rhomboides</a></i></span></td>
                    <td><span>pinfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Leostomus xanthurus">Leostomus xanthurus</a></i></span></td>
                    <td><span>spot</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Lucania parva</i></span></td>
                    <td><span>rainwater killifish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus analis">Lutjanus analis</a></i></span></td>
                    <td><span>mutton snapper</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus apodus">Lutjanus apodus</a></i></span></td>
                    <td><span>schoolmaster</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus cyanopterus">Lutjanus cyanopterus</a></i></span></td>
                    <td><span>cubera snapper</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus griseus">Lutjanus griseus</a></i></span></td>
                    <td><span>gray snapper (or mangrove)</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus jocu">Lutjanus jocu</a></i></span></td>
                    <td><span>dog snapper</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus mahogoni">Lutjanus mahogoni</a></i></span></td>
                    <td><span>mahogany snapper</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Lutjanus synagris">Lutjanus synagris</a></i></span></td>
                    <td><span>lane snapper</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Megalops atlanticus">Megalops atlanticus</a></i></span></td>
                    <td><span>tarpon</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Membras martinica</i></span></td>
                    <td><span>rough silverside</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Menidia beryllina</i></span></td>
                    <td><span>tidewater silversides</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Menida peninsulae</i></span></td>
                    <td><span>penninsula silverside</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Micropogonias undulatus">Micropogonias undulatus</a></i></span></td>
                    <td><span>Atlantic croaker</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Monacanthus hipsidus</i></span></td>
                    <td><span>planehead filefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Monacanthus ciliatus</i></span></td>
                    <td><span>fringed filefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Mugil cephalus">Mugil cephalus</a></i></span></td>
                    <td><span>striped mullet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mugil curema</i></span></td>
                    <td><span>white mullet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mugil curvidens</i></span></td>
                    <td><span>mullet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mugil gaimardianus</i></span></td>
                    <td><span>redeye mullet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mugil gyrans</i></span></td>
                    <td><span>fantail mullet</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Mugil liza</i></span></td>
                    <td><span>liza</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Oligoplites saurus">Oligoplites saurus</a></i></span></td>
                    <td><span>leatherjacket</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Opisthnema oglinum">Opisthnema oglinum</a></i></span></td>
                    <td><span>Atlantic threadfin herring</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Opsanus tao</i></span></td>
                    <td><span>oyster toadfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Palaemontes spp.</i></span></td>
                    <td><span>grass shrimp</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Poecilia latipinna</i></span></td>
                    <td><span>sailfin molly</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Pogonias cromis">Pogonias cromis</a></i></span></td>
                    <td><span>Black drum</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Sardinella aurita">Sardinella aurita</a></i></span></td>
                    <td><span>Spanish sardine</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Sciaenops ocellatus">Sciaenops ocellatus</a></i></span></td>
                    <td><span>red drum</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Scomberomerus cavalla">Scomberomerus cavalla</a></i></span></td>
                    <td><span>king mackerel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Scomberomerus maculatus">Scomberomerus maculatus</a></i></span></td>
                    <td><span>Spanish mackerel</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Scorpaena brasilensis</i></span></td>
                    <td><span>barbfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Scorpaena dispar</i></span></td>
                    <td><span>hunchback scorpionfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Scorpaena grandicornis</i></span></td>
                    <td><span>plumed scorpionfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Scorpaena plumieri</i></span></td>
                    <td><span>spotted scorpionfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sparisoma chrysopterum</i></span></td>
                    <td><span>redtail parrotfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sparisoma radians</i></span></td>
                    <td><span>bucktooth parrotfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sparisoma rubripinne</i></span></td>
                    <td><span>redfin parrotfish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sphoeroides maculatus</i></span></td>
                    <td><span>northern puffer</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Sphoeroides nephelus">Sphoeroides nephelus</a></i></span></td>
                    <td><span>southern puffer</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sphoeroides spengleri</i></span></td>
                    <td><span>bandtail puffer</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sphoeroides testudineus</i></span></td>
                    <td><span>checkered puffer</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Sphyraena barracuda">Sphyraena barracuda</a></i></span></td>
                    <td><span>great barracuda</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Strongylura marina">Strongylura marina</a></i></span></td>
                    <td><span>Atlantic needlefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strongylura notata</i></span></td>
                    <td><span>redfin needlefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Strongylura timucu</i></span></td>
                    <td><span>Timucu</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sygnathus floridae</i></span></td>
                    <td><span>dusky pipefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Sygnathus louisianae</i></span></td>
                    <td><span>chain pipefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Sygnathus scovelli">Sygnathus scovelli</a></i></span></td>
                    <td><span>Gulf pipefish</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Tilapia spp.</i></span></td>
                    <td><span>Tilapia</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Trachinotus carolinus">Trachinotus carolinus</a></i></span></td>
                    <td><span>Florida pompano</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i>Trichecus manatus</i></span></td>
                    <td><span>West Indian manatee</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td><span><i><a href="../taxa/index.php?taxon=Tursiops trucatus">Tursiops trucatus</a></i></span></td>
                    <td><span>bottlenosed dolphin</span></td>
                    <td>&nbsp;</td>
                </tr>
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
                        <p class="body">Eiseman, N.J. 1980. An Illustrated Guide to the Sea Grasses of the Indian River Region of<br>
                            Florida. Harbor Branch Foundation, Inc. Technical Report No. 31.&nbsp;
                            24 pages.</p>
                        <p class="body">Fenchel, T. 1970. Studies on the decomposition of organic matter derived from turtle grass,<br>
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
                            Florida.  Florida Marine Research Publications 42, 139-151.</p>
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
                        <p class="body">Packard, J. M. 1984. Impact of manatees <i>Trichecus manatus</i> on seagrass communities in<br>
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
                            <i>Cymodoceum manatorum</i>.  Master's Thesis, Fla.
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
                            U.S.A.: A Review.  Florida Marine Research Publications 42, 89-116.</p>
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
                            FWS/OBS-82/25.  158 Pages.</p>
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
