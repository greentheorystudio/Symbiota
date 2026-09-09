const gbifInstitutionCollectionListPopup = {
    props: {
        dataArr: {
            type: Array,
            default: null
        },
        popupType: {
            type: String,
            default: 'institution'
        },
        showPopup: {
            type: Boolean,
            default: false
        }
    },
    template: `
        <q-dialog class="z-top" v-model="showPopup" persistent>
            <q-card class="md-square-popup overflow-hidden">
                <div class="row justify-end items-start map-sm-popup">
                    <div>
                        <q-btn square dense color="red" text-color="white" icon="fas fa-times" @click="closePopup();" aria-label="Close window" tabindex="0"></q-btn>
                    </div>
                </div>
                <div ref="contentRef" class="fit">
                    <div :style="contentStyle" class="overflow-auto">
                        <div v-if="displayArr.length" class="q-pa-md column q-gutter-md">
                            <q-card v-for="record in displayArr">
                                <q-card-section class="q-pa-md column">
                                    <template v-if="popupType === 'institution'">
                                        <div>
                                            <span class="text-bold">{{ record['code'] + ': ' }}</span> {{ record['name'] }}
                                        </div>
                                        <div class="q-mt-md q-pl-md row justify-end q-gutter-md">
                                            <q-btn color="primary" @click="processInstitutionSelection(record.key);" label="Select" dense tabindex="0" />
                                        </div>
                                    </template>
                                </q-card-section>
                            </q-card>
                        </div>
                    </div>
                </div>
            </q-card>
        </q-dialog>
    `,
    setup(props, context) {
        const { hideWorking, showWorking } = useCore();

        const contentRef = Vue.ref(null);
        const contentStyle = Vue.ref(null);
        const displayArr = Vue.computed(() => {
            const returnArr = [];
            props.dataArr.forEach(record => {
                const newRecord = {
                    code: record.code,
                    key: record.key,
                    name: record.name
                };
                if(!returnArr.includes(newRecord) && record['type'] === props.popupType){
                    returnArr.push(newRecord);
                }
            });
            returnArr.sort((a, b) => {
                return a['name'].localeCompare(b['name']);
            });
            return returnArr;
        });
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function processInstitutionSelection(key) {
            showWorking();
            const url = 'https://api.gbif.org/v1/grscicoll/institution/' + key;
            fetch(url, {
                method: 'GET'
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                hideWorking();
                context.emit('update:data', data);
            });
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        Vue.onMounted(() => {
            setContentStyle();
            window.addEventListener('resize', setContentStyle);
        });

        return {
            contentRef,
            contentStyle,
            displayArr,
            closePopup,
            processInstitutionSelection
        }
    }
};
