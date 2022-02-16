const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmvouchers', {
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        vid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        CLID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        editornotes: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        preferredImage: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        Notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmvouchers',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "vid"},
                ]
            },
            {
                name: "UNIQUE_voucher",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "CLID"},
                    {name: "occid"},
                ]
            },
        ]
    });
};
