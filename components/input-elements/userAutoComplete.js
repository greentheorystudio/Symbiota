const userAutoComplete = {
    props: {
        definition: {
            type: Object,
            default: null
        },
        disabled: {
            type: Boolean,
            default: false
        },
        label: {
            type: String,
            default: 'User'
        },
        tabindex: {
            type: Number,
            default: 0
        },
        userType: {
            type: String,
            default: 'confirmed'
        },
        value: {
            type: String,
            default: null
        }
    },
    template: `
        <q-select v-model="currentValue" use-input hide-selected fill-input outlined dense options-dense hide-dropdown-icon popup-content-class="z-top" behavior="menu" input-debounce="0" bg-color="white" :options="autocompleteOptions" @filter="getOptions" @update:model-value="processValueChange" :label="label" :tabindex="tabindex" :disable="disabled">
            <template v-if="!disabled && (currentValue || definition)" v-slot:append>
                <q-icon role="button" v-if="definition" name="help" class="cursor-pointer" @click="openDefinitionPopup();" @keyup.enter="openDefinitionPopup();" aria-label="See field definition" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        See field definition
                    </q-tooltip>
                </q-icon>
                <q-icon role="button" v-if="currentValue" name="cancel" class="cursor-pointer" @click="processValueChange(null);" @keyup.enter="processValueChange(null);" aria-label="Clear value" :tabindex="tabindex">
                    <q-tooltip anchor="top middle" self="bottom middle" class="text-body2" :delay="1000" :offset="[10, 10]">
                        Clear value
                    </q-tooltip>
                </q-icon>
            </template>
        </q-select>
        <template v-if="definition">
            <q-dialog class="z-top" v-model="displayDefinitionPopup" persistent aria-label="Definition pop up">
                <q-card class="sm-popup">
                    <div class="row justify-end items-start map-sm-popup">
                        <div>
                            <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="displayDefinitionPopup = false" aria-label="Close definition pop up" :tabindex="tabindex"></q-btn>
                        </div>
                    </div>
                    <div class="q-pa-sm column q-gutter-sm">
                        <div class="text-h6">{{ label }}</div>
                        <template v-if="definition.definition">
                            <div>
                                <span class="text-bold">Definition: </span>{{ definition.definition }}
                            </div>
                        </template>
                        <template v-if="definition.comments">
                            <div>
                                <span class="text-bold">Comments: </span>{{ definition.comments }}
                            </div>
                        </template>
                        <template v-if="definition.examples">
                            <div>
                                <span class="text-bold">Examples: </span>{{ definition.examples }}
                            </div>
                        </template>
                        <template v-if="definition.source">
                            <div>
                                <a :href="definition.source" target="_blank" aria-label="External link: Go to source - Opens in separate tab" :tabindex="tabindex"><span class="text-bold">Go to source</span></a>
                            </div>
                        </template>
                    </div>
                </q-card>
            </q-dialog>
        </template>
    `,
    setup(props, context) {
        const autocompleteOptions = Vue.computed(() => {
            const returnArr = [];
            userArr.value.forEach((user) => {
                user.value = user['uid'];
                user.label = (user['lastname'] + ', ' + user['firstname'] + ' (' + user['username'] + ')');
                returnArr.push(user);
            });
            return returnArr;
        });
        const currentValue = Vue.ref(null);
        const displayDefinitionPopup = Vue.ref(false);
        const userArr = Vue.ref([]);

        function getOptions(val, update) {
            update(() => {
                if(val.length > 2) {
                    const formData = new FormData();
                    formData.append('keyword', val);
                    formData.append('userType', props.userType);
                    formData.append('action', 'getUserListArr');
                    fetch(profileApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((resObj) => {
                        userArr.value = resObj;
                    });
                }
                else{
                    userArr.value = [];
                }
            });
        }

        function openDefinitionPopup() {
            displayDefinitionPopup.value = true;
        }

        function processValueChange(selectedObj) {
            context.emit('update:value', selectedObj);
            currentValue.value = selectedObj;
        }

        function setCurrentValueFromUid() {
            const formData = new FormData();
            formData.append('uid', props.value);
            formData.append('action', 'getUserByUid');
            fetch(profileApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                if(resObj.hasOwnProperty('uid') && Number(resObj.uid) > 0) {
                    currentValue.value = {
                        value: resObj.uid,
                        label: (resObj['lastname'] + ', ' + resObj['firstname'] + ' (' + resObj['username'] + ')')
                    };
                }
            });
        }

        Vue.onMounted(() => {
            if(props.value){
                if(Number(props.value) > 0){
                    setCurrentValueFromUid();
                }
                else{
                    currentValue.value = {value: props.value, label: props.value};
                }
            }
        });

        return {
            autocompleteOptions,
            currentValue,
            displayDefinitionPopup,
            getOptions,
            openDefinitionPopup,
            processValueChange
        }
    }
};
