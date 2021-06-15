<?php
include_once(__DIR__ . '/../config/symbini.php');
header("Content-Type: text/html; charset=" . $GLOBALS['CHARSET']);
?>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<head>
    <title>A Word About Species Names...</title>
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
    <h2>A Word About Species Names...</h2>
    <table style="border:0;width:700px;margin-left:auto;margin-right:auto;">
        <tr>
            <td><p class="body">Many people wonder why scientists use complicated, hard to pronounce names when speaking
                    about familiar animals and plants. Common names such as blue crab, redfish, and dolphin are
                    successfully used in casual communication, and convey an immediate idea of what a particular animal
                    or plant looks like. Scientists avoid using common names because they are often not specific to a
                    particular species. For instance, the blue crab is only one of many species of crabs that can be
                    described as blue. In the Indian River Lagoon alone, there are several species of "blue crabs": the
                    blue crab (Callinectes sapidus), the lesser blue crab (Callinectes similis), the red blue crab
                    (Callinectes bocourti), the ornate blue crab (Callinectes ornatus), and the blue land crab
                    (Cardisoma guanhumi).<br/>
                    <br/>
                    Common names can also be confusing in other respects. Another familiar problem occurs when a species
                    has more than one common name. For example, the striped mullet, Mugil cephalus, is found all over
                    the world, and has a variety of common names (striped mullet, black mullet, sea mullet, flathead
                    mullet, and gray mullet) which are used in different regions. Still another problem arises when a
                    species is so obscure that it has no common name. Thus, to avoid any confusion or ambiguity in
                    scientific research, biologists use scientific names in identifying species.<br/>
                </p>
                <p class="title">What's in a Name?</p>
                <table width="300" class="image_left">
                    <tr>
                        <td><img border="0" src="../content/imglib/CommonNames.jpg" hspace="2" vspace="2" width="299"
                                 height="177"></td>
                    </tr>
                </table>
                <p class="body">
                    The system of naming species was first developed by Swedish botanist and physician, Carolus Linnaeus
                    in the mid- 1700s. Linnaeus is the father of the branch of biology called taxonomy, which seeks to
                    describe, name and classify organisms. His system of naming species, still in use today, begins with
                    assigning all species a two-part Latin name called a binomial. The first word of the binomial is the
                    genus name of the species, and the second word is the specific epithet for the species. For example
                    (see figure above), the scientific name for the blue crab is Callinectes sapidus. Callinectes, the
                    genus name, is the collective term which includes many species of crabs closely related to the blue
                    crab. The specific epithet, sapidus, describes exactly which of the Callinectes species is being
                    identified.</p>

                <p class="title">How does Taxonomy Work?</p>

                <table width="200" cellpadding="0" cellspacing="0" class="image_left">
                    <tr>
                        <td><img src="../content/imglib/CatSpecies.jpg" width="201" height="106"></td>
                    </tr>
                </table>
                <p class="body">Linnaeus' original classification system is based on 2 main goals. The first is to
                    distinguish between closely related species and assign them as separate species based on differences
                    in specific traits called diagnostic characters. The second goal of taxonomy is to organize groups
                    of similar species into broader and more collective categories. For example, the species name for
                    the domestic cat is Felis catus. Felis denotes the genus name for this species, while catus denotes
                    the unique specific epithet for the species.</p>
                <table width="200" class="image_left">
                    <tr>
                        <td><img border="0" src="../content/imglib/CatGenus.jpg" width="244" height="106"></td>
                    </tr>
                </table>
                <p class="body">The housecat is closely related to several other feline species
                    such as the bobcat, <i>Felis rufus</i>, and the cougar, <i>Felis concolor</i>,
                    so they are all placed in the same genus.</p>
                <div class="clear"></div>
                <br/>

                <table style="clear:both;" width="200">
                    <tr>
                        <td><img border="0" src="../content/imglib/CatFamily.jpg" width="249" height="182"></td>
                    </tr>
                </table>
                <p class="body">Members of the genus <i>Felis</i> are also
                    related, though less closely, to other cat genera such as <i>Panthera</i>, which
                    includes lions, leopards and tigers; and <i>Leopardus</i>, which includes
                    the ocelots. Because the members of all of these genera are cats, they can
                    be grouped together under the family Felidae.</p>
                <div class="clear"></div>
                <br/>
                <table width="200" class="image_left">
                    <tr>
                        <td><img border="0" src="../content/imglib/CatOrder.jpg" width="276" height="142" align="left">
                        </td>
                    </tr>
                </table>
                <p class="body">At the Order level, cats are grouped with
                    other animals that are quite different in physical appearance and general
                    behavior, but with whom they share other basic attributes. In this case,
                    cats, dogs, bears and some other groups are all predators that hunt and prey
                    upon other animals. They are thus grouped together in the Order Carnivora,
                    which includes meat eating animals.</p>
                <div class="clear"></div>
                <br/>

                <table width="200" class="image_left">
                    <tr>
                        <td><img border="0" src="../content/imglib/CatClass.jpg" width="293" height="182"></td>
                    </tr>
                </table>
                <p class="body">At the Class level, cats and other
                    predatory animals are grouped with non-predators with whom they share specific
                    biological traits. In this case cats, dogs, bears, sheep, horses, cows,
                    giraffes, whales, and many other groups, including people, belong
                    to Class Mammalia (mammals). All mammals have hair, are warm-blooded, and give birth to live young
                    which feed
                    via mammary glands.</p>

                <p class="body">At the Phylum level, cats are included with all other vertebrate animals in the
                    subphylum Vertebrata, in the Phylum Chordata. This large grouping includes all animals having either
                    a notochord, or an actual spine. Lastly, the most inclusive taxonomic grouping is the Kingdom.
                    Biologists often delimit the basic taxonomic groupings used to classify all living things into a 5
                    kingdom scheme. Kingdom Monera includes all the bacteria and other Prokaryotic cell types; Kingdom
                    Protista includes all the algae and single celled Eukaryotes; Kingdom Animalia includes all the
                    animals; Kingdom Plantae includes all the plants; and Kingdom Fungi includes all of the fungi and
                    molds.</p>
            </td>
        </tr>
    </table>
</div>
<?php
include(__DIR__ . '/../footer.php');
?>
</body>
</html>
