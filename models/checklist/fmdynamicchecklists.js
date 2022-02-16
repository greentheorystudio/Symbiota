const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmdynamicchecklists', {
        dynclid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        name: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        details: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        uid: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        type: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "DynamicList"
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        expiration: {
            type: DataTypes.DATE,
            allowNull: false
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmdynamicchecklists',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "dynclid"},
                ]
            },
        ]
    });
};
