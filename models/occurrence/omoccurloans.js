const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurloans', {
        loanid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        loanIdentifierOwn: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        loanIdentifierBorr: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        collidOwn: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        collidBorr: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        iidOwner: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'institutions',
                key: 'iid'
            }
        },
        iidBorrower: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'institutions',
                key: 'iid'
            }
        },
        dateSent: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        dateSentReturn: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        receivedStatus: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        totalBoxes: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        totalBoxesReturned: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        numSpecimens: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        shippingMethod: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        shippingMethodReturn: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        dateDue: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        dateReceivedOwn: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        dateReceivedBorr: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        dateClosed: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        forWhom: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        invoiceMessageOwn: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        invoiceMessageBorr: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        createdByOwn: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        createdByBorr: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        processingStatus: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 1
        },
        processedByOwn: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        processedByBorr: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        processedByReturnOwn: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        processedByReturnBorr: {
            type: DataTypes.STRING(30),
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omoccurloans',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "loanid"},
                ]
            },
            {
                name: "FK_occurloans_owninst",
                using: "BTREE",
                fields: [
                    {name: "iidOwner"},
                ]
            },
            {
                name: "FK_occurloans_borrinst",
                using: "BTREE",
                fields: [
                    {name: "iidBorrower"},
                ]
            },
            {
                name: "FK_occurloans_owncoll",
                using: "BTREE",
                fields: [
                    {name: "collidOwn"},
                ]
            },
            {
                name: "FK_occurloans_borrcoll",
                using: "BTREE",
                fields: [
                    {name: "collidBorr"},
                ]
            },
        ]
    });
};
