const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadglossary', {
        term: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        definition: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        language: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        source: {
            type: DataTypes.STRING(1000),
            allowNull: true
        },
        author: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        translator: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        resourceurl: {
            type: DataTypes.STRING(600),
            allowNull: true
        },
        tidStr: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        synonym: {
            type: DataTypes.BOOLEAN,
            allowNull: true
        },
        newGroupId: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        currentGroupId: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadglossary',
        timestamps: false,
        indexes: [
            {
                name: "term_index",
                using: "BTREE",
                fields: [
                    {name: "term"},
                ]
            },
            {
                name: "relatedterm_index",
                using: "BTREE",
                fields: [
                    {name: "newGroupId"},
                ]
            },
        ]
    });
};
