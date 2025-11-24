const keyCharacterEditorCharacterStatesTab = {
    template: `
        <div class="column q-gutter-xs">
            <div class="row justify-end">
                <div>
                    <q-btn color="primary" @click="openKeyCharacterStateEditorPopup(0);" label="Add Character State" dense tabindex="0"></q-btn>
                </div>
            </div>
            <div class="q-ml-sm">
                <template v-if="characterStateArr.length > 0">
                    <template v-for="state in characterStateArr">
                        <div class="row justify-start q-gutter-sm">
                            <div class="text-body1">{{ state['characterstatename'] }}</div>
                            <div>
                                <q-btn color="grey-4" text-color="black" class="black-border" size="xs" @click="openKeyCharacterStateEditorPopup(state['csid']);" icon="fas fa-edit" dense aria-label="Edit character state record" tabindex="0">
                                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                        Edit character state record
                                    </q-tooltip>
                                </q-btn>
                            </div>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div>There are currently no character states to display</div>
                </template>
            </div>
        </div>
    `,
    setup(_, context) {
        const keyCharacterStateStore = useKeyCharacterStateStore();

        const characterStateArr = Vue.computed(() => keyCharacterStateStore.getKeyCharacterStateArr);

        function openKeyCharacterStateEditorPopup(stateid) {
            context.emit('open:character-state-popup', stateid);
        }

        return {
            characterStateArr,
            openKeyCharacterStateEditorPopup
        }
    }
};
