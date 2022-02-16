const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const LookupCounty = sequelize.define('lkupcounty', {
    countyId: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        primaryKey: true
    },
    stateId: {
        type: Sequelize.DataTypes.INTEGER,
        allowNull: false,
        references: {
            model: 'lkupstateprovince',
            key: 'stateId'
        }
    },
    countyName: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: false
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'lkupcounty',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "countyId"},
            ]
        },
        {
            name: "unique_county",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "stateId"},
                {name: "countyName"},
            ]
        },
        {
            name: "fk_stateprovince",
            using: "BTREE",
            fields: [
                {name: "stateId"},
            ]
        },
        {
            name: "index_countyname",
            using: "BTREE",
            fields: [
                {name: "countyName"},
            ]
        },
    ]
});

module.exports = LookupCounty;
