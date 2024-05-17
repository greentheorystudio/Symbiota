const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        additionalData: {},
        additionalDataFields: [],
        basisOfRecordOptions: [
            {value: 'PreservedSpecimen', label: 'Preserved Specimen'},
            {value: 'HumanObservation', label: 'Observation'},
            {value: 'FossilSpecimen', label: 'Fossil Specimen'},
            {value: 'LivingSpecimen', label: 'Living Specimen'},
            {value: 'MaterialSample', label: 'Material Sample'}
        ],
        blankEventRecord: {
            eventid: 0,
            collid: 0,
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
        collectingEventAutoSearch: false,
        collectingEventBenthicData: {},
        collectingEventCollectionArr: [],
        collectingEventData: {},
        collectingEventEditData: {},
        collectingEventFields: {},
        collectingEventId: 0,
        collectingEventUpdateData: {},
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
        entryFollowUpAction: 'remain',
        geneticLinkArr: [],
        imageArr: [],
        isEditor: false,
        isLocked: false,
        locationCollectingEventArr: [],
        locationData: {},
        locationEditData: {},
        locationFields: {},
        locationId: 0,
        locationUpdateData: {},
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
        getBasisOfRecordOptions(state) {
            return state.basisOfRecordOptions;
        },
        getChecklistArr(state) {
            return state.checklistArr;
        },
        getClientRoot() {
            const store = useBaseStore();
            return store.getClientRoot;
        },
        getCollectingEventAutoSearch(state) {
            return state.collectingEventAutoSearch;
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
        getEmbeddedOccurrenceRecord(state) {
            return (state.occurrenceEntryFormat !== 'benthic' && state.occurrenceEntryFormat !== 'lot');
        },
        getEntryFollowUpAction(state) {
            return state.entryFollowUpAction;
        },
        getEventRecordFields(state) {
            return Object.keys(state.blankEventRecord);
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
        getLocationCollectingEventArr(state) {
            return state.locationCollectingEventArr;
        },
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
        },
        getMediaArr(state) {
            return state.mediaArr;
        },
        getNewRecordExisting(state) {
            return state.occidArr.includes(0);
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
            return (state.occurrenceEditData['sciname']);
        }
    },
    actions: {
        clearCollectionData() {
            this.isEditor = false;
            this.collectionData = Object.assign({}, {});
            this.additionalDataFields.length = 0;
        },
        clearCollectingEventData() {
            this.additionalData = Object.assign({}, {});
            this.collectingEventData = Object.assign({}, this.blankEventRecord);
            this.collectingEventBenthicData = Object.assign({}, {});
            this.collectingEventCollectionArr.length = 0;
        },
        clearLocationData() {
            this.locationCollectingEventArr.length = 0;
            this.locationData = Object.assign({}, this.blankLocationRecord);
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
        createCollectingEventRecord(callback) {
            this.collectingEventEditData['collid'] = this.collId;
            const formData = new FormData();
            formData.append('event', JSON.stringify(this.collectingEventEditData));
            formData.append('action', 'createCollectingEventRecord');
            fetch(occurrenceCollectingEventApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(Number(res));
                    if(res && Number(res) > 0){
                        this.updateOccurrenceEditData('eventid', Number(res));
                        this.setCurrentCollectingEventRecord(Number(res));
                    }
                });
            });
        },
        createLocationRecord(callback) {
            this.locationEditData['collid'] = this.collId;
            const formData = new FormData();
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
                        this.updateOccurrenceEditData('locationid', Number(res));
                        this.setCurrentLocationRecord(Number(res));
                    }
                });
            });
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
                        if(this.occidArr[(this.occidArr.length - 1)] === 0){
                            this.occidArr.splice((this.occidArr.length - 1), 1);
                        }
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
        deleteOccurrenceRecord(occid, callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('occid', occid);
            formData.append('action', 'deleteOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((val) => {
                    if(this.occidArr.includes(Number(occid))){
                        const index = this.occidArr.indexOf(Number(occid));
                        this.occidArr.splice(index, 1);
                    }
                    if(this.occId === Number(occid)){
                        if(this.occidArr.length > 0){
                            this.setCurrentOccurrenceRecord(this.occidArr[(this.occidArr.length - 1)]);
                        }
                        else{
                            this.setCurrentOccurrenceRecord(0);
                        }
                    }
                    callback(Number(val));
                });
            });
        },
        evaluateOccurrenceForDeletion(occid, callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('occid', occid);
            formData.append('action', 'evaluateOccurrenceForDeletion');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.json().then((data) => {
                    callback(data);
                });
            });
        },
        getCollectingEvents(type, callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            if(type === 'occurrence'){
                formData.append('occid', this.occId.toString());
                formData.append('recordedby', this.occurrenceEditData['recordedby']);
                formData.append('recordnumber', this.occurrenceEditData['recordnumber']);
                formData.append('eventdate', this.occurrenceEditData['eventdate']);
                formData.append('lastname', this.parseRecordedByLastName(this.occurrenceEditData['recordedby']));
            }
            else if(type === 'location'){
                formData.append('locationid', this.locationId.toString());
            }
            formData.append('action', 'getCollectingEventArr');
            fetch(occurrenceCollectingEventApiUrl, {
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
        getNearbyLocations(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
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
                this.setCurrentCollectingEventRecord(0);
            }
        },
        goToPreviousRecord() {
            this.setCurrentOccurrenceRecord(this.occidArr[(this.getCurrentRecordIndex - 2)]);
        },
        mergeEventOccurrenceData() {
            this.occurrenceEditData['eventid'] = this.collectingEventId;
            this.occurrenceEditData['fieldnotes'] = this.collectingEventEditData['fieldnotes'];
            this.occurrenceEditData['fieldnumber'] = this.collectingEventEditData['fieldnumber'];
            this.occurrenceEditData['recordedby'] = this.collectingEventEditData['recordedby'];
            this.occurrenceEditData['recordnumber'] = this.collectingEventEditData['recordnumber'];
            this.occurrenceEditData['recordedbyid'] = this.collectingEventEditData['recordedbyid'];
            this.occurrenceEditData['associatedcollectors'] = this.collectingEventEditData['associatedcollectors'];
            this.occurrenceEditData['eventdate'] = this.collectingEventEditData['eventdate'];
            this.occurrenceEditData['latestdatecollected'] = this.collectingEventEditData['latestdatecollected'];
            this.occurrenceEditData['eventtime'] = this.collectingEventEditData['eventtime'];
            this.occurrenceEditData['year'] = this.collectingEventEditData['year'];
            this.occurrenceEditData['month'] = this.collectingEventEditData['month'];
            this.occurrenceEditData['day'] = this.collectingEventEditData['day'];
            this.occurrenceEditData['startdayofyear'] = this.collectingEventEditData['startdayofyear'];
            this.occurrenceEditData['enddayofyear'] = this.collectingEventEditData['enddayofyear'];
            this.occurrenceEditData['verbatimeventdate'] = this.collectingEventEditData['verbatimeventdate'];
            this.occurrenceEditData['habitat'] = this.collectingEventEditData['habitat'];
            this.occurrenceEditData['substrate'] = this.collectingEventEditData['substrate'];
            if(Number(this.occurrenceEditData['localitysecurity']) !== 1 && Number(this.collectingEventEditData['localitysecurity']) === 1){
                this.occurrenceEditData['localitysecurity'] = this.collectingEventEditData['localitysecurity'];
                this.occurrenceEditData['localitysecurityreason'] = this.collectingEventEditData['localitysecurityreason'];
            }
            if(this.collectingEventEditData['decimallatitude']){
                this.occurrenceEditData['decimallatitude'] = this.collectingEventEditData['decimallatitude'];
            }
            if(this.collectingEventEditData['decimallongitude']){
                this.occurrenceEditData['decimallongitude'] = this.collectingEventEditData['decimallongitude'];
            }
            if(this.collectingEventEditData['geodeticdatum']){
                this.occurrenceEditData['geodeticdatum'] = this.collectingEventEditData['geodeticdatum'];
            }
            if(this.collectingEventEditData['coordinateuncertaintyinmeters']){
                this.occurrenceEditData['coordinateuncertaintyinmeters'] = this.collectingEventEditData['coordinateuncertaintyinmeters'];
            }
            if(this.collectingEventEditData['footprintwkt']){
                this.occurrenceEditData['footprintwkt'] = this.collectingEventEditData['footprintwkt'];
            }
            if(this.collectingEventEditData['georeferencedby']){
                this.occurrenceEditData['georeferencedby'] = this.collectingEventEditData['georeferencedby'];
            }
            if(this.collectingEventEditData['georeferenceprotocol']){
                this.occurrenceEditData['georeferenceprotocol'] = this.collectingEventEditData['georeferenceprotocol'];
            }
            if(this.collectingEventEditData['georeferencesources']){
                this.occurrenceEditData['georeferencesources'] = this.collectingEventEditData['georeferencesources'];
            }
            if(this.collectingEventEditData['georeferenceverificationstatus']){
                this.occurrenceEditData['georeferenceverificationstatus'] = this.collectingEventEditData['georeferenceverificationstatus'];
            }
            if(this.collectingEventEditData['georeferenceremarks']){
                this.occurrenceEditData['georeferenceremarks'] = this.collectingEventEditData['georeferenceremarks'];
            }
            this.occurrenceEditData['minimumdepthinmeters'] = this.collectingEventEditData['minimumdepthinmeters'];
            this.occurrenceEditData['maximumdepthinmeters'] = this.collectingEventEditData['maximumdepthinmeters'];
            this.occurrenceEditData['verbatimdepth'] = this.collectingEventEditData['verbatimdepth'];
            this.occurrenceEditData['samplingprotocol'] = this.collectingEventEditData['samplingprotocol'];
            this.occurrenceEditData['samplingeffort'] = this.collectingEventEditData['samplingeffort'];
            this.occurrenceEditData['labelproject'] = this.collectingEventEditData['labelproject'];
        },
        mergeLocationOccurrenceData() {
            this.occurrenceEditData['locationid'] = this.locationId;
            this.occurrenceEditData['waterbody'] = this.locationEditData['waterbody'];
            this.occurrenceEditData['country'] = this.locationEditData['country'];
            this.occurrenceEditData['stateprovince'] = this.locationEditData['stateprovince'];
            this.occurrenceEditData['county'] = this.locationEditData['county'];
            this.occurrenceEditData['municipality'] = this.locationEditData['municipality'];
            this.occurrenceEditData['locality'] = this.locationEditData['locality'];
            this.occurrenceEditData['localitysecurity'] = this.locationEditData['localitysecurity'];
            this.occurrenceEditData['localitysecurityreason'] = this.locationEditData['localitysecurityreason'];
            this.occurrenceEditData['decimallatitude'] = this.locationEditData['decimallatitude'];
            this.occurrenceEditData['decimallongitude'] = this.locationEditData['decimallongitude'];
            this.occurrenceEditData['geodeticdatum'] = this.locationEditData['geodeticdatum'];
            this.occurrenceEditData['coordinateuncertaintyinmeters'] = this.locationEditData['coordinateuncertaintyinmeters'];
            this.occurrenceEditData['footprintwkt'] = this.locationEditData['footprintwkt'];
            this.occurrenceEditData['coordinateprecision'] = this.locationEditData['coordinateprecision'];
            this.occurrenceEditData['locationremarks'] = this.locationEditData['locationremarks'];
            this.occurrenceEditData['verbatimcoordinates'] = this.locationEditData['verbatimcoordinates'];
            this.occurrenceEditData['verbatimcoordinatesystem'] = this.locationEditData['verbatimcoordinatesystem'];
            this.occurrenceEditData['georeferencedby'] = this.locationEditData['georeferencedby'];
            this.occurrenceEditData['georeferenceprotocol'] = this.locationEditData['georeferenceprotocol'];
            this.occurrenceEditData['georeferencesources'] = this.locationEditData['georeferencesources'];
            this.occurrenceEditData['georeferenceverificationstatus'] = this.locationEditData['georeferenceverificationstatus'];
            this.occurrenceEditData['georeferenceremarks'] = this.locationEditData['georeferenceremarks'];
            this.occurrenceEditData['minimumelevationinmeters'] = this.locationEditData['minimumelevationinmeters'];
            this.occurrenceEditData['maximumelevationinmeters'] = this.locationEditData['maximumelevationinmeters'];
            this.occurrenceEditData['verbatimelevation'] = this.locationEditData['verbatimelevation'];
        },
        mergeSelectedEventOccurrenceData(data, overwrite) {
            const dataProps = Object.keys(data);
            dataProps.forEach((prop) => {
                if(data[prop] && (!this.occurrenceEditData[prop] || overwrite)){
                    this.occurrenceEditData[prop] = data[prop];
                }
            });
        },
        parseRecordedByLastName(nameStr) {
            let lastName = null;
            const trimmedName = nameStr ? nameStr.trim() : null;
            if(trimmedName){
                let lastNameArr = [];
                let primaryArr = trimmedName.split(';');
                if(primaryArr.length > 0){
                    primaryArr = primaryArr[0].split('&');
                }
                if(primaryArr.length > 0){
                    primaryArr = primaryArr[0].split(' and ');
                }
                if(primaryArr.length > 0){
                    lastNameArr = primaryArr[0].split(',');
                }
                if(lastNameArr.length > 0 && lastNameArr[0]){
                    if(lastNameArr.length > 1){
                        lastName = lastNameArr[0];
                    }
                    else{
                        let tempArr = lastNameArr[0].split(' ');
                        lastName = tempArr.pop();
                        do{
                            lastName = tempArr.pop();
                        }
                        while(tempArr.length > 0 && (lastName.includes('.') || lastName === 'III' || lastName.length < 3));
                    }
                }
            }
            return lastName;
        },
        revertCollectingEventEditData() {
            this.collectingEventEditData = Object.assign({}, this.collectingEventData);
        },
        revertLocationEditData() {
            this.locationEditData = Object.assign({}, this.locationData);
        },
        setAdditionalData() {
            const formData = new FormData();
            formData.append('eventid', this.collectingEventId.toString());
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
        setCollectingEventAutoSearch(value) {
            this.collectingEventAutoSearch = value;
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
        setCollectingEventData() {
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
                this.setAdditionalData();
                if(this.occurrenceEntryFormat === 'benthic'){
                    this.setCollectingEventBenthicData();
                }
                else{
                    this.setCollectingEventCollectionsArr();
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
        setCollection(collid, callback = null) {
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
                            this.setCurrentLocationRecord(this.occurrenceEditData['locationid'] ? this.occurrenceEditData['locationid'] : 0);
                            this.setCurrentCollectingEventRecord(this.occurrenceEditData['eventid'] ? this.occurrenceEditData['eventid'] : 0);
                        }
                        if(callback){
                            callback();
                        }
                    }
                    else{
                        window.location.href = this.getClientRoot + '/index.php';
                    }
                });
            });
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
                    if(this.collectingEventId === 0){
                        this.collectingEventEditData['repcount'] = this.collectionData['defaultrepcount'] ? Number(this.collectionData['defaultrepcount']) : 0;
                    }
                    this.occurrenceEntryFormat = this.collectionData['datarecordingmethod'];
                    if(this.collectionData['additionalDataFields'] && this.collectionData['additionalDataFields'].hasOwnProperty('dataFields') && this.collectionData['additionalDataFields']['dataFields'].length > 0){
                        this.additionalDataFields = this.collectionData['additionalDataFields']['dataFields'];
                    }
                });
            });
        },
        setCurrentCollectingEventRecord(eventid) {
            if(eventid && Number(eventid) > 0){
                if(this.collectingEventId !== Number(eventid)){
                    this.collectingEventId = Number(eventid);
                    this.clearCollectingEventData();
                    this.setCollectingEventData();
                }
            }
            else{
                this.collectingEventId = 0;
                this.clearCollectingEventData();
                this.collectingEventData['repcount'] = this.collectionData['defaultrepcount'] ? Number(this.collectionData['defaultrepcount']) : 0;
                this.collectingEventEditData = Object.assign({}, this.collectingEventData);
            }
        },
        setCurrentLocationRecord(locationid) {
            if(locationid && Number(locationid) > 0){
                if(this.locationId !== Number(locationid)){
                    this.locationId = Number(locationid);
                    this.clearLocationData();
                    this.setLocationData();
                }
            }
            else{
                this.locationId = 0;
                this.clearLocationData();
                this.locationEditData = Object.assign({}, this.locationData);
            }
        },
        setCurrentOccurrenceRecord(occid, callback = null) {
            this.occId = Number(occid);
            if(!this.occidArr.includes(this.occId)){
                this.occidArr.push(this.occId);
            }
            this.clearOccurrenceData();
            if(this.occId > 0){
                this.occurrenceEditData = Object.assign({}, {});
                this.setOccurrenceData(callback);
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
                if(this.locationId > 0){
                    this.mergeLocationOccurrenceData();
                }
                if(this.collectingEventId > 0){
                    this.mergeEventOccurrenceData();
                }
                if(callback){
                    callback();
                }
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
        setLocationData() {
            const formData = new FormData();
            formData.append('locationid', this.locationId.toString());
            formData.append('collid', this.collId.toString());
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
                this.occurrenceEditData['locationid'] = this.locationId.toString();
                this.getCollectingEvents('location', (listArr) => {
                    if(listArr.length > 0){
                        this.locationCollectingEventArr = this.locationCollectingEventArr.concat(listArr);
                    }
                });
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
        setOccurrenceData(callback) {
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
                        if(data.hasOwnProperty('occid') && Number(data.occid) > 0){
                            this.occurrenceData = Object.assign({}, data);
                            this.setDeterminationArr();
                            this.setImageArr();
                            this.setMediaArr();
                            this.setChecklistArr();
                            this.setDuplicateArr();
                            this.setGeneticLinkArr();
                            if(this.collId !== Number(this.occurrenceData.collid)){
                                this.setCollection(this.occurrenceData.collid, callback);
                            }
                            else{
                                this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                                this.setCurrentLocationRecord(this.occurrenceEditData['locationid'] ? this.occurrenceEditData['locationid'] : 0);
                                this.setCurrentCollectingEventRecord(this.occurrenceEditData['eventid'] ? this.occurrenceEditData['eventid'] : 0);
                                if(callback){
                                    callback();
                                }
                            }
                        }
                        else if(this.collId > 0){
                            this.setCurrentOccurrenceRecord(0);
                            if(callback){
                                callback();
                            }
                        }
                        else{
                            const baseStore = useBaseStore();
                            window.location.href = baseStore.getClientRoot + '/index.php';
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
        updateCollectingEventEditData(key, value) {
            this.collectingEventEditData[key] = value;
        },
        updateLocationEditData(key, value) {
            this.locationEditData[key] = value;
        },
        updateOccurrenceEditData(key, value) {
            this.occurrenceEditData[key] = value;
            if(this.getEmbeddedOccurrenceRecord && this.getEventRecordFields.includes(key) && this.getAdditionalDataFields.length > 0){
                this.updateCollectingEventEditData(key, value);
            }
        },
        updateOccurrenceEditDataTaxon(taxon) {
            this.occurrenceEditData['sciname'] = taxon ? taxon.sciname : null;
            this.occurrenceEditData['tid'] = taxon ? taxon.tid : null;
            this.occurrenceEditData['family'] = taxon ? taxon.family : null;
            this.occurrenceEditData['scientificnameauthorship'] = taxon ? taxon.author : null;
            this.occurrenceEditData['taxonData'] = taxon ? Object.assign({}, taxon) : null;
        },
        updateCollectingEventRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
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
        updateLocationRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
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
                    if(res && Number(res) === 1){
                        this.occurrenceData = Object.assign({}, this.occurrenceEditData);
                    }
                    if(this.collectingEventId > 0 && this.getEmbeddedOccurrenceRecord && this.getCollectingEventEditsExist){
                        this.updateCollectingEventRecord(callback);
                    }
                    else{
                        callback(Number(res));
                    }
                });
            });
        }
    }
});
