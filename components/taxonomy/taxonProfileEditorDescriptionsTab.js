const taxonProfileEditorDescriptionsTab = {
    template: `
        <div class="column q-gutter-sm">
            <div class="full-width row justify-end items-center">
                <div>
                    <q-btn color="primary" @click="openBlockEditorPopup(0);" label="Add Description Block" tabindex="0" />
                </div>
            </div>
            <template v-if="descriptionArr.length > 0">
                <template v-for="block in descriptionArr">
                    <q-card>
                        <q-card-section class="column q-gutter-sm">
                            <div class="row justify-between">
                                <div class="column">
                                    <div class="text-subtitle1 text-bold">{{ block['caption'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Source: </span>{{ block['source'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Source URL: </span>{{ block['sourceurl'] }}</div>
                                    <div><span class="text-subtitle1 text-bold">Notes: </span>{{ block['notes'] }}</div>
                                </div>
                                <div class="column q-gutter-sm">
                                    <div class="row justify-end">
                                        <q-btn color="grey-4" text-color="black" class="black-border" size="sm" @click="openBlockEditorPopup(block['tdbid']);" icon="fas fa-edit" dense aria-label="Edit description block record" tabindex="0">
                                            <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                Edit description block record
                                            </q-tooltip>
                                        </q-btn>
                                    </div>
                                    <div>
                                        <q-btn color="secondary" @click="openStatementEditorPopup(block['tdbid'], 0);" label="Add Description Statement" tabindex="0" />
                                    </div>
                                </div>
                            </div>
                            <template v-if="block['stmts'].length > 0">
                                <template v-for="statement in block['stmts']">
                                    <q-card flat bordered>
                                        <q-card-section class="column">
                                            <div class="row justify-end">
                                                <div>
                                                    <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openStatementEditorPopup(block['tdbid'], statement['tdsid']);" icon="fas fa-edit" dense aria-label="Edit description block record" tabindex="0">
                                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                                            Edit description statement record
                                                        </q-tooltip>
                                                    </q-btn>
                                                </div>
                                            </div>
                                            <div v-html="statement['statement']"></div>
                                        </q-card-section>
                                    </q-card>
                                </template>
                            </template>
                            <template v-else>
                                <div class="q-mt-sm text-body1 text-bold">There are no statements for this description block yet, click the Add Description Statement button above to add the first one</div>
                            </template>
                        </q-card-section>
                    </q-card>
                </template>
            </template>
            <template v-else>
                <div class="q-mt-sm text-body1 text-bold">There are no description blocks for this taxon yet, click the Add Description Block button above to add the first one</div>
            </template>
        </div>
        <template v-if="showBlockEditorPopup">
            <taxon-profile-editor-description-block-editor-popup
                :block-id="editBlockId"
                :show-popup="showBlockEditorPopup" 
                @close:popup="showBlockEditorPopup = false"
            ></taxon-profile-editor-description-block-editor-popup>
        </template>
        <template v-if="showStatementEditorPopup">
            <taxon-profile-editor-description-statement-editor-popup
                :block-id="editBlockId"
                :statement-id="editStatementId"
                :show-popup="showStatementEditorPopup"
                @close:popup="showStatementEditorPopup = false"
            ></taxon-profile-editor-description-statement-editor-popup>
        </template>
    `,
    components: {
        'taxon-profile-editor-description-block-editor-popup': taxonProfileEditorDescriptionBlockEditorPopup,
        'taxon-profile-editor-description-statement-editor-popup': taxonProfileEditorDescriptionStatementEditorPopup
    },
    setup() {
        const taxaStore = useTaxaStore();

        const descriptionArr = Vue.computed(() => {
            const displayArr = [];
            if(descriptionBlockArr.value.length > 0){
                descriptionBlockArr.value.forEach((desc) => {
                    const description = Object.assign({}, desc);
                    description['stmts'] = [];
                    descriptionStatementData.value[desc['tdbid']].forEach((stmt) => {
                        if(stmt['statement'] && stmt['statement'] !== ''){
                            const statement = Object.assign({}, stmt);
                            if(statement['statement'].startsWith('<p>')){
                                statement['statement'] = statement['statement'].slice(3);
                            }
                            if(statement['statement'].endsWith('</p>')){
                                statement['statement'] = statement['statement'].substring(0, statement['statement'].length - 4);
                            }
                            if(Number(statement['displayheader']) === 1 && statement['heading'] && statement['heading'] !== ''){
                                const headingText = '<span class="desc-statement-heading">' + statement['heading'] + '</span>: ';
                                statement['statement'] = headingText + statement['statement'];
                            }
                            description['stmts'].push(statement);
                        }
                    });
                    displayArr.push(description);
                });
            }
            return displayArr;
        });
        const descriptionBlockArr = Vue.computed(() => taxaStore.getTaxaDescriptionBlockArr);
        const descriptionStatementData = Vue.computed(() => taxaStore.getTaxaDescriptionStatementArr);
        const editBlockId = Vue.ref(0);
        const editStatementId = Vue.ref(0);
        const showBlockEditorPopup = Vue.ref(false);
        const showStatementEditorPopup = Vue.ref(false);

        function openBlockEditorPopup(blockid) {
            editBlockId.value = blockid;
            showBlockEditorPopup.value = true;
        }

        function openStatementEditorPopup(blockid, statementid) {
            editBlockId.value = blockid;
            editStatementId.value = statementid;
            showStatementEditorPopup.value = true;
        }

        return {
            descriptionArr,
            editBlockId,
            editStatementId,
            showBlockEditorPopup,
            showStatementEditorPopup,
            openBlockEditorPopup,
            openStatementEditorPopup
        }
    }
};
