const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('guidoccurdeterminations', {
        guid: {
            type: DataTypes.STRING(45),
            allowNull: false,
            primaryKey: true
        },
        detid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            unique: "guidoccurdet_detid_unique"
        },
        archivestatus: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        archiveobj: {
            type: DataTypes.TEXT,
            allowNull: true
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
        tableName: 'guidoccurdeterminations',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "guid"},
                ]
            },
            {
                name: "guidoccurdet_detid_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "detid"},
                ]
            },
        ]
    });
};
