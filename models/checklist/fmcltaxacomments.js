const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmcltaxacomments', {
        cltaxacommentsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false
        },
        comment: {
            type: DataTypes.TEXT,
            allowNull: false
        },
        uid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        ispublic: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 1
        },
        parentid: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmcltaxacomments',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "cltaxacommentsid"},
                ]
            },
            {
                name: "FK_clcomment_users",
                using: "BTREE",
                fields: [
                    {name: "uid"},
                ]
            },
        ]
    });
};
