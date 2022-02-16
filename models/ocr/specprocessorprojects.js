const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('specprocessorprojects', {
        spprid: {
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
        title: {
            type: DataTypes.STRING(100),
            allowNull: false
        },
        projecttype: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        specKeyPattern: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        patternReplace: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        replaceStr: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        speckeyretrieval: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        coordX1: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        coordX2: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        coordY1: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        coordY2: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true
        },
        sourcePath: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        targetPath: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        imgUrl: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        webPixWidth: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 1200
        },
        tnPixWidth: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 130
        },
        lgPixWidth: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 2400
        },
        jpgcompression: {
            type: DataTypes.INTEGER,
            allowNull: true,
            defaultValue: 70
        },
        createTnImg: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 1
        },
        createLgImg: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: true,
            defaultValue: 1
        },
        source: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        lastrundate: {
            type: DataTypes.DATEONLY,
            allowNull: true
        },
        initialTimestamp: {
            type: DataTypes.DATE,
            allowNull: false,
            defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
        }
    }, {
        sequelize,
        tableName: 'specprocessorprojects',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "spprid"},
                ]
            },
            {
                name: "FK_specprocessorprojects_coll",
                using: "BTREE",
                fields: [
                    {name: "collid"},
                ]
            },
        ]
    });
};
