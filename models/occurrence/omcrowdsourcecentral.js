const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcrowdsourcecentral', {
        omcsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omcollections',
                key: 'CollID'
            },
            unique: "FK_omcrowdsourcecentral_collid"
        },
        instructions: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        trainingurl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        editorlevel: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
            comment: "0=public, 1=public limited, 2=private"
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
        tableName: 'omcrowdsourcecentral',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "omcsid"},
                ]
            },
            {
                name: "Index_omcrowdsourcecentral_collid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
            {
                name: "FK_omcrowdsourcecentral_collid",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
