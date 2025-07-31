const useImageStore = Pinia.defineStore('image', {
    state: () => ({
        blankImageRecord: {
            imgid: 0,
            tid: null,
            sciname: null,
            url: null,
            thumbnailurl: null,
            originalurl: null,
            photographer: null,
            photographeruid: null,
            format: null,
            caption: null,
            owner: null,
            sourceurl: null,
            referenceurl: null,
            copyright: null,
            rights: null,
            locality: null,
            occid: null,
            notes: null,
            anatomy: null,
            username: null,
            sourceidentifier: null,
            mediamd5: null,
            dynamicproperties: null,
            sortsequence: null,
            tagArr: []
        },
        checklistImageData: {},
        imageArr: [],
        imageData: {},
        imageEditData: {},
        imageId: 0,
        imageTaxon: {},
        imageUpdateData: {}
    }),
    getters: {
        getBlankImageRecord(state) {
            return state.blankImageRecord;
        },
        getChecklistImageData(state) {
            return state.checklistImageData;
        },
        getImageArr(state) {
            return state.imageArr;
        },
        getImageCount(state) {
            return state.imageArr.length;
        },
        getImageData(state) {
            return state.imageEditData;
        },
        getImageEditsExist(state) {
            let exist = false;
            state.imageUpdateData = Object.assign({}, {});
            for(let key in state.imageEditData) {
                if(key === 'tagArr'){
                    if(state.imageEditData[key].length !== state.imageData[key].length){
                        exist = true;
                        state.imageUpdateData[key] = state.imageEditData[key];
                    }
                    else if(state.imageData[key].length > 0){
                        state.imageData[key].forEach(tag => {
                            if(!state.imageEditData[key].includes(tag)){
                                exist = true;
                                state.imageUpdateData[key] = state.imageEditData[key];
                            }
                        });
                    }
                }
                else{
                    if(state.imageEditData.hasOwnProperty(key) && state.imageEditData[key] !== state.imageData[key]) {
                        exist = true;
                        state.imageUpdateData[key] = state.imageEditData[key];
                    }
                }
            }
            return exist;
        },
        getImageID(state) {
            return state.imageId;
        },
        getImageTaxon(state) {
            return state.imageTaxon;
        },
        getImageValid(state) {
            return !!state.imageEditData['url'];
        }
    },
    actions: {
        clearChecklistImageData() {
            this.checklistImageData = Object.assign({}, {});
        },
        clearImageArr() {
            this.imageArr.length = 0;
        },
        clearImageData() {
            this.imageData = Object.assign({}, this.blankImageRecord);
            this.imageEditData = Object.assign({}, {});
            this.imageTaxon = Object.assign({}, {});
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
        setChecklistImageData(clidArr, numberPerTaxon) {
            this.clearChecklistImageData();
            const formData = new FormData();
            formData.append('clidArr', JSON.stringify(clidArr));
            formData.append('numberPerTaxon', numberPerTaxon.toString());
            formData.append('action', 'getChecklistImageData');
            fetch(imageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistImageData = Object.assign({}, data);
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
                    this.imageTaxon = Object.assign({}, data['taxonData']);
                    delete data['taxonData'];
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
