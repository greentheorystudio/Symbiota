const configuredDataFieldRowGroup = {
    props: {
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
        <q-card flat bordered class="black-border">
            <q-card-section v-if="label">
                <div class="text-bold text-body1">{{ label }}</div>
            </q-card-section>
            <q-card-section v-if="rows.length > 0" class="q-px-sm q-pb-sm column q-col-gutter-sm">
                <template v-for="row in rows">
                    <configured-data-field-row :fields="row.fields"></configured-data-field-row>
                </template>
            </q-card-section>
        </q-card>
    `,
    components: {
        'configured-data-field-row': configuredDataFieldRow
    }
};
