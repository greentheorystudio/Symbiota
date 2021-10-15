<?php
include_once(__DIR__ . '/config/symbini.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
    <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <meta name='keywords' content='' />
    <?php include_once(__DIR__ . '/config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/header.php');
?>
<div  id="innertext">
    <div style="float:right;width:380px;margin:15px;">
        <img style="width:380px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/Plega_hal7-1x_pinremoved_web.jpg" />
    </div>
    <h1>Welcome to BIOMNA</h1>
    <div style="padding: 0 15px;">
        BIOMNA is a specimen-based information management archive of macroinvertebrates from the entomologically
        poorly-known Colorado River basin (CRB). In part, the life work of Dr. Larry Stevens (Museum of Northern
        Arizona Biology Curator), this database provides information on all taxa in the Museum of Northern Arizona’s
        invertebrate collections, as well as Dr. Stevens’ observations and notes from data-mining efforts in multiple
        other collections. The field opportunities he has had in the Southwest, working as Ecologist for Grand Canyon
        National Park and a guide on the Colorado River, as well as extensive exploration of the springs and wild
        regions of this landscape, are not likely to be repeated. Most of the data presented here have not previously
        been compiled, and therefore represent unique distributional and elevational range information, data that
        provide a baseline for future studies of climate and land use changes. This database was designed by Jeri D.
        Ledbetter and Benjamin Brandt, exceptionally talented information management experts for maximum accuracy,
        ease of use, and reporting. The data herein have formed the basis of a large number of peer-reviewed scientific
        and popular works, and serve not only as a legacy, but the foundation of invertebrate biodiversity for this
        vast, diverse, and rapidly changing river basin.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        The CRB bridges the Colorado Plateau and the Basin and Range geologic provinces, naturally dividing it into
        two sub-basins. The 283,384 km2 Upper Colorado River Basin (UCRB) drains the West Slope of the Rocky Mountains
        and the stratigraphically largely undeformed Colorado Plateau section of the Rocky Mountains geologic province.
        It ranges in elevation from 4,365 m at Uncompahgre Peak in Colorado down to 350 m on Lake Mead Reservoir.
        The UCRB serves as an exploitation/extraction-dominated basin, with abundant geologic, forest, agricultural,
        water and hydropower, and recreational resources. The 344,440 km2 Lower Colorado River Basin (LCRB) lies
        in the Basin and Range geologic province, a late Cenozoic, tectonically extensional terrain dominated by
        horst and graben mountain ranges. The CRB contains a phenomenal diversity of internationally recognized
        national parks, national forests, rangelands, and water features, including Rocky Mountain, Natural Bridges,
        Canyonlands, Grand Canyon, Zion, Bryce, and other national and state parks and monuments, many Tribal reservations,
        portions of the two largest reservoirs in the coterminous USA, and the large cities of Phoenix, Tucson,
        and Las Vegas.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        Lying between the two geologic provinces, the Grand Canyon ecoregion (GCE) occupies 35,000 km2 of the CRB
        that drains into Grand Canyon, Arizona’s world-renowned, deeply incised canyon. The GCE extends from lower
        Lake Powell Reservoir 500 km downstream to Lake Mead reservoir, and ranges in elevation from Lake Mead to
        the 3,850 m-tall summit of the San Francisco Peaks. The GCE encompasses four biomes, and has been suggested
        to support 50,000 invertebrate species.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        Invertebrate taxonomy is in a constant state of improvement, adding to the difficulty of information management.
        At present, this database attempts to agree with the Integrated Taxonomic Information System (ITIS; itis.gov),
        affiliated with the Global Biodiversity Information Facility (GBIF), the Catalogue of Life (COL), and the
        Encyclopedia of Life (EOL). Nonetheless, some historic taxonomic conventions are retained for ease and clarity.
        For example, we retain as orders Homoptera and Hemiptera, taxa which are readily distinguished by have been
        lumped under the latter epithet. Such choices are readily recognized by entomologists, and should not create
        much difficult for those using the database. Wherever possible, effort is made to keep the taxonomy up-to-date.
    </div>
    <div style="margin-top:15px;padding: 0 10px;">
        This database is intended to be used by any researcher interested in increasing knowledge about invertebrate
        biology, ecology, biogeography, or conservation in the CRB. This database is not intended to be used for
        commercial exploitation. However, while every effort will be made to provide information readily to researchers,
        it also is important to note that museums like the private, non-profit MNA struggle mightily to remain solvent,
        and that it becomes culturally critical to support museum collections.
    </div>
</div>

<?php
include(__DIR__ . '/footer.php');
?>
</body>
</html>
