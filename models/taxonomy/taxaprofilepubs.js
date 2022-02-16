const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxaprofilepubs', {
        tppid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        pubtitle: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        authors: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        abstract: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        uidowner: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        externalurl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        rights: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        usageterm: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        accessrights: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        ispublic: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        inclusive: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxaprofilepubs',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tppid"},
                ]
            },
            {
                name: "FK_taxaprofilepubs_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uidowner"},
                ]
            },
            {
                name: "INDEX_taxaprofilepubs_title",
                using: "BTREE",
                fields: [
                    {name: "pubtitle"},
                ]
            },
        ]
    });
};
