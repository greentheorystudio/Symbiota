const useOccurrenceLocationStore = Pinia.defineStore('occurrence-location', {
    state: () => ({
        blankLocationRecord: {
            locationid: 0,
            collid: 0,
            locationname: null,
            locationcode: null,
            island: null,
            islandgroup: null,
            waterbody: null,
            continent: null,
            country: null,
            stateprovince: null,
            county: null,
            municipality: null,
            locality: null,
            localitysecurity: null,
            localitysecurityreason: null,
            decimallatitude: null,
            decimallongitude: null,
            geodeticdatum: null,
            coordinateuncertaintyinmeters: null,
            footprintwkt: null,
            coordinateprecision: null,
            locationremarks: null,
            verbatimcoordinates: null,
            verbatimcoordinatesystem: null,
            georeferencedby: null,
            georeferenceprotocol: null,
            georeferencesources: null,
            georeferenceverificationstatus: null,
            georeferenceremarks: null,
            minimumelevationinmeters: null,
            maximumelevationinmeters: null,
            verbatimelevation: null
        },
        locationData: {},
        locationEditData: {},
        locationFields: {},
        locationId: 0,
        locationUpdateData: {}
    }),
    getters: {
        getLocationData(state) {
            return state.locationEditData;
        },
        getLocationEditsExist(state) {
            let exist = false;
            state.locationUpdateData = Object.assign({}, {});
            for(let key in state.locationEditData) {
                if(state.locationEditData.hasOwnProperty(key) && state.locationEditData[key] !== state.locationData[key]) {
                    exist = true;
                    state.locationUpdateData[key] = state.locationEditData[key];
                }
            }
            return exist;
        },
        getLocationFields(state) {
            return state.locationFields;
        },
        getLocationID(state) {
            return state.locationId;
        },
        getLocationValid(state) {
            return (state.locationEditData['country'] && state.locationEditData['stateprovince']);
        }
    },
    actions: {
        clearLocationData() {
            this.locationId = 0;
            this.locationData = Object.assign({}, this.blankLocationRecord);
        },
        createLocationRecord(collid, callback) {
            this.locationEditData['collid'] = collid;
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('location', JSON.stringify(this.locationEditData));
            formData.append('action', 'createLocationRecord');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) > 0){
                        this.setCurrentLocationRecord(Number(res), collid);
                    }
                });
            });
        },
        getNearbyLocations(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('locationid', this.locationId.toString());
            formData.append('decimallatitude', this.locationEditData['decimallatitude'].toString());
            formData.append('decimallongitude', this.locationEditData['decimallongitude'].toString());
            formData.append('action', 'getNearbyLocationArr');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                callback(data);
            });
        },
        revertLocationEditData() {
            this.locationEditData = Object.assign({}, this.locationData);
        },
        searchLocations(collid, criteria, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('criteria', JSON.stringify(criteria));
            formData.append('action', 'searchLocations');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                callback(data);
            });
        },
        setCurrentLocationRecord(locationid, collid, callback = null) {
            if(locationid && Number(locationid) > 0){
                if(this.locationId !== Number(locationid)){
                    this.clearLocationData();
                    this.locationId = Number(locationid);
                    this.setLocationData(collid, callback);
                }
            }
            else{
                this.clearLocationData();
                this.locationEditData = Object.assign({}, this.locationData);
            }
        },
        setLocationData(collid, callback) {
            const formData = new FormData();
            formData.append('locationid', this.locationId.toString());
            formData.append('collid', collid.toString());
            formData.append('action', 'getLocationDataArr');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.locationData = Object.assign({}, data);
                this.locationEditData = Object.assign({}, this.locationData);
                if(callback){
                    callback();
                }
            });
        },
        setLocationFields() {
            const formData = new FormData();
            formData.append('action', 'getLocationFields');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.locationFields = Object.assign({}, data);
            });
        },
        updateLocationEditData(key, value) {
            this.locationEditData[key] = value;
        },
        updateLocationRecord(collid, callback) {
            const formData = new FormData();
            formData.append('collid', collid.toString());
            formData.append('locationid', this.locationId.toString());
            formData.append('locationData', JSON.stringify(this.locationUpdateData));
            formData.append('action', 'updateLocationRecord');
            fetch(occurrenceLocationApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.locationData = Object.assign({}, this.locationEditData);
                    }
                });
            });
        }
    }
});
