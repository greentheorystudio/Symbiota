const useOccurrenceStore = Pinia.defineStore('occurrence', {
    state: () => ({
        basisOfRecordOptions: [
            {value: 'PreservedSpecimen', label: 'Preserved Specimen'},
            {value: 'HumanObservation', label: 'Observation'},
            {value: 'FossilSpecimen', label: 'Fossil Specimen'},
            {value: 'LivingSpecimen', label: 'Living Specimen'},
            {value: 'MaterialSample', label: 'Material Sample'}
        ],
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
        collectingEventStore: useOccurrenceCollectingEventStore(),
        collectionStore: useCollectionStore(),
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
        determinationStore: useOccurrenceDeterminationStore(),
        displayMode: 1,
        editArr: [],
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
            {field: 'continent', label: 'Continent'},
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
            {field: 'island', label: 'Island'},
            {field: 'islandgroup', label: 'Island Group'},
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
        geneticLinkStore: useOccurrenceGeneticLinkStore(),
        imageStore: useImageStore(),
        isEditor: false,
        isLocked: false,
        locationStore: useOccurrenceLocationStore(),
        mediaStore: useMediaStore(),
        occId: null,
        occidArr: [],
        occurrenceData: {},
        occurrenceEditData: {},
        occurrenceEntryFormat: 'specimen',
        occurrenceFields: {},
        occurrenceFieldDefinitions: {},
        occurrenceUpdateData: {}
    }),
    getters: {
        getBasisOfRecordOptions(state) {
            return state.basisOfRecordOptions;
        },
        getBlankOccurrenceRecord(state) {
            return state.blankOccurrenceRecord;
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
            return state.collectingEventStore.getCollectingEventBenthicData;
        },
        getCollectingEventBenthicTaxaCnt(state) {
            return state.collectingEventStore.getCollectingEventBenthicTaxaCnt;
        },
        getCollectingEventCollectionArr(state) {
            return state.collectingEventStore.getCollectingEventCollectionArr;
        },
        getCollectingEventData(state) {
            return state.collectingEventStore.getCollectingEventData;
        },
        getCollectingEventEditsExist(state) {
            return state.collectingEventStore.getCollectingEventEditsExist;
        },
        getCollectingEventFields(state) {
            return state.collectingEventStore.getCollectingEventFields;
        },
        getCollectingEventID(state) {
            return state.collectingEventStore.getCollectingEventID;
        },
        getCollectingEventValid(state) {
            return state.collectingEventStore.getCollectingEventValid;
        },
        getCollectionData(state) {
            return state.collectionStore.getCollectionData;
        },
        getCollId(state) {
            return state.collectionStore.getCollectionId;
        },
        getConfiguredData(state) {
            return state.collectingEventStore.getConfiguredData;
        },
        getConfiguredDataFields(state) {
            return state.collectionStore.getConfiguredDataFields;
        },
        getConfiguredDataFieldsLayoutData(state) {
            return state.collectionStore.getConfiguredDataFieldsLayoutData;
        },
        getConfiguredDataLabel(state) {
            return state.collectionStore.getConfiguredDataLabel;
        },
        getCrowdSourceQueryFieldOptions(state) {
            return state.crowdSourceQueryFieldOptions;
        },
        getCurrentRecordIndex(state) {
            return (state.occidArr.indexOf(state.occId) + 1);
        },
        getDeterminationArr(state) {
            return state.determinationStore.getDeterminationArr;
        },
        getDeterminationData(state) {
            return state.determinationStore.getDeterminationData;
        },
        getDeterminationEditsExist(state) {
            return state.determinationStore.getDeterminationEditsExist;
        },
        getDeterminationFields(state) {
            return state.determinationStore.getDeterminationFields;
        },
        getDeterminationID(state) {
            return state.determinationStore.getDeterminationID;
        },
        getDeterminationValid(state) {
            return state.determinationStore.getDeterminationValid;
        },
        getDisplayMode(state) {
            return state.displayMode;
        },
        getEditArr(state) {
            return state.editArr;
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
            return state.collectingEventStore.getEventRecordFields;
        },
        getGeneticLinkArr(state) {
            return state.geneticLinkStore.getGeneticLinkArr;
        },
        getGeneticLinkData(state) {
            return state.geneticLinkStore.getGeneticLinkData;
        },
        getGeneticLinkEditsExist(state) {
            return state.geneticLinkStore.getGeneticLinkEditsExist;
        },
        getGeneticLinkValid(state) {
            return state.geneticLinkStore.getGeneticLinkValid;
        },
        getImageArr(state) {
            return state.imageStore.getImageArr;
        },
        getImageCount(state) {
            return state.imageStore.getImageCount;
        },
        getIsEditor(state) {
            return (state.collectionStore.getCollectionPermissions.includes('CollAdmin') || state.collectionStore.getCollectionPermissions.includes('CollEditor'));
        },
        getIsLocked(state) {
            return state.isLocked;
        },
        getLocationCollectingEventArr(state) {
            return state.collectingEventStore.getLocationCollectingEventArr;
        },
        getLocationData(state) {
            return state.locationStore.getLocationData;
        },
        getLocationEditsExist(state) {
            return state.locationStore.getLocationEditsExist;
        },
        getLocationFields(state) {
            return state.locationStore.getLocationFields;
        },
        getLocationID(state) {
            return state.locationStore.getLocationID;
        },
        getLocationValid(state) {
            return state.locationStore.getLocationValid;
        },
        getMediaArr(state) {
            return state.mediaStore.getMediaArr;
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
        addConfiguredDataValue(key, value, callback = null) {
            this.collectingEventStore.addConfiguredDataValue(this.getCollId, key, value, this.getConfiguredDataFields, callback);
        },
        clearOccurrenceData() {
            this.occurrenceData = Object.assign({}, this.blankOccurrenceRecord);
            this.isLocked = false;
            this.determinationStore.clearDeterminationArr();
            this.editArr.length = 0;
            this.imageStore.clearImageArr();
            this.mediaStore.clearMediaArr();
            this.checklistArr.length = 0;
            this.geneticLinkStore.clearGeneticLinkArr();
        },
        createCollectingEventRecord(callback) {
            this.collectingEventStore.createCollectingEventRecord(this.getCollId, this.getLocationID, this.occurrenceEntryFormat, this.getCollectionData['defaultrepcount'], this.getConfiguredDataFields, (newEventId) => {
                callback(Number(newEventId));
                if(newEventId && Number(newEventId) > 0){
                    this.updateOccurrenceEditData('eventid', Number(newEventId));
                }
            });
        },
        createLocationRecord(callback) {
            this.locationStore.createLocationRecord(this.getCollId, (newLocationId) => {
                callback(Number(newLocationId));
                if(newLocationId && Number(newLocationId) > 0){
                    this.updateOccurrenceEditData('locationid', Number(newLocationId));
                }
            });
        },
        createOccurrenceDeterminationRecord(callback) {
            const newIsCurrent = Number(this.determinationStore.getDeterminationData['iscurrent']) === 1;
            this.determinationStore.createOccurrenceDeterminationRecord(this.getCollId, this.occId, (newDetId) => {
                callback(Number(newDetId));
                if(newIsCurrent){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
                else if(newDetId && Number(newDetId) > 0){
                    this.determinationStore.setDeterminationArr(this.occId);
                }
            });
        },
        createOccurrenceGeneticLinkageRecord(callback) {
            this.geneticLinkStore.createOccurrenceGeneticLinkageRecord(this.getCollId, this.occId, (newLinkId) => {
                callback(Number(newLinkId));
                if(newLinkId && Number(newLinkId) > 0){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        createOccurrenceRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
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
                        if(this.occurrenceEntryFormat !== 'observation'){
                            if(this.entryFollowUpAction === 'remain' || this.entryFollowUpAction === 'none'){
                                this.setCurrentOccurrenceRecord(Number(res));
                            }
                            else{
                                this.setCurrentOccurrenceRecord(0);
                            }
                        }
                    }
                });
            });
        },
        deleteConfiguredDataValue(key, callback = null) {
            this.collectingEventStore.deleteConfiguredDataValue(this.getCollId, key, this.getConfiguredDataFields, callback);
        },
        deleteOccurrenceDeterminationRecord(callback = null) {
            this.determinationStore.deleteDeterminationRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.determinationStore.setDeterminationArr(this.occId);
                }
            });
        },
        deleteGeneticLinkageRecord(callback = null) {
            this.geneticLinkStore.deleteGeneticLinkageRecord(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        deleteOccurrenceRecord(occid, callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
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
            formData.append('collid', this.getCollId.toString());
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
        getCoordinateVerificationData(callback) {
            if(this.occurrenceEditData['decimallatitude'] && this.occurrenceEditData['decimallongitude']){
                const url = 'https://nominatim.openstreetmap.org/reverse?lat=' + this.occurrenceEditData['decimallatitude'].toString() + '&lon=' + this.occurrenceEditData['decimallongitude'].toString() + '&format=json';
                fetch(url)
                .then((response) => {
                    return response.ok ? response.json() : null;
                })
                .then((data) => {
                    const returnData = {
                        valid: false,
                        address: false,
                        country: null,
                        state: null,
                        county: null
                    };
                    if(data.hasOwnProperty('address')){
                        returnData.address = true;
                        returnData.country = data.address.country;
                        returnData.state = data.address.state;
                        returnData.valid = true;
                        if((!this.occurrenceEditData['country'] || this.occurrenceEditData['country'] === '') && returnData.country && returnData.country !== ''){
                            this.updateOccurrenceEditData('country', returnData.country);
                        }
                        if(this.occurrenceEditData['country'] && returnData.country && this.occurrenceEditData['country'] !== '' && this.occurrenceEditData['country'].toLowerCase() !== returnData.country.toLowerCase()){
                            if(this.occurrenceEditData['country'].toLowerCase() !== 'usa' && this.occurrenceEditData['country'].toLowerCase() !== 'united states of america' && returnData.country.toLowerCase() !== 'united states'){
                                returnData.valid = false;
                            }
                        }
                        if(returnData.state && returnData.state !== ''){
                            if(this.occurrenceEditData['stateprovince'] && this.occurrenceEditData['stateprovince'] !== '' && this.occurrenceEditData['stateprovince'].toLowerCase() !== returnData.state.toLowerCase()){
                                returnData.valid = false;
                            }
                            else{
                                this.updateOccurrenceEditData('stateprovince', returnData.state);
                            }
                        }
                        if(data.address.county && data.address.county !== ''){
                            let coordCountyIn = data.address.county.replace(' County', '');
                            coordCountyIn = coordCountyIn.replace(' Parish', '');
                            returnData.county = coordCountyIn;
                            if(this.occurrenceEditData['county'] && this.occurrenceEditData['county'] !== '' && this.occurrenceEditData['county'].toLowerCase() !== coordCountyIn.toLowerCase()){
                                returnData.valid = false;
                            }
                            else{
                                this.updateOccurrenceEditData('county', coordCountyIn);
                            }
                        }
                    }
                    callback(returnData);
                });
            }
        },
        getNearbyLocations(callback) {
            this.locationStore.getNearbyLocations(this.getCollId, callback);
        },
        getOccurrenceCollectingEvents(callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
            formData.append('occid', this.occId.toString());
            formData.append('recordedby', this.occurrenceEditData['recordedby']);
            formData.append('recordnumber', this.occurrenceEditData['recordnumber']);
            formData.append('eventdate', this.occurrenceEditData['eventdate']);
            formData.append('lastname', this.parseRecordedByLastName(this.occurrenceEditData['recordedby']));
            formData.append('action', 'getOccurrenceCollectingEventArr');
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
                this.locationStore.clearLocationData();
                this.collectingEventStore.clearLocationData();
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
        makeDeterminationCurrent(callback = null) {
            this.determinationStore.makeDeterminationCurrent(this.getCollId, (res) => {
                if(callback){
                    callback(Number(res));
                }
                if(Number(res) === 1){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
            });
        },
        mergeEventOccurrenceData() {
            const eventData = this.getCollectingEventData;
            this.occurrenceEditData['eventid'] = this.getCollectingEventID;
            this.occurrenceEditData['fieldnotes'] = eventData['fieldnotes'];
            this.occurrenceEditData['fieldnumber'] = eventData['fieldnumber'];
            this.occurrenceEditData['recordedby'] = eventData['recordedby'];
            this.occurrenceEditData['recordnumber'] = eventData['recordnumber'];
            this.occurrenceEditData['recordedbyid'] = eventData['recordedbyid'];
            this.occurrenceEditData['associatedcollectors'] = eventData['associatedcollectors'];
            this.occurrenceEditData['eventdate'] = eventData['eventdate'];
            this.occurrenceEditData['latestdatecollected'] = eventData['latestdatecollected'];
            this.occurrenceEditData['eventtime'] = eventData['eventtime'];
            this.occurrenceEditData['year'] = eventData['year'];
            this.occurrenceEditData['month'] = eventData['month'];
            this.occurrenceEditData['day'] = eventData['day'];
            this.occurrenceEditData['startdayofyear'] = eventData['startdayofyear'];
            this.occurrenceEditData['enddayofyear'] = eventData['enddayofyear'];
            this.occurrenceEditData['verbatimeventdate'] = eventData['verbatimeventdate'];
            this.occurrenceEditData['habitat'] = eventData['habitat'];
            this.occurrenceEditData['substrate'] = eventData['substrate'];
            if(Number(this.occurrenceEditData['localitysecurity']) !== 1 && Number(eventData['localitysecurity']) === 1){
                this.occurrenceEditData['localitysecurity'] = eventData['localitysecurity'];
                this.occurrenceEditData['localitysecurityreason'] = eventData['localitysecurityreason'];
            }
            if(eventData['decimallatitude']){
                this.occurrenceEditData['decimallatitude'] = eventData['decimallatitude'];
            }
            if(eventData['decimallongitude']){
                this.occurrenceEditData['decimallongitude'] = eventData['decimallongitude'];
            }
            if(eventData['geodeticdatum']){
                this.occurrenceEditData['geodeticdatum'] = eventData['geodeticdatum'];
            }
            if(eventData['coordinateuncertaintyinmeters']){
                this.occurrenceEditData['coordinateuncertaintyinmeters'] = eventData['coordinateuncertaintyinmeters'];
            }
            if(eventData['footprintwkt']){
                this.occurrenceEditData['footprintwkt'] = eventData['footprintwkt'];
            }
            if(eventData['georeferencedby']){
                this.occurrenceEditData['georeferencedby'] = eventData['georeferencedby'];
            }
            if(eventData['georeferenceprotocol']){
                this.occurrenceEditData['georeferenceprotocol'] = eventData['georeferenceprotocol'];
            }
            if(eventData['georeferencesources']){
                this.occurrenceEditData['georeferencesources'] = eventData['georeferencesources'];
            }
            if(eventData['georeferenceverificationstatus']){
                this.occurrenceEditData['georeferenceverificationstatus'] = eventData['georeferenceverificationstatus'];
            }
            if(eventData['georeferenceremarks']){
                this.occurrenceEditData['georeferenceremarks'] = eventData['georeferenceremarks'];
            }
            this.occurrenceEditData['minimumdepthinmeters'] = eventData['minimumdepthinmeters'];
            this.occurrenceEditData['maximumdepthinmeters'] = eventData['maximumdepthinmeters'];
            this.occurrenceEditData['verbatimdepth'] = eventData['verbatimdepth'];
            this.occurrenceEditData['samplingprotocol'] = eventData['samplingprotocol'];
            this.occurrenceEditData['samplingeffort'] = eventData['samplingeffort'];
            this.occurrenceEditData['labelproject'] = eventData['labelproject'];
        },
        mergeLocationOccurrenceData() {
            const locationData = this.getLocationData;
            this.occurrenceEditData['locationid'] = this.getLocationID;
            this.occurrenceEditData['waterbody'] = locationData['waterbody'];
            this.occurrenceEditData['country'] = locationData['country'];
            this.occurrenceEditData['stateprovince'] = locationData['stateprovince'];
            this.occurrenceEditData['county'] = locationData['county'];
            this.occurrenceEditData['municipality'] = locationData['municipality'];
            this.occurrenceEditData['locality'] = locationData['locality'];
            this.occurrenceEditData['localitysecurity'] = locationData['localitysecurity'];
            this.occurrenceEditData['localitysecurityreason'] = locationData['localitysecurityreason'];
            this.occurrenceEditData['decimallatitude'] = locationData['decimallatitude'];
            this.occurrenceEditData['decimallongitude'] = locationData['decimallongitude'];
            this.occurrenceEditData['geodeticdatum'] = locationData['geodeticdatum'];
            this.occurrenceEditData['coordinateuncertaintyinmeters'] = locationData['coordinateuncertaintyinmeters'];
            this.occurrenceEditData['footprintwkt'] = locationData['footprintwkt'];
            this.occurrenceEditData['coordinateprecision'] = locationData['coordinateprecision'];
            this.occurrenceEditData['locationremarks'] = locationData['locationremarks'];
            this.occurrenceEditData['verbatimcoordinates'] = locationData['verbatimcoordinates'];
            this.occurrenceEditData['verbatimcoordinatesystem'] = locationData['verbatimcoordinatesystem'];
            this.occurrenceEditData['georeferencedby'] = locationData['georeferencedby'];
            this.occurrenceEditData['georeferenceprotocol'] = locationData['georeferenceprotocol'];
            this.occurrenceEditData['georeferencesources'] = locationData['georeferencesources'];
            this.occurrenceEditData['georeferenceverificationstatus'] = locationData['georeferenceverificationstatus'];
            this.occurrenceEditData['georeferenceremarks'] = locationData['georeferenceremarks'];
            this.occurrenceEditData['minimumelevationinmeters'] = locationData['minimumelevationinmeters'];
            this.occurrenceEditData['maximumelevationinmeters'] = locationData['maximumelevationinmeters'];
            this.occurrenceEditData['verbatimelevation'] = locationData['verbatimelevation'];
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
            this.collectingEventStore.revertCollectingEventEditData();
        },
        revertLocationEditData() {
            this.locationStore.revertLocationEditData();
        },
        setChecklistArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('action', 'getChecklistListByOccurrenceVoucher');
            fetch(checklistVoucherApiUrl, {
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
            this.collectingEventStore.setCollectingEventBenthicData();
        },
        setCollectingEventCollectionsArr() {
            this.collectingEventStore.setCollectingEventCollectionsArr();
        },
        setCollectingEventFields() {
            this.collectingEventStore.setCollectingEventFields();
        },
        setCollection(collid, callback = null) {
            this.collectionStore.setCollection(collid, () => {
                if(this.getIsEditor){
                    this.occurrenceEntryFormat = this.getCollectionData['datarecordingmethod'];
                    if(this.occurrenceData.hasOwnProperty('occid')){
                        this.occurrenceEditData = Object.assign({}, this.occurrenceData);
                        this.setCurrentLocationRecord(this.occurrenceEditData['locationid'] ? this.occurrenceEditData['locationid'] : 0);
                        this.setCurrentCollectingEventRecord(this.occurrenceEditData['eventid'] ? this.occurrenceEditData['eventid'] : 0);
                    }
                    if(this.getCollectingEventID === 0){
                        this.updateCollectingEventEditData('repcount', (this.getCollectionData['defaultrepcount'] ? Number(this.getCollectionData['defaultrepcount']) : 0))
                    }
                    if(callback){
                        callback();
                    }
                }
                else{
                    window.location.href = this.getClientRoot + '/index.php';
                }
            });
        },
        setCurrentCollectingEventRecord(eventid) {
            this.collectingEventStore.setCurrentCollectingEventRecord(eventid, this.occurrenceEntryFormat, this.getCollectionData['defaultrepcount'], this.getConfiguredDataFields);
        },
        setCurrentDeterminationRecord(detid) {
            this.determinationStore.setCurrentDeterminationRecord(detid);
        },
        setCurrentGeneticLinkageRecord(linkid) {
            this.geneticLinkStore.setCurrentGeneticLinkageRecord(linkid);
        },
        setCurrentLocationRecord(locationid) {
            this.locationStore.setCurrentLocationRecord(locationid, this.getCollId, () => {
                this.updateOccurrenceEditData('locationid', this.getLocationID.toString());
                this.collectingEventStore.getLocationCollectingEvents(this.getCollId, locationid);
            });
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
                if(this.getLocationID > 0){
                    this.mergeLocationOccurrenceData();
                }
                if(this.getCollectingEventID > 0){
                    this.mergeEventOccurrenceData();
                }
                if(callback){
                    callback();
                }
            }
        },
        setDisplayMode(value) {
            this.displayMode = Number(value);
        },
        setEditArr() {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('action', 'getOccurrenceEditArr');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                return response.ok ? response.json() : null;
            })
            .then((data) => {
                this.editArr = data;
            });
        },
        setEntryFollowUpAction(value) {
            this.entryFollowUpAction = value;
        },
        setLocationFields() {
            this.locationStore.setLocationFields();
        },
        setOccurrenceCollectionData() {
            this.occurrenceData['collid'] = this.getCollId;
            this.occurrenceData['basisofrecord'] = this.getCollectionData['colltype'];
        },
        setOccurrenceData(callback) {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('collid', this.getCollId.toString());
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
                            this.setOccurrenceDeterminationArr();
                            this.setEditArr();
                            this.setOccurrenceImageArr();
                            this.setOccurrenceMediaArr();
                            this.setChecklistArr();
                            this.geneticLinkStore.setGeneticLinkArr(this.occId);
                            if(this.getCollId !== Number(this.occurrenceData.collid)){
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
                        else if(this.getCollId > 0){
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
        setOccurrenceDeterminationArr() {
            this.determinationStore.setDeterminationArr(this.occId);
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
        setOccurrenceImageArr() {
            this.imageStore.setImageArr('occid', this.occId);
        },
        setOccurrenceMediaArr() {
            this.mediaStore.setMediaArr('occid', this.occId);
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
            this.occurrenceData['island'] = this.occurrenceEditData['island'];
            this.occurrenceData['islandgroup'] = this.occurrenceEditData['islandgroup'];
            this.occurrenceData['waterbody'] = this.occurrenceEditData['waterbody'];
            this.occurrenceData['continent'] = this.occurrenceEditData['continent'];
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
        transferOccurrenceRecord(transferToCollid, callback) {
            const formData = new FormData();
            formData.append('occid', this.occId.toString());
            formData.append('transferToCollid', transferToCollid);
            formData.append('action', 'transferOccurrenceRecord');
            fetch(occurrenceApiUrl, {
                method: 'POST',
                body: formData
            })
            .then((response) => {
                response.text().then((res) => {
                    callback(res);
                    this.setCurrentOccurrenceRecord(this.occId);
                });
            });
        },
        updateCollectingEventEditData(key, value) {
            this.collectingEventStore.updateCollectingEventEditData(key, value);
        },
        updateCollectingEventRecord(callback) {
            this.collectingEventStore.updateCollectingEventRecord(this.getCollId, callback);
        },
        updateConfiguredDataValue(key, value, callback = null) {
            this.collectingEventStore.updateConfiguredDataValue(this.getCollId, key, value, this.getConfiguredDataFields, callback);
        },
        updateDeterminationEditData(key, value) {
            this.determinationStore.updateDeterminationEditData(key, value);
        },
        updateGeneticLinkageEditData(key, value) {
            this.geneticLinkStore.updateGeneticLinkageEditData(key, value);
        },
        updateLocationEditData(key, value) {
            this.locationStore.updateLocationEditData(key, value);
        },
        updateLocationRecord(callback) {
            this.locationStore.updateLocationRecord(this.getCollId, callback);
        },
        updateOccurrenceDeterminationRecord(callback) {
            const isCurrent = Number(this.determinationStore.getDeterminationData['iscurrent']) === 1;
            this.determinationStore.updateDeterminationRecord(this.getCollId, (res) => {
                callback(Number(res));
                if(isCurrent){
                    this.setCurrentOccurrenceRecord(this.occId);
                }
                else{
                    this.setOccurrenceDeterminationArr();
                }
            });
        },
        updateOccurrenceEditData(key, value) {
            this.occurrenceEditData[key] = value;
            if(key === 'locationid' || (this.getEmbeddedOccurrenceRecord && this.getEventRecordFields.includes(key) && Object.keys(this.getConfiguredDataFields).length > 0)){
                this.updateCollectingEventEditData(key, value);
            }
        },
        updateOccurrenceEditDataDate(dateData) {
            this.occurrenceEditData['eventdate'] = dateData['date'];
            this.occurrenceEditData['year'] = dateData['year'];
            this.occurrenceEditData['month'] = dateData['month'];
            this.occurrenceEditData['day'] = dateData['day'];
            this.occurrenceEditData['startdayofyear'] = dateData['startDayOfYear'];
            this.occurrenceEditData['enddayofyear'] = dateData['endDayOfYear'];

        },
        updateOccurrenceEditDataTaxon(taxon) {
            this.occurrenceEditData['sciname'] = taxon ? taxon.sciname : null;
            this.occurrenceEditData['tid'] = taxon ? taxon.tid : null;
            this.occurrenceEditData['family'] = taxon ? taxon.family : null;
            this.occurrenceEditData['scientificnameauthorship'] = taxon ? taxon.author : null;
            this.occurrenceEditData['taxonData'] = taxon ? Object.assign({}, taxon) : null;
        },
        updateOccurrenceGeneticLinkageRecord(callback) {
            this.geneticLinkStore.updateGeneticLinkageRecord(this.getCollId, (res) => {
                callback(Number(res));
                if(Number(res) === 1){
                    this.geneticLinkStore.setGeneticLinkArr(this.occId);
                }
            });
        },
        updateOccurrenceRecord(callback) {
            const formData = new FormData();
            formData.append('collid', this.getCollId.toString());
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
                        if(this.occurrenceUpdateData.hasOwnProperty('sciname') || this.occurrenceUpdateData.hasOwnProperty('tid')){
                            this.determinationStore.setDeterminationArr(this.occId);
                        }
                        this.occurrenceData = Object.assign({}, this.occurrenceEditData);
                    }
                    if(this.getCollectingEventID > 0 && (this.occurrenceUpdateData.hasOwnProperty('locationid') || this.getEmbeddedOccurrenceRecord) && this.getCollectingEventEditsExist){
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
