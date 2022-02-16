const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referenceauthors', {
        refauthorid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        lastname: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        firstname: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        middlename: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        modifieduid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        modifiedtimestamp: {
            type: DataTypes.DATE,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referenceauthors',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refauthorid"},
                ]
            },
            {
                name: "INDEX_refauthlastname",
                using: "BTREE",
                fields: [
                    {name: "lastname"},
                ]
            },
        ]
    });
};
