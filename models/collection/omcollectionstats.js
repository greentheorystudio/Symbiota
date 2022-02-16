const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollectionstats', {
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        recordcnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0
        },
        georefcnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        familycnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        genuscnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        speciescnt: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        uploaddate: {
            type: DataTypes.DATE,
            allowNull: true
        },
        datelastmodified: {
            type: DataTypes.DATE,
            allowNull: true
        },
        uploadedby: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollectionstats',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
