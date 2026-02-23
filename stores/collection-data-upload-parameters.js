const useCollectionDataUploadParametersStore = Pinia.defineStore('collection-data-upload-parameters', {
    state: () => ({
        baseStore: useBaseStore(),
        blankCollectionDataUploadParameterRecord: {
            uspid: 0,
            collid: null,
            uploadtype: null,
            title: null,
            dwcpath: null,
            queryparamjson: {},
            cleansql: [],
            configjson: {}
        },
        blankConfigurations: {
            catalogNumberMatchField: 'catalognumber',
            existingRecords: 'update',
            existingDeterminationRecords: 'merge',
            existingMediaRecords: 'sync',
            existingMofRecords: 'update',
            cleanImageDerivatives: false,
            saveSourcePrimaryIdentifier: true,
            createPolygonCentroidCoordinates: false,
            matchOnRecordId: false,
            matchOnCatalogNumber: false,
            removeUnmatchedRecords: false,
            gbifPredicateJson: null,
            gbifDownloadKey: null,
            gbifDownloadKeyTimestamp: null,
            gbifDownloadPath: null
        },
        collectionDataUploadParametersArr: [],
        collectionDataUploadParametersData: {},
        collectionDataUploadParametersEditData: {},
        collectionDataUploadParametersId: null,
        collectionDataUploadParametersUpdateData: {},
        collectionId: 0,
        eventMofDataFields: {},
        occurrenceMofDataFields: {}
    }),
    getters: {
        getAdminEmail(state) {
            return state.baseStore.getAdminEmail;
        },
        getCleanSqlArr(state) {
            if(state.collectionDataUploadParametersEditData.hasOwnProperty('cleansql') && state.collectionDataUploadParametersEditData.cleansql && state.collectionDataUploadParametersEditData.cleansql.length > 0){
                return state.collectionDataUploadParametersEditData.cleansql;
            }
            else{
                return [];
            }
        },
        getCollectionDataUploadParametersArr(state) {
            return state.collectionDataUploadParametersArr;
        },
        getCollectionDataUploadParametersData(state) {
            return state.collectionDataUploadParametersEditData;
        },
        getCollectionDataUploadParametersEditsExist(state) {
            let exist = false;
            state.collectionDataUploadParametersUpdateData = Object.assign({}, {});
            for(let key in state.collectionDataUploadParametersEditData) {
                if(state.collectionDataUploadParametersEditData.hasOwnProperty(key) && state.collectionDataUploadParametersEditData[key] !== state.collectionDataUploadParametersData[key]) {
                    exist = true;
                    state.collectionDataUploadParametersUpdateData[key] = state.collectionDataUploadParametersEditData[key];
                }
            }
            if(this.getGbifPredicateChanged){
                state.collectionDataUploadParametersUpdateData['configjson']['gbifDownloadKey'] = null;
                state.collectionDataUploadParametersUpdateData['configjson']['gbifDownloadKeyTimestamp'] = null;
                state.collectionDataUploadParametersUpdateData['configjson']['gbifDownloadPath'] = null;
            }
            return exist;
        },
        getCollectionDataUploadParametersID(state) {
            return state.collectionDataUploadParametersId;
        },
        getCollectionDataUploadParametersValid(state) {
            return (
                state.collectionDataUploadParametersEditData['uploadtype'] && state.collectionDataUploadParametersEditData['title']
            );
        },
        getConfigurations(state) {
            if(state.collectionDataUploadParametersEditData.hasOwnProperty('configjson') && state.collectionDataUploadParametersEditData.configjson && Object.keys(state.collectionDataUploadParametersEditData.configjson).length > 0){
                return state.collectionDataUploadParametersEditData.configjson;
            }
            else{
                return state.blankConfigurations;
            }
        },
        getGbifCredentialsConfigured(state) {
            return state.baseStore.getGbifCredentialsConfigured;
        },
        getGbifPredicateChanged(state) {
            if(state.collectionDataUploadParametersData.hasOwnProperty('configjson') && state.collectionDataUploadParametersEditData.hasOwnProperty('configjson') && state.collectionDataUploadParametersData['configjson'].hasOwnProperty('gbifPredicateJson') && state.collectionDataUploadParametersEditData['configjson'].hasOwnProperty('gbifPredicateJson')){
                return state.collectionDataUploadParametersData['configjson']['gbifPredicateJson'] !== state.collectionDataUploadParametersEditData['configjson']['gbifPredicateJson'];
            }
            else{
                return false;
            }
        },
        getUploadTypeOptions() {
            const returnArr = [
                {value: 6, label: 'File Upload (DwC-A (zip), csv, txt, geojson, json)'},
                {value: 8, label: 'IPT/DwC-A Provider'},
                {value: 10, label: 'Symbiota Portal'}
            ];
            if(this.getAdminEmail && this.getGbifCredentialsConfigured){
                returnArr.push(
                    {value: 11, label: 'GBIF Data Upload'}
                );
            }
            return returnArr;
        }
    },
    actions: {
        cancelGbifDownloadRequest(callback) {
            const url = 'https://api.gbif.org/v1/occurrence/download/request/' + this.collectionDataUploadParametersEditData['configjson']['gbifDownloadKey'];
            fetch(url, {
                method: 'DELETE'
            })
            .then((response) => {
                if(response.status === 204){
                    this.clearGbifDownloadKey();
                    callback(1);
                }
                else{
                    callback(0);
                }
            });
        },
        clearGbifDownloadKey() {
            this.updateCollectionDataUploadParametersEditData('dwcpath', null);
            const config = Object.assign({}, this.collectionDataUploadParametersEditData['configjson']);
            config['gbifDownloadKey'] = null;
            config['gbifDownloadKeyTimestamp'] = null;
            config['gbifDownloadPath'] = null;
            this.updateCollectionDataUploadParametersEditData('configjson', config);
            if(this.getCollectionDataUploadParametersEditsExist){
                this.updateCollectionDataUploadParametersRecord();
            }
        },
        createCollectionDataUploadParametersRecord(collid, callback) {
            this.collectionDataUploadParametersEditData['collid'] = collid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('uploadParams', JSON.stringify(this.collectionDataUploadParametersEditData));
            formData.append('action', 'createCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(Number(res) > 0){
                    this.setCollectionDataUploadParametersArr(collid, Number(res));
                }
            });
        },
        deleteCollectionDataUploadParametersRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('uspid', this.collectionDataUploadParametersId.toString());
            formData.append('action', 'deleteCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((val) => {
                callback(Number(val));
                if(Number(val) === 1){
                    this.setCollectionDataUploadParametersArr(this.collectionId);
                    this.setCurrentCollectionDataUploadParametersRecord(0);
                }
            });
        },
        getCurrentCollectionDataUploadParametersData() {
            return this.collectionDataUploadParametersArr.find(params => Number(params.uspid) === this.collectionDataUploadParametersId);
        },
        getGbifDownloadKeyStatus() {
            const url = 'https://api.gbif.org/v1/occurrence/download/' + this.collectionDataUploadParametersEditData['configjson']['gbifDownloadKey'];
            fetch(url, {
                method: 'GET'
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((resObj) => {
                if(resObj && resObj.hasOwnProperty('status') && resObj['status'] === 'SUCCEEDED') {
                    this.saveGbifDownloadPath(resObj['downloadLink']);
                }
            });
        },
        saveGbifDownloadKey(key) {
            const config = Object.assign({}, this.collectionDataUploadParametersEditData['configjson']);
            config['gbifDownloadKey'] = key;
            config['gbifDownloadKeyTimestamp'] = Date.now();
            this.updateCollectionDataUploadParametersEditData('configjson', config);
            if(this.getCollectionDataUploadParametersEditsExist){
                this.updateCollectionDataUploadParametersRecord();
            }
        },
        saveGbifDownloadPath(path) {
            this.updateCollectionDataUploadParametersEditData('dwcpath', path);
            const config = Object.assign({}, this.collectionDataUploadParametersEditData['configjson']);
            config['gbifDownloadPath'] = path;
            this.updateCollectionDataUploadParametersEditData('configjson', config);
            if(this.getCollectionDataUploadParametersEditsExist){
                this.updateCollectionDataUploadParametersRecord();
            }
        },
        setCurrentCollectionDataUploadParametersRecord(uspid) {
            if(Number(uspid) > 0){
                this.collectionDataUploadParametersId = Number(uspid);
                this.collectionDataUploadParametersData = Object.assign({}, this.getCurrentCollectionDataUploadParametersData());
                this.collectionId = Number(this.collectionDataUploadParametersData['collid']);
            }
            else{
                this.collectionId = 0;
                this.collectionDataUploadParametersId = null;
                this.collectionDataUploadParametersData = Object.assign({}, this.blankCollectionDataUploadParameterRecord);
                this.collectionDataUploadParametersData['configjson'] = Object.assign({}, this.blankConfigurations);
            }
            this.collectionDataUploadParametersEditData = Object.assign({}, this.collectionDataUploadParametersData);
            if(Number(this.collectionDataUploadParametersData['uploadtype']) === 11 && this.collectionDataUploadParametersData['configjson'] && this.collectionDataUploadParametersData['configjson']['gbifDownloadKey']){
                this.validateGbifDownloadKey();
            }
        },
        setCollectionDataUploadParametersArr(collid, uspid = null) {
            this.collectionDataUploadParametersArr.length = 0;
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('action', 'getCollectionDataUploadParametersByCollection');
            fetch(collectionDataUploadParametersApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectionDataUploadParametersArr = data;
                if(uspid){
                    this.setCurrentCollectionDataUploadParametersRecord(uspid);
                }
            });
        },
        updateCollectionDataUploadParametersEditData(key, value) {
            this.collectionDataUploadParametersEditData[key] = value;
        },
        updateCollectionDataUploadParametersRecord(callback = null) {
            const formData = new FormData();
            formData.append('collid', this.collectionId.toString());
            formData.append('uspid', this.collectionDataUploadParametersId.toString());
            formData.append('paramsData', JSON.stringify(this.collectionDataUploadParametersUpdateData));
            formData.append('action', 'updateCollectionDataUploadParameterRecord');
            fetch(collectionDataUploadParametersApiUrl, {
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
                if(res && Number(res) === 1){
                    this.setCollectionDataUploadParametersArr(this.collectionId, this.collectionDataUploadParametersId);
                }
            });
        },
        validateGbifDownloadKey() {
            const currentDate = new Date();
            const dateSixMonthsAhead = new Date();
            dateSixMonthsAhead.setDate(currentDate.getDate() + 182);
            const downloadKeyDate = new Date(this.collectionDataUploadParametersData['configjson']['gbifDownloadKeyTimestamp']);
            if(downloadKeyDate > dateSixMonthsAhead){
                this.clearGbifDownloadKey();
            }
            else if(!this.collectionDataUploadParametersData['configjson']['gbifDownloadPath']){
                this.getGbifDownloadKeyStatus();
            }
        }
    }
});
