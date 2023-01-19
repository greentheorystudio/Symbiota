let dataSource = Vue.ref('col');

const taxonomyDataSourceBulletSelector = {
    props: [
        'disable'
    ],
    template: `
        <fieldset style="padding:5px;">
            <legend><b>Taxonomic Data Source</b></legend>
            <q-option-group :options="dataSourceOptions" type="radio" v-model="taxresource" :disable="disable" dense />
        </fieldset>
    `,
    data() {
        return {
            taxresource: dataSource,
            dataSourceOptions: [
                { label: 'Catalogue of Life (COL)', value: 'col' },
                { label: 'Integrated Taxonomic Information System (ITIS)', value: 'itis' },
                { label: 'World Register of Marine Species (WoRMS)', value: 'worms' }
            ]
        };
    }
};
