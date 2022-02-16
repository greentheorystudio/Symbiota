const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchecklists', {
        CLID: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        Name: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        Title: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        Locality: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        Publication: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        Abstract: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        Authors: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Type: {
            type: DataTypes.STRING(50),
            allowNull: true,
            defaultValue: "static"
        },
        politicalDivision: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        dynamicsql: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        Parent: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        parentclid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        LatCentroid: {
            type: DataTypes.DOUBLE(9, 6),
            allowNull: true
        },
        LongCentroid: {
            type: DataTypes.DOUBLE(9, 6),
            allowNull: true
        },
        pointradiusmeters: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        footprintWKT: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        percenteffort: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        Access: {
            type: DataTypes.STRING(45),
            allowNull: true,
            defaultValue: "private"
        },
        defaultSettings: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        iconUrl: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        headerUrl: {
            type: DataTypes.STRING(150),
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
        SortSequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 50
        },
        expiration: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        DateLastModified: {
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
        tableName: 'fmchecklists',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "CLID"},
                ]
            },
            {
                name: "FK_checklists_uid",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
            {
                name: "name",
                using: "BTREE",
                fields: [
                    {name: "Name"},
                    {name: "Type"},
                ]
            },
        ]
    });
};
