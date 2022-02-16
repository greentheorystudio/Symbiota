const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const LookupStateProvince = sequelize.define('lkupstateprovince', {
    stateId: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    countryId: {
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        references: {
            model: 'lkupcountry',
            key: 'countryId'
        }
    },
    stateName: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: false
    },
    abbrev: {
        type: Sequelize.DataTypes.STRING(3),
        allowNull: true
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'lkupstateprovince',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "stateId"},
            ]
        },
        {
            name: "state_index",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "stateName"},
                {name: "countryId"},
            ]
        },
        {
            name: "fk_country",
            using: "BTREE",
            fields: [
                {name: "countryId"},
            ]
        },
        {
            name: "index_statename",
            using: "BTREE",
            fields: [
                {name: "stateName"},
            ]
        },
        {
            name: "Index_lkupstate_abbr",
            using: "BTREE",
            fields: [
                {name: "abbrev"},
            ]
        },
    ]
});

module.exports = LookupStateProvince;
