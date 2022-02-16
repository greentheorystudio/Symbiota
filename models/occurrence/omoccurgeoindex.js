const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurgeoindex', {
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        decimallatitude: {
            type: DataTypes.DOUBLE,
            allowNull: false,
            primaryKey: true
        },
        decimallongitude: {
            type: DataTypes.DOUBLE,
            allowNull: false,
            primaryKey: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurgeoindex',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tid"},
                    {name: "decimallatitude"},
                    {name: "decimallongitude"},
                ]
            },
        ]
    });
};
