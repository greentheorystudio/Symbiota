const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const LookupCountry = sequelize.define('lkupcountry', {
    countryId: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    countryName: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: false,
        unique: "country_unique"
    },
    iso: {
        type: Sequelize.DataTypes.STRING(2),
        allowNull: true
    },
    iso3: {
        type: Sequelize.DataTypes.STRING(3),
        allowNull: true
    },
    numcode: {
        type: Sequelize.DataTypes.INTEGER,
        allowNull: true
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'lkupcountry',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "countryId"},
            ]
        },
        {
            name: "country_unique",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "countryName"},
            ]
        },
        {
            name: "Index_lkupcountry_iso",
            using: "BTREE",
            fields: [
                {name: "iso"},
            ]
        },
        {
            name: "Index_lkupcountry_iso3",
            using: "BTREE",
            fields: [
                {name: "iso3"},
            ]
        },
    ]
});

module.exports = LookupCountry;
