const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmprojects', {
        pid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        projname: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        displayname: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        managers: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        briefdescription: {
            type: DataTypes.STRING(300),
            allowNull: true
        },
        fulldescription: {
            type: DataTypes.STRING(5000),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        iconUrl: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        headerUrl: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        occurrencesearch: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0
        },
        ispublic: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 0
        },
        dynamicProperties: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        parentpid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            references: {
                model: 'fmprojects',
                key: 'pid'
            }
        },
        SortSequence: {
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
        tableName: 'fmprojects',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "pid"},
                ]
            },
            {
                name: "FK_parentpid_proj",
                using: "BTREE",
                fields: [
                    {name: "parentpid"},
                ]
            },
        ]
    });
};
