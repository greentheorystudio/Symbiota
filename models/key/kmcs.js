const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcs', {
        cid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'kmcharacters',
                key: 'cid'
            }
        },
        cs: {
            type: DataTypes.STRING(16),
            allowNull: false,
            primaryKey: true
        },
        CharStateName: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        Implicit: {
            type: DataTypes.BOOLEAN,
            allowNull: false,
            defaultValue: 0
        },
        Notes: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        Description: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        IllustrationUrl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        StateID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        SortSequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        },
        EnteredBy: {
            type: DataTypes.STRING(45),
            allowNull: true
        }
    }, {
        sequelize,
        tableName: 'kmcs',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "cs"},
                    {name: "cid"},
                ]
            },
            {
                name: "FK_cs_chars",
                using: "BTREE",
                fields: [
                    {name: "cid"},
                ]
            },
        ]
    });
};
