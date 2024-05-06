const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        additionalData: {},
        additionalDataFields: [],
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
        collectingEventData: {},
        collectingEventEditData: {},
        collectingEventUpdateData: {},
        collectionData: {},
        collectionEventAutoSearch: false,
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
        eventData: {},
        eventId: 0,
        geneticLinkArr: [],
        imageArr: [],
        isEditor: false,
        isLocked: false,
        locationData: {},
        locationEditData: {},
        locationUpdateData: {},
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
        getCollectionData(state) {
            return state.collectionData;
        },
        getCollectionEventAutoSearch(state) {
            return state.collectionEventAutoSearch;
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
        getLocationData(state) {
            return state.locationData;
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
        getCollectingEvents(callback) {
            const formData = new FormData();
            formData.append('collid', this.collId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('recordedby', this.occurrenceEditData['recordedby']);
            formData.append('recordnumber', this.occurrenceEditData['recordnumber']);
            formData.append('eventdate', this.occurrenceEditData['eventdate']);
            formData.append('lastname', this.parseRecordedByLastName(this.occurrenceEditData['recordedby']));
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
            this.occurrenceEditData['eventid'] = this.eventId;
            this.occurrenceEditData['fieldnotes'] = this.eventData['fieldnotes'];
            this.occurrenceEditData['fieldnumber'] = this.eventData['fieldnumber'];
            this.occurrenceEditData['recordedby'] = this.eventData['recordedby'];
            this.occurrenceEditData['recordnumber'] = this.eventData['recordnumber'];
            this.occurrenceEditData['recordedbyid'] = this.eventData['recordedbyid'];
            this.occurrenceEditData['associatedcollectors'] = this.eventData['associatedcollectors'];
            this.occurrenceEditData['eventdate'] = this.eventData['eventdate'];
            this.occurrenceEditData['latestdatecollected'] = this.eventData['latestdatecollected'];
            this.occurrenceEditData['eventtime'] = this.eventData['eventtime'];
            this.occurrenceEditData['year'] = this.eventData['year'];
            this.occurrenceEditData['month'] = this.eventData['month'];
            this.occurrenceEditData['day'] = this.eventData['day'];
            this.occurrenceEditData['startdayofyear'] = this.eventData['startdayofyear'];
            this.occurrenceEditData['enddayofyear'] = this.eventData['enddayofyear'];
            this.occurrenceEditData['verbatimeventdate'] = this.eventData['verbatimeventdate'];
            this.occurrenceEditData['habitat'] = this.eventData['habitat'];
            this.occurrenceEditData['substrate'] = this.eventData['substrate'];
            if(Number(this.occurrenceEditData['localitysecurity']) !== 1 && Number(this.eventData['localitysecurity']) === 1){
                this.occurrenceEditData['localitysecurity'] = this.eventData['localitysecurity'];
                this.occurrenceEditData['localitysecurityreason'] = this.eventData['localitysecurityreason'];
            }
            if(this.eventData['decimallatitude']){
                this.occurrenceEditData['decimallatitude'] = this.eventData['decimallatitude'];
            }
            if(this.eventData['decimallongitude']){
                this.occurrenceEditData['decimallongitude'] = this.eventData['decimallongitude'];
            }
            if(this.eventData['geodeticdatum']){
                this.occurrenceEditData['geodeticdatum'] = this.eventData['geodeticdatum'];
            }
            if(this.eventData['coordinateuncertaintyinmeters']){
                this.occurrenceEditData['coordinateuncertaintyinmeters'] = this.eventData['coordinateuncertaintyinmeters'];
            }
            if(this.eventData['footprintwkt']){
                this.occurrenceEditData['footprintwkt'] = this.eventData['footprintwkt'];
            }
            if(this.eventData['georeferencedby']){
                this.occurrenceEditData['georeferencedby'] = this.eventData['georeferencedby'];
            }
            if(this.eventData['georeferenceprotocol']){
                this.occurrenceEditData['georeferenceprotocol'] = this.eventData['georeferenceprotocol'];
            }
            if(this.eventData['georeferencesources']){
                this.occurrenceEditData['georeferencesources'] = this.eventData['georeferencesources'];
            }
            if(this.eventData['georeferenceverificationstatus']){
                this.occurrenceEditData['georeferenceverificationstatus'] = this.eventData['georeferenceverificationstatus'];
            }
            if(this.eventData['georeferenceremarks']){
                this.occurrenceEditData['georeferenceremarks'] = this.eventData['georeferenceremarks'];
            }
            this.occurrenceEditData['minimumdepthinmeters'] = this.eventData['minimumdepthinmeters'];
            this.occurrenceEditData['maximumdepthinmeters'] = this.eventData['maximumdepthinmeters'];
            this.occurrenceEditData['verbatimdepth'] = this.eventData['verbatimdepth'];
            this.occurrenceEditData['samplingprotocol'] = this.eventData['samplingprotocol'];
            this.occurrenceEditData['samplingeffort'] = this.eventData['samplingeffort'];
            this.occurrenceEditData['labelproject'] = this.eventData['labelproject'];
        },
        mergeLocationOccurrenceData() {
            this.occurrenceEditData['locationid'] = this.locationId;
            this.occurrenceEditData['waterbody'] = this.locationData['waterbody'];
            this.occurrenceEditData['country'] = this.locationData['country'];
            this.occurrenceEditData['stateprovince'] = this.locationData['stateprovince'];
            this.occurrenceEditData['county'] = this.locationData['county'];
            this.occurrenceEditData['municipality'] = this.locationData['municipality'];
            this.occurrenceEditData['locality'] = this.locationData['locality'];
            this.occurrenceEditData['localitysecurity'] = this.locationData['localitysecurity'];
            this.occurrenceEditData['localitysecurityreason'] = this.locationData['localitysecurityreason'];
            this.occurrenceEditData['decimallatitude'] = this.locationData['decimallatitude'];
            this.occurrenceEditData['decimallongitude'] = this.locationData['decimallongitude'];
            this.occurrenceEditData['geodeticdatum'] = this.locationData['geodeticdatum'];
            this.occurrenceEditData['coordinateuncertaintyinmeters'] = this.locationData['coordinateuncertaintyinmeters'];
            this.occurrenceEditData['footprintwkt'] = this.locationData['footprintwkt'];
            this.occurrenceEditData['coordinateprecision'] = this.locationData['coordinateprecision'];
            this.occurrenceEditData['locationremarks'] = this.locationData['locationremarks'];
            this.occurrenceEditData['verbatimcoordinates'] = this.locationData['verbatimcoordinates'];
            this.occurrenceEditData['verbatimcoordinatesystem'] = this.locationData['verbatimcoordinatesystem'];
            this.occurrenceEditData['georeferencedby'] = this.locationData['georeferencedby'];
            this.occurrenceEditData['georeferenceprotocol'] = this.locationData['georeferenceprotocol'];
            this.occurrenceEditData['georeferencesources'] = this.locationData['georeferencesources'];
            this.occurrenceEditData['georeferenceverificationstatus'] = this.locationData['georeferenceverificationstatus'];
            this.occurrenceEditData['georeferenceremarks'] = this.locationData['georeferenceremarks'];
            this.occurrenceEditData['minimumelevationinmeters'] = this.locationData['minimumelevationinmeters'];
            this.occurrenceEditData['maximumelevationinmeters'] = this.locationData['maximumelevationinmeters'];
            this.occurrenceEditData['verbatimelevation'] = this.locationData['verbatimelevation'];
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
        setCollectionEventAutoSearch(value) {
            this.collectionEventAutoSearch = value;
        },
        setCollectionEventData() {
            const formData = new FormData();
            formData.append('eventid', this.eventId.toString());
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
                    this.occurrenceEntryFormat = this.collectionData['datarecordingmethod'];
                    if(this.collectionData['additionalDataFields'] && this.collectionData['additionalDataFields'].hasOwnProperty('dataFields') && this.collectionData['additionalDataFields']['dataFields'].length > 0){
                        this.additionalDataFields = this.collectionData['additionalDataFields']['dataFields'];
                    }
                });
            });
        },
        setCurrentCollectingEventRecord(eventid) {
            this.additionalData = Object.assign({}, {});
            this.collectingEventData = Object.assign({}, this.blankEventRecord);
            if(eventid && Number(eventid) > 0){
                if(this.eventId !== Number(eventid)){
                    this.eventId = Number(eventid);
                    this.setCollectionEventData();
                }
            }
            else{
                this.eventId = 0;
                this.collectingEventEditData = Object.assign({}, this.collectingEventData);
            }
        },
        setCurrentLocationRecord(locationid) {
            this.locationData = Object.assign({}, this.blankLocationRecord);
            if(locationid && Number(locationid) > 0){
                if(this.locationId !== Number(locationid)){
                    this.locationId = Number(locationid);
                    this.setLocationData();
                }
            }
            else{
                this.locationId = 0;
                this.locationEditData = Object.assign({}, this.locationData);
            }
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
                        this.setCurrentLocationRecord(this.occurrenceData['locationID']);
                        this.setCurrentCollectingEventRecord(this.occurrenceData['eventID']);
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
            formData.append('eventid', this.eventId.toString());
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
                    if(this.eventId > 0 && this.getEmbeddedOccurrenceRecord && this.getCollectingEventEditsExist){
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
