const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('referenceobject', {
        refid: {
            autoIncrement: true,
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        parentRefId: {
            type: DataTypes.INTEGER,
            allowNull: true,
            references: {
                model: 'referenceobject',
                key: 'refid'
            }
        },
        ReferenceTypeId: {
            type: DataTypes.INTEGER,
            allowNull: true,
            references: {
                model: 'referencetype',
                key: 'ReferenceTypeId'
            }
        },
        title: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        secondarytitle: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        shorttitle: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        tertiarytitle: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        alternativetitle: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        typework: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        figures: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        pubdate: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        edition: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        volume: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        numbervolumes: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        number: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        pages: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        section: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        placeofpublication: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        publisher: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        isbn_issn: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        url: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        guid: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        ispublished: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        cheatauthors: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        cheatcitation: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        modifieduid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        modifiedtimestamp: {
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
        tableName: 'referenceobject',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "refid"},
                ]
            },
            {
                name: "INDEX_refobj_title",
                using: "BTREE",
                fields: [
                    {name: "title"},
                ]
            },
            {
                name: "FK_refobj_parentrefid_idx",
                using: "BTREE",
                fields: [
                    {name: "parentRefId"},
                ]
            },
            {
                name: "FK_refobj_typeid_idx",
                using: "BTREE",
                fields: [
                    {name: "ReferenceTypeId"},
                ]
            },
        ]
    });
};
