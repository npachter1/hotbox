(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[14],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Package__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Package */ "./resources/js/models/Package.js");


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

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
      type: Number,
      "default": null
    },
    "package": {
      type: Object,
      "default": null
    },
    model: {
      type: String,
      "default": 'Package'
    },
    module: {
      type: String,
      "default": 'outgoing'
    },
    adjustment_reasons: {
      type: Array,
      "default": null
    }
  },
  data: function data() {
    return {
      item: null,
      isLoading: false,
      isDownloading: false,
      isMedical: false,
      newItem: {
        package_id: this.id,
        package_uom: 'Grams',
        package_date: new Date(),
        patient_license_number: null,
        product_requires_remediation: 0,
        item_sections: [],
        item_sections_count: 1 // number of package sections

      }
    };
  },
  watch: {
    "package": function _package() {
      this.initializeSection(0);
    }
  },
  mounted: function () {
    var _mounted = _asyncToGenerator(
    /*#__PURE__*/
    _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
      var _this = this;

      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              _context.next = 2;
              return this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase());

            case 2:
              // this is a sub resource - it loads its own schema upon modal load
              this.isLoading = true;
              this.initializeSection(0);

              if (this.id) {
                axios.get('/api/v1/' + this.schema.meta.resource + '/' + this.id).then(function (response) {
                  _this.item = response.data;
                  _this.isLoading = false;
                  axios.get('/api/v1/admin/auth/location/ismedical/' + _this.item.location_id).then(function (response2) {
                    _this.isMedical = response2.data;
                  });
                })["catch"](function (error) {
                  _this.isLoading = false;

                  _this.$announcer({
                    status: 400,
                    data: {
                      message: 'We had a hiccup fetching the data - Please try again later.'
                    }
                  });

                  _this.$emit('refresh', {}, 'log'); // will close this modal

                });
              } else {
                this.isLoading = false;
                this.$announcer({
                  status: 422,
                  data: {
                    message: 'Whoops - couldnt find the associated record - Please try again later.'
                  }
                });
                this.$emit('refresh', {}, 'log');
              }

            case 5:
            case "end":
              return _context.stop();
          }
        }
      }, _callee, this);
    }));

    function mounted() {
      return _mounted.apply(this, arguments);
    }

    return mounted;
  }(),
  methods: {
    submit: function submit(e) {
      var _this2 = this;

      this.$validator.validateAll().then(function (result) {
        if (result) {
          _this2.$swal.fire({
            title: 'Are you sure?',
            text: 'This will create packages from package ' + _this2.item.label,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
          }).then(function (result) {
            if (result.value) {
              _this2.isProcessing = true;
              axios.post('/api/v1/' + _this2.schema.meta.resource + '/createPackages', _this2.newItem).then(function (response) {
                _this2.$announcer({
                  status: 200,
                  data: {
                    message: 'Success'
                  }
                });

                if (response.schema) _this2.$store.commit(_this2.module + '/setSchema', {
                  data: response.schema,
                  key: _this2.model.toLowerCase() + 'Schema'
                });

                _this2.$router.push({
                  name: _this2.model.toLowerCase()
                });
              })["catch"](function (error) {
                _this2.isProcessing = false;

                _this2.$announcer(error.response);
              });
            } else {//
            }
          });
        }
      });
    },
    // isMedical() {
    //     return this.item.location.settings.is_medical;
    // },
    // add new Item section to form
    initializeSection: function initializeSection(n) {
      this.newItem.item_sections[n] = {};
      Vue.set(this.newItem.item_sections[n], 'package_count', 1);
      Vue.set(this.newItem.item_sections[n], 'package_weights', null);
      Vue.set(this.newItem.item_sections[n], 'product_weight', null);
      Vue.set(this.newItem.item_sections[n], 'product_count', null);
      Vue.set(this.newItem.item_sections[n], 'item_id', null);
      Vue.set(this.newItem.item_sections[n], 'labels', []);
      this.newItem.item_sections_count = n + 1;
    },
    // set the Package Count for an Item section
    setCount: function setCount(count, n) {
      Vue.set(this.newItem.item_sections[n], 'package_count', count.srcElement.value);
      this.$forceUpdate();
    },
    // set the Weight per Package for an Item section
    setWeight: function setWeight(weight, n) {
      Vue.set(this.newItem.item_sections[n], 'package_weights', weight.srcElement.value);
      var product_count = this.newItem.item_sections[n].product_count;
      var product_weight = this.newItem.item_sections[n].product_weight;
      var package_weights = this.newItem.item_sections[n].package_weights;

      if (product_count > 0 && product_count * product_weight !== weight.srcElement.value) {
        product_weight = weight.srcElement.value / product_count;
        Vue.set(this.newItem.item_sections[n], 'product_weight', product_weight);
      } else if (product_weight && !product_count) {
        Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(package_weights / product_weight));
        Vue.set(this.newItem.item_sections[n], 'package_weights', product_weight * Math.floor(package_weights / product_weight));
      }

      this.$forceUpdate();
    },
    // set the Products per Package for an Item section
    setProductCount: function setProductCount(count, n) {
      Vue.set(this.newItem.item_sections[n], 'product_count', count.srcElement.value);
      var product_count = this.newItem.item_sections[n].product_count;
      var product_weight = this.newItem.item_sections[n].product_weight;
      var package_weights = this.newItem.item_sections[n].package_weights;
      if (product_count && product_weight) Vue.set(this.newItem.item_sections[n], 'package_weights', product_count * product_weight);else if (product_count && package_weights) Vue.set(this.newItem.item_sections[n], 'product_weight', package_weights / product_count);
      this.$forceUpdate();
    },
    // set the Weight per Product for an Item section
    setProductWeight: function setProductWeight(weight, n) {
      Vue.set(this.newItem.item_sections[n], 'product_weight', weight.srcElement.value);
      var product_count = this.newItem.item_sections[n].product_count;
      var product_weight = this.newItem.item_sections[n].product_weight;
      var package_weights = this.newItem.item_sections[n].package_weights;
      if (product_count && product_weight) Vue.set(this.newItem.item_sections[n], 'package_weights', product_count * product_weight);else if (product_weight && package_weights) {
        Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(package_weights / product_weight));
        Vue.set(this.newItem.item_sections[n], 'package_weights', product_weight * Math.floor(package_weights / product_weight));
      }
      this.$forceUpdate();
    },
    remainingWeight: function remainingWeight() {
      // todo: convert $totalWeight to package_uom
      var totalWeight = this.item.quantity;
      var usedWeight = 0;
      this.newItem.item_sections.forEach(function (item) {
        usedWeight += item.package_weights * item.package_count;
      });
      return totalWeight - usedWeight;
    },
    // calculate maximum Weight per Package based on Package Count and remaining weight
    getMaxWeight: function getMaxWeight(n) {
      var product_count = this.newItem.item_sections[n].product_count;
      var product_weight = this.newItem.item_sections[n].product_weight;
      var package_count = this.newItem.item_sections[n].package_count;
      var package_weights = this.newItem.item_sections[n].package_weights;

      if (package_count > 0) {
        var remainingWeight = this.remainingWeight() + package_weights * package_count;
        var maxWeight = remainingWeight / this.newItem.item_sections[n].package_count;
        Vue.set(this.newItem.item_sections[n], 'package_weights', maxWeight);
        if (product_count && !product_weight) Vue.set(this.newItem.item_sections[n], 'product_weight', maxWeight / product_count);else if (product_weight && !product_count) {
          Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(maxWeight / product_weight));
          Vue.set(this.newItem.item_sections[n], 'package_weights', Math.floor(maxWeight / product_weight) * product_weight);
        } else if (product_weight && product_count && product_weight * product_count !== maxWeight) {
          Vue.set(this.newItem.item_sections[n], 'product_count', Math.floor(maxWeight / product_weight));
          Vue.set(this.newItem.item_sections[n], 'package_weights', Math.floor(maxWeight / product_weight) * product_weight);
        }
      }

      this.$forceUpdate();
    },
    // calculate maximum Package Count based on Weight per Package and remaining weight
    getMaxPackageCount: function getMaxPackageCount(n) {
      var package_count = this.newItem.item_sections[n].package_count;
      var package_weights = this.newItem.item_sections[n].package_weights;
      var remainingWeight = this.remainingWeight() + package_weights * package_count;
      var maxCount = Math.floor(remainingWeight / package_weights);
      Vue.set(this.newItem.item_sections[n], 'package_count', maxCount);
      this.$forceUpdate();
    },
    setTag: function setTag(tag, n, i) {
      Vue.set(this.newItem.item_sections[n].labels, i, tag.srcElement.value);
    },
    removeSection: function removeSection(n) {
      this.newItem.item_sections.splice(n, 1);
      this.newItem.item_sections_count -= 1;
      this.$forceUpdate();
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&":
/*!*******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& ***!
  \*******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.vdp-datepicker__calendar {\n    left: 0px;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad&":
/*!****************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad& ***!
  \****************************************************************************************************************************************************************************************************************************************************/
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
            staticClass: "modal-form",
            attrs: { id: "createpackages" },
            on: {
              submit: function($event) {
                $event.preventDefault()
                return _vm.submit($event)
              }
            }
          },
          [
            _c("fieldset", [
              _c("h3", { staticClass: "mb-4" }, [_vm._v("Create Packages")]),
              _vm._v(" "),
              _vm.item
                ? _c("div", { staticClass: "form-row" }, [
                    _c("div", { staticClass: "col-3" }, [
                      _c("label", [_vm._v("Source Package")]),
                      _vm._v(" "),
                      _c("div", { staticClass: "form-group" }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.item.label) +
                            "\n                "
                        )
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "col-3" }, [
                      _c("label", [_vm._v("Product")]),
                      _vm._v(" "),
                      _c("div", { staticClass: "form-group" }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.item.item.name) +
                            "\n                "
                        )
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "col-3" }, [
                      _c("label", [_vm._v("Total Weight")]),
                      _vm._v(" "),
                      _c("div", { staticClass: "form-group" }, [
                        _vm._v(
                          "\n                    " +
                            _vm._s(_vm.item.quantity) +
                            " " +
                            _vm._s(_vm.item.unit_of_measure) +
                            "\n                "
                        )
                      ])
                    ]),
                    _vm._v(" "),
                    _c("div", { staticClass: "col-3" }, [
                      _c("label", [_vm._v("Remaining Weight")]),
                      _vm._v(" "),
                      _c(
                        "div",
                        {
                          staticClass: "form-group",
                          staticStyle: { color: "red" }
                        },
                        [
                          _vm._v(
                            "\n                    " +
                              _vm._s(_vm.remainingWeight()) +
                              " " +
                              _vm._s(
                                _vm.newItem.package_uom
                                  ? _vm.newItem.package_uom
                                  : ""
                              ) +
                              "\n                "
                          )
                        ]
                      )
                    ])
                  ])
                : _vm._e(),
              _vm._v(" "),
              _c(
                "div",
                { staticClass: "form-row" },
                [
                  _c("div", { staticClass: "col-6" }, [
                    _c("label", [_vm._v("Date")]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "form-group" },
                      [
                        _c("datepicker", {
                          directives: [
                            {
                              name: "validate",
                              rawName: "v-validate",
                              value: "required",
                              expression: "'required'"
                            }
                          ],
                          attrs: {
                            id: "item-date",
                            name: "date",
                            format: "MM/dd/yyyy",
                            "bootstrap-styling": true,
                            "input-class": "form-datepicker"
                          },
                          model: {
                            value: _vm.newItem.package_date,
                            callback: function($$v) {
                              _vm.$set(_vm.newItem, "package_date", $$v)
                            },
                            expression: "newItem.package_date"
                          }
                        })
                      ],
                      1
                    )
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-6" }, [
                    _c("label", [_vm._v("Unit of Measure")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "form-group" }, [
                      _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.newItem.package_uom,
                              expression: "newItem.package_uom"
                            }
                          ],
                          staticClass: "form-control",
                          attrs: { name: "package_uom" },
                          on: {
                            change: function($event) {
                              var $$selectedVal = Array.prototype.filter
                                .call($event.target.options, function(o) {
                                  return o.selected
                                })
                                .map(function(o) {
                                  var val = "_value" in o ? o._value : o.value
                                  return val
                                })
                              _vm.$set(
                                _vm.newItem,
                                "package_uom",
                                $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              )
                            }
                          }
                        },
                        [
                          _c("option", { attrs: { value: "Milligrams" } }, [
                            _vm._v("Milligrams")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "Grams" } }, [
                            _vm._v("Grams")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "Ounces" } }, [
                            _vm._v("Ounces")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "Kilograms" } }, [
                            _vm._v("Kilograms")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "Pounds" } }, [
                            _vm._v("Pounds")
                          ])
                        ]
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _vm.isMedical
                    ? _c("div", { staticClass: "col-6" }, [
                        _c(
                          "label",
                          { attrs: { for: "item-patient_license_number" } },
                          [_vm._v("Patient License Number")]
                        ),
                        _vm._v(" "),
                        _c("div", { staticClass: "form-group" }, [
                          _c("input", {
                            directives: [
                              {
                                name: "model",
                                rawName: "v-model",
                                value: _vm.newItem.patient_license_number,
                                expression: "newItem.patient_license_number"
                              },
                              { name: "validate", rawName: "v-validate" }
                            ],
                            ref: "patient_license_numberInput",
                            staticClass: "form-control",
                            class: {
                              input: true,
                              "text-danger": _vm.errors.has(
                                "patient_license_number"
                              )
                            },
                            attrs: {
                              id: "item-patient_license_number",
                              "aria-describedby": "addon-right addon-left",
                              name: "patient_license_number",
                              type: "text",
                              placeholder: ""
                            },
                            domProps: {
                              value: _vm.newItem.patient_license_number
                            },
                            on: {
                              input: function($event) {
                                if ($event.target.composing) {
                                  return
                                }
                                _vm.$set(
                                  _vm.newItem,
                                  "patient_license_number",
                                  $event.target.value
                                )
                              }
                            }
                          }),
                          _vm._v(" "),
                          _c(
                            "span",
                            {
                              directives: [
                                {
                                  name: "show",
                                  rawName: "v-show",
                                  value: _vm.errors.has(
                                    "patient_license_number"
                                  ),
                                  expression:
                                    "errors.has('patient_license_number')"
                                }
                              ],
                              staticClass: "form-text text-muted text-danger"
                            },
                            [
                              _vm._v(
                                _vm._s(
                                  _vm.errors.first("patient_license_number")
                                )
                              )
                            ]
                          )
                        ])
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _c("div", { staticClass: "col-6" }, [
                    _c("label", [_vm._v("Requires Remediation")]),
                    _vm._v(" "),
                    _c("div", { staticClass: "form-group" }, [
                      _c(
                        "select",
                        {
                          directives: [
                            {
                              name: "model",
                              rawName: "v-model",
                              value: _vm.newItem.product_requires_remediation,
                              expression: "newItem.product_requires_remediation"
                            }
                          ],
                          staticClass: "form-control",
                          attrs: { name: "is_production_batch" },
                          on: {
                            change: function($event) {
                              var $$selectedVal = Array.prototype.filter
                                .call($event.target.options, function(o) {
                                  return o.selected
                                })
                                .map(function(o) {
                                  var val = "_value" in o ? o._value : o.value
                                  return val
                                })
                              _vm.$set(
                                _vm.newItem,
                                "product_requires_remediation",
                                $event.target.multiple
                                  ? $$selectedVal
                                  : $$selectedVal[0]
                              )
                            }
                          }
                        },
                        [
                          _c("option", { attrs: { value: "1" } }, [
                            _vm._v("True")
                          ]),
                          _vm._v(" "),
                          _c("option", { attrs: { value: "0" } }, [
                            _vm._v("False")
                          ])
                        ]
                      )
                    ])
                  ]),
                  _vm._v(" "),
                  _vm._m(0),
                  _vm._v(" "),
                  _vm._l(_vm.newItem.item_sections_count, function(n) {
                    return [
                      _c("div", { staticClass: "col-4" }, [
                        _c("label", { attrs: { for: "item-item_id" } }, [
                          _vm._v("Product")
                        ]),
                        _vm._v(" "),
                        _vm.newItem.item_sections[n - 1]
                          ? _c("div", { staticClass: "form-group" }, [
                              _c(
                                "select",
                                {
                                  directives: [
                                    {
                                      name: "model",
                                      rawName: "v-model",
                                      value:
                                        _vm.newItem.item_sections[n - 1]
                                          .item_id,
                                      expression:
                                        "newItem.item_sections[n-1].item_id"
                                    },
                                    {
                                      name: "validate",
                                      rawName: "v-validate",
                                      value: "required",
                                      expression: "'required'"
                                    }
                                  ],
                                  staticClass: "form-control",
                                  on: {
                                    change: function($event) {
                                      var $$selectedVal = Array.prototype.filter
                                        .call($event.target.options, function(
                                          o
                                        ) {
                                          return o.selected
                                        })
                                        .map(function(o) {
                                          var val =
                                            "_value" in o ? o._value : o.value
                                          return val
                                        })
                                      _vm.$set(
                                        _vm.newItem.item_sections[n - 1],
                                        "item_id",
                                        $event.target.multiple
                                          ? $$selectedVal
                                          : $$selectedVal[0]
                                      )
                                    }
                                  }
                                },
                                _vm._l(
                                  _vm.schema.form.product_id.values,
                                  function(item) {
                                    return _c(
                                      "option",
                                      { domProps: { value: item.id } },
                                      [_vm._v(_vm._s(item.name))]
                                    )
                                  }
                                ),
                                0
                              )
                            ])
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "col-2" }, [
                        _c("label", [_vm._v("Weight / Product")]),
                        _vm._v(" "),
                        _vm.newItem.item_sections[n - 1]
                          ? _c("div", { staticClass: "form-group" }, [
                              _c("input", {
                                directives: [
                                  {
                                    name: "model",
                                    rawName: "v-model",
                                    value:
                                      _vm.newItem.item_sections[n - 1]
                                        .product_weight,
                                    expression:
                                      "newItem.item_sections[n-1].product_weight"
                                  }
                                ],
                                staticClass: "form-control",
                                attrs: { type: "text", name: "product_weight" },
                                domProps: {
                                  value:
                                    _vm.newItem.item_sections[n - 1]
                                      .product_weight
                                },
                                on: {
                                  change: function($event) {
                                    _vm.setProductWeight($event, n - 1)
                                  },
                                  input: function($event) {
                                    if ($event.target.composing) {
                                      return
                                    }
                                    _vm.$set(
                                      _vm.newItem.item_sections[n - 1],
                                      "product_weight",
                                      $event.target.value
                                    )
                                  }
                                }
                              })
                            ])
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "col-2" }, [
                        _c("label", [_vm._v("Products / Package")]),
                        _vm._v(" "),
                        _vm.newItem.item_sections[n - 1]
                          ? _c("div", { staticClass: "form-group" }, [
                              _c("input", {
                                directives: [
                                  {
                                    name: "model",
                                    rawName: "v-model",
                                    value:
                                      _vm.newItem.item_sections[n - 1]
                                        .product_count,
                                    expression:
                                      "newItem.item_sections[n-1].product_count"
                                  }
                                ],
                                staticClass: "form-control",
                                attrs: { type: "text", name: "product_count" },
                                domProps: {
                                  value:
                                    _vm.newItem.item_sections[n - 1]
                                      .product_count
                                },
                                on: {
                                  change: function($event) {
                                    _vm.setProductCount($event, n - 1)
                                  },
                                  input: function($event) {
                                    if ($event.target.composing) {
                                      return
                                    }
                                    _vm.$set(
                                      _vm.newItem.item_sections[n - 1],
                                      "product_count",
                                      $event.target.value
                                    )
                                  }
                                }
                              })
                            ])
                          : _vm._e()
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "col-2" }, [
                        _c("label", [_vm._v("Weight / Package")]),
                        _vm._v(" "),
                        _vm.newItem.item_sections[n - 1]
                          ? _c(
                              "div",
                              {
                                staticClass: "form-group",
                                staticStyle: { "margin-bottom": "0" }
                              },
                              [
                                _c("input", {
                                  directives: [
                                    {
                                      name: "model",
                                      rawName: "v-model",
                                      value:
                                        _vm.newItem.item_sections[n - 1]
                                          .package_weights,
                                      expression:
                                        "newItem.item_sections[n-1].package_weights"
                                    }
                                  ],
                                  staticClass: "form-control",
                                  attrs: {
                                    type: "text",
                                    name: "package_weight"
                                  },
                                  domProps: {
                                    value:
                                      _vm.newItem.item_sections[n - 1]
                                        .package_weights
                                  },
                                  on: {
                                    change: function($event) {
                                      _vm.setWeight($event, n - 1)
                                    },
                                    input: function($event) {
                                      if ($event.target.composing) {
                                        return
                                      }
                                      _vm.$set(
                                        _vm.newItem.item_sections[n - 1],
                                        "package_weights",
                                        $event.target.value
                                      )
                                    }
                                  }
                                })
                              ]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "a",
                          {
                            staticStyle: {
                              color: "red",
                              "font-size": "smaller",
                              "margin-left": "5px",
                              cursor: "pointer"
                            },
                            on: {
                              click: function($event) {
                                return _vm.getMaxWeight(n - 1)
                              }
                            }
                          },
                          [_vm._v("(Max)")]
                        )
                      ]),
                      _vm._v(" "),
                      _c("div", { staticClass: "col-2" }, [
                        _c("label", [_vm._v("Package Count")]),
                        _vm._v(" "),
                        _vm.newItem.item_sections[n - 1]
                          ? _c(
                              "div",
                              {
                                staticClass: "form-group",
                                staticStyle: { "margin-bottom": "0" }
                              },
                              [
                                _c("input", {
                                  directives: [
                                    {
                                      name: "model",
                                      rawName: "v-model",
                                      value:
                                        _vm.newItem.item_sections[n - 1]
                                          .package_count,
                                      expression:
                                        "newItem.item_sections[n-1].package_count"
                                    }
                                  ],
                                  staticClass: "form-control",
                                  attrs: {
                                    type: "text",
                                    name: "package_count"
                                  },
                                  domProps: {
                                    value:
                                      _vm.newItem.item_sections[n - 1]
                                        .package_count
                                  },
                                  on: {
                                    change: function($event) {
                                      _vm.setCount($event, n - 1)
                                    },
                                    input: function($event) {
                                      if ($event.target.composing) {
                                        return
                                      }
                                      _vm.$set(
                                        _vm.newItem.item_sections[n - 1],
                                        "package_count",
                                        $event.target.value
                                      )
                                    }
                                  }
                                })
                              ]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "a",
                          {
                            staticStyle: {
                              color: "red",
                              "font-size": "smaller",
                              "margin-left": "5px",
                              cursor: "pointer"
                            },
                            on: {
                              click: function($event) {
                                return _vm.getMaxPackageCount(n - 1)
                              }
                            }
                          },
                          [_vm._v("(Max)")]
                        )
                      ]),
                      _vm._v(" "),
                      _vm.newItem.item_sections[n - 1]
                        ? _c(
                            "div",
                            { staticClass: "col-6" },
                            [
                              _c("label", [_vm._v("Package Tag # (s)")]),
                              _vm._v(" "),
                              _vm._l(
                                _vm.newItem.item_sections[n - 1].package_count *
                                  1,
                                function(i) {
                                  return _c(
                                    "div",
                                    { staticClass: "form-group" },
                                    [
                                      _c("input", {
                                        staticClass: "form-control",
                                        attrs: { type: "text" },
                                        on: {
                                          change: function($event) {
                                            return _vm.setTag(
                                              $event,
                                              n - 1,
                                              i - 1
                                            )
                                          }
                                        }
                                      })
                                    ]
                                  )
                                }
                              )
                            ],
                            2
                          )
                        : _vm._e(),
                      _vm._v(" "),
                      _c(
                        "div",
                        {
                          staticClass: "col-12",
                          staticStyle: { "text-align": "right" }
                        },
                        [
                          _c("br"),
                          _vm._v(" "),
                          _c(
                            "a",
                            {
                              staticStyle: {
                                cursor: "pointer",
                                "margin-right": "5px"
                              },
                              on: {
                                click: function($event) {
                                  return _vm.removeSection(n - 1)
                                }
                              }
                            },
                            [
                              _c("i", {
                                staticClass:
                                  "hotbox-icon hotbox-icon-trash-round show-red"
                              })
                            ]
                          ),
                          _vm._v(" "),
                          _c("hr")
                        ]
                      )
                    ]
                  }),
                  _vm._v(" "),
                  _c("br"),
                  _vm._v(" "),
                  _c(
                    "div",
                    {
                      staticClass: "col-12",
                      staticStyle: { "text-align": "right" }
                    },
                    [
                      _c(
                        "a",
                        {
                          staticStyle: {
                            cursor: "pointer",
                            "margin-right": "5px"
                          },
                          on: {
                            click: function($event) {
                              return _vm.initializeSection(
                                _vm.newItem.item_sections_count
                              )
                            }
                          }
                        },
                        [
                          _c("i", {
                            staticClass: "hotbox-icon hotbox-icon-c-add"
                          })
                        ]
                      )
                    ]
                  ),
                  _vm._v(" "),
                  _c("br"),
                  _c("br")
                ],
                2
              ),
              _vm._v(" "),
              _vm._m(1)
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
              type: "loadModal"
            }
          })
        ],
        1
      )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "col-12" }, [_c("hr")])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "col-12 clearfix mt-2 text-center" }, [
      _c("button", { staticClass: "btn btn-info", attrs: { type: "submit" } }, [
        _vm._v("Create Packages")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue":
/*!***************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./createMultiplePackagesForm.vue?vue&type=template&id=551b60ad& */ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad&");
/* harmony import */ var _createMultiplePackagesForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./createMultiplePackagesForm.vue?vue&type=script&lang=js& */ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _createMultiplePackagesForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__["render"],
  _createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createMultiplePackagesForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&":
/*!************************************************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& ***!
  \************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad&":
/*!**********************************************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad& ***!
  \**********************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createMultiplePackagesForm.vue?vue&type=template&id=551b60ad& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/package/createMultiplePackagesForm.vue?vue&type=template&id=551b60ad&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createMultiplePackagesForm_vue_vue_type_template_id_551b60ad___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Package.js":
/*!****************************************!*\
  !*** ./resources/js/models/Package.js ***!
  \****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Package; });
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



var Package =
/*#__PURE__*/
function (_Model) {
  _inherits(Package, _Model);

  function Package() {
    _classCallCheck(this, Package);

    return _possibleConstructorReturn(this, _getPrototypeOf(Package).apply(this, arguments));
  }

  _createClass(Package, [{
    key: "resource",
    value: function resource() {
      return 'admin/grow/packages';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Package;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);