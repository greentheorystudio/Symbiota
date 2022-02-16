const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('imageannotations', {
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        imgid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'images',
                key: 'imgid'
            }
        },
        AnnDate: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: "0000-00-00 00:00:00",
            primaryKey: true
        },
        Annotator: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'imageannotations',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                    {name: "AnnDate"},
                ]
            },
            {
                name: "TID",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
        ]
    });
};
