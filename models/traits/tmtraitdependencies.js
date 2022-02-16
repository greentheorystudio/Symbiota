const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('tmtraitdependencies', {
        traitid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'tmtraits',
                key: 'traitid'
            }
        },
        parentstateid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'tmstates',
                key: 'stateid'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'tmtraitdependencies',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                    {name: "parentstateid"},
                ]
            },
            {
                name: "FK_tmdepend_traitid_idx",
                using: "BTREE",
                fields: [
                    {name: "traitid"},
                ]
            },
            {
                name: "FK_tmdepend_stateid_idx",
                using: "BTREE",
                fields: [
                    {name: "parentstateid"},
                ]
            },
        ]
    });
};
