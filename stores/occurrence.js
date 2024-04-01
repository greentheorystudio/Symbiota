const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        additionalData: {},
        additionalDataFields: [],
        blankEventRecord: {
            locationID: 0,
            eventType: null,
            fieldNotes: null,
            fieldnumber: null,
            recordedBy: null,
            recordNumber: null,
            recordedbyid: null,
            associatedCollectors: null,
            eventDate: null,
            latestDateCollected: null,
            eventTime: null,
            year: null,
            month: null,
            day: null,
            startDayOfYear: null,
            endDayOfYear: null,
            verbatimEventDate: null,
            habitat: null,
            substrate: null,
            localitySecurity: null,
            localitySecurityReason: null,
            decimalLatitude: null,
            decimalLongitude: null,
            geodeticDatum: null,
            coordinateUncertaintyInMeters: null,
            footprintWKT: null,
            eventRemarks: null,
            georeferencedBy: null,
            georeferenceProtocol: null,
            georeferenceSources: null,
            georeferenceVerificationStatus: null,
            georeferenceRemarks: null,
            minimumDepthInMeters: null,
            maximumDepthInMeters: null,
            verbatimDepth: null,
            samplingProtocol: null,
            samplingEffort: null,
            repCount: null,
            labelProject: null
        },
        blankLocationRecord: {
            collid: 0,
            locationName: null,
            locationCode: null,
            waterBody: null,
            country: null,
            stateProvince: null,
            county: null,
            municipality: null,
            locality: null,
            localitySecurity: null,
            localitySecurityReason: null,
            decimalLatitude: null,
            decimalLongitude: null,
            geodeticDatum: null,
            coordinateUncertaintyInMeters: null,
            footprintWKT: null,
            coordinatePrecision: null,
            locationRemarks: null,
            verbatimCoordinates: null,
            verbatimCoordinateSystem: null,
            georeferencedBy: null,
            georeferenceProtocol: null,
            georeferenceSources: null,
            georeferenceVerificationStatus: null,
            georeferenceRemarks: null,
            minimumElevationInMeters: null,
            maximumElevationInMeters: null,
            verbatimElevation: null
        },
        blankOccurrenceRecord: {
            occid: 0,
            collid: 0,
            dbpk: null,
            basisOfRecord: null,
            occurrenceID: null,
            catalogNumber: null,
            otherCatalogNumbers: null,
            ownerInstitutionCode: null,
            institutionID: null,
            collectionID: null,
            datasetID: null,
            institutionCode: null,
            collectionCode: null,
            family: null,
            verbatimScientificName: null,
            sciname: null,
            tid: null,
            genus: null,
            specificEpithet: null,
            taxonRank: null,
            infraspecificEpithet: null,
            scientificNameAuthorship: null,
            taxonRemarks: null,
            identifiedBy: null,
            dateIdentified: null,
            identificationReferences: null,
            identificationRemarks: null,
            identificationQualifier: null,
            typeStatus: null,
            recordedBy: null,
            recordNumber: null,
            recordedbyid: null,
            associatedCollectors: null,
            eventDate: null,
            latestDateCollected: null,
            eventTime: null,
            year: null,
            month: null,
            day: null,
            startDayOfYear: null,
            endDayOfYear: null,
            verbatimEventDate: null,
            habitat: null,
            substrate: null,
            fieldNotes: null,
            fieldnumber: null,
            eventID: null,
            eventRemarks: null,
            occurrenceRemarks: null,
            informationWithheld: null,
            dataGeneralizations: null,
            associatedOccurrences: null,
            associatedTaxa: null,
            dynamicProperties: null,
            verbatimAttributes: null,
            behavior: null,
            reproductiveCondition: null,
            cultivationStatus: null,
            establishmentMeans: null,
            lifeStage: null,
            sex: null,
            individualCount: null,
            samplingProtocol: null,
            samplingEffort: null,
            rep: null,
            preparations: null,
            locationID: null,
            waterBody: null,
            country: null,
            stateProvince: null,
            county: null,
            municipality: null,
            locality: null,
            localitySecurity: null,
            localitySecurityReason: null,
            decimalLatitude: null,
            decimalLongitude: null,
            geodeticDatum: null,
            coordinateUncertaintyInMeters: null,
            footprintWKT: null,
            coordinatePrecision: null,
            locationRemarks: null,
            verbatimCoordinates: null,
            verbatimCoordinateSystem: null,
            georeferencedBy: null,
            georeferenceProtocol: null,
            georeferenceSources: null,
            georeferenceVerificationStatus: null,
            georeferenceRemarks: null,
            minimumElevationInMeters: null,
            maximumElevationInMeters: null,
            verbatimElevation: null,
            minimumDepthInMeters: null,
            maximumDepthInMeters: null,
            verbatimDepth: null,
            previousIdentifications: null,
            disposition: null,
            storageLocation: null,
            language: null,
            observeruid: null,
            processingstatus: null,
            duplicateQuantity: null,
            labelProject: null
        },
        checklistArr: [],
        collectingEventData: {},
        collectionData: {},
        collId: 0,
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
        determinationArr: [],
        displayMode: 1,
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
        eventData: {},
        eventId: 0,
        geneticLinkArr: [],
        imageArr: [],
        isEditor: false,
        isLocked: false,
        locationData: {},
        locationId: 0,
        mediaArr: [],
        occId: 0,
        occidArr: [],
        occurrenceData: {},
        occurrenceEntryFormat: 'specimen'
    }),
    getters: {
        getAdditionalData(state) {
            return state.additionalData;
        },
        getAdditionalDataFields(state) {
            return state.additionalDataFields;
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
        getCollId(state) {
            return state.collId;
        },
        getCrowdSourceQueryFieldOptions(state) {
            return state.crowdSourceQueryFieldOptions;
        },
        getCurrentRecordIndex(state) {
            return (state.occidArr.indexOf(state.occId) + 1);
        },
        getDeterminationArr(state) {
            return state.determinationArr;
        },
        getDisplayMode(state) {
            return state.displayMode;
        },
        getDuplicateArr(state) {
            return state.duplicateArr;
        },
        getEditorQueryFieldOptions(state) {
            return state.editorQueryFieldOptions;
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
        getLocationData(state) {
            return state.locationData;
        },
        getMediaArr(state) {
            return state.mediaArr;
        },
        getRecordCount(state) {
            return state.occidArr.length;
        },
        getOccId(state) {
            return state.occId;
        },
        getOccurrenceData(state) {
            return state.occurrenceData;
        },
        getOccurrenceEntryFormat(state) {
            return state.occurrenceEntryFormat;
        }
    },
    actions: {
        clearCollectionData() {
            this.isEditor = false;
            this.collectionData = Object.assign({}, {});
            this.additionalDataFields.length = 0;
        },
        clearOccurrenceData() {
            this.occurrenceData = Object.assign({}, this.blankOccurrenceRecord);
            this.isLocked = false;
            this.determinationArr.length = 0;
            this.imageArr.length = 0;
            this.mediaArr.length = 0;
            this.checklistArr.length = 0;
            this.duplicateArr.length = 0;
            this.geneticLinkArr.length = 0;
        },
        goToFirstRecord() {
            this.setOccurrenceData(this.occidArr[0]);
        },
        goToLastRecord() {
            this.setOccurrenceData(this.occidArr[(this.occidArr.length - 1)]);
        },
        goToNextRecord() {
            this.setOccurrenceData(this.occidArr[this.getCurrentRecordIndex]);
        },
        goToNewOccurrenceRecord(carryLocation = false, carryEvent = false) {
            this.setOccurrenceData(0);
            if(carryLocation){
                this.mergeLocationOccurrenceData();
            }
            else{
                this.locationId = 0;
                this.locationData = Object.assign({}, this.blankLocationRecord);
            }
            if(carryEvent){
                this.mergeEventOccurrenceData();
            }
            else{
                this.eventId = 0;
                this.eventData = Object.assign({}, this.blankEventRecord);
            }
        },
        goToPreviousRecord() {
            this.setOccurrenceData(this.occidArr[(this.getCurrentRecordIndex - 2)]);
        },
        mergeEventOccurrenceData() {
            this.occurrenceData['eventID'] = this.eventId;
            this.occurrenceData['fieldNotes'] = this.eventData['fieldNotes'];
            this.occurrenceData['fieldnumber'] = this.eventData['fieldnumber'];
            this.occurrenceData['recordedBy'] = this.eventData['recordedBy'];
            this.occurrenceData['recordNumber'] = this.eventData['recordNumber'];
            this.occurrenceData['recordedbyid'] = this.eventData['recordedbyid'];
            this.occurrenceData['associatedCollectors'] = this.eventData['associatedCollectors'];
            this.occurrenceData['eventDate'] = this.eventData['eventDate'];
            this.occurrenceData['latestDateCollected'] = this.eventData['latestDateCollected'];
            this.occurrenceData['eventTime'] = this.eventData['eventTime'];
            this.occurrenceData['year'] = this.eventData['year'];
            this.occurrenceData['month'] = this.eventData['month'];
            this.occurrenceData['day'] = this.eventData['day'];
            this.occurrenceData['startDayOfYear'] = this.eventData['startDayOfYear'];
            this.occurrenceData['endDayOfYear'] = this.eventData['endDayOfYear'];
            this.occurrenceData['verbatimEventDate'] = this.eventData['verbatimEventDate'];
            this.occurrenceData['habitat'] = this.eventData['habitat'];
            this.occurrenceData['substrate'] = this.eventData['substrate'];
            if(Number(this.occurrenceData['localitySecurity']) !== 1 && Number(this.eventData['localitySecurity']) === 1){
                this.occurrenceData['localitySecurity'] = this.eventData['localitySecurity'];
                this.occurrenceData['localitySecurityReason'] = this.eventData['localitySecurityReason'];
            }
            if(this.eventData['decimalLatitude']){
                this.occurrenceData['decimalLatitude'] = this.eventData['decimalLatitude'];
            }
            if(this.eventData['decimalLongitude']){
                this.occurrenceData['decimalLongitude'] = this.eventData['decimalLongitude'];
            }
            if(this.eventData['geodeticDatum']){
                this.occurrenceData['geodeticDatum'] = this.eventData['geodeticDatum'];
            }
            if(this.eventData['coordinateUncertaintyInMeters']){
                this.occurrenceData['coordinateUncertaintyInMeters'] = this.eventData['coordinateUncertaintyInMeters'];
            }
            if(this.eventData['footprintWKT']){
                this.occurrenceData['footprintWKT'] = this.eventData['footprintWKT'];
            }
            if(this.eventData['georeferencedBy']){
                this.occurrenceData['georeferencedBy'] = this.eventData['georeferencedBy'];
            }
            if(this.eventData['georeferenceProtocol']){
                this.occurrenceData['georeferenceProtocol'] = this.eventData['georeferenceProtocol'];
            }
            if(this.eventData['georeferenceSources']){
                this.occurrenceData['georeferenceSources'] = this.eventData['georeferenceSources'];
            }
            if(this.eventData['georeferenceVerificationStatus']){
                this.occurrenceData['georeferenceVerificationStatus'] = this.eventData['georeferenceVerificationStatus'];
            }
            if(this.eventData['georeferenceRemarks']){
                this.occurrenceData['georeferenceRemarks'] = this.eventData['georeferenceRemarks'];
            }
            this.occurrenceData['minimumDepthInMeters'] = this.eventData['minimumDepthInMeters'];
            this.occurrenceData['maximumDepthInMeters'] = this.eventData['maximumDepthInMeters'];
            this.occurrenceData['verbatimDepth'] = this.eventData['verbatimDepth'];
            this.occurrenceData['samplingProtocol'] = this.eventData['samplingProtocol'];
            this.occurrenceData['samplingEffort'] = this.eventData['samplingEffort'];
            this.occurrenceData['labelProject'] = this.eventData['labelProject'];
        },
        mergeLocationOccurrenceData() {
            this.occurrenceData['locationID'] = this.locationId;
            this.occurrenceData['waterBody'] = this.locationData['waterBody'];
            this.occurrenceData['country'] = this.locationData['country'];
            this.occurrenceData['stateProvince'] = this.locationData['stateProvince'];
            this.occurrenceData['county'] = this.locationData['county'];
            this.occurrenceData['municipality'] = this.locationData['municipality'];
            this.occurrenceData['locality'] = this.locationData['locality'];
            this.occurrenceData['localitySecurity'] = this.locationData['localitySecurity'];
            this.occurrenceData['localitySecurityReason'] = this.locationData['localitySecurityReason'];
            this.occurrenceData['decimalLatitude'] = this.locationData['decimalLatitude'];
            this.occurrenceData['decimalLongitude'] = this.locationData['decimalLongitude'];
            this.occurrenceData['geodeticDatum'] = this.locationData['geodeticDatum'];
            this.occurrenceData['coordinateUncertaintyInMeters'] = this.locationData['coordinateUncertaintyInMeters'];
            this.occurrenceData['footprintWKT'] = this.locationData['footprintWKT'];
            this.occurrenceData['coordinatePrecision'] = this.locationData['coordinatePrecision'];
            this.occurrenceData['locationRemarks'] = this.locationData['locationRemarks'];
            this.occurrenceData['verbatimCoordinates'] = this.locationData['verbatimCoordinates'];
            this.occurrenceData['verbatimCoordinateSystem'] = this.locationData['verbatimCoordinateSystem'];
            this.occurrenceData['georeferencedBy'] = this.locationData['georeferencedBy'];
            this.occurrenceData['georeferenceProtocol'] = this.locationData['georeferenceProtocol'];
            this.occurrenceData['georeferenceSources'] = this.locationData['georeferenceSources'];
            this.occurrenceData['georeferenceVerificationStatus'] = this.locationData['georeferenceVerificationStatus'];
            this.occurrenceData['georeferenceRemarks'] = this.locationData['georeferenceRemarks'];
            this.occurrenceData['minimumElevationInMeters'] = this.locationData['minimumElevationInMeters'];
            this.occurrenceData['maximumElevationInMeters'] = this.locationData['maximumElevationInMeters'];
            this.occurrenceData['verbatimElevation'] = this.locationData['verbatimElevation'];
        },
        setAdditionalData() {
            const formData = new FormData();
            formData.append('eventid', this.eventId.toString());
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
            formData.append('occid', this.occId.toString());
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
            if(this.collId !== Number(collid)){
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
                            this.collId = Number(collid);
                            this.setCollectionInfo();
                        }
                        else{
                            window.location.href = this.getClientRoot + '/index.php';
                        }
                    });
                });
            }
        },
        setCollectionEventData(eventid) {
            if(eventid && Number(eventid) > 0){
                if(this.eventId !== Number(eventid)){
                    this.eventId = Number(eventid);
                    this.additionalData = Object.assign({}, {});
                    const formData = new FormData();
                    formData.append('eventid', eventid.toString());
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
                        this.setAdditionalData();
                    });
                }
            }
            else{
                this.eventId = 0;
                this.collectingEventData = Object.assign({}, {});
                this.additionalData = Object.assign({}, {});
            }
        },
        setCollectionInfo() {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('action', 'getCollectionInfoArr');
            fetch(collectionApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((resObj) => {
                    this.collectionData = Object.assign({}, resObj);
                    this.occurrenceEntryFormat = this.collectionData['datarecordingmethod'];
                    if(this.collectionData['additionalDataFields'] && this.collectionData['additionalDataFields'].hasOwnProperty('dataFields') && this.collectionData['additionalDataFields']['dataFields'].length > 0){
                        this.additionalDataFields = this.collectionData['additionalDataFields']['dataFields'];
                    }
                });
            });
        },
        setDeterminationArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
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
        setDisplayMode(value) {
            this.displayMode = Number(value);
        },
        setDuplicateArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
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
            formData.append('occid', this.occId.toString());
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
            formData.append('occid', this.occId.toString());
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
        setLocationData(locationid) {
            if(locationid && Number(locationid) > 0){
                if(this.locationId !== Number(locationid)){
                    this.locationId = Number(locationid);
                    const formData = new FormData();
                    formData.append('locationid', locationid.toString());
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
                }
            }
            else{
                this.locationId = 0;
                this.locationData = Object.assign({}, {});
            }
        },
        setMediaArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
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
            this.occId = Number(occid);
            if(!this.occidArr.includes(this.occId)){
                this.occidArr.push(this.occId);
            }
            this.clearOccurrenceData();
            if(this.occId > 0){
                const formData = new FormData();
                formData.append('occid', this.occId.toString());
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
                        formData.append('occid', this.occId.toString());
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
                            this.setLocationData(this.occurrenceData['locationID']);
                            this.setCollectionEventData(this.occurrenceData['eventID']);
                        });
                    }
                });
            }
        },
        setOccurrenceEntryFormat(value) {
            this.occurrenceEntryFormat = value;
        }
    }
});
