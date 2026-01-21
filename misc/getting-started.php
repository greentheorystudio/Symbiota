<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
<?php
include_once(__DIR__ . '/../config/header-includes.php');
?>
<head>
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Getting Started</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css" />
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="mainContainer">
    <div class="q-pa-md">
        <div class="text-h5 text-bold">Getting started with the <?php echo $GLOBALS['DEFAULT_TITLE']; ?></div>
        <div class="q-mt-md text-h6 text-bold">Single Species Search</div>
        <div class="q-mb-md q-pl-md">
            <div>
                This is the easiest way to search for a species. Just select “Common Name” or “Scientific Name” in the upper left
                corner of the homepage search box. Begin typing and select from the options that pop up. Don’t see what you’re looking
                for? Try the Advanced Search. If you still can’t find it, <a href="mailto:irlwebmaster@si.edu" tabindex="0">let us know</a>!
            </div>
        </div>
        <div class="q-mt-md text-h6 text-bold">Advanced Search</div>
        <div class="q-mb-md q-pl-md">
            <div>
                Do you want to know what bird species (Class Aves) are in the IRL?  How about the number of bony fishes (Class
                Teleostei)? Try the Advanced Search tool. For best results, use taxonomic names when searching. We’re working
                on improving common name searches and will roll out enhancements soon.
            </div>
            <ul>
                <li>
                    Click on the "Advanced Search" link on the top right corner of the homepage search bar.
                </li>
                <li>
                    The easiest way to use this search is to enter the scientific name or group of interest (e.g., phylum,
                    class) in the “Scientific Name” field. Start typing and select the appropriate suggestion when it pops
                    up.
                </li>
                <li>
                    Keep the “Limit to species with information” box unchecked to return the full list of species.
                </li>
                <li>
                    Click the “Build Species List” button.
                </li>
                <li>
                    The search will return a table of hits that you can download by clicking on the “Download Results” button.
                    You can also navigate through the links in each row, from Kingdom to Scientific Name, for more information.
                </li>
            </ul>
        </div>
        <div class="q-mt-md text-h6 text-bold">Map Occurrence Search</div>
        <div class="q-mb-md q-pl-md">
            <div>
                If you want to know where in the watershed a particular species has been collected, the <a href="/spatial/index.php" tabindex="0">Map Occurrence Search</a>
                tool is for you. There are lots of neat features in this tool, and we’ve built an extensive tutorial to guide
                you.
            </div>
            <ul>
                <li>
                    Use the link above or navigate to the tool using the navigation ribbon in the header: Data Explorer > Map Occurrence Search.
                </li>
                <li>
                    Click on the question mark icon on the right side of the top navigation panel to open the tutorial.
                </li>
            </ul>
        </div>
        <div class="q-mt-md text-h6 text-bold">Dynamic Checklist</div>
        <div class="q-mb-md q-pl-md">
            <div>
                So, you’re headed to a local park this weekend and are wondering what species you might see? We got you. Build
                a <a href="/checklists/checklist.php" tabindex="0">Dynamic Checklist</a> for the area based on decades of scientific collections and observations.
            </div>
            <ul>
                <li>
                    Enter criteria for the checklist you would like to build in the popup window.
                </li>
                <li>
                    Click the Build Checklist button in the top right corner of the popup to build the dynamic checklist.
                </li>
            </ul>
        </div>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
<script type="text/javascript">
    const gettingStartedModule = Vue.createApp({
        setup() {
            const baseStore = useBaseStore();

            const clientRoot = baseStore.getClientRoot;
            const defaultTitle = baseStore.getDefaultTitle;
            const currentDay = Vue.computed(() => {
                const date = new Date();
                return date.toLocaleString('en-US', { day: '2-digit' });
            });
            const currentMonth = Vue.computed(() => {
                const date = new Date();
                return date.toLocaleString('en-US', { month: '2-digit' });
            });
            const currentMonthText = Vue.computed(() => {
                const date = new Date();
                const options = { month: 'long' };
                return new Intl.DateTimeFormat('en-US', options).format(date);
            });
            const currentYear = Vue.computed(() => {
                const date = new Date();
                return date.getFullYear();
            });
            const urlPrefix = Vue.computed(() => {
                return window.location.origin;
            });

            return {
                clientRoot,
                defaultTitle,
                currentDay,
                currentMonth,
                currentMonthText,
                currentYear,
                urlPrefix
            }
        }
    });
    gettingStartedModule.use(Quasar, { config: {} });
    gettingStartedModule.use(Pinia.createPinia());
    gettingStartedModule.mount('#mainContainer');
</script>
</body>
</html>
