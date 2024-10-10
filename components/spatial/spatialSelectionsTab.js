const spatialSelectionsTab = {
    template: `
        <div class="column">
            <div class="q-px-sm q-mb-xs row justify-start">
                <div class="row q-gutter-sm">
                    <search-data-downloader :selection="true"></search-data-downloader>
                </div>
            </div>
            <div class="q-px-sm q-mb-xs row justify-between q-gutter-sm">
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processClearSelections();" label="Clear Selections" dense />
                </div>
                <div>
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="zoomToSelections();" label="Zoom to Selections" dense />
                </div>
            </div>
            <div class="q-px-sm q-mb-sm row justify-start">
                <div class="row q-gutter-sm">
                    <q-checkbox v-model="mapSettings.toggleSelectedPoints" label="Show Only Selected Points" @update:model-value="processToggleSelectedPoints"></q-checkbox>
                </div>
            </div>
            <q-separator ></q-separator>
            <div class="q-py-md">
                <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" :columns="columns" row-key="name" :loading="tableLoading" separator="cell" selection="multiple" wrap-cells hide-pagination>
                    <template v-slot:header="props">
                        <q-tr :props='props' class="bg-blue-grey-2">
                            <q-th></q-th>
                            <q-th v-for="col in props.cols" :key="col.name" :props="props">
                                {{ col.label }}
                            </q-th>
                        </q-tr>
                    </template>
                    <template v-slot:body="props">
                        <q-tr v-if="!tableLoading" :props="props">
                            <q-td>
                                <q-checkbox v-model="checkboxValue" @update:model-value="deselectRecord(props.row.siteid)" dense />
                            </q-td>
                            <q-td key="siteId" :props="props">
                                {{ props.row.siteid }}
                            </q-td>
                            <q-td key="siteName" :props="props">
                                <div class="column q-gutter-xs">
                                    <div class="fit text-left">
                                        <a class="cursor-pointer" @click="openRecordInfoWindow(props.row.siteid);">{{ props.row.name }}</a>
                                    </div>
                                    <div class="row justify-end">
                                        <q-btn color="grey-4" size="xs" text-color="black" class="q-ml-sm black-border" icon="fas fa-search-location" @click="setMapFinderPopup(props.row);" dense>
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                See location on map
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-td>
                            <q-td key="landUnitDetail" :props="props">
                                {{ props.row.landunitdetail }}
                            </q-td>
                        </q-tr>
                    </template>
                    <template v-slot:no-data>
                        <div class="text-bold">Loading...</div>
                    </template>
                    <template v-slot:loading>
                        <q-inner-loading showing color="primary"></q-inner-loading>
                    </template>
                </q-table>
            </div>
            <q-separator ></q-separator>
        </div>
    `,
    components: {
        'copy-url-button': copyURLButton,
        'list-display-button': listDisplayButton,
        'search-data-downloader': searchDataDownloader,
        'table-display-button': tableDisplayButton
    },
    setup() {
        const searchStore = useSearchStore();

        const checkboxValue = Vue.ref(true);
        const columns = [
            { name: 'siteId', label: 'Site ID', field: 'siteid' },
            { name: 'siteName', label: 'Site Name', field: 'name' },
            { name: 'landUnitDetail', label: 'Land Unit Detail', field: 'landunitdetail' }
        ];
        const layersObj = Vue.inject('layersObj');
        const mapSettings = Vue.inject('mapSettings');
        const recordDataArr = Vue.computed(() => searchStore.getSelections);
        const tableLoading = Vue.computed(() => (recordDataArr.value.length === 0));

        const findRecordClusterPosition = Vue.inject('findRecordClusterPosition');
        const openRecordInfoWindow = Vue.inject('openRecordInfoWindow');
        const processToggleSelectedChange = Vue.inject('processToggleSelectedChange');
        const showPopup = Vue.inject('showPopup');
        const updateMapSettings = Vue.inject('updateMapSettings');
        const updatePointStyle = Vue.inject('updatePointStyle');
        const zoomToSelections = Vue.inject('zoomToSelections');

        function deselectRecord(siteid) {
            checkboxValue.value = true;
            searchStore.removeRecordFromSelections(siteid);
            updatePointStyle(siteid);
            if(recordDataArr.value.length === 0){
                updateMapSettings('selectedRecordsSelectionsSymbologyTab', 'records');
            }
        }

        function processClearSelections() {
            searchStore.clearSelections();
            updateMapSettings('selectedRecordsSelectionsSymbologyTab', 'records');
            layersObj['pointv'].getSource().changed();
        }

        function processToggleSelectedPoints(value) {
            updateMapSettings('toggleSelectedPoints', value);
            processToggleSelectedChange();
        }

        function setMapFinderPopup(record) {
            const label = record.name ? record.name : record.siteid.toString();
            const recordPosition = findRecordClusterPosition(record.siteid);
            showPopup(label, recordPosition, false, true);
        }

        return {
            checkboxValue,
            columns,
            mapSettings,
            recordDataArr,
            tableLoading,
            deselectRecord,
            openRecordInfoWindow,
            processClearSelections,
            processToggleSelectedPoints,
            setMapFinderPopup,
            zoomToSelections
        }
    }
};
