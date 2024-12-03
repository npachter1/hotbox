(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[5],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_Harvest__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/Harvest */ "./resources/js/models/Harvest.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_1__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    id: {
      type: [Number, String],
      "default": 0
    },
    model: {
      type: String,
      "default": 'Harvest'
    },
    module: {
      type: String,
      "default": 'plants'
    },
    type: {
      type: [String, Number],
      "default": 'form' // form or modal, which routes to grid or just emits result

    }
  },
  data: function data() {
    return {
      item: null,
      isSyncing: null,
      itemState: 'save',
      isMedical: false
    };
  },
  components: {},
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;

    if (this.id) {
      _models_Harvest__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.id).then(function (response) {
        _this.item = new _models_Harvest__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this.schema);
        _this.isLoading = false;

        _this.$emit('loaded');

        axios.get('/api/v1/admin/auth/location/ismedical/' + _this.item.location_id).then(function (response2) {
          _this.isMedical = response2.data;
        });
      })["catch"](function (error) {
        _this.$announcer({
          status: 400,
          data: {
            message: 'We had a hiccup fetching the data - Please try again later.'
          }
        });
      });
    } else {
      this.item = new _models_Harvest__WEBPACK_IMPORTED_MODULE_0__["default"]().withDefaults(this.schema);
      this.isLoading = false;
    }
  },
  methods: {
    autoSave: function autoSave() {
      var _this2 = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      if (confirm === false && !this.id) return false; // dont autosave a new entry unless pressing button (ie confirming)

      this.$validator.validateAll().then(function (result) {
        if (result) {
          if (!confirm) lodash__WEBPACK_IMPORTED_MODULE_1___default.a.debounce(function () {
            _this2._save();
          }, 2000)();else _this2._save(true);
        } else if (confirm == true) {
          _this2.$announcer({
            status: 422,
            data: {
              message: 'Whoops, Please check and correct inputs in order to continue.'
            }
          });
        } else _this2.$validator.reset(); // if not validated or confirming, clear validation errors..

      });
    },
    _save: function _save() {
      var _this3 = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.itemState = 'saving..';
      this.item.save().then(function (response) {
        if (confirm) {
          _this3.$announcer({
            status: 200,
            data: {
              message: 'Your ' + _this3.model + ' data has been Saved!'
            }
          });

          if (response.schema) _this3.$store.commit(_this3.module + '/setSchema', {
            data: response.schema,
            key: _this3.model.toLowerCase() + 'Schema'
          });

          if (_this3.type === 'modal') {
            // if we are a modal edit, we need to reset new schemas so any parent form can select any new value we created.
            //await this.$store.dispatch(this.module+'/setSchemas','campaign,group');
            _this3.$emit('refresh', response);

            _this3.$emit('toggle');
          } else _this3.$router.push({
            name: _this3.model.toLowerCase()
          });
        }

        _this3.itemState = 'saved';
      })["catch"](function (error) {
        _this3.$announcer(error.response);

        _this3.itemState = 'resave';
      });
    },
    syncMetrcHarvests: function syncMetrcHarvests() {
      var _this4 = this;

      this.isSyncing = true;
      axios.get('/api/v1/admin/grow/harvests/syncMetrc').then(function (response) {
        _this4.isSyncing = false;
        if (response.data.schema) _this4.$store.commit(_this4.module + '/setSchema', {
          data: response.data.schema,
          key: _this4.model.toLowerCase() + 'Schema'
        });

        _this4.$announcer(response);
      })["catch"](function (error) {
        _this4.isSyncing = false;

        _this4.$announcer(error.response);
      });
    },
    getRoomLink: function getRoomLink(id) {
      if (id) return '/admin/warehouse/room/' + id + '/edit';else return '';
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    }
  },
  watch: {
    item: {
      handler: function handler(newVal, oldVal) {
        this.itemState = oldVal ? 'save changes' : newVal.id ? 'save' : 'create';
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478& ***!
  \***********************************************************************************************************************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "render", function() { return render; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return staticRenderFns; });
var render = function() {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return _vm.item && _vm.schema
    ? _c("div", { staticClass: "col-12" }, [
        _c(
          "form",
          {
            on: {
              change: function($event) {
                return _vm.$emit("change")
              }
            }
          },
          [
            _c("fieldset", [
              _c("div", { staticClass: "col-12" }, [
                _vm.$store.getters.getAgent == "metrc"
                  ? _c(
                      "a",
                      {
                        staticClass: "float-right",
                        attrs: { href: "" },
                        on: {
                          click: function($event) {
                            $event.preventDefault()
                            return _vm.syncMetrcHarvests($event)
                          }
                        }
                      },
                      [
                        _c("spinner", {
                          staticClass: "float-left",
                          attrs: {
                            isProcessing: _vm.isSyncing,
                            isFullScreen: false,
                            isLine: true,
                            spinnerWidth: 25
                          }
                        }),
                        _vm._v(" "),
                        !_vm.isSyncing
                          ? _c("i", {
                              staticClass: "hotbox-icon hotbox-icon-refresh-69"
                            })
                          : _vm._e(),
                        _vm._v(" Sync Harvests\n            ")
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm.item.id
                  ? _c("h3", { staticClass: "mb-4" }, [
                      _vm._v(_vm._s(_vm.item.name))
                    ])
                  : _vm._e(),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  _vm._l(_vm.schema.form, function(formItem, ele) {
                    return _c("div", { staticClass: "col-6" }, [
                      formItem.name == "patient_license_number"
                        ? _c("div", [
                            _vm.isMedical
                              ? _c(
                                  "label",
                                  { staticStyle: { "padding-right": "15px" } },
                                  [_vm._v(_vm._s(formItem.title))]
                                )
                              : _vm._e()
                          ])
                        : _c("div", [
                            _c(
                              "label",
                              { staticStyle: { "padding-right": "15px" } },
                              [_vm._v(_vm._s(formItem.title))]
                            )
                          ]),
                      _vm._v(" "),
                      formItem.name == "drying_room_id"
                        ? _c("div", [
                            _vm.item["drying_room_id"]
                              ? _c(
                                  "a",
                                  {
                                    attrs: {
                                      href: _vm.getRoomLink(
                                        _vm.item["drying_room_id"]
                                      )
                                    }
                                  },
                                  [
                                    _vm._v(
                                      "\n                            " +
                                        _vm._s(
                                          _vm.schema.form.drying_room_id.values.find(
                                            function(r) {
                                              return (
                                                r.id ===
                                                _vm.item["drying_room_id"]
                                              )
                                            }
                                          ).name
                                        ) +
                                        "\n                            "
                                    )
                                  ]
                                )
                              : _c("span", [_vm._v("n/a")])
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      formItem.name == "patient_license_number" && _vm.isMedical
                        ? _c("div", [
                            _vm._v(
                              "\n                            " +
                                _vm._s(_vm.item[ele] ? _vm.item[ele] : "n/a") +
                                "\n                        "
                            )
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      formItem.type == "weight"
                        ? _c("div", [
                            _vm._v(
                              _vm._s(_vm.item[ele]) +
                                " " +
                                _vm._s(_vm.item["unit_of_weight"])
                            )
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      formItem.type == "date"
                        ? _c("div", [
                            _vm._v(
                              _vm._s(
                                _vm._f("formattedLocalDate")(_vm.item[ele])
                              )
                            )
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      formItem.type == "number"
                        ? _c("div", [
                            _vm._v(_vm._s(_vm.item[ele] ? _vm.item[ele] : "0"))
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      formItem.type == "text" &&
                      formItem.name != "drying_room_id" &&
                      formItem.name != "patient_license_number"
                        ? _c("div", [
                            _vm._v(
                              _vm._s(_vm.item[ele] ? _vm.item[ele] : "n/a")
                            )
                          ])
                        : _vm._e()
                    ])
                  }),
                  0
                ),
                _vm._v(" "),
                _c("div", { staticClass: "col-12 clearfix mt-3" }, [
                  _c("div", { staticClass: "drsection-content" }, [
                    _vm.type !== "form"
                      ? _c(
                          "a",
                          {
                            staticClass: "btn btn-md btn-light",
                            on: {
                              click: function($event) {
                                $event.preventDefault()
                                return _vm.$emit("toggle")
                              }
                            }
                          },
                          [_vm._v("Close.")]
                        )
                      : _vm._e(),
                    _vm._v(" "),
                    _vm.type === "form"
                      ? _c(
                          "a",
                          {
                            staticClass: "btn btn-sm btn-light",
                            on: {
                              click: function($event) {
                                if (
                                  !$event.type.indexOf("key") &&
                                  _vm._k(
                                    $event.keyCode,
                                    "default",
                                    undefined,
                                    $event.key,
                                    undefined
                                  )
                                ) {
                                  return null
                                }
                                return _vm.$router.go(-1)
                              }
                            }
                          },
                          [_vm._v("Return.")]
                        )
                      : _vm._e()
                  ])
                ])
              ])
            ])
          ]
        )
      ])
    : _c(
        "div",
        [
          _c("loading", {
            attrs: {
              display: _vm.schema && _vm.item ? false : true,
              type: "loadPage"
            }
          })
        ],
        1
      )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/views/plants/harvest/showDetails.vue":
/*!**********************************************************************!*\
  !*** ./resources/js/components/views/plants/harvest/showDetails.vue ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./showDetails.vue?vue&type=template&id=86840478& */ "./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478&");
/* harmony import */ var _showDetails_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./showDetails.vue?vue&type=script&lang=js& */ "./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _showDetails_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__["render"],
  _showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/plants/harvest/showDetails.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_showDetails_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./showDetails.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_showDetails_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478& ***!
  \*****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./showDetails.vue?vue&type=template&id=86840478& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/plants/harvest/showDetails.vue?vue&type=template&id=86840478&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_showDetails_vue_vue_type_template_id_86840478___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Harvest.js":
/*!****************************************!*\
  !*** ./resources/js/models/Harvest.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Harvest; });
/* harmony import */ var _Model__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Model */ "./resources/js/models/Model.js");
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }



var Harvest =
/*#__PURE__*/
function (_Model) {
  _inherits(Harvest, _Model);

  function Harvest() {
    _classCallCheck(this, Harvest);

    return _possibleConstructorReturn(this, _getPrototypeOf(Harvest).apply(this, arguments));
  }

  _createClass(Harvest, [{
    key: "resource",
    value: function resource() {
      return 'admin/grow/harvests';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Harvest;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);