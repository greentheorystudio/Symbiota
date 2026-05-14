const institutionsSelector = {
    props: {
        disable: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: null
        },
        setOptions: {
            type: Array,
            default: []
        },
        selectedLocation: {
            type: Object,
            default: null
        },
        tabindex: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-select outlined v-model="selectedLocation" popup-content-class="z-top" behavior="menu" :options="locationOpts" :label="label" @update:model-value="processChange" :tabindex="tabindex" :readonly="disable" dense options-dense />
    `,
    setup(props, context) {
        const institutionsArr = Vue.ref([]);
        const institutionOpts = Vue.computed(() => {
            let returnVal = [];
            institutionsArr.value.forEach((institution) => {
                returnVal.push({
                    value: institution.iid,
                    label: institution.institutionname + " (" + institution.institutioncode + ")"
                });
            });
            return returnVal;
        });

        function processChange(institutionobj) {
            context.emit('update:selected-location', institutionobj);
        }

        function setInstitutionOptions() {
            const formData = new FormData();
            formData.append('action', 'getInstitutionsArr');
            fetch(institutionsApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                institutionsArr.value = resData;
            });
        }

        Vue.onMounted(() => {
            setInstitutionOptions();
        });

        return {
            institutionOpts,
            processChange
        }
    }
};
