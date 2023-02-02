const taxonomyDataSourceBulletSelector = {
    props: [
        'selected-data-source',
        'disable'
    ],
    template: `
        <q-card flat bordered>
            <q-card-section>
                <div class="text-subtitle1 text-weight-bold">Select Taxonomic Data Source</div>
                <q-option-group :options="dataSourceOptions" type="radio" v-model="selectedDataSource" :disable="disable" @update:model-value="processChange" dense />
            </q-card-section>
        </q-card>
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
