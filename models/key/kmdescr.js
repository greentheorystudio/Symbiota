const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('kmdescr', {
        TID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        CID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cid'
            }
        },
        Modifier: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        CS: {
            type: DataTypes.STRING(16),
            allowNull: false,
            primaryKey: true,
            references: {
                model: 'kmcs',
                key: 'cs'
            }
        },
        X: {
            type: DataTypes.DOUBLE(15, 5),
            allowNull: true
        },
        TXT: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        PseudoTrait: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 0
        },
        Frequency: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 5,
            comment: "Frequency of occurrence; 1 = rare... 5 = common"
        },
        Inherited: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        Source: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        Seq: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        Notes: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        DateEntered: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'kmdescr',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "TID"},
                    {name: "CID"},
                    {name: "CS"},
                ]
            },
            {
                name: "CSDescr",
                using: "BTREE",
                fields: [
                    {name: "CID"},
                    {name: "CS"},
                ]
            },
        ]
    });
};
