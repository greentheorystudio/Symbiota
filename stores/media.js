const useMediaStore = Pinia.defineStore('media', {
    state: () => ({
        baseStore: useBaseStore(),
        blankMediaRecord: {
            mediaid: 0,
            tid: null,
            sciname: null,
            occid: null,
            accessuri: null,
            sourceurl: null,
            descriptivetranscripturi: null,
            title: null,
            creatoruid: null,
            creator: null,
            type: null,
            format: null,
            owner: null,
            furtherinformationurl: null,
            language: null,
            usageterms: null,
            rights: null,
            bibliographiccitation: null,
            publisher: null,
            contributor: null,
            locationcreated: null,
            description: null,
            sortsequence: null
        },
        mediaArr: [],
        mediaData: {},
        mediaEditData: {},
        mediaId: 0,
        mediaTaxon: {},
        mediaUpdateData: {}
    }),
    getters: {
        getBlankMediaRecord(state) {
            return state.blankMediaRecord;
        },
        getMediaArr(state) {
            return state.mediaArr;
        },
        getMediaData(state) {
            return state.mediaEditData;
        },
        getMediaEditsExist(state) {
            let exist = false;
            state.mediaUpdateData = Object.assign({}, {});
            for(let key in state.mediaEditData) {
                if(state.mediaEditData.hasOwnProperty(key) && state.mediaEditData[key] !== state.mediaData[key]) {
                    exist = true;
                    state.mediaUpdateData[key] = state.mediaEditData[key];
                }
            }
            return exist;
        },
        getMediaID(state) {
            return state.mediaId;
        },
        getMediaTaxon(state) {
            return state.mediaTaxon;
        },
        getMediaValid(state) {
            return !!state.mediaEditData['accessuri'];
        }
    },
    actions: {
        clearMediaArr() {
            this.mediaArr.length = 0;
        },
        clearMediaData() {
            this.mediaData = Object.assign({}, this.blankMediaRecord);
            this.mediaEditData = Object.assign({}, {});
            this.mediaTaxon = Object.assign({}, {});
        },
        deleteMediaRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('mediaid', this.mediaId.toString());
            formData.append('action', 'deleteMediaRecord');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((val) => {
                callback(Number(val));
            });
        },
        deleteMediaTranscriptFile(collid, filepath, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('transcriptPath', filepath.toString());
            formData.append('action', 'deleteDescriptiveTranscriptFile');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((val) => {
                callback(Number(val));
            });
        },
        resetOccurrenceLinkage(collid, occidVal, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('mediaid', this.mediaId.toString());
            formData.append('mediaData', JSON.stringify({occid: occidVal}));
            formData.append('action', 'updateMediaRecord');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
            });
        },
        setCurrentMediaRecord(medid) {
            this.mediaId = Number(medid);
            this.clearMediaData();
            if(this.mediaId > 0){
                this.setMediaData();
            }
            else{
                this.mediaData = Object.assign({}, this.blankMediaRecord);
                this.mediaData['language'] = this.baseStore.getDefaultLanguageName;
                this.mediaEditData = Object.assign({}, this.mediaData);
            }
        },
        setMediaArr(property, value) {
            const formData = new FormData();
            formData.append('property', property);
            formData.append('value', value.toString());
            formData.append('action', 'getMediaArrByProperty');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.mediaArr = data;
            });
        },
        setMediaData() {
            const formData = new FormData();
            formData.append('mediaid', this.mediaId.toString());
            formData.append('action', 'getMediaData');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('mediaid') && Number(data.mediaid) > 0){
                    data.sciname = data['taxonData'] ? data['taxonData']['sciname'] : null;
                    this.mediaTaxon = Object.assign({}, data['taxonData']);
                    delete data['taxonData'];
                    this.mediaData = Object.assign({}, data);
                    this.mediaEditData = Object.assign({}, this.mediaData);
                }
            });
        },
        updateMediaEditData(key, value) {
            this.mediaEditData[key] = value;
        },
        updateMediaRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('mediaid', this.mediaId.toString());
            formData.append('mediaData', JSON.stringify(this.mediaUpdateData));
            formData.append('action', 'updateMediaRecord');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.mediaData = Object.assign({}, this.mediaEditData);
                }
            });
        },
        updateMediaSortSequence(collid, mediaid, sortsequenceVal, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('mediaid', mediaid.toString());
            formData.append('mediaData', JSON.stringify({sortsequence: sortsequenceVal}));
            formData.append('action', 'updateMediaRecord');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        uploadDescriptiveTranscriptFromFile(collid, file, uploadPath, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('transcriptfile', file);
            formData.append('uploadpath', uploadPath.toString());
            formData.append('action', 'uploadDescriptiveTranscriptFromFile');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res);
            });
        },
        uploadDescriptiveTranscriptFromUrl(collid, url, uploadPath, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('transcripturl', url.toString());
            formData.append('uploadpath', uploadPath.toString());
            formData.append('action', 'uploadDescriptiveTranscriptFromUrl');
            fetch(mediaApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(res);
            });
        }
    }
});
