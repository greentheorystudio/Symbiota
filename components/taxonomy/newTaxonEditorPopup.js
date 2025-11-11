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
                                <div></div>
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
        const taxonData = Vue.ref(null);
        const taxonValid = Vue.computed(() => {
            return (
                (taxonData.value['sciname'] && taxonData.value['sciname'].length > 0) &&
                (Number(taxonData.value['rankid']) === 10 || Number(taxonData.value['kingdomid'])) > 0 &&
                Number(taxonData.value['rankid']) > 0 &&
                (Number(taxonData.value['rankid']) === 10 || Number(taxonData.value['parenttid']) > 0)
            );
        });

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function addTaxon() {
            taxaStore.createTaxonRecord(taxonData.value, (newTaxonId) => {
                if(newTaxonId > 0){
                    showNotification('positive','Taxon added successfully.');
                    context.emit('taxon:created', newTaxonId);
                    context.emit('close:popup');
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
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            taxonData.value = taxaStore.getBlankTaxaRecord;
        });

        return {
            contentRef,
            contentStyle,
            taxonData,
            taxonValid,
            addTaxon,
            closePopup,
            updateTaxonData
        }
    }
};
