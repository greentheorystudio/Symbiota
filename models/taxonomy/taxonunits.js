const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxonunits', {
        taxonunitid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        kingdomid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            references: {
                model: 'taxonkingdoms',
                key: 'kingdom_id'
            }
        },
        rankid: {
            type: DataTypes.SMALLINT.UNSIGNED,
            allowNull: false,
            defaultValue: 0
        },
        rankname: {
            type: DataTypes.STRING(15),
            allowNull: false
        },
        suffix: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        dirparentrankid: {
            type: DataTypes.SMALLINT,
            allowNull: false
        },
        reqparentrankid: {
            type: DataTypes.SMALLINT,
            allowNull: true
        },
        modifiedby: {
            type: DataTypes.STRING(45),
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
        tableName: 'taxonunits',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "taxonunitid"},
                ]
            },
            {
                name: "INDEX-Unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "kingdomid"},
                    {name: "rankid"},
                ]
            },
        ]
    });
};
