const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const UserRoles = sequelize.define('userroles', {
    userroleid: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: false,
        primaryKey: true
    },
    uid: {
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: false,
        references: {
            model: 'users',
            key: 'uid'
        }
    },
    role: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: false
    },
    tablename: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    tablepk: {
        type: Sequelize.DataTypes.INTEGER,
        allowNull: true
    },
    secondaryVariable: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    notes: {
        type: Sequelize.DataTypes.STRING(250),
        allowNull: true
    },
    uidassignedby: {
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: true,
        references: {
            model: 'users',
            key: 'uid'
        }
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'userroles',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "userroleid"},
            ]
        },
        {
            name: "Unique_userroles",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "uid"},
                {name: "role"},
                {name: "tablename"},
                {name: "tablepk"},
            ]
        },
        {
            name: "FK_userroles_uid_idx",
            using: "BTREE",
            fields: [
                {name: "uid"},
            ]
        },
        {
            name: "FK_usrroles_uid2_idx",
            using: "BTREE",
            fields: [
                {name: "uidassignedby"},
            ]
        },
        {
            name: "Index_userroles_table",
            using: "BTREE",
            fields: [
                {name: "tablename"},
                {name: "tablepk"},
            ]
        },
    ]
});

module.exports = UserRoles;
