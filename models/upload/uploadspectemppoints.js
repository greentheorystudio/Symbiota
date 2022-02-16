const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadspectemppoints', {
        geoID: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        upspid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            unique: "upspid"
        },
        point: {
            type: "POINT",
            allowNull: false
        }
    }, {
        sequelize,
        tableName: 'uploadspectemppoints',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "geoID"},
                ]
            },
            {
                name: "upspid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "upspid"},
                ]
            },
            {
                name: "point",
                type: "SPATIAL",
                fields: [
                    {name: "point", length: 32},
                ]
            },
        ]
    });
};
