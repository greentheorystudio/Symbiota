const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadspecmap', {
        usmid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        uspid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'uploadspecparameters',
                key: 'uspid'
            }
        },
        sourcefield: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        symbdatatype: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "string",
            comment: "string, numeric, datetime"
        },
        symbspecfield: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadspecmap',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "usmid"},
                ]
            },
            {
                name: "Index_unique",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "uspid"},
                    {name: "symbspecfield"},
                    {name: "sourcefield"},
                ]
            },
        ]
    });
};
