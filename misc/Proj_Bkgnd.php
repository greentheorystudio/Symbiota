<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>Project Background</title>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" type="text/css"
          rel="stylesheet"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/jquery-ui.css" type="text/css" rel="stylesheet"/>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery.js" type="text/javascript"></script>
    <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/js/jquery-ui.js" type="text/javascript"></script>
    <?php include_once(__DIR__ . '/../config/googleanalytics.php'); ?>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="innertext">
    <h2>Project Background</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td align="center">
                <p class="title">An Overview of the IRL Species Inventory Project</p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/IRLCAMPBELL11.jpg" width="436" height="328"/></td>
        </tr>
    </table>
    <br/>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">The geographical position of the Indian River Lagoon (IRL), straddling the zone of
                    overlap between temperate and subtropical biotic provinces, contributes to its vast species
                    richness. The IRL had often been purported to be one of the most diverse estuarine systems in the
                    continental United States; however, evidence to support this status was lacking. Having no
                    documentation of the high biodiversity of the IRL not only hampered scientific understanding of this
                    complex system, but also was seen as a major drawback to developing a comprehensive management
                    strategy to protect biodiversity in the Indian River Lagoon.</p>
                <p class="body">Following the first IRL Conference on Biodiversity held in 1994, the apparent lack of
                    evidence to support claims of high biodiversity in the IRL prompted Dr. Hilary Swain and her
                    colleagues Susan E. Hopkins and Clarissa L. Thornton to compile the Indian River Lagoon Species
                    Inventory, a taxonomic listing of 2,493 species of protists, plants and animals occurring in the
                    IRL. The inventory provided the first substantial evidence that attested to the high species
                    richness of the IRL system. </p>
                <br/>
                <p class="body">The initial IRL Species Inventory was compiled by performing literature searches, and by
                    surveying colleagues with taxonomic and ecological expertise in IRL biota. Over 70 people
                    contributed information to the project. Species included in the inventory were those which: 1) occur
                    within the IRL during some stage in their life cycles; 2) those which utilize adjacent wetland
                    habitat areas; 3) those bird species which frequent the flyway above the lagoon; and 4) species
                    which occur most often in adjacent upland habitats (scrub, shoreline plants, etc.), but which are
                    also tolerant of estuarine conditions.</p>
                <p class="body">
                    Clearly, some taxonomic groups have been studied more extensively than others, thus the initial
                    inventory was somewhat biased in coverage. Taxonomic groups such as mollusks, fishes, birds,
                    echinoderms, sipunculids, and some protozoan groups have extensive and possibly complete species
                    listings, while other groups such as the vascular plants, amphibians, reptiles, macroalgae, sponges
                    and other groups would benefit from more extensive documentation in the IRL.</p>

                <p class="title">Our Goal:</p>
                <p class="body">
                    The Smithsonian Marine Station (SMS) at Fort Pierce became the depository for the Indian River
                    Lagoon Species Inventory in 1997 thanks, in large part, to the efforts of Mary Rice (then Director
                    of SMS) and Joseph Dineen (Research Associate). Funding was subsequently procurred to ensure initial
                    efforts were not lost and that the inventory would continue to be expanded as new IRL information
                    became available. The IRL Species Inventory went on-line in 1999. Since becoming Director of the
                    marine station in 2002, Valerie Paul has continued to prioritize the IRL Species Inventory as a
                    valuable component of the Smithsonian Marine Station's public outreach program.</p>
                <p class="body">Our goal in obtaining the Inventory is to enhance scientific knowledge of the IRL
                    ecosystem and to further the promotion of public awareness and the need for stewardship of the
                    IRL.In order to accomplish this objective, we have: substantially expanded the initial taxonomic
                    species database; added ecological and life history information; included extensive documentation of
                    IRL's many habitats; and have added a wealth of information for IRL citizenry including information
                    on personal behavior modification relative to improved IRL water quality.</p>
            </td>
        </tr>
        <tr>
            <td align="center"><img src="../content/imglib/IRLSI_DIAGRAM.jpg" width="455" height="267"/></td>
        </tr>
        <tr>
            <td>
                <p class="body">Improving awareness and increasing knowledge about all aspects of the Indian River
                    Lagoon is perhaps the most important step in increasing stewardship of the lagoon, and preserving
                    and sustaining this invaluable natural resource for future generations. We have and will continue to
                    strive over the years to make the Indian River Lagoon Species Inventory a useful, relevant, and
                    current educational tool for the scientific research community, resource managerial and academic
                    groups and the general public.</p></td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
