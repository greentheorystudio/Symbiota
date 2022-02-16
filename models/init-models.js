const DataTypes = require("sequelize").DataTypes;
const _fmchecklists = require("./checklist/fmchecklists");
const _fmchklstchildren = require("./checklist/fmchklstchildren");
const _fmchklstcoordinates = require("./checklist/fmchklstcoordinates");
const _fmchklstprojlink = require("./checklist/fmchklstprojlink");
const _fmchklsttaxalink = require("./checklist/fmchklsttaxalink");
const _fmchklsttaxastatus = require("./checklist/fmchklsttaxastatus");
const _fmcltaxacomments = require("./checklist/fmcltaxacomments");
const _fmdynamicchecklists = require("./checklist/fmdynamicchecklists");
const _fmdyncltaxalink = require("./checklist/fmdyncltaxalink");
const _fmprojectcategories = require("./checklist/fmprojectcategories");
const _fmprojects = require("./checklist/fmprojects");
const _fmvouchers = require("./checklist/fmvouchers");
const _glossary = require("./glossary/glossary");
const _glossaryimages = require("./glossary/glossaryimages");
const _glossarysources = require("./glossary/glossarysources");
const _glossarytaxalink = require("./glossary/glossarytaxalink");
const _glossarytermlink = require("./glossary/glossarytermlink");
const _guidimages = require("./media/guidimages");
const _guidoccurdeterminations = require("./occurrence/guidoccurdeterminations");
const _guidoccurrences = require("./occurrence/guidoccurrences");
const _igsnverification = require("./media/igsnverification");
const _imageannotations = require("./media/imageannotations");
const _imagekeywords = require("./media/imagekeywords");
const _images = require("./media/images");
const _imagetag = require("./media/imagetag");
const _imagetagkey = require("./media/imagetagkey");
const _institutions = require("./collection/institutions");
const _kmcharacterlang = require("./key/kmcharacterlang");
const _kmcharacters = require("./key/kmcharacters");
const _kmchardependence = require("./key/kmchardependence");
const _kmcharheading = require("./key/kmcharheading");
const _kmchartaxalink = require("./key/kmchartaxalink");
const _kmcs = require("./key/kmcs");
const _kmcsimages = require("./key/kmcsimages");
const _kmcslang = require("./key/kmcslang");
const _kmdescr = require("./key/kmdescr");
const _media = require("./media/media");
const _omcollcategories = require("./collection/omcollcategories");
const _omcollcatlink = require("./collection/omcollcatlink");
const _omcollections = require("./collection/omcollections");
const _omcollectionstats = require("./collection/omcollectionstats");
const _omcollectors = require("./occurrence/omcollectors");
const _omcollpublications = require("./collection/omcollpublications");
const _omcollpuboccurlink = require("./collection/omcollpuboccurlink");
const _omcollsecondary = require("./collection/omcollsecondary");
const _omcrowdsourcecentral = require("./occurrence/omcrowdsourcecentral");
const _omcrowdsourcequeue = require("./occurrence/omcrowdsourcequeue");
const _omexsiccatinumbers = require("./occurrence/omexsiccatinumbers");
const _omexsiccatiocclink = require("./occurrence/omexsiccatiocclink");
const _omexsiccatititles = require("./occurrence/omexsiccatititles");
const _omoccuraccessstats = require("./occurrence/omoccuraccessstats");
const _omoccurassociations = require("./occurrence/omoccurassociations");
const _omoccurcomments = require("./occurrence/omoccurcomments");
const _omoccurdatasetlink = require("./occurrence/omoccurdatasetlink");
const _omoccurdatasets = require("./occurrence/omoccurdatasets");
const _omoccurdeterminations = require("./occurrence/omoccurdeterminations");
const _omoccurduplicatelink = require("./occurrence/omoccurduplicatelink");
const _omoccurduplicates = require("./occurrence/omoccurduplicates");
const _omoccureditlocks = require("./occurrence/omoccureditlocks");
const _omoccuredits = require("./occurrence/omoccuredits");
const _omoccurexchange = require("./occurrence/omoccurexchange");
const _omoccurgenetic = require("./occurrence/omoccurgenetic");
const _omoccurgeoindex = require("./occurrence/omoccurgeoindex");
const _omoccuridentifiers = require("./occurrence/omoccuridentifiers");
const _omoccurlithostratigraphy = require("./occurrence/omoccurlithostratigraphy");
const _omoccurloans = require("./occurrence/omoccurloans");
const _omoccurloanslink = require("./occurrence/omoccurloanslink");
const _omoccurpaleo = require("./occurrence/omoccurpaleo");
const _omoccurpaleogts = require("./occurrence/omoccurpaleogts");
const _omoccurpoints = require("./occurrence/omoccurpoints");
const _omoccurrences = require("./occurrence/omoccurrences");
const _omoccurrencesfulltext = require("./occurrence/omoccurrencesfulltext");
const _omoccurrencetypes = require("./occurrence/omoccurrencetypes");
const _omoccurrevisions = require("./occurrence/omoccurrevisions");
const _omoccurverification = require("./occurrence/omoccurverification");
const _referenceauthorlink = require("./reference/referenceauthorlink");
const _referenceauthors = require("./reference/referenceauthors");
const _referencechecklistlink = require("./reference/referencechecklistlink");
const _referencechklsttaxalink = require("./reference/referencechklsttaxalink");
const _referencecollectionlink = require("./reference/referencecollectionlink");
const _referenceobject = require("./reference/referenceobject");
const _referenceoccurlink = require("./reference/referenceoccurlink");
const _referencetaxalink = require("./reference/referencetaxalink");
const _referencetype = require("./reference/referencetype");
const _specprocessorprojects = require("./ocr/specprocessorprojects");
const _specprocessorrawlabels = require("./ocr/specprocessorrawlabels");
const _specprococrfrag = require("./ocr/specprococrfrag");
const _taxa = require("./taxonomy/taxa");
const _taxadescrblock = require("./taxonomy/taxadescrblock");
const _taxadescrstmts = require("./taxonomy/taxadescrstmts");
const _taxaenumtree = require("./taxonomy/taxaenumtree");
const _taxalinks = require("./taxonomy/taxalinks");
const _taxamaps = require("./taxonomy/taxamaps");
const _taxaprofilepubdesclink = require("./taxonomy/taxaprofilepubdesclink");
const _taxaprofilepubimagelink = require("./taxonomy/taxaprofilepubimagelink");
const _taxaprofilepubmaplink = require("./taxonomy/taxaprofilepubmaplink");
const _taxaprofilepubs = require("./taxonomy/taxaprofilepubs");
const _taxaresourcelinks = require("./taxonomy/taxaresourcelinks");
const _taxauthority = require("./taxauthority");
const _taxavernaculars = require("./taxonomy/taxavernaculars");
const _taxonkingdoms = require("./taxonomy/taxonkingdoms");
const _taxonunits = require("./taxonomy/taxonunits");
const _taxstatus = require("./taxonomy/taxstatus");
const _tmattributes = require("./traits/tmattributes");
const _tmstates = require("./traits/tmstates");
const _tmtraitdependencies = require("./traits/tmtraitdependencies");
const _tmtraits = require("./traits/tmtraits");
const _tmtraittaxalink = require("./traits/tmtraittaxalink");
const _uploaddetermtemp = require("./upload/uploaddetermtemp");
const _uploadglossary = require("./upload/uploadglossary");
const _uploadimagetemp = require("./upload/uploadimagetemp");
const _uploadspecmap = require("./upload/uploadspecmap");
const _uploadspecparameters = require("./upload/uploadspecparameters");
const _uploadspectemp = require("./upload/uploadspectemp");
const _uploadspectemppoints = require("./upload/uploadspectemppoints");
const _uploadtaxa = require("./upload/uploadtaxa");
const _usertaxonomy = require("./taxonomy/usertaxonomy");

function initModels(sequelize) {
    const fmchecklists = _fmchecklists(sequelize, DataTypes);
    const fmchklstchildren = _fmchklstchildren(sequelize, DataTypes);
    const fmchklstcoordinates = _fmchklstcoordinates(sequelize, DataTypes);
    const fmchklstprojlink = _fmchklstprojlink(sequelize, DataTypes);
    const fmchklsttaxalink = _fmchklsttaxalink(sequelize, DataTypes);
    const fmchklsttaxastatus = _fmchklsttaxastatus(sequelize, DataTypes);
    const fmcltaxacomments = _fmcltaxacomments(sequelize, DataTypes);
    const fmdynamicchecklists = _fmdynamicchecklists(sequelize, DataTypes);
    const fmdyncltaxalink = _fmdyncltaxalink(sequelize, DataTypes);
    const fmprojectcategories = _fmprojectcategories(sequelize, DataTypes);
    const fmprojects = _fmprojects(sequelize, DataTypes);
    const fmvouchers = _fmvouchers(sequelize, DataTypes);
    const glossary = _glossary(sequelize, DataTypes);
    const glossaryimages = _glossaryimages(sequelize, DataTypes);
    const glossarysources = _glossarysources(sequelize, DataTypes);
    const glossarytaxalink = _glossarytaxalink(sequelize, DataTypes);
    const glossarytermlink = _glossarytermlink(sequelize, DataTypes);
    const guidimages = _guidimages(sequelize, DataTypes);
    const guidoccurdeterminations = _guidoccurdeterminations(sequelize, DataTypes);
    const guidoccurrences = _guidoccurrences(sequelize, DataTypes);
    const igsnverification = _igsnverification(sequelize, DataTypes);
    const imageannotations = _imageannotations(sequelize, DataTypes);
    const imagekeywords = _imagekeywords(sequelize, DataTypes);
    const images = _images(sequelize, DataTypes);
    const imagetag = _imagetag(sequelize, DataTypes);
    const imagetagkey = _imagetagkey(sequelize, DataTypes);
    const institutions = _institutions(sequelize, DataTypes);
    const kmcharacterlang = _kmcharacterlang(sequelize, DataTypes);
    const kmcharacters = _kmcharacters(sequelize, DataTypes);
    const kmchardependence = _kmchardependence(sequelize, DataTypes);
    const kmcharheading = _kmcharheading(sequelize, DataTypes);
    const kmchartaxalink = _kmchartaxalink(sequelize, DataTypes);
    const kmcs = _kmcs(sequelize, DataTypes);
    const kmcsimages = _kmcsimages(sequelize, DataTypes);
    const kmcslang = _kmcslang(sequelize, DataTypes);
    const kmdescr = _kmdescr(sequelize, DataTypes);
    const media = _media(sequelize, DataTypes);
    const omcollcategories = _omcollcategories(sequelize, DataTypes);
    const omcollcatlink = _omcollcatlink(sequelize, DataTypes);
    const omcollections = _omcollections(sequelize, DataTypes);
    const omcollectionstats = _omcollectionstats(sequelize, DataTypes);
    const omcollectors = _omcollectors(sequelize, DataTypes);
    const omcollpublications = _omcollpublications(sequelize, DataTypes);
    const omcollpuboccurlink = _omcollpuboccurlink(sequelize, DataTypes);
    const omcollsecondary = _omcollsecondary(sequelize, DataTypes);
    const omcrowdsourcecentral = _omcrowdsourcecentral(sequelize, DataTypes);
    const omcrowdsourcequeue = _omcrowdsourcequeue(sequelize, DataTypes);
    const omexsiccatinumbers = _omexsiccatinumbers(sequelize, DataTypes);
    const omexsiccatiocclink = _omexsiccatiocclink(sequelize, DataTypes);
    const omexsiccatititles = _omexsiccatititles(sequelize, DataTypes);
    const omoccuraccessstats = _omoccuraccessstats(sequelize, DataTypes);
    const omoccurassociations = _omoccurassociations(sequelize, DataTypes);
    const omoccurcomments = _omoccurcomments(sequelize, DataTypes);
    const omoccurdatasetlink = _omoccurdatasetlink(sequelize, DataTypes);
    const omoccurdatasets = _omoccurdatasets(sequelize, DataTypes);
    const omoccurdeterminations = _omoccurdeterminations(sequelize, DataTypes);
    const omoccurduplicatelink = _omoccurduplicatelink(sequelize, DataTypes);
    const omoccurduplicates = _omoccurduplicates(sequelize, DataTypes);
    const omoccureditlocks = _omoccureditlocks(sequelize, DataTypes);
    const omoccuredits = _omoccuredits(sequelize, DataTypes);
    const omoccurexchange = _omoccurexchange(sequelize, DataTypes);
    const omoccurgenetic = _omoccurgenetic(sequelize, DataTypes);
    const omoccurgeoindex = _omoccurgeoindex(sequelize, DataTypes);
    const omoccuridentifiers = _omoccuridentifiers(sequelize, DataTypes);
    const omoccurlithostratigraphy = _omoccurlithostratigraphy(sequelize, DataTypes);
    const omoccurloans = _omoccurloans(sequelize, DataTypes);
    const omoccurloanslink = _omoccurloanslink(sequelize, DataTypes);
    const omoccurpaleo = _omoccurpaleo(sequelize, DataTypes);
    const omoccurpaleogts = _omoccurpaleogts(sequelize, DataTypes);
    const omoccurpoints = _omoccurpoints(sequelize, DataTypes);
    const omoccurrences = _omoccurrences(sequelize, DataTypes);
    const omoccurrencesfulltext = _omoccurrencesfulltext(sequelize, DataTypes);
    const omoccurrencetypes = _omoccurrencetypes(sequelize, DataTypes);
    const omoccurrevisions = _omoccurrevisions(sequelize, DataTypes);
    const omoccurverification = _omoccurverification(sequelize, DataTypes);
    const referenceauthorlink = _referenceauthorlink(sequelize, DataTypes);
    const referenceauthors = _referenceauthors(sequelize, DataTypes);
    const referencechecklistlink = _referencechecklistlink(sequelize, DataTypes);
    const referencechklsttaxalink = _referencechklsttaxalink(sequelize, DataTypes);
    const referencecollectionlink = _referencecollectionlink(sequelize, DataTypes);
    const referenceobject = _referenceobject(sequelize, DataTypes);
    const referenceoccurlink = _referenceoccurlink(sequelize, DataTypes);
    const referencetaxalink = _referencetaxalink(sequelize, DataTypes);
    const referencetype = _referencetype(sequelize, DataTypes);
    const specprocessorprojects = _specprocessorprojects(sequelize, DataTypes);
    const specprocessorrawlabels = _specprocessorrawlabels(sequelize, DataTypes);
    const specprococrfrag = _specprococrfrag(sequelize, DataTypes);
    const taxa = _taxa(sequelize, DataTypes);
    const taxadescrblock = _taxadescrblock(sequelize, DataTypes);
    const taxadescrstmts = _taxadescrstmts(sequelize, DataTypes);
    const taxaenumtree = _taxaenumtree(sequelize, DataTypes);
    const taxalinks = _taxalinks(sequelize, DataTypes);
    const taxamaps = _taxamaps(sequelize, DataTypes);
    const taxaprofilepubdesclink = _taxaprofilepubdesclink(sequelize, DataTypes);
    const taxaprofilepubimagelink = _taxaprofilepubimagelink(sequelize, DataTypes);
    const taxaprofilepubmaplink = _taxaprofilepubmaplink(sequelize, DataTypes);
    const taxaprofilepubs = _taxaprofilepubs(sequelize, DataTypes);
    const taxaresourcelinks = _taxaresourcelinks(sequelize, DataTypes);
    const taxauthority = _taxauthority(sequelize, DataTypes);
    const taxavernaculars = _taxavernaculars(sequelize, DataTypes);
    const taxonkingdoms = _taxonkingdoms(sequelize, DataTypes);
    const taxonunits = _taxonunits(sequelize, DataTypes);
    const taxstatus = _taxstatus(sequelize, DataTypes);
    const tmattributes = _tmattributes(sequelize, DataTypes);
    const tmstates = _tmstates(sequelize, DataTypes);
    const tmtraitdependencies = _tmtraitdependencies(sequelize, DataTypes);
    const tmtraits = _tmtraits(sequelize, DataTypes);
    const tmtraittaxalink = _tmtraittaxalink(sequelize, DataTypes);
    const uploaddetermtemp = _uploaddetermtemp(sequelize, DataTypes);
    const uploadglossary = _uploadglossary(sequelize, DataTypes);
    const uploadimagetemp = _uploadimagetemp(sequelize, DataTypes);
    const uploadspecmap = _uploadspecmap(sequelize, DataTypes);
    const uploadspecparameters = _uploadspecparameters(sequelize, DataTypes);
    const uploadspectemp = _uploadspectemp(sequelize, DataTypes);
    const uploadspectemppoints = _uploadspectemppoints(sequelize, DataTypes);
    const uploadtaxa = _uploadtaxa(sequelize, DataTypes);
    const usertaxonomy = _usertaxonomy(sequelize, DataTypes);

    adminlanguages.belongsToMany(kmcharacters, {
        as: 'cid_kmcharacters',
        through: kmcharacterlang,
        foreignKey: "langid",
        otherKey: "cid"
    });
    fmchecklists.belongsToMany(fmchecklists, {
        as: 'clidchild_fmchecklists',
        through: fmchklstchildren,
        foreignKey: "clid",
        otherKey: "clidchild"
    });
    fmchecklists.belongsToMany(fmchecklists, {
        as: 'clid_fmchecklists',
        through: fmchklstchildren,
        foreignKey: "clidchild",
        otherKey: "clid"
    });
    fmchecklists.belongsToMany(fmprojects, {
        as: 'pid_fmprojects',
        through: fmchklstprojlink,
        foreignKey: "clid",
        otherKey: "pid"
    });
    fmchecklists.belongsToMany(referenceobject, {
        as: 'refid_referenceobject_referencechecklistlinks',
        through: referencechecklistlink,
        foreignKey: "clid",
        otherKey: "refid"
    });
    fmdynamicchecklists.belongsToMany(taxa, {
        as: 'tid_taxas',
        through: fmdyncltaxalink,
        foreignKey: "dynclid",
        otherKey: "tid"
    });
    fmprojects.belongsToMany(fmchecklists, {
        as: 'clid_fmchecklists_fmchklstprojlinks',
        through: fmchklstprojlink,
        foreignKey: "pid",
        otherKey: "clid"
    });
    glossary.belongsToMany(taxa, {
        as: 'tid_taxa_glossarytaxalinks',
        through: glossarytaxalink,
        foreignKey: "glossid",
        otherKey: "tid"
    });
    images.belongsToMany(taxaprofilepubs, {
        as: 'tppid_taxaprofilepubs_taxaprofilepubimagelinks',
        through: taxaprofilepubimagelink,
        foreignKey: "imgid",
        otherKey: "tppid"
    });
    kmcharacters.belongsToMany(adminlanguages, {
        as: 'langid_adminlanguages',
        through: kmcharacterlang,
        foreignKey: "cid",
        otherKey: "langid"
    });
    kmcharacters.belongsToMany(taxa, {as: 'TID_taxas', through: kmchartaxalink, foreignKey: "CID", otherKey: "TID"});
    omcollcategories.belongsToMany(omcollections, {
        as: 'collid_omcollections',
        through: omcollcatlink,
        foreignKey: "ccpk",
        otherKey: "collid"
    });
    omcollections.belongsToMany(omcollcategories, {
        as: 'ccpk_omcollcategories',
        through: omcollcatlink,
        foreignKey: "collid",
        otherKey: "ccpk"
    });
    omcollections.belongsToMany(referenceobject, {
        as: 'refid_referenceobject_referencecollectionlinks',
        through: referencecollectionlink,
        foreignKey: "collid",
        otherKey: "refid"
    });
    omcollpublications.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences',
        through: omcollpuboccurlink,
        foreignKey: "pubid",
        otherKey: "occid"
    });
    omexsiccatinumbers.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_omexsiccatiocclinks',
        through: omexsiccatiocclink,
        foreignKey: "omenid",
        otherKey: "occid"
    });
    omoccurdatasets.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_omoccurdatasetlinks',
        through: omoccurdatasetlink,
        foreignKey: "datasetid",
        otherKey: "occid"
    });
    omoccurduplicates.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_omoccurduplicatelinks',
        through: omoccurduplicatelink,
        foreignKey: "duplicateid",
        otherKey: "occid"
    });
    omoccurloans.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_omoccurloanslinks',
        through: omoccurloanslink,
        foreignKey: "loanid",
        otherKey: "occid"
    });
    omoccurrences.belongsToMany(omcollpublications, {
        as: 'pubid_omcollpublications',
        through: omcollpuboccurlink,
        foreignKey: "occid",
        otherKey: "pubid"
    });
    omoccurrences.belongsToMany(omexsiccatinumbers, {
        as: 'omenid_omexsiccatinumbers',
        through: omexsiccatiocclink,
        foreignKey: "occid",
        otherKey: "omenid"
    });
    omoccurrences.belongsToMany(omoccurdatasets, {
        as: 'datasetid_omoccurdatasets',
        through: omoccurdatasetlink,
        foreignKey: "occid",
        otherKey: "datasetid"
    });
    omoccurrences.belongsToMany(omoccurduplicates, {
        as: 'duplicateid_omoccurduplicates',
        through: omoccurduplicatelink,
        foreignKey: "occid",
        otherKey: "duplicateid"
    });
    omoccurrences.belongsToMany(omoccurloans, {
        as: 'loanid_omoccurloans',
        through: omoccurloanslink,
        foreignKey: "occid",
        otherKey: "loanid"
    });
    omoccurrences.belongsToMany(paleochronostratigraphy, {
        as: 'chronoId_paleochronostratigraphies',
        through: omoccurlithostratigraphy,
        foreignKey: "occid",
        otherKey: "chronoId"
    });
    omoccurrences.belongsToMany(referenceobject, {
        as: 'refid_referenceobject_referenceoccurlinks',
        through: referenceoccurlink,
        foreignKey: "occid",
        otherKey: "refid"
    });
    omoccurrences.belongsToMany(tmstates, {
        as: 'stateid_tmstates',
        through: tmattributes,
        foreignKey: "occid",
        otherKey: "stateid"
    });
    paleochronostratigraphy.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_omoccurlithostratigraphies',
        through: omoccurlithostratigraphy,
        foreignKey: "chronoId",
        otherKey: "occid"
    });
    referenceauthors.belongsToMany(referenceobject, {
        as: 'refid_referenceobjects',
        through: referenceauthorlink,
        foreignKey: "refauthid",
        otherKey: "refid"
    });
    referenceobject.belongsToMany(fmchecklists, {
        as: 'clid_fmchecklists_referencechecklistlinks',
        through: referencechecklistlink,
        foreignKey: "refid",
        otherKey: "clid"
    });
    referenceobject.belongsToMany(omcollections, {
        as: 'collid_omcollections_referencecollectionlinks',
        through: referencecollectionlink,
        foreignKey: "refid",
        otherKey: "collid"
    });
    referenceobject.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_referenceoccurlinks',
        through: referenceoccurlink,
        foreignKey: "refid",
        otherKey: "occid"
    });
    referenceobject.belongsToMany(referenceauthors, {
        as: 'refauthid_referenceauthors',
        through: referenceauthorlink,
        foreignKey: "refid",
        otherKey: "refauthid"
    });
    referenceobject.belongsToMany(taxa, {
        as: 'tid_taxa_referencetaxalinks',
        through: referencetaxalink,
        foreignKey: "refid",
        otherKey: "tid"
    });
    taxa.belongsToMany(fmdynamicchecklists, {
        as: 'dynclid_fmdynamicchecklists',
        through: fmdyncltaxalink,
        foreignKey: "tid",
        otherKey: "dynclid"
    });
    taxa.belongsToMany(glossary, {
        as: 'glossid_glossaries',
        through: glossarytaxalink,
        foreignKey: "tid",
        otherKey: "glossid"
    });
    taxa.belongsToMany(kmcharacters, {
        as: 'CID_kmcharacters',
        through: kmchartaxalink,
        foreignKey: "TID",
        otherKey: "CID"
    });
    taxa.belongsToMany(referenceobject, {
        as: 'refid_referenceobject_referencetaxalinks',
        through: referencetaxalink,
        foreignKey: "tid",
        otherKey: "refid"
    });
    taxa.belongsToMany(taxa, {as: 'parenttid_taxas', through: taxaenumtree, foreignKey: "tid", otherKey: "parenttid"});
    taxa.belongsToMany(taxa, {
        as: 'tid_taxa_taxaenumtrees',
        through: taxaenumtree,
        foreignKey: "parenttid",
        otherKey: "tid"
    });
    taxa.belongsToMany(taxa, {as: 'tidaccepted_taxas', through: taxstatus, foreignKey: "tid", otherKey: "tidaccepted"});
    taxa.belongsToMany(taxa, {
        as: 'tid_taxa_taxstatuses',
        through: taxstatus,
        foreignKey: "tidaccepted",
        otherKey: "tid"
    });
    taxa.belongsToMany(tmtraits, {
        as: 'traitid_tmtraits_tmtraittaxalinks',
        through: tmtraittaxalink,
        foreignKey: "tid",
        otherKey: "traitid"
    });
    taxadescrblock.belongsToMany(taxaprofilepubs, {
        as: 'tppid_taxaprofilepubs',
        through: taxaprofilepubdesclink,
        foreignKey: "tdbid",
        otherKey: "tppid"
    });
    taxamaps.belongsToMany(taxaprofilepubs, {
        as: 'tppid_taxaprofilepubs_taxaprofilepubmaplinks',
        through: taxaprofilepubmaplink,
        foreignKey: "mid",
        otherKey: "tppid"
    });
    taxaprofilepubs.belongsToMany(images, {
        as: 'imgid_images',
        through: taxaprofilepubimagelink,
        foreignKey: "tppid",
        otherKey: "imgid"
    });
    taxaprofilepubs.belongsToMany(taxadescrblock, {
        as: 'tdbid_taxadescrblocks',
        through: taxaprofilepubdesclink,
        foreignKey: "tppid",
        otherKey: "tdbid"
    });
    taxaprofilepubs.belongsToMany(taxamaps, {
        as: 'mid_taxamaps',
        through: taxaprofilepubmaplink,
        foreignKey: "tppid",
        otherKey: "mid"
    });
    tmstates.belongsToMany(omoccurrences, {
        as: 'occid_omoccurrences_tmattributes',
        through: tmattributes,
        foreignKey: "stateid",
        otherKey: "occid"
    });
    tmstates.belongsToMany(tmtraits, {
        as: 'traitid_tmtraits',
        through: tmtraitdependencies,
        foreignKey: "parentstateid",
        otherKey: "traitid"
    });
    tmtraits.belongsToMany(taxa, {
        as: 'tid_taxa_tmtraittaxalinks',
        through: tmtraittaxalink,
        foreignKey: "traitid",
        otherKey: "tid"
    });
    tmtraits.belongsToMany(tmstates, {
        as: 'parentstateid_tmstates',
        through: tmtraitdependencies,
        foreignKey: "traitid",
        otherKey: "parentstateid"
    });
    kmcharacterlang.belongsTo(adminlanguages, {as: "lang", foreignKey: "langid"});
    adminlanguages.hasMany(kmcharacterlang, {as: "kmcharacterlangs", foreignKey: "langid"});
    kmcharheading.belongsTo(adminlanguages, {as: "lang", foreignKey: "langid"});
    adminlanguages.hasMany(kmcharheading, {as: "kmcharheadings", foreignKey: "langid"});
    kmcslang.belongsTo(adminlanguages, {as: "lang", foreignKey: "langid"});
    adminlanguages.hasMany(kmcslang, {as: "kmcslangs", foreignKey: "langid"});
    taxadescrblock.belongsTo(adminlanguages, {as: "lang", foreignKey: "langid"});
    adminlanguages.hasMany(taxadescrblock, {as: "taxadescrblocks", foreignKey: "langid"});
    taxavernaculars.belongsTo(adminlanguages, {as: "lang", foreignKey: "langid"});
    adminlanguages.hasMany(taxavernaculars, {as: "taxavernaculars", foreignKey: "langid"});
    fmchklstchildren.belongsTo(fmchecklists, {as: "cl", foreignKey: "clid"});
    fmchecklists.hasMany(fmchklstchildren, {as: "fmchklstchildren", foreignKey: "clid"});
    fmchklstchildren.belongsTo(fmchecklists, {as: "clidchild_fmchecklist", foreignKey: "clidchild"});
    fmchecklists.hasMany(fmchklstchildren, {as: "clidchild_fmchklstchildren", foreignKey: "clidchild"});
    fmchklstprojlink.belongsTo(fmchecklists, {as: "cl", foreignKey: "clid"});
    fmchecklists.hasMany(fmchklstprojlink, {as: "fmchklstprojlinks", foreignKey: "clid"});
    referencechecklistlink.belongsTo(fmchecklists, {as: "cl", foreignKey: "clid"});
    fmchecklists.hasMany(referencechecklistlink, {as: "referencechecklistlinks", foreignKey: "clid"});
    referencechklsttaxalink.belongsTo(fmchklsttaxalink, {as: "cl", foreignKey: "clid"});
    fmchklsttaxalink.hasMany(referencechklsttaxalink, {as: "referencechklsttaxalinks", foreignKey: "clid"});
    referencechklsttaxalink.belongsTo(fmchklsttaxalink, {as: "tid_fmchklsttaxalink", foreignKey: "tid"});
    fmchklsttaxalink.hasMany(referencechklsttaxalink, {as: "tid_referencechklsttaxalinks", foreignKey: "tid"});
    fmdyncltaxalink.belongsTo(fmdynamicchecklists, {as: "dyncl", foreignKey: "dynclid"});
    fmdynamicchecklists.hasMany(fmdyncltaxalink, {as: "fmdyncltaxalinks", foreignKey: "dynclid"});
    fmchklstprojlink.belongsTo(fmprojects, {as: "pid_fmproject", foreignKey: "pid"});
    fmprojects.hasMany(fmchklstprojlink, {as: "fmchklstprojlinks", foreignKey: "pid"});
    fmprojectcategories.belongsTo(fmprojects, {as: "pid_fmproject", foreignKey: "pid"});
    fmprojects.hasMany(fmprojectcategories, {as: "fmprojectcategories", foreignKey: "pid"});
    fmprojects.belongsTo(fmprojects, {as: "parentp", foreignKey: "parentpid"});
    fmprojects.hasMany(fmprojects, {as: "fmprojects", foreignKey: "parentpid"});
    glossaryimages.belongsTo(glossary, {as: "gloss", foreignKey: "glossid"});
    glossary.hasMany(glossaryimages, {as: "glossaryimages", foreignKey: "glossid"});
    glossarytaxalink.belongsTo(glossary, {as: "gloss", foreignKey: "glossid"});
    glossary.hasMany(glossarytaxalink, {as: "glossarytaxalinks", foreignKey: "glossid"});
    glossarytermlink.belongsTo(glossary, {as: "glossgrp", foreignKey: "glossgrpid"});
    glossary.hasMany(glossarytermlink, {as: "glossarytermlinks", foreignKey: "glossgrpid"});
    glossarytermlink.belongsTo(glossary, {as: "gloss", foreignKey: "glossid"});
    glossary.hasMany(glossarytermlink, {as: "gloss_glossarytermlinks", foreignKey: "glossid"});
    imageannotations.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(imageannotations, {as: "imageannotations", foreignKey: "imgid"});
    imagekeywords.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(imagekeywords, {as: "imagekeywords", foreignKey: "imgid"});
    imagetag.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(imagetag, {as: "imagetags", foreignKey: "imgid"});
    specprocessorrawlabels.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(specprocessorrawlabels, {as: "specprocessorrawlabels", foreignKey: "imgid"});
    taxaprofilepubimagelink.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(taxaprofilepubimagelink, {as: "taxaprofilepubimagelinks", foreignKey: "imgid"});
    tmattributes.belongsTo(images, {as: "img", foreignKey: "imgid"});
    images.hasMany(tmattributes, {as: "tmattributes", foreignKey: "imgid"});
    imagetag.belongsTo(imagetagkey, {as: "keyvalue_imagetagkey", foreignKey: "keyvalue"});
    imagetagkey.hasMany(imagetag, {as: "imagetags", foreignKey: "keyvalue"});
    omcollections.belongsTo(institutions, {as: "iid_institution", foreignKey: "iid"});
    institutions.hasMany(omcollections, {as: "omcollections", foreignKey: "iid"});
    omoccurloans.belongsTo(institutions, {as: "iidOwner_institution", foreignKey: "iidOwner"});
    institutions.hasMany(omoccurloans, {as: "omoccurloans", foreignKey: "iidOwner"});
    omoccurloans.belongsTo(institutions, {as: "iidBorrower_institution", foreignKey: "iidBorrower"});
    institutions.hasMany(omoccurloans, {as: "iidBorrower_omoccurloans", foreignKey: "iidBorrower"});
    kmcharacterlang.belongsTo(kmcharacters, {as: "cid_kmcharacter", foreignKey: "cid"});
    kmcharacters.hasMany(kmcharacterlang, {as: "kmcharacterlangs", foreignKey: "cid"});
    kmchardependence.belongsTo(kmcharacters, {as: "CID_kmcharacter", foreignKey: "CID"});
    kmcharacters.hasMany(kmchardependence, {as: "kmchardependences", foreignKey: "CID"});
    kmchartaxalink.belongsTo(kmcharacters, {as: "CID_kmcharacter", foreignKey: "CID"});
    kmcharacters.hasMany(kmchartaxalink, {as: "kmchartaxalinks", foreignKey: "CID"});
    kmcs.belongsTo(kmcharacters, {as: "cid_kmcharacter", foreignKey: "cid"});
    kmcharacters.hasMany(kmcs, {as: "kmcs", foreignKey: "cid"});
    kmcharacters.belongsTo(kmcharheading, {as: "hid_kmcharheading", foreignKey: "hid"});
    kmcharheading.hasMany(kmcharacters, {as: "kmcharacters", foreignKey: "hid"});
    kmchardependence.belongsTo(kmcs, {as: "CIDDependance_kmc", foreignKey: "CIDDependance"});
    kmcs.hasMany(kmchardependence, {as: "kmchardependences", foreignKey: "CIDDependance"});
    kmchardependence.belongsTo(kmcs, {as: "CSDependance_kmc", foreignKey: "CSDependance"});
    kmcs.hasMany(kmchardependence, {as: "CSDependance_kmchardependences", foreignKey: "CSDependance"});
    kmcsimages.belongsTo(kmcs, {as: "cid_kmc", foreignKey: "cid"});
    kmcs.hasMany(kmcsimages, {as: "kmcsimages", foreignKey: "cid"});
    kmcsimages.belongsTo(kmcs, {as: "cs_kmc", foreignKey: "cs"});
    kmcs.hasMany(kmcsimages, {as: "cs_kmcsimages", foreignKey: "cs"});
    kmcslang.belongsTo(kmcs, {as: "cid_kmc", foreignKey: "cid"});
    kmcs.hasMany(kmcslang, {as: "kmcslangs", foreignKey: "cid"});
    kmcslang.belongsTo(kmcs, {as: "cs_kmc", foreignKey: "cs"});
    kmcs.hasMany(kmcslang, {as: "cs_kmcslangs", foreignKey: "cs"});
    kmdescr.belongsTo(kmcs, {as: "CID_kmc", foreignKey: "CID"});
    kmcs.hasMany(kmdescr, {as: "kmdescrs", foreignKey: "CID"});
    kmdescr.belongsTo(kmcs, {as: "CS_kmc", foreignKey: "CS"});
    kmcs.hasMany(kmdescr, {as: "CS_kmdescrs", foreignKey: "CS"});
    omcollcatlink.belongsTo(omcollcategories, {as: "ccpk_omcollcategory", foreignKey: "ccpk"});
    omcollcategories.hasMany(omcollcatlink, {as: "omcollcatlinks", foreignKey: "ccpk"});
    omcollcatlink.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omcollcatlink, {as: "omcollcatlinks", foreignKey: "collid"});
    omcollectionstats.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasOne(omcollectionstats, {as: "omcollectionstat", foreignKey: "collid"});
    omcollpublications.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omcollpublications, {as: "omcollpublications", foreignKey: "collid"});
    omcollsecondary.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omcollsecondary, {as: "omcollsecondaries", foreignKey: "collid"});
    omcrowdsourcecentral.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasOne(omcrowdsourcecentral, {as: "omcrowdsourcecentral", foreignKey: "collid"});
    omoccurdatasets.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omoccurdatasets, {as: "omoccurdatasets", foreignKey: "collid"});
    omoccurexchange.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omoccurexchange, {as: "omoccurexchanges", foreignKey: "collid"});
    omoccurloans.belongsTo(omcollections, {as: "collidOwn_omcollection", foreignKey: "collidOwn"});
    omcollections.hasMany(omoccurloans, {as: "omoccurloans", foreignKey: "collidOwn"});
    omoccurloans.belongsTo(omcollections, {as: "collidBorr_omcollection", foreignKey: "collidBorr"});
    omcollections.hasMany(omoccurloans, {as: "collidBorr_omoccurloans", foreignKey: "collidBorr"});
    omoccurrences.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(omoccurrences, {as: "omoccurrences", foreignKey: "collid"});
    referencecollectionlink.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(referencecollectionlink, {as: "referencecollectionlinks", foreignKey: "collid"});
    specprocessorprojects.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(specprocessorprojects, {as: "specprocessorprojects", foreignKey: "collid"});
    uploadspecparameters.belongsTo(omcollections, {as: "Coll", foreignKey: "CollID"});
    omcollections.hasMany(uploadspecparameters, {as: "uploadspecparameters", foreignKey: "CollID"});
    uploadspectemp.belongsTo(omcollections, {as: "coll", foreignKey: "collid"});
    omcollections.hasMany(uploadspectemp, {as: "uploadspectemps", foreignKey: "collid"});
    omoccurdeterminations.belongsTo(omcollectors, {as: "idby", foreignKey: "idbyid"});
    omcollectors.hasMany(omoccurdeterminations, {as: "omoccurdeterminations", foreignKey: "idbyid"});
    omcollpuboccurlink.belongsTo(omcollpublications, {as: "pub", foreignKey: "pubid"});
    omcollpublications.hasMany(omcollpuboccurlink, {as: "omcollpuboccurlinks", foreignKey: "pubid"});
    omexsiccatiocclink.belongsTo(omexsiccatinumbers, {as: "omen", foreignKey: "omenid"});
    omexsiccatinumbers.hasMany(omexsiccatiocclink, {as: "omexsiccatiocclinks", foreignKey: "omenid"});
    omexsiccatinumbers.belongsTo(omexsiccatititles, {as: "omet", foreignKey: "ometid"});
    omexsiccatititles.hasMany(omexsiccatinumbers, {as: "omexsiccatinumbers", foreignKey: "ometid"});
    omoccurdatasetlink.belongsTo(omoccurdatasets, {as: "dataset", foreignKey: "datasetid"});
    omoccurdatasets.hasMany(omoccurdatasetlink, {as: "omoccurdatasetlinks", foreignKey: "datasetid"});
    omoccurduplicatelink.belongsTo(omoccurduplicates, {as: "duplicate", foreignKey: "duplicateid"});
    omoccurduplicates.hasMany(omoccurduplicatelink, {as: "omoccurduplicatelinks", foreignKey: "duplicateid"});
    omoccurloanslink.belongsTo(omoccurloans, {as: "loan", foreignKey: "loanid"});
    omoccurloans.hasMany(omoccurloanslink, {as: "omoccurloanslinks", foreignKey: "loanid"});
    omoccurpaleogts.belongsTo(omoccurpaleogts, {as: "parentgt", foreignKey: "parentgtsid"});
    omoccurpaleogts.hasMany(omoccurpaleogts, {as: "omoccurpaleogts", foreignKey: "parentgtsid"});
    igsnverification.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(igsnverification, {as: "igsnverifications", foreignKey: "occid"});
    images.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(images, {as: "images", foreignKey: "occid"});
    media.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(media, {as: "media", foreignKey: "occid"});
    omcollpuboccurlink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omcollpuboccurlink, {as: "omcollpuboccurlinks", foreignKey: "occid"});
    omcrowdsourcequeue.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasOne(omcrowdsourcequeue, {as: "omcrowdsourcequeue", foreignKey: "occid"});
    omexsiccatiocclink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omexsiccatiocclink, {as: "omexsiccatiocclinks", foreignKey: "occid"});
    omoccuraccessstats.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccuraccessstats, {as: "omoccuraccessstats", foreignKey: "occid"});
    omoccurassociations.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurassociations, {as: "omoccurassociations", foreignKey: "occid"});
    omoccurassociations.belongsTo(omoccurrences, {as: "occidassociate_omoccurrence", foreignKey: "occidassociate"});
    omoccurrences.hasMany(omoccurassociations, {
        as: "occidassociate_omoccurassociations",
        foreignKey: "occidassociate"
    });
    omoccurcomments.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurcomments, {as: "omoccurcomments", foreignKey: "occid"});
    omoccurdatasetlink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurdatasetlink, {as: "omoccurdatasetlinks", foreignKey: "occid"});
    omoccurdeterminations.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurdeterminations, {as: "omoccurdeterminations", foreignKey: "occid"});
    omoccurduplicatelink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurduplicatelink, {as: "omoccurduplicatelinks", foreignKey: "occid"});
    omoccuredits.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccuredits, {as: "omoccuredits", foreignKey: "occid"});
    omoccurgenetic.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurgenetic, {as: "omoccurgenetics", foreignKey: "occid"});
    omoccuridentifiers.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccuridentifiers, {as: "omoccuridentifiers", foreignKey: "occid"});
    omoccurlithostratigraphy.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurlithostratigraphy, {as: "omoccurlithostratigraphies", foreignKey: "occid"});
    omoccurloanslink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurloanslink, {as: "omoccurloanslinks", foreignKey: "occid"});
    omoccurpaleo.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasOne(omoccurpaleo, {as: "omoccurpaleo", foreignKey: "occid"});
    omoccurrencetypes.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurrencetypes, {as: "omoccurrencetypes", foreignKey: "occid"});
    omoccurrevisions.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurrevisions, {as: "omoccurrevisions", foreignKey: "occid"});
    omoccurverification.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(omoccurverification, {as: "omoccurverifications", foreignKey: "occid"});
    referenceoccurlink.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(referenceoccurlink, {as: "referenceoccurlinks", foreignKey: "occid"});
    specprocessorrawlabels.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(specprocessorrawlabels, {as: "specprocessorrawlabels", foreignKey: "occid"});
    tmattributes.belongsTo(omoccurrences, {as: "occ", foreignKey: "occid"});
    omoccurrences.hasMany(tmattributes, {as: "tmattributes", foreignKey: "occid"});
    omoccurlithostratigraphy.belongsTo(paleochronostratigraphy, {as: "chrono", foreignKey: "chronoId"});
    paleochronostratigraphy.hasMany(omoccurlithostratigraphy, {
        as: "omoccurlithostratigraphies",
        foreignKey: "chronoId"
    });
    referenceauthorlink.belongsTo(referenceauthors, {as: "refauth", foreignKey: "refauthid"});
    referenceauthors.hasMany(referenceauthorlink, {as: "referenceauthorlinks", foreignKey: "refauthid"});
    omoccurrencetypes.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(omoccurrencetypes, {as: "omoccurrencetypes", foreignKey: "refid"});
    referenceauthorlink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referenceauthorlink, {as: "referenceauthorlinks", foreignKey: "refid"});
    referencechecklistlink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referencechecklistlink, {as: "referencechecklistlinks", foreignKey: "refid"});
    referencechklsttaxalink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referencechklsttaxalink, {as: "referencechklsttaxalinks", foreignKey: "refid"});
    referencecollectionlink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referencecollectionlink, {as: "referencecollectionlinks", foreignKey: "refid"});
    referenceobject.belongsTo(referenceobject, {as: "parentRef", foreignKey: "parentRefId"});
    referenceobject.hasMany(referenceobject, {as: "referenceobjects", foreignKey: "parentRefId"});
    referenceoccurlink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referenceoccurlink, {as: "referenceoccurlinks", foreignKey: "refid"});
    referencetaxalink.belongsTo(referenceobject, {as: "ref", foreignKey: "refid"});
    referenceobject.hasMany(referencetaxalink, {as: "referencetaxalinks", foreignKey: "refid"});
    referenceobject.belongsTo(referencetype, {as: "ReferenceType", foreignKey: "ReferenceTypeId"});
    referencetype.hasMany(referenceobject, {as: "referenceobjects", foreignKey: "ReferenceTypeId"});
    specprococrfrag.belongsTo(specprocessorrawlabels, {as: "prl", foreignKey: "prlid"});
    specprocessorrawlabels.hasMany(specprococrfrag, {as: "specprococrfrags", foreignKey: "prlid"});
    fmdyncltaxalink.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(fmdyncltaxalink, {as: "fmdyncltaxalinks", foreignKey: "tid"});
    glossarysources.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasOne(glossarysources, {as: "glossarysource", foreignKey: "tid"});
    glossarytaxalink.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(glossarytaxalink, {as: "glossarytaxalinks", foreignKey: "tid"});
    imageannotations.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(imageannotations, {as: "imageannotations", foreignKey: "tid"});
    images.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(images, {as: "images", foreignKey: "tid"});
    kmchartaxalink.belongsTo(taxa, {as: "TID_taxa", foreignKey: "TID"});
    taxa.hasMany(kmchartaxalink, {as: "kmchartaxalinks", foreignKey: "TID"});
    kmdescr.belongsTo(taxa, {as: "TID_taxa", foreignKey: "TID"});
    taxa.hasMany(kmdescr, {as: "kmdescrs", foreignKey: "TID"});
    media.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(media, {as: "media", foreignKey: "tid"});
    omoccurassociations.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(omoccurassociations, {as: "omoccurassociations", foreignKey: "tid"});
    omoccurdeterminations.belongsTo(taxa, {as: "tidinterpreted_taxa", foreignKey: "tidinterpreted"});
    taxa.hasMany(omoccurdeterminations, {as: "omoccurdeterminations", foreignKey: "tidinterpreted"});
    omoccurgeoindex.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(omoccurgeoindex, {as: "omoccurgeoindices", foreignKey: "tid"});
    omoccurrences.belongsTo(taxa, {as: "tidinterpreted_taxa", foreignKey: "tidinterpreted"});
    taxa.hasMany(omoccurrences, {as: "omoccurrences", foreignKey: "tidinterpreted"});
    omoccurrencetypes.belongsTo(taxa, {as: "tidinterpreted_taxa", foreignKey: "tidinterpreted"});
    taxa.hasMany(omoccurrencetypes, {as: "omoccurrencetypes", foreignKey: "tidinterpreted"});
    referencetaxalink.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(referencetaxalink, {as: "referencetaxalinks", foreignKey: "tid"});
    taxadescrblock.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxadescrblock, {as: "taxadescrblocks", foreignKey: "tid"});
    taxaenumtree.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxaenumtree, {as: "taxaenumtrees", foreignKey: "tid"});
    taxaenumtree.belongsTo(taxa, {as: "parentt", foreignKey: "parenttid"});
    taxa.hasMany(taxaenumtree, {as: "parentt_taxaenumtrees", foreignKey: "parenttid"});
    taxalinks.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxalinks, {as: "taxalinks", foreignKey: "tid"});
    taxamaps.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxamaps, {as: "taxamaps", foreignKey: "tid"});
    taxaresourcelinks.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxaresourcelinks, {as: "taxaresourcelinks", foreignKey: "tid"});
    taxavernaculars.belongsTo(taxa, {as: "TID_taxa", foreignKey: "TID"});
    taxa.hasMany(taxavernaculars, {as: "taxavernaculars", foreignKey: "TID"});
    taxstatus.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(taxstatus, {as: "taxstatuses", foreignKey: "tid"});
    taxstatus.belongsTo(taxa, {as: "tidaccepted_taxa", foreignKey: "tidaccepted"});
    taxa.hasMany(taxstatus, {as: "tidaccepted_taxstatuses", foreignKey: "tidaccepted"});
    taxstatus.belongsTo(taxa, {as: "parentt", foreignKey: "parenttid"});
    taxa.hasMany(taxstatus, {as: "parentt_taxstatuses", foreignKey: "parenttid"});
    tmtraittaxalink.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(tmtraittaxalink, {as: "tmtraittaxalinks", foreignKey: "tid"});
    usertaxonomy.belongsTo(taxa, {as: "tid_taxa", foreignKey: "tid"});
    taxa.hasMany(usertaxonomy, {as: "usertaxonomies", foreignKey: "tid"});
    taxadescrstmts.belongsTo(taxadescrblock, {as: "tdb", foreignKey: "tdbid"});
    taxadescrblock.hasMany(taxadescrstmts, {as: "taxadescrstmts", foreignKey: "tdbid"});
    taxaprofilepubdesclink.belongsTo(taxadescrblock, {as: "tdb", foreignKey: "tdbid"});
    taxadescrblock.hasMany(taxaprofilepubdesclink, {as: "taxaprofilepubdesclinks", foreignKey: "tdbid"});
    taxaprofilepubmaplink.belongsTo(taxamaps, {as: "mid_taxamap", foreignKey: "mid"});
    taxamaps.hasMany(taxaprofilepubmaplink, {as: "taxaprofilepubmaplinks", foreignKey: "mid"});
    taxaprofilepubdesclink.belongsTo(taxaprofilepubs, {as: "tpp", foreignKey: "tppid"});
    taxaprofilepubs.hasMany(taxaprofilepubdesclink, {as: "taxaprofilepubdesclinks", foreignKey: "tppid"});
    taxaprofilepubimagelink.belongsTo(taxaprofilepubs, {as: "tpp", foreignKey: "tppid"});
    taxaprofilepubs.hasMany(taxaprofilepubimagelink, {as: "taxaprofilepubimagelinks", foreignKey: "tppid"});
    taxaprofilepubmaplink.belongsTo(taxaprofilepubs, {as: "tpp", foreignKey: "tppid"});
    taxaprofilepubs.hasMany(taxaprofilepubmaplink, {as: "taxaprofilepubmaplinks", foreignKey: "tppid"});
    usertaxonomy.belongsTo(taxauthority, {as: "taxauth", foreignKey: "taxauthid"});
    taxauthority.hasMany(usertaxonomy, {as: "usertaxonomies", foreignKey: "taxauthid"});
    taxa.belongsTo(taxonkingdoms, {as: "kingdom", foreignKey: "kingdomId"});
    taxonkingdoms.hasMany(taxa, {as: "taxas", foreignKey: "kingdomId"});
    taxonunits.belongsTo(taxonkingdoms, {as: "kingdom", foreignKey: "kingdomid"});
    taxonkingdoms.hasMany(taxonunits, {as: "taxonunits", foreignKey: "kingdomid"});
    tmattributes.belongsTo(tmstates, {as: "state", foreignKey: "stateid"});
    tmstates.hasMany(tmattributes, {as: "tmattributes", foreignKey: "stateid"});
    tmtraitdependencies.belongsTo(tmstates, {as: "parentstate", foreignKey: "parentstateid"});
    tmstates.hasMany(tmtraitdependencies, {as: "tmtraitdependencies", foreignKey: "parentstateid"});
    tmstates.belongsTo(tmtraits, {as: "trait", foreignKey: "traitid"});
    tmtraits.hasMany(tmstates, {as: "tmstates", foreignKey: "traitid"});
    tmtraitdependencies.belongsTo(tmtraits, {as: "trait", foreignKey: "traitid"});
    tmtraits.hasMany(tmtraitdependencies, {as: "tmtraitdependencies", foreignKey: "traitid"});
    tmtraittaxalink.belongsTo(tmtraits, {as: "trait", foreignKey: "traitid"});
    tmtraits.hasMany(tmtraittaxalink, {as: "tmtraittaxalinks", foreignKey: "traitid"});
    uploadspecmap.belongsTo(uploadspecparameters, {as: "usp", foreignKey: "uspid"});
    uploadspecparameters.hasMany(uploadspecmap, {as: "uploadspecmaps", foreignKey: "uspid"});
    fmchecklists.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(fmchecklists, {as: "fmchecklists", foreignKey: "uid"});
    fmcltaxacomments.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(fmcltaxacomments, {as: "fmcltaxacomments", foreignKey: "uid"});
    glossary.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(glossary, {as: "glossaries", foreignKey: "uid"});
    glossaryimages.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(glossaryimages, {as: "glossaryimages", foreignKey: "uid"});
    imagekeywords.belongsTo(users, {as: "uidassignedby_user", foreignKey: "uidassignedby"});
    users.hasMany(imagekeywords, {as: "imagekeywords", foreignKey: "uidassignedby"});
    images.belongsTo(users, {as: "photographeru", foreignKey: "photographeruid"});
    users.hasMany(images, {as: "images", foreignKey: "photographeruid"});
    institutions.belongsTo(users, {as: "modifiedu", foreignKey: "modifieduid"});
    users.hasMany(institutions, {as: "institutions", foreignKey: "modifieduid"});
    media.belongsTo(users, {as: "creatoru", foreignKey: "creatoruid"});
    users.hasMany(media, {as: "media", foreignKey: "creatoruid"});
    omcrowdsourcequeue.belongsTo(users, {as: "uidprocessor_user", foreignKey: "uidprocessor"});
    users.hasMany(omcrowdsourcequeue, {as: "omcrowdsourcequeues", foreignKey: "uidprocessor"});
    omoccurassociations.belongsTo(users, {as: "createdu", foreignKey: "createduid"});
    users.hasMany(omoccurassociations, {as: "omoccurassociations", foreignKey: "createduid"});
    omoccurassociations.belongsTo(users, {as: "modifiedu", foreignKey: "modifieduid"});
    users.hasMany(omoccurassociations, {as: "modifiedu_omoccurassociations", foreignKey: "modifieduid"});
    omoccurcomments.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(omoccurcomments, {as: "omoccurcomments", foreignKey: "uid"});
    omoccurdatasets.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(omoccurdatasets, {as: "omoccurdatasets", foreignKey: "uid"});
    omoccuredits.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(omoccuredits, {as: "omoccuredits", foreignKey: "uid"});
    omoccurrences.belongsTo(users, {as: "observeru", foreignKey: "observeruid"});
    users.hasMany(omoccurrences, {as: "omoccurrences", foreignKey: "observeruid"});
    omoccurrevisions.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(omoccurrevisions, {as: "omoccurrevisions", foreignKey: "uid"});
    omoccurverification.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(omoccurverification, {as: "omoccurverifications", foreignKey: "uid"});
    taxa.belongsTo(users, {as: "modifiedU", foreignKey: "modifiedUid"});
    users.hasMany(taxa, {as: "taxas", foreignKey: "modifiedUid"});
    taxaprofilepubs.belongsTo(users, {as: "uidowner_user", foreignKey: "uidowner"});
    users.hasMany(taxaprofilepubs, {as: "taxaprofilepubs", foreignKey: "uidowner"});
    tmattributes.belongsTo(users, {as: "modifiedu", foreignKey: "modifieduid"});
    users.hasMany(tmattributes, {as: "tmattributes", foreignKey: "modifieduid"});
    tmattributes.belongsTo(users, {as: "createdu", foreignKey: "createduid"});
    users.hasMany(tmattributes, {as: "createdu_tmattributes", foreignKey: "createduid"});
    tmstates.belongsTo(users, {as: "modifiedu", foreignKey: "modifieduid"});
    users.hasMany(tmstates, {as: "tmstates", foreignKey: "modifieduid"});
    tmstates.belongsTo(users, {as: "createdu", foreignKey: "createduid"});
    users.hasMany(tmstates, {as: "createdu_tmstates", foreignKey: "createduid"});
    tmtraits.belongsTo(users, {as: "modifiedu", foreignKey: "modifieduid"});
    users.hasMany(tmtraits, {as: "tmtraits", foreignKey: "modifieduid"});
    tmtraits.belongsTo(users, {as: "createdu", foreignKey: "createduid"});
    users.hasMany(tmtraits, {as: "createdu_tmtraits", foreignKey: "createduid"});
    usertaxonomy.belongsTo(users, {as: "uid_user", foreignKey: "uid"});
    users.hasMany(usertaxonomy, {as: "usertaxonomies", foreignKey: "uid"});

    return {
        fmchecklists,
        fmchklstchildren,
        fmchklstcoordinates,
        fmchklstprojlink,
        fmchklsttaxalink,
        fmchklsttaxastatus,
        fmcltaxacomments,
        fmdynamicchecklists,
        fmdyncltaxalink,
        fmprojectcategories,
        fmprojects,
        fmvouchers,
        glossary,
        glossaryimages,
        glossarysources,
        glossarytaxalink,
        glossarytermlink,
        guidimages,
        guidoccurdeterminations,
        guidoccurrences,
        igsnverification,
        imageannotations,
        imagekeywords,
        images,
        imagetag,
        imagetagkey,
        institutions,
        kmcharacterlang,
        kmcharacters,
        kmchardependence,
        kmcharheading,
        kmchartaxalink,
        kmcs,
        kmcsimages,
        kmcslang,
        kmdescr,
        media,
        omcollcategories,
        omcollcatlink,
        omcollections,
        omcollectionstats,
        omcollectors,
        omcollpublications,
        omcollpuboccurlink,
        omcollsecondary,
        omcrowdsourcecentral,
        omcrowdsourcequeue,
        omexsiccatinumbers,
        omexsiccatiocclink,
        omexsiccatititles,
        omoccuraccessstats,
        omoccurassociations,
        omoccurcomments,
        omoccurdatasetlink,
        omoccurdatasets,
        omoccurdeterminations,
        omoccurduplicatelink,
        omoccurduplicates,
        omoccureditlocks,
        omoccuredits,
        omoccurexchange,
        omoccurgenetic,
        omoccurgeoindex,
        omoccuridentifiers,
        omoccurlithostratigraphy,
        omoccurloans,
        omoccurloanslink,
        omoccurpaleo,
        omoccurpaleogts,
        omoccurpoints,
        omoccurrences,
        omoccurrencesfulltext,
        omoccurrencetypes,
        omoccurrevisions,
        omoccurverification,
        referenceauthorlink,
        referenceauthors,
        referencechecklistlink,
        referencechklsttaxalink,
        referencecollectionlink,
        referenceobject,
        referenceoccurlink,
        referencetaxalink,
        referencetype,
        specprocessorprojects,
        specprocessorrawlabels,
        specprococrfrag,
        taxa,
        taxadescrblock,
        taxadescrstmts,
        taxaenumtree,
        taxalinks,
        taxamaps,
        taxaprofilepubdesclink,
        taxaprofilepubimagelink,
        taxaprofilepubmaplink,
        taxaprofilepubs,
        taxaresourcelinks,
        taxauthority,
        taxavernaculars,
        taxonkingdoms,
        taxonunits,
        taxstatus,
        tmattributes,
        tmstates,
        tmtraitdependencies,
        tmtraits,
        tmtraittaxalink,
        uploaddetermtemp,
        uploadglossary,
        uploadimagetemp,
        uploadspecmap,
        uploadspecparameters,
        uploadspectemp,
        uploadspectemppoints,
        uploadtaxa,
        usertaxonomy,
    };
}
return initModels;
//module.exports = initModels;
//module.exports.initModels = initModels;
//module.exports.default = initModels;
