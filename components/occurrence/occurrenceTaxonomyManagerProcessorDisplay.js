let currentSciname = Vue.ref(null);
let undoButtonsDisabled = Vue.ref(true);
let processorDisplayArr = Vue.ref([]);

const occurrenceTaxonomyManagerProcessorDisplay = {
    template: `
        <q-card class="bg-grey-3 q-pa-sm">
            <q-scroll-area ref="procDisplayScrollAreaRef" class="bg-grey-1 processor-display" @scroll="setScroller">
                <q-list dense>
                    <q-item v-for="proc in procDispArr">
                        <q-item-section>
                            <div>{{ proc.procText }} <q-spinner v-if="proc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                            <template v-if="!proc.loading && proc.resultText">
                                <div v-if="proc.result === 'success'" class="q-ml-sm text-weight-bold text-green-9">
                                    {{proc.resultText}}
                                </div>
                                <div v-if="proc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                    {{proc.resultText}}
                                </div>
                            </template>
                            <template v-if="proc.type === 'multi' && proc.subs.length">
                                <div class="q-ml-sm">
                                    <div v-for="subproc in proc.subs">
                                        <template v-if="subproc.type === 'text' || subproc.type === 'undo'">
                                            <div>{{ subproc.procText }} <q-spinner v-if="subproc.loading" class="q-ml-sm" color="green" size="1.2em" :thickness="10"></q-spinner></div>
                                            <template v-if="!subproc.loading && subproc.resultText">
                                                <div v-if="subproc.result === 'success' && subproc.type === 'text'" class="q-ml-sm text-weight-bold text-green-9">
                                                    {{subproc.resultText}}
                                                </div>
                                                <div v-if="subproc.result === 'success' && subproc.type === 'undo'" class="q-ml-sm text-weight-bold text-green-9">
                                                    {{subproc.resultText}} <q-btn :disabled="undoButtonsDisabled" class="q-ml-md text-grey-9" color="warning" size="sm" @click="undoChangedSciname(proc.id,subproc.undoOrigName,subproc.undoChangedName);" label="Undo" dense />
                                                </div>
                                                <div v-if="subproc.result === 'error'" class="q-ml-sm text-weight-bold text-negative">
                                                    {{subproc.resultText}}
                                                </div>
                                            </template>
                                        </template>
                                        <template v-if="subproc.type === 'fuzzy'">
                                            <template v-if="subproc.procText === 'skip'">
                                                <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                    <div></div>
                                                    <div>
                                                        <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="runTaxThesaurusFuzzyMatchProcess();" label="Skip Taxon" dense />
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <div class="q-mx-xl q-my-sm fuzzy-match-row">
                                                    <div class="text-weight-bold">
                                                        {{ subproc.procText }}
                                                    </div>
                                                    <div>
                                                        <q-btn :disabled="!(currentSciname === proc.id)" class="q-ml-md" color="primary" size="sm" @click="selectFuzzyMatch(subproc.undoOrigName,subproc.undoChangedName,subproc.changedTid);" label="Select" dense />
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </q-item-section>
                    </q-item>
                </q-list>
            </q-scroll-area>
        </q-card>
    `,
    setup() {
        let procDisplayScrollAreaRef = Vue.ref(null);
        let procDisplayScrollHeight = Vue.ref(0);
        return {
            procDisplayScrollAreaRef,
            setScroller(info) {
                if(info.hasOwnProperty('verticalSize') && info.verticalSize > 610 && info.verticalSize !== procDisplayScrollHeight.value){
                    procDisplayScrollHeight.value = info.verticalSize;
                    procDisplayScrollAreaRef.value.setScrollPosition('vertical', info.verticalSize);
                }
            }
        }
    },
    data() {
        return {
            currentSciname: currentSciname,
            undoButtonsDisabled: undoButtonsDisabled,
            procDispArr: processorDisplayArr
        };
    },
    methods: {
        undoChangedSciname,
        runTaxThesaurusFuzzyMatchProcess,
        selectFuzzyMatch
    }
};
