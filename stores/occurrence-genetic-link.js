const useOccurrenceGeneticLinkStore = Pinia.defineStore('occurrence-genetic-link', {
    state: () => ({
        blankGeneticLinkRecord: {
            idoccurgenetic: 0,
            occid: null,
            identifier: null,
            resourcename: null,
            title: null,
            locus: null,
            resourceurl: null,
            notes: null
        },
        geneticLinkArr: [],
        geneticLinkData: {},
        geneticLinkEditData: {},
        geneticLinkId: 0,
        geneticLinkUpdateData: {}
    }),
    getters: {
        getGeneticLinkArr(state) {
            return state.geneticLinkArr;
        },
        getGeneticLinkData(state) {
            return state.geneticLinkEditData;
        },
        getGeneticLinkEditsExist(state) {
            let exist = false;
            state.geneticLinkUpdateData = Object.assign({}, {});
            for(let key in state.geneticLinkEditData) {
                if(state.geneticLinkEditData.hasOwnProperty(key) && state.geneticLinkEditData[key] !== state.geneticLinkData[key]) {
                    exist = true;
                    state.geneticLinkUpdateData[key] = state.geneticLinkEditData[key];
                }
            }
            return exist;
        },
        getGeneticLinkID(state) {
            return state.geneticLinkId;
        },
        getGeneticLinkValid(state) {
            return (
                state.geneticLinkEditData['resourcename']
            );
        }
    },
    actions: {
        clearGeneticLinkArr() {
            this.geneticLinkArr.length = 0;
        },
        createOccurrenceGeneticLinkageRecord(collid, occid, callback) {
            this.geneticLinkEditData['occid'] = occid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('linkage', JSON.stringify(this.geneticLinkEditData));
            formData.append('action', 'createOccurrenceGeneticLinkageRecord');
            fetch(occurrenceGeneticLinkApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        deleteGeneticLinkageRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('idoccurgenetic', this.geneticLinkId.toString());
            formData.append('action', 'deleteGeneticLinkageRecord');
            fetch(occurrenceGeneticLinkApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        getCurrentGeneticLinkageData() {
            return this.geneticLinkArr.find(link => Number(link.idoccurgenetic) === this.geneticLinkId);
        },
        setCurrentGeneticLinkageRecord(linkid) {
            this.geneticLinkId = Number(linkid);
            if(this.geneticLinkId > 0){
                this.geneticLinkData = Object.assign({}, this.getCurrentGeneticLinkageData());
            }
            else{
                this.geneticLinkData = Object.assign({}, this.blankGeneticLinkRecord);
            }
            this.geneticLinkEditData = Object.assign({}, this.geneticLinkData);
        },
        setGeneticLinkArr(occid) {
            const formData = new FormData();
            formData.append('occid', occid.toString());
            formData.append('action', 'getOccurrenceGeneticLinkArr');
            fetch(occurrenceGeneticLinkApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.geneticLinkArr = data;
            });
        },
        updateGeneticLinkageEditData(key, value) {
            this.geneticLinkEditData[key] = value;
        },
        updateGeneticLinkageRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('idoccurgenetic', this.geneticLinkId.toString());
            formData.append('linkageData', JSON.stringify(this.geneticLinkUpdateData));
            formData.append('action', 'updateGeneticLinkageRecord');
            fetch(occurrenceGeneticLinkApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.geneticLinkData = Object.assign({}, this.geneticLinkEditData);
                    }
                });
            });
        }
    }
});
