const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('guidimages', {
        guid: {
            type: DataTypes.STRING(45),
            allowNull: false,
            primaryKey: true
        },
        imgid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            unique: "guidimages_imgid_unique"
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
        tableName: 'guidimages',
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
                name: "guidimages_imgid_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                ]
            },
        ]
    });
};
