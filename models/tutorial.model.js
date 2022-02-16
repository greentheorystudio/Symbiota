const Sequelize = require('sequelize');
const sequelize = require('../util/database');

const Tutorial = sequelize.define('tutorial', {
    title: {
        type: Sequelize.STRING
    },
    description: {
        type: Sequelize.STRING
    },
    published: {
        type: Sequelize.BOOLEAN
    }
});

module.exports = Tutorial;
