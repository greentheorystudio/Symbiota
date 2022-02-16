const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('images', {
        imgid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'taxa',
                key: 'TID'
            }
        },
        url: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        thumbnailurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        originalurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        archiveurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        photographer: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        photographeruid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'users',
                key: 'uid'
            }
        },
        imagetype: {
            type: DataTypes.STRING(50),
            allowNull: true
        },
        format: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        caption: {
            type: DataTypes.STRING(750),
            allowNull: true
        },
        owner: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sourceurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        referenceUrl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        copyright: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        rights: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        accessrights: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        locality: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'omoccurrences',
                key: 'occid'
            }
        },
        notes: {
            type: DataTypes.STRING(350),
            allowNull: true
        },
        anatomy: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        username: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sourceIdentifier: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        mediaMD5: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 50
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'images',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "imgid"},
                ]
            },
            {
                name: "Index_tid",
                using: "BTREE",
                fields: [
                    {name: "tid"},
                ]
            },
            {
                name: "FK_images_occ",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "FK_photographeruid",
                using: "BTREE",
                fields: [
                    {name: "photographeruid"},
                ]
            },
            {
                name: "Index_images_datelastmod",
                using: "BTREE",
                fields: [
                    {name: "InitialTimeStamp"},
                ]
            },
        ]
    });
};
