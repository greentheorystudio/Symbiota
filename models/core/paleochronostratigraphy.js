const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const Paleochronostratigraphy = sequelize.define('paleochronostratigraphy', {
    chronoId: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: false,
        primaryKey: true
    },
    Eon: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    Era: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    Period: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    Epoch: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    Stage: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    }
}, {
    sequelize,
    tableName: 'paleochronostratigraphy',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "chronoId"},
            ]
        },
        {
            name: "Eon",
            using: "BTREE",
            fields: [
                {name: "Eon"},
            ]
        },
        {
            name: "Era",
            using: "BTREE",
            fields: [
                {name: "Era"},
            ]
        },
        {
            name: "Period",
            using: "BTREE",
            fields: [
                {name: "Period"},
            ]
        },
        {
            name: "Epoch",
            using: "BTREE",
            fields: [
                {name: "Epoch"},
            ]
        },
        {
            name: "Stage",
            using: "BTREE",
            fields: [
                {name: "Stage"},
            ]
        },
    ]
});

module.exports = Paleochronostratigraphy;
