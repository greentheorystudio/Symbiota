<?php
include_once(__DIR__ . '/../config/symbbase.php');
include_once(__DIR__ . '/../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$tId = array_key_exists('tid', $_REQUEST) ? (int)$_REQUEST['tid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Identification Character Management</title>
        <meta name="description" content="Identification character management module for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <span class="text-bold">Identification Character Management</span>
            </div>
            <div v-if="isEditor" class="q-pa-md column q-gutter-sm">
                <div class="q-mb-sm text-h5 text-bold">
                    Identification Character Management
                </div>
                <div class="row justify-end">
                    <div>
                        <q-btn color="primary" @click="openKeyCharacterHeaderEditorPopup(0);" label="Add Heading" tabindex="0" />
                    </div>
                </div>
                <template v-if="headingArr.length > 0">
                    <template v-for="header in headingArr">
                        <q-card>
                            <q-card-section class="column q-gutter-xs">
                                <div class="row justify-between">
                                    <div class="text-h6 text-bold q-mb-sm">{{ header.headingname }}</div>
                                    <div class="row justify-end q-gutter-sm">
                                        <div>
                                            <q-btn color="primary" size="sm" @click="openKeyCharacterEditorPopup(header['chid'], 0);" label="Add Character" dense tabindex="0"></q-btn>
                                        </div>
                                        <div>
                                            <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openKeyCharacterHeaderEditorPopup(header['chid']);" icon="fas fa-edit" dense aria-label="Edit heading record" dense tabindex="0">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Edit heading record
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                </div>
                                <div class="q-ml-sm">
                                    <template v-if="characterData.hasOwnProperty(header['chid']) && characterData[header['chid']].length > 0">
                                        <template v-for="character in characterData[header['chid']]">
                                            <div class="row justify-start q-gutter-sm">
                                                <div class="text-body1">{{ character['charactername'] }}</div>
                                                <div>
                                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openKeyCharacterEditorPopup(header['chid'], character['cid']);" icon="fas fa-edit" dense aria-label="Edit character record" tabindex="0">
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Edit character record
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div>There are currently no characters to display</div>
                                    </template>
                                </div>
                            </q-card-section>
                        </q-card>
                    </template>
                </template>
                <template v-else>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <div class="q-pa-md row justify-center text-h6 text-bold">
                        There is currently no key data to display. Please click the Add Header button to start adding data
                    </div>
                </template>
            </div>
            <template v-if="showKeyCharacterHeaderEditorPopup">
                <key-character-heading-editor-popup
                    :heading-id="editHeaderId"
                    :show-popup="showKeyCharacterHeaderEditorPopup"
                    @change:heading="setData"
                    @close:popup="showKeyCharacterHeaderEditorPopup = false"
                ></key-character-heading-editor-popup>
            </template>
            <template v-if="showKeyCharacterEditorPopup">
                <key-character-editor-popup
                    :heading-id="editHeaderId"
                    :character-id="editCharacterId"
                    :show-popup="showKeyCharacterEditorPopup"
                    @change:character="setData"
                    @close:popup="showKeyCharacterEditorPopup = false"
                ></key-character-editor-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/key-character-state.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/key-character.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/key-character-heading.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/confirmationPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/singleLanguageAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/keyCharacterAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/keyCharacterHeadingAutoComplete.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterEditorInfoTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterEditorCharacterStatesTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterEditorDependenceTab.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterStateEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/identification/keyCharacterHeadingEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const characterManagementModule = Vue.createApp({
                components: {
                    'key-character-editor-popup': keyCharacterEditorPopup,
                    'key-character-heading-editor-popup': keyCharacterHeadingEditorPopup
                },
                setup() {
                    const baseStore = useBaseStore();
                    const keyCharacterStore = useKeyCharacterStore();
                    const keyCharacterHeadingStore = useKeyCharacterHeadingStore();

                    const characterData = Vue.computed(() => keyCharacterStore.getKeyCharacterArrData);
                    const clientRoot = baseStore.getClientRoot;
                    const headingArr = Vue.computed(() => keyCharacterHeadingStore.getKeyCharacterHeadingArr);
                    const editCharacterId = Vue.ref(0);
                    const editHeaderId = Vue.ref(0);
                    const isEditor = Vue.ref(false);
                    const showKeyCharacterEditorPopup = Vue.ref(false);
                    const showKeyCharacterHeaderEditorPopup = Vue.ref(false);

                    function openKeyCharacterEditorPopup(headerid, characterid) {
                        editHeaderId.value = headerid;
                        editCharacterId.value = characterid;
                        showKeyCharacterEditorPopup.value = true;
                    }

                    function openKeyCharacterHeaderEditorPopup(headerid) {
                        editHeaderId.value = headerid;
                        showKeyCharacterHeaderEditorPopup.value = true;
                    }

                    function setData() {
                        keyCharacterHeadingStore.setKeyCharacterHeadingArr();
                    }

                    function setEditor() {
                        const formData = new FormData();
                        formData.append('permission', 'KeyAdmin');
                        formData.append('action', 'validatePermission');
                        fetch(permissionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            isEditor.value = resData.includes('KeyAdmin');
                            if(!isEditor.value){
                                window.location.href = clientRoot + '/index.php';
                            }
                        });
                    }

                    Vue.onMounted(() => {
                        setEditor();
                        setData();
                    });
                    
                    return {
                        characterData,
                        clientRoot,
                        editCharacterId,
                        editHeaderId,
                        headingArr,
                        isEditor,
                        showKeyCharacterEditorPopup,
                        showKeyCharacterHeaderEditorPopup,
                        openKeyCharacterEditorPopup,
                        openKeyCharacterHeaderEditorPopup,
                        setData
                    }
                }
            });
            characterManagementModule.use(Quasar, { config: {} });
            characterManagementModule.use(Pinia.createPinia());
            characterManagementModule.mount('#mainContainer');
        </script>
    </body>
</html>
