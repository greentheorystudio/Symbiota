const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const AdminLanguages = sequelize.define('adminlanguages', {
    langid: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    langname: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: false,
        unique: "index_langname_unique"
    },
    iso639_1: {
        type: Sequelize.DataTypes.STRING(10),
        allowNull: true
    },
    iso639_2: {
        type: Sequelize.DataTypes.STRING(10),
        allowNull: true
    },
    'ISO 639-3': {
        type: Sequelize.DataTypes.STRING(3),
        allowNull: true
    },
    notes: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'adminlanguages',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "langid"},
            ]
        },
        {
            name: "index_langname_unique",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "langname"},
            ]
        },
    ]
});

module.exports = AdminLanguages;
