const userPermissionManagementModule = {
    props: {
        disabled: {
            type: Boolean,
            default: false
        },
        permissionLabel: {
            type: String,
            default: 'Permitted'
        },
        permission: {
            type: String,
            default: null
        },
        tablePk: {
            type: Number,
            default: null
        }
    },
    template: `
        <div class="q-pa-md column q-col-gutter-lg">
            <template v-if="permittedUserArr.length > 0">
                <div class="column">
                    <div class="row justify-start q-gutter-md">
                        <div class="text-h6 text-bold">{{ permissionLabel }} user list</div>
                    </div>
                    <div class="q-mt-xs q-ml-md column q-gutter-xs">
                        <template v-for="user in permittedUserArr">
                            <div class="row justify-start q-gutter-md">
                                <div class="text-body1">
                                    {{ user['lastname'] + ', ' + user['firstname'] + ' (' + user['username'] + ')' }}
                                </div>
                                <div class="self-center">
                                    <q-btn color="white" text-color="black" size=".6rem" @click="removeUser(user['uid']);" icon="far fa-trash-alt" dense>
                                        <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                                            Remove user
                                        </q-tooltip>
                                    </q-btn>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="text-h6 text-bold">
                    There are currently no users with {{ permissionLabel }} permission
                </div>
            </template>
            <div class="q-px-xl">
                <q-card flat bordered>
                    <q-card-section>
                        <div class="text-body1 text-bold">Add a new {{ permissionLabel }}</div>
                        <div class="row justify-between q-gutter-sm no-wrap">
                            <div class="col-grow">
                                <user-auto-complete label="Add User" :value="selectedAddUser" @update:value="processAddUserChange"></user-auto-complete>
                            </div>
                            <div class="col-2 row justify-end">
                                <div>
                                    <q-btn color="secondary" @click="addUser();" label="Add User"/>
                                </div>
                            </div>
                        </div>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    `,
    components: {
        'user-auto-complete': userAutoComplete
    },
    setup(props, context) {
        const { showNotification } = useCore();

        const permittedUserArr = Vue.ref([]);
        const selectedAddUser = Vue.ref(null);
        const selectedAddUserId = Vue.ref(null);

        function addUser() {
            const formData = new FormData();
            formData.append('uid', selectedAddUserId.value.toString());
            formData.append('permission', props.permission.toString());
            formData.append('tablepk', props.tablePk.toString());
            formData.append('action', 'addUserPermission');
            fetch(permissionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    setPermittedUserArr();
                    selectedAddUser.value = null;
                    selectedAddUserId.value = null;
                }
            });
        }

        function processAddUserChange(user) {
            if(typeof user === 'object' || !user){
                selectedAddUser.value = null;
                selectedAddUserId.value = null;
                const existingObj = user ? permittedUserArr.value.find(puser => Number(puser['uid']) === Number(user['uid'])) : null;
                if(existingObj){
                    showNotification('negative', 'That user already is included in the user list');
                }
                else if(user){
                    selectedAddUser.value = user['label'];
                    selectedAddUserId.value = user['uid'];
                }
            }
        }

        function removeUser(uid) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('permission', props.permission.toString());
            formData.append('tablepk', props.tablePk.toString());
            formData.append('action', 'deleteUserPermission');
            fetch(permissionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    setPermittedUserArr();
                }
            });
        }

        function setPermittedUserArr() {
            const formData = new FormData();
            formData.append('permission', props.permission);
            formData.append('tablepk', props.tablePk.toString());
            formData.append('action', 'getUserArrByPermission');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                permittedUserArr.value = resObj;
                context.emit('update:user-list', permittedUserArr.value);
            });
        }

        Vue.onMounted(() => {
            if(props.permission){
                setPermittedUserArr();
            }
        });

        return {
            permittedUserArr,
            selectedAddUser,
            addUser,
            processAddUserChange,
            removeUser
        }
    }
};
