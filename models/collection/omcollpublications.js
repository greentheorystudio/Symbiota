const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollpublications', {
        pubid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        targeturl: {
            type: DataTypes.STRING(250),
            allowNull: false
        },
        securityguid: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        criteriajson: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        includedeterminations: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
        },
        includeimages: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
        },
        autoupdate: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        lastdateupdate: {
            type: DataTypes.DATE,
            allowNull: true
        },
        updateinterval: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollpublications',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "pubid"},
                ]
            },
            {
                name: "FK_adminpub_collid_idx",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
