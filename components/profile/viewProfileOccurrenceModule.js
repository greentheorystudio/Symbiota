const viewProfileOccurrenceModule = {
    props: {
        accountInfo: {
            type: Object,
            default: null
        }
    },
    template: `
        <template v-if="collectionArr.length > 0">
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
        const collectionStore = useCollectionStore();

        const clientRoot = baseStore.getClientRoot;
        const collectionArr = Vue.ref([]);

        function setAccountCollections() {
            collectionStore.getCollectionListByUserRights((collListData) => {
                collectionArr.value = collListData;
            });
        }

        Vue.onMounted(() => {
            setAccountCollections();
        });

        return {
            clientRoot,
            collectionArr
        }
    }
};
