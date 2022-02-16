const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('tmstates', {
        stateid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        traitid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'tmtraits',
                key: 'traitid'
            }
        },
        statecode: {
            type: DataTypes.STRING(2),
            allowNull: false
        },
        statename: {
            type: DataTypes.STRING(75),
            allowNull: false
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
        sortseq: {
            type: DataTypes.INTEGER,
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
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'tmstates',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "stateid"},
                ]
            },
            {
                name: "traitid_code_UNIQUE",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                    {name: "statecode"},
                ]
            },
            {
                name: "FK_tmstate_uidcreated_idx",
                using: "BTREE",
                fields: [
                    {name: "createduid"},
                ]
            },
            {
                name: "FK_tmstate_uidmodified_idx",
                using: "BTREE",
                fields: [
                    {name: "modifieduid"},
                ]
            },
        ]
    });
};
