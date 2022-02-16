const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omcollsecondary', {
        ocsid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        InstitutionCode: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        CollectionCode: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        CollectionName: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        BriefDescription: {
            type: DataTypes.STRING(300),
            allowNull: true
        },
        FullDescription: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        Homepage: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        IndividualUrl: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        Contact: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Email: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        LatitudeDecimal: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        LongitudeDecimal: {
            type: DataTypes.DOUBLE,
            allowNull: true
        },
        icon: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        CollType: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        SortSeq: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        InitialTimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'omcollsecondary',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "ocsid"},
                ]
            },
            {
                name: "FK_omcollsecondary_coll",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
