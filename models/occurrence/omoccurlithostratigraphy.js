const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurlithostratigraphy', {
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        chronoId: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'paleochronostratigraphy',
                key: 'chronoId'
            }
        },
        Group: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        Formation: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        Member: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        Bed: {
            type: DataTypes.STRING(255),
            allowNull: true
        }
    }, {
        sequelize,
        tableName: 'omoccurlithostratigraphy',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "chronoId"},
                ]
            },
            {
                name: "FK_occurlitho_chronoid",
                using: "BTREE",
                fields: [
                    {name: "chronoId"},
                ]
            },
            {
                name: "FK_occurlitho_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Group",
                using: "BTREE",
                fields: [
                    {name: "Group"},
                ]
            },
            {
                name: "Formation",
                using: "BTREE",
                fields: [
                    {name: "Formation"},
                ]
            },
            {
                name: "Member",
                using: "BTREE",
                fields: [
                    {name: "Member"},
                ]
            },
        ]
    });
};
