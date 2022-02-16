const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('tmtraits', {
        traitid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        traitname: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        traittype: {
            type: DataTypes.STRING(2),
            allowNull: false,
            defaultValue: "UM"
        },
        units: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        refurl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
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
        datelastmodified: {
            type: DataTypes.DATE,
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
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'tmtraits',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                ]
            },
            {
                name: "traitsname",
                using: "BTREE",
                fields: [
                    {name: "traitname"},
                ]
            },
            {
                name: "FK_traits_uidcreated_idx",
                using: "BTREE",
                fields: [
                    {name: "createduid"},
                ]
            },
            {
                name: "FK_traits_uidmodified_idx",
                using: "BTREE",
                fields: [
                    {name: "modifieduid"},
                ]
            },
        ]
    });
};
