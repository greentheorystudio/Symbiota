const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const Configurations = sequelize.define('configurations', {
    id: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    configurationName: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: false,
        unique: "configurationname"
    },
    configurationDataType: {
        type: Sequelize.DataTypes.STRING(15),
        allowNull: false,
        defaultValue: "string"
    },
    configurationValue: {
        type: Sequelize.DataTypes.TEXT,
        allowNull: false
    },
    dateApplied: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'configurations',
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
            name: "configurationname",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "configurationName"},
            ]
        },
    ]
});

module.exports = Configurations;
