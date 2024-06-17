const useOccurrenceDeterminationStore = Pinia.defineStore('occurrence-determination', {
    state: () => ({
        blankDeterminationRecord: {
            detid: 0,
            identifiedby: null,
            dateidentified: null,
            sciname: null,
            verbatimscientificname: null,
            tid: null,
            scientificnameauthorship: null,
            identificationqualifier: null,
            iscurrent: 0,
            identificationreferences: null,
            identificationremarks: null,
            sortsequence: 10,
            printqueue: 0
        },
        determinationArr: [],
        determinationData: {},
        determinationEditData: {},
        determinationFields: {},
        determinationId: 0,
        determinationUpdateData: {}
    }),
    getters: {
        getDeterminationArr(state) {
            return state.determinationArr;
        },
        getDeterminationData(state) {
            return state.determinationEditData;
        },
        getDeterminationEditsExist(state) {
            let exist = false;
            state.determinationUpdateData = Object.assign({}, {});
            for(let key in state.determinationEditData) {
                if(state.determinationEditData.hasOwnProperty(key) && state.locationEditData[key] !== state.determinationData[key]) {
                    exist = true;
                    state.determinationUpdateData[key] = state.determinationEditData[key];
                }
            }
            return exist;
        },
        getDeterminationFields(state) {
            return state.determinationFields;
        },
        getDeterminationID(state) {
            return state.determinationId;
        },
        getDeterminationValid(state) {
            return (
                state.determinationEditData['sciname'] &&
                state.determinationEditData['identifiedby'] &&
                state.determinationEditData['dateidentified'] &&
                Number(state.determinationEditData['sortsequence']) > 0
            );
        }
    },
    actions: {
        clearDeterminationArr() {
            this.determinationArr.length = 0;
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
        getCurrentDeterminationData(detid) {
            return this.determinationArr.find(det => Number(det.detid) === Number(detid));
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
        setDeterminationArr(occid) {
            const formData = new FormData();
            formData.append('occid', occid.toString());
            formData.append('action', 'getOccurrenceDeterminationArr');
            fetch(occurrenceDeterminationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.determinationArr = data;
            });
        },
        updateDeterminationEditData(key, value) {
            this.determinationEditData[key] = value;
        },
    }
});
