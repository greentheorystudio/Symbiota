const collectionCatalogNumberQuickSearch = {
    props: {
        collectionId: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-card flat bordered class="full-width">
            <q-card-section class="q-pa-xs full-width row justify-between">
                <div class="col-5">
                    <q-input outlined v-model="catalogNumber" label="Catalog Number Quick Search" @keyup.enter="processSearch" dense tabindex="0" />
                </div>
                <div class="col-grow row justify-end self-center q-mr-md">
                    <div>
                        <q-btn color="secondary" size="sm" @click="processSearch" label="Go To Record" :disabled="!catalogNumber" tabindex="0" />
                    </div>
                </div>
            </q-card-section>
        </q-card>
    `,
    setup(props) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();

        const catalogNumber = Vue.ref(null);
        const clientRoot = baseStore.getClientRoot;

        function processSearch() {
            if(catalogNumber.value && catalogNumber.value.length > 0){
                const formData = new FormData();
                formData.append('catalognumber', catalogNumber.value.toString());
                formData.append('collid', props.collectionId.toString());
                formData.append('action', 'getOccurrencesByCatalogNumber');
                fetch(occurrenceApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.json().then((data) => {
                        if(data.length === 0){
                            showNotification('negative', 'There are no records with that catalog number.');
                        }
                        else if(data.length > 1){
                            showNotification('negative', 'There are multiple records with that catalog number.');
                        }
                        else{
                            window.location.href = (clientRoot + '/collections/editor/occurrenceeditor.php?occid=' + data[0]['occid']);
                        }
                    });
                });
            }
            else{
                showNotification('negative', 'Please enter a catalog number to search.');
            }
        }

        return {
            catalogNumber,
            processSearch
        }
    }
};
