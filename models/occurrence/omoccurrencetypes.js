const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurrencetypes', {
        occurtypeid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        typestatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        typeDesignationType: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        typeDesignatedBy: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        scientificName: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        scientificNameAuthorship: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        tidinterpreted: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        basionym: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        refid: {
            type: DataTypes.INTEGER,
            allowNull: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        bibliographicCitation: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurrencetypes',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occurtypeid"},
                ]
            },
            {
                name: "FK_occurtype_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_occurtype_refid_idx",
                using: "BTREE",
                fields: [
                    {name: "refid"},
                ]
            },
            {
                name: "FK_occurtype_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tidinterpreted"},
                ]
            },
        ]
    });
};
