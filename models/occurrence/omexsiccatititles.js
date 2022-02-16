const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omexsiccatititles', {
        ometid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        title: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        abbreviation: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        editor: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        exsrange: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        startdate: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        enddate: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(2000),
            allowNull: true
        },
        lasteditedby: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omexsiccatititles',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ometid"},
                ]
            },
            {
                name: "index_exsiccatiTitle",
                using: "BTREE",
                fields: [
                    {name: "title"},
                ]
            },
        ]
    });
};
