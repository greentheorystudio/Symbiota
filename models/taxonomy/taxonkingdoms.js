const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxonkingdoms', {
        kingdom_id: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        kingdom_name: {
            type: DataTypes.STRING(250),
            allowNull: false
        }
    }, {
        sequelize,
        tableName: 'taxonkingdoms',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "kingdom_id"},
                ]
            },
            {
                name: "INDEX_kingdom_name",
                using: "BTREE",
                fields: [
                    {name: "kingdom_name"},
                ]
            },
            {
                name: "INDEX_kingdoms",
                using: "BTREE",
                fields: [
                    {name: "kingdom_id"},
                    {name: "kingdom_name"},
                ]
            },
        ]
    });
};
