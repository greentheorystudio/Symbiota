const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        additionalData: {},
        checklistArr: [],
        collectingEventData: {},
        collectionData: {},
        crowdSourceQueryFieldOptions: [
            {field: 'family', label: 'Family'},
            {field: 'sciname', label: 'Scientific Name'},
            {field: 'othercatalognumbers', label: 'Other Catalog Numbers'},
            {field: 'country', label: 'Country'},
            {field: 'stateProvince', label: 'State/Province'},
            {field: 'county', label: 'County'},
            {field: 'municipality', label: 'Municipality'},
            {field: 'recordedby', label: 'Collector'},
            {field: 'recordnumber', label: 'Collector Number'},
            {field: 'eventdate', label: 'Collection Date'}
        ],
        currentCollid: 0,
        currentOccid: 0,
        determinationArr: [],
        duplicateArr: [],
        editorQueryFieldOptions: [
            {field: 'associatedCollectors', label: 'Associated Collectors'},
            {field: 'associatedOccurrences', label: 'Associated Occurrences'},
            {field: 'associatedTaxa', label: 'Associated Taxa'},
            {field: 'attributes', label: 'Attributes'},
            {field: 'scientificNameAuthorship', label: 'Author'},
            {field: 'basisOfRecord', label: 'Basis Of Record'},
            {field: 'behavior', label: 'Behavior'},
            {field: 'catalogNumber', label: 'Catalog Number'},
            {field: 'collectionCode', label: 'Collection Code (override)'},
            {field: 'recordNumber', label: 'Collection Number'},
            {field: 'recordedBy', label: 'Collector/Observer'},
            {field: 'coordinateUncertaintyInMeters', label: 'Coordinate Uncertainty (m)'},
            {field: 'country', label: 'Country'},
            {field: 'county', label: 'County'},
            {field: 'cultivationStatus', label: 'Cultivation Status'},
            {field: 'dataGeneralizations', label: 'Data Generalizations'},
            {field: 'eventDate', label: 'Date'},
            {field: 'dateEntered', label: 'Date Entered'},
            {field: 'dateLastModified', label: 'Date Last Modified'},
            {field: '`day`', label: 'Day'},
            {field: 'dbpk', label: 'dbpk'},
            {field: 'decimalLatitude', label: 'Decimal Latitude'},
            {field: 'decimalLongitude', label: 'Decimal Longitude'},
            {field: 'maximumDepthInMeters', label: 'Depth Maximum (m)'},
            {field: 'minimumDepthInMeters', label: 'Depth Minimum (m)'},
            {field: 'verbatimAttributes', label: 'Description'},
            {field: 'disposition', label: 'Disposition'},
            {field: 'dynamicProperties', label: 'Dynamic Properties'},
            {field: 'maximumElevationInMeters', label: 'Elevation Maximum (m)'},
            {field: 'minimumElevationInMeters', label: 'Elevation Minimum (m)'},
            {field: 'establishmentMeans', label: 'Establishment Means'},
            {field: 'family', label: 'Family'},
            {field: 'fieldNotes', label: 'Field Notes'},
            {field: 'fieldnumber', label: 'Field Number'},
            {field: 'genus', label: 'Genus'},
            {field: 'geodeticDatum', label: 'Geodetic Datum'},
            {field: 'georeferenceProtocol', label: 'Georeference Protocol'},
            {field: 'georeferenceRemarks', label: 'Georeference Remarks'},
            {field: 'georeferenceSources', label: 'Georeference Sources'},
            {field: 'georeferenceVerificationStatus', label: 'Georeference Verification Status'},
            {field: 'georeferencedBy', label: 'Georeferenced By'},
            {field: 'habitat', label: 'Habitat'},
            {field: 'identificationQualifier', label: 'Identification Qualifier'},
            {field: 'identificationReferences', label: 'Identification References'},
            {field: 'identificationRemarks', label: 'Identification Remarks'},
            {field: 'identifiedBy', label: 'Identified By'},
            {field: 'individualCount', label: 'Individual Count'},
            {field: 'informationWithheld', label: 'Information Withheld'},
            {field: 'institutionCode', label: 'Institution Code (override)'},
            {field: 'labelProject', label: 'Label Project'},
            {field: 'lifeStage', label: 'Life Stage'},
            {field: 'locality', label: 'Locality'},
            {field: 'localitySecurity', label: 'Locality Security'},
            {field: 'localitySecurityReason', label: 'Locality Security Reason'},
            {field: 'locationRemarks', label: 'Location Remarks'},
            {field: 'username', label: 'Modified By'},
            {field: '`month`', label: 'Month'},
            {field: 'municipality', label: 'Municipality'},
            {field: 'occurrenceRemarks', label: 'Notes (Occurrence Remarks)'},
            {field: 'otherCatalogNumbers', label: 'Other Catalog Numbers'},
            {field: 'ownerInstitutionCode', label: 'Owner Code'},
            {field: 'preparations', label: 'Preparations'},
            {field: 'reproductiveCondition', label: 'Reproductive Condition'},
            {field: 'samplingEffort', label: 'Sampling Effort'},
            {field: 'samplingProtocol', label: 'Sampling Protocol'},
            {field: 'sciname', label: 'Scientific Name'},
            {field: 'sex', label: 'Sex'},
            {field: 'specificEpithet', label: 'Specific Epithet'},
            {field: 'stateProvince', label: 'State/Province'},
            {field: 'substrate', label: 'Substrate'},
            {field: 'tid', label: 'Taxon ID'},
            {field: 'taxonRemarks', label: 'Taxon Remarks'},
            {field: 'typeStatus', label: 'Type Status'},
            {field: 'verbatimCoordinates', label: 'Verbatim Coordinates'},
            {field: 'verbatimEventDate', label: 'Verbatim Date'},
            {field: 'verbatimDepth', label: 'Verbatim Depth'},
            {field: 'verbatimElevation', label: 'Verbatim Elevation'},
            {field: '`year`', label: 'Year'}
        ],
        geneticLinkArr: [],
        imageArr: [],
        isEditor: false,
        isLocked: false,
        locationData: {},
        mediaArr: [],
        occidArr: [],
        occurrenceData: {}
    }),
    getters: {
        getAdditionalData(state) {
            return state.additionalData;
        },
        getChecklistArr(state) {
            return state.checklistArr;
        },
        getClientRoot() {
            const store = useBaseStore();
            return store.getClientRoot;
        },
        getCollectingEventData(state) {
            return state.collectingEventData;
        },
        getCollectionData(state) {
            return state.collectionData;
        },
        getCrowdSourceQueryFieldOptions(state) {
            return state.crowdSourceQueryFieldOptions;
        },
        getCurrentRecordIndex(state) {
            return state.occidArr.length > 0 ? (state.occidArr.indexOf(state.currentOccid) + 1) : 1;
        },
        getDeterminationArr(state) {
            return state.determinationArr;
        },
        getDuplicateArr(state) {
            return state.duplicateArr;
        },
        getEditorQueryFieldOptions(state) {
            return state.editorQueryFieldOptions;
        },
        getFirstRecord(state) {
            return state.occidArr[0];
        },
        getGeneticLinkArr(state) {
            return state.geneticLinkArr;
        },
        getImageArr(state) {
            return state.imageArr;
        },
        getImageCount(state) {
            return state.imageArr.length;
        },
        getIsEditor(state) {
            return state.isEditor;
        },
        getIsLocked(state) {
            return state.isLocked;
        },
        getLastRecord(state) {
            return state.occidArr[(state.occidArr.length - 1)];
        },
        getLocationData(state) {
            return state.locationData;
        },
        getMediaArr(state) {
            return state.mediaArr;
        },
        getNextRecord(state) {
            return state.occidArr[this.getCurrentRecordIndex];
        },
        getPreviousRecord(state) {
            return state.occidArr[(this.getCurrentRecordIndex - 2)];
        },
        getRecordCount(state) {
            return state.occidArr.length > 0 ? state.occidArr.length : 1;
        },
        getOccurrenceData(state) {
            return state.occurrenceData;
        }
    },
    actions: {
        clearCollectionData() {
            this.isEditor = false;
            this.collectionData = Object.assign({}, {});
        },
        clearOccurrenceData() {
            this.occurrenceData = Object.assign({}, {});
            this.isLocked = false;
            this.locationData = Object.assign({}, {});
            this.collectingEventData = Object.assign({}, {});
            this.determinationArr.length = 0;
            this.imageArr.length = 0;
            this.mediaArr.length = 0;
            this.checklistArr.length = 0;
            this.duplicateArr.length = 0;
            this.geneticLinkArr.length = 0;
            this.additionalData = Object.assign({}, {});
        },
        setAdditionalData() {
            const formData = new FormData();
            formData.append('eventid', this.occurrenceData['eventID'].toString());
            formData.append('action', 'getAdditionalDataArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.additionalData = Object.assign({}, data);
            });
        },
        setChecklistArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceChecklistArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.checklistArr = data;
            });
        },
        setCollection(collid) {
            if(Number(this.currentCollid) !== Number(collid)){
                this.clearCollectionData();
                const formData = new FormData();
                formData.append('permission[]', '["CollAdmin","CollEditor"]');
                formData.append('key', collid.toString());
                formData.append('action', 'validatePermission');
                fetch(profileApiUrl, {
                    method: 'POST',
                    body: formData
                })
                .then((response) => {
                    response.text().then((res) => {
                        this.isEditor = Number(res) === 1;
                        if(this.isEditor){
                            this.currentCollid = collid;
                            this.setCollectionInfo();
                        }
                        else{
                            window.location.href = this.getClientRoot + '/index.php';
                        }
                    });
                });
            }
        },
        setCollectionEventData() {
            const formData = new FormData();
            formData.append('eventid', this.occurrenceData['eventID'].toString());
            formData.append('action', 'getCollectionEventDataArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.collectingEventData = Object.assign({}, data);
            });
        },
        setCollectionInfo() {
            const formData = new FormData();
            formData.append('collid', this.currentCollid.toString());
            formData.append('action', 'getCollectionInfoArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    this.collectionData = Object.assign({}, resObj);
                });
            });
        },
        setDeterminationArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceDeterminationArr');
            fetch(occurrenceApiUrl, {
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
        setDuplicateArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceDuplicateArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.duplicateArr = data;
            });
        },
        setGeneticLinkArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceGeneticLinkArr');
            fetch(occurrenceApiUrl, {
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
        setImageArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceImageArr');
            fetch(occurrenceApiUrl, {
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
        setLocationData() {
            const formData = new FormData();
            formData.append('locationid', this.occurrenceData['locationID'].toString());
            formData.append('action', 'getLocationDataArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.locationData = Object.assign({}, data);
            });
        },
        setMediaArr() {
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceMediaArr');
            fetch(occurrenceApiUrl, {
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
        setOccurrenceData(occid) {
            this.currentOccid = occid;
            this.clearOccurrenceData();
            const formData = new FormData();
            formData.append('occid', this.currentOccid.toString());
            formData.append('action', 'getOccurrenceDataLock');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.text() : null;
            })
            .then((res) => {
                this.isLocked = Number(res) === 1;
                if(!this.isLocked){
                    const formData = new FormData();
                    formData.append('occid', this.currentOccid.toString());
                    formData.append('action', 'getOccurrenceDataArr');
                    fetch(occurrenceApiUrl, {
                        method: 'POST',
                        body: formData
                    })
                    .then((response) => {
                        return response.ok ? response.json() : null;
                    })
                    .then((data) => {
                        this.occurrenceData = Object.assign({}, data);
                        this.setCollection(this.occurrenceData.collid);
                        this.setDeterminationArr();
                        this.setImageArr();
                        this.setMediaArr();
                        this.setChecklistArr();
                        this.setDuplicateArr();
                        this.setGeneticLinkArr();
                        if(this.occurrenceData['locationID'] && Number(this.occurrenceData['locationID']) > 0){
                            this.setLocationData();
                        }
                        if(this.occurrenceData['eventID'] && Number(this.occurrenceData['eventID']) > 0){
                            this.setCollectionEventData();
                            this.setAdditionalData();
                        }
                    });
                }
            });
        }
    }
});
