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
    <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Terms of Use</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
</head>
<body>
<?php
include(__DIR__ . '/../header.php');
?>
<div id="mainContainer">
    <div class="q-pa-md">
        <div class="text-h5 text-bold">Terms of Use</div>
        <div class="q-mt-md text-h6 text-bold">Recommended Citation Formats</div>
        <div class="q-pl-md q-mt-md q-mb-md">
            Use one of the following formats to cite data retrieved from the {{ defaultTitle }} network:
            <div class="q-mt-sm text-body1 text-bold">General Citation:</div>
            <div class="q-pl-md q-mb-md">
                {{ (defaultTitle + '. ' + currentYear + '. ' + urlPrefix + clientRoot + '/index.php. Accessed on ' + currentMonthText + ' ' + currentDay + '.') }}
            </div>
            <div class="text-body1 text-bold">Usage of occurrence data from specific institutions:</div>
            <div class="q-pl-md">
                Biodiversity occurrence data published by: &lt;List of Collections&gt;
                (Accessed through {{ defaultTitle }} Data Portal,
                {{ urlPrefix + clientRoot + '/index.php' }}, YYYY-MM-DD)
                <div class="q-mt-sm">
                    <span class="text-bold">For example:</span><br/>
                    Biodiversity occurrence data published by:
                    Field Museum of Natural History, Museum of Vertebrate Zoology, and New York Botanical Garden
                    (Accessed through {{ defaultTitle }} Data Portal,
                    {{ urlPrefix + clientRoot + '/index.php, ' + currentYear + '-' + currentMonth + '-' + currentDay + ')' }}
                </div>
            </div>
        </div>
        <div class="text-h6 text-bold">Occurrence Record Use Policy</div>
        <div class="q-mb-md">
            <ul>
                <li>
                    While {{ defaultTitle }} will make every effort possible to control and document the quality
                    of the data it publishes, the data are made available "as is". Any report of errors in the data should be
                    directed to the appropriate curators and/or collections managers.
                </li>
                <li>
                    {{ defaultTitle }} cannot assume responsibility for damages resulting from mis-use or
                    mis-interpretation of datasets or from errors or omissions that may exist in the data.
                </li>
                <li>
                    It is considered a matter of professional ethics to cite and acknowledge the work of other scientists that
                    has resulted in data used in subsequent research. We encourages users to
                    contact the original investigator responsible for the data that they are accessing.
                </li>
                <li>
                    {{ defaultTitle }} asks that users not redistribute data obtained from this site without permission for data owners.
                    However, links or references to this site may be freely posted.
                </li>
            </ul>
        </div>
        <div class="text-h6 text-bold">Images</div>
        <div class="q-pl-md q-mb-md">
            Images within this website have been generously contributed by their owners to
            promote education and research. These contributors retain the full copyright for
            their images. Unless stated otherwise, images are made available under the Creative Commons
            Attribution-ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC BY-SA</a>)
            Users are allowed to copy, transmit, reuse, and/or adapt content, as long as attribution
            regarding the source of the content is made. If the content is altered, transformed, or enhanced,
            it may be re-distributed only under the same or similar license by which it was acquired.
        </div>
        <div class="text-h6 text-bold">Notes on Occurrence Records and Images</div>
        <div class="q-pl-md q-mb-md">
            Occurrences are used for scientific research and because of skilled preparation and
            careful use they may last for hundreds of years. Some collections have specimens
            that were collected over 100 years ago that are no longer occur within the area.
            By making these records available on the web as images, their availability and
            value improves without an increase in inadvertent damage caused by use. Note that
            if you are considering making specimens, remember collecting normally requires
            permission of the landowner and, in the case of rare and endangered plants,
            additional permits may be required. It is best to coordinate such efforts with a
            regional institution that manages a publically accessible collection.
        </div>
    </div>
</div>
<?php
include_once(__DIR__ . '/../config/footer-includes.php');
include(__DIR__ . '/../footer.php');
?>
<script type="text/javascript">
    const usagePolicyModule = Vue.createApp({
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
    usagePolicyModule.use(Quasar, { config: {} });
    usagePolicyModule.use(Pinia.createPinia());
    usagePolicyModule.mount('#mainContainer');
</script>
</body>
</html>
