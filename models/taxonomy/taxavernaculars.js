const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxavernaculars', {
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        VernacularName: {
            type: DataTypes.STRING(80),
            allowNull: false
        },
        Language: {
            type: DataTypes.STRING(15),
            allowNull: true
        },
        langid: {
            type: DataTypes.INTEGER,
            allowNull: true,
            references: {
                model: 'adminlanguages',
                key: 'langid'
            }
        },
        Source: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        username: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        isupperterm: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        SortSequence: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 50
        },
        VID: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxavernaculars',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "VID"},
                ]
            },
            {
                name: "unique-key",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "VernacularName"},
                    {name: "TID"},
                    {name: "langid"},
                ]
            },
            {
                name: "tid1",
                using: "BTREE",
                fields: [
                    {name: "TID"},
                ]
            },
            {
                name: "vernacularsnames",
                using: "BTREE",
                fields: [
                    {name: "VernacularName"},
                ]
            },
            {
                name: "FK_vern_lang_idx",
                using: "BTREE",
                fields: [
                    {name: "langid"},
                ]
            },
        ]
    });
};
