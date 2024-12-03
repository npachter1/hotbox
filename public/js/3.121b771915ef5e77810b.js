(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[3],{

/***/ "./node_modules/html-to-image/lib/applyStyleWithOptions.js":
/*!*****************************************************************!*\
  !*** ./node_modules/html-to-image/lib/applyStyleWithOptions.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
function applyStyleWithOptions(clonedNode, options) {
    var style = clonedNode.style;
    if (options.backgroundColor) {
        style.backgroundColor = options.backgroundColor;
    }
    if (options.width) {
        style.width = options.width + "px";
    }
    if (options.height) {
        style.height = options.height + "px";
    }
    if (options.style) {
        Object.assign(style, options.style);
    }
    return clonedNode;
}
exports.default = applyStyleWithOptions;


/***/ }),

/***/ "./node_modules/html-to-image/lib/cloneNode.js":
/*!*****************************************************!*\
  !*** ./node_modules/html-to-image/lib/cloneNode.js ***!
  \*****************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
var clonePseudoElements_1 = __importDefault(__webpack_require__(/*! ./clonePseudoElements */ "./node_modules/html-to-image/lib/clonePseudoElements.js"));
function cloneSingleNode(nativeNode) {
    if (nativeNode instanceof HTMLCanvasElement) {
        return utils_1.createImage(nativeNode.toDataURL());
    }
    if (nativeNode.tagName && nativeNode.tagName.toLowerCase() === 'svg') {
        return Promise.resolve(nativeNode)
            .then(function (svg) { return utils_1.svgToDataURL(svg); })
            .then(utils_1.createImage);
    }
    return Promise.resolve(nativeNode.cloneNode(false));
}
function cloneChildren(nativeNode, clonedNode, filter) {
    var children = utils_1.toArray(nativeNode.childNodes);
    if (children.length === 0) {
        return Promise.resolve(clonedNode);
    }
    // clone children in order
    return children.reduce(function (done, child) { return done
        .then(function () { return cloneNode(child, filter); })
        .then(function (clonedChild) {
        if (clonedChild) {
            clonedNode.appendChild(clonedChild);
        }
    }); }, Promise.resolve())
        .then(function () { return clonedNode; });
}
function cloneCssStyle(nativeNode, clonedNode) {
    var source = window.getComputedStyle(nativeNode);
    var target = clonedNode.style;
    if (source.cssText) {
        target.cssText = source.cssText;
    }
    else {
        utils_1.toArray(source).forEach(function (name) {
            target.setProperty(name, source.getPropertyValue(name), source.getPropertyPriority(name));
        });
    }
}
function cloneInputValue(nativeNode, clonedNode) {
    if (nativeNode instanceof HTMLTextAreaElement) {
        clonedNode.innerHTML = nativeNode.value;
    }
    if (nativeNode instanceof HTMLInputElement) {
        clonedNode.setAttribute('value', nativeNode.value);
    }
}
function decorate(nativeNode, clonedNode) {
    if (!(clonedNode instanceof Element)) {
        return clonedNode;
    }
    return Promise.resolve()
        .then(function () { return cloneCssStyle(nativeNode, clonedNode); })
        .then(function () { return clonePseudoElements_1.default(nativeNode, clonedNode); })
        .then(function () { return cloneInputValue(nativeNode, clonedNode); })
        .then(function () { return clonedNode; });
}
function cloneNode(domNode, filter, isRoot) {
    if (!isRoot && filter && !filter(domNode)) {
        return Promise.resolve(null);
    }
    return Promise.resolve(domNode)
        .then(cloneSingleNode)
        .then(function (clonedNode) { return cloneChildren(domNode, clonedNode, filter); })
        .then(function (clonedNode) { return decorate(domNode, clonedNode); });
}
exports.default = cloneNode;


/***/ }),

/***/ "./node_modules/html-to-image/lib/clonePseudoElements.js":
/*!***************************************************************!*\
  !*** ./node_modules/html-to-image/lib/clonePseudoElements.js ***!
  \***************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
function formatCssText(style) {
    var content = style.getPropertyValue('content');
    return style.cssText + " content: " + content + ";";
}
function formatCssProperties(style) {
    return utils_1.toArray(style).map(function (name) {
        var value = style.getPropertyValue(name);
        var priority = style.getPropertyPriority(name);
        return name + ": " + value + (priority ? ' !important' : '') + ";";
    }).join(' ');
}
function getPseudoElementStyle(className, pseudo, style) {
    var selector = "." + className + ":" + pseudo;
    var cssText = style.cssText ? formatCssText(style) : formatCssProperties(style);
    return document.createTextNode(selector + "{" + cssText + "}");
}
function clonePseudoElement(nativeNode, clonedNode, pseudo) {
    var style = window.getComputedStyle(nativeNode, pseudo);
    var content = style.getPropertyValue('content');
    if (content === '' || content === 'none') {
        return;
    }
    var className = utils_1.uuid();
    var styleElement = document.createElement('style');
    styleElement.appendChild(getPseudoElementStyle(className, pseudo, style));
    clonedNode.className = clonedNode.className + " " + className;
    clonedNode.appendChild(styleElement);
}
function clonePseudoElements(nativeNode, clonedNode) {
    [
        ':before',
        ':after',
    ].forEach(function (pseudo) { return clonePseudoElement(nativeNode, clonedNode, pseudo); });
}
exports.default = clonePseudoElements;


/***/ }),

/***/ "./node_modules/html-to-image/lib/createSvgDataURL.js":
/*!************************************************************!*\
  !*** ./node_modules/html-to-image/lib/createSvgDataURL.js ***!
  \************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
function createSvgDataURL(clonedNode, width, height) {
    var xmlns = 'http://www.w3.org/2000/svg';
    var svg = document.createElementNS(xmlns, 'svg');
    var foreignObject = document.createElementNS(xmlns, 'foreignObject');
    svg.setAttributeNS('', 'width', "" + width);
    svg.setAttributeNS('', 'height', "" + height);
    foreignObject.setAttributeNS('', 'width', '100%');
    foreignObject.setAttributeNS('', 'height', '100%');
    foreignObject.setAttributeNS('', 'x', '0');
    foreignObject.setAttributeNS('', 'y', '0');
    foreignObject.setAttributeNS('', 'externalResourcesRequired', 'true');
    svg.appendChild(foreignObject);
    foreignObject.appendChild(clonedNode);
    return utils_1.svgToDataURL(svg);
}
exports.default = createSvgDataURL;


/***/ }),

/***/ "./node_modules/html-to-image/lib/embedImages.js":
/*!*******************************************************!*\
  !*** ./node_modules/html-to-image/lib/embedImages.js ***!
  \*******************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
var getBlobFromURL_1 = __importDefault(__webpack_require__(/*! ./getBlobFromURL */ "./node_modules/html-to-image/lib/getBlobFromURL.js"));
var embedResources_1 = __importDefault(__webpack_require__(/*! ./embedResources */ "./node_modules/html-to-image/lib/embedResources.js"));
function embedBackground(clonedNode, options) {
    var background = clonedNode.style.getPropertyValue('background');
    if (!background) {
        return Promise.resolve(clonedNode);
    }
    return Promise.resolve(background)
        .then(function (cssString) { return embedResources_1.default(cssString, null, options); })
        .then(function (cssString) {
        clonedNode.style.setProperty('background', cssString, clonedNode.style.getPropertyPriority('background'));
        return clonedNode;
    });
}
function embedImageNode(clonedNode, options) {
    if (!(clonedNode instanceof HTMLImageElement) || utils_1.isDataUrl(clonedNode.src)) {
        return Promise.resolve(clonedNode);
    }
    return Promise.resolve(clonedNode.src)
        .then(function (url) { return getBlobFromURL_1.default(url, options); })
        .then(function (data) { return utils_1.toDataURL(data, utils_1.getMimeType(clonedNode.src)); })
        .then(function (dataURL) { return new Promise((function (resolve, reject) {
        clonedNode.onload = resolve;
        clonedNode.onerror = reject;
        clonedNode.src = dataURL;
    })); })
        .then(function () { return clonedNode; }, function () { return clonedNode; });
}
function embedChildren(clonedNode, options) {
    var children = utils_1.toArray(clonedNode.childNodes);
    var deferreds = children.map(function (child) { return embedImages(child, options); });
    return Promise.all(deferreds).then(function () { return clonedNode; });
}
function embedImages(clonedNode, options) {
    if (!(clonedNode instanceof Element)) {
        return Promise.resolve(clonedNode);
    }
    return Promise.resolve(clonedNode)
        .then(function (node) { return embedBackground(node, options); })
        .then(function (node) { return embedImageNode(node, options); })
        .then(function (node) { return embedChildren(node, options); });
}
exports.default = embedImages;


/***/ }),

/***/ "./node_modules/html-to-image/lib/embedResources.js":
/*!**********************************************************!*\
  !*** ./node_modules/html-to-image/lib/embedResources.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var getBlobFromURL_1 = __importDefault(__webpack_require__(/*! ./getBlobFromURL */ "./node_modules/html-to-image/lib/getBlobFromURL.js"));
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
var URL_REGEX = /url\((['"]?)([^'"]+?)\1\)/g;
function resolveUrl(url, baseUrl) {
    // url is absolute already
    if (url.match(/^[a-z]+:\/\//i)) {
        return url;
    }
    // url is absolute already, without protocol
    if (url.match(/^\/\//)) {
        return window.location.protocol + url;
    }
    // dataURI, mailto:, tel:, etc.
    if (url.match(/^[a-z]+:/i)) {
        return url;
    }
    var doc = document.implementation.createHTMLDocument();
    var base = doc.createElement('base');
    var a = doc.createElement('a');
    doc.head.appendChild(base);
    doc.body.appendChild(a);
    if (baseUrl) {
        base.href = baseUrl;
    }
    a.href = url;
    return a.href;
}
function escape(url) {
    return url.replace(/([.*+?^${}()|\[\]\/\\])/g, '\\$1');
}
function urlToRegex(url) {
    return new RegExp("(url\\(['\"]?)(" + escape(url) + ")(['\"]?\\))", 'g');
}
function parseURLs(str) {
    var result = [];
    str.replace(URL_REGEX, function (raw, quotation, url) {
        result.push(url);
        return raw;
    });
    return result.filter(function (url) { return !utils_1.isDataUrl(url); });
}
function embed(cssString, resourceURL, baseURL, options) {
    var resolvedURL = baseURL ? resolveUrl(resourceURL, baseURL) : resourceURL;
    return Promise.resolve(resolvedURL)
        .then(function (url) { return getBlobFromURL_1.default(url, options); })
        .then(function (data) { return utils_1.toDataURL(data, utils_1.getMimeType(resourceURL)); })
        .then(function (dataURL) { return cssString.replace(urlToRegex(resourceURL), "$1" + dataURL + "$3"); })
        .then(function (content) { return content; }, function () { return resolvedURL; });
}
function shouldEmbed(string) {
    return string.search(URL_REGEX) !== -1;
}
exports.shouldEmbed = shouldEmbed;
function embedResources(cssString, baseUrl, options) {
    if (!shouldEmbed(cssString)) {
        return Promise.resolve(cssString);
    }
    return Promise.resolve(cssString)
        .then(parseURLs)
        .then(function (urls) { return urls.reduce(function (done, url) { return done.then(function (ret) { return embed(ret, url, baseUrl, options); }); }, Promise.resolve(cssString)); });
}
exports.default = embedResources;


/***/ }),

/***/ "./node_modules/html-to-image/lib/embedWebFonts.js":
/*!*********************************************************!*\
  !*** ./node_modules/html-to-image/lib/embedWebFonts.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importStar = (this && this.__importStar) || function (mod) {
    if (mod && mod.__esModule) return mod;
    var result = {};
    if (mod != null) for (var k in mod) if (Object.hasOwnProperty.call(mod, k)) result[k] = mod[k];
    result["default"] = mod;
    return result;
};
Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
var embedResources_1 = __importStar(__webpack_require__(/*! ./embedResources */ "./node_modules/html-to-image/lib/embedResources.js"));
function parseCSS(source) {
    if (source === undefined) {
        return [];
    }
    var cssText = source;
    var css = [];
    var cssKeyframeRegex = '((@.*?keyframes [\\s\\S]*?){([\\s\\S]*?}\\s*?)})';
    var combinedCSSRegex = '((\\s*?(?:\\/\\*[\\s\\S]*?\\*\\/)?\\s*?@media[\\s\\S]'
        + '*?){([\\s\\S]*?)}\\s*?})|(([\\s\\S]*?){([\\s\\S]*?)})'; // to match css & media queries together
    var cssCommentsRegex = new RegExp('(\\/\\*[\\s\\S]*?\\*\\/)', 'gi');
    // strip out comments
    cssText = cssText.replace(cssCommentsRegex, '');
    var keyframesRegex = new RegExp(cssKeyframeRegex, 'gi');
    var arr;
    while (true) {
        arr = keyframesRegex.exec(cssText);
        if (arr === null) {
            break;
        }
        css.push(arr[0]);
    }
    cssText = cssText.replace(keyframesRegex, '');
    // unified regex
    var unified = new RegExp(combinedCSSRegex, 'gi');
    while (true) {
        arr = unified.exec(cssText);
        if (arr === null) {
            break;
        }
        css.push(arr[0]);
    }
    return css;
}
function fetchCSS(url, sheet) {
    return fetch(url).then(function (res) {
        return {
            url: url,
            cssText: res.text(),
        };
    }, function (e) {
        console.log('ERROR FETCHING CSS: ', e.toString());
    });
}
function embedFonts(data) {
    return data.cssText.then(function (resolved) {
        var cssText = resolved;
        var fontLocations = cssText.match(/url\([^)]+\)/g) || [];
        var fontLoadedPromises = fontLocations.map(function (location) {
            var url = location.replace(/url\(([^]+)\)/g, '$1');
            if (!url.startsWith('https://')) {
                var source = data.url;
                url = new URL(url, source).href;
            }
            return new Promise(function (resolve, reject) {
                fetch(url)
                    .then(function (res) { return res.blob(); })
                    .then(function (blob) {
                    var reader = new FileReader();
                    reader.addEventListener('load', function (res) {
                        // Side Effect
                        cssText = cssText.replace(location, "url(" + reader.result + ")");
                        resolve([location, reader.result]);
                    });
                    reader.readAsDataURL(blob);
                })
                    .catch(reject);
            });
        });
        return Promise.all(fontLoadedPromises).then(function () { return cssText; });
    });
}
function getCssRules(styleSheets) {
    var ret = [];
    var promises = [];
    // First loop inlines imports
    styleSheets.forEach(function (sheet) {
        if ('cssRules' in sheet) {
            try {
                utils_1.toArray(sheet.cssRules).forEach(function (item) {
                    if (item.type === CSSRule.IMPORT_RULE) {
                        promises.push(fetchCSS(item.href, sheet)
                            .then(embedFonts)
                            .then(function (cssText) {
                            var parsed = parseCSS(cssText);
                            parsed.forEach(function (rule) {
                                sheet.insertRule(rule, sheet.cssRules.length);
                            });
                        })
                            .catch(function (e) {
                            console.log('Error loading remote css', e.toString());
                        }));
                    }
                });
            }
            catch (e) {
                var inline_1 = styleSheets.find(function (a) { return a.href === null; }) || document.styleSheets[0];
                if (sheet.href != null) {
                    promises.push(fetchCSS(sheet.href, inline_1)
                        .then(embedFonts)
                        .then(function (cssText) {
                        var parsed = parseCSS(cssText);
                        parsed.forEach(function (rule) {
                            inline_1.insertRule(rule, sheet.cssRules.length);
                        });
                    })
                        .catch(function (e) {
                        console.log('Error loading remote stylesheet', e.toString());
                    }));
                }
                console.log('Error inlining remote css file', e.toString());
            }
        }
    });
    return Promise
        .all(promises)
        .then(function () {
        // Second loop parses rules
        styleSheets.forEach(function (sheet) {
            if ('cssRules' in sheet) {
                try {
                    utils_1.toArray(sheet.cssRules).forEach(function (item) {
                        ret.push(item);
                    });
                }
                catch (e) {
                    console.log("Error while reading CSS rules from " + sheet.href, e.toString());
                }
            }
        });
        return ret;
    });
}
function getWebFontRules(cssRules) {
    return cssRules
        .filter(function (rule) { return rule.type === CSSRule.FONT_FACE_RULE; })
        .filter(function (rule) { return embedResources_1.shouldEmbed(rule.style.getPropertyValue('src')); });
}
function parseWebFontRules(clonedNode) {
    return new Promise(function (resolve, reject) {
        if (!clonedNode.ownerDocument) {
            reject(new Error('Provided element is not within a Document'));
        }
        resolve(utils_1.toArray(clonedNode.ownerDocument.styleSheets));
    })
        .then(getCssRules)
        .then(getWebFontRules);
}
exports.parseWebFontRules = parseWebFontRules;
function embedWebFonts(clonedNode, options) {
    return parseWebFontRules(clonedNode)
        .then(function (rules) { return Promise.all(rules.map(function (rule) {
        var baseUrl = rule.parentStyleSheet ? rule.parentStyleSheet.href : null;
        return embedResources_1.default(rule.cssText, baseUrl, options);
    })); })
        .then(function (cssStrings) { return cssStrings.join('\n'); })
        .then(function (cssString) {
        var styleNode = document.createElement('style');
        var sytleContent = document.createTextNode(cssString);
        styleNode.appendChild(sytleContent);
        if (clonedNode.firstChild) {
            clonedNode.insertBefore(styleNode, clonedNode.firstChild);
        }
        else {
            clonedNode.appendChild(styleNode);
        }
        return clonedNode;
    });
}
exports.default = embedWebFonts;


/***/ }),

/***/ "./node_modules/html-to-image/lib/getBlobFromURL.js":
/*!**********************************************************!*\
  !*** ./node_modules/html-to-image/lib/getBlobFromURL.js ***!
  \**********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

/* tslint:disable:max-line-length */
Object.defineProperty(exports, "__esModule", { value: true });
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
// KNOWN ISSUE
// -----------
// Can not handle redirect-url, such as when access 'http://something.com/avatar.png'
// will redirect to 'http://something.com/65fc2ffcc8aea7ba65a1d1feda173540'
var TIMEOUT = 30000;
function getBlobFromURL(url, options) {
    // cache bypass so we dont have CORS issues with cached images
    // ref: https://developer.mozilla.org/en/docs/Web/API/XMLHttpRequest/Using_XMLHttpRequest#Bypassing_the_cache
    if (options.cacheBust) {
        url += ((/\?/).test(url) ? '&' : '?') + (new Date()).getTime(); // tslint:disable-line
    }
    var failed = function (reason) {
        var placeholder = '';
        if (options.imagePlaceholder) {
            var split = options.imagePlaceholder.split(/,/);
            if (split && split[1]) {
                placeholder = split[1];
            }
        }
        var msg = "Failed to fetch resource: " + url;
        if (reason) {
            msg = typeof reason === 'string' ? reason : reason.message;
        }
        if (msg) {
            console.error(msg);
        }
        return placeholder;
    };
    var deferred = window.fetch
        // fetch
        ? window.fetch(url)
            .then(function (response) { return response.blob(); })
            .then(function (blob) { return new Promise(function (resolve, reject) {
            var reader = new FileReader();
            reader.onloadend = function () { return resolve(reader.result); };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        }); })
            .then(utils_1.getDataURLContent)
            .catch(function () { return new Promise(function (resolve, reject) {
            reject();
        }); })
        // xhr
        : new Promise((function (resolve, reject) {
            var req = new XMLHttpRequest();
            var timeout = function () {
                reject(new Error("Timeout of " + TIMEOUT + "ms occured while fetching resource: " + url));
            };
            var done = function () {
                if (req.readyState !== 4) {
                    return;
                }
                if (req.status !== 200) {
                    reject(new Error("Failed to fetch resource: " + url + ", status: " + req.status));
                    return;
                }
                var encoder = new FileReader();
                encoder.onloadend = function () {
                    resolve(utils_1.getDataURLContent(encoder.result));
                };
                encoder.readAsDataURL(req.response);
            };
            req.onreadystatechange = done;
            req.ontimeout = timeout;
            req.responseType = 'blob';
            req.timeout = TIMEOUT;
            req.open('GET', url, true);
            req.send();
        }));
    return deferred.catch(failed);
}
exports.default = getBlobFromURL;


/***/ }),

/***/ "./node_modules/html-to-image/lib/index.js":
/*!*************************************************!*\
  !*** ./node_modules/html-to-image/lib/index.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var cloneNode_1 = __importDefault(__webpack_require__(/*! ./cloneNode */ "./node_modules/html-to-image/lib/cloneNode.js"));
var embedWebFonts_1 = __importDefault(__webpack_require__(/*! ./embedWebFonts */ "./node_modules/html-to-image/lib/embedWebFonts.js"));
var embedImages_1 = __importDefault(__webpack_require__(/*! ./embedImages */ "./node_modules/html-to-image/lib/embedImages.js"));
var createSvgDataURL_1 = __importDefault(__webpack_require__(/*! ./createSvgDataURL */ "./node_modules/html-to-image/lib/createSvgDataURL.js"));
var applyStyleWithOptions_1 = __importDefault(__webpack_require__(/*! ./applyStyleWithOptions */ "./node_modules/html-to-image/lib/applyStyleWithOptions.js"));
var utils_1 = __webpack_require__(/*! ./utils */ "./node_modules/html-to-image/lib/utils.js");
function getImageSize(domNode, options) {
    if (options === void 0) { options = {}; }
    var width = options.width || utils_1.getNodeWidth(domNode);
    var height = options.height || utils_1.getNodeHeight(domNode);
    return { width: width, height: height };
}
function toSvgDataURL(domNode, options) {
    if (options === void 0) { options = {}; }
    var _a = getImageSize(domNode, options), width = _a.width, height = _a.height;
    return cloneNode_1.default(domNode, options.filter, true)
        .then(function (clonedNode) { return embedWebFonts_1.default(clonedNode, options); })
        .then(function (clonedNode) { return embedImages_1.default(clonedNode, options); })
        .then(function (clonedNode) { return applyStyleWithOptions_1.default(clonedNode, options); })
        .then(function (clonedNode) { return createSvgDataURL_1.default(clonedNode, width, height); });
}
exports.toSvgDataURL = toSvgDataURL;
function toCanvas(domNode, options) {
    if (options === void 0) { options = {}; }
    return toSvgDataURL(domNode, options)
        .then(utils_1.createImage)
        .then(utils_1.delay(100))
        .then(function (image) {
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        var ratio = utils_1.getPixelRatio();
        var _a = getImageSize(domNode, options), width = _a.width, height = _a.height;
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = "" + width;
        canvas.style.height = "" + height;
        context.scale(ratio, ratio);
        if (options.backgroundColor) {
            context.fillStyle = options.backgroundColor;
            context.fillRect(0, 0, canvas.width, canvas.height);
        }
        context.drawImage(image, 0, 0);
        return canvas;
    });
}
exports.toCanvas = toCanvas;
function toPixelData(domNode, options) {
    if (options === void 0) { options = {}; }
    var _a = getImageSize(domNode, options), width = _a.width, height = _a.height;
    return toCanvas(domNode, options)
        .then(function (canvas) { return (canvas.getContext('2d').getImageData(0, 0, width, height).data); });
}
exports.toPixelData = toPixelData;
function toPng(domNode, options) {
    if (options === void 0) { options = {}; }
    return toCanvas(domNode, options).then(function (canvas) { return (canvas.toDataURL()); });
}
exports.toPng = toPng;
function toJpeg(domNode, options) {
    if (options === void 0) { options = {}; }
    return toCanvas(domNode, options).then(function (canvas) { return (canvas.toDataURL('image/jpeg', options.quality || 1)); });
}
exports.toJpeg = toJpeg;
function toBlob(domNode, options) {
    if (options === void 0) { options = {}; }
    return toCanvas(domNode, options).then(utils_1.canvasToBlob);
}
exports.toBlob = toBlob;
exports.default = {
    toSvgDataURL: toSvgDataURL,
    toCanvas: toCanvas,
    toPixelData: toPixelData,
    toPng: toPng,
    toJpeg: toJpeg,
    toBlob: toBlob,
};


/***/ }),

/***/ "./node_modules/html-to-image/lib/utils.js":
/*!*************************************************!*\
  !*** ./node_modules/html-to-image/lib/utils.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var WOFF = 'application/font-woff';
var JPEG = 'image/jpeg';
var mimes = {
    woff: WOFF,
    woff2: WOFF,
    ttf: 'application/font-truetype',
    eot: 'application/vnd.ms-fontobject',
    png: 'image/png',
    jpg: JPEG,
    jpeg: JPEG,
    gif: 'image/gif',
    tiff: 'image/tiff',
    svg: 'image/svg+xml',
};
exports.uuid = (function uuid() {
    // generate uuid for className of pseudo elements.
    // We should not use GUIDs, otherwise pseudo elements sometimes cannot be captured.
    var counter = 0;
    // ref: http://stackoverflow.com/a/6248722/2519373
    var randomFourChars = function () {
        return ("0000" + (Math.random() * (Math.pow(36, 4)) << 0).toString(36)).slice(-4);
    };
    return function () {
        counter += 1;
        return "u" + randomFourChars() + counter;
    };
}());
function parseExtension(url) {
    var match = /\.([^./]*?)$/g.exec(url);
    if (match)
        return match[1];
    return '';
}
exports.parseExtension = parseExtension;
function getMimeType(url) {
    var ext = parseExtension(url).toLowerCase();
    return mimes[ext] || '';
}
exports.getMimeType = getMimeType;
function delay(ms) {
    return function (args) { return new Promise((function (resolve) {
        setTimeout(function () {
            resolve(args);
        }, ms);
    })); };
}
exports.delay = delay;
function createImage(url) {
    return new Promise((function (resolve, reject) {
        var image = new Image();
        image.onload = function () {
            resolve(image);
        };
        image.onerror = reject;
        image.crossOrigin = 'anonymous';
        image.src = url;
    }));
}
exports.createImage = createImage;
function isDataUrl(url) {
    return url.search(/^(data:)/) !== -1;
}
exports.isDataUrl = isDataUrl;
function toDataURL(content, mimeType) {
    return "data:" + mimeType + ";base64," + content;
}
exports.toDataURL = toDataURL;
function getDataURLContent(dataURL) {
    return dataURL.split(/,/)[1];
}
exports.getDataURLContent = getDataURLContent;
function toBlob(canvas) {
    return new Promise((function (resolve) {
        var binaryString = window.atob(canvas.toDataURL().split(',')[1]);
        var len = binaryString.length;
        var binaryArray = new Uint8Array(len);
        for (var i = 0; i < len; i += 1) {
            binaryArray[i] = binaryString.charCodeAt(i);
        }
        resolve(new Blob([binaryArray], {
            type: 'image/png',
        }));
    }));
}
function canvasToBlob(canvas) {
    if (canvas.toBlob) {
        return new Promise((function (resolve) {
            canvas.toBlob(resolve);
        }));
    }
    return toBlob(canvas);
}
exports.canvasToBlob = canvasToBlob;
function toArray(arrayLike) {
    var arr = [];
    for (var i = 0, l = arrayLike.length; i < l; i += 1) {
        arr.push(arrayLike[i]);
    }
    return arr;
}
exports.toArray = toArray;
function px(node, styleProperty) {
    var value = window.getComputedStyle(node).getPropertyValue(styleProperty);
    return parseFloat(value.replace('px', ''));
}
function getNodeWidth(node) {
    var leftBorder = px(node, 'border-left-width');
    var rightBorder = px(node, 'border-right-width');
    return node.scrollWidth + leftBorder + rightBorder;
}
exports.getNodeWidth = getNodeWidth;
function getNodeHeight(node) {
    var topBorder = px(node, 'border-top-width');
    var bottomBorder = px(node, 'border-bottom-width');
    return node.scrollHeight + topBorder + bottomBorder;
}
exports.getNodeHeight = getNodeHeight;
function getPixelRatio() {
    return (window.devicePixelRatio || 1);
}
exports.getPixelRatio = getPixelRatio;
function svgToDataURL(svg) {
    return Promise.resolve()
        .then(function () { return new XMLSerializer().serializeToString(svg); })
        .then(encodeURIComponent)
        .then(function (html) { return "data:image/svg+xml;charset=utf-8," + html; });
}
exports.svgToDataURL = svgToDataURL;
function getBlobFromImageURL(url) {
    return createImage(url).then(function (image) {
        var width = image.width, height = image.height;
        var canvas = document.createElement('canvas');
        var context = canvas.getContext('2d');
        var ratio = getPixelRatio();
        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = "" + width;
        canvas.style.height = "" + height;
        context.scale(ratio, ratio);
        context.drawImage(image, 0, 0);
        var dataURL = canvas.toDataURL(getMimeType(url));
        return getDataURLContent(dataURL);
    });
}
exports.getBlobFromImageURL = getBlobFromImageURL;


/***/ }),

/***/ "./node_modules/printd/index.js":
/*!**************************************!*\
  !*** ./node_modules/printd/index.js ***!
  \**************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var URL_LONG = /^(((http[s]?)|file):)?(\/\/)+([0-9a-zA-Z-_.=?&].+)$/;
var URL_SHORT = /^((\.|\.\.)?\/)([0-9a-zA-Z-_.=?&]+\/)*([0-9a-zA-Z-_.=?&]+)$/;
var isValidURL = function (str) { return URL_LONG.test(str) || URL_SHORT.test(str); };
function createStyle(doc, cssText) {
    var style = doc.createElement('style');
    style.type = 'text/css';
    style.appendChild(window.document.createTextNode(cssText));
    return style;
}
exports.createStyle = createStyle;
function createLinkStyle(doc, url) {
    var style = doc.createElement('link');
    style.type = 'text/css';
    style.rel = 'stylesheet';
    style.href = url;
    return style;
}
exports.createLinkStyle = createLinkStyle;
function createIFrame(parent) {
    var el = window.document.createElement('iframe');
    var css = 'visibility:hidden;width:0;height:0;position:absolute;z-index:-9999;bottom:0;';
    el.setAttribute('src', 'about:blank');
    el.setAttribute('style', css);
    el.setAttribute('width', '0');
    el.setAttribute('height', '0');
    el.setAttribute('wmode', 'opaque');
    parent.appendChild(el);
    return el;
}
exports.createIFrame = createIFrame;
var DEFAULT_OPTIONS = {
    parent: window.document.body,
    headElements: [],
    bodyElements: []
};
/** Printd class that prints HTML elements in a blank document */
var Printd = /** @class */ (function () {
    function Printd(options) {
        this.isLoading = false;
        this.hasEvents = false;
        this.opts = Object.assign(DEFAULT_OPTIONS, (options || {}));
        this.iframe = createIFrame(this.opts.parent);
    }
    /** Gets current Iframe reference */
    Printd.prototype.getIFrame = function () {
        return this.iframe;
    };
    /**
     * Print an HTMLElement
     *
     * @param el HTMLElement
     * @param styles Optional styles (css texts or urls) that will add to iframe document.head
     * @param scripts Optional scripts (script texts or urls) that will add to iframe document.body
     * @param callback Optional callback that will be triggered when content is ready to print
     */
    Printd.prototype.print = function (el, styles, scripts, callback) {
        if (this.isLoading)
            return;
        var _a = this.iframe, contentDocument = _a.contentDocument, contentWindow = _a.contentWindow;
        if (!contentDocument || !contentWindow)
            return;
        this.iframe.src = 'about:blank';
        this.elCopy = el.cloneNode(true);
        if (!this.elCopy)
            return;
        this.isLoading = true;
        this.callback = callback;
        var doc = contentWindow.document;
        doc.open();
        doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>');
        this.addEvents();
        // 1. append custom elements
        var _b = this.opts, headElements = _b.headElements, bodyElements = _b.bodyElements;
        // 1.1 append custom head elements
        if (Array.isArray(headElements)) {
            headElements.forEach(function (el) { return doc.head.appendChild(el); });
        }
        // 1.1 append custom body elements
        if (Array.isArray(bodyElements)) {
            bodyElements.forEach(function (el) { return doc.body.appendChild(el); });
        }
        // 2. append custom styles
        if (Array.isArray(styles)) {
            styles.forEach(function (value) {
                if (value) {
                    if (isValidURL(value)) {
                        doc.head.appendChild(createLinkStyle(doc, value));
                    }
                    else {
                        doc.head.appendChild(createStyle(doc, value));
                    }
                }
            });
        }
        // 3. append element copy
        doc.body.appendChild(this.elCopy);
        // 4. append custom scripts
        if (Array.isArray(scripts)) {
            scripts.forEach(function (value) {
                if (value) {
                    var script = doc.createElement('script');
                    if (isValidURL(value)) {
                        script.src = value;
                    }
                    else {
                        script.innerText = value;
                    }
                    doc.body.appendChild(script);
                }
            });
        }
        doc.close();
    };
    /**
     * Print an URL
     *
     * @param url URL to print
     * @param callback Optional callback that will be triggered when content is ready to print
     */
    Printd.prototype.printURL = function (url, callback) {
        if (this.isLoading)
            return;
        this.addEvents();
        this.isLoading = true;
        this.callback = callback;
        this.iframe.src = url;
    };
    Printd.prototype.launchPrint = function (contentWindow) {
        var result = contentWindow.document.execCommand('print', false, null);
        if (!result) {
            contentWindow.print();
        }
    };
    Printd.prototype.addEvents = function () {
        var _this = this;
        if (!this.hasEvents) {
            this.hasEvents = true;
            this.iframe.addEventListener('load', function () { return _this.onLoad(); }, false);
        }
    };
    Printd.prototype.onLoad = function () {
        var _this = this;
        if (this.iframe) {
            this.isLoading = false;
            var _a = this.iframe, contentDocument = _a.contentDocument, contentWindow_1 = _a.contentWindow;
            if (!contentDocument || !contentWindow_1)
                return;
            if (this.callback) {
                this.callback({
                    iframe: this.iframe,
                    element: this.elCopy,
                    launchPrint: function () { return _this.launchPrint(contentWindow_1); }
                });
            }
            else {
                this.launchPrint(contentWindow_1);
            }
        }
    };
    return Printd;
}());
exports.Printd = Printd;
exports.default = Printd;


/***/ }),

/***/ "./node_modules/util/node_modules/inherits/inherits_browser.js":
/*!*********************************************************************!*\
  !*** ./node_modules/util/node_modules/inherits/inherits_browser.js ***!
  \*********************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

if (typeof Object.create === 'function') {
  // implementation from standard node.js 'util' module
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    ctor.prototype = Object.create(superCtor.prototype, {
      constructor: {
        value: ctor,
        enumerable: false,
        writable: true,
        configurable: true
      }
    });
  };
} else {
  // old school shim for old browsers
  module.exports = function inherits(ctor, superCtor) {
    ctor.super_ = superCtor
    var TempCtor = function () {}
    TempCtor.prototype = superCtor.prototype
    ctor.prototype = new TempCtor()
    ctor.prototype.constructor = ctor
  }
}


/***/ }),

/***/ "./node_modules/util/support/isBufferBrowser.js":
/*!******************************************************!*\
  !*** ./node_modules/util/support/isBufferBrowser.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = function isBuffer(arg) {
  return arg && typeof arg === 'object'
    && typeof arg.copy === 'function'
    && typeof arg.fill === 'function'
    && typeof arg.readUInt8 === 'function';
}

/***/ }),

/***/ "./node_modules/util/util.js":
/*!***********************************!*\
  !*** ./node_modules/util/util.js ***!
  \***********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

/* WEBPACK VAR INJECTION */(function(process) {// Copyright Joyent, Inc. and other Node contributors.
//
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to permit
// persons to whom the Software is furnished to do so, subject to the
// following conditions:
//
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
// OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN
// NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
// DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
// OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
// USE OR OTHER DEALINGS IN THE SOFTWARE.

var getOwnPropertyDescriptors = Object.getOwnPropertyDescriptors ||
  function getOwnPropertyDescriptors(obj) {
    var keys = Object.keys(obj);
    var descriptors = {};
    for (var i = 0; i < keys.length; i++) {
      descriptors[keys[i]] = Object.getOwnPropertyDescriptor(obj, keys[i]);
    }
    return descriptors;
  };

var formatRegExp = /%[sdj%]/g;
exports.format = function(f) {
  if (!isString(f)) {
    var objects = [];
    for (var i = 0; i < arguments.length; i++) {
      objects.push(inspect(arguments[i]));
    }
    return objects.join(' ');
  }

  var i = 1;
  var args = arguments;
  var len = args.length;
  var str = String(f).replace(formatRegExp, function(x) {
    if (x === '%%') return '%';
    if (i >= len) return x;
    switch (x) {
      case '%s': return String(args[i++]);
      case '%d': return Number(args[i++]);
      case '%j':
        try {
          return JSON.stringify(args[i++]);
        } catch (_) {
          return '[Circular]';
        }
      default:
        return x;
    }
  });
  for (var x = args[i]; i < len; x = args[++i]) {
    if (isNull(x) || !isObject(x)) {
      str += ' ' + x;
    } else {
      str += ' ' + inspect(x);
    }
  }
  return str;
};


// Mark that a method should not be used.
// Returns a modified function which warns once by default.
// If --no-deprecation is set, then it is a no-op.
exports.deprecate = function(fn, msg) {
  if (typeof process !== 'undefined' && process.noDeprecation === true) {
    return fn;
  }

  // Allow for deprecating things in the process of starting up.
  if (typeof process === 'undefined') {
    return function() {
      return exports.deprecate(fn, msg).apply(this, arguments);
    };
  }

  var warned = false;
  function deprecated() {
    if (!warned) {
      if (process.throwDeprecation) {
        throw new Error(msg);
      } else if (process.traceDeprecation) {
        console.trace(msg);
      } else {
        console.error(msg);
      }
      warned = true;
    }
    return fn.apply(this, arguments);
  }

  return deprecated;
};


var debugs = {};
var debugEnviron;
exports.debuglog = function(set) {
  if (isUndefined(debugEnviron))
    debugEnviron = process.env.NODE_DEBUG || '';
  set = set.toUpperCase();
  if (!debugs[set]) {
    if (new RegExp('\\b' + set + '\\b', 'i').test(debugEnviron)) {
      var pid = process.pid;
      debugs[set] = function() {
        var msg = exports.format.apply(exports, arguments);
        console.error('%s %d: %s', set, pid, msg);
      };
    } else {
      debugs[set] = function() {};
    }
  }
  return debugs[set];
};


/**
 * Echos the value of a value. Trys to print the value out
 * in the best way possible given the different types.
 *
 * @param {Object} obj The object to print out.
 * @param {Object} opts Optional options object that alters the output.
 */
/* legacy: obj, showHidden, depth, colors*/
function inspect(obj, opts) {
  // default options
  var ctx = {
    seen: [],
    stylize: stylizeNoColor
  };
  // legacy...
  if (arguments.length >= 3) ctx.depth = arguments[2];
  if (arguments.length >= 4) ctx.colors = arguments[3];
  if (isBoolean(opts)) {
    // legacy...
    ctx.showHidden = opts;
  } else if (opts) {
    // got an "options" object
    exports._extend(ctx, opts);
  }
  // set default options
  if (isUndefined(ctx.showHidden)) ctx.showHidden = false;
  if (isUndefined(ctx.depth)) ctx.depth = 2;
  if (isUndefined(ctx.colors)) ctx.colors = false;
  if (isUndefined(ctx.customInspect)) ctx.customInspect = true;
  if (ctx.colors) ctx.stylize = stylizeWithColor;
  return formatValue(ctx, obj, ctx.depth);
}
exports.inspect = inspect;


// http://en.wikipedia.org/wiki/ANSI_escape_code#graphics
inspect.colors = {
  'bold' : [1, 22],
  'italic' : [3, 23],
  'underline' : [4, 24],
  'inverse' : [7, 27],
  'white' : [37, 39],
  'grey' : [90, 39],
  'black' : [30, 39],
  'blue' : [34, 39],
  'cyan' : [36, 39],
  'green' : [32, 39],
  'magenta' : [35, 39],
  'red' : [31, 39],
  'yellow' : [33, 39]
};

// Don't use 'blue' not visible on cmd.exe
inspect.styles = {
  'special': 'cyan',
  'number': 'yellow',
  'boolean': 'yellow',
  'undefined': 'grey',
  'null': 'bold',
  'string': 'green',
  'date': 'magenta',
  // "name": intentionally not styling
  'regexp': 'red'
};


function stylizeWithColor(str, styleType) {
  var style = inspect.styles[styleType];

  if (style) {
    return '\u001b[' + inspect.colors[style][0] + 'm' + str +
           '\u001b[' + inspect.colors[style][1] + 'm';
  } else {
    return str;
  }
}


function stylizeNoColor(str, styleType) {
  return str;
}


function arrayToHash(array) {
  var hash = {};

  array.forEach(function(val, idx) {
    hash[val] = true;
  });

  return hash;
}


function formatValue(ctx, value, recurseTimes) {
  // Provide a hook for user-specified inspect functions.
  // Check that value is an object with an inspect function on it
  if (ctx.customInspect &&
      value &&
      isFunction(value.inspect) &&
      // Filter out the util module, it's inspect function is special
      value.inspect !== exports.inspect &&
      // Also filter out any prototype objects using the circular check.
      !(value.constructor && value.constructor.prototype === value)) {
    var ret = value.inspect(recurseTimes, ctx);
    if (!isString(ret)) {
      ret = formatValue(ctx, ret, recurseTimes);
    }
    return ret;
  }

  // Primitive types cannot have properties
  var primitive = formatPrimitive(ctx, value);
  if (primitive) {
    return primitive;
  }

  // Look up the keys of the object.
  var keys = Object.keys(value);
  var visibleKeys = arrayToHash(keys);

  if (ctx.showHidden) {
    keys = Object.getOwnPropertyNames(value);
  }

  // IE doesn't make error fields non-enumerable
  // http://msdn.microsoft.com/en-us/library/ie/dww52sbt(v=vs.94).aspx
  if (isError(value)
      && (keys.indexOf('message') >= 0 || keys.indexOf('description') >= 0)) {
    return formatError(value);
  }

  // Some type of object without properties can be shortcutted.
  if (keys.length === 0) {
    if (isFunction(value)) {
      var name = value.name ? ': ' + value.name : '';
      return ctx.stylize('[Function' + name + ']', 'special');
    }
    if (isRegExp(value)) {
      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
    }
    if (isDate(value)) {
      return ctx.stylize(Date.prototype.toString.call(value), 'date');
    }
    if (isError(value)) {
      return formatError(value);
    }
  }

  var base = '', array = false, braces = ['{', '}'];

  // Make Array say that they are Array
  if (isArray(value)) {
    array = true;
    braces = ['[', ']'];
  }

  // Make functions say that they are functions
  if (isFunction(value)) {
    var n = value.name ? ': ' + value.name : '';
    base = ' [Function' + n + ']';
  }

  // Make RegExps say that they are RegExps
  if (isRegExp(value)) {
    base = ' ' + RegExp.prototype.toString.call(value);
  }

  // Make dates with properties first say the date
  if (isDate(value)) {
    base = ' ' + Date.prototype.toUTCString.call(value);
  }

  // Make error with message first say the error
  if (isError(value)) {
    base = ' ' + formatError(value);
  }

  if (keys.length === 0 && (!array || value.length == 0)) {
    return braces[0] + base + braces[1];
  }

  if (recurseTimes < 0) {
    if (isRegExp(value)) {
      return ctx.stylize(RegExp.prototype.toString.call(value), 'regexp');
    } else {
      return ctx.stylize('[Object]', 'special');
    }
  }

  ctx.seen.push(value);

  var output;
  if (array) {
    output = formatArray(ctx, value, recurseTimes, visibleKeys, keys);
  } else {
    output = keys.map(function(key) {
      return formatProperty(ctx, value, recurseTimes, visibleKeys, key, array);
    });
  }

  ctx.seen.pop();

  return reduceToSingleString(output, base, braces);
}


function formatPrimitive(ctx, value) {
  if (isUndefined(value))
    return ctx.stylize('undefined', 'undefined');
  if (isString(value)) {
    var simple = '\'' + JSON.stringify(value).replace(/^"|"$/g, '')
                                             .replace(/'/g, "\\'")
                                             .replace(/\\"/g, '"') + '\'';
    return ctx.stylize(simple, 'string');
  }
  if (isNumber(value))
    return ctx.stylize('' + value, 'number');
  if (isBoolean(value))
    return ctx.stylize('' + value, 'boolean');
  // For some reason typeof null is "object", so special case here.
  if (isNull(value))
    return ctx.stylize('null', 'null');
}


function formatError(value) {
  return '[' + Error.prototype.toString.call(value) + ']';
}


function formatArray(ctx, value, recurseTimes, visibleKeys, keys) {
  var output = [];
  for (var i = 0, l = value.length; i < l; ++i) {
    if (hasOwnProperty(value, String(i))) {
      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
          String(i), true));
    } else {
      output.push('');
    }
  }
  keys.forEach(function(key) {
    if (!key.match(/^\d+$/)) {
      output.push(formatProperty(ctx, value, recurseTimes, visibleKeys,
          key, true));
    }
  });
  return output;
}


function formatProperty(ctx, value, recurseTimes, visibleKeys, key, array) {
  var name, str, desc;
  desc = Object.getOwnPropertyDescriptor(value, key) || { value: value[key] };
  if (desc.get) {
    if (desc.set) {
      str = ctx.stylize('[Getter/Setter]', 'special');
    } else {
      str = ctx.stylize('[Getter]', 'special');
    }
  } else {
    if (desc.set) {
      str = ctx.stylize('[Setter]', 'special');
    }
  }
  if (!hasOwnProperty(visibleKeys, key)) {
    name = '[' + key + ']';
  }
  if (!str) {
    if (ctx.seen.indexOf(desc.value) < 0) {
      if (isNull(recurseTimes)) {
        str = formatValue(ctx, desc.value, null);
      } else {
        str = formatValue(ctx, desc.value, recurseTimes - 1);
      }
      if (str.indexOf('\n') > -1) {
        if (array) {
          str = str.split('\n').map(function(line) {
            return '  ' + line;
          }).join('\n').substr(2);
        } else {
          str = '\n' + str.split('\n').map(function(line) {
            return '   ' + line;
          }).join('\n');
        }
      }
    } else {
      str = ctx.stylize('[Circular]', 'special');
    }
  }
  if (isUndefined(name)) {
    if (array && key.match(/^\d+$/)) {
      return str;
    }
    name = JSON.stringify('' + key);
    if (name.match(/^"([a-zA-Z_][a-zA-Z_0-9]*)"$/)) {
      name = name.substr(1, name.length - 2);
      name = ctx.stylize(name, 'name');
    } else {
      name = name.replace(/'/g, "\\'")
                 .replace(/\\"/g, '"')
                 .replace(/(^"|"$)/g, "'");
      name = ctx.stylize(name, 'string');
    }
  }

  return name + ': ' + str;
}


function reduceToSingleString(output, base, braces) {
  var numLinesEst = 0;
  var length = output.reduce(function(prev, cur) {
    numLinesEst++;
    if (cur.indexOf('\n') >= 0) numLinesEst++;
    return prev + cur.replace(/\u001b\[\d\d?m/g, '').length + 1;
  }, 0);

  if (length > 60) {
    return braces[0] +
           (base === '' ? '' : base + '\n ') +
           ' ' +
           output.join(',\n  ') +
           ' ' +
           braces[1];
  }

  return braces[0] + base + ' ' + output.join(', ') + ' ' + braces[1];
}


// NOTE: These type checking functions intentionally don't use `instanceof`
// because it is fragile and can be easily faked with `Object.create()`.
function isArray(ar) {
  return Array.isArray(ar);
}
exports.isArray = isArray;

function isBoolean(arg) {
  return typeof arg === 'boolean';
}
exports.isBoolean = isBoolean;

function isNull(arg) {
  return arg === null;
}
exports.isNull = isNull;

function isNullOrUndefined(arg) {
  return arg == null;
}
exports.isNullOrUndefined = isNullOrUndefined;

function isNumber(arg) {
  return typeof arg === 'number';
}
exports.isNumber = isNumber;

function isString(arg) {
  return typeof arg === 'string';
}
exports.isString = isString;

function isSymbol(arg) {
  return typeof arg === 'symbol';
}
exports.isSymbol = isSymbol;

function isUndefined(arg) {
  return arg === void 0;
}
exports.isUndefined = isUndefined;

function isRegExp(re) {
  return isObject(re) && objectToString(re) === '[object RegExp]';
}
exports.isRegExp = isRegExp;

function isObject(arg) {
  return typeof arg === 'object' && arg !== null;
}
exports.isObject = isObject;

function isDate(d) {
  return isObject(d) && objectToString(d) === '[object Date]';
}
exports.isDate = isDate;

function isError(e) {
  return isObject(e) &&
      (objectToString(e) === '[object Error]' || e instanceof Error);
}
exports.isError = isError;

function isFunction(arg) {
  return typeof arg === 'function';
}
exports.isFunction = isFunction;

function isPrimitive(arg) {
  return arg === null ||
         typeof arg === 'boolean' ||
         typeof arg === 'number' ||
         typeof arg === 'string' ||
         typeof arg === 'symbol' ||  // ES6 symbol
         typeof arg === 'undefined';
}
exports.isPrimitive = isPrimitive;

exports.isBuffer = __webpack_require__(/*! ./support/isBuffer */ "./node_modules/util/support/isBufferBrowser.js");

function objectToString(o) {
  return Object.prototype.toString.call(o);
}


function pad(n) {
  return n < 10 ? '0' + n.toString(10) : n.toString(10);
}


var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep',
              'Oct', 'Nov', 'Dec'];

// 26 Feb 16:19:34
function timestamp() {
  var d = new Date();
  var time = [pad(d.getHours()),
              pad(d.getMinutes()),
              pad(d.getSeconds())].join(':');
  return [d.getDate(), months[d.getMonth()], time].join(' ');
}


// log is just a thin wrapper to console.log that prepends a timestamp
exports.log = function() {
  console.log('%s - %s', timestamp(), exports.format.apply(exports, arguments));
};


/**
 * Inherit the prototype methods from one constructor into another.
 *
 * The Function.prototype.inherits from lang.js rewritten as a standalone
 * function (not on Function.prototype). NOTE: If this file is to be loaded
 * during bootstrapping this function needs to be rewritten using some native
 * functions as prototype setup using normal JavaScript does not work as
 * expected during bootstrapping (see mirror.js in r114903).
 *
 * @param {function} ctor Constructor function which needs to inherit the
 *     prototype.
 * @param {function} superCtor Constructor function to inherit prototype from.
 */
exports.inherits = __webpack_require__(/*! inherits */ "./node_modules/util/node_modules/inherits/inherits_browser.js");

exports._extend = function(origin, add) {
  // Don't do anything if add isn't an object
  if (!add || !isObject(add)) return origin;

  var keys = Object.keys(add);
  var i = keys.length;
  while (i--) {
    origin[keys[i]] = add[keys[i]];
  }
  return origin;
};

function hasOwnProperty(obj, prop) {
  return Object.prototype.hasOwnProperty.call(obj, prop);
}

var kCustomPromisifiedSymbol = typeof Symbol !== 'undefined' ? Symbol('util.promisify.custom') : undefined;

exports.promisify = function promisify(original) {
  if (typeof original !== 'function')
    throw new TypeError('The "original" argument must be of type Function');

  if (kCustomPromisifiedSymbol && original[kCustomPromisifiedSymbol]) {
    var fn = original[kCustomPromisifiedSymbol];
    if (typeof fn !== 'function') {
      throw new TypeError('The "util.promisify.custom" argument must be of type Function');
    }
    Object.defineProperty(fn, kCustomPromisifiedSymbol, {
      value: fn, enumerable: false, writable: false, configurable: true
    });
    return fn;
  }

  function fn() {
    var promiseResolve, promiseReject;
    var promise = new Promise(function (resolve, reject) {
      promiseResolve = resolve;
      promiseReject = reject;
    });

    var args = [];
    for (var i = 0; i < arguments.length; i++) {
      args.push(arguments[i]);
    }
    args.push(function (err, value) {
      if (err) {
        promiseReject(err);
      } else {
        promiseResolve(value);
      }
    });

    try {
      original.apply(this, args);
    } catch (err) {
      promiseReject(err);
    }

    return promise;
  }

  Object.setPrototypeOf(fn, Object.getPrototypeOf(original));

  if (kCustomPromisifiedSymbol) Object.defineProperty(fn, kCustomPromisifiedSymbol, {
    value: fn, enumerable: false, writable: false, configurable: true
  });
  return Object.defineProperties(
    fn,
    getOwnPropertyDescriptors(original)
  );
}

exports.promisify.custom = kCustomPromisifiedSymbol

function callbackifyOnRejected(reason, cb) {
  // `!reason` guard inspired by bluebird (Ref: https://goo.gl/t5IS6M).
  // Because `null` is a special error value in callbacks which means "no error
  // occurred", we error-wrap so the callback consumer can distinguish between
  // "the promise rejected with null" or "the promise fulfilled with undefined".
  if (!reason) {
    var newReason = new Error('Promise was rejected with a falsy value');
    newReason.reason = reason;
    reason = newReason;
  }
  return cb(reason);
}

function callbackify(original) {
  if (typeof original !== 'function') {
    throw new TypeError('The "original" argument must be of type Function');
  }

  // We DO NOT return the promise as it gives the user a false sense that
  // the promise is actually somehow related to the callback's execution
  // and that the callback throwing will reject the promise.
  function callbackified() {
    var args = [];
    for (var i = 0; i < arguments.length; i++) {
      args.push(arguments[i]);
    }

    var maybeCb = args.pop();
    if (typeof maybeCb !== 'function') {
      throw new TypeError('The last argument must be of type Function');
    }
    var self = this;
    var cb = function() {
      return maybeCb.apply(self, arguments);
    };
    // In true node style we process the callback on `nextTick` with all the
    // implications (stack, `uncaughtException`, `async_hooks`)
    original.apply(this, args)
      .then(function(ret) { process.nextTick(cb, null, ret) },
            function(rej) { process.nextTick(callbackifyOnRejected, rej, cb) });
  }

  Object.setPrototypeOf(callbackified, Object.getPrototypeOf(original));
  Object.defineProperties(callbackified,
                          getOwnPropertyDescriptors(original));
  return callbackified;
}
exports.callbackify = callbackify;

/* WEBPACK VAR INJECTION */}.call(this, __webpack_require__(/*! ./../process/browser.js */ "./node_modules/process/browser.js")))

/***/ })

}]);