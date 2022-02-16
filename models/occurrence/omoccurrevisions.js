const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurrevisions', {
        orid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
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
        oldValues: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        newValues: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        externalSource: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        externalEditor: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        guid: {
            type: DataTypes.STRING(45),
            allowNull: true,
            unique: "guid_UNIQUE"
        },
        reviewStatus: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        appliedStatus: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        errorMessage: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        externalTimestamp: {
            type: DataTypes.DATE,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurrevisions',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "orid"},
                ]
            },
            {
                name: "guid_UNIQUE",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "guid"},
                ]
            },
            {
                name: "fk_omrevisions_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "fk_omrevisions_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
            {
                name: "Index_omrevisions_applied",
                using: "BTREE",
                fields: [
                    {name: "appliedStatus"},
                ]
            },
            {
                name: "Index_omrevisions_reviewed",
                using: "BTREE",
                fields: [
                    {name: "reviewStatus"},
                ]
            },
            {
                name: "Index_omrevisions_source",
                using: "BTREE",
                fields: [
                    {name: "externalSource"},
                ]
            },
            {
                name: "Index_omrevisions_editor",
                using: "BTREE",
                fields: [
                    {name: "externalEditor"},
                ]
            },
        ]
    });
};
