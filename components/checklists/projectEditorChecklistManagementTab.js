const projectEditorChecklistManagementTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <template v-if="projectChecklistArr.length > 0">
                <div class="column">
                    <div class="row justify-start q-gutter-md">
                        <div class="text-h6 text-bold">Checklists</div>
                    </div>
                    <div class="q-mt-xs q-ml-md column q-gutter-xs">
                        <template v-for="checklist in projectChecklistArr">
                            <div class="row justify-start q-gutter-md">
                                <div class="text-body1">
                                    <a :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist['clid'])">{{ checklist['name'] }}</a>
                                </div>
                                <div class="self-center">
                                    <q-btn color="white" text-color="black" size=".6rem" @click="removeChecklist(checklist['clid']);" icon="far fa-trash-alt" dense>
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Remove checklist
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="text-h4 text-bold">
                    There are no checklists linked to this project
                </div>
            </template>
            <q-card flat bordered>
                <q-card-section>
                    <div class="text-h6 text-bold">Add a checklist</div>
                    <div class="row justify-between q-gutter-sm no-wrap">
                        <div class="col-grow">
                            <selector-input-element :options="checklistOptions" label="Choose Checklist" :value="selectedChecklistId" option-value="clid" option-label="name" :clearable="true" @update:value="(value) => selectedChecklistId = value"></selector-input-element>
                        </div>
                        <div class="col-2 row justify-end">
                            <div>
                                <q-btn color="secondary" @click="addChecklist();" label="Add Checklist" :disabled="!selectedChecklistId" />
                            </div>
                        </div>
                    </div>
                </q-card-section>
            </q-card>
        </div>
    `,
    components: {
        'selector-input-element': selectorInputElement
    },
    setup() {
        const baseStore = useBaseStore();
        const checklistStore = useChecklistStore();
        const projectStore = useProjectStore();

        const checklistArr = Vue.ref([]);
        const checklistOptions = Vue.computed(() => {
            const returnArr = [];
            checklistArr.value.forEach((checklist) => {
                const checklistObj = projectChecklistArr.value.length > 0 ? projectChecklistArr.value.find(prochlist => Number(prochlist['clid']) === Number(checklist['clid'])) : null;
                if(!checklistObj){
                    returnArr.push(checklist);
                }
            });
            return returnArr;
        });
        const projectChecklistArr = Vue.computed(() => projectStore.getProjectChecklistArr);
        const selectedChecklistId = Vue.ref(null);
        const symbUid = baseStore.getSymbUid;

        function addChecklist() {
            projectStore.addChecklist(selectedChecklistId.value);
            selectedChecklistId.value = null;
        }

        function removeChecklist(clid) {
            projectStore.removeChecklist(clid);
        }

        function setChecklistArr() {
            checklistStore.getChecklistListByUid(symbUid, (checklistData) => {
                checklistArr.value = checklistData;
            });
        }

        Vue.onMounted(() => {
            setChecklistArr();
        });

        return {
            checklistOptions,
            projectChecklistArr,
            selectedChecklistId,
            addChecklist,
            removeChecklist
        }
    }
};
