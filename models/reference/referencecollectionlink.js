const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referencecollectionlink', {
        refid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referencecollectionlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refid"},
                    {name: "collid"},
                ]
            },
            {
                name: "FK_refcollectionlink_collid_idx",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
