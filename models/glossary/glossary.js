const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('glossary', {
        glossid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        term: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        definition: {
            type: DataTypes.STRING(2000),
            allowNull: true
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "English"
        },
        source: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        translator: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        author: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        resourceurl: {
            type: DataTypes.STRING(600),
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
        tableName: 'glossary',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "glossid"},
                ]
            },
            {
                name: "Index_term",
                using: "BTREE",
                fields: [
                    {name: "term"},
                ]
            },
            {
                name: "Index_glossary_lang",
                using: "BTREE",
                fields: [
                    {name: "language"},
                ]
            },
            {
                name: "FK_glossary_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
        ]
    });
};
