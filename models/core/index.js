require("./adminlanguages.js");
require("./configurations.js");
const LookupCountry = require("./lkupcountry.js");
const LookupCounty = require("./lkupcounty.js");
const LookupMunicipality = require("./lkupmunicipality.js");
const LookupStateProvince = require("./lkupstateprovince.js");
require("./paleochronostratigraphy.js");
require("./schemaversion.js");
const UserAccessToken = require("./useraccesstokens.js");
const UserRoles = require("./userroles.js");
const User = require("./users.js");

LookupStateProvince.belongsTo(LookupCountry, {as: "country", foreignKey: "countryId"});

LookupCountry.hasMany(LookupStateProvince, {as: "lkupstateprovinces", foreignKey: "countryId"});

LookupCounty.belongsTo(LookupStateProvince, {as: "state", foreignKey: "stateId"});

LookupStateProvince.hasMany(LookupCounty, {as: "lkupcounties", foreignKey: "stateId"});

LookupMunicipality.belongsTo(LookupStateProvince, {as: "state", foreignKey: "stateId"});

LookupStateProvince.hasMany(LookupMunicipality, {as: "lkupmunicipalities", foreignKey: "stateId"});

UserAccessToken.belongsTo(User, {as: "uid_user", foreignKey: "uid"});

User.hasMany(UserAccessToken, {as: "useraccesstokens", foreignKey: "uid"});

UserRoles.belongsTo(User, {as: "uid_user", foreignKey: "uid"});

User.hasMany(UserRoles, {as: "userroles", foreignKey: "uid"});

UserRoles.belongsTo(User, {as: "uidassignedby_user", foreignKey: "uidassignedby"});

User.hasMany(UserRoles, {as: "uidassignedby_userroles", foreignKey: "uidassignedby"});
