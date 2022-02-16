const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollcatlink', {
        ccpk: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omcollcategories',
                key: 'ccpk'
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
        isPrimary: {
            type: DataTypes.BOOLEAN,
            allowNull: true,
            defaultValue: 1
        },
        sortsequence: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollcatlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ccpk"},
                    {name: "collid"},
                ]
            },
            {
                name: "FK_collcatlink_coll",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
