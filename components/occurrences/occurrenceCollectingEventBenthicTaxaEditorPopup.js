const occurrenceCollectingEventBenthicTaxaEditorPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-sm column justify-around q-col-gutter-sm fit">
                            <div class="q-pa-md text-body1 full-width">
                                Enter the Scientific Name for the taxon you would like to either add or edit. Rep counts for 
                                previously entered taxa matching the Scientific Name, ID Qualifier, and ID Remarks will 
                                automatically populate in the table. Once all of the Rep count data has been entered, click 
                                the Apply button to process the data.
                            </div>
                            <div class="column q-col-gutter-sm">
                                <div class="row justify-between q-col-gutter-sm">
                                    <div class="col-12 col-sm-4">
                                        <single-scientific-common-name-auto-complete :definition="occurrenceFieldDefinitions['sciname']" :sciname="taxonSciName" label="Scientific Name" @update:sciname="processScientificNameChange"></single-scientific-common-name-auto-complete>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <text-field-input-element :disabled="Number(taxonTid) > 0" :definition="occurrenceFieldDefinitions['scientificnameauthorship']" label="Author" :maxlength="occurrenceFields['scientificnameauthorship'] ? occurrenceFields['scientificnameauthorship']['length'] : 0" :value="taxonAuthor"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-2">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identificationqualifier']" label="ID Qualifier" :maxlength="occurrenceFields['identificationqualifier'] ? occurrenceFields['identificationqualifier']['length'] : 0" :value="taxonQualifier" @update:value="setRepData"></text-field-input-element>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <text-field-input-element :disabled="Number(taxonTid) > 0" :definition="occurrenceFieldDefinitions['family']" label="Family" :maxlength="occurrenceFields['family'] ? occurrenceFields['family']['length'] : 0" :value="taxonFamily"></text-field-input-element>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-grow">
                                        <text-field-input-element :definition="occurrenceFieldDefinitions['identificationremarks']" label="ID Remarks" :maxlength="occurrenceFields['identificationremarks'] ? occurrenceFields['identificationremarks']['length'] : 0" :value="taxonIdRemarks" @update:value="setRepData"></text-field-input-element>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-grow">
                                    <q-table flat bordered wrap-cells hide-pagination :rows="repData" :columns="tableColumns" :pagination="tablePagination" separator="cell">
                                        <template v-slot:header="props">
                                            <q-tr :props="props">
                                                <q-th v-for="col in props.cols" :key="col.name" :props="props">
                                                    <span class="text-bold">{{ col.label }}</span>
                                                </q-th>
                                            </q-tr>
                                        </template>
                                        <template v-slot:body-cell="props">
                                            <q-td :props="props">
                                                <q-input v-model.number="props.row[ props.col.name ]['cnt']" input-class="text-center" type="number" @update:model-value="(value) => validateRepDataCnt(props.col.name, value)" :readonly="!taxonDataKey" dense borderless></q-input>
                                            </q-td>
                                        </template>
                                    </q-table>
                                </div>
                            </div>
                            <div class="row justify-end full-width q-pr-lg">
                                <q-btn color="secondary" @click="preProcessEnteredData();" label="Apply" :disabled="!editsExist" />
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
        <confirmation-popup ref="confirmationPopupRef" @confirmation:click="processConfirmation"></confirmation-popup>
    `,
    components: {
        'confirmation-popup': confirmationPopup,
        'single-scientific-common-name-auto-complete': singleScientificCommonNameAutoComplete,
        'text-field-input-element': textFieldInputElement
    },
    setup(props, context) {
        const { hideWorking, showNotification, showWorking } = useCore();
        const occurrenceStore = Vue.inject('occurrenceStore');

        const benthicData = Vue.computed(() => occurrenceStore.getCollectingEventBenthicData);
        const confirmationPopupRef = Vue.ref(null);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const editsExist = Vue.computed(() => {
            let retValue = false;
            if(taxonSciName.value){
                const dataKeys = Object.keys(repData[0]);
                dataKeys.forEach(key => {
                    if(Number(repData[0][key]['cnt']) !== Number(existingData.value[key]['cnt'])){
                        retValue = true;
                    }
                });
            }
            return retValue;
        });
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const existingData = Vue.ref({});
        const occurrenceFields = Vue.inject('occurrenceFields');
        const occurrenceFieldDefinitions = Vue.inject('occurrenceFieldDefinitions');
        const processingAddArr = [];
        const processingDeleteArr = [];
        const processingUpdateArr = [];
        const repData = Vue.reactive([]);
        const tableColumns = Vue.ref([]);
        const tablePagination = {
            rowsPerPage: 0
        };
        const taxonAuthor = Vue.ref(null);
        const taxonDataKey = Vue.computed(() => {
            if(taxonSciName.value){
                return taxonSciName.value + (taxonQualifier.value ? ('-' + taxonQualifier.value) : '') + (taxonIdRemarks.value ? ('-' + taxonIdRemarks.value) : '');
            }
            else{
                return null;
            }
        });
        const taxonFamily = Vue.ref(null);
        const taxonIdRemarks = Vue.ref(null);
        const taxonQualifier = Vue.ref(null);
        const taxonSciName = Vue.ref(null);
        const taxonTid = Vue.ref(null);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        Vue.watch(benthicData, () => {
            setTableData();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function preProcessEnteredData() {
            const dataKeys = Object.keys(repData[0]);
            dataKeys.forEach(key => {
                if(Number(repData[0][key]['cnt']) !== Number(existingData.value[key]['cnt'])){
                    const repNumber = key.replace('rep','');
                    if(Number(repData[0][key]['occid']) === 0){
                        processingAddArr.push({rep: repNumber, cnt: repData[0][key]['cnt']});
                    }
                    else if(Number(repData[0][key]['cnt']) === 0){
                        processingDeleteArr.push(repData[0][key]['occid']);
                    }
                    else{
                        processingUpdateArr.push({occid: repData[0][key]['occid'], cnt: repData[0][key]['cnt']});
                    }
                }
            });
            if(processingAddArr.length > 0 || processingDeleteArr.length > 0 || processingUpdateArr.length > 0){
                let confirmText = 'The data you have entered will result in: ';
                if(processingAddArr.length > 0){
                    confirmText += (processingAddArr.length + ' record(s) being added');
                }
                if(processingDeleteArr.length > 0){
                    confirmText += ((processingAddArr.length > 0 ? '; ' : '') + processingDeleteArr.length + ' record(s) being deleted');
                }
                if(processingUpdateArr.length > 0){
                    confirmText += (((processingAddArr.length > 0 || processingDeleteArr.length > 0) ? '; ' : '') + processingUpdateArr.length + ' record(s) being updated');
                }
                confirmationPopupRef.value.openPopup(confirmText, {cancel: true, falseText: 'Stop', trueText: 'Proceed'});
            }
        }

        function processConfirmation(confirmed) {
            if(confirmed){
                showWorking();
                processEnteredData();
            }
        }

        function processEnteredData() {
            if(processingAddArr.length > 0){
                const recordToAdd = processingAddArr[0];
                processingAddArr.splice(0, 1);
                occurrenceStore.setCurrentOccurrenceRecord(0);
                occurrenceStore.updateOccurrenceEditData('scientificnameauthorship', taxonAuthor.value);
                occurrenceStore.updateOccurrenceEditData('sciname', taxonSciName.value);
                occurrenceStore.updateOccurrenceEditData('tid', taxonTid.value);
                occurrenceStore.updateOccurrenceEditData('family', taxonFamily.value);
                occurrenceStore.updateOccurrenceEditData('identificationqualifier', taxonQualifier.value);
                occurrenceStore.updateOccurrenceEditData('identificationremarks', taxonIdRemarks.value);
                occurrenceStore.updateOccurrenceEditData('rep', recordToAdd.rep);
                occurrenceStore.updateOccurrenceEditData('individualcount', recordToAdd.cnt);
                occurrenceStore.createOccurrenceRecord(() => {
                    processEnteredData();
                });
            }
            else if(processingDeleteArr.length > 0){
                const recordToDelete = processingDeleteArr[0];
                processingDeleteArr.splice(0, 1);
                occurrenceStore.evaluateOccurrenceForDeletion(recordToDelete, (data) => {
                    if(Number(data['images']) === 0 && Number(data['media']) === 0 && Number(data['checklists']) === 0 && Number(data['genetic']) === 0){
                        occurrenceStore.deleteOccurrenceRecord(recordToDelete, (res) => {
                            if(res === 0){
                                showNotification('negative', ('An error occurred while deleting occurrence record ' + recordToDelete + '.'));
                            }
                            processEnteredData();
                        });
                    }
                    else{
                        showNotification('negative', ('Occurrence record ' + recordToDelete + ' cannot be deleted because it has associated images, media, checklists, or genetic linkages.'));
                        processEnteredData();
                    }
                });
            }
            else if(processingUpdateArr.length > 0){
                const recordToUpdate = processingUpdateArr[0];
                processingUpdateArr.splice(0, 1);
                occurrenceStore.setCurrentOccurrenceRecord(recordToUpdate.occid, () => {
                    occurrenceStore.updateOccurrenceEditData('individualcount', recordToUpdate.cnt);
                    if(occurrenceStore.getOccurrenceEditsExist){
                        occurrenceStore.updateOccurrenceRecord((res) => {
                            if(res === 0){
                                showNotification('negative', ('There was an error saving the new count data for occurrence record ' + recordToUpdate.occid + '.'));
                            }
                            processEnteredData();
                        });
                    }
                    else{
                        processEnteredData();
                    }
                });
            }
            else{
                hideWorking();
                occurrenceStore.setCollectingEventBenthicData();
                context.emit('close:popup');
            }
        }

        function processScientificNameChange(taxon) {
            taxonSciName.value = taxon ? taxon.sciname : null;
            taxonTid.value = taxon ? taxon.tid : null;
            taxonFamily.value = taxon ? taxon.family : null;
            taxonAuthor.value = taxon ? taxon.author : null;
            setRepData();
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setRepData() {
            existingData.value = Object.assign({}, {});
            repData.length = 0;
            const newRowObj = {};
            tableColumns.value.forEach(repColumn => {
                newRowObj[repColumn.field] = {};
                existingData.value[repColumn.field] = {};
                if(taxonDataKey.value && benthicData.value && benthicData.value.hasOwnProperty(taxonDataKey.value) && benthicData.value[taxonDataKey.value].hasOwnProperty(repColumn.field)){
                    newRowObj[repColumn.field]['cnt'] = benthicData.value[taxonDataKey.value][repColumn.field]['cnt'];
                    existingData.value[repColumn.field]['cnt'] = benthicData.value[taxonDataKey.value][repColumn.field]['cnt'];
                    newRowObj[repColumn.field]['occid'] = benthicData.value[taxonDataKey.value][repColumn.field]['occid'];
                    existingData.value[repColumn.field]['occid'] = benthicData.value[taxonDataKey.value][repColumn.field]['occid'];
                }
                else{
                    newRowObj[repColumn.field]['cnt'] = 0;
                    existingData.value[repColumn.field]['cnt'] = 0;
                    newRowObj[repColumn.field]['occid'] = 0;
                    existingData.value[repColumn.field]['occid'] = 0;
                }
            });
            repData.push(newRowObj);
        }

        function setTableColumns() {
            let i = 0;
            do {
                const repIndex = 'rep' + (i + 1);
                const repLabel = 'Rep ' + (i + 1);
                tableColumns.value.push({ name: repIndex, align: 'center', label: repLabel, field: repIndex});
                i++;
            }
            while(i < Number(eventData.value.repcount));
        }

        function setTableData() {
            if(eventData.value && Number(eventData.value.repcount) > 0){
                setTableColumns();
                setRepData();
            }
        }

        function validateRepDataCnt(key, value) {
            if(Number(value) < 0){
                showNotification('negative', 'Rep counts cannot be less than zero.');
                repData[0][key]['cnt'] = 0;
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            setTableData();
        });

        return {
            confirmationPopupRef,
            contentRef,
            contentStyle,
            editsExist,
            occurrenceFields,
            occurrenceFieldDefinitions,
            repData,
            tableColumns,
            tablePagination,
            taxonAuthor,
            taxonDataKey,
            taxonFamily,
            taxonIdRemarks,
            taxonQualifier,
            taxonSciName,
            taxonTid,
            closePopup,
            preProcessEnteredData,
            processConfirmation,
            processScientificNameChange,
            setRepData,
            validateRepDataCnt
        }
    }
};
