const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurduplicatelink', {
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        duplicateid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurduplicates',
                key: 'duplicateid'
            }
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        modifiedUid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        modifiedtimestamp: {
            type: DataTypes.DATE,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurduplicatelink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                    {name: "duplicateid"},
                ]
            },
            {
                name: "FK_omoccurdupelink_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_omoccurdupelink_dupeid_idx",
                using: "BTREE",
                fields: [
                    {name: "duplicateid"},
                ]
            },
        ]
    });
};
