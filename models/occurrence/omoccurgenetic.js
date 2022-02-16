const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurgenetic', {
        idoccurgenetic: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        identifier: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        resourcename: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        title: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        locus: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        resourceurl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurgenetic',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "idoccurgenetic"},
                ]
            },
            {
                name: "UNIQUE_omoccurgenetic",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "resourceurl"},
                ]
            },
            {
                name: "FK_omoccurgenetic",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "INDEX_omoccurgenetic_name",
                using: "BTREE",
                fields: [
                    {name: "resourcename"},
                ]
            },
        ]
    });
};
