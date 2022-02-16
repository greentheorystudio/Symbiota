const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('guidoccurrences', {
        guid: {
            type: DataTypes.STRING(45),
            allowNull: false,
            primaryKey: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            unique: "guidoccurrences_occid_unique"
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
        tableName: 'guidoccurrences',
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
                name: "guidoccurrences_occid_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
        ]
    });
};
