const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omexsiccatiocclink', {
        omenid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omexsiccatinumbers',
                key: 'omenid'
            }
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        ranking: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 50
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omexsiccatiocclink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "omenid"},
                    {name: "occid"},
                ]
            },
            {
                name: "UniqueOmexsiccatiOccLink",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FKExsiccatiNumOccLink1",
                using: "BTREE",
                fields: [
                    {name: "omenid"},
                ]
            },
            {
                name: "FKExsiccatiNumOccLink2",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
