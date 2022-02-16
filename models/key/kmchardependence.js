const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmchardependence', {
        CID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcharacters',
                key: 'cid'
            }
        },
        CIDDependance: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cid'
            }
        },
        CSDependance: {
            type: DataTypes.STRING(16),
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cs'
            }
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmchardependence',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "CSDependance"},
                    {name: "CIDDependance"},
                    {name: "CID"},
                ]
            },
            {
                name: "FK_chardependance_cid_idx",
                using: "BTREE",
                fields: [
                    {name: "CID"},
                ]
            },
            {
                name: "FK_chardependance_cs_idx",
                using: "BTREE",
                fields: [
                    {name: "CIDDependance"},
                    {name: "CSDependance"},
                ]
            },
        ]
    });
};
