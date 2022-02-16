const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('imagekeywords', {
        imgkeywordid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        imgid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'images',
                key: 'imgid'
            }
        },
        keyword: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        uidassignedby: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'imagekeywords',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgkeywordid"},
                ]
            },
            {
                name: "FK_imagekeywords_imgid_idx",
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                ]
            },
            {
                name: "FK_imagekeyword_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "uidassignedby"},
                ]
            },
            {
                name: "INDEX_imagekeyword",
                using: "BTREE",
                fields: [
                    {name: "keyword"},
                ]
            },
        ]
    });
};
