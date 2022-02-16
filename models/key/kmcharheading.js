const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcharheading', {
        hid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        headingname: {
            type: DataTypes.STRING(255),
            allowNull: false
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "English"
        },
        langid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'adminlanguages',
                key: 'langid'
            }
        },
        notes: {
            type: DataTypes.TEXT,
            allowNull: true
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
        tableName: 'kmcharheading',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "hid"},
                    {name: "langid"},
                ]
            },
            {
                name: "unique_kmcharheading",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "headingname"},
                    {name: "langid"},
                ]
            },
            {
                name: "HeadingName",
                using: "BTREE",
                fields: [
                    {name: "headingname"},
                ]
            },
            {
                name: "FK_kmcharheading_lang_idx",
                using: "BTREE",
                fields: [
                    {name: "langid"},
                ]
            },
        ]
    });
};
