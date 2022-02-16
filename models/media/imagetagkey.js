const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('imagetagkey', {
        tagkey: {
            type: DataTypes.STRING(30),
            allowNull: false,
            primaryKey: true
        },
        shortlabel: {
            type: DataTypes.STRING(30),
            allowNull: false
        },
        description_en: {
            type: DataTypes.STRING(255),
            allowNull: false
        },
        sortorder: {
            type: DataTypes.INTEGER,
            allowNull: false
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'imagetagkey',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "tagkey"},
                ]
            },
            {
                name: "sortorder",
                using: "BTREE",
                fields: [
                    {name: "sortorder"},
                ]
            },
        ]
    });
};
