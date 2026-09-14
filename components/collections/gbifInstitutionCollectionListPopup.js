const gbifInstitutionCollectionListPopup = {
    props: {
        dataArr: {
            type: Array,
            default: null
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
                                    <div>
                                        <span class="text-bold">{{ record['code'] + ': ' }}</span> {{ record['name'] }}
                                    </div>
                                    <div class="q-mt-md q-pl-md row justify-end q-gutter-md">
                                        <q-btn color="primary" @click="processSelection(record.key);" label="Select" dense tabindex="0" />
                                    </div>
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

        const collectionData = Vue.ref({});
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
                if(!returnArr.includes(newRecord) && record['type'] === 'collection'){
                    returnArr.push(newRecord);
                }
            });
            returnArr.sort((a, b) => {
                return a['name'].localeCompare(b['name']);
            });
            return returnArr;
        });
        const locationData = Vue.ref({});
        
        Vue.watch(contentRef, () => {
            setContentStyle();
        });

        function closePopup() {
            context.emit('close:popup');
        }

        function getPrimaryContactFromData(contactData) {
            let returnData = contactData.find(contact => contact['primary'] === true);
            if(!returnData){
                returnData = contactData.at(-1);
            }
            return returnData;
        }

        function processSelection(key) {
            const returnData = {};
            collectionData.value = Object.assign({}, {});
            locationData.value = Object.assign({}, {});
            showWorking();
            const url = 'https://api.gbif.org/v1/grscicoll/collection/' + key;
            fetch(url, {
                method: 'GET'
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                returnData['collection'] = setCollectionData(data);
                returnData['location'] = setLocationData(data);
                hideWorking();
                context.emit('update:data', returnData);
            });
        }

        function setCollectionData(data) {
            const returnData = {};
            if(data['institutionCode']){
                returnData['institutioncode'] = data['institutionCode'];
            }
            if(data['code']){
                returnData['collectioncode'] = data['code'];
            }
            if(data['name']){
                returnData['collectionname'] = data['name'];
            }
            if(data['description']){
                returnData['fulldescription'] = data['description'];
            }
            if(data['homepage']){
                returnData['homepage'] = data['homepage'];
            }
            if(data['contactPersons'] && data['contactPersons'].length > 0){
                const contactData = getPrimaryContactFromData(data['contactPersons'], );
                if(contactData){
                    returnData['contact'] = (contactData['firstName'] ? contactData['firstName'] : '') + ((contactData['firstName'] && contactData['lastName']) ? ' ' : '') + (contactData['lastName'] ? contactData['lastName'] : '');
                    if(contactData['email'] && contactData['email'].length > 0){
                        returnData['email'] = contactData['email'][0];
                    }
                }
            }
            return returnData;
        }

        function setContentStyle() {
            contentStyle.value = null;
            if(contentRef.value){
                contentStyle.value = 'height: ' + (contentRef.value.clientHeight - 30) + 'px;width: ' + contentRef.value.clientWidth + 'px;';
            }
        }

        function setLocationData(data) {
            const returnData = {};
            if(data['institutionCode']){
                returnData['institutioncode'] = data['institutionCode'];
            }
            if(data['name']){
                returnData['institutionname'] = data['name'];
            }
            if(data['institutionName']){
                returnData['institutionname2'] = data['institutionName'];
            }
            if(data['mailingAddress']){
                if(data['mailingAddress']['address']){
                    returnData['address1'] = data['mailingAddress']['address'];
                }
                if(data['mailingAddress']['city']){
                    returnData['city'] = data['mailingAddress']['city'];
                }
                if(data['mailingAddress']['province']){
                    returnData['stateprovince'] = data['mailingAddress']['province'];
                }
                if(data['mailingAddress']['postalCode']){
                    returnData['postalcode'] = data['mailingAddress']['postalCode'];
                }
                if(data['mailingAddress']['country']){
                    returnData['country'] = data['mailingAddress']['country'];
                }
            }
            if(data['contactPersons'] && data['contactPersons'].length > 0){
                const contactData = getPrimaryContactFromData(data['contactPersons'], );
                if(contactData){
                    returnData['contact'] = (contactData['firstName'] ? contactData['firstName'] : '') + ((contactData['firstName'] && contactData['lastName']) ? ' ' : '') + (contactData['lastName'] ? contactData['lastName'] : '');
                    if(contactData['email'] && contactData['email'].length > 0){
                        returnData['email'] = contactData['email'][0];
                    }
                    if(contactData['phone'] && contactData['phone'].length > 0){
                        returnData['phone'] = contactData['phone'][0];
                    }
                }
            }
            return returnData;
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
            processSelection
        }
    }
};
