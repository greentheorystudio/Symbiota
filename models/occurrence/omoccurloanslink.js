const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurloanslink', {
        loanid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurloans',
                key: 'loanid'
            }
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        returndate: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurloanslink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "loanid"},
                    {name: "occid"},
                ]
            },
            {
                name: "FK_occurloanlink_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_occurloanlink_loanid",
                using: "BTREE",
                fields: [
                    {name: "loanid"},
                ]
            },
        ]
    });
};
