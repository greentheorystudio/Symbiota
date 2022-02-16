const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurpaleo', {
        paleoID: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            },
            unique: "FK_paleo_occid"
        },
        eon: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        era: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        period: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        epoch: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        earlyInterval: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        lateInterval: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        absoluteAge: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        storageAge: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        stage: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        localStage: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        biota: {
            type: DataTypes.STRING(65),
            allowNull: true,
            comment: "Flora or Fanua"
        },
        biostratigraphy: {
            type: DataTypes.STRING(65),
            allowNull: true,
            comment: "Biozone"
        },
        taxonEnvironment: {
            type: DataTypes.STRING(65),
            allowNull: true,
            comment: "Marine or not"
        },
        lithogroup: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        formation: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        member: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        bed: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        lithology: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        stratRemarks: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        element: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        slideProperties: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        geologicalContextID: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurpaleo',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "paleoID"},
                ]
            },
            {
                name: "UNIQUE_occid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_paleo_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
