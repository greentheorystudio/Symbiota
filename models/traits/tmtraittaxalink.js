const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('tmtraittaxalink', {
        traitid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'tmtraits',
                key: 'traitid'
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
        relation: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "include"
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'tmtraittaxalink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                    {name: "tid"},
                ]
            },
            {
                name: "FK_traittaxalink_traitid_idx",
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                ]
            },
            {
                name: "FK_traittaxalink_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
