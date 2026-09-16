<?php
include_once(__DIR__ . '/../../config/symbbase.php');
include_once(__DIR__ . '/../../services/SanitizerService.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
if(!$GLOBALS['SYMB_UID']) {
    header('Location: ../../profile/index.php?refurl=' .SanitizerService::getCleanedRequestPath(true));
}

$collid = array_key_exists('collid', $_REQUEST) ? (int)$_REQUEST['collid'] : 0;
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> Measurement or Fact Field Configuration Module</title>
        <meta name="description" content="Measurement or fact field configuration module for collection occurrence records in the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <script type="text/javascript">
            const COLLID = <?php echo $collid; ?>;
        </script>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../../header.php');
        ?>
        <div id="mainContainer">
            <div id="breadcrumbs">
                <a :href="(clientRoot + '/index.php')" tabindex="0">Home</a> &gt;&gt;
                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collectionId)" tabindex="0">Collection Control Panel</a> &gt;&gt;
                <span class="text-bold">Measurement or Fact Field Configurations</span>
            </div>
            <div class="q-pa-md">
                <div class="row justify-between q-px-md q-mb-md">
                    <div class="text-h5 text-bold">Measurement or Fact Field Configurations</div>
                    <div>
                        <q-btn-toggle v-if="collectionData['datarecordingmethod'] === 'lot' || collectionData['datarecordingmethod'] === 'replicate'" v-model="selectedMofType" :options="mofTypeOptions" class="black-border" size="md" rounded unelevated toggle-color="primary" color="white" text-color="primary" aria-label="Measurement or fact data type" tabindex="0"></q-btn-toggle>
                    </div>
                </div>
                <template v-if="isEditor">
                    <q-card>
                        <q-tabs v-model="tab" class="q-px-sm q-pt-sm" content-class="bg-grey-3" active-bg-color="grey-4" align="left">
                            <q-tab name="fields" label="Fields" no-caps></q-tab>
                            <q-tab name="layout" label="Layout" no-caps></q-tab>
                        </q-tabs>
                        <q-separator></q-separator>
                        <q-tab-panels v-model="tab">
                            <q-tab-panel name="fields">
                                <div class="fit column q-gutter-md">
                                    <div class="row justify-between">
                                        <div>

                                        </div>
                                        <div class="row justify-end q-gutter-sm">
                                            <div>
                                                <q-btn color="primary" @click="openLayerEditPopup();" label="Add Layer" tabindex="0" />
                                            </div>
                                            <div>
                                                <q-btn color="primary" @click="openLayerGroupEditPopup();" label="Add Layer Group" tabindex="0" />
                                            </div>
                                        </div>
                                    </div>
                                    <template v-if="layerConfigArr.length > 0">
                                        <draggable v-model="layerConfigArr" v-bind="dragOptions" class="q-gutter-sm items-center" group="configItem" item-key="id" :move="validateDragDrop" @add="processDragDrop" @update="processDragDrop">
                                            <template #item="{ element: configData }">
                                                <template v-if="configData['type'] === 'layer'">
                                                    <layers-configurations-layer-element :id="configData['id']" :layer="configData" @edit:layer="openLayerEditPopup"></layers-configurations-layer-element>
                                                </template>
                                                <template v-else-if="configData['type'] === 'layerGroup'">
                                                    <layers-configurations-layer-group-element :id="configData['id']" :layer-group="configData" :expanded-group-arr="expandedGroupArr" @show:layer-group="expandLayerGroup" @hide:layer-group="hideLayerGroup" @edit:layer-group="openLayerGroupEditPopup" @edit:layer="openLayerEditPopup" @update:layers-arr="processDragDrop"></layers-configurations-layer-group-element>
                                                </template>
                                            </template>
                                        </draggable>
                                    </template>
                                    <template v-else>
                                        <div class="q-pa-md row justify-center text-subtitle1 text-bold">
                                            There is currently no layer data to display
                                        </div>
                                    </template>
                                </div>
                            </q-tab-panel>
                            <q-tab-panel name="layout">

                            </q-tab-panel>
                        </q-tab-panels>
                    </q-card>
                </template>
            </div>
        </div>
        <?php
        include_once(__DIR__ . '/../../config/footer-includes.php');
        include(__DIR__ . '/../../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const measurementOrFactFieldConfigurationModule = Vue.createApp({
                components: {
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { showNotification } = useCore();
                    const baseStore = useBaseStore();
                    const collectionStore = useCollectionStore();

                    const clientRoot = baseStore.getClientRoot;
                    const collectionData = Vue.computed(() => collectionStore.getCollectionData);
                    const collectionId = Vue.computed(() => collectionStore.getCollectionId);
                    const collId = COLLID;
                    const currentDataFields = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataFieldsEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataFieldsEdit.value;
                        }
                        else{
                            returnVal = locationDataFieldsEdit.value;
                        }
                        return returnVal;
                    });
                    const currentDataFieldsLayoutData = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataFieldsLayoutDataEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataFieldsLayoutDataEdit.value;
                        }
                        else{
                            returnVal = locationDataFieldsLayoutDataEdit.value;
                        }
                        return returnVal;
                    });
                    const currentDataLabel = Vue.computed(() => {
                        let returnVal;
                        if(selectedMofType.value === 'occurrence'){
                            returnVal = occurrenceDataLabelEdit.value;
                        }
                        else if(selectedMofType.value === 'event'){
                            returnVal = eventDataLabelEdit.value;
                        }
                        else{
                            returnVal = locationDataLabelEdit.value;
                        }
                        return returnVal;
                    });
                    const eventDataFields = Vue.computed(() => collectionStore.getEventMofDataFields);
                    const eventDataFieldsEdit = Vue.ref(null);
                    const eventDataFieldsLayoutData = Vue.computed(() => collectionStore.getEventMofDataFieldsLayoutData);
                    const eventDataFieldsLayoutDataEdit = Vue.ref(null);
                    const eventDataLabel = Vue.computed(() => collectionStore.getEventMofDataLabel);
                    const eventDataLabelEdit = Vue.ref(null);
                    const eventLabelEditsExist = Vue.computed(() => {
                        return eventDataLabel.value !== eventDataLabelEdit.value;
                    });
                    const eventLayoutDataEditsExist = Vue.computed(() => {
                        return JSON.stringify(eventDataFieldsLayoutData.value) !== JSON.stringify(eventDataFieldsLayoutDataEdit.value);
                    });
                    const isEditor = Vue.computed(() => {
                        return collectionStore.getCollectionPermissions.includes('CollAdmin');
                    });
                    const locationDataFields = Vue.computed(() => collectionStore.getLocationMofDataFields);
                    const locationDataFieldsEdit = Vue.ref(null);
                    const locationDataFieldsLayoutData = Vue.computed(() => collectionStore.getLocationMofDataFieldsLayoutData);
                    const locationDataFieldsLayoutDataEdit = Vue.ref(null);
                    const locationDataLabel = Vue.computed(() => collectionStore.getLocationMofDataLabel);
                    const locationDataLabelEdit = Vue.ref(null);
                    const mofTypeOptions = [
                        {label: 'Occurrence', value: 'occurrence'},
                        {label: 'Event', value: 'event'},
                        {label: 'Location', value: 'location'}
                    ];
                    const occurrenceDataFields = Vue.computed(() => collectionStore.getOccurrenceMofDataFields);
                    const occurrenceDataFieldsEdit = Vue.ref(null);
                    const occurrenceDataFieldsLayoutData = Vue.computed(() => collectionStore.getOccurrenceMofDataFieldsLayoutData);
                    const occurrenceDataFieldsLayoutDataEdit = Vue.ref(null);
                    const occurrenceDataLabel = Vue.computed(() => collectionStore.getOccurrenceMofDataLabel);
                    const occurrenceDataLabelEdit = Vue.ref(null);
                    const selectedMofType = Vue.ref('occurrence');
                    const tab = Vue.ref('fields');

                    Vue.watch(selectedMofType, () => {
                        tab.value = 'fields';
                    });

                    Vue.onMounted(() => {
                        collectionStore.setCollection(collId, () => {
                            if(Number(collId) > 0 && !isEditor.value){
                                window.location.href = baseStore.getClientRoot + '/index.php';
                            }
                            else{
                                eventDataFieldsEdit.value = Object.assign({}, eventDataFields.value);
                                eventDataFieldsLayoutDataEdit.value = Object.assign({}, eventDataFieldsLayoutData.value);
                                eventDataLabelEdit.value = eventDataLabel.value;
                                locationDataFieldsEdit.value = Object.assign({}, locationDataFields.value);
                                locationDataFieldsLayoutDataEdit.value = Object.assign({}, locationDataFieldsLayoutData.value);
                                locationDataLabelEdit.value = locationDataLabel.value;
                                occurrenceDataFieldsEdit.value = Object.assign({}, occurrenceDataFields.value);
                                occurrenceDataFieldsLayoutDataEdit.value = Object.assign({}, occurrenceDataFieldsLayoutData.value);
                                occurrenceDataLabelEdit.value = occurrenceDataLabel.value;
                            }
                        });
                    });

                    return {
                        clientRoot,
                        collectionData,
                        collectionId,
                        currentDataFields,
                        currentDataFieldsLayoutData,
                        currentDataLabel,
                        isEditor,
                        mofTypeOptions,
                        selectedMofType,
                        tab
                    }
                }
            });
            measurementOrFactFieldConfigurationModule.use(Quasar, { config: {} });
            measurementOrFactFieldConfigurationModule.use(Pinia.createPinia());
            measurementOrFactFieldConfigurationModule.mount('#mainContainer');
        </script>
    </body>
</html>
