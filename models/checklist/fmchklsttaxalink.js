const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchklsttaxalink', {
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true
        },
        CLID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true
        },
        morphospecies: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "",
            primaryKey: true
        },
        familyoverride: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        Habitat: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Abundance: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(2000),
            allowNull: true
        },
        explicitExclude: {
            type: DataTypes.SMALLINT,
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Nativity: {
            type: DataTypes.STRING(50),
            allowNull: true,
            comment: "native, introducted"
        },
        Endemic: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        invasive: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        internalnotes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmchklsttaxalink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "TID"},
                    {name: "CLID"},
                    {name: "morphospecies"},
                ]
            },
            {
                name: "FK_chklsttaxalink_cid",
                using: "BTREE",
                fields: [
                    {name: "CLID"},
                ]
            },
            {
                name: "FK_chklsttaxalink_tid",
                using: "BTREE",
                fields: [
                    {name: "TID"},
                ]
            },
        ]
    });
};
