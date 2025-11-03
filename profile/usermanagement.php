<?php
include_once(__DIR__ . '/../config/symbbase.php');
header('Content-Type: text/html; charset=UTF-8' );
header('X-Frame-Options: SAMEORIGIN');
?>
<!DOCTYPE html>
<html lang="<?php echo $GLOBALS['DEFAULT_LANG']; ?>">
    <?php
    include_once(__DIR__ . '/../config/header-includes.php');
    ?>
    <head>
        <title><?php echo $GLOBALS['DEFAULT_TITLE']; ?> User Management</title>
        <meta name="description" content="Manage users for the <?php echo $GLOBALS['DEFAULT_TITLE']; ?> portal">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/base.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/css/main.css?ver=<?php echo $GLOBALS['CSS_VERSION']; ?>" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <a class="screen-reader-only" href="#mainContainer" tabindex="0">Skip to main content</a>
        <?php
        include(__DIR__ . '/../header.php');
        ?>
        <div id="mainContainer" class="q-pa-md column q-gutter-sm">
            <template v-if="isAdmin">
                <template v-if="Number(currentUserId) > 0">
                    <div class="column q-gutter-sm">
                        <div role="button" class="cursor-pointer text-body1 text-bold" @click="processUserChange(0);" @keyup.enter="processUserChange(0);" aria-label="Back to user list" tabindex="0">
                            Back to user list
                        </div>
                        <q-card>
                            <q-card-section class="row justify-between q-col-gutter-md">
                                <div class="col-12 col-sm-6 col-md-6 column">
                                    <div class="row justify-start q-gutter-md">
                                        <div class="text-body1 text-bold">
                                            {{ userInfo['firstname'] + ' ' + userInfo['lastname'] + '(#' + userInfo['uid'] + ')' }}
                                        </div>
                                        <div class="cursor-pointer">
                                            <q-btn icon="far fa-edit" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="showAccountEditorPopup = true" aria-label="Edit account" tabindex="0"></q-btn>
                                        </div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Title:</div>
                                        <div>{{ userInfo['title'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Institution:</div>
                                        <div>{{ userInfo['institution'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">City:</div>
                                        <div>{{ userInfo['city'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">State:</div>
                                        <div>{{ userInfo['state'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Zip:</div>
                                        <div>{{ userInfo['zip'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Country:</div>
                                        <div>{{ userInfo['country'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Email:</div>
                                        <div>{{ userInfo['email'] }}</div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">URL:</div>
                                        <div v-if="userInfo['url']"><a :href="userInfo['url']" target="_blank" aria-label="Go to user homepage - Opens in separate tab" tabindex="0">{{ userInfo['url'] }}</a></div>
                                    </div>
                                    <div class="row justify-start q-gutter-sm">
                                        <div class="text-bold">Login:</div>
                                        <div>{{ userInfo['username'] + (userInfo['lastlogindate'] ? (' (last login: ' + userInfo['lastlogindate'] + ')') : '') }}</div>
                                    </div>
                                    <div v-if="Number(userInfo['validated']) !== 1" class="row justify-start q-gutter-sm">
                                        <div class="text-bold text-red">UNCONFIRMED USER</div>
                                        <div><q-btn label="Confirm User" color="grey-4" text-color="black" class="black-border" size="sm" dense @click="confirmUser();" tabindex="0"></q-btn></div>
                                    </div>
                                    <div v-if="isAdmin" class="q-mt-sm column justify-start q-gutter-sm">
                                        <div>
                                            <q-btn label="Reset Password" color="grey-4" text-color="black" class="black-border" size="md" dense @click="resetPassword();" tabindex="0"></q-btn>
                                        </div>
                                        <div v-if="resetPasswordValue" class="text-red">
                                            Notify user that their password has been reset to: <span class="text-bold">{{ resetPasswordValue }}</span>
                                        </div>
                                        <div>
                                            <q-btn label="Login as this user" color="grey-4" text-color="black" class="black-border" size="md" dense @click="loginAsUser();" tabindex="0"></q-btn>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-md-6">
                                    <q-card flat bordered>
                                        <q-card-section class="column q-gutter-xs">
                                            <div class="text-body1 text-bold q-mb-sm">Current Permissions</div>
                                            <template v-if="Object.keys(userPermissions).length > 0">
                                                <template v-if="isAdmin">
                                                    <template v-if="userPermissions.hasOwnProperty('SuperAdmin')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Super Administrator</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('SuperAdmin', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('Taxonomy')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Taxonomy Editor</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('Taxonomy', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('TaxonProfile')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Taxon Profile Editor</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('TaxonProfile', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('KeyAdmin')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Identification Keys Administrator</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('KeyAdmin', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('KeyEditor')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Identification Keys Editor</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('KeyEditor', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('PublicChecklist')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Can Create Public Checklists and Biotic Inventory Projects</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('PublicChecklist', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('RareSppAdmin')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">Rare Species List Administrator</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('RareSppAdmin', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template v-if="userPermissions.hasOwnProperty('RareSppReadAll')">
                                                        <div class="q-pl-sm row justify-start q-gutter-sm">
                                                            <div class="text-bold">View and Map Occurrences of Rare Species from all Collections</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('RareSppReadAll', null);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="collAdminPermissionArr.length > 0">
                                                    <div class="q-pl-sm row justify-start q-gutter-sm">
                                                        <div class="text-bold">Collection Administrator for following collections</div>
                                                    </div>
                                                    <template v-for="perm in collAdminPermissionArr">
                                                        <div class="q-pl-lg row justify-start q-gutter-sm">
                                                            <div>{{ perm['name'] }}</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('CollAdmin', perm['id']);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="collEditorPermissionArr.length > 0">
                                                    <div class="q-pl-sm row justify-start q-gutter-sm">
                                                        <div class="text-bold">Collection Editor for following collections</div>
                                                    </div>
                                                    <template v-for="perm in collEditorPermissionArr">
                                                        <div class="q-pl-lg row justify-start q-gutter-sm">
                                                            <div>{{ perm['name'] }}</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('CollEditor', perm['id']);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="collRarePermissionArr.length > 0">
                                                    <div class="q-pl-sm row justify-start q-gutter-sm">
                                                        <div class="text-bold">View and Map Occurrences of Rare Species from following Collections</div>
                                                    </div>
                                                    <template v-for="perm in collRarePermissionArr">
                                                        <div class="q-pl-lg row justify-start q-gutter-sm">
                                                            <div>{{ perm['name'] }}</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('RareSppReader', perm['id']);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="projAdminPermissionArr.length > 0">
                                                    <div class="q-pl-sm row justify-start q-gutter-sm">
                                                        <div class="text-bold">Administrator for following inventory projects</div>
                                                    </div>
                                                    <template v-for="perm in projAdminPermissionArr">
                                                        <div class="q-pl-lg row justify-start q-gutter-sm">
                                                            <div>{{ perm['name'] }}</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('ProjAdmin', perm['id']);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-if="clAdminPermissionArr.length > 0">
                                                    <div class="q-pl-sm row justify-start q-gutter-sm">
                                                        <div class="text-bold">Administrator for following checklists</div>
                                                    </div>
                                                    <template v-for="perm in clAdminPermissionArr">
                                                        <div class="q-pl-lg row justify-start q-gutter-sm">
                                                            <div>{{ perm['name'] }}</div>
                                                            <div class="cursor-pointer">
                                                                <q-btn icon="far fa-trash-alt" color="grey-4" text-color="black" class="black-border" size="xs" dense @click="deletePermission('ClAdmin', perm['id']);" aria-label="Delete permission" tabindex="0"></q-btn>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div>No permissions have to been assigned to this user</div>
                                            </template>
                                            <template v-if="isAdmin">
                                                <div v-if="Object.keys(userPermissions).length > 0" class="cursor-pointer">
                                                    <q-btn label="Delete All Permissions" color="grey-4" text-color="black" class="black-border" size="sm" dense @click="deleteAllPermissions();" tabindex="0"></q-btn>
                                                </div>
                                            </template>
                                        </q-card-section>
                                    </q-card>
                                </div>
                            </q-card-section>
                        </q-card>
                        <q-card>
                            <q-card-section class="column q-gutter-sm">
                                <div class="text-body1 text-bold q-mb-sm">Assign New Permissions</div>
                                <template v-if="userPermissions.hasOwnProperty('SuperAdmin')">
                                    <div>There are no new permissions to be added</div>
                                </template>
                                <template v-else>
                                    <div class="column">
                                        <div>
                                            <checkbox-input-element label="Super Administrator" :value="false" @update:value="addPermissions([{role: 'SuperAdmin', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('Taxonomy')">
                                            <checkbox-input-element label="Taxonomy Editor" :value="false" @update:value="addPermissions([{role: 'Taxonomy', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('TaxonProfile')">
                                            <checkbox-input-element label="Taxon Profile Editor" :value="false" @update:value="addPermissions([{role: 'TaxonProfile', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('KeyAdmin')">
                                            <checkbox-input-element label="Identification Key Administrator" :value="false" @update:value="addPermissions([{role: 'KeyAdmin', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('KeyEditor')">
                                            <checkbox-input-element label="Identification Key Editor" :value="false" @update:value="addPermissions([{role: 'KeyEditor', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('RareSppAdmin')">
                                            <checkbox-input-element label="Rare Species Administrator (add/remove species from list)" :value="false" @update:value="addPermissions([{role: 'RareSppAdmin', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('RareSppReadAll')">
                                            <checkbox-input-element label="Can read Rare Species data for all collections" :value="false" @update:value="addPermissions([{role: 'RareSppReadAll', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                        <div v-if="!userPermissions.hasOwnProperty('PublicChecklist')">
                                            <checkbox-input-element label="Can Create Public Checklists and Biotic Inventory Projects" :value="false" @update:value="addPermissions([{role: 'PublicChecklist', rolepk: null}])"></checkbox-input-element>
                                        </div>
                                    </div>
                                    <template v-if="collectionArr.length > 0">
                                        <q-card class="full-width" flat bordered>
                                            <q-card-section class="column q-col-gutter-xs">
                                                <div class="full-width row justify-between q-gutter-md">
                                                    <div class="text-body1 text-bold q-mb-sm">Collections</div>
                                                    <div class="row justify-end">
                                                        <q-btn color="secondary" @click="addPermissions();" label="Add Permissions" :disabled="newPermissionArr.length === 0" dense tabindex="0" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-1 text-bold row justify-center">Admin</div>
                                                    <div class="col-1 text-bold row justify-center">Editor</div>
                                                    <div class="col-1 text-bold row justify-center">Rare</div>
                                                    <div class="col-9"></div>
                                                </div>
                                                <template v-for="collection in collectionArr">
                                                    <div class="full-width row q-ma-none q-pa-none">
                                                        <div class="col-1 row justify-center">
                                                            <checkbox-input-element :value="(userPermissions.hasOwnProperty('CollAdmin') && userPermissions['CollAdmin'].hasOwnProperty(collection['collid'])) || !!newPermissionArr.find(perm => (perm['role'] === 'CollAdmin' && perm['rolepk'] === collection['collid']))" @update:value="(value) => processPermissionArrChange({role: 'CollAdmin', rolepk: collection['collid']}, value)" :disabled="(userPermissions.hasOwnProperty('CollAdmin') && userPermissions['CollAdmin'].hasOwnProperty(collection['collid']))"></checkbox-input-element>
                                                        </div>
                                                        <div class="col-1 row justify-center">
                                                            <checkbox-input-element :value="(userPermissions.hasOwnProperty('CollEditor') && userPermissions['CollEditor'].hasOwnProperty(collection['collid'])) || !!newPermissionArr.find(perm => (perm['role'] === 'CollEditor' && perm['rolepk'] === collection['collid']))" @update:value="(value) => processPermissionArrChange({role: 'CollEditor', rolepk: collection['collid']}, value)" :disabled="((userPermissions.hasOwnProperty('CollAdmin') && userPermissions['CollAdmin'].hasOwnProperty(collection['collid'])) || (userPermissions.hasOwnProperty('CollEditor') && userPermissions['CollEditor'].hasOwnProperty(collection['collid'])))"></checkbox-input-element>
                                                        </div>
                                                        <div class="col-1 row justify-center">
                                                            <checkbox-input-element :value="(userPermissions.hasOwnProperty('RareSppReader') && userPermissions['RareSppReader'].hasOwnProperty(collection['collid'])) || !!newPermissionArr.find(perm => (perm['role'] === 'RareSppReader' && perm['rolepk'] === collection['collid']))" @update:value="(value) => processPermissionArrChange({role: 'RareSppReader', rolepk: collection['collid']}, value)" :disabled="((userPermissions.hasOwnProperty('CollAdmin') && userPermissions['CollAdmin'].hasOwnProperty(collection['collid'])) || (userPermissions.hasOwnProperty('CollEditor') && userPermissions['CollEditor'].hasOwnProperty(collection['collid'])) || (userPermissions.hasOwnProperty('RareSppReader') && userPermissions['RareSppReader'].hasOwnProperty(collection['collid'])))"></checkbox-input-element>
                                                        </div>
                                                        <div class="col-9">
                                                            {{ collection['collectionname'] + collection['acroStr'] }}
                                                        </div>
                                                    </div>
                                                </template>
                                            </q-card-section>
                                        </q-card>
                                    </template>
                                    <template v-if="projectArr.length > 0">
                                        <q-card class="full-width" flat bordered>
                                            <q-card-section class="column q-col-gutter-xs">
                                                <div class="full-width row justify-between q-gutter-md">
                                                    <div class="text-body1 text-bold q-mb-sm">Biotic Inventory Project Management</div>
                                                    <div class="row justify-end">
                                                        <q-btn color="secondary" @click="addPermissions();" label="Add Permissions" :disabled="newPermissionArr.length === 0" dense tabindex="0" />
                                                    </div>
                                                </div>
                                                <template v-for="project in projectArr">
                                                    <checkbox-input-element :label="project['projname']" :value="(userPermissions.hasOwnProperty('ProjAdmin') && userPermissions['ProjAdmin'].hasOwnProperty(project['pid'])) || !!newPermissionArr.find(perm => (perm['role'] === 'ProjAdmin' && perm['rolepk'] === project['pid']))" @update:value="(value) => processPermissionArrChange({role: 'ProjAdmin', rolepk: project['pid']}, value)"></checkbox-input-element>
                                                </template>
                                            </q-card-section>
                                        </q-card>
                                    </template>
                                    <template v-if="checklistArr.length > 0">
                                        <q-card class="full-width" flat bordered>
                                            <q-card-section class="column q-col-gutter-xs">
                                                <div class="full-width row justify-between q-gutter-md">
                                                    <div class="text-body1 text-bold q-mb-sm">Checklist Management</div>
                                                    <div class="row justify-end">
                                                        <q-btn color="secondary" @click="addPermissions();" label="Add Permissions" :disabled="newPermissionArr.length === 0" dense tabindex="0" />
                                                    </div>
                                                </div>
                                                <template v-for="checklist in checklistArr">
                                                    <checkbox-input-element :label="checklist['name']" :value="(userPermissions.hasOwnProperty('ClAdmin') && userPermissions['ClAdmin'].hasOwnProperty(checklist['clid'])) || !!newPermissionArr.find(perm => (perm['role'] === 'ClAdmin' && perm['rolepk'] === checklist['clid']))" @update:value="(value) => processPermissionArrChange({role: 'ClAdmin', rolepk: checklist['clid']}, value)"></checkbox-input-element>
                                                </template>
                                            </q-card-section>
                                        </q-card>
                                    </template>
                                </template>
                            </q-card-section>
                        </q-card>
                    </div>
                </template>
                <template v-else>
                    <q-card>
                        <q-card-section class="column q-gutter-xs">
                            <div class="text-body1 text-bold">Search</div>
                            <div>
                                <text-field-input-element label="Last Name or Login Name" :value="userListFilter" @update:value="processFilterChange"></text-field-input-element>
                            </div>
                            <div class="row q-gutter-xs">
                                <template v-for="letter in filterOptions">
                                    <span role="button" class="cursor-pointer text-body1 text-bold" @click="processFilterChange(letter);" :aria-label="('Filter user list ' + letter)" tabindex="0">
                                        {{ letter }}
                                    </span>
                                </template>
                            </div>
                            <div class="full-width row justify-start q-gutter-sm">
                                <div class="col-3">
                                    <selector-input-element label="Display" :options="userListDisplayOptions" :value="userListDisplaySelectedOption" @update:value="(value) => processListDisplayOptionChange(value)"></selector-input-element>
                                </div>
                                <template v-if="userListDisplaySelectedOption === 'unconfirmed' && userList.length > 0">
                                    <div>
                                        <q-btn color="secondary" @click="validateAllUnconfirmedUsers();" label="Confirm All" dense tabindex="0" />
                                    </div>
                                    <div>
                                        <q-btn color="secondary" @click="deleteAllUnconfirmedUsers();" label="Delete All" dense tabindex="0" />
                                    </div>
                                </template>
                            </div>
                        </q-card-section>
                    </q-card>
                    <q-card>
                        <q-card-section class="column q-gutter-xs">
                            <div class="text-body1 text-bold q-mb-sm">Users</div>
                            <template v-if="userList.length > 0">
                                <template v-for="user in userList">
                                    <div role="button" class="text-body1 cursor-pointer" @click="processUserChange(user['uid']);" @keyup.enter="processUserChange(user['uid']);" aria-label="Change user" tabindex="0">
                                        {{ (user['lastname'] ? user['lastname'] : '') + ((user['lastname'] && user['firstname']) ? ', ' : '') + (user['firstname'] ? user['firstname'] : '') + ' (' + user['username'] + ')' }}
                                    </div>
                                </template>
                            </template>
                            <template v-else>
                                <div>There are no users to display</div>
                            </template>
                        </q-card-section>
                    </q-card>
                </template>
            </template>
            <template v-if="showAccountEditorPopup">
                <account-information-editor-popup
                    :show-popup="showAccountEditorPopup"
                    @account:edit="setUserList"
                    @close:popup="showAccountEditorPopup = false"
                ></account-information-editor-popup>
            </template>
        </div>
        <?php
        include_once(__DIR__ . '/../config/footer-includes.php');
        include(__DIR__ . '/../footer.php');
        ?>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/taxa-vernacular.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist-taxa.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/checklist.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/project.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/stores/user.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/checkboxInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/textFieldInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/input-elements/selectorInputElement.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationForm.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script src="<?php echo $GLOBALS['CLIENT_ROOT']; ?>/components/profile/accountInformationEditorPopup.js?ver=<?php echo $GLOBALS['JS_VERSION']; ?>" type="text/javascript"></script>
        <script type="text/javascript">
            const userManagementModule = Vue.createApp({
                components: {
                    'account-information-editor-popup': accountInformationEditorPopup,
                    'checkbox-input-element': checkboxInputElement,
                    'selector-input-element': selectorInputElement,
                    'text-field-input-element': textFieldInputElement
                },
                setup() {
                    const { showNotification } = useCore();
                    const baseStore = useBaseStore();
                    const userStore = useUserStore();

                    const checklistArr = Vue.ref([]);
                    const clAdminPermissionArr = Vue.computed(() => {
                        const returnArr = [];
                        if(userPermissions.value.hasOwnProperty('ClAdmin')){
                            Object.keys(userPermissions.value['ClAdmin']).forEach((id) => {
                                const permObj = {};
                                permObj['id'] = id;
                                permObj['name'] = userPermissions.value['ClAdmin'][id];
                                returnArr.push(permObj);
                            });
                        }
                        returnArr.sort((a, b) => {
                            return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const clientRoot = baseStore.getClientRoot;
                    const collAdminPermissionArr = Vue.computed(() => {
                        const returnArr = [];
                        if(userPermissions.value.hasOwnProperty('CollAdmin')){
                            Object.keys(userPermissions.value['CollAdmin']).forEach((id) => {
                                const permObj = {};
                                permObj['id'] = id;
                                permObj['name'] = userPermissions.value['CollAdmin'][id];
                                returnArr.push(permObj);
                            });
                        }
                        returnArr.sort((a, b) => {
                            return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const collectionArr = Vue.ref([]);
                    const collEditorPermissionArr = Vue.computed(() => {
                        const returnArr = [];
                        if(userPermissions.value.hasOwnProperty('CollEditor')){
                            Object.keys(userPermissions.value['CollEditor']).forEach((id) => {
                                const permObj = {};
                                permObj['id'] = id;
                                permObj['name'] = userPermissions.value['CollEditor'][id];
                                returnArr.push(permObj);
                            });
                        }
                        returnArr.sort((a, b) => {
                            return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const collRarePermissionArr = Vue.computed(() => {
                        const returnArr = [];
                        if(userPermissions.value.hasOwnProperty('RareSppReader')){
                            Object.keys(userPermissions.value['RareSppReader']).forEach((id) => {
                                const permObj = {};
                                permObj['id'] = id;
                                permObj['name'] = userPermissions.value['RareSppReader'][id];
                                returnArr.push(permObj);
                            });
                        }
                        returnArr.sort((a, b) => {
                            return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const currentUserId = Vue.ref(null);
                    const currentUserRights = Vue.computed(() => baseStore.getUserRights);
                    const filterOptions = Vue.computed(() => {
                        const filterStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                        return filterStr.split('');
                    });
                    const isAdmin = Vue.computed(() => {
                        return currentUserRights.value.hasOwnProperty('SuperAdmin') && currentUserRights.value['SuperAdmin'];
                    });
                    const newPermissionArr = Vue.reactive([]);
                    const projAdminPermissionArr = Vue.computed(() => {
                        const returnArr = [];
                        if(userPermissions.value.hasOwnProperty('ProjAdmin')){
                            Object.keys(userPermissions.value['ProjAdmin']).forEach((id) => {
                                const permObj = {};
                                permObj['id'] = id;
                                permObj['name'] = userPermissions.value['ProjAdmin'][id];
                                returnArr.push(permObj);
                            });
                        }
                        returnArr.sort((a, b) => {
                            return a['name'].toLowerCase().localeCompare(b['name'].toLowerCase());
                        });
                        return returnArr;
                    });
                    const projectArr = Vue.ref([]);
                    const resetPasswordValue = Vue.ref(null);
                    const showAccountEditorPopup = Vue.ref(false);
                    const userInfo = Vue.computed(() => userStore.getUserData);
                    const userList = Vue.ref([]);
                    const userListDisplayOptions = Vue.ref([
                        {value: 'all', label: 'All Users'},
                        {value: 'confirmed', label: 'Confirmed Users'},
                        {value: 'unconfirmed', label: 'Unconfirmed Users'}
                    ]);
                    const userListDisplaySelectedOption = Vue.ref('all');
                    const userListFilter = Vue.ref(null);
                    const userPermissions = Vue.computed(() => userStore.getUserPermissionData);

                    Vue.watch(currentUserRights, () => {
                        validateCurrentUser();
                    });

                    function addPermissions(permissionArr = null) {
                        userStore.addUserPermissions((permissionArr ? permissionArr : newPermissionArr), (res) => {
                            if(Number(res) === 1){
                                showNotification('positive','Permissions added.');
                                newPermissionArr.length = 0;
                            }
                            else{
                                showNotification('negative', 'An error occurred while adding the permissions.');
                            }
                        });
                    }

                    function confirmUser() {
                        userStore.updateUserEditData('validated', '1');
                        if(userStore.getUserEditsExist){
                            userStore.updateUserRecord((res) => {
                                if(Number(res) === 1){
                                    showNotification('positive','User confirmed.');
                                }
                                else{
                                    showNotification('negative', 'An error occurred while confirming this user.');
                                }
                            });
                        }
                    }

                    function deleteAllPermissions() {
                        userStore.deleteAllUserPermissions((res) => {
                            if(Number(res) === 0){
                                showNotification('negative', 'An error occurred while deleting the permissions.');
                            }
                        });
                    }

                    function deleteAllUnconfirmedUsers() {
                        userStore.deleteAllUnconfirmedUsers((res) => {
                            if(Number(res) === 1){
                                setUserList();
                            }
                            else{
                                showNotification('negative', 'An error occurred while deleting the unconfirmed users.');
                            }
                        });
                    }

                    function deletePermission(permission, pk) {
                        userStore.deleteUserPermission(permission, pk, (res) => {
                            if(Number(res) === 0){
                                showNotification('negative', 'An error occurred while deleting the permission.');
                            }
                        });
                    }

                    function loginAsUser() {
                        const formData = new FormData();
                        formData.append('username', userInfo.value['username']);
                        formData.append('action', 'loginAsUser');
                        fetch(profileApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.text() : null;
                        })
                        .then((res) => {
                            if(Number(res) === 1){
                                window.location.href = clientRoot + '/index.php';
                            }
                            else{
                                showNotification('negative', 'An error occurred while logging in as this user.');
                            }
                        });
                    }

                    function processFilterChange(value) {
                        userListFilter.value = value;
                        setUserList();
                    }

                    function processListDisplayOptionChange(value) {
                        userListDisplaySelectedOption.value = value;
                        setUserList();
                    }

                    function processPermissionArrChange(permission, value) {
                        if(value){
                            newPermissionArr.push(permission);
                        }
                        else{
                            const index = newPermissionArr.indexOf(permission);
                            newPermissionArr.splice(index, 1);
                        }
                    }

                    function processUserChange(uid) {
                        currentUserId.value = uid;
                        userStore.setUser(uid);
                        newPermissionArr.length = 0;
                        resetPasswordValue.value = null;
                    }

                    function resetPassword() {
                        userStore.resetPassword(userInfo.value['username'], true, (res) => {
                            if(res.toString() !== '0'){
                                resetPasswordValue.value = res;
                            }
                            else{
                                showNotification('negative','An error occurred while resetting the user password.');
                            }
                        });
                    }

                    function setChecklistArr() {
                        const formData = new FormData();
                        formData.append('action', 'getChecklistArr');
                        fetch(checklistApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData && resData.length > 0){
                                checklistArr.value = resData;
                            }
                        });
                    }

                    function setCollectionArr() {
                        const formData = new FormData();
                        formData.append('action', 'getCollectionArr');
                        fetch(collectionApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData && resData.length > 0){
                                resData.forEach((collection) => {
                                    let acroStr = '';
                                    if(collection['institutioncode'] || collection['collectioncode']){
                                        acroStr += ' (';
                                    }
                                    if(collection['institutioncode']){
                                        acroStr += collection['institutioncode'];
                                    }
                                    if(collection['institutioncode'] && collection['collectioncode']){
                                        acroStr += '-';
                                    }
                                    if(collection['collectioncode']){
                                        acroStr += collection['collectioncode'];
                                    }
                                    if(collection['institutioncode'] || collection['collectioncode']){
                                        acroStr += ')';
                                    }
                                    collection['acroStr'] = acroStr;
                                });
                                collectionArr.value = resData;
                            }
                        });
                    }

                    function setProjectArr() {
                        const formData = new FormData();
                        formData.append('action', 'getProjectArr');
                        fetch(projectApiUrl, {
                            method: 'POST',
                            body: formData
                        })
                        .then((response) => {
                            return response.ok ? response.json() : null;
                        })
                        .then((resData) => {
                            if(resData && resData.length > 0){
                                projectArr.value = resData;
                            }
                        });
                    }

                    function setUserList() {
                        userStore.getUserListArr(userListFilter.value, userListDisplaySelectedOption.value, (data) => {
                            userList.value = data;
                        });
                    }

                    function validateAllUnconfirmedUsers() {
                        userStore.validateAllUnconfirmedUsers((res) => {
                            if(Number(res) === 1){
                                setUserList();
                            }
                            else{
                                showNotification('negative', 'An error occurred while confirming the unconfirmed users.');
                            }
                        });
                    }

                    function validateCurrentUser() {
                        if(!isAdmin.value){
                            window.location.href = clientRoot + '/index.php';
                        }
                        else{
                            setUserList();
                            setCollectionArr();
                            setChecklistArr();
                            setProjectArr();
                        }
                    }

                    Vue.onMounted(() => {
                        baseStore.setUserRights();
                    });

                    return {
                        checklistArr,
                        clAdminPermissionArr,
                        clientRoot,
                        collAdminPermissionArr,
                        collectionArr,
                        collEditorPermissionArr,
                        collRarePermissionArr,
                        currentUserId,
                        filterOptions,
                        isAdmin,
                        newPermissionArr,
                        projAdminPermissionArr,
                        projectArr,
                        resetPasswordValue,
                        showAccountEditorPopup,
                        userInfo,
                        userList,
                        userListDisplayOptions,
                        userListDisplaySelectedOption,
                        userListFilter,
                        userPermissions,
                        addPermissions,
                        confirmUser,
                        deleteAllPermissions,
                        deleteAllUnconfirmedUsers,
                        deletePermission,
                        loginAsUser,
                        processFilterChange,
                        processListDisplayOptionChange,
                        processPermissionArrChange,
                        processUserChange,
                        resetPassword,
                        setUserList,
                        validateAllUnconfirmedUsers
                    }
                }
            });
            userManagementModule.use(Quasar, { config: {} });
            userManagementModule.use(Pinia.createPinia());
            userManagementModule.mount('#mainContainer');
        </script>
    </body>
</html>
