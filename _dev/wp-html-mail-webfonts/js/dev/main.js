/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./js/src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./js/components/WebfontSelector.js":
/*!******************************************!*\
  !*** ./js/components/WebfontSelector.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return WebfontSelector; });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }



 //import EditableElement from "./EditableElement";

function WebfontSelector() {
  var _useState = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])({}),
      _useState2 = _slicedToArray(_useState, 2),
      settings = _useState2[0],
      setSettings = _useState2[1];

  var _useState3 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])("Lorem ipsum dolor sit amet, consetetur sadipscing elitr"),
      _useState4 = _slicedToArray(_useState3, 2),
      previewText = _useState4[0],
      setPreviewText = _useState4[1];

  var _useState5 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])(1),
      _useState6 = _slicedToArray(_useState5, 2),
      rerenderKey = _useState6[0],
      setRerenderKey = _useState6[1];

  var _useState7 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])(false),
      _useState8 = _slicedToArray(_useState7, 2),
      isSaving = _useState8[0],
      setIsSaving = _useState8[1];

  var _useState9 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])(true),
      _useState10 = _slicedToArray(_useState9, 2),
      isLoading = _useState10[0],
      setIsLoading = _useState10[1];

  var _useState11 = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useState"])(false),
      _useState12 = _slicedToArray(_useState11, 2),
      showSaveSuccess = _useState12[0],
      setShowSaveSuccess = _useState12[1];

  var loadSettings = function loadSettings() {
    fetch(window.wphtmlmailWebfonts.restUrl + "webfontsettings").then(function (resp) {
      return resp.json();
    }).then(function (data) {
      var newSettings = _objectSpread({}, data);

      if (!("fontsets" in newSettings)) newSettings.fontsets = [];

      for (var fontsetIndex = 0; fontsetIndex <= 2; fontsetIndex++) {
        if (newSettings.fontsets.length <= fontsetIndex) newSettings.fontsets[fontsetIndex] = {
          googleFont: "",
          fallbackFont: ""
        };
      }

      setSettings(newSettings);
      setIsLoading(false);
    });
  };

  var saveSettings = function saveSettings() {
    setIsSaving(true);

    var preparedSettings = _objectSpread({}, settings);

    preparedSettings.fontsets.forEach(function (fontset, fontsetIndex) {
      if (preparedSettings.fontsets[fontsetIndex].googleFont && preparedSettings.fontsets[fontsetIndex].fallbackFont) {
        preparedSettings.fontsets[fontsetIndex].name = "Fontset #" + (fontsetIndex + 1) + ": " + preparedSettings.fontsets[fontsetIndex].googleFont;
        preparedSettings.fontsets[fontsetIndex].cssvalue = '"' + preparedSettings.fontsets[fontsetIndex].googleFont + '",' + preparedSettings.fontsets[fontsetIndex].fallbackFont;
      } else {
        preparedSettings.fontsets[fontsetIndex].name = "";
        preparedSettings.fontsets[fontsetIndex].cssvalue = "";
      }
    });
    var request = new Request(window.wphtmlmailWebfonts.restUrl + "webfontsettings", {
      method: "POST",
      body: JSON.stringify(preparedSettings),
      headers: {
        "Content-Type": "application/json"
      }
    });
    fetch(request).then(function (resp) {
      setIsSaving(false);
      setShowSaveSuccess(true);
      setTimeout(function () {
        setShowSaveSuccess(false);
      }, 4000);
    });
  };

  var refreshGoogleFontsStylesheetURL = function refreshGoogleFontsStylesheetURL(settings) {
    var url = "";
    settings.fontsets.forEach(function (fontset) {
      if (fontset.googleFont) url += "&family=" + fontset.googleFont.replace(" ", "+");
    });
    if (url === "") url = false;else url = "https://fonts.googleapis.com/css2?display=swap" + url;
    return url;
  };

  Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["useEffect"])(function () {
    loadSettings();
  }, []);

  var renderFontset = function renderFontset(fontsetIndex) {
    var googleFonts = [];
    window.wphtmlmailWebfonts.googleFonts.forEach(function (font) {
      googleFonts.push({
        label: font.family,
        value: font.family
      });
    });
    return /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelBody"], {
      title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Fontset", "wp-html-mail-webfonts") + " #" + (fontsetIndex + 1) + (settings.fontsets[fontsetIndex].googleFont ? ": " + settings.fontsets[fontsetIndex].googleFont : ""),
      initialOpen: fontsetIndex === 0,
      className: "fontset",
      key: "fontset-" + fontsetIndex
    }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], {
      className: "fontsetrow"
    }, /*#__PURE__*/React.createElement("div", {
      className: "fontselectcol"
    }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Google Webfont", "wp-html-mail-webfonts"),
      key: "google-font-select-" + fontsetIndex + rerenderKey,
      value: settings.fontsets[fontsetIndex].googleFont,
      options: [{
        value: "",
        label: "-"
      }].concat(googleFonts),
      onChange: function onChange(font) {
        setSettings(function (settings) {
          settings.fontsets[fontsetIndex].googleFont = font;
          settings.googleFontsStylesheetURL = refreshGoogleFontsStylesheetURL(settings);
          return settings;
        });
        setRerenderKey(function (rerenderKey) {
          return rerenderKey + 1;
        });
      }
    })), /*#__PURE__*/React.createElement("div", {
      className: "previewcol"
    }, settings.fontsets[fontsetIndex].googleFont && /*#__PURE__*/React.createElement("input", {
      className: "previewtext",
      type: "text",
      style: {
        fontFamily: settings.fontsets[fontsetIndex].googleFont
      },
      value: previewText,
      onChange: function onChange(e) {
        setPreviewText(e.target.value);
      }
    }))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["PanelRow"], {
      className: "fontsetrow"
    }, /*#__PURE__*/React.createElement("div", {
      className: "fontselectcol"
    }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Alternative font", "wp-html-mail-webfonts"),
      key: "fallback-font-select-" + fontsetIndex + rerenderKey,
      value: settings.fontsets[fontsetIndex].fallbackFont,
      options: [{
        value: "",
        label: "-"
      }].concat(_toConsumableArray(window.wphtmlmailWebfonts.fallbackFonts)),
      onChange: function onChange(font) {
        setSettings(function (settings) {
          settings.fontsets[fontsetIndex].fallbackFont = font;
          return settings;
        });
        setRerenderKey(function (rerenderKey) {
          return rerenderKey + 1;
        });
      }
    })), /*#__PURE__*/React.createElement("div", {
      className: "previewcol"
    }, settings.fontsets[fontsetIndex].fallbackFont && /*#__PURE__*/React.createElement("input", {
      className: "previewtext",
      type: "text",
      style: {
        fontFamily: settings.fontsets[fontsetIndex].fallbackFont
      },
      value: previewText,
      onChange: function onChange(e) {
        setPreviewText(e.target.value);
      }
    }))));
  };

  if (isLoading || !settings) return /*#__PURE__*/React.createElement("div", {
    className: "mail-loader"
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Spinner"], null));else return /*#__PURE__*/React.createElement(React.Fragment, null, /*#__PURE__*/React.createElement("div", {
    className: "settings-editor"
  }, showSaveSuccess && /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Notice"], {
    status: "success",
    isDismissible: false
  }, /*#__PURE__*/React.createElement("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Your settings have been saved. Go to the template tab now and use your new fonts.", "wp-html-mail-webfonts"))), /*#__PURE__*/React.createElement("div", {
    className: "save-button-pane components-panel__header"
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Button"], {
    isPrimary: true,
    isBusy: isSaving,
    onClick: function onClick(e) {
      e.preventDefault();
      saveSettings();
    }
  }, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Save settings", "wp-html-mail-webfonts"))), /*#__PURE__*/React.createElement("div", {
    className: "description"
  }, /*#__PURE__*/React.createElement("h3", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Choose your fonts", "wp-html-mail-webfonts")), /*#__PURE__*/React.createElement("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Create up to three fontsets. Each of them consists of a webfont and a fallback font for email clients which do not support webfonts. Once defined here you can use them in your template and in our WooCommerce extension like any other font.", "wp-html-mail-webfonts"))), "googleFontsStylesheetURL" in settings && settings.googleFontsStylesheetURL && /*#__PURE__*/React.createElement("link", {
    key: "google-stylesheet-" + rerenderKey,
    rel: "stylesheet",
    type: "text/css",
    href: settings.googleFontsStylesheetURL
  }), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Panel"], {
    className: "fontsets-panel"
  }, settings.fontsets && settings.fontsets.length > 0 && settings.fontsets.map(function (fontset, fontsetIndex) {
    return renderFontset(fontsetIndex);
  }))), /*#__PURE__*/React.createElement("div", {
    className: "info-sidebar"
  }, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["Card"], null, /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["CardHeader"], null, /*#__PURE__*/React.createElement("h3", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Webfonts in emails", "wp-html-mail-webfonts"))), /*#__PURE__*/React.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__["CardBody"], {
    className: "description"
  }, /*#__PURE__*/React.createElement("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("As not all email clients can handle webfonts those who don't will show a different font family. To avoid each email client to pick its own favorite font you can define an alternativ font for each webfont you choose. Please keep in mind that your fallback font uses the same font size and style as the main one.", "wp-html-mail-webfonts")), /*#__PURE__*/React.createElement("p", null, Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__["__"])("Currently these email clients support webfonts:", "wp-html-mail-webfonts")), /*#__PURE__*/React.createElement("ul", null, /*#__PURE__*/React.createElement("li", null, "Apple Mail"), /*#__PURE__*/React.createElement("li", null, "iOS Mail"), /*#__PURE__*/React.createElement("li", null, "Google Android"), /*#__PURE__*/React.createElement("li", null, "Samsung Mail (Android 8.0)"), /*#__PURE__*/React.createElement("li", null, "Outlook for Mac"), /*#__PURE__*/React.createElement("li", null, "Outlook App"))))));
}

/***/ }),

/***/ "./js/src/index.js":
/*!*************************!*\
  !*** ./js/src/index.js ***!
  \*************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ "react-dom");
/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(react_dom__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _components_WebfontSelector__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../components/WebfontSelector */ "./js/components/WebfontSelector.js");



document.addEventListener("DOMContentLoaded", function () {
  react_dom__WEBPACK_IMPORTED_MODULE_1___default.a.render( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0___default.a.createElement(_components_WebfontSelector__WEBPACK_IMPORTED_MODULE_2__["default"], null), document.getElementById("wp-html-mail-webfonts"));
});

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "react":
/*!*********************************!*\
  !*** external {"this":"React"} ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["React"]; }());

/***/ }),

/***/ "react-dom":
/*!************************************!*\
  !*** external {"this":"ReactDOM"} ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["ReactDOM"]; }());

/***/ })

/******/ });
//# sourceMappingURL=main.js.map