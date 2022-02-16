const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxadescrstmts', {
        tdsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tdbid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'taxadescrblock',
                key: 'tdbid'
            }
        },
        heading: {
            type: DataTypes.STRING(75),
            allowNull: true
        },
        statement: {
            type: DataTypes.TEXT,
            allowNull: false
        },
        displayheader: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 1
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 89
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxadescrstmts',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tdsid"},
                ]
            },
            {
                name: "FK_taxadescrstmts_tblock",
                using: "BTREE",
                fields: [
                    {name: "tdbid"},
                ]
            },
        ]
    });
};
