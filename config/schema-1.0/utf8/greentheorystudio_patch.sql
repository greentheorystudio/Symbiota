ALTER TABLE `taxonunits`
    MODIFY COLUMN `kingdomName` varchar(45) NULL DEFAULT 'Organism' AFTER `taxonunitid`,
    DROP INDEX `UNIQUE_taxonunits`;
