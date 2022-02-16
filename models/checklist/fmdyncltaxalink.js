const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmdyncltaxalink', {
        dynclid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmdynamicchecklists',
                key: 'dynclid'
            }
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmdyncltaxalink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "dynclid"},
                    {name: "tid"},
                ]
            },
            {
                name: "FK_dyncltaxalink_taxa",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
