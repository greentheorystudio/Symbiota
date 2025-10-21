const collectionControlPanelMenus = {
    props: {
        collectionId: {
            type: Number,
            default: null
        },
        collectionPermissions: {
            type: Array,
            default: []
        },
        collectionType: {
            type: Number,
            default: null
        },
        userName: {
            type: String,
            default: null
        }
    },
    template: `
        <div class="q-px-md q-py-sm row justify-between q-col-gutter-x-sm">
            <template v-if="collectionPermissions.includes('SuperAdmin') || collectionPermissions.includes('CollEditor') || collectionPermissions.includes('CollAdmin')">
                <template v-if="collectionPermissions.includes('SuperAdmin') || collectionPermissions.includes('CollAdmin')">
                    <div class="col-12 col-sm-6">
                        <q-card flat bordered>
                            <q-card-section>
                                <div class="text-h6 text-bold">Administration Control Panel</div>
                                <div class="q-mt-xs q-pl-sm column">
                                    <div>
                                        <a :href="(clientRoot + '/collections/misc/collmetadata.php?collid=' + collectionId)">
                                            Edit Collection Metadata
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/misc/collpermissions.php?collid=' + collectionId)">
                                            Manage Permissions
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/editor/editreviewer.php?collid=' + collectionId)">
                                            Review/Verify Occurrence Edits
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/upload/index.php?collid=' + collectionId)">
                                            Occurrence Data Upload Module
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/management/index.php?collid=' + collectionId)">
                                            Data Management Toolbox
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/management/politicalunits.php?collid=' + collectionId)">
                                            Geography Cleaning Module
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/management/taxonomycleaner.php?collid=' + collectionId)">
                                            Taxonomy Management Module
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/datasets/datapublisher.php?collid=' + collectionId)">
                                            Darwin Core Archive Publisher
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/download/index.php?collid=' + collectionId)">
                                            Data Exporter and Backup
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/management/thumbnailbuilder.php?collid=' + collectionId)">
                                            Thumbnail Builder
                                        </a>
                                    </div>
                                    <div>
                                        <a :href="(clientRoot + '/collections/management/guidmapper.php?collid=' + collectionId)">
                                            GUID/UUID Generator
                                        </a>
                                    </div>
                                    <div class="cursor-pointer">
                                        <a @click="updateCollectionStatistics()">
                                            Update Statistics
                                        </a>
                                    </div>
                                    <template v-if="solrMode">
                                        <div class="cursor-pointer">
                                            <a @click="cleanSOLRIndex()">
                                                Clean SOLR Index
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </template>
                <div class="col-12 col-sm-6">
                    <q-card flat bordered>
                        <q-card-section>
                            <div class="text-h6 text-bold">Data Editor Control Panel</div>
                            <div class="q-mt-xs q-pl-sm column">
                                <div>
                                    <collection-catalog-number-quick-search :collection-id="collectionId"></collection-catalog-number-quick-search>
                                </div>
                                <div class="q-mt-sm">
                                    <a :href="(clientRoot + '/collections/editor/occurrenceeditor.php?gotomode=1&collid=' + collectionId)">
                                        Occurrence Editor
                                    </a>
                                </div>
                                <div>
                                    <a :href="(clientRoot + '/collections/table.php?collid=' + collectionId)">
                                        Occurrence Table Viewer
                                    </a>
                                </div>
                                <div>
                                    <a :href="(clientRoot + '/collections/editor/batchdeterminations.php?collid=' + collectionId)">
                                        Batch Determinations/Nomenclatural Adjustments
                                    </a>
                                </div>
                                <div>
                                    <a :href="(clientRoot + '/collections/reports/labelmanager.php?collid=' + collectionId)">
                                        Print Labels
                                    </a>
                                </div>
                                <div>
                                    <a :href="(clientRoot + '/collections/reports/annotationmanager.php?collid=' + collectionId)">
                                        Print Annotation Labels
                                    </a>
                                </div>
                                <div>
                                    <a :href="(clientRoot + '/collections/georef/batchgeoreftool.php?collid=' + collectionId)">
                                        Batch Georeference Occurrences
                                    </a>
                                </div>
                                <template v-if="collectionType === 'PreservedSpecimen'">
                                    <div>
                                        <a :href="(clientRoot + '/collections/loans/index.php?collid=' + collectionId)">
                                            Loan Management
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </q-card-section>
                    </q-card>
                </div>
            </template>
        </div>
    `,
    components: {
        'collection-catalog-number-quick-search': collectionCatalogNumberQuickSearch
    },
    setup(props) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const baseStore = useBaseStore();
        const collectionStore = useCollectionStore();

        const clientRoot = baseStore.getClientRoot;
        const solrMode = baseStore.getSolrMode;

        function cleanSOLRIndex() {
            showWorking();
            collectionStore.cleanSOLRIndex(props.collectionId, (res) => {
                hideWorking();
                if(res > 0){
                    showNotification('positive','Collection statistics updated successfully.');
                }
                else{
                    showNotification('negative', 'There was an error updating the collection statistics.');
                }
            });
        }

        function updateCollectionStatistics() {
            showWorking();
            collectionStore.updateCollectionStatistics(props.collectionId, (res) => {
                hideWorking();
                if(res > 0){
                    showNotification('positive','Collection statistics updated successfully.');
                }
                else{
                    showNotification('negative', 'There was an error updating the collection statistics.');
                }
            });
        }

        return {
            clientRoot,
            solrMode,
            cleanSOLRIndex,
            updateCollectionStatistics
        }
    }
};
