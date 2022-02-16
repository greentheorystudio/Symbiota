const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcslang', {
        cid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cid'
            }
        },
        cs: {
            type: DataTypes.STRING(16),
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cs'
            }
        },
        charstatename: {
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
        description: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmcslang',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "cid"},
                    {name: "cs"},
                    {name: "langid"},
                ]
            },
            {
                name: "FK_cslang_lang_idx",
                using: "BTREE",
                fields: [
                    {name: "langid"},
                ]
            },
        ]
    });
};
