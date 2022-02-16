const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurrences', {
        occid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        dbpk: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        basisOfRecord: {
            type: DataTypes.STRING(32),
            allowNull: true,
            defaultValue: "PreservedSpecimen",
            comment: "PreservedSpecimen, LivingSpecimen, HumanObservation"
        },
        occurrenceID: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "UniqueGlobalIdentifier"
        },
        catalogNumber: {
            type: DataTypes.STRING(32),
            allowNull: true
        },
        otherCatalogNumbers: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        ownerInstitutionCode: {
            type: DataTypes.STRING(32),
            allowNull: true
        },
        institutionID: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        collectionID: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        datasetID: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        institutionCode: {
            type: DataTypes.STRING(64),
            allowNull: true
        },
        collectionCode: {
            type: DataTypes.STRING(64),
            allowNull: true
        },
        family: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        scientificName: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        sciname: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        tidinterpreted: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        genus: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        specificEpithet: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        taxonRank: {
            type: DataTypes.STRING(32),
            allowNull: true
        },
        infraspecificEpithet: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        scientificNameAuthorship: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        taxonRemarks: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        identifiedBy: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        dateIdentified: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        identificationReferences: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        identificationRemarks: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        identificationQualifier: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "cf, aff, etc"
        },
        typeStatus: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        recordedBy: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "Collector(s)"
        },
        recordNumber: {
            type: DataTypes.STRING(45),
            allowNull: true,
            comment: "Collector Number"
        },
        recordedbyid: {
            type: DataTypes.BIGINT,
            allowNull: true
        },
        associatedCollectors: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "not DwC"
        },
        eventDate: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        latestDateCollected: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        year: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        month: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        day: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        startDayOfYear: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        endDayOfYear: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        verbatimEventDate: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        habitat: {
            type: DataTypes.TEXT,
            allowNull: true,
            comment: "Habitat, substrait, etc"
        },
        substrate: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        fieldNotes: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        fieldnumber: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        eventID: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        occurrenceRemarks: {
            type: DataTypes.TEXT,
            allowNull: true,
            comment: "General Notes"
        },
        informationWithheld: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dataGeneralizations: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        associatedOccurrences: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        associatedTaxa: {
            type: DataTypes.TEXT,
            allowNull: true,
            comment: "Associated Species"
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        verbatimAttributes: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        behavior: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        reproductiveCondition: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "Phenology: flowers, fruit, sterile"
        },
        cultivationStatus: {
            type: DataTypes.INTEGER,
            allowNull: true,
            comment: "0 = wild, 1 = cultivated"
        },
        establishmentMeans: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        lifeStage: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sex: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        individualCount: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        samplingProtocol: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        samplingEffort: {
            type: DataTypes.STRING(200),
            allowNull: true
        },
        preparations: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        locationID: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        waterBody: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        country: {
            type: DataTypes.STRING(64),
            allowNull: true
        },
        stateProvince: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        county: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        municipality: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        locality: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        localitySecurity: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0,
            comment: "0 = no security; 1 = hidden locality"
        },
        localitySecurityReason: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        decimalLatitude: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        decimalLongitude: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        geodeticDatum: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        coordinateUncertaintyInMeters: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        footprintWKT: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        coordinatePrecision: {
            type: DataTypes.DECIMAL(9, 7),
            allowNull: true
        },
        locationRemarks: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        verbatimCoordinates: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        verbatimCoordinateSystem: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        georeferencedBy: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        georeferenceProtocol: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        georeferenceSources: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        georeferenceVerificationStatus: {
            type: DataTypes.STRING(32),
            allowNull: true
        },
        georeferenceRemarks: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        minimumElevationInMeters: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        maximumElevationInMeters: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        verbatimElevation: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        minimumDepthInMeters: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        maximumDepthInMeters: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        verbatimDepth: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        previousIdentifications: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        disposition: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        storageLocation: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        genericcolumn1: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        genericcolumn2: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        modified: {
            type: DataTypes.DATE,
            allowNull: true,
            comment: "DateLastModified"
        },
        language: {
            type: DataTypes.STRING(20),
            allowNull: true
        },
        observeruid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        processingstatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        recordEnteredBy: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        duplicateQuantity: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        labelProject: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dynamicFields: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        dateEntered: {
            type: DataTypes.DATE,
            allowNull: true
        },
        dateLastModified: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurrences',
        hasTrigger: true,
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Index_collid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "collid"},
                    {name: "dbpk"},
                ]
            },
            {
                name: "Index_sciname",
                using: "BTREE",
                fields: [
                    {name: "sciname"},
                ]
            },
            {
                name: "Index_family",
                using: "BTREE",
                fields: [
                    {name: "family"},
                ]
            },
            {
                name: "Index_country",
                using: "BTREE",
                fields: [
                    {name: "country"},
                ]
            },
            {
                name: "Index_state",
                using: "BTREE",
                fields: [
                    {name: "stateProvince"},
                ]
            },
            {
                name: "Index_county",
                using: "BTREE",
                fields: [
                    {name: "county"},
                ]
            },
            {
                name: "Index_collector",
                using: "BTREE",
                fields: [
                    {name: "recordedBy"},
                ]
            },
            {
                name: "Index_gui",
                using: "BTREE",
                fields: [
                    {name: "occurrenceID"},
                ]
            },
            {
                name: "Index_ownerInst",
                using: "BTREE",
                fields: [
                    {name: "ownerInstitutionCode"},
                ]
            },
            {
                name: "FK_omoccurrences_tid",
                using: "BTREE",
                fields: [
                    {name: "tidinterpreted"},
                ]
            },
            {
                name: "FK_omoccurrences_uid",
                using: "BTREE",
                fields: [
                    {name: "observeruid"},
                ]
            },
            {
                name: "Index_municipality",
                using: "BTREE",
                fields: [
                    {name: "municipality"},
                ]
            },
            {
                name: "Index_collnum",
                using: "BTREE",
                fields: [
                    {name: "recordNumber"},
                ]
            },
            {
                name: "Index_catalognumber",
                using: "BTREE",
                fields: [
                    {name: "catalogNumber"},
                ]
            },
            {
                name: "FK_recordedbyid",
                using: "BTREE",
                fields: [
                    {name: "recordedbyid"},
                ]
            },
            {
                name: "Index_eventDate",
                using: "BTREE",
                fields: [
                    {name: "eventDate"},
                ]
            },
            {
                name: "Index_occurrences_procstatus",
                using: "BTREE",
                fields: [
                    {name: "processingstatus"},
                ]
            },
            {
                name: "occelevmax",
                using: "BTREE",
                fields: [
                    {name: "maximumElevationInMeters"},
                ]
            },
            {
                name: "occelevmin",
                using: "BTREE",
                fields: [
                    {name: "minimumElevationInMeters"},
                ]
            },
            {
                name: "Index_occurrences_cult",
                using: "BTREE",
                fields: [
                    {name: "cultivationStatus"},
                ]
            },
            {
                name: "Index_occurrences_typestatus",
                using: "BTREE",
                fields: [
                    {name: "typeStatus"},
                ]
            },
            {
                name: "Index_occurDateLastModifed",
                using: "BTREE",
                fields: [
                    {name: "dateLastModified"},
                ]
            },
            {
                name: "Index_occurDateEntered",
                using: "BTREE",
                fields: [
                    {name: "dateEntered"},
                ]
            },
            {
                name: "Index_occurRecordEnteredBy",
                using: "BTREE",
                fields: [
                    {name: "recordEnteredBy"},
                ]
            },
            {
                name: "Index_locality",
                using: "BTREE",
                fields: [
                    {name: "locality", length: 100},
                ]
            },
            {
                name: "Index_otherCatalogNumbers",
                using: "BTREE",
                fields: [
                    {name: "otherCatalogNumbers"},
                ]
            },
            {
                name: "Index_latestDateCollected",
                using: "BTREE",
                fields: [
                    {name: "latestDateCollected"},
                ]
            },
            {
                name: "Index_occurrenceRemarks",
                using: "BTREE",
                fields: [
                    {name: "occurrenceRemarks", length: 100},
                ]
            },
            {
                name: "Index_locationID",
                using: "BTREE",
                fields: [
                    {name: "locationID"},
                ]
            },
            {
                name: "Index_eventID",
                using: "BTREE",
                fields: [
                    {name: "eventID"},
                ]
            },
            {
                name: "Index_occur_localitySecurity",
                using: "BTREE",
                fields: [
                    {name: "localitySecurity"},
                ]
            },
            {
                name: "Index_latlng",
                using: "BTREE",
                fields: [
                    {name: "decimalLatitude"},
                    {name: "decimalLongitude"},
                ]
            },
            {
                name: "Index_ labelProject",
                using: "BTREE",
                fields: [
                    {name: "labelProject"},
                ]
            },
        ]
    });
};
