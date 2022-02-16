const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollections', {
        CollID: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        InstitutionCode: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        CollectionCode: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        CollectionName: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        collectionId: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        datasetID: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        datasetName: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        iid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'institutions',
                key: 'iid'
            }
        },
        fulldescription: {
            type: DataTypes.STRING(2000),
            allowNull: true
        },
        Homepage: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        IndividualUrl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        Contact: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        email: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        contactJson: {
            type: DataTypes.JSON,
            allowNull: true
        },
        latitudedecimal: {
            type: DataTypes.DECIMAL(8, 6),
            allowNull: true
        },
        longitudedecimal: {
            type: DataTypes.DECIMAL(9, 6),
            allowNull: true
        },
        icon: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        CollType: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "Preserved Specimens",
            comment: "Preserved Specimens, General Observations, Observations"
        },
        ManagementType: {
            type: DataTypes.STRING(45),
            allowNull: true,
            defaultValue: "Snapshot",
            comment: "Snapshot, Live Data"
        },
        PublicEdits: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 1
        },
        collectionguid: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        securitykey: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        guidtarget: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        rightsHolder: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        rights: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        usageTerm: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        publishToGbif: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        publishToIdigbio: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        aggKeysStr: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        dwcaUrl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        bibliographicCitation: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        accessrights: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        SortSeq: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollections',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "CollID"},
                ]
            },
            {
                name: "Index_inst",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "InstitutionCode"},
                    {name: "CollectionCode"},
                ]
            },
            {
                name: "FK_collid_iid_idx",
                using: "BTREE",
                fields: [
                    {name: "iid"},
                ]
            },
        ]
    });
};
