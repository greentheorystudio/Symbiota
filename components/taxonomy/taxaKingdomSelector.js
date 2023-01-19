const kingdomOptions = Vue.ref([]);
let selectedKingdom = Vue.ref(null);
let selectedKingdomId = null;
let selectedKingdomName = null;

const taxaKingdomSelector = {
    props: [
        'disable'
    ],
    template: `
        <q-select outlined v-model="kingdom" id="targetkingdomselect" :options="kingdomOpts" label="Target Kingdom" @update:model-value="setKingdomId" :readonly="disable" dense />
    `,
    data() {
        return {
            kingdomOpts: kingdomOptions,
            kingdom: selectedKingdom
        };
    },
    mounted() {
        this.setKingdomOptions();
    },
    methods: {
        setKingdomId(kingdomobj) {
            selectedKingdomId = kingdomobj.value;
            selectedKingdomName = kingdomobj.label;
        },
        setKingdomOptions() {
            const url = taxonomyApiUrl + '?action=getKingdomArr';
            sendAPIGetRequest(url,function(status,res){
                if(status === 200) {
                    const data = JSON.parse(res);
                    for(let i in data){
                        if(data.hasOwnProperty(i)){
                            const kingObj = {};
                            kingObj['value'] = i;
                            kingObj['label'] = data[i];
                            kingdomOptions.value.push(kingObj);
                        }
                    }
                    kingdomOptions.value.sort(function (a, b) {
                        return a.label.toLowerCase().localeCompare(b.label.toLowerCase());
                    });
                }
            });
        }
    }
};
