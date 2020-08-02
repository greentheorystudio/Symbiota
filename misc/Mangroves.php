<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html lang="<?php echo $DEFAULT_LANG; ?>">
	<head>
		<title>Mangrove Habitats</title>
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
            <h2>Mangrove Habitats</h2>
            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td align="center"><img src="../content/imglib/R_mangle_Main.jpg" width="500" height="667"></td>
                </tr>
            </table>
            <br />
            <br />
            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td align="center"><img src="../content/imglib/mangrove_page1.jpg" width="250" height="188"><img src="../content/imglib/mangrove_page3.jpg" width="250" height="188" /></td>
                </tr>
            </table>
            <br />
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
                                    mangle</a></i>, the black mangrove, <i><a href="../taxa/index.php?taxon=Avicennia germinans">Avicennia
                                    germinans</a></i>, and the white mangrove, <i><a href="../taxa/index.php?taxon=Laguncularia racemosa">Laguncularia racemosa</a></i>.
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
                        <p class="title"><em>Root  Aeration</em></p>
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
                            C/m<sup>-2</sup> day <sup>-1</sup> (Lugo and Snedaker 1974, Lugo <i>et al.</i> 1976). Red mangroves have the
                            highest production rates, followed by black mangroves and white mangroves (Lugo <i>et al.</i> 1976). Black mangroves have been shown to have higher respiration
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
                            Leaf fall in Florida mangroves was estimated to be 2.4 dry g m<sup>-2</sup> day <sup>-1</sup> on average, with significant variation depending on the site (Heald
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
                            content within the leaf all increase.<br />
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
                            cycle of high runoff/low salinity followed by low runoff/high salinity led Pool <i>et al.</i> (1977) to suggest that riverine mangrove forests are the most
                            highly productive of the mangrove communities.</p>
                        <p class="title"><em>Basin  Mangrove Forests</em></p>
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
                            roosting, breeding, and other activities.&nbsp;<br />
                            <br />
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
                                Pachygrapsus transverses, and Sesarma </i>spp., the isopod <i>Ligea exotica,</i> and many species of insects. Birds also constitute a major component of this
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
                            dogwoods (<i>Piscidia</i> spp.), oaks (<i>Quercus</i> spp.), red bay (<i>Persea</i> sp.), gumbo limbo (<i>Bersera simaruba</i>), mastic (<i>Mastichodendron</i> sp.), figs (<i>Ficus</i> spp.) and stoppers (<i>Eugenia</i> spp.). Also
                            included are the various species of bromeliads, orchids, ferns, and other
                            epiphytes that utilize upland trees for support and shelter. Animals of this
                            spatial guild, primarily birds and winged insects, often reside in the upland
                            community, but migrate to feeding areas located in mangroves. Common upland
                            arboreal animals include jays, wrens, woodpeckers, warblers, gnatcatchers,
                            skinks, anoles, snakes, and tree snails.</p>
                        <p class="body">Finally,
                            the upland terrestrial community is associated with the understory
                            of tropical hardwood forests. The most common members of this
                            guild include various snakes, hispid cotton rats (<i>Sigmodon</i> sp.), raccoons (<i>Procyon lotor</i>), white-tailed deer (<i>Odocoileusus
                                virginianus</i>), bobcats (<i>Felis rufus</i>), gray fox (<i>Urocyon
                                cinereoargenteus</i>), and many insect species. Many of the
                            animals in this spatial guild enter mangrove forests daily for feeding,
                            but return to the upland community at other times.></p>
                        <p class="body">The following table is an abbreviated list of mangrove species.</p>
                        <p class="title">Select
                            highlighted links below to learn more about individual species.</p>
                        &nbsp;</td>
                </tr>
            </table>
            <table style="border:0;width:700px;margin-left:auto;margin-right:auto;" cellspacing="3" cellpadding="5" class="table-border no-border alternate">
                <tr>
                    <th>Scientific Name</th>
                    <th>Common Name</th>
                </tr>
                <tr class="heading">
                    <td colspan="2"><p class="label">Plants</p></td>
                </tr>
                <tr>
                    <td><span><i> Acrostichum danaeifolium</i></span></td>
                    <td><span>Mangrove
                    fern</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Avicennia germinans">Avicennia germinans</a></i></span></td>
                    <td><span>Black
                    mangrove</span></td>
                </tr>
                <tr>
                    <td><span><i>Batis
                    maritima</i></span></td>
                    <td><span>Saltwart</span></td>
                </tr>
                <tr>
                    <td><span><i> Borrichia frutescens</i></span></td>
                    <td><span>Sea
                    Ox-eye</span></td>
                </tr>
                <tr>
                    <td><span><i> Casuarina equistifolia</i></span></td>
                    <td><span> Australian pine</span></td>
                </tr>
                <tr>
                    <td><span><i> Conocarpus erecta</i></span></td>
                    <td><span> Buttonwood</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Halodule wrightii">Halodule wrightii</a></i></span></td>
                    <td><span> Shoalgrass</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Halophila decipiens">Halophila
                    decipiens</a></i></span></td>
                    <td><span> Paddlegrass</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Halophila englemanni">Halophila englemanni</a></i></span></td>
                    <td><span>Star
                    grass</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Halophila johnsonii">Halophila johnsonii</a></i></span></td>
                    <td><span> Johnson's seagrass</span></td>
                </tr>
                <tr>
                    <td><span><i> Juncus roemerianus</i></span></td>
                    <td><span>Black
                    needlerush</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Laguncularia racemosa">Laguncularia racemosa</a></i></span></td>
                    <td><span>White
                    mangrove</span></td>
                </tr>
                <tr>
                    <td><span><i> Limonium carolinianum</i></span></td>
                    <td><span>Sea
                    lavender</span></td>
                </tr>
                <tr>
                    <td><span><i> Melaleuca quinquenervia</i></span></td>
                    <td><span> Melaleuca</span></td>
                </tr>
                <tr>
                    <td><span><i> Monarda punctata</i></span></td>
                    <td><span>Spotted
                    beebalm</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Rhizophora mangle">Rhizophora mangle</a></i></span></td>
                    <td><span>Red
                    mangrove</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Ruppia maritima">Ruppia maritima</a></i></span></td>
                    <td><span>Widgeon
                    grass</span></td>
                </tr>
                <tr>
                    <td><span><i> Salicornia bigelovii</i></span></td>
                    <td><span>Annual
                    glasswart</span></td>
                </tr>
                <tr>
                    <td><span><i> Salicornia virginica</i></span></td>
                    <td><span> Perennial glasswart</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Schinus terebinthifolia">Schinus terebinthifolia</a></i></span></td>
                    <td><span> Brazilian pepper</span></td>
                </tr>
                <tr>
                    <td><span><i> Suaeda linearis</i></span></td>
                    <td><span>Sea
                    blite</span></td>
                </tr>
                <tr>
                    <td><span><i>Sueda
                    maritima</i></span></td>
                    <td><span>Sea
                    blite</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Syringodium filiforme">Syringodium filiforme</a></i></span></td>
                    <td><span>Manatee
                    grass</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Thalassia testudinium">Thalassia testudinium</a></i></span></td>
                    <td><span> Turtlegrass</span></td>
                </tr>
                <tr>
                    <td><span><i> Verbesina virginica</i></span></td>
                    <td><span>White
                    crownbeard</span></td>
                </tr>
                <tr class="heading">
                    <td colspan="2"><p class="label">Algae &amp; Other Protists</p></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Acanthophora spicifera">Acanthophora spicifera</a></i></span></td>
                    <td><span>Red
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Anacystis montana</i></span></td>
                    <td><span>Cyanobacteria</span></td>
                </tr>
                <tr>
                    <td><span><i> Anadyomena sp.</i></span></td>
                    <td><span>Green
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Caulerpa sertularoides</i></span></td>
                    <td><span>Green
                    feather alga</font></span></td>
                </tr>
                <tr>
                    <td><span><i> Caulerpa spp.</i></span></td>
                    <td><span>Green
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i>Chaetoceros
                    anastomosans</i></span></td>
                    <td><span>Diatom</span></td>
                </tr>
                <tr>
                    <td><span><i> Chaetoceros spp.</i></span></td>
                    <td><span>Diatom</span></td>
                </tr>
                <tr>
                    <td><span><i> Chaetomorpha linum</i></span></td>
                    <td><span>Green
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Cladophoropsis membranacea</i></span></td>
                    <td><span>Green
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Cryptoperidinopsis spp.</i></span></td>
                    <td><span>Dinoflagellates</span></td>
                </tr>
                <tr>
                    <td><span><i> Derbesia vaucheriaeformis</i></span></td>
                    <td><span>Green
                    alga &nbsp;</span></td>
                </tr>
                <tr>
                    <td><span><i> Enteromorpha spp.</i></span></td>
                    <td><span>Green
                    algae</span></td>
                </tr>
                <tr>
                    <td><span><i> Gonyaulax monilata</i></span></td>
                    <td><span>Dinoflagellate&nbsp;</span></td>
                </tr>
                <tr>
                    <td><span><i> Gracilaria spp.</i></span></td>
                    <td><span>Red
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Gymnodidium pulchellum</i></span></td>
                    <td><span>Dinoflagellate</span></td>
                </tr>
                <tr>
                    <td><span><i> Halimeda discoidea</i></span></td>
                    <td><span>Green
                    alga</span></td>
                </tr>
                <tr>
                    <td><span><i> Hypnea spp..</i></span></td>
                    <td><span>Red
                    algae</span></td>
                </tr>
                <tr>
                    <td><span><i> Lyngbya lutea</i></span></td>
                    <td><span>Cyanobacteria</span></td>
                </tr>
                <tr>
                    <td><span><i> Nitzchia spp.</i></span></td>
                    <td><span>Diatoms</span></td>
                </tr>
                <tr>
                    <td><span><i> Paralia spp.</i></span></td>
                    <td><span>Diatoms</span></td>
                </tr>
                <tr>
                    <td><span><i> Phormidium crosbyanum</i></span></td>
                    <td><span>Cyanobacteria</span></td>
                </tr>
                <tr>
                    <td><span><i> Polysiphonia sp.</i></span></td>
                    <td><span>Red
                    algae</span></td>
                </tr>
                <tr>
                    <td><span><i> Scrippsiella subsalsa</i></span></td>
                    <td><span>Dinoflagellate</span></td>
                </tr>
                <tr>
                    <td><span><i> Skeletonema costatum</i></span></td>
                    <td><span>Diatom</span></td>
                </tr>
                <tr>
                    <td><span><i> Spirulina sp.</i></span></td>
                    <td><span>Cyanobacteria</span></td>
                </tr>
                <tr>
                    <td><span><i> Thalssiosira spp.</i></span></td>
                    <td><span>Diatoms</span></td>
                </tr>
                <tr>
                    <td><span><i>Ulva
                    spp.</i></span></td>
                    <td><span>Green
                    algae</span></td>
                </tr>
                <tr class="heading">
                    <td colspan="2"><p class="label">Animals</p></td>
                </tr>
                <tr>
                    <td><span><i> Abudefduf saxatilus</i></span></td>
                    <td><span>Sergeant
                    major</span></td>
                </tr>
                <tr>
                    <td><span><i> Acetes americanus</i></span></td>
                    <td><span>Aviu
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Achirus lineatus</i></span></td>
                    <td><span>Lined
                    sole</span></td>
                </tr>
                <tr>
                    <td><span><i> Acteocina canaliculata</i></span></td>
                    <td><span> Cahnneled barrel-bubble</span></td>
                </tr>
                <tr>
                    <td><span><i> Aiptasia pallida</i></span></td>
                    <td><span>Pale
                    anemone</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Ajaia ajaia">Ajaia ajaia</a></i></span></td>
                    <td><span>Roseate
                    spoonbill</span></td>
                </tr>
                <tr>
                    <td><span><i> Alligator mississipensis</i></span></td>
                    <td><span>American
                    alligator</span></td>
                </tr>
                <tr>
                    <td><span><i> Alpheus armillatus </i></span></td>
                    <td><span>Banded
                    snapping shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Alpheus heterochaelis </i></span></td>
                    <td><span>Common
                    snapping shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Amygdalum papyrum</i></span></td>
                    <td><span>Atlantic
                    papermussel</span></td>
                </tr>
                <tr>
                    <td><span><i> Anachis semiplicata</i></span></td>
                    <td><span>Gulf
                    dovesnail</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    acuta </i></span></td>
                    <td><span>Northern
                    pintail</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    americana</i></span></td>
                    <td><span>American
                    widgeon</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    clypeata</i></span></td>
                    <td><span>Northern
                    shoveler</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    crecca</i></span></td>
                    <td><span> Green-winged teal</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    discors</i></span></td>
                    <td><span> blue-winged teals</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    fulvigula</i></span></td>
                    <td><span>Mottled
                    duck</span></td>
                </tr>
                <tr>
                    <td><span><i>Anas
                    spp.</i></span></td>
                    <td><span>Dabbling
                    ducks</span></td>
                </tr>
                <tr>
                    <td><span><i> Anchoa cubana</i></span></td>
                    <td><span>Cuban
                    anchovy</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Anchoa hepsetus">Anchoa hepsetus</a></i></span></td>
                    <td><span>Striped
                    anchovy</span></td>
                </tr>
                <tr>
                    <td><span><i> Anchoa lyolepis</i></span></td>
                    <td><span>Dusky
                    anchovy</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Anchoa mitchelli">Anchoa mitchelli</a></i></span></td>
                    <td><span>Bay
                    anchovy</span></td>
                </tr>
                <tr>
                    <td><span><i> Anguilla rostrata</i></span></td>
                    <td><span>American
                    eel</span></td>
                </tr>
                <tr>
                    <td><span><i> Anhinga anhinga</i></span></td>
                    <td><span>Anhinga</span></td>
                </tr>
                <tr>
                    <td><span><i> Anomalocardia auberiana</i></span></td>
                    <td><span>Pointed
                    venus</span></td>
                </tr>
                <tr>
                    <td><span><i> Apalone ferox</i></span></td>
                    <td><span>Florida
                    softshelled turtle</span></td>
                </tr>
                <tr>
                    <td><span><i>Arca
                    imbricata</i></span></td>
                    <td><span>Mossy
                    ark</span></td>
                </tr>
                <tr>
                    <td><span><i> Aratus pisoni</i></span></td>
                    <td><span>Mangrove
                    crab</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Archosargus probatocephalus">Archosargus probatocephalus</a></i></span></td>
                    <td><span> Sheepshead</span></td>
                </tr>
                <tr>
                    <td><span><i> Archosargus rhomboidalis</i></span></td>
                    <td><span>Sea
                    bream</span></td>
                </tr>
                <tr>
                    <td><span><i> Arctia tonsa</i></span></td>
                    <td><span>Calanoid
                    copepod</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Ardea alba">Ardea alba</a></i></span></td>
                    <td><span>Great
                    egret</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Ardea herodias">Ardea herodias</a></i></span></td>
                    <td><span>Great
                    blue heron</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="Arius_felis">Arius felis</a></i></span></td>
                    <td><span>Hardhead
                    catfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Ascidia curvata</i></span></td>
                    <td><span>Curved
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Ascidia nigra</i></span></td>
                    <td><span>Black
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Assiminea spp.</i></span></td>
                    <td><span>(none)</span></td>
                </tr>
                <tr>
                    <td><span><i> Astyris lunata</i></span></td>
                    <td><span>Lunar
                    dovesnail</span></td>
                </tr>
                <tr>
                    <td><span><i> Atherinomorus stipes</i></span></td>
                    <td><span>Hardhead
                    silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> Aythya affinis</i></span></td>
                    <td><span>Lesser
                    scaup</span></td>
                </tr>
                <tr>
                    <td><span><i> Aythya americana</i></span></td>
                    <td><span>Redhead
                    duck</span></td>
                </tr>
                <tr>
                    <td><span><i> Aythya collaris</i></span></td>
                    <td><span>Ringneck
                    duck</span></td>
                </tr>
                <tr>
                    <td><span><i> Aythya valisineria </i></span></td>
                    <td><span> Canvasback</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Bagre marinus">Bagre marinus</a></i></span></td>
                    <td><span> Gafftopsail catfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Balanus eburneus</i></span></td>
                    <td><span>Ivory
                    barnacle</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Bairdiella chrysoura">Bairdiella chrysoura</a></i></span></td>
                    <td><span>Silver
                    perch, yellowtail</span></td>
                </tr>
                <tr>
                    <td><span><i> Bathygobius curacao</i></span></td>
                    <td><span> Notchtongue goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Bathygobius soporator</i></span></td>
                    <td><span>Frillfin
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Bittiolum varium</i></span></td>
                    <td><span>grass
                    cerith</span></td>
                </tr>
                <tr>
                    <td><span><i> Boonea impressa</i></span></td>
                    <td><span> Impressed odostome</span></td>
                </tr>
                <tr>
                    <td><span><i> Botryllus planus</i></span></td>
                    <td><span>Variable
                    encrusting tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Brachidontes exustus</i></span></td>
                    <td><span>Scorched
                    mussel</span></td>
                </tr>
                <tr>
                    <td><span><i> Branchiomma nigromaculata</i></span></td>
                    <td><span>Black
                    spotted fanworm</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Brevoortia smithi">Brevoortia smithi</a></i></span></td>
                    <td><span>menhaden</span></td>
                </tr>
                <tr>
                    <td><span><i> Brevoortia tyrannus</i></span></td>
                    <td><span>Atlantic
                    menhaden</span></td>
                </tr>
                <tr>
                    <td><span><i> Bubulcus ibis</i></span></td>
                    <td><span>Cattle
                    egret</span></td>
                </tr>
                <tr>
                    <td><span><i> Bucephala albeola</i></span></td>
                    <td><span> Bufflehead</span></td>
                </tr>
                <tr>
                    <td><span><i>Bulla
                    striata </i></span></td>
                    <td><span>Striate
                    bubble</span></td>
                </tr>
                <tr>
                    <td><span><i> Bunodosoma cavernata</i></span></td>
                    <td><span>American
                    warty anemone</span></td>
                </tr>
                <tr>
                    <td><span><i> Bunodosoma graniliferum</i></span></td>
                    <td><span>Red
                    warty anemone</span></td>
                </tr>
                <tr>
                    <td><span><i> Bursatella leachii pleii</i></span></td>
                    <td><span>Browsing
                    sea hares</span></td>
                </tr>
                <tr>
                    <td><span><i> Busycon contrarium</i></span></td>
                    <td><span> Lightning whelk</span></td>
                </tr>
                <tr>
                    <td><span><i> Butroides virescens</i></span></td>
                    <td><span>Green
                    backed heron</span></td>
                </tr>
                <tr>
                    <td><span><i> Calidris alpina</i></span></td>
                    <td><span>Dunlin</span></td>
                </tr>
                <tr>
                    <td><span><i> Calidris mauri</i></span></td>
                    <td><span>Western
                    sandpiper</span></td>
                </tr>
                <tr>
                    <td><span><i> Calidris minutilla</i></span></td>
                    <td><span>Least
                    sandpiper</span></td>
                </tr>
                <tr>
                    <td><span><i> Calidris spp.</i></span></td>
                    <td><span> Sandpipers</span></td>
                </tr>
                <tr>
                    <td><span><i> Callinectes bocourti</i></span></td>
                    <td><span>Red
                    crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Callinectes ornatus</i></span></td>
                    <td><span>ornate
                    blue crab</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Callinectes sapidus">Callinectes sapidus</a></i></span></td>
                    <td><span>blue
                    crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Callinectes similis</i></span></td>
                    <td><span>lesser
                    blue crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Capitella spp.</i></span></td>
                    <td><span> Polychaete worm</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Caranx hippos">Caranx hippos</a></i></span></td>
                    <td><span>Crevalle
                    jack</span></td>
                </tr>
                <tr>
                    <td><span><i> Carcharhinus leucas</i></span></td>
                    <td><span>bull
                    shark</span></td>
                </tr>
                <tr>
                    <td><span><i> Cardinalis cardinalis</i></span></td>
                    <td><span>Cardinal</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Cardisoma guanhumi">Cardisoma guanhumi</a></i></span></td>
                    <td><span>giant
                    land crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Carditamera floridana</i></span></td>
                    <td><span>Broad
                    ribbed carditid</span></td>
                </tr>
                <tr>
                    <td><span><i> Cassiopeia frondosa</i></span></td>
                    <td><span> upside-down jellyfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Cassiopeia xamachana</i></span></td>
                    <td><span> Upside-down jellyfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Catoptrophorus semipalmatus</i></span></td>
                    <td><span>Willet</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Centropomus parallelus">Centropomus parallelus</a></i></span></td>
                    <td><span>Fat
                    snook</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Centropomus pectinatus">Centropomus pectinatus</a></i></span></td>
                    <td><span>Tarpon
                    snook</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Centropomus undecimalis">Centropomus undecimalis</a></i></span></td>
                    <td><span>common
                    snook</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Centropristis philadelphica">Centropristis philadelphica</a></i></span></td>
                    <td><span>Rock
                    sea bass</span></td>
                </tr>
                <tr>
                    <td><span><i> Ceratozona squalida</i></span></td>
                    <td><span>Eastern
                    surf chiton</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Cerithidea scalariformis">Cerithidea scalariformis</a></i></span></td>
                    <td><span> Ladderhorn snail</span></td>
                </tr>
                <tr>
                    <td><span><i> Cerithium muscarum</i></span></td>
                    <td><span>Flyspeck
                    cerith</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Chaetodipterus faber">Chaetodipterus faber</a></i></span></td>
                    <td><span>Atlantic
                    spadefish</span></td>
                </tr>
                <tr>
                    <td><span><i> Charadrius vociferus </i></span></td>
                    <td><span>Killdeer</span></td>
                </tr>
                <tr>
                    <td><span><i> Chardrius semipalmatus</i></span></td>
                    <td><span> Semipalmated plover</span></td>
                </tr>
                <tr>
                    <td><span><i> Chasmodes bosquianus</i></span></td>
                    <td><span>Striped
                    blenny</span></td>
                </tr>
                <tr>
                    <td><span><i> Chasmodes saburrae</i></span></td>
                    <td><span>Florida
                    blenny</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Chelonia mydas">Chelonia mydas</a></i></span></td>
                    <td><span>Green
                    sea turtle</span></td>
                </tr>
                <tr>
                    <td><span><i> Chicoreus florifer</i></span></td>
                    <td><span>Lace
                    murex</span></td>
                </tr>
                <tr>
                    <td><span><i> Chondrilla nucula</i></span></td>
                    <td><span>Chicken
                    liver sponge</span></td>
                </tr>
                <tr>
                    <td><span><i> Citharichtys spilopteus</i></span></td>
                    <td><span>Bay
                    whiff</span></td>
                </tr>
                <tr>
                    <td><span><i> Clavelina oblonga</i></span></td>
                    <td><span>Oblong
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Clavelina picta</i></span></td>
                    <td><span>Painted
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Coccyzus minor</i></span></td>
                    <td><span>Mangrove
                    cuckoo</span></td>
                </tr>
                <tr>
                    <td><span><i> Columba leucocephala </i></span></td>
                    <td><span> White-crowed pigeon</span></td>
                </tr>
                <tr>
                    <td><span><i> Corophium sp.</i></span></td>
                    <td><span>Amphipod</span></td>
                </tr>
                <tr>
                    <td><span><i> Costoanachis avara</i></span></td>
                    <td><span>Greedy
                    dovesnail</span></td>
                </tr>
                <tr>
                    <td><span><i> Crassostrea virginica</i></span></td>
                    <td><span>Eastern
                    oyster</span></td>
                </tr>
                <tr>
                    <td><span><i> Crepidula convexa</i></span></td>
                    <td><span>Convex
                    slippersnail</span></td>
                </tr>
                <tr>
                    <td><span><i> Crepidula plana</i></span></td>
                    <td><span>Eastern
                    white slippersnail</span></td>
                </tr>
                <tr>
                    <td><span><i> Crocodylus acutus</i></span></td>
                    <td><span>American
                    crocodile</span></td>
                </tr>
                <tr>
                    <td><span><i> Cymatium pileare</i></span></td>
                    <td><span>Hairy
                    triton</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Cynoscion nebulosus">Cynoscion nebulosus</a></i></span></td>
                    <td><span>Spotted
                    seatrout</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Cynoscion regalis">Cynoscion regalis</a></i></span></td>
                    <td><span>Weakfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Cyprinodon variegatus </i></span></td>
                    <td><span> sheepshead minnow</span></td>
                </tr>
                <tr>
                    <td><span><i> Cypselurus heterurus</i></span></td>
                    <td><span>Atlantic
                    flyingfish</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Dasyatis sabina">Dasyatis sabina</a></i></span></td>
                    <td><span>Atlantic
                    stingray</span></td>
                </tr>
                <tr>
                    <td><span><i> Dendroica petechia gundlachi</i></span></td>
                    <td><span>Cuban
                    yellow warbler</span></td>
                </tr>
                <tr>
                    <td><span><i> Dendroica discolor paludicola</i></span></td>
                    <td><span>Florida
                    prairie warbler</span></td>
                </tr>
                <tr>
                    <td><span><i> Diapterus auratus</i></span></td>
                    <td><span>Irish
                    pompano</span></td>
                </tr>
                <tr>
                    <td><span><i> Didemnum conchyliatum</i></span></td>
                    <td><span>White
                    spongy tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Diodora cayensis</i></span></td>
                    <td><span>Keyhole
                    limpet</span></td>
                </tr>
                <tr>
                    <td><span><i> Diopatra spp. </i></span></td>
                    <td><span>Plumed
                    worm,</span></td>
                </tr>
                <tr>
                    <td><span><i> Diplodus argenteus</i></span></td>
                    <td><span>Silver
                    porgy</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Diplodus holbrooki">Diplodus holbrooki</a></i></span></td>
                    <td><span>Spottail
                    pinfish</span></td>
                </tr>
                <tr>
                    <td><span><i>Donax
                    variablilis</i></span></td>
                    <td><span>Variable
                    coquina</span></td>
                </tr>
                <tr>
                    <td><span><i> Dormitator maculatus</i></span></td>
                    <td><span>Fat
                    sleeper</span></td>
                </tr>
                <tr>
                    <td><span><i> Drymarchon corais couperi</i></span></td>
                    <td><span>Eastern
                    indigo snake</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Ectenascidea turbiniata">Ectenascidea turbiniata</a></i></span></td>
                    <td><span>Mangrove
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Egretta caerula">Egretta caerula</a></i></span></td>
                    <td><span>Little
                    blue heron</span></td>
                </tr>
                <tr>
                    <td><span><i> Egretta rufescens</i></span></td>
                    <td><span>Reddish
                    egret</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Egretta thula">Egretta thula</a></i></span></td>
                    <td><span>Snowy
                    egret</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Egretta tricolor">Egretta tricolor</a></i></span></td>
                    <td><span> Tricolored heron</span></td>
                </tr>
                <tr>
                    <td><span><i> Eleotris pisonis</i></span></td>
                    <td><span> Spinycheek sleeper</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Elops saurus">Elops saurus</a></i></span></td>
                    <td><span>Ladyfish</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Epinephelus itajara">Epinephelus itajara</a></i></span></td>
                    <td><span>Goliath
                    grouper</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Epinephelus morio">Epinephelus morio</a></i></span></td>
                    <td><span>Red
                    grouper</span></td>
                </tr>
                <tr>
                    <td><span><i> Eretmochelys imbricata</i></span></td>
                    <td><span> Hawksbill sea turtle</span></td>
                </tr>
                <tr>
                    <td><span><i> Erotelis smaragdus</i></span></td>
                    <td><span>Emerald
                    sleeper</span></td>
                </tr>
                <tr>
                    <td><span><i> Eucinostomus argenteus</i></span></td>
                    <td><span>Spotfin
                    mojarra</span></td>
                </tr>
                <tr>
                    <td><span><i> Eucinostomus gula</i></span></td>
                    <td><span>Silver
                    jenny</span></td>
                </tr>
                <tr>
                    <td><span><i> Eucinostomus harengulus</i></span></td>
                    <td><span> Tidewater mojarra</span></td>
                </tr>
                <tr>
                    <td><span><i> Eucinostomus melanopterus</i></span></td>
                    <td><span>Flagfin
                    mojarra</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Eudocimus albus">Eudocimus albus</a></i></span></td>
                    <td><span>White
                    ibis</span></td>
                </tr>
                <tr>
                    <td><span><i> Eugerres plumieri</i></span></td>
                    <td><span>striped
                    mojarra, goatfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Eurypanopeus depressus </i></span></td>
                    <td><span> Depressed mud crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Eurytium limnosum</i></span></td>
                    <td><span> Broadback mud crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Evorthodus lyricus</i></span></td>
                    <td><span>Lyre
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i>Falco
                    peregrinus</i></span></td>
                    <td><span> Peregrine falcon</span></td>
                </tr>
                <tr>
                    <td><span><i> Fasciolaria lilium hunteria</i></span></td>
                    <td><span>Banded
                    tulip</span></td>
                </tr>
                <tr>
                    <td><span><i>Felis
                    rufus</i></span></td>
                    <td><span>Bobcat</span></td>
                </tr>
                <tr>
                    <td><span><i> Floridichthys carpio</i></span></td>
                    <td><span> Goldspotted killifish</span></td>
                </tr>
                <tr>
                    <td><span><i> Fundulus cingulatus</i></span></td>
                    <td><span>Banded
                    topminnow</span></td>
                </tr>
                <tr>
                    <td><span><i> Fundulus confluentus</i></span></td>
                    <td><span>Marsh
                    killifish</span></td>
                </tr>
                <tr>
                    <td><span><i> Fundulus grandis</i></span></td>
                    <td><span>gulf
                    killifish</span></td>
                </tr>
                <tr>
                    <td><span><i> Fundulus seminolis</i></span></td>
                    <td><span>seminole
                    killifish</span></td>
                </tr>
                <tr>
                    <td><span><i> Gambusia affinis</i></span></td>
                    <td><span> Mosquitofish</span></td>
                </tr>
                <tr>
                    <td><span><i> Gambusia holbrooki</i></span></td>
                    <td><span>Eastern
                    mosquitofish</span></td>
                </tr>
                <tr>
                    <td><span><i> Gambusia rhizophorae</i></span></td>
                    <td><span>Mangrove
                    gambusia</span></td>
                </tr>
                <tr>
                    <td><span><i> Gerres cinereus</i></span></td>
                    <td><span> Yellowfin mojarra</span></td>
                </tr>
                <tr>
                    <td><span><i> Geukensia demisa</i></span></td>
                    <td><span>Ribbed
                    mussel</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobiesox strumosus</i></span></td>
                    <td><span> Skilletfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobioides broussoneti</i></span></td>
                    <td><span>Violet
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobionellus boleosoma</i></span></td>
                    <td><span>Darter
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobionellus oceanicus</i></span></td>
                    <td><span>Highfin
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobionellus smaragdus</i></span></td>
                    <td><span>Emerald
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobiosoma bosc</i></span></td>
                    <td><span>Naked
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobiosoma macrodon</i></span></td>
                    <td><span>Tiger
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Gobiosoma robustum</i></span></td>
                    <td><span>code
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Goniopsis cruentata</i></span></td>
                    <td><span>Spotted
                    mangrove crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Grandidierella bonnieroides</i></span></td>
                    <td><span> Amphipod</span></td>
                </tr>
                <tr>
                    <td><span><i> Haemulon chrysargyreum </i></span></td>
                    <td><span> Smallmouth grunt</span></td>
                </tr>
                <tr>
                    <td><span><i> Haemulon parra</i></span></td>
                    <td><span>Sailor's
                    choice</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Haemulon plumieri">Haemulon plumieri</a></i></span></td>
                    <td><span>White
                    grunt</span></td>
                </tr>
                <tr>
                    <td><span><i> Haemulon sciurus</i></span></td>
                    <td><span> Bluestriped grunt</span></td>
                </tr>
                <tr>
                    <td><span><i> Haliaeetus leucocephalus</i></span></td>
                    <td><span>Bald
                    eagle</span></td>
                </tr>
                <tr>
                    <td><span><i> Haminoea antillarum</i></span></td>
                    <td><span>Antilles
                    glassy-bubble</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Harengula jaquana">Harengula jaquana</a></i></span></td>
                    <td><span>Scaled
                    sardine</span></td>
                </tr>
                <tr>
                    <td><span><i> Hemiramphus balao</i></span></td>
                    <td><span>Balao</span></td>
                </tr>
                <tr>
                    <td><span><i> Henrya morrisoni</i></span></td>
                    <td><span>Gastropod</span></td>
                </tr>
                <tr>
                    <td><span><i> Hippocampus erectus</i></span></td>
                    <td><span>Lined
                    seahorse</span></td>
                </tr>
                <tr>
                    <td><span><i> Hippocampus zosterae</i></span></td>
                    <td><span>Dwarf
                    seahorse</span></td>
                </tr>
                <tr>
                    <td><span><i> Hippolyte spp.</i></span></td>
                    <td><span> Broken-back shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Hydroides spp.</i></span></td>
                    <td><span>Feather
                    duster worms</span></td>
                </tr>
                <tr>
                    <td><span><i> Hypoatherina harringtonensis</i></span></td>
                    <td><span>Reef
                    silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> Ircinia strobilina</i></span></td>
                    <td><span>Stinking
                    pillow sponge</span></td>
                </tr>
                <tr>
                    <td><span><i> Ishadium recurvum</i></span></td>
                    <td><span>Hooked
                    mussel</span></td>
                </tr>
                <tr>
                    <td><span><i> Isognomon alatus</i></span></td>
                    <td><span>Flat
                    tree oyster</span></td>
                </tr>
                <tr>
                    <td><span><i> Isognomon bicolor</i></span></td>
                    <td><span>Bicolor
                    purse oyster</span></td>
                </tr>
                <tr>
                    <td><span><i> Labidesthes sicculus</i></span></td>
                    <td><span>Brook
                    silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lagodon rhomboides">Lagodon rhomboides</a></i></span></td>
                    <td><span>Pinfish,
                    sailor's choice</span></td>
                </tr>
                <tr>
                    <td><span><i> Lasiurus spp.</i></span></td>
                    <td><span>Bat</span></td>
                </tr>
                <tr>
                    <td><span><i> Leander tenuicornis </i></span></td>
                    <td><span>Brown
                    glass shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Leiostomus xanthurus">Leiostomus xanthurus</a></i></span></td>
                    <td><span>Spot</span></td>
                </tr>
                <tr>
                    <td><span><i> Lepidochelys kempi</i></span></td>
                    <td><span>Atlantic
                    ridley sea turtle</span></td>
                </tr>
                <tr>
                    <td><span><i> Lepisosteus osseus</i></span></td>
                    <td><span>Longnose
                    gar</span></td>
                </tr>
                <tr>
                    <td><span><i> Libinia dubia </i></span></td>
                    <td><span>Doubtful
                    spider crab</span></td>
                </tr>
                <tr>
                    <td><span><i>Ligia
                    exotica</i></span></td>
                    <td><span>Sea
                    roach</span></td>
                </tr>
                <tr>
                    <td><span><i> Limnodromus griseus</i></span></td>
                    <td><span>Short
                    billed dowitcher</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Limulus polyphemus">Limulus polyphemus</a></i></span></td>
                    <td><span> Horseshoe crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Littorina angulifera</i></span></td>
                    <td><span>Mangrove
                    periwinkle</span></td>
                </tr>
                <tr>
                    <td><span><i> Littorina irrorata</i></span></td>
                    <td><span>Marsh
                    periwinkle</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lobotes surinamensis">Lobotes surinamensis</a></i></span></td>
                    <td><span> Tripletail</span></td>
                </tr>
                <tr>
                    <td><span><i> Lolliguncula brevis</i></span></td>
                    <td><span>Atlantic
                    brief squid</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lontra canadensis">Lontra canadensis</a></i></span></td>
                    <td><span>River
                    otter</span></td>
                </tr>
                <tr>
                    <td><span><i> Lophogobius cyprinoides</i></span></td>
                    <td><span>Crested
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Lucania parva</i></span></td>
                    <td><span> Rainwater killifish</span></td>
                </tr>
                <tr>
                    <td><span><i> Lupinoblennius nicholsi</i></span></td>
                    <td><span>Highfin
                    blenny</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lutjanus analis">Lutjanus analis</a></i></span></td>
                    <td><span>Mutton
                    snapper</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lutjanus apodus">Lutjanus apodus</a></i></span></td>
                    <td><span> Schoolmaster</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lutjanus griseus">Lutjanus griseus</a></i></span></td>
                    <td><span>Gray
                    snapper, mangrove snapper</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lutjanus jocu">Lutjanus jocu</a></i></span></td>
                    <td><span>Dog
                    snapper</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Lutjanus synagris">Lutjanus synagris</a></i></span></td>
                    <td><span>Lane
                    snapper</span></td>
                </tr>
                <tr>
                    <td><span><i>Lynx
                    rufus</i></span></td>
                    <td><span>Bobcat</span></td>
                </tr>
                <tr>
                    <td><span><i> Lyonsia floridana </i></span></td>
                    <td><span>Florida
                    lyonsia</span></td>
                </tr>
                <tr>
                    <td><span><i> Macrobrachium acanthurus </i></span></td>
                    <td><span> Caribbean crayfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Malaclemys terrapin rhizophorarum</i></span></td>
                    <td><span>Mangrove
                    diamondback terrapin</span></td>
                </tr>
                <tr>
                    <td><span><i> Malaclemys terrapin tequesta</i></span></td>
                    <td><span> Diamondback terrapin</span></td>
                </tr>
                <tr>
                    <td><span><i> Trichechus manatus </i></span></td>
                    <td><span>West
                    Indian manatee</span></td>
                </tr>
                <tr>
                    <td><span><i> Martesia striata</i></span></td>
                    <td><span>Striate
                    paddock, wood boring martesia</span></td>
                </tr>
                <tr>
                    <td><span><i> Megaceryle alcyon</i></span></td>
                    <td><span>Belted
                    kingfisher</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Megalops atlanticus">Megalops atlanticus</a></i></span></td>
                    <td><span>Tarpon</span></td>
                </tr>
                <tr>
                    <td><span><i> Melamphus coffeus</i></span></td>
                    <td><span>Coffee
                    bean snail</span></td>
                </tr>
                <tr>
                    <td><span><i> Melampus bidentatus</i></span></td>
                    <td><span>Easten
                    melampus</span></td>
                </tr>
                <tr>
                    <td><span><i> Melongena corona </i></span></td>
                    <td><span>Crown
                    conch</span></td>
                </tr>
                <tr>
                    <td><span><i> Membras martinica</i></span></td>
                    <td><span>Rough
                    silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> Menidia beryllina</i></span></td>
                    <td><span>Inland
                    silverside; tidewater silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> Menidia peninsulae</i></span></td>
                    <td><span> Penninsula silverside</span></td>
                </tr>
                <tr>
                    <td><span><i> Menippe mercenaria </i></span></td>
                    <td><span>Florida
                    stone crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Menippe nodifrons</i></span></td>
                    <td><span>Cuban
                    stone crab</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Menticirrhus americanus">Menticirrhus americanus</a></i></span></td>
                    <td><span>Southern
                    kingfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Mephitis mephitis</i></span></td>
                    <td><span>Spotted
                    skunk</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Mercenaria mercenaria">Mercenaria mercenaria</a></i></span></td>
                    <td><span>Hard
                    clam, quahog</span></td>
                </tr>
                <tr>
                    <td><span><i> Mergus cucullatus</i></span></td>
                    <td><span>Hooded
                    merganser</span></td>
                </tr>
                <tr>
                    <td><span><i> Mergus serrator</i></span></td>
                    <td><span> Red-breasted merganser</span></td>
                </tr>
                <tr>
                    <td><span><i> Microgobius gulosus</i></span></td>
                    <td><span>Clown
                    goby</span></td>
                </tr>
                <tr>
                    <td><span><i> Micropogonias undulatus</i></span></td>
                    <td><span>Croaker</span></td>
                </tr>
                <tr>
                    <td><span><i> Mogula occidentalis</i></span></td>
                    <td><span>Sandy
                    sea squirt, western sea squirt</span></td>
                </tr>
                <tr>
                    <td><span><i>Mola
                    mola</i></span></td>
                    <td><span>Ocean
                    sunfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Monacanthus hispidus</i></span></td>
                    <td><span> Planehead filefish</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Mugil cephalus">Mugil cephalus</a></i></span></td>
                    <td><span>Striped
                    mullet</span></td>
                </tr>
                <tr>
                    <td><span><i>Mugil
                    curema</i></span></td>
                    <td><span>White
                    mullet</span></td>
                </tr>
                <tr>
                    <td><span><i> Mycteria americana</i></span></td>
                    <td><span>Wood
                    stork</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Mycteroperca microlpis">Mycteroperca microlpis</a></i></span></td>
                    <td><span>Gag
                    grouper, grey grouper</span></td>
                </tr>
                <tr>
                    <td><span><i> Myiarchus crinitus crinitus</i></span></td>
                    <td><span>Southern
                    crested flycatcher</span></td>
                </tr>
                <tr>
                    <td><span><i> Myrophis punctatus</i></span></td>
                    <td><span>Speckled
                    worm eel</span></td>
                </tr>
                <tr>
                    <td><span><i> Mytilopsis leucophaeta</i></span></td>
                    <td><span>Dark
                    falsemussel</span></td>
                </tr>
                <tr>
                    <td><span><i> Nassarius vibex</i></span></td>
                    <td><span>Bruised
                    nassa</span></td>
                </tr>
                <tr>
                    <td><span><i> Neotoma floridana </i></span></td>
                    <td><span>Eastern
                    wood rat</span></td>
                </tr>
                <tr>
                    <td><span><i> Nereis succinea</i></span></td>
                    <td><span> Polychaete worm</span></td>
                </tr>
                <tr>
                    <td><span><i> Neritina clenchi</i></span></td>
                    <td><span>Clench's
                    nerite</span></td>
                </tr>
                <tr>
                    <td><span><i> Neritina virginea</i></span></td>
                    <td><span>Virgin
                    nerite</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Nerodia clarkii">Nerodia clarkii</a></i></span></td>
                    <td><span>Salt
                    marsh snake</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Nerodia fasciata compressicauda">Nerodia fasciata compressicauda</a></i></span></td>
                    <td><span>Mangrove
                    water snake</span></td>
                </tr>
                <tr>
                    <td><span><i> Noetia ponderosa</i></span></td>
                    <td><span> Ponderous ark</span></td>
                </tr>
                <tr>
                    <td><span><i> Odocoileus virginianes</i></span></td>
                    <td><span> Whitetail deer</span></td>
                </tr>
                <tr>
                    <td><span><i> Odostomia engonia </i></span></td>
                    <td><span>Gastropod</span></td>
                </tr>
                <tr>
                    <td><span><i> Ogilbia cayorum</i></span></td>
                    <td><span>Key
                    brotula</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Oligoplites saurus">Oligoplites saurus</a></i></span></td>
                    <td><span> Leatherjacket</span></td>
                </tr>
                <tr>
                    <td><span><i> Onuphis spp.</i></span></td>
                    <td><span> Parchment tube worm, Onuphis worm</span></td>
                </tr>
                <tr>
                    <td><span><i> Ophichthus gomesi </i></span></td>
                    <td><span>Shrimp
                    eel</span></td>
                </tr>
                <tr>
                    <td><span><i> Opisthonema oglinum</i></span></td>
                    <td><span>Atlantic
                    thread herring</span></td>
                </tr>
                <tr>
                    <td><span><i> Opsanus beta</i></span></td>
                    <td><span>Gulf
                    toadfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Orchestia spp.</i></span></td>
                    <td><span> Amphipod</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Orthropristis chrysoptera">Orthropristis chrysoptera</a></i></span></td>
                    <td><span>Pigfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Oxyura jamaicensis</i></span></td>
                    <td><span>Ruddy
                    duck</span></td>
                </tr>
                <tr>
                    <td><span><i> Pachygrapsus gracilis</i></span></td>
                    <td><span>Wharf
                    crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Pachygrapsus transversus</i></span></td>
                    <td><span>Common
                    shore crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Palaemontes spp.</i></span></td>
                    <td><span>Grass
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Pandion haliaetus</i></span></td>
                    <td><span>Osprey</span></td>
                </tr>
                <tr>
                    <td><span><i> Panopeus herbstii</i></span></td>
                    <td><span>Common
                    mud crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Panulirus argus</i></span></td>
                    <td><span>Spiny
                    lobster</span></td>
                </tr>
                <tr>
                    <td><span><i> Parablennius marmoreus</i></span></td>
                    <td><span>Seaweed
                    blenny</span></td>
                </tr>
                <tr>
                    <td><span><i> Paraclinus fasciatus</i></span></td>
                    <td><span>Banded
                    blenny</span></td>
                </tr>
                <tr>
                    <td><span><i> Parastarte triquetra</i></span></td>
                    <td><span>Brown
                    gemclam</span></td>
                </tr>
                <tr>
                    <td><span><i> Pelecanus erythrorhynchos</i></span></td>
                    <td><span>White
                    pelican</span></td>
                </tr>
                <tr>
                    <td><span><i> Pelicanus occidentalis</i></span></td>
                    <td><span>Brown
                    pelican</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Penaeus aztecus">Penaeus aztecus</a></i></span></td>
                    <td><span>Brown
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Penaeus duorarum">Penaeus duorarum</a></i></span></td>
                    <td><span>Pink
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Penaeus setiferus">Penaeus setiferus</a></i></span></td>
                    <td><span>White
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Perophora viridis</i></span></td>
                    <td><span>Green
                    colonial tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Petaloconchus varians</i></span></td>
                    <td><span>Variable
                    wormsnail</span></td>
                </tr>
                <tr>
                    <td><span><i> Phalacocorax auritus</i></span></td>
                    <td><span> Double-crested cormorant</span></td>
                </tr>
                <tr>
                    <td><span><i> Phallusia nigra</i></span></td>
                    <td><span>Black
                    tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Phrynelox scaber</i></span></td>
                    <td><span> Splitlure frogfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Pisania pusio</i></span></td>
                    <td><span> Miniature trumpet triton, Pisa snail</span></td>
                </tr>
                <tr>
                    <td><span><i> Plagusia depressa</i></span></td>
                    <td><span>Spray
                    crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Planobella scalare</i></span></td>
                    <td><span>Mesa
                    ram's horn</span></td>
                </tr>
                <tr>
                    <td><span><i> Planorbella duryi</i></span></td>
                    <td><span>Seminole
                    ram's horn</span></td>
                </tr>
                <tr>
                    <td><span><i> Plegadis falcinellus</i></span></td>
                    <td><span>Glossy
                    ibis</span></td>
                </tr>
                <tr>
                    <td><span><i> Pluvialis squatarola</i></span></td>
                    <td><span>Black
                    billed plover</span></td>
                </tr>
                <tr>
                    <td><span><i> Podilymbus podiceps</i></span></td>
                    <td><span> Pied-billed grebe</span></td>
                </tr>
                <tr>
                    <td><span><i> Poecilia latipinna</i></span></td>
                    <td><span>Sailfin
                    molly</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Pogonias cromis">Pogonias cromis</a></i></span></td>
                    <td><span>Black
                    drum</span></td>
                </tr>
                <tr>
                    <td><span><i> Polyclinum constellatum</i></span></td>
                    <td><span>Starred
                    gelatinous tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Polygyra cereolus</i></span></td>
                    <td><span>Southern
                    flatcoil</span></td>
                </tr>
                <tr>
                    <td><span><i> Polygyra spp.</i></span></td>
                    <td><span> flatcoils</span></td>
                </tr>
                <tr>
                    <td><span><i> Prionotus tribulus </i></span></td>
                    <td><span>Bighead
                    searobin</span></td>
                </tr>
                <tr>
                    <td><span><i> Procambarus alleni</i></span></td>
                    <td><span>Crayfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Procyon lotor </i></span></td>
                    <td><span>Raccoon</span></td>
                </tr>
                <tr>
                    <td><span><i> Rallus longirostris</i></span></td>
                    <td><span>Clapper
                    rail</span></td>
                </tr>
                <tr>
                    <td><span><i> Rithropanopeus harrisi</i></span></td>
                    <td><span>Harris'
                    mud crab</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Rivulus marmoratus">Rivulus marmoratus</a></i></span></td>
                    <td><span>Mangrove
                    rivulus</span></td>
                </tr>
                <tr>
                    <td><span><i> Sagitta spp.</i></span></td>
                    <td><span>Arrow
                    worm</span></td>
                </tr>
                <tr>
                    <td><span><i> Sardinella aurita</i></span></td>
                    <td><span>Spanish
                    sardine</span></td>
                </tr>
                <tr>
                    <td><span><em><a href="../taxa/index.php?taxon=Sarotherodon melanotheron">Sarotherodon
                    melanotheron</a></em></span></td>
                    <td><span>Blackchin
                    tilapia</font></span></td>
                </tr>
                <tr>
                    <td><span><i> Sayella crosseana</i></span></td>
                    <td><span>Gastropod</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Sciaenops ocellatus">Sciaenops ocellatus</a></i></span></td>
                    <td><span>Red
                    drum</span></td>
                </tr>
                <tr>
                    <td><span><i> Scorpaena brasiliensis</i></span></td>
                    <td><span>Barbfish</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Selene vomer">Selene vomer</a></i></span></td>
                    <td><span>Lookdown</span></td>
                </tr>
                <tr>
                    <td><span><i> Sesarma cinereum</i></span></td>
                    <td><span>Gray
                    marsh crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Sesarma curacaoense</i></span></td>
                    <td><span>Curacao
                    marsh crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Sesarma ricordi</i></span></td>
                    <td><span>Marbled
                    marsh crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Sigmodon hispidus littoralis </i></span></td>
                    <td><span>Hipsid
                    cotton rat</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphaeroma sp.</i></span></td>
                    <td><span> Wood-boring crustaceans</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphenia antillensis</i></span></td>
                    <td><span> Antillean sphenia</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphoeroides nephelus</i></span></td>
                    <td><span>Southern
                    puffer</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphoeroides spengleri </i></span></td>
                    <td><span>Bandtail
                    puffer</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphoeroides testudineus</i></span></td>
                    <td><span> Checkered puffer</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Sphyraena barracuda">Sphyraena barracuda</a></i></span></td>
                    <td><span>Great
                    barracuda</span></td>
                </tr>
                <tr>
                    <td><span><i> Sphyraena borealis</i></span></td>
                    <td><span>Northern
                    sennett</span></td>
                </tr>
                <tr>
                    <td><span><i> Spindalis zena</i></span></td>
                    <td><span> Stripe-headed tanager</span></td>
                </tr>
                <tr>
                    <td><span><i> Spirorbis sp.</i></span></td>
                    <td><span>Serpulid
                    worm</span></td>
                </tr>
                <tr>
                    <td><span><i> Stellatoma stellata</i></span></td>
                    <td><span>Gastropod</span></td>
                </tr>
                <tr>
                    <td><span><i> Stenonereis martini</i></span></td>
                    <td><span> Polychaete worm</span></td>
                </tr>
                <tr>
                    <td><span><i> Strongylura notata</i></span></td>
                    <td><span>Redfin
                    needlefish</span></td>
                </tr>
                <tr>
                    <td><span><i> Strongylura timucu</i></span></td>
                    <td><span>Timucu</span></td>
                </tr>
                <tr>
                    <td><span><i> Styela plicata</i></span></td>
                    <td><span>Pleated
                    sea squirt</span></td>
                </tr>
                <tr>
                    <td><span><i> Sylvilagus floridanus</i></span></td>
                    <td><span>Eastern
                    cottontail</span></td>
                </tr>
                <tr>
                    <td><span><i> Sylvilagus palustris paludicola</i></span></td>
                    <td><span>Marsh
                    rabbit</span></td>
                </tr>
                <tr>
                    <td><span><i> Synalpheus fritzmuelleri</i></span></td>
                    <td><span>Speckled
                    snapping shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Syngnathus louisianae</i></span></td>
                    <td><span>Chain
                    pipefish</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Syngnathus scovelli">Syngnathus scovelli</a></i></span></td>
                    <td><span>Gulf
                    pipefish</span></td>
                </tr>
                <tr>
                    <td><span><i> Synodus foetens</i></span></td>
                    <td><span>Inshore
                    lizardfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Tagelus plebeius</i></span></td>
                    <td><span>Stout
                    tagelus</span></td>
                </tr>
                <tr>
                    <td><span><i> Taphromysis bowmani </i></span></td>
                    <td><span>Mysid
                    shrimp</span></td>
                </tr>
                <tr>
                    <td><span><i> Tedania ignis</i></span></td>
                    <td><span>Fire
                    sponge</span></td>
                </tr>
                <tr>
                    <td><span><i> Tellina tampaenis</i></span></td>
                    <td><span>Tampa
                    tellin</span></td>
                </tr>
                <tr>
                    <td><span><i>Thais
                    spp.</i></span></td>
                    <td><span>Rock
                    shells</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Trachinotus falcatus">Trachinotus falcatus</a></i></span></td>
                    <td><span>Permit</span></td>
                </tr>
                <tr>
                    <td><span><i> Trichechus manatus </i></span></td>
                    <td><span>Florida
                    manatee</span></td>
                </tr>
                <tr>
                    <td><span><i> Trichiurus lepturus</i></span></td>
                    <td><span>Atlantic
                    cutlassfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Trididemnum savignii</i></span></td>
                    <td><span> Savigni's encrusting tunicate</span></td>
                </tr>
                <tr>
                    <td><span><i> Trinectes maculatus </i></span></td>
                    <td><span> Hogchoker</span></td>
                </tr>
                <tr>
                    <td><span><i> Tringa flavipes</i></span></td>
                    <td><span>Lesser
                    yellowlegs</span></td>
                </tr>
                <tr>
                    <td><span><i> Tringa melanoleuca</i></span></td>
                    <td><span>Greater
                    yellowlegs</span></td>
                </tr>
                <tr>
                    <td><span><i> Truncatella pulchella </i></span></td>
                    <td><span> Beautiful truncatella</span></td>
                </tr>
                <tr>
                    <td><span><i> Tursiops truncatus</i></span></td>
                    <td><span> Bottlenosed dolphin</span></td>
                </tr>
                <tr>
                    <td><span><i> Turritella spp.</i></span></td>
                    <td><span> Turretsnails</span></td>
                </tr>
                <tr>
                    <td><span><i> Tylosurus acus</i></span></td>
                    <td><span>Agujon</span></td>
                </tr>
                <tr>
                    <td><span><i> Tylosurus crocodilus</i></span></td>
                    <td><span> Houndfish</span></td>
                </tr>
                <tr>
                    <td><span><i> Tyrannus caudifasciatus</i></span></td>
                    <td><span> Loggerhead kingbird</span></td>
                </tr>
                <tr>
                    <td><span><i> Tyrannus dominicensis</i></span></td>
                    <td><span>Gray
                    kingbird</span></td>
                </tr>
                <tr>
                    <td><span><i>Uca
                    pugilator</i></span></td>
                    <td><span>Sand
                    fiddler crab</span></td>
                </tr>
                <tr>
                    <td><span><i>Uca
                    rapax</i></span></td>
                    <td><span> Caribbean fiddler crab</span></td>
                </tr>
                <tr>
                    <td><span><i>Uca
                    speciosa</i></span></td>
                    <td><span>Ive's
                    fiddler crab</span></td>
                </tr>
                <tr>
                    <td><span><i>Uca
                    thayeri</i></span></td>
                    <td><span>Thayer's
                    fiddler crab</span></td>
                </tr>
                <tr>
                    <td><span><i> Urocyon cinereoargenteus</i></span></td>
                    <td><span>Gray
                    fox</span></td>
                </tr>
                <tr>
                    <td><span><i> Urosalpinx cinerea</i></span></td>
                    <td><span>Atlantic
                    oyster drill</span></td>
                </tr>
                <tr>
                    <td><span><i>Ursus
                    americanus</i></span></td>
                    <td><span>Black
                    bear</span></td>
                </tr>
                <tr>
                    <td><span><i> <a href="../taxa/index.php?taxon=Vallentinia gabriellae">Vallentinia gabriellae</a></i></span></td>
                    <td><span> Hitch-hiking jellyfish</span></td>
                </tr>
                <tr>
                    <td><span><i>Vireo
                    altiloquus</i></span></td>
                    <td><span>Black
                    whiskered vireo</span></td>
                </tr>
                <tr>
                    <td><span><i> Vitrinella floridana</i></span></td>
                    <td><span>Florida
                    vitrinella</span></td>
                </tr>
            </table>

            <table style="width:700px;margin-left:auto;margin-right:auto;">
                <tr>
                    <td>
                        <p class="title">References &amp; Further Reading</p>
                        <p class="body">Atkinson, MR, Findlay, GP, Hope, AB, Pitman, MG, Sadler, HDW &amp;
                            HR West. 1967. Salt regulation in the mangroves <em>Rhizophora mangle</em> Lam. and <em>Aerialitis annulata</em> R. <em>Australian J. Biol.
                                Sci.</em> 20: 589-599.</p>
                        <p class="body">Brockmeyer, RE, Rey, JR, Virnstein, RW, Gilmore,
                            Jr., RG &amp; L Earnest. 1997. Rehabilitation of impounded estuarine
                            wetlands by hydrologic reconnection to the Indian River Lagoon,
                            Florida. <em>J. Wetlands Ecol. Manag.</em> 4: 93-109. </p>
                        <p class="body">Carlson, PR &amp; LA Yarbro. 1987. Physical
                            and biological control of mangrove pore water chemistry. <em>In</em>:
                            Hook, DD et al., eds. <em>The Ecology and Management of Wetlands.</em> 112-132. Croom Helm. London, UK.</p>
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
                            1979. Litter production in a southwest Florida black mangrove community. <em>Proc. FL Anti-Mosquito Assoc. 50th Meeting.</em> 24-33. </p>
                        <p class="body">Hull, JB &amp; WE Dove. 1939. Experimental
                            diking for control of sand fly and mosquito breeding in Florida
                            saltwater marshes. <em>J. Econ. Entomology.</em> 32: 309-312. </p>
                        <p class="body">Lahmann, E. 1988. <em>Effects of different
                                hydrologic regimes on the productivity of </em>Rhizophora mangle<em> L. A case study of mosquito control impoundments in Hutchinson Island,
                                St. Lucie County, Florida.</em> Ph.D. dissertation, University of
                            Miami. Coral Gables, FL.</p>
                        <p class="body">Lewis, III, RR, Gilmore, Jr., RG, Crewz, DW
                            &amp; WE Odum. 1985. Mangrove habitat and fishery resources of Florida. <em>In</em>: Seaman, Jr., W, ed. <em>Florida Aquatic Habitat and
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
                            impact of low temperature stress on mangrove structure and growth. <em> Trop. Ecol.</em> 18: 149-161.</p>
                        <p class="body">Miller, PC. 1972. Bioclimate, leaf temperature,
                            and primary production in red mangrove canopies in South Florida. <em>Ecology.</em> 53: 22-45. </p>
                        <p class="body">Odum, WE. 1970. <em>Pathways of energy flow
                                in a south Florida estuary.</em> Ph.D. Thesis, University of Miami.
                            Coral Gables, FL.</p>
                        <p class="body">Odum, WE &amp; CC McIvor. 1990. Mangroves. <em>In</em>: Myers, RL &amp; JJ Ewel, eds. <em>Ecosystems of Florida</em>.
                            517 - 548. University of Central Florida Press. Orlando, FL. </p>
                        <p class="body">Odum, WE, McIvor, CC &amp; TJ Smith III. 1982. <em>The ecology of the mangroves of south Florida: a community profile.</em> U.S. Fish and Wildlife Service, Office of Biological Services. FWS/OBS-81-24.</p>
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
                            in two impounded tropical marshes on fishes and physical conditions. <em>Wetlands.</em> 10: 27-47. </p>
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
                            of Contract no. 14-16-008-606. Center for Aquatic Sciences.<br />
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
                            Smithsonian Marine Station at Fort Pierce<br />
                            Updates &amp; Photos by: LH Sweat, Smithsonian Marine Station at
                            Fort Pierce<br />
                            Submit additional information, photos or comments
                            to:<br />
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
