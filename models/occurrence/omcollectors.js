const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollectors', {
        recordedById: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        familyname: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        firstname: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        middlename: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        startyearactive: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        endyearactive: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        rating: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 10
        },
        guid: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        preferredrecbyid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollectors',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "recordedById"},
                ]
            },
            {
                name: "fullname",
                using: "BTREE",
                fields: [
                    {name: "familyname"},
                    {name: "firstname"},
                ]
            },
            {
                name: "FK_preferred_recby_idx",
                using: "BTREE",
                fields: [
                    {name: "preferredrecbyid"},
                ]
            },
        ]
    });
};
