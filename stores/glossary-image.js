const useGlossaryImageStore = Pinia.defineStore('glossary-image', {
    state: () => ({
        blankGlossaryImageRecord: {
            glimgid: 0,
            glossid: null,
            url: null,
            structures: null,
            notes: null,
            createdby: null,
            uid: null
        },
        glossaryImageArr: [],
        glossaryImageData: {},
        glossaryImageEditData: {},
        glossaryImageId: 0,
        glossaryImageUpdateData: {}
    }),
    getters: {
        getGlossaryImageArr(state) {
            return state.glossaryImageArr;
        },
        getGlossaryImageData(state) {
            return state.glossaryImageEditData;
        },
        getGlossaryImageEditsExist(state) {
            let exist = false;
            state.glossaryImageUpdateData = Object.assign({}, {});
            for(let key in state.glossaryImageEditData) {
                if(state.glossaryImageEditData.hasOwnProperty(key) && state.glossaryImageEditData[key] !== state.glossaryImageData[key]) {
                    exist = true;
                    state.glossaryImageUpdateData[key] = state.glossaryImageEditData[key];
                }
            }
            return exist;
        },
        getGlossaryImageID(state) {
            return state.glossaryImageId;
        },
        getGlossaryImageValid(state) {
            return !!state.glossaryImageEditData['url'];
        }
    },
    actions: {
        clearGlossaryImageData() {
            this.glossaryImageArr.length = 0;
        },
        createGlossaryImageRecord(callback) {
            const formData = new FormData();
            formData.append('glossaryImage', JSON.stringify(this.glossaryImageEditData));
            formData.append('action', 'createGlossaryImageRecord');
            fetch(glossaryImageApiUrl, {
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
        deleteGlossaryImageRecord(callback) {
            const formData = new FormData();
            formData.append('glimgid', this.glossaryImageId.toString());
            formData.append('action', 'deleteGlossaryImageRecord');
            fetch(glossaryImageApiUrl, {
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
        getCurrentGlossaryImageData() {
            return this.glossaryImageArr.find(image => Number(image.glimgid) === this.glossaryImageId);
        },
        setCurrentGlossaryImageRecord(glimgid) {
            this.glossaryImageId = Number(glimgid);
            if(this.glossaryImageId > 0){
                this.glossaryImageData = Object.assign({}, this.getCurrentGlossaryImageData());
            }
            else{
                this.glossaryImageData = Object.assign({}, this.blankGlossaryImageRecord);
            }
            this.glossaryImageEditData = Object.assign({}, this.glossaryImageData);
        },
        setGlossaryImageArr(glossid) {
            const formData = new FormData();
            formData.append('glossIdArr', JSON.stringify([glossid]));
            formData.append('action', 'getGlossaryImageDataFromGlossidArr');
            fetch(glossaryImageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty(glossid)){
                    this.glossaryImageArr = data[glossid].slice();
                }
            });
        },
        updateGlossaryImageEditData(key, value) {
            this.glossaryImageEditData[key] = value;
        },
        updateGlossaryImageRecord(callback) {
            const formData = new FormData();
            formData.append('glimgid', this.glossaryImageId.toString());
            formData.append('glossaryImageData', JSON.stringify(this.glossaryImageUpdateData));
            formData.append('action', 'updateGlossaryImageRecord');
            fetch(glossaryImageApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                callback(Number(res));
                if(res && Number(res) === 1){
                    this.glossaryImageData = Object.assign({}, this.glossaryImageEditData);
                }
            });
        }
    }
});
