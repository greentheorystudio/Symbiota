const keyCharacterEditorDependenceTab = {
    template: `
        <div class="q-pa-md column q-gutter-sm">
            <div class="q-mb-sm">
                <q-card>
                    <q-card-section class="column q-gutter-sm">
                        <div class="text-subtitle1 text-bold">Add new dependency</div>
                        <div class="row q-col-gutter-sm no-wrap">
                            <div class="col-4">
                                <key-character-auto-complete label="Character" :value="selectedCharacterName" @update:value="processCharacterSelection"></key-character-auto-complete>
                            </div>
                            <div class="col-4">
                                <selector-input-element :options="characterStateOptions" label="Character State" :value="selectedCharacterStateName" option-label="characterstatename" option-value="csid" :clearable="true" :disabled="characterStateOptions.length === 0" @update:value="(value) => selectedCharacterState = value"></selector-input-element>
                            </div>
                            <div class="col-4 row justify-end">
                                <div>
                                    <q-btn color="primary" @click="addDependency();" label="Add" :disabled="!selectedCharacterName" aria-label="Associate" tabindex="0" />
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
            <template v-if="dependenceArr.length > 0">
                <template v-for="dependence in dependenceArr">
                    <div>
                        <q-card flat bordered>
                            <q-card-section class="column q-gutter-sm">
                                <div class="row q-col-gutter-sm no-wrap">
                                    <div class="col-4">
                                        <key-character-auto-complete label="Character" :value="dependence['charactername']" :disabled="true"></key-character-auto-complete>
                                    </div>
                                    <div class="col-4">
                                        <text-field-input-element label="Character State" :value="dependence['characterstatename']" :disabled="true"></text-field-input-element>
                                    </div>
                                    <div class="col-4 row justify-end">
                                        <div class="self-center">
                                            <q-btn color="negative" size="sm" @click="deleteDependency(dependence['cdid']);" icon="far fa-trash-alt" dense aria-label="Delete dependence" :tabindex="tabindex">
                                                <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                    Delete dependence
                                                </q-tooltip>
                                            </q-btn>
                                        </div>
                                    </div>
                                </div>
                            </q-card-section>
                        </q-card>
                    </div>
                </template>
            </template>
            <template v-else>
                <q-separator size="1px" color="grey-8" class="q-ma-md"></q-separator>
                <div class="q-pa-md row justify-center text-subtitle1 text-bold">
                    There are currently no dependencies to display
                </div>
            </template>
        </div>
    `,
    components: {
        'key-character-auto-complete': keyCharacterAutoComplete,
        'selector-input-element': selectorInputElement,
        'text-field-input-element': textFieldInputElement
    },
    setup() {
        const { showNotification } = useCore();
        const keyCharacterStore = useKeyCharacterStore();

        const characterId = Vue.computed(() => keyCharacterStore.getKeyCharacterID);
        const characterStateOptions = Vue.ref([]);
        const dependenceArr = Vue.computed(() => keyCharacterStore.getKeyCharacterDependenceArr);
        const selectedCharacterId = Vue.ref(null);
        const selectedCharacterName = Vue.ref(null);
        const selectedCharacterState = Vue.ref(null);
        const selectedCharacterStateName = Vue.computed(() => {
            let returnStr = null;
            if(selectedCharacterState.value){
                const selectedObj = characterStateOptions.value.find(option => Number(option['csid']) === Number(selectedCharacterState.value));
                if(selectedObj){
                    returnStr = selectedObj['characterstatename'];
                }
            }
            return returnStr;
        });

        function addDependency() {
            keyCharacterStore.addCharacterDependencyRecord(selectedCharacterId.value, selectedCharacterState.value, (newDepId) => {
                if(newDepId > 0){
                    selectedCharacterId.value = null;
                    selectedCharacterName.value = null;
                    selectedCharacterState.value = null;
                    showNotification('positive','Dependence added successfully.');
                }
                else{
                    showNotification('negative', 'There was an error adding the new dependence.');
                }
            });
        }

        function deleteDependency(depid) {
            keyCharacterStore.deleteKeyCharacterDependencyRecord(depid, (res) => {
                if(res === 1){
                    showNotification('positive','Dependence has been deleted.');
                }
                else{
                    showNotification('negative', 'There was an error deleting the dependence.');
                }
            });
        }

        function processCharacterSelection(charObj) {
            characterStateOptions.value.length = 0;
            if(charObj){
                selectedCharacterId.value = charObj['cid'];
                selectedCharacterName.value = charObj['charactername'];
                setCharacterStateOptions();
            }
            else{
                selectedCharacterId.value = null;
                selectedCharacterName.value = null;
            }
        }

        function setCharacterStateOptions() {
            const formData = new FormData();
            formData.append('cid', selectedCharacterId.value.toString());
            formData.append('action', 'getKeyCharacterStatesArrFromCid');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                characterStateOptions.value = data;
            });
        }

        return {
            characterId,
            characterStateOptions,
            dependenceArr,
            selectedCharacterId,
            selectedCharacterName,
            selectedCharacterState,
            selectedCharacterStateName,
            addDependency,
            deleteDependency,
            processCharacterSelection
        }
    }
};
