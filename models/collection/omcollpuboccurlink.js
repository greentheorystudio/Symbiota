const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollpuboccurlink', {
        pubid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omcollpublications',
                key: 'pubid'
            }
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        verification: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        refreshtimestamp: {
            type: DataTypes.DATE,
            allowNull: false
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollpuboccurlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "pubid"},
                    {name: "occid"},
                ]
            },
            {
                name: "FK_ompuboccid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
