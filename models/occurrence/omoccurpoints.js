const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurpoints', {
        geoID: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            unique: "occid"
        },
        point: {
            type: "POINT",
            allowNull: false
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurpoints',
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
                name: "occid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
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
