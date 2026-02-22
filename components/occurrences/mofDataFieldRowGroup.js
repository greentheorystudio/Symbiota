const mofDataFieldRowGroup = {
    props: {
        configuredData: {
            type: Object,
            default: {}
        },
        configuredDataFields: {
            type: Object,
            default: {}
        },
        editor: {
            type: Boolean,
            default: true
        },
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
            <div>
                <q-expansion-item class="shadow-1 overflow-hidden expansion-element" :label="label" header-class="bg-grey-3 text-grey-8 text-h6 text-weight-bolder q-pl-md" expand-icon-class="text-bold">
                    <div class="q-pa-sm column q-col-gutter-sm">
                        <template v-for="row in rows">
                            <mof-data-field-row :editor="editor" :configured-data="configuredData" :configured-data-fields="configuredDataFields" :fields="row.fields" @update:configured-edit-data="updateConfiguredEditData"></mof-data-field-row>
                        </template>
                    </div>
                </q-expansion-item>
            </div>
        </template>
        <template v-else>
            <div>
                <q-card flat bordered>
                    <q-card-section v-if="rows.length > 0" class="q-pa-sm column q-col-gutter-sm">
                        <div v-if="label" class="text-grey-8 text-h6 text-weight-bolder q-pl-md">
                            {{ label }}
                        </div>
                        <template v-for="row in rows">
                            <mof-data-field-row :editor="editor" :configured-data="configuredData" :configured-data-fields="configuredDataFields" :fields="row.fields" @update:configured-edit-data="updateConfiguredEditData"></mof-data-field-row>
                        </template>
                    </q-card-section>
                </q-card>
            </div>
        </template>
    `,
    components: {
        'mof-data-field-row': mofDataFieldRow
    },
    setup(_, context) {
        function updateConfiguredEditData(data) {
            context.emit('update:configured-edit-data', data);
        }

        return {
            updateConfiguredEditData
        }
    }
};
