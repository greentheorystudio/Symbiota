const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referenceauthorlink', {
        refid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        refauthid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'referenceauthors',
                key: 'refauthorid'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referenceauthorlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refid"},
                    {name: "refauthid"},
                ]
            },
            {
                name: "FK_refauthlink_refid_idx",
                using: "BTREE",
                fields: [
                    {name: "refid"},
                ]
            },
            {
                name: "FK_refauthlink_refauthid_idx",
                using: "BTREE",
                fields: [
                    {name: "refauthid"},
                ]
            },
        ]
    });
};
