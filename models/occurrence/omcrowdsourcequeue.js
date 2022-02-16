const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcrowdsourcequeue', {
        idomcrowdsourcequeue: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        omcsid: {
            type: DataTypes.INTEGER,
            allowNull: false
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            },
            unique: "FK_omcrowdsourcequeue_occid"
        },
        reviewstatus: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0,
            comment: "0=open,5=pending review, 10=closed"
        },
        uidprocessor: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        points: {
            type: DataTypes.INTEGER,
            allowNull: true,
            comment: "0=fail, 1=minor edits, 2=no edits <default>, 3=excelled"
        },
        isvolunteer: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 1
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
        tableName: 'omcrowdsourcequeue',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "idomcrowdsourcequeue"},
                ]
            },
            {
                name: "Index_omcrowdsource_occid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_omcrowdsourcequeue_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_omcrowdsourcequeue_uid",
                using: "BTREE",
                fields: [
                    {name: "uidprocessor"},
                ]
            },
        ]
    });
};
