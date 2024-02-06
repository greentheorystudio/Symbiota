const collectionControlPanelMenus = {
    props: {
        userName: {
            type: String,
            default: null
        },
        collectionId: {
            type: Number,
            default: null
        },
        collType: {
            type: Number,
            default: null
        },
        occCount: {
            type: Number,
            default: 0
        },
        collAccessLevel: {
            type: String,
            default: null
        }
    },
    template: `
        <template v-if="collType === 'HumanObservation'">
            <div class="q-ml-md row justify-start q-gutter-lg">
                <div class="text-h6 text-bold">
                    Total Record Count: {{ occCount }}
                </div>
                <div>
                    <q-btn round color="primary" size=".6rem" @click="downloadPersonalOccurrences(collectionId);" icon="fas fa-download"></q-btn>
                </div>
            </div>
        </template>
        <div class="q-pa-lg">
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6">Data Editor Control Panel</div>
                    <ul>
                        <template v-if="collType === 'HumanObservation'">
                            <li>
                                <a :href="(clientRoot + '/collections/editor/observationsubmit.php?collid=' + collectionId)">
                                    Create A New Observation Record
                                </a>
                            </li>
                        </template>
                        <li>
                            <a :href="(clientRoot + '/collections/editor/occurrenceeditor.php?gotomode=1&collid=' + collectionId)">
                                Create A New Occurrence Record
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/editor/imageoccursubmit.php?collid=' + collectionId)">
                                Create A New Occurrence Record From An Image
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/editor/skeletalsubmit.php?collid=' + collectionId)">
                                Create A New Skeletal Occurrence Record
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/editor/occurrencetabledisplay.php?displayquery=1&collid=' + collectionId)">
                                View/Edit Existing Records
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/editor/batchdeterminations.php?collid=' + collectionId)">
                                Batch Determinations/Nomenclatural Adjustments
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/reports/labelmanager.php?collid=' + collectionId)">
                                Print Labels
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/reports/annotationmanager.php?collid=' + collectionId)">
                                Print Annotation Labels
                            </a>
                        </li>
                        <li>
                            <a :href="(clientRoot + '/collections/georef/batchgeoreftool.php?collid=' + collectionId)">
                                Batch Georeference Occurrences
                            </a>
                        </li>
                        <template v-if="collType === 'PreservedSpecimen'">
                            <li>
                                <a :href="(clientRoot + '/collections/loans/index.php?collid=' + collectionId)">
                                    Loan Management
                                </a>
                            </li>
                        </template>
                    </ul>
                </q-card-section>
            </q-card>
            <template v-if="collAccessLevel === 'admin'">
                <q-card flat bordered class="q-mt-md">
                    <q-card-section>
                        <div class="text-h6">Administration Control Panel</div>
                        <ul>
                            <li>
                                <a :href="(clientRoot + '/collections/misc/collmetadata.php?collid=' + collectionId)">
                                    Edit Collection Metadata
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/misc/commentlist.php?collid=' + collectionId)">
                                    View Posted Comments
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/reports/accessreport.php?collid=' + collectionId)">
                                    View Access Statistics
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/misc/collpermissions.php?collid=' + collectionId)">
                                    Manage Permissions
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/editor/editreviewer.php?collid=' + collectionId)">
                                    Review/Verify Occurrence Edits
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/upload/index.php?collid=' + collectionId)">
                                    Occurrence Data Upload Module
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/management/index.php?collid=' + collectionId)">
                                    Data Management Toolbox
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/management/politicalunits.php?collid=' + collectionId)">
                                    Geography Cleaning Module
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/management/taxonomycleaner.php?collid=' + collectionId)">
                                    Taxonomy Management Module
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/datasets/datapublisher.php?collid=' + collectionId)">
                                    Darwin Core Archive Publisher
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/download/index.php?collid=' + collectionId)">
                                    Data Exporter and Backup
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/management/thumbnailbuilder.php?collid=' + collectionId)">
                                    Thumbnail Builder
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/management/guidmapper.php?collid=' + collectionId)">
                                    GUID/UUID Generator
                                </a>
                            </li>
                            <li>
                                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collectionId + '&action=UpdateStatistics')">
                                    Update Statistics
                                </a>
                            </li>
                            <template v-if="solrMode">
                                <li>
                                    <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + collectionId + '&action=cleanSOLR')">
                                        Clean SOLR Index
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </q-card-section>
                </q-card>
            </template>
        </div>
    `,
    setup() {
        const store = useBaseStore();
        const clientRoot = store.getClientRoot;
        const solrMode = store.getSolrMode;

        function downloadPersonalOccurrences(collid) {
            const formData = new FormData();
            formData.append('collid', collid);
            formData.append('action', 'getPersonalOccurrencesCsvData');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    let csvContent = '';
                    resObj.forEach(row => {
                        const fixedRow = [];
                        row.forEach(val => {
                            if(val){
                                val = '\"' + val + '\"';
                            }
                            fixedRow.push(val);
                        });
                        csvContent += fixedRow.join(',') + '\n';
                    });
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8,' });
                    const filename = this.userName + '_' + Math.floor(new Date().getTime() / 1000).toString() + '.csv';
                    const elem = window.document.createElement('a');
                    elem.href = window.URL.createObjectURL(blob);
                    elem.download = filename;
                    document.body.appendChild(elem);
                    elem.click();
                    document.body.removeChild(elem);
                });
            });
        }

        return {
            clientRoot,
            solrMode,
            downloadPersonalOccurrences
        }
    }
};
