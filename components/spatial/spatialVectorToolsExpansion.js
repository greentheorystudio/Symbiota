const spatialVectorToolsExpansion = {
    template: `
        <div>
            <q-separator ></q-separator>
            <q-tabs v-model="tab" active-bg-color="grey-4" align="left">
                <q-tab name="vector" class="bg-grey-3" label="Shapes" no-caps />
                <q-tab name="point" class="bg-grey-3" label="Points" no-caps />
            </q-tabs>
            <q-separator></q-separator>
            <q-tab-panels v-model="tab">
                <q-tab-panel name="vector">
                    <spatial-vector-tools-tab></spatial-vector-tools-tab>
                </q-tab-panel>
                <q-tab-panel name="point">
                    <spatial-point-vector-tools-tab></spatial-point-vector-tools-tab>
                </q-tab-panel>
            </q-tab-panels>
        </div>
    `,
    components: {
        'spatial-point-vector-tools-tab': spatialPointVectorToolsTab,
        'spatial-vector-tools-tab': spatialVectorToolsTab
    },
    setup() {
        const tab = Vue.ref('vector');

        return {
            tab
        }
    }
};
