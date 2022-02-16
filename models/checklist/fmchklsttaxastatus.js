const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('fmchklsttaxastatus', {
        clid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        tid: {
            type: DataTypes.INTEGER.UNSIGNED,
            allowNull: false,
            primaryKey: true
        },
        geographicRange: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        populationRank: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        abundance: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        habitatSpecificity: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        intrinsicRarity: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        threatImminence: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        populationTrends: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        nativeStatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        endemicStatus: {
            type: DataTypes.INTEGER,
            allowNull: false,
            defaultValue: 0
        },
        protectedStatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        localitySecurity: {
            type: DataTypes.INTEGER,
            allowNull: true
        },
        localitySecurityReason: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        invasiveStatus: {
            type: DataTypes.STRING(45),
            allowNull: true
        },
        notes: {
            type: DataTypes.STRING(250),
            allowNull: true
        },
        modifiedUid: {
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
        tableName: 'fmchklsttaxastatus',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "clid"},
                    {name: "tid"},
                ]
            },
        ]
    });
};
