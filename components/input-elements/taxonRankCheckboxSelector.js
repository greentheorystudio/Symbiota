const taxonRankCheckboxSelector = {
    props: {
        disable: {
            type: Boolean,
            default: false
        },
        innerLabel: {
            type: String,
            default: 'Select taxonomic ranks'
        },
        kingdomId: {
            type: Number,
            default: null
        },
        linkLabel: {
            type: String,
            default: 'Select Taxonomic Ranks'
        },
        requiredRanks: {
            type: Array,
            default: []
        },
        selectedRanks: {
            type: Array,
            default: []
        },
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <div class="text-bold text-h6 cursor-pointer" @click="rankSelectDialog = true">{{ linkLabel }}</div>
        <q-dialog v-model="rankSelectDialog" class="z-top">
            <q-card>
                <div class="row justify-end q-pb-none">
                    <q-btn icon="close" flat round dense v-close-popup aria-label="Close pop up" :tabindex="tabindex"></q-btn>
                </div>
                <q-card-section class="row justify-between q-pb-none">
                    <div class="text-h6">{{ innerLabel }}</div>                  
                </q-card-section>
                <q-card-section>
                    <div>
                        <q-checkbox indeterminate-value="some" v-model="selectAll" label="Select All" :disable="disable" @update:model-value="selectAllChange" :tabindex="tabindex" />
                    </div>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <template v-for="option in rankOptions">
                        <q-checkbox v-model="selectedRanks" :val="option.rankid" :label="option.rankname" :disable="disable" @update:model-value="processChange" :tabindex="tabindex" />
                    </template>
                </q-card-section>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const propsRefs = Vue.toRefs(props);
        const rankSelectDialog = Vue.ref(false);
        const rankOptions = Vue.ref({});
        const rankArr = Vue.ref([]);
        const selectAll = Vue.ref(false);

        Vue.watch(propsRefs.kingdomId, () => {
            setRankOptions();
        });

        Vue.watch(propsRefs.selectedRanks, () => {
            setSelectAll();
        });

        function processChange(selectedArr) {
            props.requiredRanks.forEach((rank) => {
                if(!selectedArr.includes(rank)){
                    selectedArr.push(rank);
                }
            });
            context.emit('update:selected-ranks', selectedArr);
        }

        function selectAllChange(val) {
            if(val){
                context.emit('update:selected-ranks', rankArr.value);
            }
            else{
                context.emit('update:selected-ranks', props.requiredRanks);
            }
        }

        function setRankArray() {
            rankArr.value = [];
            const stringKeys = Object.keys(rankOptions.value);
            stringKeys.forEach((key) => {
                rankArr.value.push(Number(key));
            });
            const selectedArr = props.selectedRanks.slice();
            selectedArr.forEach((rank) => {
                if(!rankArr.value.includes(Number(rank))){
                    const index = selectedArr.indexOf(rank);
                    selectedArr.splice(index,1);
                }
            });
            context.emit('update:selected-ranks', selectedArr);
            setSelectAll();
        }

        function setRankOptions() {
            const url = taxonRankApiUrl + '?action=getRankArr&kingdomid=' + props.kingdomId;
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                rankOptions.value = data;
                setRankArray();
            });
        }

        function setSelectAll() {
            if(props.selectedRanks.length === 0){
                selectAll.value = false;
            }
            else if(props.selectedRanks.length === rankArr.value.length){
                selectAll.value = true;
            }
            else{
                selectAll.value = 'some';
            }
        }

        Vue.onMounted(() => {
            setRankOptions();
        });
        
        return {
            rankSelectDialog,
            rankOptions,
            selectAll,
            processChange,
            selectAllChange
        }
    }
};
