/*
code originally pulled from https://github.com/mvayngrib/parse-usdl
several modifications have been made as we test more ids
 */
const CodeToKey = require("./keys").CodeToKey;

const lineSeparator = "\n";

const defaultOptions = {suppressErrors: false};

exports.parse = function parseCode128(str, options = defaultOptions) {
    const props = {};
    const rawLines = str.toUpperCase().trim().split(lineSeparator);
    const lines = rawLines.map(rawLine => sanitizeData(rawLine));
    let started;
    lines.forEach(line => {
        if (!started) {
            started = ((line.indexOf("ANSI ") === 0) || (line.indexOf("AAMVA") ===0));
            //check for DAQ (doc number) or DAB (last name) in this line for old/non-standard versions
            if (started) {
                const regex = /(DAQ|DAB)([\w\-]*)[\s]/
                let found = `${line}\n`.match(regex);
                if (found && Array.isArray(found) && found.length>0) {
                    if (found[1]==='DAQ') props[getKey('DAQ')]=found[2];
                    if (found[1]==='DAB') props[getKey('DAB')]=found[2];
                }
                return;
            }
        }

        let code = getCode(line);
        let value = getValue(line);
        let key = getKey(code);
        if (!key) {
            if (options.suppressErrors) {
                return;
            } else {
                throw new Error("unknown code: " + code);
            }
        }

        if (isSexField(code)) value = getSex(code, value);

        props[key] = isDateField(key) ? getDateFormat(value) : value;

        if ((code==='DAA') && (value!=='')) { //old versions DAA line which is format "last,first,mi"
            let a = value.split(',');
            if (a.length>2) {
                if (!props[getKey('DCS')]) props[getKey('DCS')]=a[0]; //last
                if (!props[getKey('DAC')]) props[getKey('DAC')]=a[1]; //first
                if (!props[getKey('DAD')]) props[getKey('DAD')]=a[2]; //middle
            }
        }
        if ((code==='DCT') && (value!=='')) { //aamva v2 DCT can be 'first' or 'first,middle' or 'first middle' or some variation. we'll only worry about comma
            let a = value.split(',');
            if (a.length===2) {
                if (!props[getKey('DAC')]) props[getKey('DAC')]=a[0]; //first
                if (!props[getKey('DAD')]) props[getKey('DAD')]=a[1]; //middle
            } else {
                if (!props[getKey('DAC')]) props[getKey('DAC')]=value; //first
            }
        }
    });

    return props;
};

const sanitizeData = rawLine => rawLine.match(/[\011\012\015\040-\177\203\212\214\216\234\236\237\300-\377]*/g).join('').trim();

const getCode = line => line.slice(0, 3);
const getValue = line => line.slice(3);
const getKey = code => CodeToKey[code];

const isSexField = code => code === "DBC";

const getSex = (code, value) => ((value.toLowerCase()==="m" || value === "1") ? "male" : (value === "1") ? "other" : "female");

const isDateField = key => key.indexOf("date") === 0;

const getDateFormat = value => {
    //old format is YYYYMMDD while newer is MMDDCCYY
    //nc format is MM-DD-YYYY
    //aamva format current is MMDDYYYY
    const formats = ['MMDDYYYY','YYYYMMDD','MM-DD-YYYY'];
    let dt = new moment(value,formats);
    // return dt.isValid() ? dt.format('YYYY-MM-DD') : null;
    return dt.isValid() ? dt : null;
};