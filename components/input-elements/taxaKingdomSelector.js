const taxaKingdomSelector = {
    props: {
        disable: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: null
        },
        selectedKingdom: {
            type: Object,
            default: null
        },
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-select outlined v-model="selectedKingdom" popup-content-class="z-max" behavior="menu" :options="kingdomOpts" option-value="id" option-label="name" :label="label" @update:model-value="processChange" :tabindex="tabindex" :readonly="disable" dense options-dense />
    `,
    setup(props, context) {
        const kingdomOpts = Vue.ref([]);

        function processChange(kingdomobj) {
            context.emit('update:selected-kingdom', kingdomobj);
        }

        function setKingdomOptions() {
            const url = taxonKingdomApiUrl + '?action=getKingdomArr';
            fetch(url)
            .then((response) => {
                if(response.ok){
                    return response.json();
                }
            })
            .then((data) => {
                kingdomOpts.value = data;
            });
        }

        Vue.onMounted(() => {
            setKingdomOptions();
        });

        return {
            kingdomOpts,
            processChange
        }
    }
};
