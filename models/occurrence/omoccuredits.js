const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccuredits', {
        ocedid: {
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
        FieldName: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        FieldValueNew: {
            type: DataTypes.TEXT,
            allowNull: false
        },
        FieldValueOld: {
            type: DataTypes.TEXT,
            allowNull: false
        },
        ReviewStatus: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 1,
            comment: "1=Open;2=Pending;3=Closed"
        },
        AppliedStatus: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
            comment: "0=Not Applied;1=Applied"
        },
        editType: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0,
            comment: "0 = general edit, 1 = batch edit"
        },
        guid: {
            type: DataTypes.STRING(45),
            allowNull: true,
            unique: "guid_UNIQUE"
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
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
        tableName: 'omoccuredits',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ocedid"},
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
                name: "fk_omoccuredits_uid",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
            {
                name: "fk_omoccuredits_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
