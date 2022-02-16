const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('tmattributes', {
        stateid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'tmstates',
                key: 'stateid'
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
        modifier: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        xvalue: {
            type: DataTypes.DOUBLE(15, 5),
            allowNull: true
        },
        imgid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'images',
                key: 'imgid'
            }
        },
        imagecoordinates: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        statuscode: {
            type: DataTypes.TINYINT,
            allowNull: true
        },
        modifieduid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        datelastmodified: {
            type: DataTypes.DATE,
            allowNull: true
        },
        createduid: {
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
        tableName: 'tmattributes',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "stateid"},
                    {name: "occid"},
                ]
            },
            {
                name: "FK_tmattr_stateid_idx",
                using: "BTREE",
                fields: [
                    {name: "stateid"},
                ]
            },
            {
                name: "FK_tmattr_occid_idx",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_tmattr_imgid_idx",
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                ]
            },
            {
                name: "FK_attr_uidcreate_idx",
                using: "BTREE",
                fields: [
                    {name: "createduid"},
                ]
            },
            {
                name: "FK_tmattr_uidmodified_idx",
                using: "BTREE",
                fields: [
                    {name: "modifieduid"},
                ]
            },
        ]
    });
};
