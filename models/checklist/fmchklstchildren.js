const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchklstchildren', {
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchecklists',
                key: 'CLID'
            }
        },
        clidchild: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchecklists',
                key: 'CLID'
            }
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
        tableName: 'fmchklstchildren',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "clid"},
                    {name: "clidchild"},
                ]
            },
            {
                name: "FK_fmchklstchild_clid_idx",
                using: "BTREE",
                fields: [
                    {name: "clid"},
                ]
            },
            {
                name: "FK_fmchklstchild_child_idx",
                using: "BTREE",
                fields: [
                    {name: "clidchild"},
                ]
            },
        ]
    });
};
