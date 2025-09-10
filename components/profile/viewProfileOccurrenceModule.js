const viewProfileOccurrenceModule = {
    template: `
        <template v-if="collectionArr.length > 0">
            <template v-if="collectionArr.length > 1">
                <q-list bordered class="rounded-borders q-mt-md">
                    <template v-for="collection in collectionArr">
                        <q-expansion-item expand-separator group="collectionGroup" :label="collection.label" header-class="text-h6">
                            <collection-cotrol-panel-menus :user-name="accountInfo.username" :collection-id="collection.collid" :collection-type="collection.colltype" :collection-permissions="collection.collectionpermissions"></collection-cotrol-panel-menus>
                        </q-expansion-item>
                    </template>
                </q-list>
            </template>
            <template v-else>
                <q-card class="q-mt-md">
                    <q-card-section>
                        <collection-cotrol-panel-menus :user-name="accountInfo.username" :collection-id="collectionArr[0].collid" :collection-type="collectionArr[0].colltype" :collection-permissions="collectionArr[0].collectionpermissions"></collection-cotrol-panel-menus>
                    </q-card-section>
                </q-card>
            </template>
        </template>
        <template v-else>
            <q-card class="q-mt-md">
                <q-card-section class="text-h6">
                    You do not have permissions for any occurrence collections
                </q-card-section>
            </q-card>
        </template>
        <q-card class="q-mt-md">
            <q-card-section class="text-h6">
                <a :href="(clientRoot + '/collections/datasets/index.php')">
                    Dataset Management
                </a>
            </q-card-section>
        </q-card>
    `,
    components: {
        'collection-cotrol-panel-menus': collectionControlPanelMenus
    },
    setup() {
        const baseStore = useBaseStore();
        const userStore = useUserStore();

        const accountInfo = Vue.computed(() => userStore.getUserData);
        const clientRoot = baseStore.getClientRoot;
        const collectionArr = Vue.computed(() => userStore.getCollectionArr);

        return {
            accountInfo,
            clientRoot,
            collectionArr
        }
    }
};
