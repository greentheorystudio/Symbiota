const newTaxonEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md column q-col-gutter-sm">
                            <div class="row justify-between">
                                <div>
                                    <span v-if="errorMessage" class="text-subtitle1 text-bold text-red">{{ errorMessage }}</span>
                                </div>
                                <div class="row justify-end q-gutter-xs">
                                    <q-btn color="secondary" @click="addTaxon();" label="Add Taxon" :disabled="!taxonValid" tabindex="0" />
                                </div>
                            </div>
                            <taxon-field-module :data="taxonData" @update:taxon-data="(data) => updateTaxonData(data.key, data.value)"></taxon-field-module>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'taxon-field-module': taxonFieldModule
    },
    setup(props, context) {
        const { showNotification } = useCore();
        const taxaStore = useTaxaStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const errorMessage = Vue.computed(() => {
            let errorMessage = null;
            if(taxonData.value['sciname'] && taxonData.value['sciname'].length > 0){
                if(Number(taxonData.value['rankid']) !== 10 && Number(taxonData.value['parenttid']) === 0){
                    errorMessage = 'Parent taxon required.';
                }
                else if(!uniqueTaxon.value){
                    errorMessage = 'Taxon already exists in the thesaurus.';
                }
            }
            return errorMessage;
        });
        const taxonData = Vue.ref(null);
        const taxonValid = Vue.computed(() => {
            return (
                uniqueTaxon.value &&
                (taxonData.value['sciname'] && taxonData.value['sciname'].length > 0) &&
                (Number(taxonData.value['rankid']) === 10 || Number(taxonData.value['kingdomid'])) > 0 &&
                Number(taxonData.value['rankid']) > 0 &&
                (Number(taxonData.value['rankid']) === 10 || Number(taxonData.value['parenttid']) > 0)
            );
        });
        const uniqueTaxon = Vue.ref(false);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addTaxon() {
            taxaStore.createTaxonRecord(taxonData.value, (newTaxonId) => {
                if(newTaxonId > 0){
                    context.emit('taxon:created', newTaxonId);
                    context.emit('close:popup');
                    showNotification('positive','Taxon added successfully.');
                }
                else{
                    showNotification('negative', 'There was an error adding the new taxon.');
                }
            });
        }

        function closePopup() {
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function updateTaxonData(key, value) {
            taxonData.value[key] = value;
            if(key === 'sciname' || key === 'kingdomid'){
                validateUniqueTaxon();
            }
        }

        function validateUniqueTaxon() {
            uniqueTaxon.value = false;
            if(Number(taxonData.value['rankid']) === 10){
                uniqueTaxon.value = true;
            }
            else if(taxonData.value['sciname'] && taxonData.value['sciname'].length > 0 && Number(taxonData.value['kingdomid']) > 0){
                const formData = new FormData();
                formData.append('action', 'getTaxonFromSciname');
                formData.append('kingdomid', taxonData.value['kingdomid'].toString());
                formData.append('sciname', taxonData.value['sciname']);
                fetch(taxaApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    if(!data.hasOwnProperty('tid') || Number(data['tid']) === 0){
                        uniqueTaxon.value = true;
                    }
                });
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxonData.value = taxaStore.getBlankTaxaRecord;
        });

        return {
            contentRef,
            contentStyle,
            errorMessage,
            taxonData,
            taxonValid,
            addTaxon,
            closePopup,
            updateTaxonData
        }
    }
};
