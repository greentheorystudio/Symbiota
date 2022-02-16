const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcharacters', {
        cid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        charname: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        chartype: {
            type: DataTypes.STRING(2),
            allowNull: false,
            defaultValue: "UM"
        },
        defaultlang: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "English"
        },
        difficultyrank: {
            type: DataTypes.SMALLINT.UNSIGNED,
            allowNull: false,
            defaultValue: 1
        },
        hid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'kmcharheading',
                key: 'hid'
            }
        },
        units: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        display: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        helpurl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        enteredby: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmcharacters',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "cid"},
                ]
            },
            {
                name: "Index_charname",
                using: "BTREE",
                fields: [
                    {name: "charname"},
                ]
            },
            {
                name: "Index_sort",
                using: "BTREE",
                fields: [
                    {name: "sortsequence"},
                ]
            },
            {
                name: "FK_charheading_idx",
                using: "BTREE",
                fields: [
                    {name: "hid"},
                ]
            },
        ]
    });
};
