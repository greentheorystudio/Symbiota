<?php
include_once(__DIR__ . '/config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Home</title>
        <meta name="description" content="Welcome to the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <meta name='keywords' content='' />
    </head>
    <body>
        <?php
        include(__DIR__ . '/header.php');
        ?>
        <div id="mainContainer">
            <div class="q-pa-md">
                <h1 class="q-mb-md">Welcome to SeedTrack</h1>

                <div style="padding: 0 10px;">
                    The Symbiota software system has developed a library of webtools to aid biologists in establishing specimen-based
                    biological research and can be extended to the seed banking plant conservation community and expand its number of users.
                    Symbiota is a data access technology connecting biodiversity collections around the world and serves as a data management
                    system primarily for in-situ natural history specimen collections. The new SeedTrack module proposed here will be an
                    ex-situ data management extension and a new feature of the open access Symbiota software package. Some of the data
                    structure and its multi-user functionality currently exists within the Symbiota software as Darwin Core terms. The Darwin
                    Core is a predefined subset of the terms that have common use across a wide variety of biodiversity applications.
                    The terms used in the Simple Darwin Core are those that are found at the cross-section of taxonomic names, places,
                    and events that document biological specimen occurrences on the planet. The two driving principles are simplicity
                    and flexibility.
                </div>

                <div style="margin-top:10px;padding: 0 10px;">
                    The module will be developed using existing functionality within Symbiota to track ex-situ plant and seed collections,
                    and add further functionality to track plant propagation methods used in growing events and link those events to
                    specimen based collections. The new data module will have the ability to be visible to users who wish to incorporate
                    the new plant conservation feature.
                </div>
                <div style="margin-top:10px;padding: 0 10px;">
                    The restoration module will be developed as an extension to the Symbiota biological informatics system to:
                    <ol start="1" type="1">
                        <li>Provide users (plant conservationists, seed collectors, data managers, curators, and researchers) with
                            a core data standard to track propagule specimen collections (primarily seed) and seedlings in both
                            cultivated and wild populations.
                        </li>
                        <li>Standardize data to assess the seed viability in disjunct species populations.</li>
                        <li>Track and share specimen collection events, propagation methodology, source native species plant material,
                            and document out-planted seedlings.</li>
                        <li>Allow for the tracking of changes that occur during the propagation and growing cycle such as changes
                            in fertilizer regime, changes in pot size, and plant mortality rate.</li>
                    </ol>
                </div>
            </div>
        </div>
        <?php
        include(__DIR__ . '/footer.php');
        include_once(__DIR__ . '/config/footer-includes.php');
        ?>
        <script>
            const homePageModule = Vue.createApp();
            homePageModule.use(Quasar, { config: {} });
            homePageModule.use(Pinia.createPinia());
            homePageModule.mount('#mainContainer');
        </script>
    </body>
</html>
