const useCollectionStore = Pinia.defineStore('collection', {
    state: () => ({
        blankCollectionRecord: {
            collid: 0,
            ccpk: null,
            institutioncode: null,
            collectioncode: null,
            collectionname: null,
            collectionid: null,
            datasetid: null,
            datasetname: null,
            iid: null,
            fulldescription: null,
            homepage: null,
            individualurl: null,
            contact: null,
            email: null,
            latitudedecimal: null,
            longitudedecimal: null,
            icon: null,
            colltype: null,
            managementtype: null,
            datarecordingmethod: null,
            defaultrepcount: null,
            collectionguid: null,
            securitykey: null,
            guidtarget: null,
            rightsholder: null,
            rights: null,
            usageterm: null,
            publishtogbif: null,
            publishtoidigbio: null,
            aggkeysstr: null,
            dwcaurl: null,
            dwcapublishtimestamp: null,
            bibliographiccitation: null,
            accessrights: null,
            configjson: null,
            configuredData: null,
            ispublic: null
        },
        collectionArr: [],
        collectionData: {},
        collectionEditData: {},
        collectionId: 0,
        collectionPermissions: [],
        collectionUpdateData: {},
        configuredDataDownloads: [],
        editorHideFields: [],
        eventMofData: {},
        eventMofDataFields: {},
        eventMofDataFieldsLayoutData: {},
        eventMofDataLabel: 'Measurement or Fact Data',
        occurrenceFieldControlledVocabularies: {},
        occurrenceMofData: {},
        occurrenceMofDataFields: {},
        occurrenceMofDataFieldsLayoutData: {},
        occurrenceMofDataLabel: 'Measurement or Fact Data',
        transcriberHideFields: [],
    }),
    getters: {
        getClientRoot() {
            const store = useBaseStore();
            return store.getClientRoot;
        },
        getCollectionArr(state) {
            return state.collectionArr;
        },
        getCollectionData(state) {
            return state.collectionEditData;
        },
        getCollectionEditsExist(state) {
            let exist = false;
            state.collectionUpdateData = Object.assign({}, {});
            for(let key in state.collectionEditData) {
                if(state.collectionEditData.hasOwnProperty(key) && state.collectionEditData[key] !== state.collectionData[key]) {
                    exist = true;
                    state.collectionUpdateData[key] = state.collectionEditData[key];
                }
            }
            return exist;
        },
        getCollectionId(state) {
            return state.collectionId;
        },
        getCollectionPermissions(state) {
            return state.collectionPermissions;
        },
        getCollectionValid(state) {
            return !!state.collectionEditData['collectionname'];
        },
        getConfiguredDataDownloads(state) {
            return state.configuredDataDownloads;
        },
        getDatasetKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('datasetKey')) ? state.collectionData['aggkeysstr']['datasetKey'] : null);
        },
        getEditorHideFields(state) {
            return state.editorHideFields;
        },
        getEndpointKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('endpointKey')) ? state.collectionData['aggkeysstr']['endpointKey'] : null);
        },
        getEventMofData(state) {
            return state.eventMofData;
        },
        getEventMofDataFields(state) {
            return state.eventMofDataFields;
        },
        getEventMofDataFieldsLayoutData(state) {
            return state.eventMofDataFieldsLayoutData;
        },
        getEventMofDataLabel(state) {
            return state.eventMofDataLabel;
        },
        getGeoreferencedPercent(state) {
            let percent = 0;
            if(state.collectionData.hasOwnProperty('georefcnt') && Number(state.collectionData['georefcnt']) > 0 && state.collectionData.hasOwnProperty('recordcnt') && Number(state.collectionData['recordcnt']) > 0){
                percent = (100 * (Number(state.collectionData['georefcnt']) / Number(state.collectionData['recordcnt'])));
            }
            percent = percent > 1 ? percent.toFixed() : percent.toFixed(2);
            return percent;
        },
        getIdigbioKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('idigbioKey')) ? state.collectionData['aggkeysstr']['idigbioKey'] : null);
        },
        getImagePercent(state) {
            let percent = 0;
            if(state.collectionData.hasOwnProperty('dynamicProperties') && state.collectionData['dynamicProperties'].hasOwnProperty('imgcnt') && Number(state.collectionData['dynamicProperties']['imgcnt']) > 0 && state.collectionData.hasOwnProperty('recordcnt') && Number(state.collectionData['recordcnt']) > 0){
                percent = (100 * (Number(state.collectionData['dynamicProperties']['imgcnt']) / Number(state.collectionData['recordcnt'])));
            }
            percent = percent > 1 ? percent.toFixed() : percent.toFixed(2);
            return percent;
        },
        getInstallationKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('installationKey')) ? state.collectionData['aggkeysstr']['installationKey'] : null);
        },
        getLimitIdsToThesaurus(state) {
            return (state.collectionData['configuredData'] && state.collectionData['configuredData'].hasOwnProperty('limitIdsToThesaurus') && Number(state.collectionData['configuredData']['limitIdsToThesaurus']) === 1);
        },
        getOccurrenceFieldControlledVocabularies(state) {
            return state.occurrenceFieldControlledVocabularies;
        },
        getOccurrenceMofData(state) {
            return state.occurrenceMofData;
        },
        getOccurrenceMofDataFields(state) {
            return state.occurrenceMofDataFields;
        },
        getOccurrenceMofDataFieldsLayoutData(state) {
            return state.occurrenceMofDataFieldsLayoutData;
        },
        getOccurrenceMofDataLabel(state) {
            return state.occurrenceMofDataLabel;
        },
        getOrganizationKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('organizationKey')) ? state.collectionData['aggkeysstr']['organizationKey'] : null);
        },
        getPublishToGBIF(state) {
            return (state.collectionData.hasOwnProperty('publishtogbif') && Number(state.collectionData['publishtogbif']) === 1);
        },
        getPublishToIdigbio(state) {
            return (state.collectionData.hasOwnProperty('publishtoidigbio') && Number(state.collectionData['publishtoidigbio']) === 1);
        },
        getSpeciesIDPercent(state) {
            let percent = 0;
            if(state.collectionData.hasOwnProperty('dynamicProperties') && state.collectionData['dynamicProperties'].hasOwnProperty('SpecimensCountID') && Number(state.collectionData['dynamicProperties']['SpecimensCountID']) > 0 && state.collectionData.hasOwnProperty('recordcnt') && Number(state.collectionData['recordcnt']) > 0){
                percent = (100 * (Number(state.collectionData['dynamicProperties']['SpecimensCountID']) / Number(state.collectionData['recordcnt'])));
            }
            percent = percent > 1 ? percent.toFixed() : percent.toFixed(2);
            return percent;
        },
        getTranscriberHideFields(state) {
            return state.transcriberHideFields;
        }
    },
    actions: {
        batchPopulateCollectionRecordGUIDs(callback = null) {
            const formData = new FormData();
            formData.append('collid', this.getCollectionId.toString());
            formData.append('action', 'batchPopulateOccurrenceGUIDs');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    const formData = new FormData();
                    formData.append('collid', this.getCollectionId.toString());
                    formData.append('action', 'batchPopulateOccurrenceDeterminationGUIDs');
                    fetch(occurrenceDeterminationApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.text() : null;
                    })
                    .then((res) => {
                        if(Number(res) === 1){
                            const formData = new FormData();
                            formData.append('collid', this.getCollectionId.toString());
                            formData.append('action', 'batchPopulateOccurrenceImageGUIDs');
                            fetch(imageApiUrl, {
                                method: 'POST',
                                body: formData
                            })
                            .then((response) => {
                                return response.ok ? response.text() : null;
                            })
                            .then((res) => {
                                if(callback){
                                    callback(Number(res));
                                }
                            });
                        }
                        else if(callback){
                            callback(Number(res));
                        }
                    });
                }
                else if(callback){
                    callback(Number(res));
                }
            });
        },
        cleanSOLRIndex(collidStr, callback = null) {
            if(collidStr){
                const formData = new FormData();
                formData.append('collidStr', collidStr.toString());
                formData.append('action', 'cleanSOLRIndex');
                fetch(collectionApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(callback){
                        callback(Number(res));
                    }
                });
            }
        },
        clearCollectionData() {
            this.collectionId = 0;
            this.collectionData = Object.assign({}, this.blankCollectionRecord);
            this.collectionPermissions.length = 0;
            this.configuredDataDownloads.length = 0;
            this.eventMofData = Object.assign({}, {});
            this.eventMofDataFields = Object.assign({}, {});
            this.eventMofDataFieldsLayoutData = Object.assign({}, {});
            this.eventMofDataLabel = 'Measurement or Fact Data';
            this.occurrenceFieldControlledVocabularies = Object.assign({}, {});
            this.occurrenceMofData = Object.assign({}, {});
            this.occurrenceMofDataFields = Object.assign({}, {});
            this.occurrenceMofDataFieldsLayoutData = Object.assign({}, {});
            this.occurrenceMofDataLabel = 'Measurement or Fact Data';
        },
        createCollectionRecord(callback) {
            const formData = new FormData();
            formData.append('collection', JSON.stringify(this.collectionEditData));
            formData.append('action', 'createCollectionRecord');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) > 0){
                    this.setCollection(Number(res));
                }
            });
        },
        deleteCollectionRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('action', 'deleteCollectionRecord');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    this.setCollection(0);
                    callback(Number(val));
                });
            });
        },
        getCollectionListByUid(uid, callback) {
            const formData = new FormData();
            formData.append('uid', uid.toString());
            formData.append('action', 'getCollectionListByUid');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                callback(resObj);
            });
        },
        setCollection(collid, callback = null) {
            this.clearCollectionData();
            if(Number(collid) > 0){
                this.collectionId = Number(collid);
                const formData = new FormData();
                formData.append('permissionJson', JSON.stringify(['CollAdmin', 'CollEditor']));
                formData.append('key', collid.toString());
                formData.append('action', 'validatePermission');
                fetch(permissionApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((resData) => {
                    this.collectionPermissions = resData;
                    this.setCollectionInfo(callback);
                });
            }
        },
        setCollectionArr() {
            const formData = new FormData();
            formData.append('action', 'getCollectionArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resData) => {
                this.collectionArr = resData;
            });
        },
        setCollectionInfo(callback = null) {
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('action', 'getCollectionInfoArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                if(Number(resObj['ispublic']) === 1 || (this.collectionPermissions.includes('CollAdmin') || this.collectionPermissions.includes('CollEditor'))){
                    this.collectionData = Object.assign({}, resObj);
                    this.collectionEditData = Object.assign({}, this.collectionData);
                    if(this.collectionData['configuredData']){
                        if(this.collectionData['configuredData'].hasOwnProperty('eventMofExtension')){
                            this.eventMofData = Object.assign({}, this.collectionData['configuredData']['eventMofExtension']);
                            if(Object.keys(this.collectionData['configuredData']['eventMofExtension']['dataFields']).length > 0){
                                this.eventMofDataFields = this.collectionData['configuredData']['eventMofExtension']['dataFields'];
                                if(this.collectionData['configuredData']['eventMofExtension'].hasOwnProperty('dataLayout') && this.collectionData['configuredData']['eventMofExtension']['dataLayout']){
                                    this.eventMofDataFieldsLayoutData = this.collectionData['configuredData']['eventMofExtension']['dataLayout'];
                                }
                                if(this.collectionData['configuredData']['eventMofExtension'].hasOwnProperty('dataLabel') && this.collectionData['configuredData']['eventMofExtension']['dataLabel']){
                                    this.eventMofDataLabel = this.collectionData['configuredData']['eventMofExtension']['dataLabel'].toString();
                                }
                            }
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('occurrenceMofExtension')){
                            this.occurrenceMofData = Object.assign({}, this.collectionData['configuredData']['occurrenceMofExtension']);
                            if(Object.keys(this.collectionData['configuredData']['occurrenceMofExtension']['dataFields']).length > 0){
                                this.occurrenceMofDataFields = this.collectionData['configuredData']['occurrenceMofExtension']['dataFields'];
                                if(this.collectionData['configuredData']['occurrenceMofExtension'].hasOwnProperty('dataLayout') && this.collectionData['configuredData']['occurrenceMofExtension']['dataLayout']){
                                    this.occurrenceMofDataFieldsLayoutData = this.collectionData['configuredData']['occurrenceMofExtension']['dataLayout'];
                                }
                                if(this.collectionData['configuredData']['occurrenceMofExtension'].hasOwnProperty('dataLabel') && this.collectionData['configuredData']['occurrenceMofExtension']['dataLabel']){
                                    this.occurrenceMofDataLabel = this.collectionData['configuredData']['occurrenceMofExtension']['dataLabel'].toString();
                                }
                            }
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('dataDownloads') && this.collectionData['configuredData']['dataDownloads']){
                            this.configuredDataDownloads = this.collectionData['configuredData']['dataDownloads'];
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('occurrenceFieldControlledVocabularies') && this.collectionData['configuredData']['occurrenceFieldControlledVocabularies']){
                            this.occurrenceFieldControlledVocabularies = Object.assign({}, this.collectionData['configuredData']['occurrenceFieldControlledVocabularies']);
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('editorHideFields') && this.collectionData['configuredData']['editorHideFields']){
                            this.editorHideFields = this.collectionData['configuredData']['editorHideFields'];
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('transcriberHideFields') && this.collectionData['configuredData']['transcriberHideFields']){
                            this.transcriberHideFields = this.collectionData['configuredData']['transcriberHideFields'];
                        }
                    }
                    if(callback){
                        callback();
                    }
                }
                else{
                    window.location.href = this.getClientRoot + '/index.php';
                }
            });
        },
        updateCollectionRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('collectionData', JSON.stringify(this.collectionUpdateData));
            formData.append('action', 'updateCollectionRecord');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.collectionData = Object.assign({}, this.collectionEditData);
                }
            });
        },
        updateCollectionStatistics(collidStr, newUpload, callback = null) {
            if(collidStr){
                const formData = new FormData();
                formData.append('collid', this.collectionId.toString());
                formData.append('collidStr', collidStr.toString());
                formData.append('newUpload', (newUpload ? '1' : '0'));
                formData.append('action', 'updateCollectionStatistics');
                fetch(collectionApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    return response.ok ? response.text() : null;
                })
                .then((res) => {
                    if(callback){
                        callback(Number(res));
                        this.setCollectionInfo();
                    }
                });
            }
        }
    }
});
