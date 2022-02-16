const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const SchemaVersion = sequelize.define('schemaversion', {
    id: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    versionnumber: {
        type: Sequelize.DataTypes.STRING(20),
        allowNull: false,
        unique: "versionnumber_UNIQUE"
    },
    dateapplied: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'schemaversion',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "id"},
            ]
        },
        {
            name: "versionnumber_UNIQUE",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "versionnumber"},
            ]
        },
    ]
});

module.exports = SchemaVersion;
