const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referencechecklistlink', {
        refid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchecklists',
                key: 'CLID'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referencechecklistlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refid"},
                    {name: "clid"},
                ]
            },
            {
                name: "FK_refcheckllistlink_refid_idx",
                using: "BTREE",
                fields: [
                    {name: "refid"},
                ]
            },
            {
                name: "FK_refcheckllistlink_clid_idx",
                using: "BTREE",
                fields: [
                    {name: "clid"},
                ]
            },
        ]
    });
};
