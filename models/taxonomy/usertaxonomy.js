const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('usertaxonomy', {
        idusertaxonomy: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        taxauthid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 1,
            references: {
                model: 'taxauthority',
                key: 'taxauthid'
            }
        },
        editorstatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        geographicScope: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        modifiedUid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
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
        tableName: 'usertaxonomy',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "idusertaxonomy"},
                ]
            },
            {
                name: "usertaxonomy_UNIQUE",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "uid"},
                    {name: "tid"},
                    {name: "taxauthid"},
                    {name: "editorstatus"},
                ]
            },
            {
                name: "FK_usertaxonomy_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
            {
                name: "FK_usertaxonomy_tid_idx",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
            {
                name: "FK_usertaxonomy_taxauthid_idx",
                using: "BTREE",
                fields: [
                    {name: "taxauthid"},
                ]
            },
        ]
    });
};
