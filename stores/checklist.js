const useChecklistStore = Pinia.defineStore('checklist', {
    state: () => ({
        blankChecklistRecord: {
            clid: 0,
            name: null,
            title: null,
            locality: null,
            publication: null,
            abstract: null,
            authors: null,
            type: null,
            politicaldivision: null,
            searchterms: null,
            parent: null,
            parentclid: null,
            notes: null,
            latcentroid: null,
            longcentroid: null,
            pointradiusmeters: null,
            footprintwkt: null,
            percenteffort: null,
            access: null,
            defaultsettings: null,
            iconurl: null,
            headerurl: null,
            uid: null,
            sortsequence: null,
            childChecklistArr: []
        },
        checklistData: {},
        checklistEditData: {},
        checklistId: 0,
        checklistTaxaArr: [],
        checklistUpdateData: {},
        checklistVoucherArr: []
    }),
    getters: {
        getBlankChecklistRecord(state) {
            return state.blankChecklistRecord;
        },
        getChecklistData(state) {
            return state.checklistEditData;
        },
        getChecklistEditsExist(state) {
            let exist = false;
            state.checklistUpdateData = Object.assign({}, {});
            for(let key in state.checklistEditData) {
                if(state.checklistEditData.hasOwnProperty(key) && state.checklistEditData[key] !== state.checklistData[key]) {
                    exist = true;
                    state.checklistUpdateData[key] = state.checklistEditData[key];
                }
            }
            return exist;
        },
        getChecklistID(state) {
            return state.checklistId;
        },
        getChecklistValid(state) {
            return !!state.checklistEditData['name'];
        }
    },
    actions: {
        clearImageArr() {
            this.imageArr.length = 0;
        },
        clearImageData() {
            this.imageData = Object.assign({}, this.blankImageRecord);
            this.imageEditData = Object.assign({}, {});
        },
        deleteImageRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('imgid', this.imageId.toString());
            formData.append('action', 'deleteImageRecord');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        deleteImageTag(collid, tag) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('imgid', this.imageId.toString());
            formData.append('tag', tag);
            formData.append('action', 'deleteImageTag');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            });
        },
        resetOccurrenceLinkage(collid, occidVal, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('imgid', this.imageId.toString());
            formData.append('imageData', JSON.stringify({occid: occidVal}));
            formData.append('action', 'updateImageRecord');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        setCurrentImageRecord(imgid) {
            this.imageId = Number(imgid);
            this.clearImageData();
            if(this.imageId > 0){
                this.setImageData();
            }
        },
        setImageArr(property, value) {
            const formData = new FormData();
            formData.append('property', property);
            formData.append('value', value.toString());
            formData.append('action', 'getImageArrByProperty');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.imageArr = data;
            });
        },
        setImageData() {
            const formData = new FormData();
            formData.append('imgid', this.imageId.toString());
            formData.append('action', 'getImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('imgid') && Number(data.imgid) > 0){
                    data.sciname = data['taxonData'] ? data['taxonData']['sciname'] : null;
                    this.imageData = Object.assign({}, data);
                    this.imageEditData = Object.assign({}, this.imageData);
                }
            });
        },
        updateImageEditData(key, value) {
            this.imageEditData[key] = value;
        },
        updateImageRecord(collid, callback) {
            if(this.imageUpdateData.hasOwnProperty('tagArr')){
                this.imageData['tagArr'].forEach(tag => {
                    if(!this.imageUpdateData['tagArr'].includes(tag)){
                        this.deleteImageTag(collid, tag);
                    }
                    else{
                        const index = this.imageUpdateData['tagArr'].indexOf(tag);
                        this.imageUpdateData['tagArr'].splice(index,1);
                    }
                });
            }
            if(!this.imageUpdateData.hasOwnProperty('tagArr') || this.imageUpdateData['tagArr'].length > 0 || Object.keys(this.imageUpdateData).length > 1){
                const formData = new FormData();
                formData.append('collid', collid.toString());
                formData.append('imgid', this.imageId.toString());
                formData.append('imageData', JSON.stringify(this.imageUpdateData));
                formData.append('action', 'updateImageRecord');
                fetch(imageApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        callback(Number(res));
                        if(res && Number(res) === 1){
                            this.imageData = Object.assign({}, this.imageEditData);
                        }
                    });
                });
            }
            else{
                callback(1);
            }
        }
    }
});
