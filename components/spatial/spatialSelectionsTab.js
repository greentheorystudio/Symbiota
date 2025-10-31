const spatialSelectionsTab = {
    template: `
        <div class="column">
            <div class="q-pa-md row justify-between">
                <div class="column q-gutter-xs">
                    <div class="row q-gutter-sm">
                        <search-data-downloader :selections="true"></search-data-downloader>
                    </div>
                    <div class="row q-gutter-sm">
                        <q-checkbox v-model="mapSettings.toggleSelectedPoints" label="Show Only Selected Points" @update:model-value="processToggleSelectedPoints" tabindex="0"></q-checkbox>
                    </div>
                </div>
                <div class="column q-gutter-xs">
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="processClearSelections();" label="Clear Selections" dense tabindex="0" />
                    <q-btn color="grey-4" text-color="black" class="black-border" size="md" @click="zoomToSelections();" label="Zoom to Selections" dense tabindex="0" />
                </div>
            </div>
            <q-separator ></q-separator>
            <div>
                <q-table flat bordered class="spatial-record-table" :rows="recordDataArr" :columns="columns" row-key="name" :loading="tableLoading" separator="cell" selection="multiple" :rows-per-page-options="[0]" wrap-cells hide-pagination dense>
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
                                <q-checkbox v-model="checkboxValue" @update:model-value="deselectRecord(props.row.occid)" dense aria-label="Deselect record" tabindex="0" />
                            </q-td>
                            <q-td key="catalognumber" :props="props">
                                {{ props.row.catalognumber }}
                            </q-td>
                            <q-td key="collector" :props="props">
                                <div class="column q-gutter-xs">
                                    <div class="fit text-left">
                                        <a role="button" class="cursor-pointer" @click="openRecordInfoWindow(props.row.occid);" aria-label="View record details" tabindex="0">{{ (props.row.collector ? props.row.collector : '[No data]') }}</a>
                                    </div>
                                    <div class="row justify-end">
                                        <q-btn color="grey-4" size="xs" text-color="black" class="q-ml-sm black-border" icon="fas fa-search-location" @click="setMapFinderPopup(props.row);" dense aria-label="See location on map" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                See location on map
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                </div>
                            </q-td>
                            <q-td key="eventdate" :props="props">
                                {{ props.row.eventdate }}
                            </q-td>
                            <q-td key="sciname" :props="props">
                                <template v-if="Number(props.row.tid) > 0">
                                    <a :href="(clientRoot + '/taxa/index.php?taxon=' + props.row.tid)" target="_blank" :aria-label="( props.row.sciname + ' taxon profile page page - Opens in separate tab')" tabindex="0">{{ props.row.sciname }}</a>
                                </template>
                                <template v-else>
                                    {{ props.row.sciname }}
                                </template>
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
        </div>
    `,
    components: {
        'search-data-downloader': searchDataDownloader
    },
    setup() {
        const baseStore = useBaseStore();
        const searchStore = useSearchStore();

        const checkboxValue = Vue.ref(true);
        const clientRoot = baseStore.getClientRoot;
        const columns = [
            { name: 'catalognumber', label: 'Catalog #', field: 'catalognumber' },
            { name: 'collector', label: 'Collector', field: 'collector' },
            { name: 'eventdate', label: 'Date', field: 'eventdate' },
            { name: 'sciname', label: 'Scientific Name', field: 'sciname' }
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

        function deselectRecord(occid) {
            checkboxValue.value = true;
            searchStore.removeRecordFromSelections(occid);
            updatePointStyle(occid);
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
            const label = record.collector ? record.collector : record.occid.toString();
            const recordPosition = findRecordClusterPosition(record.occid);
            showPopup(label, recordPosition, false, true);
        }

        return {
            checkboxValue,
            clientRoot,
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
