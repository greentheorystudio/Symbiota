const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadtaxa', {
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        SourceId: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        Family: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        kingdomId: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        kingdomName: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        RankId: {
            type: DataTypes.SMALLINT,
            allowNull: true
        },
        RankName: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        scinameinput: {
            type: DataTypes.STRING(250),
            allowNull: false
        },
        SciName: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        UnitInd1: {
            type: DataTypes.STRING(1),
            allowNull: true
        },
        UnitName1: {
            type: DataTypes.STRING(50),
            allowNull: true
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
            type: DataTypes.STRING(45),
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
        InfraAuthor: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        Acceptance: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 1,
            comment: "0 = not accepted; 1 = accepted"
        },
        TidAccepted: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        AcceptedStr: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        SourceAcceptedId: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        UnacceptabilityReason: {
            type: DataTypes.STRING(24),
            allowNull: true
        },
        ParentTid: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        ParentStr: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        SourceParentId: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        SecurityStatus: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            comment: "0 = no security; 1 = hidden locality"
        },
        Source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        vernacular: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        vernlang: {
            type: DataTypes.STRING(15),
            allowNull: true
        },
        Hybrid: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        ErrorStatus: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadtaxa',
        timestamps: false,
        indexes: [
            {
                name: "UNIQUE_sciname",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "SciName"},
                    {name: "RankId"},
                    {name: "Author"},
                    {name: "AcceptedStr"},
                ]
            },
            {
                name: "sourceID_index",
                using: "BTREE",
                fields: [
                    {name: "SourceId"},
                ]
            },
            {
                name: "sourceAcceptedId_index",
                using: "BTREE",
                fields: [
                    {name: "SourceAcceptedId"},
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
                name: "scinameinput_index",
                using: "BTREE",
                fields: [
                    {name: "scinameinput"},
                ]
            },
            {
                name: "parentStr_index",
                using: "BTREE",
                fields: [
                    {name: "ParentStr"},
                ]
            },
            {
                name: "acceptedStr_index",
                using: "BTREE",
                fields: [
                    {name: "AcceptedStr"},
                ]
            },
            {
                name: "unitname1_index",
                using: "BTREE",
                fields: [
                    {name: "UnitName1"},
                ]
            },
            {
                name: "sourceParentId_index",
                using: "BTREE",
                fields: [
                    {name: "SourceParentId"},
                ]
            },
            {
                name: "acceptance_index",
                using: "BTREE",
                fields: [
                    {name: "Acceptance"},
                ]
            },
            {
                name: "kingdomId_index",
                using: "BTREE",
                fields: [
                    {name: "kingdomId"},
                ]
            },
            {
                name: "kingdomName_index",
                using: "BTREE",
                fields: [
                    {name: "kingdomName"},
                ]
            },
        ]
    });
};
