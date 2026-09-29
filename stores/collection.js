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
            colltype: 'PreservedSpecimen',
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
            aggkeysstr: null,
            dwcaurl: null,
            dwcapublishtimestamp: null,
            bibliographiccitation: null,
            accessrights: null,
            configjson: null,
            configuredData: null,
            ispublic: 1
        },
        collectionArr: [],
        collectionData: {},
        collectionEditData: {},
        collectionFieldDefinitions: {},
        collectionId: 0,
        collectionMofFieldDefinitions: {},
        collectionPermissions: [],
        collectionUpdateData: {},
        computedDataConfig: {
            event: [],
            location: [],
            occurrence: []
        },
        computedDataFieldNameArr: [],
        configuredDataDownloads: [],
        editorHideFields: [],
        eventMofCalculatedDataFields: {},
        eventMofData: {},
        eventMofDataFields: {},
        eventMofDataFieldsLayoutData: [],
        eventMofDataLabel: 'Measurement or Fact Data',
        locationMofCalculatedDataFields: {},
        locationMofData: {},
        locationMofDataFields: {},
        locationMofDataFieldsLayoutData: [],
        locationMofDataLabel: 'Measurement or Fact Data',
        occurrenceFieldControlledVocabularies: {},
        occurrenceMofCalculatedDataFields: {},
        occurrenceMofData: {},
        occurrenceMofDataFields: {},
        occurrenceMofDataFieldsLayoutData: [],
        occurrenceMofDataLabel: 'Measurement or Fact Data',
        taxonIdentifierFieldArr: [],
        transcriberHideFields: []
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
            const skipFields = ['institutionname','institutionname2','address1','address2','city','stateprovince','postalcode','country','countrycode'];
            let exist = false;
            state.collectionUpdateData = Object.assign({}, {});
            for(let key in state.collectionEditData) {
                if(!skipFields.includes(key) && state.collectionEditData.hasOwnProperty(key) && state.collectionEditData[key] !== state.collectionData[key]) {
                    exist = true;
                    state.collectionUpdateData[key] = state.collectionEditData[key];
                }
            }
            return exist;
        },
        getCollectionFieldDefinitions(state) {
            return state.collectionFieldDefinitions;
        },
        getCollectionId(state) {
            return state.collectionId;
        },
        getCollectionMofFieldDefinitions(state) {
            return state.collectionMofFieldDefinitions;
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
        getEventMofCalculatedDataFields(state) {
            return state.eventMofCalculatedDataFields;
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
        getLocationMofCalculatedDataFields(state) {
            return state.locationMofCalculatedDataFields;
        },
        getLocationMofData(state) {
            return state.locationMofData;
        },
        getLocationMofDataFields(state) {
            return state.locationMofDataFields;
        },
        getLocationMofDataFieldsLayoutData(state) {
            return state.locationMofDataFieldsLayoutData;
        },
        getLocationMofDataLabel(state) {
            return state.locationMofDataLabel;
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
        getOccurrenceMofCalculatedDataFields(state) {
            return state.occurrenceMofCalculatedDataFields;
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
        getPublishToGBIF(state) {
            return (state.collectionData.hasOwnProperty('publishtogbif') && Number(state.collectionData['publishtogbif']) === 1);
        },
        getSpeciesIDPercent(state) {
            let percent = 0;
            if(state.collectionData.hasOwnProperty('dynamicProperties') && state.collectionData['dynamicProperties'].hasOwnProperty('SpecimensCountID') && Number(state.collectionData['dynamicProperties']['SpecimensCountID']) > 0 && state.collectionData.hasOwnProperty('recordcnt') && Number(state.collectionData['recordcnt']) > 0){
                percent = (100 * (Number(state.collectionData['dynamicProperties']['SpecimensCountID']) / Number(state.collectionData['recordcnt'])));
            }
            percent = percent > 1 ? percent.toFixed() : percent.toFixed(2);
            return percent;
        },
        getTaxonIdentifierFieldArr(state) {
            return state.taxonIdentifierFieldArr;
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
        clearCollectionData() {
            this.computedDataConfig.event.length = 0;
            this.computedDataConfig.location.length = 0;
            this.computedDataConfig.occurrence.length = 0;
            this.computedDataFieldNameArr.length = 0;
            this.collectionData = Object.assign({}, this.blankCollectionRecord);
            this.configuredDataDownloads.length = 0;
            this.eventMofCalculatedDataFields = Object.assign({}, {});
            this.eventMofData = Object.assign({}, {});
            this.eventMofDataFields = Object.assign({}, {});
            this.eventMofDataFieldsLayoutData.length = 0;
            this.eventMofDataLabel = 'Measurement or Fact Data';
            this.locationMofCalculatedDataFields = Object.assign({}, {});
            this.locationMofData = Object.assign({}, {});
            this.locationMofDataFields = Object.assign({}, {});
            this.locationMofDataFieldsLayoutData.length = 0;
            this.locationMofDataLabel = 'Measurement or Fact Data';
            this.occurrenceFieldControlledVocabularies = Object.assign({}, {});
            this.occurrenceMofCalculatedDataFields = Object.assign({}, {});
            this.occurrenceMofData = Object.assign({}, {});
            this.occurrenceMofDataFields = Object.assign({}, {});
            this.occurrenceMofDataFieldsLayoutData.length = 0;
            this.occurrenceMofDataLabel = 'Measurement or Fact Data';
            this.editorHideFields.length = 0;
            this.transcriberHideFields.length = 0;
            this.taxonIdentifierFieldArr.length = 0;
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
                if(res && Number(res) > 0){
                    this.setCollection(Number(res));
                }
                callback(Number(res));
            });
        },
        deleteCollectionIconRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('action', 'deleteCollectionIconRecord');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(Number(res) === 1){
                    this.collectionData['icon'] = null;
                    this.collectionEditData['icon'] = null;
                }
                callback(Number(res));
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
        processConfiguredDataFields() {
            Object.keys(this.eventMofDataFields).forEach((fieldName) => {
                if(this.eventMofDataFields[fieldName]['dataType'] === 'calculated'){
                    this.eventMofCalculatedDataFields[fieldName] = Object.assign({}, this.eventMofDataFields[fieldName]);
                }
            });
            Object.keys(this.locationMofDataFields).forEach((fieldName) => {
                if(this.locationMofDataFields[fieldName]['dataType'] === 'calculated'){
                    this.locationMofCalculatedDataFields[fieldName] = Object.assign({}, this.locationMofDataFields[fieldName]);
                }
            });
            Object.keys(this.occurrenceMofDataFields).forEach((fieldName) => {
                if(this.occurrenceMofDataFields[fieldName]['dataType'] === 'calculated'){
                    this.occurrenceMofCalculatedDataFields[fieldName] = Object.assign({}, this.occurrenceMofDataFields[fieldName]);
                }
                else if(this.occurrenceMofDataFields[fieldName]['dataType'] === 'taxon-identifier'){
                    this.taxonIdentifierFieldArr.push({
                        fieldName: fieldName,
                        identifier: this.occurrenceMofDataFields[fieldName]['identifier']
                    });
                }
            });
        },
        setCollection(collid, callback = null) {
            this.collectionPermissions.length = 0;
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
            else{
                this.clearCollectionData();
                this.collectionEditData = Object.assign({}, this.collectionData);
                if(callback){
                    callback();
                }
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
        setCollectionFieldDefinitions() {
            fetch(fieldDefinitionsUrl)
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('collection')){
                    this.collectionFieldDefinitions = Object.assign({}, data['collection']);
                }
            });
        },
        setCollectionInfo(callback = null) {
            this.clearCollectionData();
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
                        if(this.collectionData['configuredData'].hasOwnProperty('locationMofExtension')){
                            this.locationMofData = Object.assign({}, this.collectionData['configuredData']['locationMofExtension']);
                            if(Object.keys(this.collectionData['configuredData']['locationMofExtension']['dataFields']).length > 0){
                                this.locationMofDataFields = this.collectionData['configuredData']['locationMofExtension']['dataFields'];
                                if(this.collectionData['configuredData']['locationMofExtension'].hasOwnProperty('dataLayout') && this.collectionData['configuredData']['locationMofExtension']['dataLayout']){
                                    this.locationMofDataFieldsLayoutData = this.collectionData['configuredData']['locationMofExtension']['dataLayout'].slice();
                                }
                                if(this.collectionData['configuredData']['locationMofExtension'].hasOwnProperty('dataLabel') && this.collectionData['configuredData']['locationMofExtension']['dataLabel']){
                                    this.locationMofDataLabel = this.collectionData['configuredData']['locationMofExtension']['dataLabel'].toString();
                                }
                            }
                        }
                        if(this.collectionData['configuredData'].hasOwnProperty('eventMofExtension')){
                            this.eventMofData = Object.assign({}, this.collectionData['configuredData']['eventMofExtension']);
                            if(Object.keys(this.collectionData['configuredData']['eventMofExtension']['dataFields']).length > 0){
                                this.eventMofDataFields = this.collectionData['configuredData']['eventMofExtension']['dataFields'];
                                if(this.collectionData['configuredData']['eventMofExtension'].hasOwnProperty('dataLayout') && this.collectionData['configuredData']['eventMofExtension']['dataLayout']){
                                    this.eventMofDataFieldsLayoutData = this.collectionData['configuredData']['eventMofExtension']['dataLayout'].slice();
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
                                    this.occurrenceMofDataFieldsLayoutData = this.collectionData['configuredData']['occurrenceMofExtension']['dataLayout'].slice();
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
                        this.processConfiguredDataFields();
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
        setCollectionMofFieldDefinitions() {
            fetch(fieldDefinitionsUrl)
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('collection-mof-fields')){
                    this.collectionMofFieldDefinitions = Object.assign({}, data['collection-mof-fields']);
                }
            });
        },
        updateCollectionEditData(key, value) {
            this.collectionEditData[key] = (value && (key === 'instituioncode' || key === 'collectioncode')) ? value.toUpperCase() : value;
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
        },
        uploadCollectionIcon(file, url, callback) {
            const formData = new FormData();
            formData.append('iconFile', file);
            formData.append('iconUrl', url);
            formData.append('collid', this.collectionId.toString());
            formData.append('action', 'uploadCollectionIcon');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res !== ''){
                    this.collectionData['icon'] = res;
                    this.collectionEditData['icon'] = res;
                }
                callback(res);
            });
        },
        updateConfiguredPropertyValue(key, value, callback) {
            const configuredData = {};
            if(this.configuredDataDownloads.length > 0 || key === 'dataDownloads'){
                configuredData['dataDownloads'] = key === 'dataDownloads' ? value.slice() : this.configuredDataDownloads.slice();
            }
            if(Object.keys(this.occurrenceFieldControlledVocabularies).length > 0 || key === 'occurrenceFieldControlledVocabularies'){
                configuredData['occurrenceFieldControlledVocabularies'] = key === 'occurrenceFieldControlledVocabularies' ? Object.assign({}, value) : Object.assign({}, this.occurrenceFieldControlledVocabularies);
            }
            if(this.editorHideFields.length > 0 || key === 'editorHideFields'){
                configuredData['editorHideFields'] = key === 'editorHideFields' ? value.slice() : this.editorHideFields.slice();
            }
            if(this.transcriberHideFields.length > 0 || key === 'transcriberHideFields'){
                configuredData['transcriberHideFields'] = key === 'transcriberHideFields' ? value.slice() : this.transcriberHideFields.slice();
            }
            if(Object.keys(this.locationMofDataFields).length > 0 || key === 'locationMofExtension'){
                configuredData['locationMofExtension'] = {};
                configuredData['locationMofExtension']['dataFields'] = key === 'locationMofExtension' ? Object.assign({}, value['dataFields']) : Object.assign({}, this.locationMofDataFields);
                configuredData['locationMofExtension']['dataLayout'] = key === 'locationMofExtension' ? value['dataLayout'].slice() : this.locationMofDataFieldsLayoutData.slice();
                configuredData['locationMofExtension']['dataLabel'] = key === 'locationMofExtension' ? value['dataLabel'] : this.locationMofDataLabel;
            }
            if(Object.keys(this.eventMofDataFields).length > 0 || key === 'eventMofExtension'){
                configuredData['eventMofExtension'] = {};
                configuredData['eventMofExtension']['dataFields'] = key === 'eventMofExtension' ? Object.assign({}, value['dataFields']) : Object.assign({}, this.eventMofDataFields);
                configuredData['eventMofExtension']['dataLayout'] = key === 'eventMofExtension' ? value['dataLayout'].slice() : this.eventMofDataFieldsLayoutData.slice();
                configuredData['eventMofExtension']['dataLabel'] = key === 'eventMofExtension' ? value['dataLabel'] : this.eventMofDataLabel;
            }
            if(Object.keys(this.occurrenceMofDataFields).length > 0 || key === 'occurrenceMofExtension'){
                configuredData['occurrenceMofExtension'] = {};
                configuredData['occurrenceMofExtension']['dataFields'] = key === 'occurrenceMofExtension' ? Object.assign({}, value['dataFields']) : Object.assign({}, this.occurrenceMofDataFields);
                configuredData['occurrenceMofExtension']['dataLayout'] = key === 'occurrenceMofExtension' ? value['dataLayout'].slice() : this.occurrenceMofDataFieldsLayoutData.slice();
                configuredData['occurrenceMofExtension']['dataLabel'] = key === 'occurrenceMofExtension' ? value['dataLabel'] : this.occurrenceMofDataLabel;
            }
            const updateData = {
                configjson: configuredData
            };
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('collectionData', JSON.stringify(updateData));
            formData.append('action', 'updateCollectionRecord');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                if(res && Number(res) === 1){
                    this.setCollectionInfo(callback);
                }
                else {
                    callback(1);
                }
            });
        }
    }
});
