const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const LookupMunicipality = sequelize.define('lkupmunicipality', {
    municipalityId: {
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
    municipalityName: {
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
    tableName: 'lkupmunicipality',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "municipalityId"},
            ]
        },
        {
            name: "unique_municipality",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "stateId"},
                {name: "municipalityName"},
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
            name: "index_municipalityname",
            using: "BTREE",
            fields: [
                {name: "municipalityName"},
            ]
        },
    ]
});

module.exports = LookupMunicipality;
