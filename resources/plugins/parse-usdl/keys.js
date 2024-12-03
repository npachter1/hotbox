// Source: http://www.aamva.org/DL-ID-Card-Design-Standard/
// changes/additions are noted in comments
exports.CodeToKey = {
    DAA: 'last-first-mi', // add aamva v1
    DCT: 'first-middle', // add aamva v2
    DAL: 'addressStreet', // v1 format
    DAN: 'addressCity', // v1 format
    DAO: 'addressState', // v1 format
    DAP: 'addressPostalCode', // v1 format
    DAB: 'lastName', // v1 format
    DCA: 'jurisdictionVehicleClass',
    DCB: 'jurisdictionRestrictionCodes',
    DCD: 'jurisdictionEndorsementCodes',
    DBA: 'dateOfExpiry',
    DCS: 'lastName',
    DAC: 'firstName',
    DAD: 'middleName',
    DBD: 'dateOfIssue',
    DBB: 'dateOfBirth',
    DBC: 'sex',
    DAY: 'eyeColor',
    DAU: 'height',
    DAG: 'addressStreet',
    DAI: 'addressCity',
    DAJ: 'addressState',
    DAK: 'addressPostalCode',
    DAQ: 'documentNumber',
    DCF: 'documentDiscriminator',
    DCG: 'country',
    DDE: 'lastNameTruncated',
    DDF: 'firstNameTruncated',
    DDG: 'middleNameTruncated',
    // optional
    DAZ: 'hairColor',
    DAH: 'addressStreet2',
    DCI: 'placeOfBirth',
    DCJ: 'auditInformation',
    DCK: 'inventoryControlNumber',
    DBN: 'otherLastName',
    DBG: 'otherFirstName',
    DBS: 'otherSuffixName',
    DCU: 'nameSuffix', // e.g. jr, sr
    DCE: 'weightRange',
    DCL: 'race',
    DCM: 'standardVehicleClassification',
    DCN: 'standardEndorsementCode',
    DCO: 'standardRestrictionCode',
    DCP: 'jurisdictionVehicleClassificationDescription',
    DCQ: 'jurisdictionEndorsementCodeDescription',
    DCR: 'jurisdictionRestrictionCodeDescription',
    DDA: 'complianceType',
    DDB: 'dateCardRevised',
    DDC: 'dateOfExpiryHazmatEndorsement',
    DDD: 'limitedDurationDocumentIndicator',
    DAW: 'weightLb',
    DAX: 'weightKg',
    DDH: 'dateAge18',
    DDI: 'dateAge19',
    DDJ: 'dateAge21',
    DDK: 'organDonor',
    DDL: 'veteran'
}