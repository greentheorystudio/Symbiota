<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=' .$GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>The Project</title>
    <link href="../css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
    <link href="../css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css" rel="stylesheet" />
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <div style="margin:15px;">
        This website will bring together the results of an NSF-funded project to study the diversification of a large
        clade of Apiaceae from primarily the western United States. This clade, known as the Perennial Endemic North
        American (PENA) clade of Apiaceae, contains 18 genera and over 200 species. The results of this project will be
        presented here in the form of an electronic monograph that will include species descriptions, photographs,
        specimen records, distribution maps, phylogenetic trees, and more. This is a work in progress, and we hope to
        complete it by 2024.  An abstract of our project, <b>Collaborative Research: ARTS: Diversification in the Perennial
        Endemic North American (PENA) clade of Apiaceae: Defining genera and species in a major western North American
        radiation,</b> can be found <a href="https://www.nsf.gov/awardsearch/showAward?AWD_ID=1916885&HistoricalAwards=false" target="_blank" >here</a>.
    </div>

    <div style="margin:15px;display:flex;justify-content:space-between;">
        <div style="min-width:640px;max-width:700px;padding-right:15px;">
            Despite its size and importance in the western flora, the evolution of the Perennial Endemic North American
            (PENA) clade of Apiaceae is poorly understood, a fact reflected in the artificiality of its genera. This project
            will develop molecular-phylogenetic hypotheses of the PENA clade using comprehensive sampling of all known species
            and infraspecific taxa to serve as a framework to test specific hypotheses on species delimitation and relationships
            congruent with morphology, ecology, geography, and climate. In diverse and rapidly evolving groups, such the PENA
            clade, the inability to accurately define species boundaries limits any discussion of a plant’s value, use,
            ecological relationships or conservation.  Our work will utilize molecular, ecological, morphological, and
            climatic data to clarify the taxonomy and relationships of species within the PENA clade and provide a revised
            and updated classification for this group. An important part of this project is to develop this website, an online
            electronic monograph, that will include interactive identification guides, as well as species pages with descriptions,
            images, distribution maps, and links to specimen data.  The identification tools and species-specific environmental
            and ecological data provided on this website will aid in future research and in the conservation of the many rare
            species found within this group.
        </div>
        <div style="width:330px;">
            <div style="width:325px;">
                <img style="width:325px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/DSC02667.JPG" />
            </div>
            <div style="width:310px;margin-top:5px;margin-left:auto;margin-right:auto;font-size:12px;">
                Don Mansfield, Harold M. Tucker Herbarium, The College of Idaho
            </div>
        </div>
    </div>

    <div style="margin:15px;display:flex;justify-content:space-between;">
        <div style="width:230px;">
            <div style="width:225px;">
                <img style="width:225px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/DSC02708.JPG" />
            </div>
            <div style="width:210px;margin-top:5px;margin-left:auto;margin-right:auto;font-size:12px;">
                Jim Smith, Snake River Plains Herbarium, Boise State University
            </div>
        </div>
        <div style="min-width:740px;max-width:800px;padding-left:15px;">
            Because this study focuses on a large, diverse plant group in North America, it provides a wide range of prospects
            for scientific outreach and training across a broad cross section of target audiences. Students of all levels will
            be involved in this project. Undergraduate and graduate students from diverse backgrounds will be recruited to
            take part in field and laboratory work. Field studies in Idaho will be organized for K-12 students and their
            teachers, introducing them to the taxonomy, evolution, and ethnobotany of these plants. In addition, although the
            primary goal of this project is to better delimit the taxonomy and species boundaries of the PENA clade, the
            completion of this work will allow future studies to investigate patterns and processes of speciation, ecological
            interactions, community assembly and provide a greater understanding for the diversification of western North
            American Flora as well as the Biota that rely on that flora.
            <div style="width:560px;margin-top:20px;margin-left:auto;margin-right:auto">
                <div style="width:555px;">
                    <img style="width:555px;" src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/images/layout/DSC02997.JPG" />
                </div>
                <div style="width:540px;margin-top:5px;margin-left:auto;margin-right:auto;font-size:12px;">
                    Mary Ann Feist, Wisconsin State Herbarium, University of Wisconsin-Madison
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
