const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollcategories', {
        ccpk: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        category: {
            type: DataTypes.STRING(75),
            allowNull: false
        },
        icon: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        acronym: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        url: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        inclusive: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
        },
        notes: {
            type: DataTypes.STRING(250),
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
        tableName: 'omcollcategories',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ccpk"},
                ]
            },
        ]
    });
};
