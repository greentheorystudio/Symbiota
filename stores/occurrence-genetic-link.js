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
            notes: 10,
            initialtimestamp: 0
        },
        geneticLinkArr: [],
        geneticLinkData: {},
        geneticLinkEditData: {},
        geneticLinkFields: {},
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
        getGeneticLinkFields(state) {
            return state.geneticLinkFields;
        },
        getGeneticLinkID(state) {
            return state.geneticLinkId;
        },
        getGeneticLinkValid(state) {
            return (
                state.geneticLinkEditData['occid'] &&
                state.geneticLinkEditData['resourcename']
            );
        }
    },
    actions: {
        clearGeneticLinkArr() {
            this.geneticLinkArr.length = 0;
        },
        createOccurrenceDeterminationRecord(collid, occid, callback) {
            this.determinationEditData['occid'] = occid.toString();
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('determination', JSON.stringify(this.determinationEditData));
            formData.append('action', 'createOccurrenceDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                });
            });
        },
        deleteDeterminationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('action', 'deleteDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        getCurrentDeterminationData(detid) {
            return this.determinationArr.find(det => Number(det.detid) === Number(detid));
        },
        makeDeterminationCurrent(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('action', 'makeDeterminationCurrent');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    callback(Number(val));
                });
            });
        },
        setCurrentDeterminationRecord(detid) {
            if(Number(detid) > 0){
                this.determinationData = Object.assign({}, this.getCurrentDeterminationData(detid));
            }
            else{
                this.determinationData = Object.assign({}, this.blankDeterminationRecord);
            }
            this.determinationEditData = Object.assign({}, this.determinationData);
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
        updateDeterminationEditData(key, value) {
            this.determinationEditData[key] = value;
        },
        updateDeterminationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('detid', this.determinationId.toString());
            formData.append('determinationData', JSON.stringify(this.determinationUpdateData));
            formData.append('action', 'updateDeterminationRecord');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.determinationData = Object.assign({}, this.determinationEditData);
                    }
                });
            });
        }
    }
});
