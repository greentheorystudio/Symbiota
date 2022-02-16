const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('glossarysources', {
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        contributorTerm: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        contributorImage: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        translator: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        additionalSources: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'glossarysources',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
