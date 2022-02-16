const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmprojectcategories', {
        projcatid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        pid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'fmprojects',
                key: 'pid'
            }
        },
        categoryname: {
            type: DataTypes.STRING(150),
            allowNull: false
        },
        managers: {
            type: DataTypes.STRING(100),
            allowNull: true
        },
        description: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        parentpid: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        occurrencesearch: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 0
        },
        ispublic: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 1
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        sortsequence: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        initialtimestamp: {
            type: DataTypes.DATE,
            allowNull: true,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'fmprojectcategories',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "projcatid"},
                ]
            },
            {
                name: "FK_fmprojcat_pid_idx",
                using: "BTREE",
                fields: [
                    {name: "pid"},
                ]
            },
        ]
    });
};
