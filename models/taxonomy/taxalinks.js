const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxalinks', {
        tlid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        url: {
            type: DataTypes.STRING(500),
            allowNull: false
        },
        title: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        sourceIdentifier: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        owner: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        icon: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        inherit: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 50
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxalinks',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tlid"},
                ]
            },
            {
                name: "Index_unique",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                    {name: "url", length: 255},
                ]
            },
        ]
    });
};
