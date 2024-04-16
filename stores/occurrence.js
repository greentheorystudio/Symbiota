const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        additionalData: {},
        additionalDataFields: [],
        blankEventRecord: {
            eventid: 0,
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
        blankLocationRecord: {
            locationid: 0,
            collid: 0,
            locationname: null,
            locationcode: null,
            waterbody: null,
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
        blankOccurrenceRecord: {
            occid: 0,
            collid: 0,
            dbpk: null,
            basisofrecord: null,
            occurrenceid: null,
            catalognumber: null,
            othercatalognumbers: null,
            ownerinstitutioncode: null,
            institutionid: null,
            collectionid: null,
            datasetid: null,
            institutioncode: null,
            collectioncode: null,
            family: null,
            verbatimscientificname: null,
            sciname: null,
            tid: null,
            genus: null,
            specificepithet: null,
            taxonrank: null,
            infraspecificepithet: null,
            scientificnameauthorship: null,
            taxonremarks: null,
            identifiedby: null,
            dateidentified: null,
            identificationreferences: null,
            identificationremarks: null,
            identificationqualifier: null,
            typestatus: null,
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
            fieldnotes: null,
            fieldnumber: null,
            eventid: null,
            eventremarks: null,
            occurrenceremarks: null,
            informationwithheld: null,
            datageneralizations: null,
            associatedoccurrences: null,
            associatedtaxa: null,
            dynamicproperties: null,
            verbatimattributes: null,
            behavior: null,
            reproductivecondition: null,
            cultivationstatus: null,
            establishmentmeans: null,
            lifestage: null,
            sex: null,
            individualcount: null,
            samplingprotocol: null,
            samplingeffort: null,
            rep: null,
            preparations: null,
            locationid: null,
            waterbody: null,
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
            verbatimelevation: null,
            minimumdepthinmeters: null,
            maximumdepthinmeters: null,
            verbatimdepth: null,
            previousidentifications: null,
            disposition: null,
            storagelocation: null,
            language: null,
            observeruid: null,
            processingstatus: null,
            duplicatequantity: null,
            labelproject: null
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
            {field: 'stateprovince', label: 'State/Province'},
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
            {field: 'associatedcollectors', label: 'Associated Collectors'},
            {field: 'associatedoccurrences', label: 'Associated Occurrences'},
            {field: 'associatedtaxa', label: 'Associated Taxa'},
            {field: 'attributes', label: 'Attributes'},
            {field: 'scientificnameauthorship', label: 'Author'},
            {field: 'basisofrecord', label: 'Basis Of Record'},
            {field: 'behavior', label: 'Behavior'},
            {field: 'catalognumber', label: 'Catalog Number'},
            {field: 'collectioncode', label: 'Collection Code (override)'},
            {field: 'recordnumber', label: 'Collection Number'},
            {field: 'recordedby', label: 'Collector/Observer'},
            {field: 'coordinateuncertaintyinmeters', label: 'Coordinate Uncertainty (m)'},
            {field: 'country', label: 'Country'},
            {field: 'county', label: 'County'},
            {field: 'cultivationstatus', label: 'Cultivation Status'},
            {field: 'datageneralizations', label: 'Data Generalizations'},
            {field: 'eventdate', label: 'Date'},
            {field: 'dateentered', label: 'Date Entered'},
            {field: 'datelastmodified', label: 'Date Last Modified'},
            {field: '`day`', label: 'Day'},
            {field: 'dbpk', label: 'dbpk'},
            {field: 'decimallatitude', label: 'Decimal Latitude'},
            {field: 'decimallongitude', label: 'Decimal Longitude'},
            {field: 'maximumdepthinmeters', label: 'Depth Maximum (m)'},
            {field: 'minimumdepthinmeters', label: 'Depth Minimum (m)'},
            {field: 'verbatimattributes', label: 'Description'},
            {field: 'disposition', label: 'Disposition'},
            {field: 'dynamicproperties', label: 'Dynamic Properties'},
            {field: 'maximumelevationinmeters', label: 'Elevation Maximum (m)'},
            {field: 'minimumelevationinmeters', label: 'Elevation Minimum (m)'},
            {field: 'establishmentmeans', label: 'Establishment Means'},
            {field: 'family', label: 'Family'},
            {field: 'fieldnotes', label: 'Field Notes'},
            {field: 'fieldnumber', label: 'Field Number'},
            {field: 'genus', label: 'Genus'},
            {field: 'geodeticdatum', label: 'Geodetic Datum'},
            {field: 'georeferenceprotocol', label: 'Georeference Protocol'},
            {field: 'georeferenceremarks', label: 'Georeference Remarks'},
            {field: 'georeferencesources', label: 'Georeference Sources'},
            {field: 'georeferenceverificationstatus', label: 'Georeference Verification Status'},
            {field: 'georeferencedby', label: 'Georeferenced By'},
            {field: 'habitat', label: 'Habitat'},
            {field: 'identificationqualifier', label: 'Identification Qualifier'},
            {field: 'identificationreferences', label: 'Identification References'},
            {field: 'identificationremarks', label: 'Identification Remarks'},
            {field: 'identifiedby', label: 'Identified By'},
            {field: 'individualcount', label: 'Individual Count'},
            {field: 'informationwithheld', label: 'Information Withheld'},
            {field: 'institutioncode', label: 'Institution Code (override)'},
            {field: 'labelproject', label: 'Label Project'},
            {field: 'lifestage', label: 'Life Stage'},
            {field: 'locality', label: 'Locality'},
            {field: 'localitysecurity', label: 'Locality Security'},
            {field: 'localitysecurityreason', label: 'Locality Security Reason'},
            {field: 'locationremarks', label: 'Location Remarks'},
            {field: 'username', label: 'Modified By'},
            {field: '`month`', label: 'Month'},
            {field: 'municipality', label: 'Municipality'},
            {field: 'occurrenceremarks', label: 'Notes (Occurrence Remarks)'},
            {field: 'othercatalognumbers', label: 'Other Catalog Numbers'},
            {field: 'ownerinstitutioncode', label: 'Owner Code'},
            {field: 'preparations', label: 'Preparations'},
            {field: 'reproductivecondition', label: 'Reproductive Condition'},
            {field: 'samplingeffort', label: 'Sampling Effort'},
            {field: 'samplingprotocol', label: 'Sampling Protocol'},
            {field: 'sciname', label: 'Scientific Name'},
            {field: 'sex', label: 'Sex'},
            {field: 'specificepithet', label: 'Specific Epithet'},
            {field: 'stateprovince', label: 'State/Province'},
            {field: 'substrate', label: 'Substrate'},
            {field: 'tid', label: 'Taxon ID'},
            {field: 'taxonremarks', label: 'Taxon Remarks'},
            {field: 'typestatus', label: 'Type Status'},
            {field: 'verbatimcoordinates', label: 'Verbatim Coordinates'},
            {field: 'verbatimeventdate', label: 'Verbatim Date'},
            {field: 'verbatimdepth', label: 'Verbatim Depth'},
            {field: 'verbatimelevation', label: 'Verbatim Elevation'},
            {field: '`year`', label: 'Year'}
        ],
        entryFollowUpAction: 'none',
        eventData: {},
        eventId: 0,
        geneticLinkArr: [],
        imageArr: [],
        isEditor: false,
        isLocked: false,
        locationData: {},
        locationId: 0,
        mediaArr: [],
        occId: null,
        occidArr: [],
        occurrenceData: {},
        occurrenceEditData: {},
        occurrenceEntryFormat: 'specimen',
        occurrenceFields: {},
        occurrenceFieldDefinitions: {},
        occurrenceUpdateData: {},
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
        getEntryFollowUpAction(state) {
            return state.entryFollowUpAction;
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
            return state.occurrenceEditData;
        },
        getOccurrenceEditsExist(state) {
            let exist = false;
            state.occurrenceUpdateData = Object.assign({}, {});
            for(let key in state.occurrenceEditData) {
                if(state.occurrenceEditData.hasOwnProperty(key) && state.occurrenceEditData[key] !== state.occurrenceData[key]) {
                    exist = true;
                    state.occurrenceUpdateData[key] = state.occurrenceEditData[key];
                }
            }
            return exist;
        },
        getOccurrenceEntryFormat(state) {
            return state.occurrenceEntryFormat;
        },
        getOccurrenceFields(state) {
            return state.occurrenceFields;
        },
        getOccurrenceFieldDefinitions(state) {
            return state.occurrenceFieldDefinitions;
        },
        getOccurrenceValid(state) {
            return (state.occurrenceEditData['sciname'] && state.occurrenceEditData['sciname'] !== '');
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
        createOccurrenceRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('occurrence', JSON.stringify(this.occurrenceEditData));
            formData.append('action', 'createOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) > 0){
                        this.occidArr.push(Number(res));
                        if(this.entryFollowUpAction === 'remain' || this.entryFollowUpAction === 'none'){
                            this.setCurrentOccurrenceRecord(Number(res));
                        }
                        else{
                            this.setCurrentOccurrenceRecord(0);
                        }
                    }
                });
            });
        },
        goToFirstRecord() {
            this.setCurrentOccurrenceRecord(this.occidArr[0]);
        },
        goToLastRecord() {
            this.setCurrentOccurrenceRecord(this.occidArr[(this.occidArr.length - 1)]);
        },
        goToNextRecord() {
            this.setCurrentOccurrenceRecord(this.occidArr[this.getCurrentRecordIndex]);
        },
        goToNewOccurrenceRecord(carryLocation = false, carryEvent = false) {
            this.setCurrentOccurrenceRecord(0);
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
            this.setCurrentOccurrenceRecord(this.occidArr[(this.getCurrentRecordIndex - 2)]);
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
            fetch(occurrenceCollectingEventApiUrl, {
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
                        if(this.occurrenceData.hasOwnProperty('occid')){
                            this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                        }
                    }
                    else{
                        window.location.href = this.getClientRoot + '/index.php';
                    }
                });
            });
        },
        setCollectionEventData(eventid) {
            if(eventid && Number(eventid) > 0){
                if(this.eventId !== Number(eventid)){
                    this.eventId = Number(eventid);
                    this.additionalData = Object.assign({}, {});
                    const formData = new FormData();
                    formData.append('eventid', eventid.toString());
                    formData.append('action', 'getCollectionEventDataArr');
                    fetch(occurrenceCollectingEventApiUrl, {
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
        setCurrentOccurrenceRecord(occid) {
            this.occId = Number(occid);
            if(!this.occidArr.includes(this.occId)){
                this.occidArr.push(this.occId);
            }
            this.clearOccurrenceData();
            if(this.occId > 0){
                this.occurrenceEditData = Object.assign({}, {});
                this.setOccurrenceData();
            }
            else{
                this.setOccurrenceCollectionData();
                if(this.entryFollowUpAction === 'newrecordlocation'){
                    this.transferEditLocationDataToOccurrenceData();
                }
                else if(this.entryFollowUpAction === 'newrecordevent'){
                    this.transferEditCollectingEventDataToOccurrenceData();
                }
                this.occurrenceEditData = Object.assign({}, this.occurrenceData);
            }
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
        setEntryFollowUpAction(value) {
            this.entryFollowUpAction = value;
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
                    fetch(occurrenceLocationApiUrl, {
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
        setOccurrenceCollectionData() {
            this.occurrenceData['collid'] = this.collId;
            this.occurrenceData['basisofrecord'] = this.collectionData['colltype'];
        },
        setOccurrenceData() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('collid', this.collId.toString());
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
                        this.setDeterminationArr();
                        this.setImageArr();
                        this.setMediaArr();
                        this.setChecklistArr();
                        this.setDuplicateArr();
                        this.setGeneticLinkArr();
                        this.setLocationData(this.occurrenceData['locationID']);
                        this.setCollectionEventData(this.occurrenceData['eventID']);
                        if(this.collId !== Number(this.occurrenceData.collid)){
                            this.setCollection(this.occurrenceData.collid);
                        }
                        else{
                            this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                        }
                    });
                }
            });
        },
        setOccurrenceEntryFormat(value) {
            this.occurrenceEntryFormat = value;
        },
        setOccurrenceFields() {
            const formData = new FormData();
            formData.append('action', 'getOccurrenceFields');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.occurrenceFields = Object.assign({}, data);
                this.setOccurrenceFieldDefinitions();
            });
        },
        setOccurrenceFieldDefinitions() {
            fetch(fieldDefinitionsUrl)
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                if(data.hasOwnProperty('occurrence')){
                    this.occurrenceFieldDefinitions = Object.assign({}, data['occurrence']);
                }
            });
        },
        transferEditCollectingEventDataToOccurrenceData() {
            this.transferEditLocationDataToOccurrenceData();
            this.occurrenceData['eventid'] = this.occurrenceEditData['eventid'];
            this.occurrenceData['fieldnotes'] = this.occurrenceEditData['fieldnotes'];
            this.occurrenceData['fieldnumber'] = this.occurrenceEditData['fieldnumber'];
            this.occurrenceData['recordedby'] = this.occurrenceEditData['recordedby'];
            this.occurrenceData['recordnumber'] = this.occurrenceEditData['recordnumber'];
            this.occurrenceData['recordedbyid'] = this.occurrenceEditData['recordedbyid'];
            this.occurrenceData['associatedcollectors'] = this.occurrenceEditData['associatedcollectors'];
            this.occurrenceData['eventdate'] = this.occurrenceEditData['eventdate'];
            this.occurrenceData['latestdatecollected'] = this.occurrenceEditData['latestdatecollected'];
            this.occurrenceData['eventtime'] = this.occurrenceEditData['eventtime'];
            this.occurrenceData['year'] = this.occurrenceEditData['year'];
            this.occurrenceData['month'] = this.occurrenceEditData['month'];
            this.occurrenceData['day'] = this.occurrenceEditData['day'];
            this.occurrenceData['startdayofyear'] = this.occurrenceEditData['startdayofyear'];
            this.occurrenceData['enddayofyear'] = this.occurrenceEditData['enddayofyear'];
            this.occurrenceData['verbatimeventdate'] = this.occurrenceEditData['verbatimeventdate'];
            this.occurrenceData['habitat'] = this.occurrenceEditData['habitat'];
            this.occurrenceData['substrate'] = this.occurrenceEditData['substrate'];
            this.occurrenceData['minimumdepthinmeters'] = this.occurrenceEditData['minimumdepthinmeters'];
            this.occurrenceData['maximumdepthinmeters'] = this.occurrenceEditData['maximumdepthinmeters'];
            this.occurrenceData['verbatimdepth'] = this.occurrenceEditData['verbatimdepth'];
            this.occurrenceData['samplingprotocol'] = this.occurrenceEditData['samplingprotocol'];
            this.occurrenceData['samplingeffort'] = this.occurrenceEditData['samplingeffort'];
            this.occurrenceData['labelproject'] = this.occurrenceEditData['labelproject'];
        },
        transferEditLocationDataToOccurrenceData() {
            this.occurrenceData['locationid'] = this.occurrenceEditData['locationid'];
            this.occurrenceData['waterbody'] = this.occurrenceEditData['waterbody'];
            this.occurrenceData['country'] = this.occurrenceEditData['country'];
            this.occurrenceData['stateprovince'] = this.occurrenceEditData['stateprovince'];
            this.occurrenceData['county'] = this.occurrenceEditData['county'];
            this.occurrenceData['municipality'] = this.occurrenceEditData['municipality'];
            this.occurrenceData['locality'] = this.occurrenceEditData['locality'];
            this.occurrenceData['localitysecurity'] = this.occurrenceEditData['localitysecurity'];
            this.occurrenceData['localitysecurityreason'] = this.occurrenceEditData['localitysecurityreason'];
            this.occurrenceData['decimallatitude'] = this.occurrenceEditData['decimallatitude'];
            this.occurrenceData['decimallongitude'] = this.occurrenceEditData['decimallongitude'];
            this.occurrenceData['geodeticdatum'] = this.occurrenceEditData['geodeticdatum'];
            this.occurrenceData['coordinateuncertaintyinmeters'] = this.occurrenceEditData['coordinateuncertaintyinmeters'];
            this.occurrenceData['footprintwkt'] = this.occurrenceEditData['footprintwkt'];
            this.occurrenceData['coordinateprecision'] = this.occurrenceEditData['coordinateprecision'];
            this.occurrenceData['locationremarks'] = this.occurrenceEditData['locationremarks'];
            this.occurrenceData['verbatimcoordinates'] = this.occurrenceEditData['verbatimcoordinates'];
            this.occurrenceData['verbatimcoordinatesystem'] = this.occurrenceEditData['verbatimcoordinatesystem'];
            this.occurrenceData['georeferencedby'] = this.occurrenceEditData['georeferencedby'];
            this.occurrenceData['georeferenceprotocol'] = this.occurrenceEditData['georeferenceprotocol'];
            this.occurrenceData['georeferencesources'] = this.occurrenceEditData['georeferencesources'];
            this.occurrenceData['georeferenceverificationstatus'] = this.occurrenceEditData['georeferenceverificationstatus'];
            this.occurrenceData['georeferenceremarks'] = this.occurrenceEditData['georeferenceremarks'];
            this.occurrenceData['minimumelevationinmeters'] = this.occurrenceEditData['minimumelevationinmeters'];
            this.occurrenceData['maximumelevationinmeters'] = this.occurrenceEditData['maximumelevationinmeters'];
            this.occurrenceData['verbatimelevation'] = this.occurrenceEditData['verbatimelevation'];
        },
        updateOccurrenceEditData(key, value) {
            this.occurrenceEditData[key] = value;
        },
        updateOccurrenceRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('occurrenceData', JSON.stringify(this.occurrenceUpdateData));
            formData.append('action', 'updateOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) === 1){
                        this.occurrenceData = Object.assign({}, this.occurrenceEditData);
                    }
                });
            });
        },
    }
});
