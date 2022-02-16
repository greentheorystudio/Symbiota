const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('taxaprofilepubimagelink', {
        imgid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'images',
                key: 'imgid'
            }
        },
        tppid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'taxaprofilepubs',
                key: 'tppid'
            }
        },
        caption: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        editornotes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'taxaprofilepubimagelink',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                    {name: "tppid"},
                ]
            },
            {
                name: "FK_tppubimagelink_id_idx",
                using: "BTREE",
                fields: [
                    {name: "tppid"},
                ]
            },
        ]
    });
};
