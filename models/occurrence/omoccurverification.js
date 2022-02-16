const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurverification', {
        ovsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        category: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        ranking: {
            type: DataTypes.INTEGER,
            allowNull: false
        },
        protocol: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
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
        tableName: 'omoccurverification',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ovsid"},
                ]
            },
            {
                name: "UNIQUE_omoccurverification",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "category"},
                ]
            },
            {
                name: "FK_omoccurverification_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_omoccurverification_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
        ]
    });
};
