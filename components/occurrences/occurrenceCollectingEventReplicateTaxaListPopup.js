const occurrenceCollectingEventReplicateTaxaListPopup = {
    props: {
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="lg-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-md">
                            <q-table flat bordered wrap-cells hide-pagination :rows="tableRows" :columns="tableColumns" :pagination="tablePagination" row-key="name" separator="cell">
                                <template v-slot:header="props">
                                    <q-tr :props="props">
                                        <q-th v-for="col in props.cols" :key="col.name" :props="props">
                                            <span class="text-bold">{{ col.label }}</span>
                                        </q-th>
                                    </q-tr>
                                </template>
                                <template v-slot:body="props">
                                    <q-tr :props="props">
                                        <template v-for="column in tableColumns">
                                            <q-td :key="column.name" :props="props">
                                                <template v-if="!column.name.startsWith('rep') || Number(props.row[column.name]) === 0">
                                                    <template v-if="column.name === 'sciname'">
                                                        <div class="row justify-between">
                                                            <div>
                                                                {{ props.row[column.name] }}
                                                            </div>
                                                            <div>
                                                                <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="processEditTaxon(props.row['taxon']);" icon="fas fa-edit" dense aria-label="Edit taxon records" tabindex="0">
                                                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                                        Edit taxon records
                                                                    </q-tooltip>
                                                                </q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        {{ props.row[column.name] }}
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div role="button" class="cursor-pointer" @click="processOccurrenceSelection(props.row['occidData'][column.name]);" @keyup.enter="processOccurrenceSelection(props.row['occidData'][column.name]);" aria-label="View record" tabindex="0">
                                                        {{ props.row[column.name] }}
                                                    </div>
                                                </template>
                                            </q-td>
                                        </template>
                                    </q-tr>
                                </template>
                            </q-table>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const occurrenceStore = useOccurrenceStore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const eventData = Vue.computed(() => occurrenceStore.getCollectingEventData);
        const replicateData = Vue.computed(() => occurrenceStore.getCollectingEventReplicateData);
        const showQualifierColumn = Vue.ref(false);
        const showRemarksColumn = Vue.ref(false);
        const tableColumns = Vue.ref([]);
        const tableColumnsReps = [];
        const tableColumnsTaxon = [];
        const tablePagination = {
            rowsPerPage: 0
        };
        const tableRows = Vue.shallowReactive([]);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        Vue.watch(replicateData, () => {
            if(replicateData.value){
                setTableColumns();
                setTableRows();
            }
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processEditTaxon(taxon) {
            context.emit('update:edit-taxon', taxon);
        }

        function processOccurrenceSelection(occid) {
            occurrenceStore.setCurrentOccurrenceRecord(occid);
            context.emit('close:popup');
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setTableColumns() {
            for(let key in replicateData.value) {
                if(replicateData.value.hasOwnProperty(key)){
                    if(replicateData.value[key].hasOwnProperty('identificationqualifier') && replicateData.value[key]['identificationqualifier']){
                        showQualifierColumn.value = true;
                    }
                    if(replicateData.value[key].hasOwnProperty('identificationremarks') && replicateData.value[key]['identificationremarks']){
                        showRemarksColumn.value = true;
                    }
                }
            }
            if(showQualifierColumn.value){
                tableColumnsTaxon.push({ name: 'qualifier', align: 'left', label: 'Qualifier', field: 'qualifier', sortable: true });
            }
            tableColumnsTaxon.push({ name: 'sciname', align: 'left', label: 'Scientific Name', field: 'sciname', sortable: true });
            if(showRemarksColumn.value){
                tableColumnsTaxon.push({ name: 'remarks', align: 'left', label: 'Remarks', field: 'remarks', sortable: true });
            }
            let i = 0;
            do {
                const repIndex = 'rep' + (i + 1);
                const repLabel = 'Rep ' + (i + 1);
                tableColumnsReps.push({ name: repIndex, align: 'center', label: repLabel, field: repIndex, sortable: true, sort: (a, b) => parseInt(a, 10) - parseInt(b, 10) });
                i++;
            }
            while(i < Number(eventData.value.repcount));
            tableColumns.value = tableColumnsTaxon.concat(tableColumnsReps);
        }

        function setTableRows() {
            const taxaArr = Object.keys(replicateData.value);
            taxaArr.forEach(taxon => {
                const newRowObj = {};
                newRowObj['occidData'] = {};
                newRowObj['taxon'] = replicateData.value[taxon];
                if(showQualifierColumn.value){
                    newRowObj['qualifier'] = replicateData.value[taxon]['identificationqualifier'];
                }
                newRowObj['sciname'] = replicateData.value[taxon]['sciname'];
                if(showRemarksColumn.value){
                    newRowObj['remarks'] = replicateData.value[taxon]['identificationremarks'];
                }
                tableColumnsReps.forEach(repColumn => {
                    if(replicateData.value[taxon].hasOwnProperty(repColumn.field)){
                        newRowObj[repColumn.field] = replicateData.value[taxon][repColumn.field]['cnt'];
                        newRowObj['occidData'][repColumn.field] = replicateData.value[taxon][repColumn.field]['occid'];
                    }
                    else{
                        newRowObj[repColumn.field] = 0;
                    }
                });
                tableRows.push(newRowObj);
            });
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            if(replicateData.value){
                setTableColumns();
                setTableRows();
            }
        });

        return {
            contentRef,
            contentStyle,
            tableColumns,
            tablePagination,
            tableRows,
            closePopup,
            processEditTaxon,
            processOccurrenceSelection
        }
    }
};
