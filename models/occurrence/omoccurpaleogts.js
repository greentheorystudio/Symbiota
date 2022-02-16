const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurpaleogts', {
        gtsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        gtsterm: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        rankid: {
            type: DataTypes.INTEGER,
            allowNull: false
        },
        rankname: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        parentgtsid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurpaleogts',
                key: 'gtsid'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurpaleogts',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "gtsid"},
                ]
            },
            {
                name: "UNIQUE_gtsterm",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "gtsid"},
                ]
            },
            {
                name: "FK_gtsparent_idx",
                using: "BTREE",
                fields: [
                    {name: "parentgtsid"},
                ]
            },
        ]
    });
};
