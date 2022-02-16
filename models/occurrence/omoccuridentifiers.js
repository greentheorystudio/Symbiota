const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccuridentifiers', {
        idomoccuridentifiers: {
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
        identifiervalue: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        identifiername: {
            type: DataTypes.STRING(45),
            allowNull: true,
            comment: "barcode, accession number, old catalog number, NPS, etc"
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        modifiedUid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        modifiedtimestamp: {
            type: DataTypes.DATE,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccuridentifiers',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "idomoccuridentifiers"},
                ]
            },
            {
                name: "FK_omoccuridentifiers_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Index_value",
                using: "BTREE",
                fields: [
                    {name: "identifiervalue"},
                ]
            },
        ]
    });
};
