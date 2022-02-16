const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxstatus', {
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        tidaccepted: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        parenttid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        hierarchystr: {
            type: DataTypes.STRING(200),
            allowNull: true
        },
        family: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        UnacceptabilityReason: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        SortSequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 50
        },
        modifiedBy: {
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
        tableName: 'taxstatus',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tid"},
                    {name: "tidaccepted"},
                ]
            },
            {
                name: "FK_taxstatus_tidacc",
                using: "BTREE",
                fields: [
                    {name: "tidaccepted"},
                ]
            },
            {
                name: "Index_ts_family",
                using: "BTREE",
                fields: [
                    {name: "family"},
                ]
            },
            {
                name: "Index_parenttid",
                using: "BTREE",
                fields: [
                    {name: "parenttid"},
                ]
            },
            {
                name: "Index_tid",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
