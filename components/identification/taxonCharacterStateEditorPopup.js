const taxonCharacterStateEditorPopup = {
    props: {
        characterId: {
            type: Number,
            default: 0
        },
        characterName: {
            type: String,
            default: null
        },
        showPopup: {
            type: Boolean,
            default: false
        },
        taxonId: {
            type: Number,
            default: 0
        },
        taxonRankid: {
            type: Number,
            default: 0
        }
    },
    template: `
        <q-dialog class="z-max" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div class="q-pa-lg column q-gutter-md">
                            <div v-if="inheritanceStr" class="text-h6">
                                Inheriting <span class="text-bold">{{ inheritanceStr }}</span> from <span class="text-bold">{{ inheritanceTaxon }}</span>
                            </div>
                            <div class="q-px-lg q-pb-lg column q-gutter-xs">
                                <div class="text-h5 text-bold">
                                    {{ characterName }}
                                </div>
                                <template v-for="state in characterStateArr">
                                    <div class="q-ml-xl">
                                        <checkbox-input-element :label="state.characterstatename" :value="selectedCsidArr.includes(Number(state.csid)) ? '1' : '0'" @update:value="(value) => processCharacterStateSelectionChange(state, value)"></checkbox-input-element>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    components: {
        'checkbox-input-element': checkboxInputElement
    },
    setup(props, context) {
        const characterStateArr = Vue.ref([]);
        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const inheritanceStr = Vue.computed(() => {
            const returnStrArr = [];
            let currentRank = 0;
            const tempArr = taxonCharacterStateDataArr.value.slice();
            tempArr.sort((a, b) => a.sortsequence - b.sortsequence);
            tempArr.forEach((taxonCS) => {
                if(Number(taxonCS['rankid']) < Number(props.taxonRankid)){
                    if(Number(taxonCS['rankid']) > currentRank){
                        currentRank = Number(taxonCS['rankid']);
                        inheritanceTaxon.value = taxonCS['parentname'];
                        returnStrArr.length = 0;
                    }
                    returnStrArr.push(taxonCS['characterstatename']);
                }
            });
            return returnStrArr.length > 0 ? returnStrArr.join(', ') : null;
        });
        const inheritanceTaxon = Vue.ref(null);
        const selectedCsidArr = Vue.computed(() => {
            const returnArr = [];
            taxonCharacterStateDataArr.value.forEach((taxonCS) => {
                if(Number(taxonCS['rankid']) === Number(props.taxonRankid)){
                    returnArr.push(Number(taxonCS['csid']));
                }
            });
            return returnArr;
        });
        const taxonCharacterStateDataArr = Vue.ref([]);

        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processAddCharacterState(state) {
            const formData = new FormData();
            formData.append('cid', props.characterId.toString());
            formData.append('csid', state['csid'].toString());
            formData.append('tid', props.taxonId.toString());
            formData.append('action', 'addTaxonCharacterStateLinkage');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    context.emit('change:character-state');
                    taxonCharacterStateDataArr.value.push({
                        parenttid: 0,
                        parentname: null,
                        rankid: props.taxonRankid,
                        csid: state['csid'],
                        characterstatename: state['characterstatename']
                    });
                }
            });
        }

        function processCharacterStateSelectionChange(state, value) {
            if(Number(value) === 1){
                processAddCharacterState(state);
            }
            else{
                processRemoveCharacterState(state);
            }
        }

        function processRemoveCharacterState(state) {
            const formData = new FormData();
            formData.append('cid', props.characterId.toString());
            formData.append('csid', state['csid'].toString());
            formData.append('tid', props.taxonId.toString());
            formData.append('action', 'removeTaxonCharacterStateLinkage');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    context.emit('change:character-state');
                    const taxonCSObj = taxonCharacterStateDataArr.value.find(tcs => (Number(tcs.rankid) === Number(props.taxonRankid) && Number(tcs.csid) === Number(state['csid'])));
                    const index = taxonCharacterStateDataArr.value.indexOf(taxonCSObj);
                    taxonCharacterStateDataArr.value.splice(index, 1);
                }
            });
        }

        function setCharacterStateArr() {
            const formData = new FormData();
            formData.append('cid', props.characterId.toString());
            formData.append('action', 'getKeyCharacterStatesArrFromCid');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                characterStateArr.value = resData.slice();
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setTaxonCharacterStateDataArr() {
            const formData = new FormData();
            formData.append('cid', props.characterId.toString());
            formData.append('tid', props.taxonId.toString());
            formData.append('action', 'getCharacterCharacterStateDataFromTid');
            fetch(keyCharacterStateApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                taxonCharacterStateDataArr.value = resData.slice();
            });
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
            setCharacterStateArr();
            setTaxonCharacterStateDataArr();
        });

        return {
            characterStateArr,
            contentRef,
            contentStyle,
            inheritanceStr,
            inheritanceTaxon,
            selectedCsidArr,
            closePopup,
            processCharacterStateSelectionChange
        }
    }
};
