const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omexsiccatinumbers', {
        omenid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        exsnumber: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        ometid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omexsiccatititles',
                key: 'ometid'
            }
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
        tableName: 'omexsiccatinumbers',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "omenid"},
                ]
            },
            {
                name: "Index_omexsiccatinumbers_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "exsnumber"},
                    {name: "ometid"},
                ]
            },
            {
                name: "FK_exsiccatiTitleNumber",
                using: "BTREE",
                fields: [
                    {name: "ometid"},
                ]
            },
        ]
    });
};
