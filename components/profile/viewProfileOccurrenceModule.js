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
                        <collection-cotrol-panel-menus :user-name="accountInfo.username" :collection-id="collection.collid" :coll-type="collection.colltype" :occ-count="collection.occCount" :coll-access-level="collection.accesslevel"></collection-cotrol-panel-menus>
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
    data() {
        return {
            clientRoot: Vue.ref(CLIENT_ROOT),
            collectionArr: Vue.ref([])
        }
    },
    components: {
        'collection-cotrol-panel-menus': collectionControlPanelMenus
    },
    mounted() {
        this.setAccountCollections();
    },
    methods: {
        setAccountCollections(){
            const formData = new FormData();
            formData.append('uid', this.uid);
            formData.append('action', 'getAccountCollections');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    this.collectionArr = resObj;
                });
            });
        }
    }
};