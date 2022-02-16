const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('media', {
        mediaid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        accessuri: {
            type: DataTypes.STRING(2048),
            allowNull: false
        },
        title: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        creatoruid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        creator: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        type: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        format: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        owner: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        furtherinformationurl: {
            type: DataTypes.STRING(2048),
            allowNull: true
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        usageterms: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        rights: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        bibliographiccitation: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        publisher: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        contributor: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        locationcreated: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'media',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "mediaid"},
                ]
            },
            {
                name: "FK_media_taxa_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
            {
                name: "FK_media_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_media_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "creatoruid"},
                ]
            },
            {
                name: "INDEX_format",
                using: "BTREE",
                fields: [
                    {name: "format"},
                ]
            },
        ]
    });
};
