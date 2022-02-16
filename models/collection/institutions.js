const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('institutions', {
        iid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        InstitutionCode: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        InstitutionName: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        InstitutionName2: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        Address1: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        Address2: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        City: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        StateProvince: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        PostalCode: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Country: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Phone: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Contact: {
            type: DataTypes.STRING(65),
            allowNull: true
        },
        Email: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Url: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        Notes: {
            type: DataTypes.STRING(250),
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
        modifiedTimeStamp: {
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
        tableName: 'institutions',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "iid"},
                ]
            },
            {
                name: "FK_inst_uid_idx",
                using: "BTREE",
                fields: [
                    {name: "modifieduid"},
                ]
            },
        ]
    });
};
