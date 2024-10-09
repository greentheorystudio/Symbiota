const useCollectionStore = Pinia.defineStore('collection', {
    state: () => ({
        collectionArr: [],
        collectionData: {},
        collectionId: 0,
        collectionPermissions: [],
        configuredDataDownloads: [],
        configuredDataFields: {},
        configuredDataFieldsLayoutData: {},
        configuredDataLabel: 'Additional Data'
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
            return state.collectionData;
        },
        getCollectionId(state) {
            return state.collectionId;
        },
        getCollectionPermissions(state) {
            return state.collectionPermissions;
        },
        getConfiguredDataDownloads(state) {
            return state.configuredDataDownloads;
        },
        getConfiguredDataFields(state) {
            return state.configuredDataFields;
        },
        getConfiguredDataFieldsLayoutData(state) {
            return state.configuredDataFieldsLayoutData;
        },
        getConfiguredDataLabel(state) {
            return state.configuredDataLabel;
        },
        getDatasetKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('datasetKey')) ? state.collectionData['aggkeysstr']['datasetKey'] : null);
        },
        getEndpointKey(state) {
            return ((state.collectionData.hasOwnProperty('aggkeysstr') && state.collectionData['aggkeysstr'].hasOwnProperty('endpointKey')) ? state.collectionData['aggkeysstr']['endpointKey'] : null);
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
        }
    },
    actions: {
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
                    response.text().then((res) => {
                        if(callback){
                            callback(Number(res));
                        }
                    });
                });
            }
        },
        clearCollectionData() {
            this.collectionId = 0;
            this.collectionData = Object.assign({}, {});
            this.collectionPermissions.length = 0;
            this.configuredDataDownloads.length = 0;
            this.configuredDataFields = Object.assign({}, {});
            this.configuredDataFieldsLayoutData = Object.assign({}, {});
            this.configuredDataLabel = 'Additional Data';
        },
        getCollectionListByUserRights(callback) {
            const formData = new FormData();
            formData.append('action', 'getCollectionListByUserRights');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    callback(resObj);
                });
            });
        },
        setCollection(collid, callback = null) {
            this.clearCollectionData();
            if(Number(collid) > 0){
                this.collectionId = Number(collid);
                const formData = new FormData();
                formData.append('permissionJson', JSON.stringify(["CollAdmin", "CollEditor"]));
                formData.append('key', collid.toString());
                formData.append('action', 'validatePermission');
                fetch(permissionApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.json().then((resData) => {
                        this.collectionPermissions = resData;
                        this.setCollectionInfo(callback);
                    });
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
                response.json().then((resData) => {
                    this.collectionArr = resData;
                });
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
                response.json().then((resObj) => {
                    if(Number(resObj['ispublic']) === 1 || (this.collectionPermissions.includes('CollAdmin') || this.collectionPermissions.includes('CollEditor'))){
                        this.collectionData = Object.assign({}, resObj);
                        if(this.collectionData['configuredData'] && this.collectionData['configuredData'].hasOwnProperty('dataFields') && Object.keys(this.collectionData['configuredData']['dataFields']).length > 0){
                            if(this.collectionData['configuredData'].hasOwnProperty('dataFields') && this.collectionData['configuredData']['dataFields']){
                                this.configuredDataFields = this.collectionData['configuredData']['dataFields'];
                                if(this.collectionData['configuredData'].hasOwnProperty('dataLayout') && this.collectionData['configuredData']['dataLayout']){
                                    this.configuredDataFieldsLayoutData = this.collectionData['configuredData']['dataLayout'];
                                }
                                if(this.collectionData['configuredData'].hasOwnProperty('dataLabel') && this.collectionData['configuredData']['dataLabel']){
                                    this.configuredDataLabel = this.collectionData['configuredData']['dataLabel'].toString();
                                }
                                if(this.collectionData['configuredData'].hasOwnProperty('dataDownloads') && this.collectionData['configuredData']['dataDownloads']){
                                    this.configuredDataDownloads = this.collectionData['configuredData']['dataDownloads'];
                                }
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
            });
        },
        updateCollectionStatistics(collidStr, callback = null) {
            if(collidStr){
                const formData = new FormData();
                formData.append('collidStr', collidStr.toString());
                formData.append('action', 'updateCollectionStatistics');
                fetch(collectionApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        if(callback){
                            callback(Number(res));
                            this.setCollectionInfo();
                        }
                    });
                });
            }
        }
    }
});
