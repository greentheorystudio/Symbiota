const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcharacterlang', {
        cid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcharacters',
                key: 'cid'
            }
        },
        charname: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: false
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
            type: DataTypes.STRING(255),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        helpurl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmcharacterlang',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "cid"},
                    {name: "langid"},
                ]
            },
            {
                name: "FK_charlang_lang_idx",
                using: "BTREE",
                fields: [
                    {name: "langid"},
                ]
            },
        ]
    });
};
