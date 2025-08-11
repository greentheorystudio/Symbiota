const collectionCheckboxSelector = {
    props: {
        valueArr: {
            type: Array,
            default: []
        }
    },
    template: `
        <div class="column q-gutter-sm">
            <div class="q-pl-lg row justify-start q-gutter-md">
                <div class="text-body1">
                    <q-checkbox v-model="selectAllValue" label="Select all" @update:model-value="processSelectAllChange" dense></q-checkbox>
                </div>
                <div v-if="selectAllValue === false" class="text-body1 text-bold text-red self-center">
                    *Note that if all collections remain deselected the result will default to include all collections
                </div>
            </div>
            <template v-for="cat in checkboxSelectorData['categories']">
                <template v-if="cat.collections.length > 0">
                    <q-list bordered class="rounded-borders">
                        <q-expansion-item v-model="cat.expanded" dense>
                            <template v-slot:header>
                                <q-item-section avatar>
                                    <q-checkbox v-model="cat['selectAllVal']" @update:model-value="(value) => processCatSelectAllChange(cat.ccpk, value)" dense></q-checkbox>
                                </q-item-section>
                                <q-item-section class="text-h6 text-bold">
                                    {{ cat.name }}
                                </q-item-section>
                            </template>
                            <q-card>
                                <q-card-section class="q-pl-xl q-pt-none q-pb-none q-pr-md">
                                    <q-list dense>
                                        <template v-for="col in cat.collections">
                                            <q-item>
                                                <q-item-section avatar>
                                                    <template v-if="col.icon">
                                                        <q-img :src="col.icon" class="coll-icon-collection-checklist" :fit="contain"></q-img>
                                                    </template>
                                                </q-item-section>
                                                <q-item-section avatar>
                                                    <q-checkbox v-model="col['checkboxVal']" @update:model-value="(value) => processColCheckboxChange(col.collid, value)" dense></q-checkbox>
                                                </q-item-section>
                                                <q-item-section class="text-body1">
                                                    <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + col.collid)" target="_blank">
                                                        {{ col.name + (col.code ? ' (' + col.code + ')' : '') }}
                                                    </a>
                                                </q-item-section>
                                            </q-item>
                                        </template>
                                    </q-list>
                                </q-card-section>
                            </q-card>
                        </q-expansion-item>
                    </q-list>
                </template>
            </template>
            <template v-if="checkboxSelectorData['collections'].length > 0">
                <q-list class="full-width" dense>
                    <template v-for="col in checkboxSelectorData['collections']">
                        <q-item>
                            <q-item-section avatar>
                                <template v-if="col.icon">
                                    <img :src="col.icon" class="coll-icon-collection-checklist">
                                </template>
                            </q-item-section>
                            <q-item-section avatar>
                                <q-checkbox v-model="col['checkboxVal']" @update:model-value="(value) => processColCheckboxChange(col.collid, value)" dense></q-checkbox>
                            </q-item-section>
                            <q-item-section>
                                <a :href="(clientRoot + '/collections/misc/collprofiles.php?collid=' + col.collid)" target="_blank">
                                    {{ col.name + (col.code ? ' (' + col.code + ')' : '') }}
                                </a>
                            </q-item-section>
                        </q-item>
                    </template>
                </q-list>
            </template>
        </div>
    `,
    setup(props, context) {
        const { showNotification } = useCore();
        const baseStore = useBaseStore();

        const checkboxSelectorData = Vue.reactive({
            categories: [],
            collections: []
        });
        const clientRoot = baseStore.getClientRoot;
        const collectionArr = Vue.ref([]);
        const collectionIdArr = Vue.ref([]);
        const collectionCategoryArr = Vue.ref([]);
        const defaultCategoryId = baseStore.getDefaultCollectionCategoryId;
        const propsRefs = Vue.toRefs(props);
        const selectAllValue = Vue.computed(() => {
            if(selectedCollectionIdArr.value.length === collectionIdArr.value.length && collectionIdArr.value.every((id) => selectedCollectionIdArr.value.includes(id))){
                return true;
            }
            else if(selectedCollectionIdArr.value.length === 0){
                return false;
            }
            else{
                return null;
            }
        });
        const selectedCollectionIdArr = Vue.ref([]);
        const valueChangeEvent = Vue.ref(false);

        Vue.watch(propsRefs.valueArr, () => {
            if(!valueChangeEvent.value){
                processValueArrChange();
            }
        });

        function prepareCollectionCategoryData() {
            collectionCategoryArr.value.forEach((cat) => {
                const catObj = {
                    ccpk: cat.ccpk,
                    name: cat.category,
                    expanded: (!defaultCategoryId.value || Number(defaultCategoryId.value) === Number(cat.ccpk)),
                    collections: [],
                    collectionIds: [],
                    selectAllVal: Vue.computed(() => {
                        const selectedArr = [];
                        const category = checkboxSelectorData['categories'].find(c => Number(c.ccpk) === Number(cat.ccpk));
                        category['collectionIds'].forEach((id) => {
                            if(selectedCollectionIdArr.value.includes(id)){
                                selectedArr.push(id)
                            }
                        });
                        if(selectedArr.length === category['collectionIds'].length){
                            return true;
                        }
                        else if(selectedArr.length === 0){
                            return false;
                        }
                        else{
                            return null;
                        }
                    })
                };
                checkboxSelectorData['categories'].push(catObj);
            });
        }

        function prepareCollectionData() {
            collectionArr.value.forEach((col) => {
                let colCodeStr = '';
                if(col['institutioncode']){
                    colCodeStr += col['institutioncode'];
                }
                if(col['institutioncode'] && col['collectioncode']){
                    colCodeStr += ':';
                }
                if(col['collectioncode']){
                    colCodeStr += col['collectioncode'];
                }
                const colObj = {
                    collid: col.collid,
                    name: col['collectionname'],
                    code: colCodeStr,
                    icon: col['icon'],
                    checkboxVal: Vue.computed(() => {
                        return selectedCollectionIdArr.value.includes(col.collid);
                    })
                };
                if(Number(col.ccpk) > 0){
                    const category = checkboxSelectorData['categories'].find(cat => Number(cat.ccpk) === Number(col.ccpk));
                    category['collections'].push(colObj);
                    category['collectionIds'].push(col.collid);
                }
                else{
                    checkboxSelectorData['collections'].push(colObj);
                }
                collectionIdArr.value.push(col.collid);
                if(props.valueArr.length === 0 || props.valueArr.includes(col.collid)){
                    selectedCollectionIdArr.value.push(col.collid);
                }
            });
        }

        function processCatSelectAllChange(ccpk, val) {
            const category = checkboxSelectorData['categories'].find(cat => Number(cat.ccpk) === Number(ccpk));
            if(val || selectedCollectionIdArr.value.length > category['collectionIds'].length){
                category['collectionIds'].forEach((id) => {
                    if(!val && selectedCollectionIdArr.value.includes(id)){
                        const index = selectedCollectionIdArr.value.indexOf(id);
                        selectedCollectionIdArr.value.splice(index, 1);
                    }
                    else if(val && !selectedCollectionIdArr.value.includes(id)){
                        selectedCollectionIdArr.value.push(id);
                    }
                });
                processSelectionChange();
            }
            else{
                showNotification('negative', 'At least one collection must be selected');
            }
        }

        function processColCheckboxChange(collid, val) {
            if(val || selectedCollectionIdArr.value.length > 1){
                if(val){
                    selectedCollectionIdArr.value.push(collid);
                }
                else{
                    const index = selectedCollectionIdArr.value.indexOf(collid);
                    selectedCollectionIdArr.value.splice(index, 1);
                }
                processSelectionChange();
            }
            else{
                showNotification('negative', 'At least one collection must be selected');
            }
        }

        function processSelectAllChange(val) {
            valueChangeEvent.value = true;
            if(!val){
                selectedCollectionIdArr.value.length = 0;
            }
            else{
                selectedCollectionIdArr.value = collectionIdArr.value.slice();
            }
            context.emit('update:value', []);
            setTimeout(() => {
                valueChangeEvent.value = false;
            }, 200 );
        }

        function processSelectionChange() {
            valueChangeEvent.value = true;
            if(selectedCollectionIdArr.value.length === 0 || collectionIdArr.value.every((id) => selectedCollectionIdArr.value.includes(id))){
                context.emit('update:value', []);
            }
            else{
                context.emit('update:value', selectedCollectionIdArr.value);
            }
            setTimeout(() => {
                valueChangeEvent.value = false;
            }, 200 );
        }

        function processValueArrChange() {
            if(props.valueArr.length === 0){
                selectedCollectionIdArr.value = collectionIdArr.value.slice();
            }
            else{
                selectedCollectionIdArr.value = props.valueArr.slice();
            }
        }

        function setCollectionCategories() {
            const formData = new FormData();
            formData.append('action', 'getCollectionCategoryArr');
            fetch(collectionCategoryApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((result) => {
                collectionCategoryArr.value = result;
                prepareCollectionCategoryData();
                setCollections();
            });
        }

        function setCollections() {
            const formData = new FormData();
            formData.append('action', 'getCollectionArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => response.json())
            .then((result) => {
                collectionArr.value = result;
                prepareCollectionData();
            });
        }

        Vue.onMounted(() => {
            setCollectionCategories();
        });
        
        return {
            checkboxSelectorData,
            clientRoot,
            selectAllValue,
            processCatSelectAllChange,
            processColCheckboxChange,
            processSelectAllChange
        }
    }
};
