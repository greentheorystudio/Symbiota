const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadspectemp', {
        upspid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
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
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        basisOfRecord: {
            type: DataTypes.STRING(32),
            allowNull: true,
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
            allowNull: true
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
        recordNumberPrefix: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        recordNumberSuffix: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        recordNumber: {
            type: DataTypes.STRING(32),
            allowNull: true,
            comment: "Collector Number"
        },
        CollectorFamilyName: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "not DwC"
        },
        CollectorInitials: {
            type: DataTypes.STRING(255),
            allowNull: true,
            comment: "not DwC"
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
        LatestDateCollected: {
            type: DataTypes.DATEONLY,
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
        host: {
            type: DataTypes.STRING(250),
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
        associatedMedia: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        associatedReferences: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        associatedSequences: {
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
            allowNull: true,
            comment: "Plant Description?"
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
            type: DataTypes.STRING(32),
            allowNull: true,
            comment: "cultivated, invasive, escaped from captivity, wild, native"
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
            comment: "0 = display locality, 1 = hide locality"
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
        latDeg: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        latMin: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        latSec: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        latNS: {
            type: DataTypes.STRING(3),
            allowNull: true
        },
        lngDeg: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        lngMin: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        lngSec: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        lngEW: {
            type: DataTypes.STRING(3),
            allowNull: true
        },
        verbatimLatitude: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        verbatimLongitude: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        UtmNorthing: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        UtmEasting: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        UtmZoning: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        trsTownship: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        trsRange: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        trsSection: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        trsSectionDetails: {
            type: DataTypes.STRING(45),
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
            type: DataTypes.STRING(255),
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
        elevationNumber: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        elevationUnits: {
            type: DataTypes.STRING(45),
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
            type: DataTypes.STRING(32),
            allowNull: true,
            comment: "Dups to"
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
        exsiccatiIdentifier: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        exsiccatiNumber: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        exsiccatiNotes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        paleoJSON: {
            type: DataTypes.TEXT,
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
        recordEnteredBy: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        duplicateQuantity: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        labelProject: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        processingStatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        tempfield01: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield02: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield03: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield04: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield05: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield06: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield07: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield08: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield09: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield10: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield11: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield12: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield13: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield14: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        tempfield15: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadspectemp',
        hasTrigger: true,
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "upspid"},
                ]
            },
            {
                name: "FK_uploadspectemp_coll",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
            {
                name: "Index_uploadspectemp_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Index_uploadspectemp_dbpk",
                using: "BTREE",
                fields: [
                    {name: "dbpk"},
                ]
            },
            {
                name: "Index_uploadspec_sciname",
                using: "BTREE",
                fields: [
                    {name: "sciname"},
                ]
            },
            {
                name: "Index_uploadspec_catalognumber",
                using: "BTREE",
                fields: [
                    {name: "catalogNumber"},
                ]
            },
            {
                name: "Index_uploadspec_othercatalognumbers",
                using: "BTREE",
                fields: [
                    {name: "otherCatalogNumbers"},
                ]
            },
            {
                name: "Index_decimalLatitude",
                using: "BTREE",
                fields: [
                    {name: "decimalLatitude"},
                ]
            },
            {
                name: "Index_ decimalLongitude",
                using: "BTREE",
                fields: [
                    {name: "decimalLongitude"},
                ]
            },
            {
                name: "Index_ institutionCode",
                using: "BTREE",
                fields: [
                    {name: "institutionCode"},
                ]
            },
        ]
    });
};
