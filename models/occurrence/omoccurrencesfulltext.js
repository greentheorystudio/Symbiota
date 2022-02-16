const Sequelize = require('sequelize');
module.exports = function (sequelize, DataTypes) {
    return sequelize.define('omoccurrencesfulltext', {
        occid: {
            type: DataTypes.INTEGER,
            allowNull: false,
            primaryKey: true
        },
        locality: {
            type: DataTypes.TEXT,
            allowNull: true
        },
        recordedby: {
            type: DataTypes.STRING(255),
            allowNull: true
        }
    }, {
        sequelize,
        tableName: 'omoccurrencesfulltext',
        timestamps: false,
        indexes: [
            {
                name: "PRIMARY",
                unique: true,
                using: "BTREE",
                fields: [
                    {name: "occid"},
                ]
            },
            {
                name: "ft_occur_locality",
                type: "FULLTEXT",
                fields: [
                    {name: "locality"},
                ]
            },
            {
                name: "ft_occur_recordedby",
                type: "FULLTEXT",
                fields: [
                    {name: "recordedby"},
                ]
            },
        ]
    });
};
