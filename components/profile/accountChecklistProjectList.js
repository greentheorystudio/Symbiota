const accountChecklistProjectList = {
    template: `
        <template v-if="checklistArr.length > 0">
            <q-list bordered class="rounded-borders q-mt-md">
                <q-expansion-item expand-separator label="Checklists associated with your account" header-class="text-h6">
                    <q-card>
                        <q-card-section>
                            <q-list bordered separator>
                                <template v-for="checklist in checklistArr">
                                    <template v-if="checklist.name">
                                        <q-item :href="(clientRoot + '/checklists/checklist.php?clid=' + checklist.clid)">
                                            <q-item-section>
                                                <div class="row justify-start q-gutter-md items-center">
                                                    <div class="text-h6">
                                                        {{ checklist.name }}
                                                    </div>
                                                    <div>
                                                        <q-btn round color="primary" size=".6rem" :href="(clientRoot + '/checklists/checklistadmin.php?clid=' + checklist.clid)" icon="far fa-edit"></q-btn>
                                                    </div>
                                                </div>
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                </template>
                            </q-list>
                        </q-card-section>
                    </q-card>
                </q-expansion-item>
            </q-list>
        </template>
        <template v-else>
            <q-card class="q-mt-md">
                <q-card-section class="text-h6">
                    There are no checklists associated with your account
                </q-card-section>
            </q-card>
        </template>
        <template v-if="projectArr.length > 0">
            <q-list bordered class="rounded-borders q-mt-md">
                <q-expansion-item expand-separator label="Biotic inventory projects associated with your account" header-class="text-h6">
                    <q-card>
                        <q-card-section>
                            <q-list bordered separator>
                                <template v-for="project in projectArr">
                                    <q-item :href="(clientRoot + '/projects/project.php?pid=' + project.pid)">
                                        <q-item-section>
                                            <div class="row justify-start q-gutter-md items-center">
                                                <div class="text-h6">
                                                    {{ project.projname }}
                                                </div>
                                                <div>
                                                    <q-btn round color="primary" size=".6rem" :href="(clientRoot + '/projects/project.php?pid=' + project.pid)" icon="far fa-edit"></q-btn>
                                                </div>
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </template>
                            </q-list>
                        </q-card-section>
                    </q-card>
                </q-expansion-item>
            </q-list>
        </template>
        <template v-else>
            <q-card class="q-mt-md">
                <q-card-section class="text-h6">
                    There are no biotic inventory projects associated with your account
                </q-card-section>
            </q-card>
        </template>
    `,
    setup() {
        const baseStore = useBaseStore();
        const userStore = useUserStore();

        const checklistArr = Vue.computed(() => userStore.getChecklistArr);
        const clientRoot = baseStore.getClientRoot;
        const projectArr = Vue.computed(() => userStore.getProjectArr);

        return {
            checklistArr,
            clientRoot,
            projectArr
        }
    }
};
