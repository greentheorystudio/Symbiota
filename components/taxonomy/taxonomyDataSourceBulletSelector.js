const taxonomyDataSourceBulletSelector = {
    props: [
        'selected-data-source',
        'disable'
    ],
    template: `
        <fieldset style="padding:5px;">
            <legend><b>Taxonomic Data Source</b></legend>
            <q-option-group :options="dataSourceOptions" type="radio" v-model="selectedDataSource" :disable="disable" @update:model-value="processChange" dense />
        </fieldset>
    `,
    data() {
        return {
            dataSourceOptions: [
                { label: 'Catalogue of Life (COL)', value: 'col' },
                { label: 'Integrated Taxonomic Information System (ITIS)', value: 'itis' },
                { label: 'World Register of Marine Species (WoRMS)', value: 'worms' }
            ]
        };
    },
    methods: {
        processChange(datasourceobj) {
            this.$emit('update:selected-data-source', datasourceobj);
        }
    }
};
