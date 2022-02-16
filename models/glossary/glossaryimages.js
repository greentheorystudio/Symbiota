const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('glossaryimages', {
        glimgid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        glossid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'glossary',
                key: 'glossid'
            }
        },
        url: {
            type: DataTypes.STRING(255),
            allowNull: false
        },
        thumbnailurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        structures: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        createdBy: {
            type: DataTypes.STRING(250),
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
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'glossaryimages',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "glimgid"},
                ]
            },
            {
                name: "FK_glossaryimages_gloss",
                using: "BTREE",
                fields: [
                    {name: "glossid"},
                ]
            },
            {
                name: "FK_glossaryimages_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
        ]
    });
};
