const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurdeterminations', {
        detid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        identifiedBy: {
            type: DataTypes.STRING(60),
            allowNull: false
        },
        idbyid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omcollectors',
                key: 'recordedById'
            }
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
        tidinterpreted: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
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
        printqueue: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        appliedStatus: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
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
            type: DataTypes.STRING(500),
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
        tableName: 'omoccurdeterminations',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "detid"},
                ]
            },
            {
                name: "Index_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "dateIdentified"},
                    {name: "identifiedBy"},
                    {name: "sciname"},
                ]
            },
            {
                name: "FK_omoccurdets_tid",
                using: "BTREE",
                fields: [
                    {name: "tidinterpreted"},
                ]
            },
            {
                name: "FK_omoccurdets_idby_idx",
                using: "BTREE",
                fields: [
                    {name: "idbyid"},
                ]
            },
            {
                name: "Index_dateIdentInterpreted",
                using: "BTREE",
                fields: [
                    {name: "dateIdentifiedInterpreted"},
                ]
            },
        ]
    });
};
