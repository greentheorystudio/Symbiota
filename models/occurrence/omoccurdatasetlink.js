const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurdatasetlink', {
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        datasetid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurdatasets',
                key: 'datasetid'
            }
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
        tableName: 'omoccurdatasetlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "datasetid"},
                ]
            },
            {
                name: "FK_omoccurdatasetlink_datasetid",
                using: "BTREE",
                fields: [
                    {name: "datasetid"},
                ]
            },
            {
                name: "FK_omoccurdatasetlink_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
