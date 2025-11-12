const taxonRankSelector = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        kingdomId: {
            type: Number,
            default: null
        },
        label: {
            type: String,
            default: null
        },
        rankIdFilterArr: {
            type: Array,
            default: []
        },
        tabindex: {
            type: Number,
            default: 0
        },
        value: {
            type: Number,
            default: null
        }
    },
    template: `
        <q-select outlined v-model="selectedOption" popup-content-class="z-max" behavior="menu" :options="rankOptions" option-value="rankid" option-label="rankname" :label="label" @update:model-value="processChange" :tabindex="tabindex" :readonly="disabled" dense options-dense />
    `,
    setup(props, context) {
        const propsRefs = Vue.toRefs(props);
        const rankOptionData = Vue.ref({});
        const rankOptions = Vue.computed(() => {
            const returnArr = [];
            Object.keys(rankOptionData.value).forEach((rankid) => {
                if(!props.rankIdFilterArr.includes(Number(rankid))){
                    returnArr.push(rankOptionData.value[rankid]);
                }
            });
            return returnArr;
        });
        const selectedOption = Vue.computed(() => {
            return rankOptions.value.find(option => Number(option['rankid']) === Number(props.value));
        });

        Vue.watch(propsRefs.kingdomId, () => {
            setRankOptions();
        });

        function processChange(value) {
            context.emit('update:value', value);
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
                rankOptionData.value = data;
            });
        }

        Vue.onMounted(() => {
            setRankOptions();
        });

        return {
            rankOptions,
            selectedOption,
            processChange
        }
    }
};
