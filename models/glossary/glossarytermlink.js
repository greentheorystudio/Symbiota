const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('glossarytermlink', {
        gltlinkid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        glossgrpid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'glossary',
                key: 'glossid'
            }
        },
        glossid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'glossary',
                key: 'glossid'
            }
        },
        relationshipType: {
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
        tableName: 'glossarytermlink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "gltlinkid"},
                ]
            },
            {
                name: "Unique_termkeys",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "glossgrpid"},
                    {name: "glossid"},
                ]
            },
            {
                name: "glossarytermlink_ibfk_1",
                using: "BTREE",
                fields: [
                    {name: "glossid"},
                ]
            },
        ]
    });
};
