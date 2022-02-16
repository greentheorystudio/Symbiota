const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('imagetag', {
        imagetagid: {
            autoIncrement: true,
            type: DataTypes.BIGINT,
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
        keyvalue: {
            type: DataTypes.STRING(30),
            allowNull: false,
            references: {
                model: 'imagetagkey',
                key: 'tagkey'
            }
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'imagetag',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imagetagid"},
                ]
            },
            {
                name: "imgid",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                    {name: "keyvalue"},
                ]
            },
            {
                name: "keyvalue",
                using: "BTREE",
                fields: [
                    {name: "keyvalue"},
                ]
            },
            {
                name: "FK_imagetag_imgid_idx",
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                ]
            },
        ]
    });
};
