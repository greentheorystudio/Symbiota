const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadimagetemp', {
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
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
            allowNull: true
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
            type: DataTypes.STRING(100),
            allowNull: true
        },
        owner: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        sourceUrl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        referenceurl: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        copyright: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        accessrights: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        rights: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        locality: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        occid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        collid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        dbpk: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        sourceIdentifier: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(255),
            allowNull: true
        },
        username: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadimagetemp',
        timestamps: false,
        indexes: [
            {
                name: "Index_uploadimg_occid",
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "Index_uploadimg_collid",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
            {
                name: "Index_uploadimg_dbpk",
                using: "BTREE",
                fields: [
                    {name: "dbpk"},
                ]
            },
            {
                name: "Index_uploadimg_ts",
                using: "BTREE",
                fields: [
                    {name: "initialtimestamp"},
                ]
            },
        ]
    });
};
