const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referencechklsttaxalink', {
        refid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchklsttaxalink',
                key: 'CLID'
            }
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'fmchklsttaxalink',
                key: 'TID'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referencechklsttaxalink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refid"},
                    {name: "clid"},
                    {name: "tid"},
                ]
            },
            {
                name: "FK_refchktaxalink_clidtid_idx",
                using: "BTREE",
                fields: [
                    {name: "clid"},
                    {name: "tid"},
                ]
            },
        ]
    });
};
