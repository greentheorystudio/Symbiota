const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurassociations', {
        associd: {
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
        occidassociate: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        relationship: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        identifier: {
            type: DataTypes.STRING(250),
            allowNull: true,
            comment: "e.g. GUID"
        },
        basisOfRecord: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        resourceurl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        verbatimsciname: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        locationOnHost: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        condition: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dateEmerged: {
            type: DataTypes.DATE,
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        createduid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        datelastmodified: {
            type: DataTypes.DATE,
            allowNull: true
        },
        modifieduid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurassociations',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "associd"},
                ]
            },
            {
                name: "omossococcur_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "omossococcur_occidassoc_idx",
                using: "BTREE",
                fields: [
                    {name: "occidassociate"},
                ]
            },
            {
                name: "FK_occurassoc_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
            {
                name: "FK_occurassoc_uidmodified_idx",
                using: "BTREE",
                fields: [
                    {name: "modifieduid"},
                ]
            },
            {
                name: "FK_occurassoc_uidcreated_idx",
                using: "BTREE",
                fields: [
                    {name: "createduid"},
                ]
            },
            {
                name: "INDEX_verbatimSciname",
                using: "BTREE",
                fields: [
                    {name: "verbatimsciname"},
                ]
            },
        ]
    });
};
