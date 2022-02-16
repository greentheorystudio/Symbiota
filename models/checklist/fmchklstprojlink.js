const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchklstprojlink', {
        pid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmprojects',
                key: 'pid'
            }
        },
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchecklists',
                key: 'CLID'
            }
        },
        clNameOverride: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        mapChecklist: {
            type: DataTypes.SMALLINT,
            allowNull: true,
            defaultValue: 1
        },
        sortSequence: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmchklstprojlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "pid"},
                    {name: "clid"},
                ]
            },
            {
                name: "FK_chklst",
                using: "BTREE",
                fields: [
                    {name: "clid"},
                ]
            },
        ]
    });
};
