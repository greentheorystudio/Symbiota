const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxaresourcelinks', {
        taxaresourceid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        sourcename: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        sourceidentifier: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sourceguid: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        url: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        ranking: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxaresourcelinks',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "taxaresourceid"},
                ]
            },
            {
                name: "UNIQUE_taxaresource",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tid"},
                    {name: "sourcename"},
                ]
            },
            {
                name: "taxaresource_name",
                using: "BTREE",
                fields: [
                    {name: "sourcename"},
                ]
            },
            {
                name: "FK_taxaresource_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
