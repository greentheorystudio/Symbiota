const taxaKingdomSelector = {
    props: [
        'selected-kingdom',
        'label',
        'disable'
    ],
    template: `
        <q-select outlined v-model="selectedKingdom" :options="kingdomOpts" option-value="id" option-label="name" :label="label" @update:model-value="processChange" :readonly="disable" dense options-dense />
    `,
    data() {
        return {
            kingdomOpts: Vue.ref([])
        };
    },
    mounted() {
        this.setKingdomOptions();
    },
    methods: {
        processChange(kingdomobj) {
            this.$emit('update:selected-kingdom', kingdomobj);
        },
        setKingdomOptions() {
            const url = taxonomyApiUrl + '?action=getKingdomArr';
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                this.kingdomOpts = data;
            });
        }
    }
};
