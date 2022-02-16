const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('uploadspecparameters', {
        uspid: {
            autoIncrement: true,
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        CollID: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            references: {
                model: 'omcollections',
                key: 'CollID'
            }
        },
        UploadType: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            defaultValue: 1,
            comment: "1 = Direct; 2 = DiGIR; 3 = File"
        },
        title: {
            type: DataTypes.STRING(45),
            allowNull: false
        },
        Platform: {
            type: DataTypes.STRING(45),
            allowNull: true,
            defaultValue: "1",
            comment: "1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker"
        },
        server: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        port: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        driver: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Code: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Path: {
            type: DataTypes.STRING(500),
            allowNull: true
        },
        PkField: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Username: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        Password: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        SchemaName: {
            type: DataTypes.STRING(150),
            allowNull: true
        },
        QueryStr: {
            type: DataTypes.STRING(2000),
            allowNull: true
        },
        cleanupsp: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        existingrecords: {
            type: DataTypes.STRING(45),
            allowNull: false,
            defaultValue: "update"
        },
        dlmisvalid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 0
        },
        InitialTimeStamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'uploadspecparameters',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "uspid"},
                ]
            },
            {
                name: "FK_uploadspecparameters_coll",
                using: "BTREE",
                fields: [
                    {name: "CollID"},
                ]
            },
        ]
    });
};
