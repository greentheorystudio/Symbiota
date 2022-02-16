const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('igsnverification', {
        igsn: {
            type: DataTypes.STRING(15),
            allowNull: false
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        status: {
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
        tableName: 'igsnverification',
        timestamps: false,
        indexes: [
            {
                name: "FK_igsn_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "INDEX_igsn",
                using: "BTREE",
                fields: [
                    {name: "igsn"},
                ]
            },
        ]
    });
};
