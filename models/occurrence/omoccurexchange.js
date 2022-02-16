const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurexchange', {
        exchangeid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        identifier: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        iid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        transactionType: {
            type: DataTypes.STRING(10),
            allowNull: true
        },
        in_out: {
            type: DataTypes.STRING(3),
            allowNull: true
        },
        dateSent: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        dateReceived: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        totalBoxes: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        shippingMethod: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        totalExMounted: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        totalExUnmounted: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        totalGift: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        totalGiftDet: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        adjustment: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        invoiceBalance: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        invoiceMessage: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        createdBy: {
            type: DataTypes.STRING(20),
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurexchange',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "exchangeid"},
                ]
            },
            {
                name: "FK_occexch_coll",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
