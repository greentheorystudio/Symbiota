const taxonRankCheckboxSelector = {
    props: {
        selectedRanks: {
            type: Array,
            default: []
        },
        linkLabel: {
            type: String,
            default: 'Select Taxonomic Ranks'
        },
        innerLabel: {
            type: String,
            default: 'Select taxonomic ranks'
        },
        kingdomId: {
            type: Number,
            default: null
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    watch: {
        kingdomId: function(){
            this.setRankOptions();
        },
        selectedRanks: function(){
            this.setSelectAll();
        }
    },
    template: `
        <a class="anchor-link" @click="rankSelectDialog = true">{{ linkLabel }}</a>
        <q-dialog v-model="rankSelectDialog">
            <q-card>
                <q-card-section class="row items-center q-pb-none">
                    <div class="text-h6">{{ innerLabel }}</div>
                    <q-space></q-space>
                    <q-btn icon="close" flat round dense v-close-popup></q-btn>
                </q-card-section>
                <q-card-section>
                    <div>
                        <q-checkbox indeterminate-value="some" v-model="selectAll" label="Select All" :disable="disable" @update:model-value="selectAllChange" />
                    </div>
                    <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                    <template v-for="option in rankOptions">
                        <q-checkbox v-model="selectedRanks" :val="option.rankid" :label="option.rankname" :disable="disable" @update:model-value="processChange" />
                    </template>
                </q-card-section>
            </q-card>
        </q-dialog>
    `,
    data() {
        return {
            rankSelectDialog: Vue.ref(false),
            rankOptions: Vue.ref([]),
            rankArr: Vue.ref([]),
            selectAll: Vue.ref(false)
        };
    },
    mounted() {
        this.setRankOptions();
    },
    methods: {
        processChange(selectedArr) {
            this.$emit('update:selected-ranks', selectedArr);
        },
        setRankOptions() {
            const url = taxonomyApiUrl + '?action=getRankArr&kingdomid=' + this.kingdomId;
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                this.rankOptions = data;
                this.setRankArray();
            });
        },
        setRankArray() {
            this.rankArr = [];
            const stringKeys = Object.keys(this.rankOptions);
            stringKeys.forEach((key) => {
                this.rankArr.push(Number(key));
            });
            this.setSelectAll();
        },
        setSelectAll() {
            if(this.selectedRanks.length === 0){
                this.selectAll = false;
            }
            else if(this.selectedRanks.length === this.rankArr.length){
                this.selectAll = true;
            }
            else{
                this.selectAll = 'some';
            }
        },
        selectAllChange(val) {
            if(val){
                this.$emit('update:selected-ranks', this.rankArr);
            }
            else{
                this.$emit('update:selected-ranks', []);
            }
        }
    }
};
