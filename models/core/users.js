const Sequelize = require('sequelize');
const sequelize = require('../../util/database');

const User = sequelize.define('users', {
    uid: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: false,
        primaryKey: true
    },
    firstname: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    middleinitial: {
        type: Sequelize.DataTypes.STRING(2),
        allowNull: true
    },
    lastname: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: false
    },
    username: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: false
    },
    password: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: false
    },
    title: {
        type: Sequelize.DataTypes.STRING(150),
        allowNull: true
    },
    institution: {
        type: Sequelize.DataTypes.STRING(200),
        allowNull: true
    },
    department: {
        type: Sequelize.DataTypes.STRING(200),
        allowNull: true
    },
    address: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    city: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: true
    },
    state: {
        type: Sequelize.DataTypes.STRING(50),
        allowNull: true
    },
    zip: {
        type: Sequelize.DataTypes.STRING(15),
        allowNull: true
    },
    country: {
        type: Sequelize.DataTypes.STRING(50),
        allowNull: true
    },
    phone: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    email: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: false
    },
    RegionOfInterest: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    url: {
        type: Sequelize.DataTypes.STRING(400),
        allowNull: true
    },
    Biography: {
        type: Sequelize.DataTypes.STRING(1500),
        allowNull: true
    },
    notes: {
        type: Sequelize.DataTypes.STRING(255),
        allowNull: true
    },
    ispublic: {
        type: Sequelize.DataTypes.INTEGER.UNSIGNED,
        allowNull: false,
        defaultValue: 0
    },
    defaultrights: {
        type: Sequelize.DataTypes.STRING(250),
        allowNull: true
    },
    rightsholder: {
        type: Sequelize.DataTypes.STRING(250),
        allowNull: true
    },
    rights: {
        type: Sequelize.DataTypes.STRING(250),
        allowNull: true
    },
    accessrights: {
        type: Sequelize.DataTypes.STRING(250),
        allowNull: true
    },
    guid: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: true
    },
    validated: {
        type: Sequelize.DataTypes.STRING(45),
        allowNull: false,
        defaultValue: "0"
    },
    usergroups: {
        type: Sequelize.DataTypes.STRING(100),
        allowNull: true
    },
    lastlogindate: {
        type: Sequelize.DataTypes.DATE,
        allowNull: true
    },
    InitialTimeStamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'users',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "uid"},
            ]
        },
        {
            name: "Index_email",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "email"},
                {name: "lastname"},
            ]
        },
    ]
});

module.exports = User;
