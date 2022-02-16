const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmchartaxalink', {
        CID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'kmcharacters',
                key: 'cid'
            }
        },
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        Status: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        Relation: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "include"
        },
        EditabilityInherited: {
            type: DataTypes.BOOLEAN,
            allowNull: true
        },
        timestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmchartaxalink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "CID"},
                    {name: "TID"},
                ]
            },
            {
                name: "FK_CharTaxaLink-TID",
                using: "BTREE",
                fields: [
                    {name: "TID"},
                ]
            },
        ]
    });
};
