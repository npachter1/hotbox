(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[2],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_Room__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/Room */ "./resources/js/models/Room.js");
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


/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    id: {
      type: [Number, String],
      "default": 0
    },
    model: {
      type: String,
      "default": 'Room'
    },
    module: {
      type: String,
      "default": 'warehouse'
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
      itemState: 'save'
    };
  },
  components: {},
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;

    if (this.id) {
      _models_Room__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.id).then(function (response) {
        _this.item = new _models_Room__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this.schema);

        _this.$emit('loaded');

        _this.isLoading = false;
      })["catch"](function (error) {
        _this.$announcer({
          status: 400,
          data: {
            message: 'We had a hiccup fetching the data - Please try again later.'
          }
        });
      });
    } else {
      this.item = new _models_Room__WEBPACK_IMPORTED_MODULE_0__["default"]().withDefaults(this.schema);
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
          }); //this.$router.push({name:this.model.toLowerCase()});

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
    syncMetrcRooms: function syncMetrcRooms() {
      var _this4 = this;

      this.isSyncing = true;
      axios.get('/api/v1/admin/grow/rooms/syncMetrc').then(function (response) {
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa&":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa& ***!
  \********************************************************************************************************************************************************************************************************************************/
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
                            return _vm.syncMetrcRooms($event)
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
                        _vm._v(" Sync Rooms\n            ")
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm.item.id
                  ? _c("h3", { staticClass: "mb-4" }, [
                      _vm._v("Edit " + _vm._s(_vm.item.name))
                    ])
                  : _c("h3", [_vm._v("Add a new Room:")]),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "row" },
                  _vm._l(_vm.schema.form, function(formItem, ele) {
                    var _obj
                    return _c(
                      "div",
                      {
                        staticClass: "float-left",
                        class:
                          ((_obj = {}),
                          (_obj[
                            formItem.container ? [formItem.container] : "col-12"
                          ] = true),
                          _obj)
                      },
                      [
                        _c(
                          "div",
                          { staticClass: "form-group-list" },
                          [
                            formItem.type == "boolean"
                              ? _c("form-boolean", {
                                  attrs: {
                                    declared: _vm.item[ele],
                                    schema: formItem
                                  },
                                  on: {
                                    input: function(upd) {
                                      _vm.item[ele] = upd
                                    }
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "text"
                              ? _c("form-text", {
                                  attrs: { schema: formItem },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "number"
                              ? _c("form-number", {
                                  attrs: { schema: formItem },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "textarea"
                              ? _c("form-textarea", {
                                  attrs: {
                                    schema: formItem,
                                    rows: formItem.rows || 3
                                  },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "select"
                              ? _c("form-select", {
                                  attrs: {
                                    schema: formItem,
                                    scopeData: _vm.item.country
                                  },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "simpleselect"
                              ? _c("form-simpleselect", {
                                  attrs: {
                                    schema: formItem,
                                    isDisabled:
                                      _vm.item.id && ele == "type"
                                        ? true
                                        : false
                                  },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "multiselect"
                              ? _c("form-multiselect", {
                                  attrs: { schema: formItem },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "datetime"
                              ? _c("form-datetime", {
                                  attrs: { schema: formItem },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "image"
                              ? _c("form-file", {
                                  attrs: {
                                    schema: formItem,
                                    type: "image",
                                    resource: _vm.model.toLowerCase(),
                                    view: formItem.view || "profile"
                                  },
                                  on: {
                                    input: function(upd) {
                                      _vm.item[ele] = upd
                                    }
                                  },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e(),
                            _vm._v(" "),
                            formItem.type == "simpleobject"
                              ? _c("form-simpleobject", {
                                  attrs: {
                                    formData: _vm.item,
                                    schema: formItem
                                  },
                                  model: {
                                    value: _vm.item[ele],
                                    callback: function($$v) {
                                      _vm.$set(_vm.item, ele, $$v)
                                    },
                                    expression: "item[ele]"
                                  }
                                })
                              : _vm._e()
                          ],
                          1
                        )
                      ]
                    )
                  }),
                  0
                ),
                _vm._v(" "),
                _c("div", { staticClass: "col-12 clearfix mt-3" }, [
                  _c(
                    "div",
                    { staticClass: "drsection-content" },
                    [
                      _c("auto-save", {
                        attrs: { type: "save", state: _vm.itemState },
                        on: {
                          autoSave: function($event) {
                            return _vm.autoSave(true)
                          }
                        }
                      }),
                      _vm._v(" "),
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
                    ],
                    1
                  )
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

/***/ "./resources/js/components/views/warehouse/room/editForm.vue":
/*!*******************************************************************!*\
  !*** ./resources/js/components/views/warehouse/room/editForm.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editForm.vue?vue&type=template&id=4af798fa& */ "./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa&");
/* harmony import */ var _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editForm.vue?vue&type=script&lang=js& */ "./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/warehouse/room/editForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa&":
/*!**************************************************************************************************!*\
  !*** ./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=template&id=4af798fa& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/warehouse/room/editForm.vue?vue&type=template&id=4af798fa&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_4af798fa___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Room.js":
/*!*************************************!*\
  !*** ./resources/js/models/Room.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Room; });
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



var Room =
/*#__PURE__*/
function (_Model) {
  _inherits(Room, _Model);

  function Room() {
    _classCallCheck(this, Room);

    return _possibleConstructorReturn(this, _getPrototypeOf(Room).apply(this, arguments));
  }

  _createClass(Room, [{
    key: "resource",
    value: function resource() {
      return 'admin/grow/rooms';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Room;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);