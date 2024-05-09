const taxonomyDataSourceBulletSelector = {
    props: {
        selectedDataSource: {
            type: String,
            default: 'col'
        },
        disable: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-card flat bordered>
            <q-card-section>
                <div class="text-subtitle1 text-weight-bold">Select Taxonomic Data Source</div>
                <q-option-group :options="dataSourceOptions" type="radio" v-model="selectedOption" :disable="disable" @update:model-value="processChange" dense />
            </q-card-section>
        </q-card>
    `,
    setup(props, context) {
        const dataSourceOptions = [
            { label: 'Catalogue of Life (COL)', value: 'col' },
            { label: 'Integrated Taxonomic Information System (ITIS)', value: 'itis' },
            { label: 'World Register of Marine Species (WoRMS)', value: 'worms' }
        ];
        const propsRefs = Vue.toRefs(props);
        const selectedOption = Vue.ref(null);

        Vue.watch(propsRefs.selectedDataSource, () => {
            setSelectedOption();
        });

        function processChange(datasourceobj) {
            context.emit('update:selected-data-source', datasourceobj);
        }

        function setSelectedOption() {
            selectedOption.value = props.selectedDataSource;
        }

        Vue.onMounted(() => {
            setSelectedOption();
        });

        return {
            dataSourceOptions,
            selectedOption,
            processChange
        }
    }
};
