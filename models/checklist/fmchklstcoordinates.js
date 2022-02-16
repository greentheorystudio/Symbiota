const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchklstcoordinates', {
        chklstcoordid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        decimallatitude: {
            type: DataTypes.DOUBLE,
            allowNull: false
        },
        decimallongitude: {
            type: DataTypes.DOUBLE,
            allowNull: false
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
        tableName: 'fmchklstcoordinates',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "chklstcoordid"},
                ]
            },
            {
                name: "FKchklsttaxalink",
                using: "BTREE",
                fields: [
                    {name: "clid"},
                    {name: "tid"},
                ]
            },
        ]
    });
};
