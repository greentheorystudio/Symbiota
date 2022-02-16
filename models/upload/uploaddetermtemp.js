const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploaddetermtemp', {
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        dbpk: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        identifiedBy: {
            type: DataTypes.STRING(60),
            allowNull: false
        },
        dateIdentified: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        dateIdentifiedInterpreted: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        sciname: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        scientificNameAuthorship: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        identificationQualifier: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        iscurrent: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        detType: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        identificationReferences: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        identificationRemarks: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        sourceIdentifier: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 10
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploaddetermtemp',
        timestamps: false,
        indexes: [
            {
                name: "Index_uploaddet_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Index_uploaddet_collid",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
            {
                name: "Index_uploaddet_dbpk",
                using: "BTREE",
                fields: [
                    {name: "dbpk"},
                ]
            },
        ]
    });
};
