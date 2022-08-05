<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../classes/IRLManager.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Tidal Flat Habitats</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/external/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <style>
        .hero-container {
            background-image: url("../content/imglib/static/13_FischerD1.JPG");
            background-position: center bottom;
        }

        #innertext{
            position: sticky;
        }
    </style>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/jquery-ui.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/external/modernizr.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/static-page.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<div class="hero-container">
    <div class="top-shade-container"></div>
    <div class="logo-container">
        <img class="logo-image" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/janky_mangrove_logo_med.png" />
    </div>
    <div class="title-container">
        <span class="titlefont">Indian River Lagoon<br />
            Species Inventory</span>
    </div>
    <div class="login-container">
        <?php
        include(__DIR__ . '/../header-login.php');
        ?>
    </div>
    <div class="nav-bar-container">
        <?php
        include(__DIR__ . '/../header-navigation.php');
        ?>
    </div>
    <div class="breadcrumb-container">
        <div class='navpath'>
            <a href="Maps.php">The Indian River Lagoon</a> &gt;
            <a href="Whatsa_Habitat.php">Habitats</a> &gt;
            <b>Tidal Flats</b>
        </div>
    </div>
    <div class="page-title-container">
        <h1>Tidal Flats</h1>
    </div>
    <div class="top-text-container">
        <h3>
            In some areas of the Indian River Lagoon, when the tides recede, an ephemeral landscape reveals itself. At first
            glance, the lagoon’s silty-sandy bottom may seem like a barren mudscape – but just below the surface an abundance
            of life is burrowed in, waiting for the return of the lagoon’s waters.
        </h3>
    </div>
    <div class="photo-credit-container">
        Photo credit: D. Fischer
    </div>
</div>
<div id="bodyContainer">
    <div class="sideNavMover">
        <div class="sideNavContainer">
            <nav id="cd-vertical-nav">
                <ul class="vertical-nav-list">
                    <li>
                        <a href="#intro-section" data-number="1">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Intro</span>
                        </a>
                    </li>
                    <li>
                        <a href="#mud-sand-section" data-number="2">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Mud vs. Sand</span>
                        </a>
                    </li>
                    <li>
                        <a href="#pros-cons-section" data-number="3">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Ecological Pros and Cons</span>
                        </a>
                    </li>
                    <li>
                        <a href="#threats-section" data-number="4">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Threats to Tidal Flats</span>
                        </a>
                    </li>
                    <li>
                        <a href="#species-section" data-number="5">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Tidal Flat Species</span>
                        </a>
                    </li>
                    <li>
                        <a href="#further-reading-section" data-number="6">
                            <span class="cd-dot"></span>
                            <span class="cd-label">Further Reading</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div id="innertext">
        <div id="intro-section" class="cd-section">
            <p>
                Covered at high tide and exposed at low tide, tidal flats are dominated by soft sediments and generally lack
                vegetation. Found worldwide, they are common elements of estuaries, and are the basic landform upon which
                coastal wetlands accumulate. In the Indian River Lagoon, tidal flats are most abundant near inlets, where
                tidal influence is strongest.
            </p>
            <p>
                Tidal flats comprise only about 7 percent of total coastal shelf areas, but are highly productive ecosystems.
                Though overall biological diversity may be relatively low, tidal flats can contain astounding volumes of
                microorganisms and benthic infauna, or tiny animals that live in the top layer of sediment. In addition to
                recycling organic matter and nutrients from terrestrial and marine sources, benthic infauna are also prey
                for many fin and shellfish species, as well as resident and migratory wetland birds.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/foltz_on_flats_MZD.jpg" />
                    <figcaption>
                        <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="mud-sand-section" class="cd-section">
            <h4>Mud vs. Sand</h4>
            <p>
                Tidal flats are highly dynamic, with sediments continuously on the move. Depending on sediment grain size,
                tidal flats are generally categorized as either mud or sand flats.
            </p>
            <p>
                Mudflats usually occur in the upper portion of the intertidal zone, and in areas with low-energy water movement.
                Here, sediments contain a high proportion of fine silt and clay particles. Mudflats have higher organic content,
                generally from microbial activity or from adjacent sources such as salt marshes, mangroves and seagrass beds.
            </p>
            <p>
                Sandflats occur in areas with stronger currents and moderate wave action that can carry larger, heavier sediment
                particles. Sediments are mostly quartz (silica) derived from erosion. In southern Florida systems, mud-sand
                and combinations of calcium carbonate coral rock soft-bottom types are common.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/Fort_Pierce_Inlet_D_Ramey_Logan-2.jpg" />
                    <figcaption>
                        Fort Pierce Inlet. <i>Credit: D. Ramey Logan, Wikimedia Commons</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                Both types of tidal flat occur on Coon Island, along the north side of Fort Pierce Inlet in the Indian River
                Lagoon. The eastern edge of the island gives way to a relatively large tidal flat. On the southern end, where
                currents are relatively strong, sediments are sandy; on the northern and western areas, which are more protected
                from inlet currents, sediments are muddier.
            </p>
            <p>
                Mud and sand flats also differ in their oxygen concentrations, which influence microbial activity. This activity
                stabilizes seasonal variation in organic material, ensuring a more consistent food supply for other organisms.
            </p>
            <p>
                In mudflats, the fine sediments trap detritus and prevent water from easily percolating through. The higher
                surface area of the numerous fine grains allows for higher numbers of microbes, which leads to increased
                anaerobic decomposition of organic matter. This activity produces hydrogen sulfide, methane and ammonia in
                an oxygen-poor layer, roughly .4 inches (1 cm) below the surface. Often black in color, this layer is visually
                striking in its contrast to the thin, grayish oxygenated layer above it.
            </p>
            <p>
                In sandflats, the large-grained particles allow water to percolate easily through sediments, which allows
                oxygen to penetrate as deep as 4 to 8 inches (10 to 20 cm) below the surface. Light can also filter deeply,
                allowing for prolonged activity by photosynthetic microorganisms.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/black_necked_stilt_Ursula_Dubrick.jpg" />
                    <figcaption>
                        Black-necked stilt with chick. <i>Credit: U. Dubrick</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="pros-cons-section" class="cd-section">
            <h4>Ecological Pros and Cons</h4>
            <p>
                For benthic organisms, life in the muddy sands of tidal flats affords many advantages. They can retreat into
                deeper sediments or burrows when threatened by predation. Able to move around, infaunal bivalves can survive
                partial predation as well as direct competition with burrowing neighbors. Desiccation is rarely an issue.
                Finally, organic materials accumulating on sediments provide a ready, constant food source.
            </p>
            <p>
                But there are drawbacks: lack of a securing "anchor" in the sediment. In contrast to rocky intertidal habitats,
                where organisms are often securely attached to the rock via cement, byssal threads and muscular feet, tidal
                flat organisms are at the mercy of the sediments. During periods of severe storm erosion, larger infauna in
                soft bottom habitats may become easily dislodged and subsequently displaced.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/luidia_senegalensis_MZD.jpg" />
                    <figcaption>
                        Nine-armed seastar. <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
        </div>
        <div id="threats-section" class="cd-section">
            <h4>Threats to Tidal Flats</h4>
            <p>
                Clean water and sediments are critical for healthy lagoon habitats. Tidal flat areas face a number of human-made
                and natural threats, including sea level rise, loss of habitat, salinity fluctuations, pollution, erosion
                and invasive species. Threats to tidal flats directly mirror threats to the larger Indian River Lagoon.
            </p>
            <p>
                For more information on the challenges facing the tidal flats and other areas of the lagoon, visit the
                <a href="Habitat_Threats.php">Threats resource page</a>.
            </p>
        </div>
        <div id="species-section" class="cd-section">
            <h4>Tidal Flat Species</h4>
            <p>
                Tidal flats host a diverse biotic assemblage, ranging from microscopic organisms to large crabs, fish and
                wading birds.
            </p>
            <div style="margin: 15px 0;display:flex;justify-content: center;">
                <figure style="margin: 15px;">
                    <img style="border:0;width:500px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/content/imglib/static/man_o_war_MZD.jpg" />
                    <figcaption>
                        Man o’ war jellyfish during low tide on the IRL. <i>Credit: M. Donahue</i>
                    </figcaption>
                </figure>
            </div>
            <p>
                The majority of organisms in tidal flats are considered to be benthic, or living in or on the lagoon bottom.
                Though most are extremely tiny, such as bacteria and diatoms, some, including parchment worms and the nine-armed
                sea star, can grow to be quite large.
            </p>
            <ul class="statictext">
                <li>
                    <i>Microbenthos</i> comprise primarily bacteria and diatoms.
                </li>
                <li>
                    <i>Meiobenthos</i> are usually less than a millimeter in length, which live in the void spaces between
                    relatively large sand grains in sediments.
                </li>
                <li>
                    <i>Hyperbenthos</i> are slightly larger, a few millimeters in length, and live in the water just above
                    the lagoon floor as well as in the very top layers of the sediment.
                </li>
                <li>
                    <i>Macrobenthos</i> are larger and can move freely through soft sediments, and include polychaete worms,
                    bivalves and amphipods.
                </li>
                <li>
                    <i>Epibenthos</i> are large, predatory and grazing species including crabs, mollusks, fish, rays, wading
                    birds and mammals.
                </li>
            </ul>
        </div>
        <div id="further-reading-section" class="cd-section">
            <h4>Further Reading</h4>
            <ul class="further-reading-list">
                <li>
                    Ambrose, W. G. 1984. Role of predatory infauna in structuring marine soft-bottom communities. Mar. Ecol. Prog. Ser. 17(2): 109-115.
                </li>
                <li>
                    Amos, C. L. 1995. Siliciclastic tidal flats. In: Perillo, G. M. (Ed.), Geomorphology and Sedimentology of Estuaries.
                    Elsevier, Amsterdam. pp. 273-306.
                </li>
                <li>
                    Bell, S. and B. Coull 1978. Field evidence that shrimp predation regulates meiofauna. Oecologia 35: 141-148.
                </li>
                <li>
                    Beyer, F. 1958. A new, bottom-living trachymedusa from the Oslo fjord. Nytt Mag. Zool. 6: 121-143.
                </li>
                <li>
                    Bertness, M. D. 1999. The Ecology of Atlantic Shorelines. Sinauer Associates, Inc., Sunderland. 417 pp.
                </li>
                <li>
                    Black, K. S., T. J. Tolhurst, S. E. Hagerthey and D. M. Paterson. 2002. Working with natural cohesive sediments.
                    J. Hydraulic Eng. Forum 128: 1-7.
                </li>
                <li>
                    Bottom, M. L. 1984. The importance of predation by horseshoe crabs, <i>Limulus polyphemus</i>, to an intertidal
                    sand flat community. J. Mar. Res. 42: 139-161.
                </li>
                <li>
                    Coelho, V. D., R. A. Cooper and S. Rodrigues. 2000. Burrow morphology and behavior of the mud shrimp <i>Upogebia omissa</i>
                    (Decapoda: Thalassinidea: Upogebiidae). Mar. Ecol. Prog. Ser. 200: 229-240.
                </li>
                <li>
                    Commito, J. A. and W. G. Ambrose. 1985. Multiple trophic levels in soft-bottom communities. Mar. Ecol. Prog. Ser. 26: 289-293.
                </li>
                <li>
                    Coull, B. C. 2009. Role of meiofauna in estuarine soft-bottom habitats. Austral Ecol. 24(4): 327-343.
                </li>
                <li>
                    de Brouwer, J. F. and L. J. Stal. 2001. Short-term dynamics in microphytobenthos distribution and associated extracellular
                    carbohydrates in surface sediments of an intertidal mudflat. Mar. Ecol. Prog. Ser. 218: 33-44.
                </li>
                <li>
                    Dyer, K. R. (Ed.), 1979. Estuarine Hydrography and Sedimentation. Estuarine and Brackish Water Sciences Association.
                    Cambridge University Press, Cambridge. 230 pp.
                </li>
                <li>
                    Dyer, K .R., M.C. Christe and E. W. Wright. 2000. The classification of mudflats. Cont. Shelf Res. 20: 1061-1078.
                </li>
                <li>
                    Felder, D. L. and R. B. Manning. 1986. A new genus and two new species of Alpheid shrimps (Decapoda: Caridea) from
                    south Florida. J. Crust. Biol. 6(3): 497-508.
                </li>
                <li>
                    Giere, O. 2009. Meiobenthology. The microscopic motile fauna of aquatic sediments. Springer-Verlag, Berlin. 527 pp.
                </li>
                <li>
                    Hendler G., J. E. Miller, D. L. Pawson , and P. M. Kier. 1995. Sea Stars, Sea Urchins, and Allies. Smithsonian
                    Institution Press, Washington, D. C. 390 pp.
                </li>
                <li>
                    Higgins, R. P. and H. Thiel. 1988. Introduction to the study of meiofauna. Smithsonian Institution Press, Washington, D. C. 488 pp.
                </li>
                <li>
                    Holligan, P. M. and W. A. Reiners. 1992. Predicting the responses of the coastal zone to global change. Adv. Ecol. Res. 22: 211-215.
                </li>
                <li>
                    Koulouri, P. Preliminary study of hyperbenthos in Heraklion Bay (Cretan Sea). Accessed 5 April 2010. Available
                    at: <a href="https://www.biomareweb.org/3.6.html" target="_blank">https://www.biomareweb.org/3.6.html</a>.
                </li>
                <li>
                    Little, C. 2000. The Biology of Soft Shores and Estuaries. Oxford University Press, Oxford. 252 pp.
                </li>
                <li>
                    MacIntyre, H. L., R. J. Geider and D. C. Miller. 1996. Microphytobenthos: the ecological role of the “Secret Garden” of
                    unvegetated, shallow-water marine habitats. I. Distribution, abundance and primary production. Estuaries 19: 186-201.
                </li>
                <li>
                    McIntyre, A. D. 1968. The macrofauna and meiofauna of some tropical beaches. J. Zool. 156: 377-392.
                </li>
                <li>
                    Mees, J. and M. B. Jones. 1997. The hyperbenthos. Ocean. Mar. Biol.Ann. Rev.35: 221-255. Mitbavkar, S. and A. C.
                    Anil. Diatoms of the microphytobenthic community: population structure in a tropical intertidal sand flat. Mar.
                    Bio. 140: 41-57.
                </li>
                <li>
                    Myers, R. L. and J. J. Ewel (Eds.), 1990. Ecosystems of Florida. U. of Central Florida Press, Orlando. 765 pp.
                </li>
                <li>
                    Nielsen, C. 2001. Animal Evolution: Interrelationships of the Living Phyla. Oxford University Press, Oxford. 578 pp.
                </li>
                <li>
                    Nybaken, J. W. and M. D. Bertness. 2005. Marine Biology: an Ecological Approach. Benjamin Cummings Publishers,
                    San Francisco. 579 pp.
                </li>
                <li>
                    Olafsson, E. B. , C. W. Peterson and W. G. Ambrose. 1994. Does recruitment limitation structure populations and
                    communities of macro-invertebrates in marine soft sediments? The relative significance of pre- and post-settlement
                    processes. Ocean. Mar. Biol. Ann. Rev. 32: 65-109.
                </li>
                <li>
                    Orth, R. J. 1975. Destruction of eelgrass, <i>Zostera bonasus</i>, in Cesapeake Bay. Chesapeake Sci. 16: 205-208.
                </li>
                <li>
                    Paterson, D. M., R. J. Aspden and K. S. Black. 2009. Intertidal flats: ecosystem functioning of soft sediment systems. In:
                    Perillo, G. M., E. Wolanski, D. R. Cahoon and M. M. Brinson (Eds.), Coastal Wetlands An Integrated Approach. Elsevier,
                    Amsterdam. Pp. 317-343.
                </li>
                <li>
                    Peterson, C. H. 1979. Predation, competitive exclusion, and diversity in the soft-sediment benthic communities of
                    estuaries and lagoons. In: Livingston, R. J. (Ed.), Ecological Processes in Coastal Marine Systems. Plenum Press,
                    New York. 548 pp.
                </li>
                <li>
                    Posey, M. H., B. R. Dumbauld and D. A. Armstrong. 1991. Effects of burrowing mud shrimp, <i>Upogebia pugettensis</i> (Dana), on
                    abundances of macro-infauna. J. Exp. Mar. Biol. Ecol. 148: 283-294.
                </li>
                <li>
                    Quammen, M. L. 1982. Influence of subtle substrate differences on feeding by shorebirds on intertidal mudflats.
                    Mar. Biol. 71: 339-343.
                </li>
                <li>
                    Rice, M. E., J. Piraino & H. F. Reicherdt. 1995. A survey of the Sipuncula of the Indian River Lagoon. Bu. Mar.
                    Sci. 57(1): 128-135.
                </li>
                <li>
                    Robertson, A. I. 1988 Decomposition of mangrove leaf litter in tropical Australia. J. Exp. Mar. Biol. Ecol. 116: 236-247.
                </li>
                <li>
                    Ruiz, G. M., J. T. Carlton, E. D. Grosholz, and A. H. Hines. 1997. Global invasions of marine and estuarine habitats by
                    non-indigenous species: mechanisms, extent, and consequences. Am. Zool. 37: 621-632.
                </li>
                <li>
                    Schmalzer, P.A. 1995. Biodiversity of saline and brackish marshes of the Indian River Lagoon: historic and current
                    patterns. Bull. Mar. Sci. 57(1): 37-48.
                </li>
                <li>
                    Sibert, J. R. 1981. Intertidal hyperbenthic populations in the Nanaimo Estuary. Mar. Biol. 64: 259-265.
                </li>
                <li>
                    Stal, L. J. 2003. Microphytobenthos, their extracellular polymeric substances, and the morphogenesis of intertidal
                    sediments. Geomicrobio. J. 20 (5): 463-478.
                </li>
                <li>
                    Stal, L. J. and F. C. de Brouwer. 2003. Biofilm formation by benthic diatoms and their influence on the stabilization of
                    intertidal mudflats. Berichte -Forschungszentrum Terramare 12: 109-111.
                </li>
                <li>
                    Stanley, S. M., 1970. Relation of shell form to life habits of the bivalve molluscs. Geol. Soc. Am. Monographs. 125: 1-296.
                </li>
                <li>
                    Stutz, M. L. and O. H. Pilkey. 2002. Global distribution and morphology of deltaic barrier island systems. J.
                    Coast. Res. 36: 694-707.
                </li>
                <li>
                    Thrush, S. F., R. D. Pridmore, R. G. Bell, V. J. Cummings, P. K. Dayton, R. Ford, J. Grant, M. O. Green, J. E. Hewitt, A. H.
                    Hines, M. T. Hume, S. M. Lawrie, P. Legendre, B. H. McArdle, D. Morrisey, D. C, Schneider, S. J. Turner, R. A. Walters,
                    R. B. Whitlatch and M. R. Wilkinson. 1997. The sandflat habitat: scaling from experiments to conclusions. J. Exp. Mar.
                    Biol. Ecol. 216: 1-9.
                </li>
                <li>
                    Van der Wal, D., P. M. Herman, R. M. Forster, T. Ysebaret, F. Rossi, E. Knaeps, Y. M. Plancke and S. J. Ides. 2008. Distribution
                    and dynamics of intertidal macrobenthos predicted from remote sensing: response to microphytobenthos and environment. Mar.
                    Ecol. Prog. Ser. 367: 57-72.
                </li>
                <li>
                    Virnstein, R. W. 1977. The importance of predation by crabs and fishes on benthic infauna in Chesapeake Bay. Ecol. 58: 1199-1217.
                </li>
                <li>
                    Watzin, M. 1983. The effects of meiofauna on settling macrofauna: meiofauna may structure macrofaunal communities.
                    Oecologia 59: 163-166.
                </li>
                <li>
                    Winkler, G. and W. Greve. 2004. Trophodynamics of two interacting species of estuarine mysids, <i>Praunus flexuosus</i>
                    and <i>Neomysis integer</i>, and their predation on the calanoid copepod <i>Eurytemora affinis</i>. J. Exp. Mar.
                    Biol. Ecol. 308: 127-146.
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
