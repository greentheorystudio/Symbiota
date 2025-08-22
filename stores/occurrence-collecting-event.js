const useOccurrenceCollectingEventStore = Pinia.defineStore('occurrence-collecting-event', {
    state: () => ({
        blankEventRecord: {
            eventid: 0,
            collid: 0,
            locationid: 0,
            eventtype: null,
            fieldnotes: null,
            fieldnumber: null,
            recordedby: null,
            recordnumber: null,
            recordedbyid: null,
            associatedcollectors: null,
            eventdate: null,
            latestdatecollected: null,
            eventtime: null,
            year: null,
            month: null,
            day: null,
            startdayofyear: null,
            enddayofyear: null,
            verbatimeventdate: null,
            habitat: null,
            substrate: null,
            localitysecurity: null,
            localitysecurityreason: null,
            decimallatitude: null,
            decimallongitude: null,
            geodeticdatum: null,
            coordinateuncertaintyinmeters: null,
            footprintwkt: null,
            eventremarks: null,
            georeferencedby: null,
            georeferenceprotocol: null,
            georeferencesources: null,
            georeferenceverificationstatus: null,
            georeferenceremarks: null,
            minimumdepthinmeters: null,
            maximumdepthinmeters: null,
            verbatimdepth: null,
            samplingprotocol: null,
            samplingeffort: null,
            repcount: null,
            labelproject: null
        },
        collectingEventBenthicData: {},
        collectingEventCollectionArr: [],
        collectingEventData: {},
        collectingEventEditData: {},
        collectingEventFields: {},
        collectingEventId: 0,
        collectingEventUpdateData: {},
        eventMofData: {},
        eventMofEditData: {},
        eventMofUpdateData: {},
        locationCollectingEventArr: []
    }),
    getters: {
        getBlankCollectingEventRecord(state) {
            return state.blankEventRecord;
        },
        getCollectingEventBenthicData(state) {
            return state.collectingEventBenthicData;
        },
        getCollectingEventBenthicTaxaCnt(state) {
            return Object.keys(state.collectingEventBenthicData).length;
        },
        getCollectingEventCollectionArr(state) {
            return state.collectingEventCollectionArr;
        },
        getCollectingEventData(state) {
            return state.collectingEventEditData;
        },
        getCollectingEventEditsExist(state) {
            let exist = false;
            state.collectingEventUpdateData = Object.assign({}, {});
            for(let key in state.collectingEventEditData) {
                if(state.collectingEventEditData.hasOwnProperty(key) && state.collectingEventEditData[key] !== state.collectingEventData[key]) {
                    exist = true;
                    state.collectingEventUpdateData[key] = state.collectingEventEditData[key];
                }
            }
            return exist;
        },
        getCollectingEventFields(state) {
            return state.collectingEventFields;
        },
        getCollectingEventID(state) {
            return state.collectingEventId;
        },
        getCollectingEventValid(state) {
            return (!!state.collectingEventEditData['eventdate']);
        },
        getEventMofData(state) {
            return state.eventMofEditData;
        },
        getEventMofEditData(state) {
            const editData = {
                add: [],
                delete: [],
                update: []
            };
            Object.keys(state.eventMofUpdateData).forEach((key) => {
                if(state.eventMofEditData[key] && !state.eventMofData[key]){
                    editData.add.push({field: key, value: state.eventMofUpdateData[key]});
                }
                else if(!state.eventMofEditData[key] && state.eventMofData[key]){
                    editData.delete.push(key);
                }
                else if(state.eventMofEditData[key] !== state.eventMofData[key]){
                    editData.update.push({field: key, value: state.eventMofUpdateData[key]});
                }
            });
            return editData;
        },
        getEventMofEditsExist(state) {
            let exist = false;
            state.eventMofUpdateData = Object.assign({}, {});
            for(let key in state.eventMofEditData) {
                if(state.eventMofEditData.hasOwnProperty(key) && state.eventMofEditData[key] !== state.eventMofData[key]) {
                    exist = true;
                    state.eventMofUpdateData[key] = state.eventMofEditData[key];
                }
            }
            return exist;
        },
        getEventRecordFields(state) {
            return Object.keys(state.blankEventRecord);
        },
        getLocationCollectingEventArr(state) {
            return state.locationCollectingEventArr;
        }
    },
    actions: {
        clearCollectingEventData() {
            this.eventMofData = Object.assign({}, {});
            this.eventMofEditData = Object.assign({}, {});
            this.collectingEventData = Object.assign({}, this.blankEventRecord);
            this.collectingEventBenthicData = Object.assign({}, {});
            this.collectingEventCollectionArr.length = 0;
        },
        clearLocationData() {
            this.locationCollectingEventArr.length = 0;
        },
        createCollectingEventRecord(collid, locationid, entryFormat, defaultRepCount, fields, callback, eventData = null) {
            if(!eventData){
                this.collectingEventEditData['collid'] = collid;
                if(locationid > 0){
                    this.collectingEventEditData['locationid'] = locationid;
                }
            }
            if(eventData && !eventData['repcount'] && Number(defaultRepCount) > 0){
                eventData['repcount'] = defaultRepCount;
            }
            const formData = new FormData();
            formData.append('collid', collid.toString());
            if(eventData){
                formData.append('event', JSON.stringify(eventData));
            }
            else{
                formData.append('event', JSON.stringify(this.collectingEventEditData));
            }
            formData.append('action', 'createCollectingEventRecord');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) > 0){
                        this.setCurrentCollectingEventRecord(Number(res), entryFormat, defaultRepCount, fields);
                        if(locationid > 0){
                            this.getLocationCollectingEvents(collid, locationid);
                        }
                    }
                });
            });
        },
        deleteCollectingEventRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('action', 'deleteCollectingEventRecord');
            fetch(occurrenceCollectingEventApiUrl, {
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
        getLocationCollectingEvents(collid, locationid) {
            this.locationCollectingEventArr.length = 0;
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('locationid', locationid.toString());
            formData.append('action', 'getLocationCollectingEventArr');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data && data.length > 0){
                    this.locationCollectingEventArr = this.locationCollectingEventArr.concat(data);
                }
            });
        },
        revertCollectingEventEditData() {
            this.collectingEventEditData = Object.assign({}, this.collectingEventData);
        },
        setCollectingEventBenthicData() {
            const formData = new FormData();
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('action', 'getCollectingEventBenthicData');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectingEventBenthicData = Object.assign({}, data);
            });
        },
        setCollectingEventCollectionsArr() {
            const formData = new FormData();
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('action', 'getCollectingEventCollectionsArr');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((dataArr) => {
                if(dataArr.length > 0){
                    this.collectingEventCollectionArr = this.collectingEventCollectionArr.concat(dataArr);
                }
            });
        },
        setCollectingEventData(entryFormat, fields, callback) {
            const formData = new FormData();
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('action', 'getCollectingEventDataArr');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectingEventData = Object.assign({}, data);
                this.collectingEventEditData = Object.assign({}, this.collectingEventData);
                this.setEventMofData(fields);
                if(entryFormat === 'benthic'){
                    this.setCollectingEventBenthicData();
                }
                else{
                    this.setCollectingEventCollectionsArr();
                }
                if(callback){
                    callback();
                }
            });
        },
        setCollectingEventFields() {
            const formData = new FormData();
            formData.append('action', 'getCollectingEventFields');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectingEventFields = Object.assign({}, data);
            });
        },
        setCurrentCollectingEventRecord(eventid, entryFormat, defaultRepCount, fields, callback = null) {
            if(eventid && Number(eventid) > 0){
                if(this.collectingEventId !== Number(eventid)){
                    this.collectingEventId = Number(eventid);
                    this.clearCollectingEventData();
                    this.setCollectingEventData(entryFormat, fields, callback);
                }
            }
            else{
                this.collectingEventId = 0;
                this.clearCollectingEventData();
                this.collectingEventData['repcount'] = defaultRepCount ? Number(defaultRepCount) : 0;
                this.collectingEventEditData = Object.assign({}, this.collectingEventData);
            }
        },
        setEventMofData(fields) {
            const formData = new FormData();
            formData.append('type', 'event');
            formData.append('id', this.collectingEventId.toString());
            formData.append('action', 'getMofDataByTypeAndId');
            fetch(occurrenceMeasurementOrFactApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                Object.keys(fields).forEach(field => {
                    this.eventMofData[field] = (data && data.hasOwnProperty(field)) ? data[field] : null;
                });
                this.eventMofEditData = Object.assign({}, this.eventMofData);
            });
        },
        updateCollectingEventEditData(key, value) {
            this.collectingEventEditData[key] = value;
        },
        updateCollectingEventLocation(collid, locationid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('locationid', locationid.toString());
            formData.append('action', 'updateCollectingEventLocation');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.collectingEventData['locationid'] = locationid;
                        this.collectingEventEditData['locationid'] = locationid;
                    }
                });
            });
        },
        updateCollectingEventRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('eventid', this.collectingEventId.toString());
            formData.append('eventData', JSON.stringify(this.collectingEventUpdateData));
            formData.append('action', 'updateCollectingEventRecord');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.collectingEventData = Object.assign({}, this.collectingEventEditData);
                    }
                });
            });
        },
        updateEventMofEditData(key, value) {
            this.eventMofEditData[key] = value;
        }
    }
});
