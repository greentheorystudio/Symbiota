const occurrenceEditorModule = {
    props: {
        occid: {
            type: Number,
            default: 0
        }
    },
    template: `
        <template v-if="isLocked">
            <q-card dark bordered class="bg-red-9">
                <q-card-section>
                    <div class="text-h6">This record is locked</div>
                    <div class="text-subtitle2">This record is locked for editing by another user. Once the user is done with the record, the lock will be removed. Records are locked for a minimum of 15 minutes.</div>
                </q-card-section>
            </q-card>
        </template>
        <template v-else>
            <div>
        
            </div>
        </template>
    `,
    setup(props) {
        const occurrenceStore = Vue.inject('occurrenceStore');
        const { checkObjectNotEmpty } = useCore();
        const isLocked = Vue.computed(() => occurrenceStore.getIsLocked);
        const occurrenceData = Vue.computed(() => occurrenceStore.getOccurrenceData);
        const propsRefs = Vue.toRefs(props);

        function processSetOccurrenceData() {
            if(Number(props.occid) > 0){
                occurrenceStore.setOccurrenceData(props.occid);
            }
            else if(checkObjectNotEmpty(occurrenceData.value)){
                occurrenceStore.clearOccurrenceData();
            }
        }

        Vue.watch(propsRefs.occid, () => {
            processSetOccurrenceData();
        });

        Vue.onMounted(() => {
            processSetOccurrenceData();
        });

        return {
            isLocked,
            occurrenceData
        }
    }
};
