const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('specprococrfrag', {
        ocrfragid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        prlid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'specprocessorrawlabels',
                key: 'prlid'
            }
        },
        firstword: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        secondword: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        keyterm: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        wordorder: {
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
        tableName: 'specprococrfrag',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ocrfragid"},
                ]
            },
            {
                name: "FK_specprococrfrag_prlid_idx",
                using: "BTREE",
                fields: [
                    {name: "prlid"},
                ]
            },
            {
                name: "Index_keyterm",
                using: "BTREE",
                fields: [
                    {name: "keyterm"},
                ]
            },
        ]
    });
};
