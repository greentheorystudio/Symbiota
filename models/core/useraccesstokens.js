const Sequelize = require('sequelize');
const sequelize = require('../../util/database');
const config = require("../../config/auth.config");
const {v4: uuidv4} = require("uuid");

const UserAccessToken = sequelize.define('useraccesstokens', {
    tokid: {
        autoIncrement: true,
        type: Sequelize.DataTypes.INTEGER,
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
    token: {
        type: Sequelize.DataTypes.STRING(50),
        allowNull: false
    },
    device: {
        type: Sequelize.DataTypes.STRING(50),
        allowNull: true
    },
    expireDate: {
        type: Sequelize.DATE,
    },
    initialtimestamp: {
        type: Sequelize.DataTypes.DATE,
        allowNull: false,
        defaultValue: Sequelize.Sequelize.literal('CURRENT_TIMESTAMP')
    }
}, {
    sequelize,
    tableName: 'useraccesstokens',
    timestamps: false,
    indexes: [
        {
            name: "PRIMARY",
            unique: true,
            using: "BTREE",
            fields: [
                {name: "tokid"},
            ]
        },
        {
            name: "FK_useraccesstokens_uid_idx",
            using: "BTREE",
            fields: [
                {name: "uid"},
            ]
        },
    ]
});

UserAccessToken.createToken = async function (user) {
    let expiredAt = new Date();
    expiredAt.setSeconds(expiredAt.getSeconds() + config.jwtRefreshExpiration);
    let _token = uuidv4();
    let refreshToken = await this.create({
        token: _token,
        uid: user.uid,
        expireDate: expiredAt.getTime(),
    });
    return refreshToken.token;
};

UserAccessToken.verifyExpiration = (token) => {
    return token.expireDate.getTime() < new Date().getTime();
};

module.exports = UserAccessToken;
