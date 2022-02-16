const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxa', {
        TID: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        kingdomName: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        kingdomId: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 100,
            references: {
                model: 'taxonkingdoms',
                key: 'kingdom_id'
            }
        },
        RankId: {
            type: DataTypes.SMALLINT.UNSIGNED,
            allowNull: true
        },
        SciName: {
            type: DataTypes.STRING(250),
            allowNull: false
        },
        UnitInd1: {
            type: DataTypes.STRING(1),
            allowNull: true
        },
        UnitName1: {
            type: DataTypes.STRING(50),
            allowNull: false
        },
        UnitInd2: {
            type: DataTypes.STRING(1),
            allowNull: true
        },
        UnitName2: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        UnitInd3: {
            type: DataTypes.STRING(15),
            allowNull: true
        },
        UnitName3: {
            type: DataTypes.STRING(35),
            allowNull: true
        },
        Author: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        PhyloSortSequence: {
            type: DataTypes.TINYINT.UNSIGNED,
            allowNull: true
        },
        Status: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        Source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Hybrid: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        locked: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        SecurityStatus: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            comment: "0 = no security; 1 = hidden locality"
        },
        modifiedUid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        modifiedTimeStamp: {
            type: DataTypes.DATE,
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxa',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "TID"},
                ]
            },
            {
                name: "sciname_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "SciName"},
                    {name: "RankId"},
                    {name: "Author"},
                ]
            },
            {
                name: "rankid_index",
                using: "BTREE",
                fields: [
                    {name: "RankId"},
                ]
            },
            {
                name: "unitname1_index",
                using: "BTREE",
                fields: [
                    {name: "UnitName1"},
                    {name: "UnitName2"},
                ]
            },
            {
                name: "FK_taxa_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "modifiedUid"},
                ]
            },
            {
                name: "sciname_index",
                using: "BTREE",
                fields: [
                    {name: "SciName"},
                ]
            },
            {
                name: "idx_taxacreated",
                using: "BTREE",
                fields: [
                    {name: "InitialTimeStamp"},
                ]
            },
            {
                name: "kingdomid_index",
                using: "BTREE",
                fields: [
                    {name: "kingdomId"},
                ]
            },
        ]
    });
};
