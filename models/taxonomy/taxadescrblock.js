const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxadescrblock', {
        tdbid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        caption: {
            type: DataTypes.STRING(40),
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sourceurl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: true,
            defaultValue: "English"
        },
        langid: {
            type: DataTypes.INTEGER,
            allowNull: true,
            references: {
                model: 'adminlanguages',
                key: 'langid'
            }
        },
        displaylevel: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 1,
            comment: "1 = short descr, 2 = intermediate descr"
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxadescrblock',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tdbid"},
                ]
            },
            {
                name: "FK_taxadesc_lang_idx",
                using: "BTREE",
                fields: [
                    {name: "langid"},
                ]
            },
            {
                name: "FK_taxadescrblock_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
