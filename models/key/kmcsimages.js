const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmcsimages', {
        csimgid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        cid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'kmcs',
                key: 'cid'
            }
        },
        cs: {
            type: DataTypes.STRING(16),
            allowNull: false,
            references: {
                model: 'kmcs',
                key: 'cs'
            }
        },
        url: {
            type: DataTypes.STRING(255),
            allowNull: false
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "50"
        },
        username: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmcsimages',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "csimgid"},
                ]
            },
            {
                name: "FK_kscsimages_kscs_idx",
                using: "BTREE",
                fields: [
                    {name: "cid"},
                    {name: "cs"},
                ]
            },
        ]
    });
};
