const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referencetype', {
        ReferenceTypeId: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        ReferenceType: {
            type: DataTypes.STRING(45),
            allowNull: false,
            unique: "ReferenceType_UNIQUE"
        },
        IsParent: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        Title: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        SecondaryTitle: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        PlacePublished: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Publisher: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Volume: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        NumberVolumes: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Number: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Pages: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Section: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        TertiaryTitle: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Edition: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Date: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        TypeWork: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        ShortTitle: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        AlternativeTitle: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        ISBN_ISSN: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Figures: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        addedByUid: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'referencetype',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ReferenceTypeId"},
                ]
            },
            {
                name: "ReferenceType_UNIQUE",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ReferenceType"},
                ]
            },
        ]
    });
};
