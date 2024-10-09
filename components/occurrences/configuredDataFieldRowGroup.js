const configuredDataFieldRowGroup = {
    props: {
        expansion: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: null
        },
        rows: {
            type: Array,
            default: []
        }
    },
    template: `
        <template v-if="expansion">
            <q-expansion-item class="shadow-1 overflow-hidden expansion-element" :label="label" header-class="bg-grey-3 text-grey-8 text-h6 text-weight-bolder q-pl-md" expand-icon-class="text-bold">
                <div class="q-pa-sm column q-col-gutter-sm">
                    <template v-for="row in rows">
                        <configured-data-field-row :fields="row.fields"></configured-data-field-row>
                    </template>
                </div>
            </q-expansion-item>
        </template>
        <template v-else>
            <q-card flat bordered>
                <q-card-section v-if="rows.length > 0" class="q-pa-sm column q-col-gutter-sm">
                    <div v-if="label" class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                        {{ label }}
                    </div>
                    <template v-for="row in rows">
                        <configured-data-field-row :fields="row.fields"></configured-data-field-row>
                    </template>
                </q-card-section>
            </q-card>
        </template>
    `,
    components: {
        'configured-data-field-row': configuredDataFieldRow
    }
};
