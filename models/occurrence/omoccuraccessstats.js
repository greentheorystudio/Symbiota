const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccuraccessstats', {
        oasid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        accessdate: {
            type: DataTypes.DATEONLY,
            allowNull: false
        },
        ipaddress: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        cnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        accesstype: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        dynamicProperties: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccuraccessstats',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "oasid"},
                ]
            },
            {
                name: "UNIQUE_occuraccess",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "accessdate"},
                    {name: "ipaddress"},
                    {name: "accesstype"},
                ]
            },
        ]
    });
};
