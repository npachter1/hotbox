(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["js/view-auth"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); if (enumerableOnly) symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; }); keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; if (i % 2) { ownKeys(Object(source), true).forEach(function (key) { _defineProperty(target, key, source[key]); }); } else if (Object.getOwnPropertyDescriptors) { Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)); } else { ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    module: {
      type: String,
      "default": 'auth'
    },
    model: {
      type: String,
      "default": 'Addressbook'
    },
    schema: {
      type: Object,
      "default": function _default() {}
    },
    ids: {
      type: Array,
      "default": function _default() {}
    },
    focus: {
      type: String,
      "default": 'edit'
    }
  },
  data: function data() {
    return {
      tab: this.focus,
      batch: null,
      batch_ids: this.ids,
      batchSortBy: null,
      batchSortDesc: false,
      batchState: 'update',
      isLoading: false
    };
  },
  components: {},
  mounted: function mounted() {
    this.getBatchData();
  },
  methods: {
    getBatchData: function getBatchData() {
      var _this = this;

      this.isLoading = true;
      axios.get('/api/v1/' + this.schema.meta.resource + '/batch?batch_ids=' + this.batch_ids.join(',')).then(function (response) {
        _this.batch = response.data;
        _this.isLoading = false;
      })["catch"](function (error) {
        _this.isLoading = false;

        _this.$announcer(error.response);

        _this.$emit('refresh');
      });
    },
    saveBatchData: function saveBatchData(typ) {
      var _this2 = this;

      this.$validator.validateAll().then(function (result) {
        if (result) {
          _this2.batchState = 'saving..';
          var up = null; // reduce properties of batch data that arent batchable - need not send more then 1k params for large batches

          if (typ == 'data') up = _this2.batch.map(function (r) {
            return Object.keys(r).reduce(function (o, k) {
              return Object.keys(_this2.batchFields).indexOf(k) !== -1 || k == 'id' ? _objectSpread({}, o, _defineProperty({}, k, r[k])) : o;
            }, {});
          });else up = _this2.batch_ids;
          axios.post('/api/v1/' + _this2.schema.meta.resource + '/batch/' + _this2.tab, {
            batch: up
          }).then(function (response) {
            if (response.data.schema) _this2.$store.commit(_this2.module + '/setSchema', {
              data: response.data.schema,
              key: _this2.model.toLowerCase() + 'Schema'
            }); // update schems, will update grid

            _this2.batchState = 'saved';

            _this2.$announcer(response);

            _this2.$emit('refresh');
          })["catch"](function (error) {
            _this2.batchState = '(re)update';

            _this2.$announcer(error.response);
          });
        } else _this2.$announcer({
          status: 422,
          data: {
            message: 'Whoops, Please check and correct inputs in order to continue.'
          }
        });
      });
    },
    batchSort: function batchSort(field) {
      this.batchSortBy = field;
      this.batchSortDesc = !this.batchSortDesc;
      this.sorter(this.batch, this.batchSortDesc, this.batchSortBy); // resort batch
    },
    batchOverride: function batchOverride(field, val) {
      this.batch = this.batch.map(function (v) {
        v[field] = val;
        return v;
      });
    },
    toggleBatchId: function toggleBatchId(val, e) {
      if (e.target.checked) {
        if (this.batch_ids.indexOf(val) === -1) this.batch_ids.push(val);
      } else this.batch_ids.splice(this.batch_ids.indexOf(val), 1);
    }
  },
  computed: {
    batchFields: function batchFields() {
      var _this3 = this;

      return Object.keys(this.schema.form).filter(function (k) {
        return _this3.schema.form[k].batchable === true;
      }).reduce(function (obj, key) {
        obj[key] = _this3.schema.form[key];
        obj[key].override = null;
        return obj;
      }, {});
    }
  },
  watch: {
    batch: {
      handler: function handler(newVal, oldVal) {
        this.batchState = oldVal ? 'update changes' : 'update';
      },
      deep: true
    },
    batch_ids: function batch_ids() {
      this.$emit('update:ids', this.batch_ids); // update ids list past to this component
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_Addressbook__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/Addressbook */ "./resources/js/models/Addressbook.js");
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


/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    id: {
      type: [Number, String],
      "default": 0
    },
    model: {
      type: String,
      "default": 'Addressbook'
    },
    module: {
      type: String,
      "default": 'auth'
    }
  },
  data: function data() {
    return {
      item: null,
      itemState: 'save'
    };
  },
  components: {},
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;

    if (this.id) {
      _models_Addressbook__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.id).then(function (response) {
        _this.item = new _models_Addressbook__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this.schema);
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
      this.item = new _models_Addressbook__WEBPACK_IMPORTED_MODULE_0__["default"]().withDefaults(this.schema);
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

          _this3.$router.push({
            name: _this3.model.toLowerCase()
          });
        }

        _this3.itemState = 'saved';
      })["catch"](function (error) {
        _this3.$announcer(error.response);

        _this3.itemState = 'resave';
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Addressbook__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Addressbook */ "./resources/js/models/Addressbook.js");
/* harmony import */ var _batchEditModal__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./batchEditModal */ "./resources/js/components/views/auth/addressbook/batchEditModal.vue");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);


function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

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



/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Addressbook'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    filters: {
      // optional initial filters (filters.filter) object can be passed via this prop!
      type: Object,
      "default": function _default() {}
    }
  },
  data: function data() {
    return {
      isLoading: false,
      isDownloading: false,
      shouldReload: false,
      gridData: null,
      gridSearch: null,
      gridPage: 1,
      gridColumns: null,
      gridFilters: null,
      batchEditModal: false,
      batchEditFocus: 'edit',
      batchEditIds: []
    };
  },
  components: {
    BatchEditModal: _batchEditModal__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  mounted: function mounted() {
    //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
    this.gridSearch = this.$route.query.search || null;

    if (this.schema) {
      this.setFilters(this.$route.params.focus); // if we have schema, then set filters, else we watch schema load/change and then set.

      this.gridColumns = this.schema.meta.fields; // for some reason, the schema changing on edit doesnt register - need to reload upon mount
    }

    this.restoreGridSettings();
  },
  methods: {
    fetchGrid: function () {
      var _fetchGrid = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!(!this.schema || !this.gridFilters)) {
                  _context.next = 4;
                  break;
                }

                return _context.abrupt("return", false);

              case 4:
                if (!(this.isLoading == true)) {
                  _context.next = 6;
                  break;
                }

                return _context.abrupt("return", false);

              case 6:
                // do not fetch if we are already fetching
                this.$store.commit(this.module + '/setSearch', {
                  gridPage: null,
                  gridFilters: null,
                  options: {
                    merge: true
                  }
                }); // clear search setting for this page

                this.$store.commit(this.module + '/setSearch', {
                  gridPage: this.gridPage,
                  gridFilters: this.gridFilters,
                  options: {
                    merge: true
                  }
                }); // persist search setting

                this.isLoading = true;
                _context.next = 11;
                return new _models_Addressbook__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).params({
                  search: this.gridSearch
                }).orderBy((this.gridFilters.orderDesc ? '-' : '') + this.gridFilters.sortBy).limit(this.gridFilters.pageLimit).page(this.gridPage).get();

              case 11:
                this.gridData = _context.sent;
                this.mergeGridData();
                this.isLoading = false;

              case 14:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function fetchGrid() {
        return _fetchGrid.apply(this, arguments);
      }

      return fetchGrid;
    }(),
    searchGrid: lodash__WEBPACK_IMPORTED_MODULE_3___default.a.debounce(function (e) {
      // upon search filter update, throttle .5 sec grid and scope refresh
      this.gridPage = 1; //this.$store.commit(this.module+'/setSearch',this.gridSearch);   // persist search setting

      this.fetchGrid();
    }, 500),
    setFilters: function setFilters() {
      var _this = this;

      var focus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'all';
      if (!this.schema) return false;
      this.gridFilters = {
        // (re)set the filters from schema (which fetchGrid will watch and run)
        pageLimit: 50,
        sortBy: Object.keys(this.schema.filters).find(function (key) {
          return _this.schema.filters[key].type === 'daterange';
        }) || 'updated_at',
        // use first daterange field(key) in schema
        orderDesc: false,
        filter: Object.assign.apply(Object, [{}].concat(_toConsumableArray(Object.keys(this.schema.filters).map(function (k) {
          return _defineProperty({}, k, _this.schema.filters[k].values.map(function (v) {
            return v.id;
          }));
        })), [this.filters])) //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:['all']}; }),this.filters)

      };
    },
    renderRowBg: function renderRowBg(item, type) {
      if (!item) return null;
      return this.inSet(item.id, this.batchEditIds) ? 'table-success' : null;
    },
    toggleBatchAll: function toggleBatchAll(e) {
      if (e.target.checked) this.batchEditIds = this.gridData.data.map(function (v) {
        return v.id;
      });else this.batchEditIds = [];
    },
    toggleBatchId: function toggleBatchId(val, e) {
      if (e.target.checked) {
        if (this.batchEditIds.indexOf(val) === -1) this.batchEditIds.push(val);
      } else this.batchEditIds.splice(this.batchEditIds.indexOf(val), 1);
    },
    confirmDelete: function confirmDelete(id) {
      var _this2 = this;

      this.$swal.fire({
        title: 'Are you sure?',
        text: 'This will Delete this ' + this.model + ' row.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete row'
      }).then(function (result) {
        if (result.value) {
          _this2.isLoading = true;
          new _models_Addressbook__WEBPACK_IMPORTED_MODULE_1__["default"]({
            id: id
          })["delete"]().then(function (response) {
            _this2.isLoading = false;

            _this2.$store.dispatch(_this2.module + '/setSchemas', _this2.model.toLowerCase()); // get schema for new agg data


            _this2.$announcer(response);

            _this2.fetchGrid(); // if we deleted an item, then refetch..

          })["catch"](function (error) {
            _this2.isLoading = false;

            _this2.$announcer(error.response);
          });
        }
      });
    },
    downloadExport: function downloadExport(typ) {
      var _this3 = this;

      this.isDownloading = true;
      axios({
        url: new _models_Addressbook__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
        method: 'GET',
        responseType: 'blob' // important

      }).then(function (response) {
        _this3.isDownloading = false;

        _this3.downloadFile(response);
      })["catch"](function (error) {
        _this3.isDownloading = false;

        _this3.$announcer(error.response);
      });
    },
    refreshFromModal: function refreshFromModal(upd) {
      this.batchEditModal = !this.batchEditModal;
      this.shouldReload = true;
    },
    restoreGridSettings: function restoreGridSettings() {
      var stateSearch = this.$store.state[this.module].search;

      if (stateSearch) {
        if (stateSearch.gridSearch) this.gridSearch = stateSearch.gridSearch;
        if (stateSearch.gridPage) this.gridPage = stateSearch.gridPage;
        if (stateSearch.gridFilters) this.gridFilters = stateSearch.gridFilters;
        if (stateSearch.gridColumns) this.gridColumns = stateSearch.gridColumns;
      }
    },
    mergeGridData: function mergeGridData() {
      var _this4 = this;

      if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridData && Array.isArray(this.gridData.data)) {
        //merge any saved options into griddata
        this.$store.state[this.module].search.gridData.forEach(function (saved) {
          var row = _this4.gridData.data.findIndex(function (e) {
            return e.id === saved.id;
          });

          if (row > -1) _this4.$set(_this4.gridData.data[row], '_showDetails', true); //$set if the _showDetails isn't already there we need the reactivity
        });
      }
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    columnsVisible: function columnsVisible() {
      return this.gridColumns ? this.gridColumns.filter(function (col) {
        return col.toggle === true || !col.hasOwnProperty('toggle');
      }) : [];
    },
    isAllInBatch: function isAllInBatch() {
      if (!this.gridData) return false;else return this.batchEditIds.length == this.gridData.data.length ? true : false;
    }
  },
  watch: {
    gridData: {
      handler: function handler(newValue, oldValue) {
        var saveGridDataOptions = newValue.data.filter(function (e) {
          return e._showDetails;
        }).map(function (e) {
          return {
            id: e.id,
            _showDetails: e._showDetails
          };
        });
        this.$store.commit(this.module + '/setSearch', {
          gridData: saveGridDataOptions,
          options: {
            merge: true
          }
        }); // persist search setting
      },
      deep: true
    },
    gridColumns: {
      handler: function handler() {
        this.$store.commit(this.module + '/setSearch', {
          gridColumns: this.gridColumns,
          options: {
            merge: true
          }
        });
      },
      deep: true
    },
    gridFilters: {
      handler: function handler(to, from) {
        if (this.gridFilters) {
          if (from && !this.isLoading) this.gridPage = 1; // reset page to 1 if filters change

          this.fetchGrid();
        }
      },
      deep: true
    },
    gridPage: function gridPage(to, from) {
      if (this.gridFilters) this.fetchGrid();
    },
    gridSearch: function gridSearch() {
      this.$store.commit(this.module + '/setSearch', {
        gridSearch: this.gridSearch,
        options: {
          merge: true
        }
      });
    },
    schema: {
      handler: function handler(to, from) {
        if (!from && to) this.setFilters(); // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
        else if (!lodash__WEBPACK_IMPORTED_MODULE_3___default.a.isEqual(to.filters, from.filters)) this.setFilters(); // if a schema filter change - reset the filters - which will refresh the grid
          else if (this.shouldReload == true) {
              // or if we set a should reload flag, reload with refreshed schema
              this.fetchGrid();
              this.shouldReload = false;
            }
        if (to && !this.gridColumns) this.gridColumns = to.meta.fields; // load gridColumns if not already loaded.
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/index.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/index.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _locations_editModal__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./locations/editModal */ "./resources/js/components/views/auth/locations/editModal.vue");
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    module: {
      type: String,
      "default": 'auth'
    }
  },
  data: function data() {
    return {
      editModal: false,
      editRef: null
    };
  },
  components: {
    EditModal: _locations_editModal__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  mounted: function mounted() {},
  methods: {
    loadEditModal: function loadEditModal(id) {
      this.editRef = id;
      this.editModal = !this.editModal;
    }
  },
  computed: {
    section: function section() {
      return this.$store.state.sections[this.$store.state.disp.section];
    },
    isEditPage: function isEditPage() {
      return this.$route.name.indexOf('_') !== -1 && this.$route.name.indexOf('_index') === -1 ? true : false;
    },
    indexView: function indexView() {
      var root = this.isEditPage ? this.$route.name.substr(0, this.$route.name.indexOf('_')) : this.$route.name;
      return this.section ? this.section.views ? this.section.views.find(function (v) {
        return v.name == root;
      }) : null : null;
    }
  },
  watch: {
    $route: function $route(newRoute, oldRoute) {// route change in section
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Locations__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Locations */ "./resources/js/models/Locations.js");


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

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Locations'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    id: {
      type: [String, Number],
      "default": null
    },
    type: {
      type: String,
      "default": 'location'
    }
  },
  data: function data() {
    return {
      item: null,
      tab: this.type,
      itemState: 'update',
      isLoading: false,
      isDownloading: false,
      subSel: null,
      chargeSel: null,
      form: {
        update_status: null,
        update_message: null,
        update_subscription: null,
        update_subscription_plan: null,
        update_subscription_coupon: null,
        update_subscription_quantity: 0,
        update_subscription_trialdays: 0,
        update_subscription_action: 'update',
        update_transaction: null,
        update_transaction_amount: 0,
        update_transaction_descriptor: null,
        update_balance_amount: 0,
        update_balance_descriptor: null,
        update_user_ids: [],
        update_app_ids: []
      }
    };
  },
  components: {},
  mounted: function () {
    var _mounted = _asyncToGenerator(
    /*#__PURE__*/
    _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
      var _this = this;

      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              this.isLoading = true;
              _context.next = 3;
              return this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase());

            case 3:
              // since we are calling this from another app module, we need to load a fresh schema first.
              _models_Locations__WEBPACK_IMPORTED_MODULE_1__["default"].find(this.id).then(function (response) {
                _this.item = new _models_Locations__WEBPACK_IMPORTED_MODULE_1__["default"](response);
                _this.form.update_status = _this.item.status;
                _this.isLoading = false;
              })["catch"](function (error) {
                _this.$announcer({
                  status: 400,
                  data: {
                    message: 'We had a hiccup fetching the data - Please try again later.'
                  }
                });

                _this.$emit('refresh');
              });

            case 4:
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
    autoSave: function autoSave() {
      var _this2 = this;

      var typ = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'status';
      var act = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'update';
      this.$swal.fire({
        title: 'Please Confirm Modification',
        input: 'password',
        inputValue: null,
        inputAttributes: {
          maxlength: 4,
          autocapitalize: 'off',
          autocorrect: 'off'
        },
        inputPlaceholder: 'Input AdminPin',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Proceed.',
        inputValidator: function inputValidator(value) {
          return new Promise(function (resolve) {
            axios.post('/api/v1/admin/auth/pin', {
              location_id: _this2.item.id,
              pin: value
            }).then(function (response) {
              _this2.$announcer(response);

              resolve();
            })["catch"](function (error) {
              _this2.$announcer(error.response);

              resolve('The PIN you have entered is not valid.');
            });
          });
        }
      }).then(function (result) {
        if (result.value) {
          _this2.$validator.validateAll().then(function (result) {
            if (result) {
              _this2.itemState = 'saving..';
              _this2.form.update_subscription_action = act;
              axios.post('/api/v1/admin/auth/modify/' + _this2.item.id + '/' + typ, _this2.form).then(function (response) {
                _this2.itemState = 'saved';

                _this2.$store.dispatch(_this2.module + '/setSchemas', _this2.model.toLowerCase()); // since we are calling this from another app module, we need to load a fresh schema first.


                _this2.$emit('refresh');

                _this2.$announcer(response);
              })["catch"](function (error) {
                _this2.itemState = 'resave';

                _this2.$announcer(error.response);
              });
            } else _this2.$announcer({
              status: 422,
              data: {
                message: 'Whoops, Please check and correct inputs in order to continue.'
              }
            });
          });
        }
      });
    },
    toggleLink: function toggleLink(id) {
      var add = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var model = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'update_user_ids';

      if (add && this.form[model]) {
        if (this.form[model].indexOf(id) === -1) this.form[model].push(id);
      } else this.form[model].splice(this.form[model].indexOf(id), 1);
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    }
  },
  watch: {
    'form.update_subscription': function formUpdate_subscription(to, from) {
      var _this3 = this;

      // load subscription data upon set of step 1
      if (to && this.item) this.subSel = this.item.services.find(function (v) {
        return v.id == _this3.form.update_subscription;
      });

      if (this.subSel) {
        // then update subscription qty, coupon, and trial days if any
        this.form.update_subscription_plan = this.subSel.stripe_plan;
        this.form.update_subscription_quantity = this.subSel.quantity || 0;
        this.form.update_subscription_coupon = this.subSel.stripe_coupon;
        this.form.update_subscription_trialdays = this.subSel.trial_ends_at ? this.$moment.utc(this.subSel.trial_ends_at).diff(this.$moment.utc(), 'days') : null;
      }
    },
    'form.update_transaction': function formUpdate_transaction(to, from) {
      var _this4 = this;

      if (to && this.item) this.chargeSel = this.item.transactions.find(function (v) {
        return v.id == _this4.form.update_transaction;
      });
    },
    'item.users': function itemUsers(to, from) {
      if (to) this.form.update_user_ids = this.item.users.map(function (v) {
        return v.id;
      });
    },
    'item.apps': function itemApps(to, from) {
      if (to) this.form.update_app_ids = this.item.apps.map(function (v) {
        return v.id;
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _editModal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editModal */ "./resources/js/components/views/auth/locations/editModal.vue");
/* harmony import */ var _auth_settings_profile__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../auth/settings/profile */ "./resources/js/components/views/auth/settings/profile.vue");
/* harmony import */ var _models_Locations__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../models/Locations */ "./resources/js/models/Locations.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_4__);


function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

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
//
//
//
//
//
//




/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Locations'
    },
    module: {
      type: String,
      "default": 'auth'
    }
  },
  data: function data() {
    return {
      isLoading: false,
      isDownloading: false,
      gridData: null,
      gridSearch: null,
      gridPage: 1,
      gridColumns: null,
      gridFilters: null,
      editModal: false,
      editRef: 0,
      editTitle: 'Location Management',
      editType: 'location'
    };
  },
  components: {
    LocationProfile: _auth_settings_profile__WEBPACK_IMPORTED_MODULE_2__["default"],
    EditModal: _editModal__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  mounted: function mounted() {
    //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
    this.gridSearch = this.$route.query.search || null;

    if (this.schema) {
      this.setFilters(this.$route.params.focus); // if we have schema, then set filters, else we watch schema load/change and then set.

      this.gridColumns = this.schema.meta.fields; // for some reason, the schema changing on edit doesnt register - need to reload upon mount
    }
  },
  methods: {
    fetchGrid: function () {
      var _fetchGrid = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (this.schema) {
                  _context.next = 2;
                  break;
                }

                return _context.abrupt("return", false);

              case 2:
                // dont fetch grid without a schema, it will fetch when loaded
                this.isLoading = true;
                _context.next = 5;
                return new _models_Locations__WEBPACK_IMPORTED_MODULE_3__["default"]().setFilters(this.gridFilters.filter).params({
                  search: this.gridSearch
                }).orderBy((this.gridFilters.orderDesc ? '-' : '') + this.gridFilters.sortBy).limit(this.gridFilters.pageLimit).page(this.gridPage).get();

              case 5:
                this.gridData = _context.sent;
                this.isLoading = false;

              case 7:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function fetchGrid() {
        return _fetchGrid.apply(this, arguments);
      }

      return fetchGrid;
    }(),
    searchGrid: lodash__WEBPACK_IMPORTED_MODULE_4___default.a.debounce(function (e) {
      // upon search filter update, throttle .5 sec grid and scope refresh
      this.gridPage = 1;
      this.fetchGrid();
    }, 500),
    setFilters: function setFilters() {
      var _this = this;

      if (!this.schema) return false;
      this.gridFilters = {
        // (re)set the filters from schema (which fetchGrid will watch and run)
        pageLimit: 50,
        sortBy: Object.keys(this.schema.filters).find(function (key) {
          return _this.schema.filters[key].type === 'daterange';
        }) || 'updated_at',
        // use first daterange field(key) in schema
        orderDesc: false,
        filter: Object.assign.apply(Object, [{}].concat(_toConsumableArray(Object.keys(this.schema.filters).map(function (k) {
          return _defineProperty({}, k, _this.schema.filters[k].values.map(function (v) {
            return v.id;
          }));
        }))))
      };
    },
    rowColor: function rowColor(item, type) {
      if (!item) return null;else if (item.id == this.$store.state.user.location.id) return 'table-default';

      switch (item.status) {
        case 'registered':
        case 'held':
          return 'table-warning';

        case 'denied':
        case 'requested':
          return 'table-danger';

        case 'activated':
          return 'table-success';

        default:
          return null;
      }
    },
    confirmDelete: function confirmDelete(id) {
      var _this2 = this;

      this.$swal.fire({
        title: 'Are you sure?',
        text: 'This will Delete this ' + this.model + ' row.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete row'
      }).then(function (result) {
        if (result.value) {
          _this2.isLoading = true;
          new _models_Locations__WEBPACK_IMPORTED_MODULE_3__["default"]({
            id: id
          })["delete"]().then(function (response) {
            _this2.isLoading = false;

            _this2.$announcer(response);

            _this2.fetchGrid(); // if we deleted an item, then refetch..

          })["catch"](function (error) {
            _this2.isLoading = false;

            _this2.$announcer(error.response);
          });
        }
      });
    },
    downloadExport: function downloadExport(typ) {
      var _this3 = this;

      this.isDownloading = true;
      axios({
        url: new _models_Locations__WEBPACK_IMPORTED_MODULE_3__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
        method: 'GET',
        responseType: 'blob' // important

      }).then(function (response) {
        _this3.isDownloading = false;

        _this3.downloadFile(response);
      })["catch"](function (error) {
        _this3.isDownloading = false;

        _this3.$announcer(error.response);
      });
    },
    jumpToLocation: function jumpToLocation(sid) {
      var _this4 = this;

      axios.get('/api/v1/admin/auth/locations/change/' + sid).then(function (response) {
        _this4.$store.dispatch('resetAuthLocation', {
          scope: 'user,sections,agg',
          route: _this4.$route,
          reset: true
        });

        _this4.$router.push({
          name: 'location'
        });

        response.data.message = 'You are now logged into Location ' + sid;

        _this4.$announcer(response);
      })["catch"](function (error) {
        _this4.$announcer(error.response);
      });
    },
    loadEditModal: function loadEditModal(id, name) {
      this.editRef = id;
      this.editTitle = 'Manage ' + name + ' - Location ' + id + ' Account';
      this.editModal = !this.editModal;
    },
    refreshEditModal: function refreshEditModal() {
      this.fetchGrid();
      this.editModal = !this.editModal;
    },
    updateDataRow: function updateDataRow(id, data) {
      this.gridData.data.map(function (o) {
        if (o.id == id) return data;else return o;
      });
      this.editModal = false;
      this.fetchGrid();
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    columnsVisible: function columnsVisible() {
      return this.gridColumns ? this.gridColumns.filter(function (col) {
        return col.toggle === true || !col.hasOwnProperty('toggle');
      }) : [];
    }
  },
  watch: {
    gridFilters: {
      handler: function handler(to, from) {
        this.gridPage = 1; // reset page to 1 if filters change

        if (this.gridFilters) this.fetchGrid();
      },
      deep: true
    },
    gridPage: function gridPage(to, from) {
      if (this.gridFilters) this.fetchGrid();
    },
    schema: {
      handler: function handler(to, from) {
        if (!from && to) this.setFilters(); // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
        else if (!lodash__WEBPACK_IMPORTED_MODULE_4___default.a.isEqual(to.filters, from.filters)) this.setFilters(); // if a schema filter change - reset the filters - which will refresh the grid
          else if (this.shouldReload == true) {
              // or if we set a should reload flag, reload with refreshed schema
              this.fetchGrid();
              this.shouldReload = false;
            }
        if (to && !this.gridColumns) this.gridColumns = to.meta.fields; // load gridColumns if not already loaded.
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_Location__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/Location */ "./resources/js/models/Location.js");
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
    model: {
      type: String,
      "default": 'Location'
    },
    module: {
      type: String,
      "default": 'auth'
    }
  },
  components: {},
  data: function data() {
    return {
      item: null,
      itemState: 'save',
      migrationType: 'btdb',
      dbConnected: false,
      locationsFound: [],
      foundOtherLocations: [],
      licensenum: null,
      isProcessing: false,
      isSending: false
    };
  },
  mounted: function mounted() {
    if (this.schema) this.loadProfile();
  },
  methods: {
    autoSave: function autoSave() {
      var _this = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.parseHost();
      this.$validator.validateAll().then(function (result) {
        if (result) {
          if (!confirm) lodash__WEBPACK_IMPORTED_MODULE_1___default.a.debounce(function () {
            _this._save();
          }, 2000)();else _this._save(true);
        } else if (confirm == true) _this.$announcer({
          status: 422,
          data: {
            message: 'Whoops, Please check and correct inputs in order to continue.'
          }
        });
      });
    },
    _save: function _save() {
      var _this2 = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.itemState = 'saving..';
      this.item.save().then(function (response) {
        if (confirm) {
          _this2.$store.dispatch('resetAuthLocation', {
            scope: 'user,agg',
            reset: false
          }); // async dispatch to update user.location info based on this change


          _this2.$emit('updateLocation', _this2.item);

          _this2.$announcer({
            status: 200,
            data: {
              message: 'Your Item Settings have been Saved!'
            }
          });
        }

        _this2.itemState = 'saved';

        if (_this2.dbLinkFormfilledOut && !_this2.isProcessing) {
          _this2.$announcer({
            data: {
              message: 'Testing Connection.  This may take a moment...'
            }
          });

          _this2.testConnection();
        } // TODO update user location store!

      })["catch"](function (error) {
        _this2.$announcer(error.response);

        _this2.itemState = 'resave';
      });
    },
    loadProfile: function loadProfile() {
      var _this3 = this;

      _models_Location__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.$store.state.disp.location).then(function (response) {
        _this3.item = new _models_Location__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this3.schema);
        _this3.isLoading = false;
      })["catch"](function (error) {
        _this3.$announcer({
          status: 400,
          data: {
            message: 'We had a hiccup fetching the data - Please try again later.'
          }
        });
      });
      return true;
    },
    parseHost: function parseHost() {
      var userInput = this.item.migration_settings.btdb_host;

      if (userInput && userInput.indexOf(':') > -1 && !this.item.migration_settings.btdb_port) {
        this.item.migration_settings.btdb_host = userInput.substr(0, userInput.indexOf(':'));
        this.item.migration_settings.btdb_port = userInput.substr(userInput.indexOf(':') + 1);
      }
    },
    testConnection: function testConnection() {
      var _this4 = this;

      this.isProcessing = true;
      axios.get('/api/v1/admin/auth/testMigrationConnection/' + this.migrationType).then(function (response) {
        if (response.data.db_connected) {
          _this4.dbConnected = true;
          _this4.licensenum = response.data.licensenum;
          _this4.locationsFound = response.data.found_locations || [];
          if (_this4.locationsFound.length == 1) _this4.item.migration_settings.btdb_location_id = _this4.locationsFound[0].id; // if only 1, just match it.

          if (response.data.other_locations && response.data.other_locations.length > 0) {
            _this4.foundOtherLocations = response.data.other_locations || [];
          }
        } else {
          _this4.dbConnected = false;
        }

        _this4.isProcessing = false;
      })["catch"](function (error) {
        _this4.dbConnected = false;
        _this4.locationsFound = [];
        _this4.isProcessing = false;

        _this4.$announcer(error.response);
      });
    },
    startMigration: function startMigration() {
      var _this5 = this;

      this.isSending = true;
      axios.post('/api/v1/admin/auth/startMigration/' + this.migrationType, {
        location: this.item
      }).then(function (response) {
        _this5.isSending = false;

        _this5.$announcer(response);

        _this5.$router.push('/admin/dashboard');
      })["catch"](function (error) {
        _this5.isSending = false;

        _this5.$announcer(error.response);
      });
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    isReady: function isReady() {
      if (!(this.item || {}).migration_settings) return false;
      return this.dbConnected && this.locationsFound.length && this.item.migration_settings.backfill && this.item.migration_settings.btdb_location_id ? true : false;
    },
    dbLinkFormfilledOut: function dbLinkFormfilledOut() {
      return this.item.migration_settings.btdb_host && this.item.migration_settings.btdb_port && this.item.migration_settings.btdb_dbname && this.item.migration_settings.btdb_schema && this.item.migration_settings.btdb_username && this.item.migration_settings.btdb_password;
    }
  },
  watch: {
    schema: function schema(to, from) {
      if (to) this.loadProfile();
    },
    item: {
      handler: function handler(newVal, oldVal) {
        this.itemState = oldVal ? 'save changes' : newVal.id ? 'save' : 'create';
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js&":
/*!**************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js& ***!
  \**************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_Location__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/Location */ "./resources/js/models/Location.js");
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
//
//
//
//
//
//
//


/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Location'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    locationRef: {
      type: Object,
      "default": null
    }
  },
  data: function data() {
    return {
      item: null,
      itemState: 'save',
      apiToken: null,
      refreshingToken: false
    };
  },
  components: {},
  mounted: function mounted() {
    if (this.schema) this.loadProfile();
  },
  methods: {
    autoSave: function autoSave() {
      var _this = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      this.$validator.validateAll().then(function (result) {
        if (result) {
          if (!confirm) lodash__WEBPACK_IMPORTED_MODULE_1___default.a.debounce(function () {
            _this._save();
          }, 2000)();else _this._save(true);
        } else if (confirm == true) _this.$announcer({
          status: 422,
          data: {
            message: 'Whoops, Please check and correct inputs in order to continue.'
          }
        });
      });
    },
    _save: function _save() {
      var _this2 = this;

      var confirm = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      console.log(this.item);
      this.itemState = 'saving..';
      this.item.settings.is_medical = this.item.licensenum.includes('R') || this.item.licensenum.includes('r') ? false : true;
      this.item.settings.is_producer = this.item.licensenum.startsWith('403') ? true : false;
      this.item.settings.is_processor = this.item.licensenum.startsWith('404') ? true : false;
      this.item.save().then(function (response) {
        if (confirm) {
          _this2.$store.dispatch('resetAuthLocation', {
            scope: 'user,agg',
            reset: false
          }); // async dispatch to update user.location info based on this change


          _this2.$emit('updateLocation', _this2.item);

          _this2.$announcer({
            status: 200,
            data: {
              message: 'Your Item Settings have been Saved!'
            }
          });
        }

        _this2.itemState = 'saved'; // TODO update user location store!
      })["catch"](function (error) {
        _this2.$announcer(error.response);

        _this2.itemState = 'resave';
      });
    },
    getToken: function getToken() {
      var _this3 = this;

      this.refreshingToken = true;
      axios.post('/api/v1/admin/auth/newToken', {}).then(function (response) {
        _this3.refreshingToken = false;
        _this3.apiToken = response.data.api_token;

        _this3.$announcer(response);
      })["catch"](function (error) {
        _this3.refreshingToken = false;

        _this3.$announcer(error.response);
      });
    },
    loadProfile: function loadProfile() {
      var _this4 = this;

      //if(this.schema) this.item = new Item(this.locationRef || this.$store.state.user.location).withDefaults(this.schema);
      _models_Location__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.$store.state.disp.location).then(function (response) {
        _this4.item = new _models_Location__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this4.schema);
        _this4.isLoading = false;
      })["catch"](function (error) {
        _this4.$announcer({
          status: 400,
          data: {
            message: 'We had a hiccup fetching the data - Please try again later.'
          }
        });
      });
      return true;
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    }
  },
  watch: {
    schema: function schema(to, from) {
      if (to) this.loadProfile();
    },
    item: {
      handler: function handler(newVal, oldVal) {
        this.itemState = oldVal ? 'save changes' : newVal.id ? 'save' : 'create';
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vue2_dropzone_dist_vue2Dropzone_min_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue2-dropzone/dist/vue2Dropzone.min.css */ "./node_modules/vue2-dropzone/dist/vue2Dropzone.min.css");
/* harmony import */ var vue2_dropzone_dist_vue2Dropzone_min_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue2_dropzone_dist_vue2Dropzone_min_css__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Task__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Task */ "./resources/js/models/Task.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
      "default": null
    },
    users: {
      "default": null
    },
    locations: {
      "default": null
    },
    location: {
      type: Object,
      "default": null
    },
    model: {
      type: String,
      "default": 'Task'
    },
    module: {
      type: String,
      "default": 'auth'
    }
  },
  data: function data() {
    return {
      item: {},
      itemState: 'save',
      isFormDisabled: true,
      isProcessing: false
    };
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    dropzoneOptions: function dropzoneOptions() {
      return {
        url: '/api/v1/admin/asset/task/file',
        thumbnailWidth: 150,
        maxFilesize: 2,
        headers: {
          'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content
        }
      };
    },
    route: function route() {
      if (this.id) return '/api/v1/admin/auth/task/';else return '/api/v1/admin/auth/task/store'; // if rule id is 0 then we go to the store route
    },
    isFieldDisabled: function isFieldDisabled() {
      if (this.item && this.schema) {
        if (this.item.created_by) {
          return this.item.created_by.id !== this.$store.state.user.id;
        } else {
          return false;
        }
      }

      return false;
    }
  },
  watch: {
    item: {
      handler: function handler(newVal, oldVal) {
        this.itemState = oldVal ? 'save changes' : newVal.id ? 'save' : 'create';
      },
      deep: true
    }
  },
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;

    if (this.id) {
      _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"].find(this.id).then(function (response) {
        if (response.schema) _this.$store.commit(_this.module + '/setSchema', {
          data: response.schema,
          key: _this.model.toLowerCase() + 'Schema'
        });
        _this.item = new _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"](response).withDefaults(_this.schema);
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
      this.item = new _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"]().withDefaults(this.schema);
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
          if (!confirm) lodash__WEBPACK_IMPORTED_MODULE_2___default.a.debounce(function () {
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

          _this3.$router.push({
            name: _this3.model.toLowerCase()
          });
        }

        _this3.itemState = 'saved';
      })["catch"](function (error) {
        _this3.$announcer(error.response);

        _this3.itemState = 'resave';
      });
    },
    initForm: function initForm() {
      var _this4 = this;

      this.isFormDisabled = false;
      this.$nextTick(function () {
        return _this4.$refs.nameInput.focus();
      });
    },
    startProcessing: function startProcessing() {
      this.isProcessing = true;
    },
    stopProcessing: function stopProcessing() {
      this.isProcessing = false;
    },
    handleFileChange: function handleFileChange(file, response) {
      var attachmentObj = {
        name: file.name,
        url: response.uri
      };

      if (!this.item.attachments) {
        this.item.attachments = [];
      }

      this.item.attachments.push(attachmentObj);
    },
    getOptionsArray: function getOptionsArray() {
      if (this.id) {
        return Object.values(this.schema.form.users.values);
      }

      return Object.values(this.schema.form.location_users.values);
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Task__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Task */ "./resources/js/models/Task.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_2__);


function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

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


/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Task'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    filters: {
      // optional initial filters (filters.filter) object can be passed via this prop!
      type: Object,
      "default": function _default() {}
    }
  },
  data: function data() {
    return {
      isLoading: false,
      isDownloading: false,
      shouldReload: false,
      gridData: null,
      gridSearch: null,
      gridPage: 1,
      gridColumns: null,
      gridFilters: null,
      isSyncing: false
    };
  },
  mounted: function mounted() {
    //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
    this.gridSearch = this.$route.query.search || null;

    if (this.schema) {
      this.setFilters(this.$route.params.focus); // if we have schema, then set filters, else we watch schema load/change and then set.

      this.gridColumns = this.schema.meta.fields; // for some reason, the schema changing on edit doesnt register - need to reload upon mount
    }

    this.restoreGridSettings();
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    columnsVisible: function columnsVisible() {
      return this.gridColumns ? this.gridColumns.filter(function (col) {
        return col.toggle === true || !col.hasOwnProperty('toggle');
      }) : [];
    }
  },
  watch: {
    gridData: {
      handler: function handler(newValue, oldValue) {
        var saveGridDataOptions = newValue.data.filter(function (e) {
          return e._showDetails;
        }).map(function (e) {
          return {
            id: e.id,
            _showDetails: e._showDetails
          };
        });
        this.$store.commit(this.module + '/setSearch', {
          gridData: saveGridDataOptions,
          options: {
            merge: true
          }
        }); // persist search setting
      },
      deep: true
    },
    gridFilters: {
      handler: function handler(to, from) {
        if (this.gridFilters) {
          if (from && !this.isLoading) this.gridPage = 1; // reset page to 1 if filters change

          this.fetchGrid();
        }
      },
      deep: true
    },
    gridPage: function gridPage(to, from) {
      if (this.gridFilters) this.fetchGrid();
    },
    schema: {
      handler: function handler(to, from) {
        if (!from && to) this.setFilters(); // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
        else if (!lodash__WEBPACK_IMPORTED_MODULE_2___default.a.isEqual(to.filters, from.filters)) this.setFilters(); // if a schema filter change - reset the filters - which will refresh the grid
          else if (this.shouldReload == true) {
              // or if we set a should reload flag, reload with refreshed schema
              this.fetchGrid();
              this.shouldReload = false;
            }
        if (to && !this.gridColumns) this.gridColumns = to.meta.fields; // load gridColumns if not already loaded.
      },
      deep: true
    }
  },
  methods: {
    fetchGrid: function () {
      var _fetchGrid = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!(!this.schema || !this.gridFilters)) {
                  _context.next = 4;
                  break;
                }

                return _context.abrupt("return", false);

              case 4:
                if (!(this.isLoading == true)) {
                  _context.next = 6;
                  break;
                }

                return _context.abrupt("return", false);

              case 6:
                // do not fetch if we are already fetching
                this.$store.commit(this.module + '/setSearch', {
                  gridPage: this.gridPage,
                  options: {
                    merge: true
                  }
                }); // persist search setting

                this.isLoading = true;
                _context.next = 10;
                return new _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).params({
                  search: this.gridSearch
                }).orderBy((this.gridFilters.orderDesc ? '-' : '') + this.gridFilters.sortBy).limit(this.gridFilters.pageLimit).page(this.gridPage).get();

              case 10:
                this.gridData = _context.sent;
                this.mergeGridData();
                this.isLoading = false;

              case 13:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function fetchGrid() {
        return _fetchGrid.apply(this, arguments);
      }

      return fetchGrid;
    }(),
    searchGrid: lodash__WEBPACK_IMPORTED_MODULE_2___default.a.debounce(function (e) {
      // upon search filter update, throttle .5 sec grid and scope refresh
      this.gridPage = 1; //this.$store.commit(this.module+'/setSearch',this.gridSearch);   // persist search setting

      this.fetchGrid();
    }, 500),
    setFilters: function setFilters() {
      var _this = this;

      var focus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'all';
      if (!this.schema) return false;
      this.gridFilters = {
        // (re)set the filters from schema (which fetchGrid will watch and run)
        pageLimit: 50,
        sortBy: Object.keys(this.schema.filters).find(function (key) {
          return _this.schema.filters[key].type === 'daterange';
        }) || 'updated_at',
        // use first daterange field(key) in schema
        orderDesc: false,
        filter: Object.assign.apply(Object, [{}].concat(_toConsumableArray(Object.keys(this.schema.filters).map(function (k) {
          return _defineProperty({}, k, _this.schema.filters[k].values.map(function (v) {
            return v.id;
          }));
        })), [this.filters])) //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:['all']}; }),this.filters)

      };
    },
    renderAssigneeList: function renderAssigneeList(list) {
      return list ? list.map(function (v) {
        return v.name;
      }).join('<BR>') : 'n/a';
    },
    confirmDelete: function confirmDelete(id) {
      var _this2 = this;

      this.$swal.fire({
        title: 'Are you sure?',
        text: 'This will Delete this ' + this.model + ' row.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete row'
      }).then(function (result) {
        if (result.value) {
          _this2.isLoading = true;
          new _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"]({
            id: id
          })["delete"]().then(function (response) {
            _this2.isLoading = false;

            _this2.$store.dispatch(_this2.module + '/setSchemas', _this2.model.toLowerCase()); // get schema for new agg data


            _this2.$announcer(response);

            _this2.fetchGrid(); // if we deleted an item, then refetch..

          })["catch"](function (error) {
            _this2.isLoading = false;

            _this2.$announcer(error.response);
          });
        }
      });
    },
    downloadExport: function downloadExport(typ) {
      var _this3 = this;

      this.isDownloading = true;
      axios({
        url: new _models_Task__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
        method: 'GET',
        responseType: 'blob' // important

      }).then(function (response) {
        _this3.isDownloading = false;

        _this3.downloadFile(response);
      })["catch"](function (error) {
        _this3.isDownloading = false;

        _this3.$announcer(error.response);
      });
    },
    restoreGridSettings: function restoreGridSettings() {
      if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridPage) {
        this.gridPage = this.$store.state[this.module].search.gridPage;
        this.$store.commit(this.module + '/setSearch', {
          gridPage: null,
          options: {
            merge: true
          }
        }); // persist search setting
      }
    },
    mergeGridData: function mergeGridData() {
      var _this4 = this;

      if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridData && Array.isArray(this.gridData.data)) {
        //merge any saved options into griddata
        this.$store.state[this.module].search.gridData.forEach(function (saved) {
          var row = _this4.gridData.data.findIndex(function (e) {
            return e.id === saved.id;
          });

          if (row > -1) _this4.$set(_this4.gridData.data[row], '_showDetails', true); //$set if the _showDetails isn't already there we need the reactivity
        });
      }
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js&":
/*!*************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_User__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/User */ "./resources/js/models/User.js");


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

/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'User'
    },
    module: {
      type: String,
      "default": 'auth'
    }
  },
  data: function data() {
    return {
      id: 0,
      item: null,
      itemState: 'create'
    };
  },
  components: {},
  mounted: function () {
    var _mounted = _asyncToGenerator(
    /*#__PURE__*/
    _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              this.isLoading = true;

              if (this.schema) {
                _context.next = 4;
                break;
              }

              _context.next = 4;
              return this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase());

            case 4:
              this.item = new _models_User__WEBPACK_IMPORTED_MODULE_1__["default"]().withDefaults(this.schema);
              this.isLoading = false;

            case 6:
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
    createItem: function createItem() {
      var _this = this;

      this.$validator.validateAll().then(function (result) {
        if (result) {
          _this.itemState = 'saving..';

          _this.item.save().then(function (response) {
            _this.$announcer({
              status: 200,
              data: {
                message: 'Your ' + _this.model + ' data has been Saved!'
              }
            });

            if (response.schema) _this.$store.commit(_this.module + '/setSchema', {
              data: response.schema,
              key: _this.model.toLowerCase() + 'Schema'
            });
            _this.itemState = 'created';

            _this.$router.push({
              name: 'user'
            });
          })["catch"](function (error) {
            _this.$announcer(error.response);

            _this.itemState = 'resave';
          });
        } else _this.$announcer({
          status: 422,
          data: {
            message: 'Missing / Invalid input - Please check form above and try again.'
          }
        });
      });
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    }
  },
  watch: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _models_User__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../models/User */ "./resources/js/models/User.js");
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    model: {
      type: String,
      "default": 'User'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    id: {
      type: [Number, String],
      "default": ''
    },
    type: {
      type: String,
      "default": 'edit'
    }
  },
  data: function data() {
    return {
      item: null,
      schema: null,
      tab: this.type,
      itemState: 'update',
      isLoading: false,
      isDownloading: false,
      resetState: 'reset',
      sendState: 'send',
      viewPINTab: false,
      resetForm: {
        id: this.id,
        password: null,
        password_confirmation: null
      }
    };
  },
  components: {},
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;
    _models_User__WEBPACK_IMPORTED_MODULE_0__["default"].find(this.id).then(function (response) {
      _this.schema = response.schema;
      _this.item = new _models_User__WEBPACK_IMPORTED_MODULE_0__["default"](response).withDefaults(_this.schema);
      _this.isLoading = false;
    })["catch"](function (error) {
      _this.$announcer({
        status: 400,
        data: {
          message: 'We had a hiccup fetching the data - Please try again later.'
        }
      });

      _this.$emit('refresh');
    });
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
          }, 500)();else _this2._save(true);
        } else if (confirm == true) _this2.$announcer({
          status: 422,
          data: {
            message: 'Whoops, Please check and correct inputs in order to continue.'
          }
        });
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
              message: _this3.model + ' has been Saved!'
            }
          });

          if (response.schema) _this3.$store.commit(_this3.module + '/setSchema', {
            data: response.schema,
            key: _this3.model.toLowerCase() + 'Schema'
          });

          _this3.$emit('refresh');
        }

        _this3.itemState = 'update (saved)';
      })["catch"](function (error) {
        _this3.$announcer(error.response);

        _this3.itemState = '(re)update';
      });
    },
    downloadExportFile: function downloadExportFile(id, typ) {
      var _this4 = this;

      this.isDownloading = true;
      axios.get('/api/v1/' + this.schema.meta.resource + '/' + id + '/export/' + typ, {
        responseType: 'arraybuffer'
      }).then(function (response) {
        _this4.isDownloading = false;

        _this4.downloadFile(response);
      })["catch"](function (error) {
        _this4.isDownloading = false;

        _this4.$announcer(error.response);
      });
    },
    hasPermissions: function hasPermissions(id, permission) {
      var _this5 = this;

      this.viewPINTab = false;
      axios.post('/api/v1/' + this.schema.meta.resource + '/' + id + '/permissions', {
        id: id,
        permission: permission
      }).then(function (data) {
        _this5.viewPINTab = true;
      })["catch"](function (data) {
        _this5.viewPINTab = false;
      });
    },
    isNumber: function isNumber(evt) {
      evt = evt ? evt : window.event;
      var charCode = evt.which ? evt.which : evt.keyCode;

      if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        evt.preventDefault();
        ;
      } else {
        return true;
      }
    },
    updatePassword: function updatePassword() {
      var _this6 = this;

      this.resetState = 'resetting..';
      axios.post('/api/v1/' + this.schema.meta.resource + '/' + this.id + '/change-password', this.resetForm).then(function (response) {
        _this6.resetState = 'resetted';

        _this6.$announcer(response);
      })["catch"](function (error) {
        _this6.resetState = 'reset';

        _this6.$announcer(error.response);
      });
    },
    updatePIN: function updatePIN() {
      var _this7 = this;

      this.resetState = 'resetting..';
      axios.post('/api/v1/' + this.schema.meta.resource + '/' + this.id + '/change-pin', this.resetForm).then(function (response) {
        _this7.resetState = 'resetted';

        _this7.$announcer(response);
      })["catch"](function (error) {
        _this7.resetState = 'reset';

        _this7.$announcer(error.response);
      });
    },
    sendActivation: function sendActivation() {
      var _this8 = this;

      this.sendState = 'preparing..';
      axios.post('/api/v1/' + this.schema.meta.resource + '/' + this.id + '/send-activation', {
        id: this.id
      }).then(function (response) {
        _this8.sendState = 'sending';

        _this8.$announcer(response);
      })["catch"](function (error) {
        _this8.sendState = 'send';

        _this8.$announcer(error.response);
      });
    }
  },
  computed: {},
  watch: {
    item: {
      handler: function handler(newVal, oldVal) {
        this.itemState = oldVal ? 'update changes' : newVal.id ? 'update' : 'create';
      },
      deep: true
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _editModal__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editModal */ "./resources/js/components/views/auth/user/editModal.vue");
/* harmony import */ var _models_User__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../models/User */ "./resources/js/models/User.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);


function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance"); }

function _iterableToArray(iter) { if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } }

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
//
//
//
//
//
//
//



/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'User'
    },
    module: {
      type: String,
      "default": 'auth'
    },
    filters: {
      // optional initial filters (filters.filter) object can be passed via this prop!
      type: Object,
      "default": function _default() {}
    }
  },
  data: function data() {
    return {
      isLoading: false,
      isDownloading: false,
      shouldReload: false,
      gridData: null,
      gridSearch: null,
      gridPage: 1,
      gridColumns: null,
      gridFilters: null,
      gridArchive: false,
      editModal: this.$route.params.showProfile ? this.$route.params.showProfile : false,
      editRef: this.$route.params.profileId ? this.$route.params.profileId : 0,
      editType: 'edit'
    };
  },
  components: {
    EditModal: _editModal__WEBPACK_IMPORTED_MODULE_1__["default"]
  },
  mounted: function mounted() {
    //this.gridSearch = this.$store.state[this.module].search || null;    // if we have a search state - populate
    this.gridSearch = this.$route.query.search || null;

    if (this.schema) {
      this.setFilters(this.$store.state.disp.location); // if we have schema, then set filters, else we watch schema load/change and then set.

      this.gridColumns = this.schema.meta.fields; // for some reason, the schema changing on edit doesnt register - need to reload upon mount
    }
  },
  methods: {
    fetchGrid: function () {
      var _fetchGrid = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!(!this.schema || !this.gridFilters)) {
                  _context.next = 4;
                  break;
                }

                return _context.abrupt("return", false);

              case 4:
                if (!(this.isLoading == true)) {
                  _context.next = 6;
                  break;
                }

                return _context.abrupt("return", false);

              case 6:
                // do not fetch if we are already fetching
                this.isLoading = true;
                _context.next = 9;
                return new _models_User__WEBPACK_IMPORTED_MODULE_2__["default"]().setFilters(this.gridFilters.filter).params({
                  search: this.gridSearch,
                  archived: this.gridArchive ? 1 : 0
                }).orderBy((this.gridFilters.orderDesc ? '-' : '') + this.gridFilters.sortBy).limit(this.gridFilters.pageLimit).page(this.gridPage).get();

              case 9:
                this.gridData = _context.sent;
                this.isLoading = false;

              case 11:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function fetchGrid() {
        return _fetchGrid.apply(this, arguments);
      }

      return fetchGrid;
    }(),
    searchGrid: lodash__WEBPACK_IMPORTED_MODULE_3___default.a.debounce(function (e) {
      // upon search filter update, throttle .5 sec grid and scope refresh
      this.gridPage = 1; //this.$store.commit(this.module+'/setSearch',this.gridSearch);   // persist search setting

      this.fetchGrid();
    }, 500),
    setFilters: function setFilters() {
      var _this = this;

      var focus = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'all';
      if (!this.schema) return false;
      this.gridFilters = {
        // (re)set the filters from schema (which fetchGrid will watch and run)
        pageLimit: 50,
        sortBy: Object.keys(this.schema.filters).find(function (key) {
          return _this.schema.filters[key].type === 'daterange';
        }) || 'created_at',
        // use first daterange filter field(key) in schema
        orderDesc: false,
        //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)
        filter: Object.assign.apply(Object, [{}].concat(_toConsumableArray(Object.keys(this.schema.filters).map(function (k) {
          return _defineProperty({}, k, ['all']);
        })), [this.filters]))
      };
    },
    parseStatus: function parseStatus(value) {
      var schemaValue = this.schema.form.status.values.find(function (k) {
        return k.id == value;
      });
      return schemaValue ? schemaValue.name : value;
    },
    loadModal: function loadModal(ref, typ) {
      this.editRef = ref;
      this.editType = typ;
      this.editModal = true;
    },
    refreshFromModal: function refreshFromModal(upd) {
      this.editModal = !this.editModal;
      this.shouldReload = true; // in the ecent the schema doesnt change from anything edited in the modal, flag a relaod.
    },
    renderRoleList: function renderRoleList(list) {
      return list ? list.map(function (v) {
        return v.name;
      }).join(', ') : 'n/a';
    },
    confirmDelete: function confirmDelete(id) {
      var _this2 = this;

      this.$swal.fire({
        title: 'Are you sure?',
        text: 'This will Archive this User',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Archive User'
      }).then(
      /*#__PURE__*/
      function () {
        var _ref2 = _asyncToGenerator(
        /*#__PURE__*/
        _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee2(result) {
          var withPin;
          return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee2$(_context2) {
            while (1) {
              switch (_context2.prev = _context2.next) {
                case 0:
                  if (!result.value) {
                    _context2.next = 10;
                    break;
                  }

                  _this2.isLoading = true;
                  _context2.next = 4;
                  return _this2.requirePin('Please Enter an Admin PIN to archive this User');

                case 4:
                  withPin = _context2.sent;

                  if (!(withPin === false)) {
                    _context2.next = 8;
                    break;
                  }

                  _this2.isLoading = false;
                  return _context2.abrupt("return", false);

                case 8:
                  new _models_User__WEBPACK_IMPORTED_MODULE_2__["default"]({
                    id: id
                  })["delete"]().then(function (response) {
                    _this2.isLoading = false;

                    _this2.$store.dispatch(_this2.module + '/setSchemas', _this2.model.toLowerCase()); // get schema for new agg data


                    _this2.$announcer(response);

                    _this2.fetchGrid(); // if we deleted an item, then refetch..

                  })["catch"](function (error) {
                    _this2.isLoading = false;

                    _this2.$announcer(error.response);
                  });
                  return _context2.abrupt("return", true);

                case 10:
                case "end":
                  return _context2.stop();
              }
            }
          }, _callee2);
        }));

        return function (_x) {
          return _ref2.apply(this, arguments);
        };
      }());
    },
    unArchive: function unArchive(id) {
      var _this3 = this;

      this.isLoading = true;
      axios.get('/api/v1/' + this.schema.meta.resource + '/' + id + '/unarchive').then(function (response) {
        _this3.isLoading = false;

        _this3.$store.dispatch(_this3.module + '/setSchemas', _this3.model.toLowerCase()); // get schema for new agg data


        _this3.$announcer(response);

        _this3.fetchGrid(); // if we deleted an item, then refetch..

      })["catch"](function (error) {
        _this3.isLoading = false;

        _this3.$announcer(error.response);
      });
    },
    downloadExport: function downloadExport(typ) {
      var _this4 = this;

      this.isDownloading = true;
      axios({
        url: new _models_User__WEBPACK_IMPORTED_MODULE_2__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
        method: 'GET',
        responseType: 'blob' // important

      }).then(function (response) {
        _this4.isDownloading = false;

        _this4.downloadFile(response);
      })["catch"](function (error) {
        _this4.isDownloading = false;

        _this4.$announcer(error.response);
      });
    },
    renderRowBg: function renderRowBg(item, type) {
      if (!item) return null;
      return {
        'grid-table-rows': true,
        'is-archived': item.status === 'archived'
      };
    },
    alertDisabledArchive: function alertDisabledArchive() {
      this.$swal.fire({
        text: 'User Already Archived.',
        title: 'Archiving Unavailable',
        type: 'warning',
        confirmButtonText: 'OK'
      });
    }
  },
  computed: {
    schema: function schema() {
      return this.$store.state[this.module][this.model.toLowerCase() + 'Schema'];
    },
    columnsVisible: function columnsVisible() {
      return this.gridColumns ? this.gridColumns.filter(function (col) {
        return col.toggle === true || !col.hasOwnProperty('toggle');
      }) : [];
    },
    gridFocus: function gridFocus() {
      return this.$store.state[this.module].focus;
    }
  },
  watch: {
    gridFilters: {
      handler: function handler(to, from) {
        this.gridPage = 1; // reset page to 1 if filters change

        if (this.gridFilters) this.fetchGrid();
      },
      deep: true
    },
    gridPage: function gridPage(to, from) {
      if (this.gridFilters) this.fetchGrid();
    },
    schema: {
      handler: function handler(to, from) {
        if (!from && to) this.setFilters(); // if we just loaded a new schema data, then set filters (otherwise, this is set on mounted)
        else if (!lodash__WEBPACK_IMPORTED_MODULE_3___default.a.isEqual(to.filters, from.filters)) this.setFilters(this.gridFilters.filter.location_id[0]); // if a schema filter change - reset the filters - which will refresh the grid
          else if (this.shouldReload == true) {
              // or if we set a should reload flag, reload with refreshed schema
              this.fetchGrid();
              this.shouldReload = false;
            }
        if (to && !this.gridColumns) this.gridColumns = to.meta.fields; // load gridColumns if not already loaded.
      },
      deep: true
    },
    gridFocus: function gridFocus(to, from) {
      if (to && this.gridFilters && !lodash__WEBPACK_IMPORTED_MODULE_3___default.a.isEqual(to, from)) {
        if (to != 'all' && from != 'all') this.gridSearch = null; // if we are changing focus tab, clear search

        if (this.gridFilters) this.gridFilters.filter['type'] = [to];
      }
    },
    gridArchive: function gridArchive(to, from) {
      if (this.gridFilters) this.fetchGrid();
    }
  }
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&":
/*!************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css& ***!
  \************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.migration-options .vdp-datepicker__calendar {\n    left: 0px;\n}\n    \n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.item-title {\n  margin-bottom: 0;\n}\n.vdp-datepicker__calendar {\n  left: 0;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.btn-create-new {\n    margin-top: 0;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&":
/*!****************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css& ***!
  \****************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./migration.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&");

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&");

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=style&index=0&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&");

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186& ***!
  \****************************************************************************************************************************************************************************************************************************************/
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
  return _vm.schema && _vm.batch
    ? _c("div", { staticClass: "col-12" }, [
        _c("form", { staticClass: "modal-form" }, [
          _c("fieldset", [
            _vm.batch && _vm.schema
              ? _c(
                  "div",
                  { staticClass: "col-12" },
                  [
                    _c("div", { staticClass: "nav-tabs-header mb-4" }, [
                      _c(
                        "ul",
                        { staticClass: "nav nav-tabs nav-tabs-custom" },
                        [
                          _c(
                            "li",
                            {
                              staticClass: "nav-link",
                              class: { active: _vm.tab == "edit" }
                            },
                            [
                              _c(
                                "a",
                                {
                                  attrs: { href: "" },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      _vm.tab = "edit"
                                    }
                                  }
                                },
                                [_vm._v("Batch Edit")]
                              )
                            ]
                          ),
                          _vm._v(" "),
                          _c(
                            "li",
                            {
                              staticClass: "nav-link",
                              class: { active: _vm.tab == "archive" }
                            },
                            [
                              _c(
                                "a",
                                {
                                  attrs: { href: "" },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      _vm.tab = "archive"
                                    }
                                  }
                                },
                                [_vm._v("Batch Archive")]
                              )
                            ]
                          )
                        ]
                      )
                    ]),
                    _vm._v(" "),
                    _c("transition", { attrs: { name: "bo-slide" } }, [
                      _vm.tab == "edit"
                        ? _c("div", [
                            _c(
                              "table",
                              {
                                staticClass: "table b-table",
                                attrs: { role: "table" }
                              },
                              [
                                _c("thead", [
                                  _c(
                                    "tr",
                                    [
                                      _c("th", { attrs: { valign: "top" } }, [
                                        _vm._v("Item")
                                      ]),
                                      _vm._v(" "),
                                      _vm._l(_vm.batchFields, function(
                                        fsch,
                                        fkey
                                      ) {
                                        return _c(
                                          "th",
                                          [
                                            _c(
                                              "span",
                                              { staticClass: "small" },
                                              [
                                                _vm._v(
                                                  "Override " +
                                                    _vm._s(fsch.title)
                                                )
                                              ]
                                            ),
                                            _vm._v(" "),
                                            _c("i", {
                                              staticClass: "float-right",
                                              class: {
                                                "fas fa-sort show-opaque":
                                                  _vm.batchSortBy != fkey,
                                                "fas fa-sort-amount-down":
                                                  _vm.batchSortBy == fkey &&
                                                  _vm.batchSortDesc === false,
                                                "fas fa-sort-amount-up":
                                                  _vm.batchSortBy == fkey &&
                                                  _vm.batchSortDesc === true
                                              },
                                              on: {
                                                click: function($event) {
                                                  return _vm.batchSort(fkey)
                                                }
                                              }
                                            }),
                                            _c("br"),
                                            _vm._v(" "),
                                            fsch.type == "boolean"
                                              ? _c("form-boolean", {
                                                  attrs: {
                                                    declared:
                                                      _vm.batchFields[fkey]
                                                        .override,
                                                    schema: fsch,
                                                    skipValidation: true,
                                                    hideLabel: true
                                                  },
                                                  on: {
                                                    input: function(upd) {
                                                      _vm.batchFields[
                                                        fkey
                                                      ].override = upd
                                                      _vm.batchOverride(
                                                        fkey,
                                                        upd
                                                      )
                                                    }
                                                  }
                                                })
                                              : _vm._e(),
                                            _vm._v(" "),
                                            fsch.type == "text"
                                              ? _c("form-text", {
                                                  attrs: {
                                                    schema: fsch,
                                                    hideLabel: true,
                                                    skipValidation: true
                                                  },
                                                  on: {
                                                    input: function($event) {
                                                      return _vm.batchOverride(
                                                        fkey,
                                                        _vm.batchFields[fkey]
                                                          .override
                                                      )
                                                    }
                                                  },
                                                  model: {
                                                    value:
                                                      _vm.batchFields[fkey]
                                                        .override,
                                                    callback: function($$v) {
                                                      _vm.$set(
                                                        _vm.batchFields[fkey],
                                                        "override",
                                                        $$v
                                                      )
                                                    },
                                                    expression:
                                                      "batchFields[fkey].override"
                                                  }
                                                })
                                              : _vm._e(),
                                            _vm._v(" "),
                                            fsch.type == "select"
                                              ? _c("form-simpleselect", {
                                                  attrs: {
                                                    schema: fsch,
                                                    hideLabel: true,
                                                    skipValidation: true
                                                  },
                                                  on: {
                                                    input: function($event) {
                                                      return _vm.batchOverride(
                                                        fkey,
                                                        _vm.batchFields[fkey]
                                                          .override
                                                      )
                                                    }
                                                  },
                                                  model: {
                                                    value:
                                                      _vm.batchFields[fkey]
                                                        .override,
                                                    callback: function($$v) {
                                                      _vm.$set(
                                                        _vm.batchFields[fkey],
                                                        "override",
                                                        $$v
                                                      )
                                                    },
                                                    expression:
                                                      "batchFields[fkey].override"
                                                  }
                                                })
                                              : _vm._e()
                                          ],
                                          1
                                        )
                                      })
                                    ],
                                    2
                                  )
                                ]),
                                _vm._v(" "),
                                _c(
                                  "tbody",
                                  _vm._l(_vm.batch, function(item, ind) {
                                    return _c(
                                      "tr",
                                      [
                                        _c("td", [
                                          _vm._v(_vm._s(item.name)),
                                          _c("br"),
                                          _c("span", { staticClass: "small" }, [
                                            _vm._v(_vm._s(item.address1)),
                                            _c("br"),
                                            _vm._v(
                                              _vm._s(item.city) +
                                                ", " +
                                                _vm._s(item.region) +
                                                " " +
                                                _vm._s(item.country)
                                            )
                                          ])
                                        ]),
                                        _vm._v(" "),
                                        _vm._l(_vm.batchFields, function(
                                          fsch,
                                          fkey
                                        ) {
                                          return _c(
                                            "td",
                                            [
                                              fsch.type == "boolean"
                                                ? _c("form-boolean", {
                                                    attrs: {
                                                      declared: item[fkey],
                                                      schema: fsch,
                                                      hideLabel: true
                                                    },
                                                    on: {
                                                      input: function(upd) {
                                                        item[fkey] = upd
                                                      }
                                                    }
                                                  })
                                                : _vm._e(),
                                              _vm._v(" "),
                                              fsch.type == "text"
                                                ? _c("form-text", {
                                                    attrs: {
                                                      schema: fsch,
                                                      hideLabel: true
                                                    },
                                                    model: {
                                                      value: item[fkey],
                                                      callback: function($$v) {
                                                        _vm.$set(
                                                          item,
                                                          fkey,
                                                          $$v
                                                        )
                                                      },
                                                      expression: "item[fkey]"
                                                    }
                                                  })
                                                : _vm._e(),
                                              _vm._v(" "),
                                              fsch.type == "select"
                                                ? _c("form-simpleselect", {
                                                    attrs: {
                                                      schema: fsch,
                                                      hideLabel: true
                                                    },
                                                    model: {
                                                      value: item[fkey],
                                                      callback: function($$v) {
                                                        _vm.$set(
                                                          item,
                                                          fkey,
                                                          $$v
                                                        )
                                                      },
                                                      expression: "item[fkey]"
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        })
                                      ],
                                      2
                                    )
                                  }),
                                  0
                                )
                              ]
                            ),
                            _vm._v(" "),
                            _c(
                              "div",
                              {
                                staticClass: "col-12 clearfix mt-2 text-center"
                              },
                              [
                                _c("auto-save", {
                                  attrs: {
                                    type: "save",
                                    state: _vm.batchState
                                  },
                                  on: {
                                    autoSave: function($event) {
                                      return _vm.saveBatchData("data")
                                    }
                                  }
                                }),
                                _vm._v(" "),
                                _c(
                                  "a",
                                  {
                                    staticClass: "btn btn-md btn-light",
                                    class: {
                                      "btn-light": _vm.batchState == "update",
                                      "btn-warning": _vm.batchState != "update"
                                    },
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
                                        return _vm.getBatchData()
                                      }
                                    }
                                  },
                                  [_vm._v("Restore.")]
                                )
                              ],
                              1
                            )
                          ])
                        : _vm._e()
                    ]),
                    _vm._v(" "),
                    _c("transition", { attrs: { name: "bo-slide" } }, [
                      _vm.tab == "archive"
                        ? _c("div", [
                            _c(
                              "div",
                              {
                                staticClass: "block-announce warning mt-4 mb-4"
                              },
                              [
                                _c("p", { staticClass: "title" }, [
                                  _c("i", {
                                    staticClass: "fal fa-megaphone show-yellow"
                                  }),
                                  _vm._v(" Are you Sure?..")
                                ]),
                                _vm._v(" "),
                                _c("p", [
                                  _vm._v(
                                    "This will remove from any view or report, but would still be used if linked to anything.  Please confirm these are the addresses you would like to archive."
                                  )
                                ])
                              ]
                            ),
                            _vm._v(" "),
                            _c(
                              "table",
                              {
                                staticClass: "table b-table table-striped",
                                attrs: { role: "table" }
                              },
                              [
                                _c("thead", [
                                  _c("tr", [
                                    _c("th", { attrs: { width: "90%" } }, [
                                      _vm._v("Item")
                                    ]),
                                    _vm._v(" "),
                                    _c("th", { attrs: { width: "10%" } }, [
                                      _vm._v("Confirm")
                                    ])
                                  ])
                                ]),
                                _vm._v(" "),
                                _c(
                                  "tbody",
                                  _vm._l(_vm.batch, function(item, ind) {
                                    return _c(
                                      "tr",
                                      {
                                        class: {
                                          "table-success": _vm.inSet(
                                            item.id,
                                            _vm.batch_ids
                                          )
                                        }
                                      },
                                      [
                                        _c("td", { attrs: { width: "90%" } }, [
                                          _vm._v(
                                            _vm._s(item.name) +
                                              " - " +
                                              _vm._s(item.address1) +
                                              " - " +
                                              _vm._s(item.city) +
                                              ", " +
                                              _vm._s(item.region) +
                                              " " +
                                              _vm._s(item.country)
                                          )
                                        ]),
                                        _vm._v(" "),
                                        _c(
                                          "td",
                                          {
                                            attrs: {
                                              width: "10%",
                                              valign: "top"
                                            }
                                          },
                                          [
                                            _c(
                                              "label",
                                              {
                                                staticClass:
                                                  "custom-control custom-checkbox"
                                              },
                                              [
                                                _c("input", {
                                                  staticClass:
                                                    "custom-control-input",
                                                  attrs: {
                                                    type: "checkbox",
                                                    disabled: false
                                                  },
                                                  domProps: {
                                                    checked: _vm.inSet(
                                                      item.id,
                                                      _vm.batch_ids
                                                    )
                                                  },
                                                  on: {
                                                    click: function($event) {
                                                      return _vm.toggleBatchId(
                                                        item.id,
                                                        $event
                                                      )
                                                    }
                                                  }
                                                }),
                                                _c("span", {
                                                  staticClass:
                                                    "custom-control-indicator"
                                                })
                                              ]
                                            )
                                          ]
                                        )
                                      ]
                                    )
                                  }),
                                  0
                                )
                              ]
                            ),
                            _vm._v(" "),
                            _c(
                              "div",
                              {
                                staticClass: "col-12 clearfix mt-2 text-center"
                              },
                              [
                                _c("auto-save", {
                                  attrs: {
                                    type: "save",
                                    state: _vm.batchState
                                  },
                                  on: {
                                    autoSave: function($event) {
                                      return _vm.saveBatchData("list")
                                    }
                                  }
                                }),
                                _vm._v(" "),
                                _c(
                                  "a",
                                  {
                                    staticClass: "btn btn-md btn-light",
                                    class: {
                                      "btn-light": _vm.batchState == "update",
                                      "btn-warning": _vm.batchState != "update"
                                    },
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
                                        return _vm.$emit("refresh")
                                      }
                                    }
                                  },
                                  [_vm._v("Cancel.")]
                                )
                              ],
                              1
                            )
                          ])
                        : _vm._e()
                    ])
                  ],
                  1
                )
              : _c(
                  "div",
                  { staticClass: "no-content-block" },
                  [
                    _c("loading", {
                      attrs: { display: true, type: "loadModal" }
                    })
                  ],
                  1
                )
          ])
        ])
      ])
    : _c(
        "div",
        { staticClass: "no-content-block" },
        [
          _c("loading", {
            attrs: { display: _vm.schema ? false : true, type: "loadModal" }
          })
        ],
        1
      )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b&":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b& ***!
  \**********************************************************************************************************************************************************************************************************************************/
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
                return _vm.autoSave()
              }
            }
          },
          [
            _c("fieldset", [
              _vm.item.id
                ? _c("h3", { staticClass: "mb-4" }, [
                    _vm._v("Edit AddressBook Entry"),
                    _c("br"),
                    _c("span", { staticClass: "small" }, [
                      _vm._v("for " + _vm._s(_vm.item.name))
                    ])
                  ])
                : _c("h3", [_vm._v("Add a new Address:")]),
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
                    _c(
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
                  ],
                  1
                )
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3& ***!
  \******************************************************************************************************************************************************************************************************************************/
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
  return _vm.schema
    ? _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("form", [
            _c("div", { staticClass: "table-filter-row" }, [
              _c(
                "div",
                {
                  staticClass: "filter-search",
                  class: { active: _vm.gridSearch }
                },
                [
                  _c("div", { staticClass: "input-group" }, [
                    _vm._m(0),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.gridSearch,
                          expression: "gridSearch"
                        }
                      ],
                      staticClass: "form-control border-left-0",
                      attrs: {
                        type: "text",
                        placeholder:
                          _vm.schema.lang.searchPrompt || "Search Grid"
                      },
                      domProps: { value: _vm.gridSearch },
                      on: {
                        input: [
                          function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.gridSearch = $event.target.value
                          },
                          _vm.searchGrid
                        ],
                        keydown: function($event) {
                          if (
                            !$event.type.indexOf("key") &&
                            _vm._k(
                              $event.keyCode,
                              "enter",
                              13,
                              $event.key,
                              "Enter"
                            )
                          ) {
                            return null
                          }
                          $event.preventDefault()
                          return _vm.searchGrid($event)
                        }
                      }
                    })
                  ])
                ]
              ),
              _vm._v(" "),
              _vm.gridFilters
                ? _c(
                    "div",
                    { staticClass: "filters" },
                    [
                      _vm.gridData
                        ? _c("filter-more", {
                            attrs: {
                              meta: _vm.gridData.meta,
                              schema: _vm.schema,
                              gridFilters: _vm.gridFilters,
                              columns: _vm.gridColumns,
                              isDownloading: _vm.isDownloading
                            },
                            on: {
                              "update:gridFilters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:grid-filters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:columns": function($event) {
                                _vm.gridColumns = $event
                              },
                              downloadExport: _vm.downloadExport
                            }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "wherein"
                          ? _c("filter-in", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      }),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "daterange"
                          ? _c("filter-date", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      })
                    ],
                    2
                  )
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c("loading", {
            attrs: { display: _vm.isLoading, type: "loadGrid" }
          }),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "bo-slide" } },
            [
              _vm.schema && _vm.gridFilters
                ? _c("b-table", {
                    attrs: {
                      striped: "",
                      hover: "",
                      id: _vm.model.toLowerCase() + "_table",
                      "primary-key": "id",
                      items: _vm.gridData ? _vm.gridData.data : [],
                      fields: _vm.columnsVisible,
                      busy: _vm.isLoading,
                      "show-empty": true,
                      "sort-by": _vm.gridFilters.sortBy,
                      "sort-desc": _vm.gridFilters.orderDesc,
                      "no-local-sorting": true,
                      "no-local-filtering": true,
                      "per-page": 0,
                      tbodyTrClass: _vm.renderRowBg,
                      responsive: "md",
                      stacked: "sm"
                    },
                    on: {
                      "update:busy": function($event) {
                        _vm.isLoading = $event
                      },
                      "update:sortBy": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sort-by": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sortDesc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      },
                      "update:sort-desc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      }
                    },
                    scopedSlots: _vm._u(
                      [
                        _vm._l(_vm.schema.meta.fields, function(field, ind) {
                          return {
                            key: "head(field.key)",
                            fn: function(column) {
                              return [
                                _c("em", [
                                  field.icon
                                    ? _c("i", { class: field.icon })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  field.description
                                    ? _c("i", {
                                        directives: [
                                          {
                                            name: "b-tooltip",
                                            rawName: "v-b-tooltip.hover",
                                            value: field.description,
                                            expression: "field.description",
                                            modifiers: { hover: true }
                                          }
                                        ],
                                        staticClass:
                                          "hotbox-icon hotbox-icon-c-question",
                                        attrs: { title: field.name }
                                      })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  field.key == "batch_ids"
                                    ? _c("span", [
                                        _c(
                                          "label",
                                          {
                                            staticClass:
                                              "custom-control custom-checkbox"
                                          },
                                          [
                                            _c("input", {
                                              staticClass:
                                                "custom-control-input",
                                              attrs: { type: "checkbox" },
                                              domProps: {
                                                checked: _vm.isAllInBatch
                                              },
                                              on: { click: _vm.toggleBatchAll }
                                            }),
                                            _c("span", {
                                              staticClass:
                                                "custom-control-indicator"
                                            })
                                          ]
                                        )
                                      ])
                                    : _c("span", [_vm._v(_vm._s(column.label))])
                                ])
                              ]
                            }
                          }
                        }),
                        {
                          key: "cell(batch_ids)",
                          fn: function(row) {
                            return [
                              _c(
                                "label",
                                {
                                  staticClass: "custom-control custom-checkbox"
                                },
                                [
                                  _c("input", {
                                    staticClass: "custom-control-input",
                                    attrs: {
                                      type: "checkbox",
                                      disabled: false
                                    },
                                    domProps: {
                                      checked: _vm.inSet(
                                        row.item.id,
                                        _vm.batchEditIds
                                      )
                                    },
                                    on: {
                                      click: function($event) {
                                        return _vm.toggleBatchId(
                                          row.item.id,
                                          $event
                                        )
                                      }
                                    }
                                  }),
                                  _c("span", {
                                    staticClass: "custom-control-indicator"
                                  })
                                ]
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(updated_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(actions)",
                          fn: function(row) {
                            return [
                              _c("div", { staticClass: "dropdown" }, [
                                _c(
                                  "a",
                                  {
                                    attrs: {
                                      "data-toggle": "dropdown",
                                      "aria-haspopup": "true",
                                      "aria-expanded": "false"
                                    }
                                  },
                                  [_c("i", { staticClass: "ti-more-alt" })]
                                ),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  {
                                    staticClass:
                                      "dropdown-menu tight dropdown-menu-right"
                                  },
                                  [
                                    _c(
                                      "router-link",
                                      {
                                        staticClass: "dropdown-item",
                                        attrs: {
                                          to: {
                                            name:
                                              _vm.model.toLowerCase() + "_edit",
                                            params: { id: row.item.id }
                                          },
                                          tag: "a"
                                        }
                                      },
                                      [
                                        _c("i", {
                                          staticClass:
                                            "hotbox-icon hotbox-icon-pencil"
                                        }),
                                        _vm._v(
                                          " Edit " +
                                            _vm._s(_vm._f("ucwords")(_vm.model))
                                        )
                                      ]
                                    ),
                                    _vm._v(" "),
                                    _c(
                                      "a",
                                      {
                                        staticClass:
                                          "dropdown-item action-danger",
                                        on: {
                                          click: function($event) {
                                            return _vm.confirmDelete(
                                              row.item.id
                                            )
                                          }
                                        }
                                      },
                                      [
                                        _c("i", {
                                          staticClass:
                                            "hotbox-icon hotbox-icon-trash-round"
                                        }),
                                        _vm._v(
                                          " Delete " +
                                            _vm._s(_vm._f("ucwords")(_vm.model))
                                        )
                                      ]
                                    )
                                  ],
                                  1
                                )
                              ]),
                              _vm._v(" "),
                              row.item.contact_notes
                                ? _c("i", {
                                    staticClass: "float-right mt-2",
                                    class: {
                                      "ti-angle-double-down": !row.detailsShowing,
                                      "ti-angle-double-up": row.detailsShowing
                                    },
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        return row.toggleDetails($event)
                                      }
                                    }
                                  })
                                : _vm._e()
                            ]
                          }
                        },
                        {
                          key: "row-details",
                          fn: function(row) {
                            return [
                              _c(
                                "div",
                                { staticClass: "block-announce info" },
                                [
                                  _c("p", { staticClass: "title" }, [
                                    _vm._v("Contact Notes")
                                  ]),
                                  _vm._v(" "),
                                  _c("p", [
                                    _vm._v(_vm._s(row.item.contact_notes))
                                  ])
                                ]
                              )
                            ]
                          }
                        },
                        _vm.gridData
                          ? {
                              key: "table-caption",
                              fn: function() {
                                return [
                                  _vm.gridData.data.length > 0
                                    ? _c("div", [
                                        _vm.gridData.meta
                                          ? _c("span", [
                                              _vm._v(
                                                "\n                        Showing " +
                                                  _vm._s(
                                                    _vm.gridData.meta.per_page *
                                                      (_vm.gridPage - 1) +
                                                      1
                                                  ) +
                                                  " to " +
                                                  _vm._s(
                                                    _vm.gridData.meta.per_page *
                                                      _vm.gridPage <
                                                      _vm.gridData.meta.total
                                                      ? _vm.gridData.meta
                                                          .per_page *
                                                          _vm.gridPage
                                                      : _vm.gridData.meta.total
                                                  ) +
                                                  " of " +
                                                  _vm._s(
                                                    _vm.gridData.meta.total
                                                  ) +
                                                  " " +
                                                  _vm._s(
                                                    _vm.schema.lang.items
                                                      ? _vm.schema.lang.items
                                                      : "Records"
                                                  ) +
                                                  "\n                    "
                                              )
                                            ])
                                          : _c("span", [
                                              _vm._v(
                                                "Showing All " +
                                                  _vm._s(
                                                    _vm.schema.lang.items
                                                      ? _vm.schema.lang.items
                                                      : "Records"
                                                  )
                                              )
                                            ]),
                                        _vm._v(" "),
                                        _c(
                                          "div",
                                          { staticClass: "table-pager-footer" },
                                          [
                                            _vm.gridData.meta.total > 0
                                              ? _c("b-pagination", {
                                                  attrs: {
                                                    "total-rows":
                                                      _vm.gridData.meta.total,
                                                    "per-page":
                                                      _vm.gridData.meta
                                                        .per_page,
                                                    "aria-controls":
                                                      "users_table"
                                                  },
                                                  model: {
                                                    value: _vm.gridPage,
                                                    callback: function($$v) {
                                                      _vm.gridPage = $$v
                                                    },
                                                    expression: "gridPage"
                                                  }
                                                })
                                              : _vm._e()
                                          ],
                                          1
                                        )
                                      ])
                                    : _vm._e()
                                ]
                              },
                              proxy: true
                            }
                          : null,
                        {
                          key: "empty",
                          fn: function() {
                            return [
                              !_vm.isLoading
                                ? _c("div", [
                                    _c("img", {
                                      attrs: {
                                        src: "/images/logo.png",
                                        alt: "No Results",
                                        width: "115"
                                      }
                                    }),
                                    _vm._v(" "),
                                    _c("h4", [
                                      _vm._v(
                                        "Hmm, There are currently no Results."
                                      )
                                    ])
                                  ])
                                : _c("div", { staticClass: "h-100" }, [
                                    _vm._v(" ")
                                  ])
                            ]
                          },
                          proxy: true
                        }
                      ],
                      null,
                      true
                    )
                  })
                : _vm._e()
            ],
            1
          ),
          _vm._v(" "),
          _c("div", { staticClass: "col-12 batch-block" }, [
            _c("i", { staticClass: "batch-icon" }),
            _vm._v(" With Selected: \n        "),
            _c(
              "select",
              {
                directives: [
                  {
                    name: "model",
                    rawName: "v-model",
                    value: _vm.batchEditFocus,
                    expression: "batchEditFocus"
                  }
                ],
                staticClass: "batch-sel",
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
                    _vm.batchEditFocus = $event.target.multiple
                      ? $$selectedVal
                      : $$selectedVal[0]
                  }
                }
              },
              [
                _c("option", { attrs: { value: "edit" } }, [
                  _vm._v("Bulk Edit")
                ]),
                _vm._v(" "),
                _c("option", { attrs: { value: "archive" } }, [
                  _vm._v("Archive")
                ])
              ]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-sm",
                class: {
                  "btn-light": _vm.batchEditIds.length <= 0,
                  "btn-warning": _vm.batchEditIds.length > 0
                },
                attrs: { disabled: _vm.batchEditIds.length > 0 ? false : true },
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
                    _vm.batchEditModal = !_vm.batchEditModal
                  }
                }
              },
              [_vm._v("Batch It!")]
            )
          ]),
          _vm._v(" "),
          _c(
            "b-modal",
            {
              ref: "batchEditModal",
              attrs: {
                centered: "",
                size: "lg",
                "header-bg-variant": "light",
                "header-text-variant": "primary"
              },
              model: {
                value: _vm.batchEditModal,
                callback: function($$v) {
                  _vm.batchEditModal = $$v
                },
                expression: "batchEditModal"
              }
            },
            [
              _c("template", { slot: "modal-header" }, [
                _c("i", {
                  staticClass: "modal-top-close fal ti-close",
                  on: {
                    click: function($event) {
                      _vm.batchEditModal = !_vm.batchEditModal
                    }
                  }
                }),
                _vm._v(" "),
                _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                  _vm._v(
                    "Modify Batch of (" +
                      _vm._s(_vm.batchEditIds.length) +
                      ") Items"
                  )
                ])
              ]),
              _vm._v(" "),
              _vm.batchEditModal
                ? _c("batch-edit-modal", {
                    attrs: {
                      focus: _vm.batchEditFocus,
                      ids: _vm.batchEditIds,
                      schema: _vm.schema
                    },
                    on: {
                      "update:ids": function($event) {
                        _vm.batchEditIds = $event
                      },
                      refresh: _vm.refreshFromModal
                    }
                  })
                : _vm._e(),
              _vm._v(" "),
              _c("template", { slot: "modal-footer" }, [
                _c(
                  "span",
                  {
                    staticClass: "btn-label btn-sm btn-light float-right",
                    on: {
                      click: function($event) {
                        _vm.batchEditModal = !_vm.batchEditModal
                      }
                    }
                  },
                  [_vm._v("Close")]
                )
              ])
            ],
            2
          )
        ],
        1
      )
    : _c(
        "div",
        [
          _c("loading", {
            attrs: { display: _vm.schema ? false : true, type: "loadPage" }
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
    return _c("div", { staticClass: "input-group-prepend" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-search-2" })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37&":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37& ***!
  \*******************************************************************************************************************************************************************************************************************/
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
  return _c("div", { staticClass: "row gutters" }, [
    _vm.section
      ? _c(
          "div",
          { staticClass: "col-xl-12 col-lg-12 col-md-12 col-sm-12" },
          [
            _c("div", { staticClass: "card top-blue-bdr" }, [
              _c("div", { staticClass: "card-header" }, [
                _vm.indexView
                  ? _c(
                      "div",
                      { staticClass: "w-100 mb-2" },
                      [
                        _vm.indexView.with_add && !_vm.isEditPage
                          ? _c(
                              "router-link",
                              {
                                staticClass: "float-right mt-0 mb-3 mr-4",
                                attrs: {
                                  to: { name: _vm.indexView.name + "_create" },
                                  tag: "a"
                                }
                              },
                              [
                                _c("span", { staticClass: "btn-label" }, [
                                  _c("i", {
                                    staticClass: "hotbox-icon hotbox-icon-e-add"
                                  })
                                ]),
                                _vm._v(
                                  " New " +
                                    _vm._s(_vm.indexView.name) +
                                    "\n                    "
                                )
                              ]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.$store.getters.userCan("Store Admin Update") &&
                        _vm.indexView.name == "location"
                          ? _c(
                              "router-link",
                              {
                                staticClass:
                                  "float-right mr-2 btn btn-sm btn-round btn-primary",
                                attrs: { to: { name: "migration" }, tag: "a" }
                              },
                              [_vm._v("Migrate to Hotbox")]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.$store.getters.userCan("Staff Update") &&
                        _vm.indexView.name == "location"
                          ? _c(
                              "a",
                              {
                                staticClass:
                                  "float-right mr-2 btn btn-sm btn-round btn-light",
                                attrs: { href: "" },
                                on: {
                                  click: function($event) {
                                    $event.preventDefault()
                                    return _vm.loadEditModal(
                                      _vm.$store.state.disp.location
                                    )
                                  }
                                }
                              },
                              [_vm._v("Manage Location Personnel »")]
                            )
                          : _vm._e(),
                        _vm._v(" "),
                        _vm.$route.name == "location"
                          ? _c("h5", [
                              _c("i", { class: _vm.indexView.icon }),
                              _vm._v(
                                " For " +
                                  _vm._s(_vm.$store.state.user.location.name)
                              )
                            ])
                          : _vm._e()
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _vm.$route.name == _vm.section.module
                  ? _c("div", [
                      _c("h5", [_vm._v(_vm._s(_vm.section.name) + " Menu")]),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "row justify-content-center" },
                        _vm._l(_vm.section.views, function(link, ind) {
                          return _c(
                            "div",
                            {
                              staticClass: "card card-stats col-sm-3 mx-3 mt-2"
                            },
                            [
                              _c("div", { staticClass: "statistics" }, [
                                _c("div", { staticClass: "info" }, [
                                  _c(
                                    "div",
                                    {
                                      staticClass: "icon icon-primary",
                                      staticStyle: { "font-size": "2em" }
                                    },
                                    [
                                      _c("i", {
                                        class:
                                          link.icon ||
                                          "hotbox-icon hotbox-icon-c-pulse"
                                      })
                                    ]
                                  ),
                                  _vm._v(" "),
                                  _c(
                                    "h6",
                                    { staticClass: "stats-title" },
                                    [
                                      _c(
                                        "router-link",
                                        {
                                          attrs: {
                                            to: { name: link.name, params: {} },
                                            tag: "a"
                                          }
                                        },
                                        [_vm._v(_vm._s(link.title) + " »")]
                                      )
                                    ],
                                    1
                                  )
                                ])
                              ])
                            ]
                          )
                        }),
                        0
                      )
                    ])
                  : _vm._e()
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "card-body" }, [
                _vm.indexView
                  ? _c(
                      "div",
                      [
                        _vm.$store.getters.userCan(_vm.indexView.can_view)
                          ? _c("router-view")
                          : _vm._e()
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _c("hr"),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "stats" },
                  [
                    !_vm.isEditPage
                      ? _c(
                          "router-link",
                          {
                            attrs: { to: { name: "location_index" }, tag: "a" }
                          },
                          [
                            _c("i", { staticClass: "ti-angle-left" }),
                            _vm._v(" Back to Dashboard")
                          ]
                        )
                      : _vm.indexView
                      ? _c(
                          "router-link",
                          {
                            staticClass: "head-link",
                            attrs: {
                              to: { name: _vm.indexView.name },
                              tag: "a"
                            }
                          },
                          [
                            _c("i", { staticClass: "ti-angle-left" }),
                            _vm._v(
                              " Back to " +
                                _vm._s(
                                  _vm.indexView.title || _vm.indexView.tabname
                                ) +
                                " Index"
                            )
                          ]
                        )
                      : _vm._e()
                  ],
                  1
                )
              ])
            ]),
            _vm._v(" "),
            _c(
              "b-modal",
              {
                ref: "editModal",
                attrs: {
                  centered: "",
                  "no-enforce-focus": true,
                  size: "lg",
                  "header-bg-variant": "light",
                  "header-text-variant": "primary"
                },
                model: {
                  value: _vm.editModal,
                  callback: function($$v) {
                    _vm.editModal = $$v
                  },
                  expression: "editModal"
                }
              },
              [
                _c("template", { slot: "modal-header" }, [
                  _c("i", {
                    staticClass: "modal-top-close fal ti-close",
                    on: {
                      click: function($event) {
                        _vm.editModal = !_vm.editModal
                      }
                    }
                  }),
                  _vm._v(" "),
                  _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                    _vm._v(
                      _vm._s(_vm.$store.state.user.location.name) + " Manager"
                    )
                  ])
                ]),
                _vm._v(" "),
                _vm.editModal
                  ? _c("edit-modal", {
                      attrs: { id: _vm.editRef, type: "links" },
                      on: {
                        refresh: function($event) {
                          _vm.editModal = !_vm.editModal
                        }
                      }
                    })
                  : _vm._e(),
                _vm._v(" "),
                _c("template", { slot: "modal-footer" }, [
                  _c(
                    "span",
                    {
                      staticClass: "btn-label btn-sm btn-light float-right",
                      on: {
                        click: function($event) {
                          _vm.editModal = !_vm.editModal
                        }
                      }
                    },
                    [_vm._v("Close")]
                  )
                ])
              ],
              2
            )
          ],
          1
        )
      : _c("div", { staticClass: "col-12 text-center mt-4" })
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12& ***!
  \*********************************************************************************************************************************************************************************************************************************/
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
        _c("form", { staticClass: "modal-form" }, [
          _c(
            "fieldset",
            [
              _c("div", { staticClass: "nav-tabs-header mb-4" }, [
                _c("ul", { staticClass: "nav nav-tabs nav-tabs-custom" }, [
                  _c(
                    "li",
                    {
                      staticClass: "nav-link",
                      class: { active: _vm.tab == "location" }
                    },
                    [
                      _c(
                        "a",
                        {
                          attrs: { href: "" },
                          on: {
                            click: function($event) {
                              $event.preventDefault()
                              _vm.tab = "location"
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "fal fa-hotel" }),
                          _vm._v(" Status")
                        ]
                      )
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "li",
                    {
                      staticClass: "nav-link",
                      class: { active: _vm.tab == "links" }
                    },
                    [
                      _c(
                        "a",
                        {
                          attrs: { href: "" },
                          on: {
                            click: function($event) {
                              $event.preventDefault()
                              _vm.tab = "links"
                            }
                          }
                        },
                        [
                          _c("i", { staticClass: "fal fa-link" }),
                          _vm._v(" Links")
                        ]
                      )
                    ]
                  )
                ])
              ]),
              _vm._v(" "),
              _c("transition", { attrs: { name: "bo-slide" } }, [
                _vm.tab == "location"
                  ? _c(
                      "div",
                      {},
                      [
                        _c("form-simpleselect", {
                          attrs: { schema: _vm.schema.form.status },
                          model: {
                            value: _vm.form.update_status,
                            callback: function($$v) {
                              _vm.$set(_vm.form, "update_status", $$v)
                            },
                            expression: "form.update_status"
                          }
                        }),
                        _vm._v(" "),
                        _vm.form.update_status != _vm.item.status
                          ? _c("form-textarea", {
                              attrs: {
                                schema: {
                                  name: "update_message",
                                  title: "Provide an (optional) Client Message"
                                }
                              },
                              model: {
                                value: _vm.form.update_message,
                                callback: function($$v) {
                                  _vm.$set(_vm.form, "update_message", $$v)
                                },
                                expression: "form.update_message"
                              }
                            })
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "div",
                          {
                            staticClass:
                              "d-sm-flex mt-2 mb-3 justify-content-center"
                          },
                          [
                            _c("auto-save", {
                              attrs: { type: "save", state: _vm.itemState },
                              on: {
                                autoSave: function($event) {
                                  return _vm.autoSave("status")
                                }
                              }
                            })
                          ],
                          1
                        )
                      ],
                      1
                    )
                  : _vm._e()
              ]),
              _vm._v(" "),
              _c("transition", { attrs: { name: "bo-slide" } }, [
                _vm.tab == "links"
                  ? _c("div", {}, [
                      _c(
                        "div",
                        { staticClass: "col-12 mt-0 mb-3" },
                        [
                          _c("h3", [_vm._v("User Access to this location:")]),
                          _vm._v(" "),
                          _vm._l(_vm.item.users_to_link, function(user, uid) {
                            return _c("form-boolean", {
                              key: uid,
                              attrs: {
                                declared: _vm.inSet(
                                  user.id,
                                  _vm.form.update_user_ids
                                ),
                                schema: {
                                  name: "link_user_" + user.id,
                                  title: user.email
                                }
                              },
                              on: {
                                input: function(upd) {
                                  _vm.toggleLink(
                                    user.id,
                                    upd,
                                    "update_user_ids"
                                  )
                                }
                              }
                            })
                          })
                        ],
                        2
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        {
                          staticClass:
                            "d-sm-flex mt-2 mt-2 mb-3 justify-content-center"
                        },
                        [
                          _c("auto-save", {
                            attrs: { type: "save", state: _vm.itemState },
                            on: {
                              autoSave: function($event) {
                                return _vm.autoSave("links")
                              }
                            }
                          })
                        ],
                        1
                      )
                    ])
                  : _vm._e()
              ])
            ],
            1
          )
        ])
      ])
    : _c(
        "div",
        { staticClass: "no-content-block" },
        [
          _c("loading", {
            attrs: {
              display: _vm.item && _vm.schema ? false : true,
              type: "loadModal"
            }
          })
        ],
        1
      )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c&":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c& ***!
  \****************************************************************************************************************************************************************************************************************************/
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
  return _vm.schema
    ? _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("form", [
            _c("div", { staticClass: "table-filter-row" }, [
              _c("div", { staticClass: "filter-search" }, [
                _c("div", { staticClass: "input-group" }, [
                  _vm._m(0),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.gridSearch,
                        expression: "gridSearch"
                      }
                    ],
                    staticClass: "form-control border-left-0",
                    attrs: {
                      type: "text",
                      placeholder: _vm.schema.lang.searchPrompt || "Search Grid"
                    },
                    domProps: { value: _vm.gridSearch },
                    on: {
                      input: [
                        function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.gridSearch = $event.target.value
                        },
                        _vm.searchGrid
                      ],
                      keydown: function($event) {
                        if (
                          !$event.type.indexOf("key") &&
                          _vm._k(
                            $event.keyCode,
                            "enter",
                            13,
                            $event.key,
                            "Enter"
                          )
                        ) {
                          return null
                        }
                        $event.preventDefault()
                        return _vm.searchGrid($event)
                      }
                    }
                  })
                ])
              ]),
              _vm._v(" "),
              _vm.gridFilters
                ? _c(
                    "div",
                    { staticClass: "filters" },
                    [
                      _vm.gridData
                        ? _c("filter-more", {
                            attrs: {
                              meta: _vm.gridData.meta,
                              schema: _vm.schema,
                              gridFilters: _vm.gridFilters,
                              columns: _vm.gridColumns,
                              isDownloading: _vm.isDownloading
                            },
                            on: {
                              "update:gridFilters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:grid-filters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:columns": function($event) {
                                _vm.gridColumns = $event
                              },
                              downloadExport: _vm.downloadExport
                            }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "wherein"
                          ? _c("filter-in", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      }),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "daterange"
                          ? _c("filter-date", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      })
                    ],
                    2
                  )
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c("loading", {
            attrs: { display: _vm.isLoading, type: "loadGrid" }
          }),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "bo-slide" } },
            [
              _vm.schema && _vm.gridFilters
                ? _c("b-table", {
                    attrs: {
                      striped: "",
                      hover: "",
                      id: _vm.model.toLowerCase() + "_table",
                      "primary-key": "id",
                      items: _vm.gridData ? _vm.gridData.data : [],
                      fields: _vm.columnsVisible,
                      busy: _vm.isLoading,
                      "show-empty": true,
                      "sort-by": _vm.gridFilters.sortBy,
                      "sort-desc": _vm.gridFilters.orderDesc,
                      "no-local-sorting": true,
                      "no-local-filtering": true,
                      "per-page": 0,
                      tbodyTrClass: _vm.rowColor,
                      responsive: "md",
                      stacked: "sm"
                    },
                    on: {
                      "update:busy": function($event) {
                        _vm.isLoading = $event
                      },
                      "update:sortBy": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sort-by": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sortDesc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      },
                      "update:sort-desc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      }
                    },
                    scopedSlots: _vm._u(
                      [
                        _vm._l(_vm.schema.meta.fields, function(field, ind) {
                          return {
                            key: "head(field.key)",
                            fn: function(column) {
                              return [
                                _c("em", [
                                  field.icon
                                    ? _c("i", { class: field.icon })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  field.description
                                    ? _c("i", {
                                        directives: [
                                          {
                                            name: "b-tooltip",
                                            rawName: "v-b-tooltip.hover",
                                            value: field.description,
                                            expression: "field.description",
                                            modifiers: { hover: true }
                                          }
                                        ],
                                        staticClass:
                                          "hotbox-icon hotbox-icon-c-question",
                                        attrs: { title: field.name }
                                      })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  field.key == "batch_ids"
                                    ? _c("span", [
                                        _c(
                                          "label",
                                          {
                                            staticClass:
                                              "custom-control custom-checkbox"
                                          },
                                          [
                                            _c("input", {
                                              staticClass:
                                                "custom-control-input",
                                              attrs: { type: "checkbox" },
                                              domProps: {
                                                checked: _vm.isAllInBatch
                                              },
                                              on: { click: _vm.toggleBatchAll }
                                            }),
                                            _c("span", {
                                              staticClass:
                                                "custom-control-indicator"
                                            })
                                          ]
                                        )
                                      ])
                                    : _c("span", [_vm._v(_vm._s(column.label))])
                                ])
                              ]
                            }
                          }
                        }),
                        {
                          key: "cell(thumb)",
                          fn: function(row) {
                            return [
                              _c("img", {
                                staticClass: "responsive",
                                attrs: {
                                  src: row.item.thumb
                                    ? row.item.thumb
                                    : "/images/none.jpg",
                                  width: "65"
                                }
                              })
                            ]
                          }
                        },
                        {
                          key: "cell(name)",
                          fn: function(row) {
                            return [
                              _vm._v("\n                " + _vm._s(row.value)),
                              _c("br"),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _vm._v("Status: " + _vm._s(row.item.status))
                              ])
                            ]
                          }
                        },
                        {
                          key: "cell(created_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(activated_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(settings)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                Contact Email: " +
                                  _vm._s(
                                    row.item.settings
                                      ? row.item.settings.communication_email
                                      : "n/a"
                                  ) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(balance)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                $" +
                                  _vm._s(_vm._f("dollar")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(actions)",
                          fn: function(row) {
                            return [
                              row.item.id != _vm.$store.state.user.location.id
                                ? _c("i", {
                                    staticClass: "float-right mt-0",
                                    class: {
                                      "ti-angle-double-down": !row.detailsShowing,
                                      "ti-angle-double-up": row.detailsShowing
                                    },
                                    on: {
                                      click: function($event) {
                                        $event.preventDefault()
                                        return row.toggleDetails($event)
                                      }
                                    }
                                  })
                                : _vm._e(),
                              _vm._v(" "),
                              _c("i", {
                                staticClass:
                                  "hotbox-icon hotbox-icon-preferences float-right mr-2",
                                on: {
                                  click: function($event) {
                                    return _vm.loadEditModal(
                                      row.item.id,
                                      row.item.name
                                    )
                                  }
                                }
                              }),
                              _vm._v(" "),
                              row.item.id != _vm.$store.state.user.location.id
                                ? _c("i", {
                                    staticClass:
                                      "hotbox-icon hotbox-icon-key-26 float-right mr-2",
                                    on: {
                                      click: function($event) {
                                        return _vm.jumpToLocation(row.item.id)
                                      }
                                    }
                                  })
                                : _vm._e()
                            ]
                          }
                        },
                        {
                          key: "row-details",
                          fn: function(row) {
                            return [
                              row.item.settings && _vm.schema.form.settings
                                ? _c(
                                    "div",
                                    { staticClass: "col-12" },
                                    _vm._l(
                                      _vm.schema.form.settings.sections,
                                      function(sect, sid) {
                                        return _c(
                                          "div",
                                          { staticClass: "mt-2" },
                                          [
                                            _c("h6", [
                                              _vm._v(_vm._s(sect.title))
                                            ]),
                                            _vm._v(" "),
                                            _vm._l(sect.properties, function(
                                              prop,
                                              pid
                                            ) {
                                              return row.item.settings[pid]
                                                ? _c(
                                                    "div",
                                                    {
                                                      staticClass: "col-12 row"
                                                    },
                                                    [
                                                      _c(
                                                        "div",
                                                        {
                                                          staticClass:
                                                            "col-sm-4 small"
                                                        },
                                                        [
                                                          _vm._v(
                                                            _vm._s(prop.title)
                                                          )
                                                        ]
                                                      ),
                                                      _vm._v(" "),
                                                      _c(
                                                        "div",
                                                        {
                                                          staticClass:
                                                            "col-sm-7 small"
                                                        },
                                                        [
                                                          _vm._v(
                                                            _vm._s(
                                                              row.item.settings[
                                                                pid
                                                              ]
                                                            )
                                                          )
                                                        ]
                                                      )
                                                    ]
                                                  )
                                                : _vm._e()
                                            })
                                          ],
                                          2
                                        )
                                      }
                                    ),
                                    0
                                  )
                                : _vm._e()
                            ]
                          }
                        },
                        _vm.gridData
                          ? {
                              key: "table-caption",
                              fn: function() {
                                return [
                                  _vm.gridData.data.length > 0
                                    ? _c("div", [
                                        _vm.gridData.meta
                                          ? _c("span", [
                                              _vm._v(
                                                "\n                        Showing " +
                                                  _vm._s(
                                                    _vm.gridData.meta.per_page *
                                                      (_vm.gridPage - 1) +
                                                      1
                                                  ) +
                                                  " to " +
                                                  _vm._s(
                                                    _vm.gridData.meta.per_page *
                                                      _vm.gridPage <
                                                      _vm.gridData.meta.total
                                                      ? _vm.gridData.meta
                                                          .per_page *
                                                          _vm.gridPage
                                                      : _vm.gridData.meta.total
                                                  ) +
                                                  " of " +
                                                  _vm._s(
                                                    _vm.gridData.meta.total
                                                  ) +
                                                  " " +
                                                  _vm._s(
                                                    _vm.schema.lang.items
                                                      ? _vm.schema.lang.items
                                                      : "Records"
                                                  ) +
                                                  "\n                    "
                                              )
                                            ])
                                          : _c("span", [
                                              _vm._v(
                                                "Showing All " +
                                                  _vm._s(
                                                    _vm.schema.lang.items
                                                      ? _vm.schema.lang.items
                                                      : "Records"
                                                  )
                                              )
                                            ]),
                                        _vm._v(" "),
                                        _c(
                                          "div",
                                          { staticClass: "table-pager-footer" },
                                          [
                                            _vm.gridData.meta.total > 0
                                              ? _c("b-pagination", {
                                                  attrs: {
                                                    "total-rows":
                                                      _vm.gridData.meta.total,
                                                    "per-page":
                                                      _vm.gridData.meta
                                                        .per_page,
                                                    "aria-controls":
                                                      "users_table"
                                                  },
                                                  model: {
                                                    value: _vm.gridPage,
                                                    callback: function($$v) {
                                                      _vm.gridPage = $$v
                                                    },
                                                    expression: "gridPage"
                                                  }
                                                })
                                              : _vm._e()
                                          ],
                                          1
                                        )
                                      ])
                                    : _vm._e()
                                ]
                              },
                              proxy: true
                            }
                          : null,
                        {
                          key: "empty",
                          fn: function() {
                            return [
                              !_vm.isLoading
                                ? _c("div", [
                                    _c("img", {
                                      attrs: {
                                        src: "/images/logo.png",
                                        alt: "No Results",
                                        width: "115"
                                      }
                                    }),
                                    _vm._v(" "),
                                    _c("h4", [
                                      _vm._v(
                                        "Hmm, There are currently no Results."
                                      )
                                    ])
                                  ])
                                : _c("div", { staticClass: "h-100" }, [
                                    _vm._v(" ")
                                  ])
                            ]
                          },
                          proxy: true
                        }
                      ],
                      null,
                      true
                    )
                  })
                : _vm._e()
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-modal",
            {
              ref: "editModal",
              attrs: {
                centered: "",
                "no-enforce-focus": true,
                size: "lg",
                "header-bg-variant": "light",
                "header-text-variant": "primary"
              },
              model: {
                value: _vm.editModal,
                callback: function($$v) {
                  _vm.editModal = $$v
                },
                expression: "editModal"
              }
            },
            [
              _c("template", { slot: "modal-header" }, [
                _c("i", {
                  staticClass: "modal-top-close fal ti-close",
                  on: {
                    click: function($event) {
                      _vm.editModal = !_vm.editModal
                    }
                  }
                }),
                _vm._v(" "),
                _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                  _vm._v(_vm._s(_vm.editTitle))
                ])
              ]),
              _vm._v(" "),
              _vm.editModal
                ? _c("edit-modal", {
                    attrs: {
                      id: _vm.editRef,
                      type: _vm.editType,
                      schema: _vm.schema
                    },
                    on: { refresh: _vm.refreshEditModal }
                  })
                : _vm._e(),
              _vm._v(" "),
              _c("template", { slot: "modal-footer" }, [
                _c(
                  "span",
                  {
                    staticClass: "btn-label btn-sm btn-light float-right",
                    on: {
                      click: function($event) {
                        _vm.editModal = !_vm.editModal
                      }
                    }
                  },
                  [_vm._v("Close")]
                )
              ])
            ],
            2
          )
        ],
        1
      )
    : _c(
        "div",
        [
          _c("loading", {
            attrs: { display: _vm.schema ? false : true, type: "loadPage" }
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
    return _c("div", { staticClass: "input-group-prepend" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-search-2" })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc&":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc& ***!
  \*********************************************************************************************************************************************************************************************************************************/
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
    ? _c("div", [
        _c(
          "form",
          {
            on: {
              change: function($event) {
                return _vm.autoSave()
              }
            }
          },
          [
            _c("div", { staticClass: "drsection-header" }, [
              _c("h5", [
                _vm._v("Step 1: Choose Link Source\n                    "),
                _vm.dbConnected
                  ? _c("span", { staticClass: "float-right small" }, [
                      _c("i", {
                        staticClass:
                          "hotbox-icon hotbox-icon-g-check show-green"
                      }),
                      _vm._v(
                        " " +
                          _vm._s(
                            _vm._f("renderValue")(
                              _vm.migrationType,
                              _vm.schema.form.migration_prompt.values
                            )
                          ) +
                          " Confirmed."
                      )
                    ])
                  : _vm._e()
              ])
            ]),
            _vm._v(" "),
            _c("transition", { attrs: { name: "hb-slide" } }, [
              !_vm.dbConnected
                ? _c(
                    "div",
                    [
                      _c(
                        "div",
                        { staticClass: "drsection-content mb-0" },
                        [
                          _c("form-select", {
                            attrs: { schema: _vm.schema.form.migration_prompt },
                            model: {
                              value: _vm.migrationType,
                              callback: function($$v) {
                                _vm.migrationType = $$v
                              },
                              expression: "migrationType"
                            }
                          })
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "transition",
                        { attrs: { name: "hb-slide" } },
                        [
                          !_vm.dbConnected
                            ? _c("form-sectionalobject", {
                                staticClass: "form-group-list mb-4",
                                attrs: {
                                  formData: _vm.item,
                                  schema: _vm.schema.form.migration_settings,
                                  scoped: _vm.migrationType
                                },
                                model: {
                                  value: _vm.item.migration_settings,
                                  callback: function($$v) {
                                    _vm.$set(
                                      _vm.item,
                                      "migration_settings",
                                      $$v
                                    )
                                  },
                                  expression: "item.migration_settings"
                                }
                              })
                            : _vm._e()
                        ],
                        1
                      ),
                      _vm._v(" "),
                      _c(
                        "div",
                        { staticClass: "col-12 text-center" },
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
                          _c(
                            "button",
                            {
                              staticClass: "btn btn-md btn-info",
                              on: {
                                click: function($event) {
                                  $event.preventDefault()
                                  return _vm.testConnection()
                                }
                              }
                            },
                            [
                              _c("spinner", {
                                staticClass: "float-left",
                                attrs: {
                                  isProcessing: _vm.isProcessing,
                                  isFullScreen: false,
                                  isLine: true,
                                  spinnerWidth: 25
                                }
                              }),
                              _vm._v(
                                "\n                     Test Connection & Continue"
                              )
                            ],
                            1
                          )
                        ],
                        1
                      )
                    ],
                    1
                  )
                : _vm._e()
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "drsection-header" }, [
              _c("h5", [
                _vm._v("Step 2: Confirm Options "),
                _vm.isReady
                  ? _c("span", { staticClass: "float-right small" }, [
                      _c("i", {
                        staticClass:
                          "hotbox-icon hotbox-icon-g-check show-green"
                      }),
                      _vm._v(" Options Confirmed.")
                    ])
                  : _vm._e()
              ])
            ]),
            _vm._v(" "),
            _vm.locationsFound.length
              ? _c(
                  "div",
                  {
                    staticClass: "form-group migration-options ml-3 mt-2 mb-3"
                  },
                  [
                    _c("div", { staticClass: "form-group col-12 mb-1 mt-1" }, [
                      _c("label", [
                        _c("strong", [_vm._v("License Number:")]),
                        _vm._v(" " + _vm._s(_vm.licensenum))
                      ])
                    ]),
                    _vm._v(" "),
                    _c("form-select", {
                      staticClass: "col-sm-6 mt-2 mb-2",
                      attrs: {
                        schema: Object.assign(
                          {},
                          _vm.schema.form.migration_location_id,
                          { values: _vm.locationsFound }
                        )
                      },
                      model: {
                        value: _vm.item.migration_settings.btdb_location_id,
                        callback: function($$v) {
                          _vm.$set(
                            _vm.item.migration_settings,
                            "btdb_location_id",
                            $$v
                          )
                        },
                        expression: "item.migration_settings.btdb_location_id"
                      }
                    }),
                    _vm._v(" "),
                    _vm.foundOtherLocations
                      ? _c("form-multiselect", {
                          staticClass: "col-sm-6 mt-2 mb-3",
                          attrs: {
                            schema: Object.assign(
                              {},
                              _vm.schema.form.migration_associated_licensenums,
                              { values: _vm.foundOtherLocations }
                            )
                          },
                          model: {
                            value:
                              _vm.item.migration_settings
                                .btdb_associated_licensenums,
                            callback: function($$v) {
                              _vm.$set(
                                _vm.item.migration_settings,
                                "btdb_associated_licensenums",
                                $$v
                              )
                            },
                            expression:
                              "item.migration_settings.btdb_associated_licensenums"
                          }
                        })
                      : _vm._e(),
                    _vm._v(" "),
                    _c("form-datetime", {
                      staticClass: "col-12 col-sm-4 mt-3 mb-4",
                      attrs: {
                        schema: _vm.schema.form.migration_backfill_date
                      },
                      model: {
                        value: _vm.item.migration_settings.backfill,
                        callback: function($$v) {
                          _vm.$set(_vm.item.migration_settings, "backfill", $$v)
                        },
                        expression: "item.migration_settings.backfill"
                      }
                    })
                  ],
                  1
                )
              : _vm._e(),
            _vm._v(" "),
            _vm.dbConnected && !_vm.locationsFound.length
              ? _c("h5", { staticClass: "w-100 text-center" }, [
                  _vm._v("Location Not Found")
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm._m(0),
            _vm._v(" "),
            _c("div", { staticStyle: { "text-align": "center" } }, [
              _vm.dbConnected
                ? _c(
                    "button",
                    {
                      staticClass: "btn btn-lg btn-primary",
                      attrs: { disabled: !_vm.isReady },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          return _vm.startMigration()
                        }
                      }
                    },
                    [
                      _c("spinner", {
                        staticClass: "float-left",
                        attrs: {
                          isProcessing: _vm.isSending,
                          isFullScreen: false,
                          isLine: true,
                          spinnerWidth: 25
                        }
                      }),
                      _vm._v(
                        "\n                     Start Migration\n                "
                      )
                    ],
                    1
                  )
                : _vm._e()
            ]),
            _vm._v(" "),
            _vm.schema.lang.migrate_comment
              ? _c(
                  "div",
                  { staticClass: "col-12 d-flex justify-content-center" },
                  [
                    _c(
                      "div",
                      { staticClass: "col-sm-10 text-center mt-4 mb-4" },
                      [
                        _c("div", { staticClass: "block-announce info" }, [
                          _vm._m(1),
                          _vm._v(" "),
                          _c("p", [
                            _vm._v(_vm._s(_vm.schema.lang.migrate_comment))
                          ])
                        ])
                      ]
                    )
                  ]
                )
              : _vm._e()
          ],
          1
        )
      ])
    : _vm._e()
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "drsection-header mt-2" }, [
      _c("h5", { staticClass: "mt-2" }, [
        _vm._v("Step 3: Start Migration Process..")
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "title" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-f-comment" }),
      _vm._v(" A Comment on Migrating..")
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8&":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8& ***!
  \******************************************************************************************************************************************************************************************************************************/
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
                return _vm.autoSave()
              }
            }
          },
          [
            _c(
              "fieldset",
              [
                _c("form-sectionalobject", {
                  staticClass: "form-group-list mb-4",
                  attrs: {
                    formData: _vm.item.settings,
                    schema: _vm.schema.form.settings,
                    disabled: _vm.$store.getters.userCan("Manage Location")
                      ? false
                      : true,
                    scoped: _vm.item.type
                  },
                  model: {
                    value: _vm.item.settings,
                    callback: function($$v) {
                      _vm.$set(_vm.item, "settings", $$v)
                    },
                    expression: "item.settings"
                  }
                }),
                _vm._v(" "),
                _vm._m(0),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "drsection-content" },
                  [
                    _c("form-text", {
                      attrs: { schema: _vm.schema.form.name },
                      model: {
                        value: _vm.item.name,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "name", $$v)
                        },
                        expression: "item.name"
                      }
                    }),
                    _vm._v(" "),
                    _c("form-text", {
                      attrs: { schema: _vm.schema.form.licensenum },
                      model: {
                        value: _vm.item.licensenum,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "licensenum", $$v)
                        },
                        expression: "item.licensenum"
                      }
                    }),
                    _vm._v(" "),
                    _c("form-search", {
                      attrs: {
                        defaultOption: _vm.$store.state.user.location.address,
                        schema: _vm.schema.form.addressbook_id
                      },
                      model: {
                        value: _vm.item.addressbook_id,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "addressbook_id", $$v)
                        },
                        expression: "item.addressbook_id"
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _vm._m(1),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "drsection-content" },
                  [
                    _c("form-text", {
                      attrs: { schema: _vm.schema.form.name },
                      model: {
                        value: _vm.item.name,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "name", $$v)
                        },
                        expression: "item.name"
                      }
                    }),
                    _vm._v(" "),
                    _c("form-text", {
                      attrs: { schema: _vm.schema.form.licensenum },
                      model: {
                        value: _vm.item.licensenum,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "licensenum", $$v)
                        },
                        expression: "item.licensenum"
                      }
                    }),
                    _vm._v(" "),
                    _c("form-search", {
                      attrs: {
                        defaultOption: _vm.$store.state.user.location.address,
                        schema: _vm.schema.form.addressbook_id
                      },
                      model: {
                        value: _vm.item.addressbook_id,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "addressbook_id", $$v)
                        },
                        expression: "item.addressbook_id"
                      }
                    })
                  ],
                  1
                ),
                _vm._v(" "),
                _vm.$store.getters.userCan("manage-server")
                  ? _c("div", { staticClass: "drsection-header" }, [
                      _c("h4", [
                        _vm._v(
                          "HotBox Admin Account Overrides for " +
                            _vm._s(_vm.item.name) +
                            ":"
                        )
                      ])
                    ])
                  : _vm._e(),
                _vm._v(" "),
                _vm.$store.getters.userCan("manage-server")
                  ? _c(
                      "div",
                      { staticClass: "drsection-content" },
                      [
                        _vm.$store.getters.userCan("manage-server")
                          ? _c("form-number", {
                              staticClass: "col-12 mt-2 mb-3",
                              attrs: {
                                schema: _vm.schema.form.admin_campaign_sms_limit
                              },
                              model: {
                                value: _vm.item.settings.campaign_sms_limit,
                                callback: function($$v) {
                                  _vm.$set(
                                    _vm.item.settings,
                                    "campaign_sms_limit",
                                    $$v
                                  )
                                },
                                expression: "item.settings.campaign_sms_limit"
                              }
                            })
                          : _vm._e()
                      ],
                      1
                    )
                  : _vm._e(),
                _vm._v(" "),
                _c("div", { staticClass: "col-12" }, [
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
                      })
                    ],
                    1
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "col-12 mt-3 mb-2" }, [
                  _vm.item.is_demo
                    ? _c("div", { staticClass: "block-announce warning" }, [
                        _vm._m(2),
                        _vm._v(" "),
                        _c("p", [
                          _vm._v(
                            "Heya, You are in Demo Mode.  No Outside communications will be sent."
                          )
                        ])
                      ])
                    : _vm._e(),
                  _vm._v(" "),
                  _c("div", { staticClass: "block-announce info mt-2 mb-3" }, [
                    _vm._m(3),
                    _vm._v(" "),
                    _c("p", { staticClass: "text-center" }, [
                      !_vm.apiToken
                        ? _c(
                            "a",
                            {
                              staticClass: "btn btn-md btn-light text-center",
                              on: { click: _vm.getToken }
                            },
                            [
                              _c("spinner", {
                                staticClass: "float-left",
                                attrs: {
                                  isProcessing: _vm.refreshingToken,
                                  isFullScreen: false,
                                  isLine: true,
                                  spinnerWidth: 25
                                }
                              }),
                              _vm._v(" Get a Fresh Token")
                            ],
                            1
                          )
                        : _vm._e()
                    ]),
                    _vm._v(" "),
                    _vm.apiToken
                      ? _c("blockquote", [
                          _vm._v(_vm._s(_vm.apiToken) + " "),
                          _c("i", {
                            class: {
                              "hotbox-icon hotbox-icon-refresh-69": !_vm.refreshingToken,
                              "hotbox-icon hotbox-icon-hourglass":
                                _vm.refreshingToken
                            },
                            on: { click: _vm.getToken }
                          })
                        ])
                      : _vm._e(),
                    _vm._v(" "),
                    _c("p", { staticClass: "text-center" }, [
                      _vm._v("This token will grant access (based on your "),
                      _c("b", [
                        _vm._v(_vm._s(_vm.$store.state.user.location.name))
                      ]),
                      _vm._v(
                        " credentials) to our API endpoints, which may be "
                      ),
                      _c("a", { attrs: { href: "", target: "_blank" } }, [
                        _vm._v("reviewed here")
                      ])
                    ])
                  ])
                ])
              ],
              1
            )
          ]
        )
      ])
    : _c(
        "div",
        [
          _c("loading", {
            attrs: {
              display: _vm.item && _vm.schema ? false : true,
              type: "loadPage"
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
    return _c("div", { staticClass: "drsection-header" }, [
      _c("h4", [_vm._v("Financial Settings")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "drsection-header" }, [
      _c("h4", [_vm._v("General Settings")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "title" }, [
      _c("i", {
        staticClass: "hotbox-icon hotbox-icon-presentation show-yellow"
      }),
      _vm._v(" You are now in Demo Mode..")
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "title" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-key-26" }),
      _vm._v(" Your API Access Token:")
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d&":
/*!***************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d& ***!
  \***************************************************************************************************************************************************************************************************************************/
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
        _c("h3", { staticClass: "item-title" }, [
          _vm._v(_vm._s(_vm.item.name))
        ]),
        _vm._v(" "),
        _vm.item.created_at
          ? _c("span", { staticClass: "description" }, [
              _vm._v(
                "\n    Created " +
                  _vm._s(
                    _vm._f("localDate")(_vm.item.created_at, "MM/DD/YYYY LTS")
                  ) +
                  "\n  "
              )
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.item.updated_at
          ? _c("span", { staticClass: "description" }, [
              _vm._v(
                "\n    Last updated " +
                  _vm._s(
                    _vm._f("localDate")(_vm.item.updated_at, "MM/DD/YYYY LTS")
                  ) +
                  "\n  "
              )
            ])
          : _vm._e(),
        _vm._v(" "),
        _vm.item.location
          ? _c("span", { staticClass: "description" }, [
              _vm._v("\n    For: " + _vm._s(_vm.item.location.name) + "\n  ")
            ])
          : _vm._e(),
        _vm._v(" "),
        _c("form", { staticClass: "mt-2", attrs: { autocomplete: "off" } }, [
          _c("fieldset", [
            _c("div", { staticClass: "form-row" }, [
              _c("div", { staticClass: "col-12 col-md-6" }, [
                _c("label", { attrs: { for: "item-name" } }, [_vm._v("Name")]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group" }, [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.item.name,
                        expression: "item.name"
                      },
                      {
                        name: "validate",
                        rawName: "v-validate",
                        value: "required",
                        expression: "'required'"
                      }
                    ],
                    ref: "nameInput",
                    staticClass: "form-control",
                    class: {
                      input: true,
                      "text-danger": _vm.errors.has("name")
                    },
                    attrs: {
                      id: "item-name",
                      "aria-describedby": "addon-right addon-left",
                      name: "name",
                      type: "text",
                      placeholder: "",
                      disabled: _vm.isFieldDisabled
                    },
                    domProps: { value: _vm.item.name },
                    on: {
                      focus: function($event) {
                        return $event.target.select()
                      },
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.item, "name", $event.target.value)
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
                          value: _vm.errors.has("name"),
                          expression: "errors.has('name')"
                        }
                      ],
                      staticClass: "form-text text-muted text-danger"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("name")))]
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-2" }, [
                _c("label", { attrs: { for: "item-due_date" } }, [
                  _vm._v("Due Date")
                ]),
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
                        id: "item-due_date",
                        name: "due_date",
                        "bootstrap-styling": true,
                        "input-class": "form-datepicker",
                        "disabled-picker": _vm.isFieldDisabled
                      },
                      model: {
                        value: _vm.item.due_date,
                        callback: function($$v) {
                          _vm.$set(_vm.item, "due_date", $$v)
                        },
                        expression: "item.due_date"
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
                            value: _vm.errors.has("due_date"),
                            expression: "errors.has('due_date')"
                          }
                        ],
                        staticClass: "form-text text-muted text-danger"
                      },
                      [_vm._v(_vm._s(_vm.errors.first("due_date")))]
                    )
                  ],
                  1
                )
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-2" }, [
                _c("label", { attrs: { for: "item-status" } }, [
                  _vm._v("Status")
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group" }, [
                  _c(
                    "select",
                    {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.item.status,
                          expression: "item.status"
                        },
                        {
                          name: "validate",
                          rawName: "v-validate",
                          value: "required",
                          expression: "'required'"
                        }
                      ],
                      staticClass: "form-control",
                      attrs: { id: "item-status", name: "status" },
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
                            _vm.item,
                            "status",
                            $event.target.multiple
                              ? $$selectedVal
                              : $$selectedVal[0]
                          )
                        }
                      }
                    },
                    [
                      _c("option", { attrs: { value: "" } }, [
                        _vm._v("Select a Status")
                      ]),
                      _vm._v(" "),
                      !_vm.isFieldDisabled
                        ? _c("option", { attrs: { value: "new" } }, [
                            _vm._v("New")
                          ])
                        : _vm._e(),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "started" } }, [
                        _vm._v("Started")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "completed" } }, [
                        _vm._v("Completed")
                      ]),
                      _vm._v(" "),
                      !_vm.isFieldDisabled
                        ? _c("option", { attrs: { value: "cancelled" } }, [
                            _vm._v("Cancelled")
                          ])
                        : _vm._e()
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "span",
                    {
                      directives: [
                        {
                          name: "show",
                          rawName: "v-show",
                          value: _vm.errors.has("status"),
                          expression: "errors.has('status')"
                        }
                      ],
                      staticClass: "form-text text-muted text-danger"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("status")))]
                  )
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-2" }, [
                _c("label", { attrs: { for: "item-priority" } }, [
                  _vm._v("Priority")
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group" }, [
                  _c(
                    "select",
                    {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.item.priority,
                          expression: "item.priority"
                        },
                        {
                          name: "validate",
                          rawName: "v-validate",
                          value: "required",
                          expression: "'required'"
                        }
                      ],
                      staticClass: "form-control",
                      attrs: {
                        id: "item-priority",
                        name: "priority",
                        disabled: _vm.isFieldDisabled
                      },
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
                            _vm.item,
                            "priority",
                            $event.target.multiple
                              ? $$selectedVal
                              : $$selectedVal[0]
                          )
                        }
                      }
                    },
                    [
                      _c("option", { attrs: { value: "" } }, [
                        _vm._v("Select a Priority")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "1" } }, [_vm._v("Low")]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "2" } }, [
                        _vm._v("Medium")
                      ]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "3" } }, [_vm._v("High")]),
                      _vm._v(" "),
                      _c("option", { attrs: { value: "4" } }, [
                        _vm._v("Urgent")
                      ])
                    ]
                  ),
                  _vm._v(" "),
                  _c(
                    "span",
                    {
                      directives: [
                        {
                          name: "show",
                          rawName: "v-show",
                          value: _vm.errors.has("priority"),
                          expression: "errors.has('priority')"
                        }
                      ],
                      staticClass: "form-text text-muted text-danger"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("priority")))]
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "form-row" },
              [
                _c("div", { staticClass: "col-12 col-md-12" }, [
                  _c("label", { attrs: { for: "item-description" } }, [
                    _vm._v("Description")
                  ]),
                  _vm._v(" "),
                  _c("div", { staticClass: "form-group" }, [
                    _c("textarea", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.item.description,
                          expression: "item.description"
                        },
                        {
                          name: "validate",
                          rawName: "v-validate",
                          value: "required",
                          expression: "'required'"
                        }
                      ],
                      ref: "descriptionInput",
                      staticClass: "form-control",
                      class: {
                        input: true,
                        "text-danger": _vm.errors.has("description")
                      },
                      attrs: {
                        id: "item-description",
                        "aria-describedby": "addon-right addon-left",
                        name: "description",
                        placeholder:
                          "Please enter detailed instructions needed to complete the task.",
                        disabled: _vm.isFieldDisabled,
                        rows: "10"
                      },
                      domProps: { value: _vm.item.description },
                      on: {
                        focus: function($event) {
                          return $event.target.select()
                        },
                        input: function($event) {
                          if ($event.target.composing) {
                            return
                          }
                          _vm.$set(_vm.item, "description", $event.target.value)
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
                            value: _vm.errors.has("description"),
                            expression: "errors.has('description')"
                          }
                        ],
                        staticClass: "form-text text-muted text-danger"
                      },
                      [_vm._v(_vm._s(_vm.errors.first("description")))]
                    )
                  ])
                ]),
                _vm._v(" "),
                _vm.schema
                  ? _c(
                      "div",
                      { staticClass: "col-12", staticStyle: { clear: "both" } },
                      [
                        _c("label", { attrs: { for: "item-assignees" } }, [
                          _vm._v("Assigned To")
                        ]),
                        _vm._v(" "),
                        _c(
                          "div",
                          { staticClass: "form-group" },
                          [
                            _c("multiselect", {
                              directives: [
                                {
                                  name: "validate",
                                  rawName: "v-validate",
                                  value: "required",
                                  expression: "'required'"
                                }
                              ],
                              attrs: {
                                name: "assignees",
                                "deselect-label": "Remove Assignee",
                                placeholder: "Click to Select",
                                searchable: true,
                                "allow-empty": true,
                                label: "name",
                                "track-by": "user_id",
                                disabled: _vm.isFieldDisabled,
                                multiple: true,
                                options: _vm.getOptionsArray()
                              },
                              model: {
                                value: _vm.item.assignees,
                                callback: function($$v) {
                                  _vm.$set(_vm.item, "assignees", $$v)
                                },
                                expression: "item.assignees"
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
                                    value: _vm.errors.has("assignees"),
                                    expression: "errors.has('assignees')"
                                  }
                                ],
                                staticClass: "form-text text-muted text-danger"
                              },
                              [_vm._v(_vm._s(_vm.errors.first("assignees")))]
                            )
                          ],
                          1
                        )
                      ]
                    )
                  : _vm._e(),
                _vm._v(" "),
                _c("hr"),
                _vm._v(" "),
                _vm.item.attachments
                  ? _c("label", [_vm._v("Attachments")])
                  : _vm._e(),
                _vm._v(" "),
                _vm._l(_vm.item.attachments, function(attachment) {
                  return _c("div", { staticClass: "col-12 mt-2" }, [
                    _c(
                      "a",
                      { attrs: { href: attachment.url, target: "_blank" } },
                      [_vm._v(_vm._s(attachment.name))]
                    )
                  ])
                }),
                _vm._v(" "),
                _c(
                  "div",
                  { staticClass: "col-12 mt-2" },
                  [
                    _c("vue-dropzone", {
                      ref: "myVueDropzone",
                      attrs: { id: "dropzone", options: _vm.dropzoneOptions },
                      on: { "vdropzone-success": _vm.handleFileChange }
                    })
                  ],
                  1
                )
              ],
              2
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
                  _c(
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
                ],
                1
              )
            ])
          ])
        ])
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775& ***!
  \***********************************************************************************************************************************************************************************************************************/
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
  return _vm.schema
    ? _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("form", [
            _c("div", { staticClass: "table-filter-row" }, [
              _c(
                "div",
                {
                  staticClass: "filter-search",
                  class: { active: _vm.gridSearch }
                },
                [
                  _c("div", { staticClass: "input-group" }, [
                    _vm._m(0),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.gridSearch,
                          expression: "gridSearch"
                        }
                      ],
                      staticClass: "form-control border-left-0",
                      attrs: {
                        type: "text",
                        placeholder:
                          _vm.schema.lang.searchPrompt || "Search Grid"
                      },
                      domProps: { value: _vm.gridSearch },
                      on: {
                        input: [
                          function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.gridSearch = $event.target.value
                          },
                          _vm.searchGrid
                        ],
                        keydown: function($event) {
                          if (
                            !$event.type.indexOf("key") &&
                            _vm._k(
                              $event.keyCode,
                              "enter",
                              13,
                              $event.key,
                              "Enter"
                            )
                          ) {
                            return null
                          }
                          $event.preventDefault()
                          return _vm.searchGrid($event)
                        }
                      }
                    })
                  ])
                ]
              ),
              _vm._v(" "),
              _vm.gridFilters
                ? _c(
                    "div",
                    { staticClass: "filters" },
                    [
                      _vm.gridData
                        ? _c("filter-more", {
                            attrs: {
                              meta: _vm.gridData.meta,
                              schema: _vm.schema,
                              gridFilters: _vm.gridFilters,
                              columns: _vm.gridColumns,
                              isDownloading: _vm.isDownloading
                            },
                            on: {
                              "update:gridFilters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:grid-filters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:columns": function($event) {
                                _vm.gridColumns = $event
                              },
                              downloadExport: _vm.downloadExport
                            }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "wherein"
                          ? _c("filter-in", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      }),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "tabular"
                          ? _c("filter-tabs", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      })
                    ],
                    2
                  )
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c("loading", {
            attrs: { display: _vm.isLoading, type: "loadGrid" }
          }),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "bo-slide" } },
            [
              _vm.schema && _vm.gridFilters
                ? _c("b-table", {
                    attrs: {
                      striped: "",
                      hover: "",
                      id: "tasks_table",
                      "primary-key": "id",
                      items: _vm.gridData ? _vm.gridData.data : [],
                      fields: _vm.columnsVisible,
                      busy: _vm.isLoading,
                      "show-empty": true,
                      "sort-by": _vm.gridFilters.sortBy,
                      "sort-desc": _vm.gridFilters.orderDesc,
                      "no-local-sorting": true,
                      "no-local-filtering": true,
                      "per-page": 0,
                      tbodyTrClass: "grid-table-rows",
                      responsive: "md",
                      stacked: "sm"
                    },
                    on: {
                      "update:busy": function($event) {
                        _vm.isLoading = $event
                      },
                      "update:sortBy": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sort-by": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sortDesc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      },
                      "update:sort-desc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      }
                    },
                    scopedSlots: _vm._u(
                      [
                        {
                          key: "cell(due_date)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(created_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(updated_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(location_id)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(row.item.location.name) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(priority)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(row.item.priority_name) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(assignees)",
                          fn: function(row) {
                            return _vm._l(row.item.assignees, function(
                              assignee
                            ) {
                              return _c("span", [
                                _vm._v(
                                  "\n                 " + _vm._s(assignee.name)
                                ),
                                _c("br")
                              ])
                            })
                          }
                        },
                        {
                          key: "cell(actions)",
                          fn: function(row) {
                            return [
                              _c("div", { staticClass: "dropdown" }, [
                                _c(
                                  "a",
                                  {
                                    attrs: {
                                      "data-toggle": "dropdown",
                                      "aria-haspopup": "true",
                                      "aria-expanded": "false"
                                    }
                                  },
                                  [_c("i", { staticClass: "ti-more-alt" })]
                                ),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  {
                                    staticClass:
                                      "dropdown-menu tight dropdown-menu-right"
                                  },
                                  [
                                    _c(
                                      "router-link",
                                      {
                                        staticClass: "dropdown-item",
                                        attrs: {
                                          to: {
                                            name:
                                              _vm.model.toLowerCase() + "_edit",
                                            params: { id: row.item.id }
                                          },
                                          tag: "a"
                                        }
                                      },
                                      [
                                        _c("i", {
                                          staticClass:
                                            "hotbox-icon hotbox-icon-pencil"
                                        }),
                                        _vm._v(
                                          " Manage " +
                                            _vm._s(_vm._f("ucwords")(_vm.model))
                                        )
                                      ]
                                    ),
                                    _vm._v(" "),
                                    row.item.created_by.id ===
                                    _vm.$store.state.user.id
                                      ? _c(
                                          "a",
                                          {
                                            staticClass:
                                              "dropdown-item action-danger",
                                            attrs: { href: "" },
                                            on: {
                                              click: function($event) {
                                                $event.preventDefault()
                                                return _vm.confirmDelete(
                                                  row.item.id
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "hotbox-icon hotbox-icon-trash-round"
                                            }),
                                            _vm._v(
                                              " Archive " +
                                                _vm._s(
                                                  _vm._f("ucwords")(_vm.model)
                                                )
                                            )
                                          ]
                                        )
                                      : _vm._e()
                                  ],
                                  1
                                )
                              ])
                            ]
                          }
                        },
                        _vm.gridData
                          ? {
                              key: "table-caption",
                              fn: function() {
                                return [
                                  _vm.gridData.data.length > 0
                                    ? _c(
                                        "div",
                                        { staticClass: "paginate-foot" },
                                        [
                                          _vm.gridData.meta
                                            ? _c("span", [
                                                _vm._v(
                                                  "\n                        Showing " +
                                                    _vm._s(
                                                      _vm.gridData.meta
                                                        .per_page *
                                                        (_vm.gridPage - 1) +
                                                        1
                                                    ) +
                                                    " to " +
                                                    _vm._s(
                                                      _vm.gridData.meta
                                                        .per_page *
                                                        _vm.gridPage <
                                                        _vm.gridData.meta.total
                                                        ? _vm.gridData.meta
                                                            .per_page *
                                                            _vm.gridPage
                                                        : _vm.gridData.meta
                                                            .total
                                                    ) +
                                                    " of " +
                                                    _vm._s(
                                                      _vm.gridData.meta.total
                                                    ) +
                                                    " " +
                                                    _vm._s(
                                                      _vm.schema.lang.items
                                                        ? _vm.schema.lang.items
                                                        : "Records"
                                                    ) +
                                                    "\n                    "
                                                )
                                              ])
                                            : _c("span", [
                                                _vm._v(
                                                  "Showing All " +
                                                    _vm._s(
                                                      _vm.schema.lang.items
                                                        ? _vm.schema.lang.items
                                                        : "Records"
                                                    )
                                                )
                                              ]),
                                          _vm._v(" "),
                                          _c(
                                            "div",
                                            {
                                              staticClass: "table-pager-footer"
                                            },
                                            [
                                              _vm.gridData.meta.total > 0
                                                ? _c("b-pagination", {
                                                    attrs: {
                                                      "total-rows":
                                                        _vm.gridData.meta.total,
                                                      "per-page":
                                                        _vm.gridData.meta
                                                          .per_page,
                                                      "aria-controls":
                                                        "tasks_table"
                                                    },
                                                    model: {
                                                      value: _vm.gridPage,
                                                      callback: function($$v) {
                                                        _vm.gridPage = $$v
                                                      },
                                                      expression: "gridPage"
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        ]
                                      )
                                    : _vm._e()
                                ]
                              },
                              proxy: true
                            }
                          : null,
                        {
                          key: "empty",
                          fn: function() {
                            return [
                              !_vm.isLoading
                                ? _c("div", [
                                    _c("img", {
                                      attrs: {
                                        src: "/images/logo.png",
                                        alt: "No Results",
                                        width: "115"
                                      }
                                    }),
                                    _vm._v(" "),
                                    _c("h4", [
                                      _vm._v(
                                        "Hmm, There are currently no Results."
                                      )
                                    ])
                                  ])
                                : _c("div", { staticClass: "h-100" }, [
                                    _vm._v(" ")
                                  ])
                            ]
                          },
                          proxy: true
                        }
                      ],
                      null,
                      true
                    )
                  })
                : _vm._e()
            ],
            1
          )
        ],
        1
      )
    : _c(
        "div",
        [
          _c("loading", {
            attrs: { display: _vm.schema ? false : true, type: "loadPage" }
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
    return _c("div", { staticClass: "input-group-prepend" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-search-3" })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9&":
/*!*****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9& ***!
  \*****************************************************************************************************************************************************************************************************************************/
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
              submit: function($event) {
                return _vm.createItem()
              }
            }
          },
          [
            _c(
              "fieldset",
              [
                _vm._m(0),
                _vm._v(" "),
                _c("div", { staticClass: "form-group-list mt-4" }, [
                  _vm._m(1),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.item.email,
                        expression: "item.email"
                      },
                      {
                        name: "validate",
                        rawName: "v-validate",
                        value: "required|email",
                        expression: "'required|email'"
                      }
                    ],
                    staticClass: "form-control",
                    class: { "val-danger-input": _vm.errors.has("email") },
                    attrs: {
                      type: "text",
                      name: "email",
                      placeholder: "User Email"
                    },
                    domProps: { value: _vm.item.email },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.item, "email", $event.target.value)
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
                          value: _vm.errors.has("email"),
                          expression: "errors.has('email')"
                        }
                      ],
                      staticClass: "form-text text-muted val-danger-text"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("email")))]
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group-list mt-4" }, [
                  _vm._m(2),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.item.password,
                        expression: "item.password"
                      },
                      {
                        name: "validate",
                        rawName: "v-validate",
                        value: "required",
                        expression: "'required'"
                      }
                    ],
                    ref: "password",
                    staticClass: "form-control",
                    class: { "val-danger-input": _vm.errors.has("password") },
                    attrs: {
                      type: "password",
                      name: "password",
                      placeholder: "New Password"
                    },
                    domProps: { value: _vm.item.password },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.item, "password", $event.target.value)
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
                          value: _vm.errors.has("password"),
                          expression: "errors.has('password')"
                        }
                      ],
                      staticClass: "form-text text-muted val-danger-text"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("password")))]
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group-list mt-4 mb-4" }, [
                  _vm._m(3),
                  _vm._v(" "),
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.item.password_confirmation,
                        expression: "item.password_confirmation"
                      },
                      {
                        name: "validate",
                        rawName: "v-validate",
                        value: "required|confirmed:password",
                        expression: "'required|confirmed:password'"
                      }
                    ],
                    staticClass: "form-control",
                    class: { "val-danger-input": _vm.errors.has("password") },
                    attrs: {
                      type: "password",
                      name: "password_confirmation",
                      placeholder: "Confirm New Password*",
                      "data-vv-as": "password"
                    },
                    domProps: { value: _vm.item.password_confirmation },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(
                          _vm.item,
                          "password_confirmation",
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
                          value: _vm.errors.has("password_confirmation"),
                          expression: "errors.has('password_confirmation')"
                        }
                      ],
                      staticClass: "form-text text-muted val-danger-text"
                    },
                    [_vm._v(_vm._s(_vm.errors.first("password_confirmation")))]
                  )
                ]),
                _vm._v(" "),
                _vm._l(_vm.schema.form, function(formItem, ele) {
                  return _c(
                    "div",
                    { staticClass: "form-group-list mt-4 clearfix" },
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
                      formItem.type == "select"
                        ? _c("form-select", {
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
                      formItem.type == "checklist"
                        ? _c("form-checklist", {
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
                      _vm.item.status == "pending_activation" &&
                      formItem.name == "status"
                        ? _c("div", { staticClass: "block-announce warning" }, [
                            _vm._m(4, true),
                            _vm._v(" "),
                            _c("p", [
                              _vm._v(
                                "Upon creation, we will email your user an activation link.  Once clicked, they may login with their credentials above."
                              )
                            ])
                          ])
                        : _vm._e()
                    ],
                    1
                  )
                }),
                _vm._v(" "),
                _c("div", { staticClass: "col-12 clearfix mt-4" }, [
                  _c(
                    "div",
                    { staticClass: "drsection-content" },
                    [
                      _c("auto-save", {
                        attrs: { type: "save", state: _vm.itemState },
                        on: {
                          autoSave: function($event) {
                            return _vm.createItem()
                          }
                        }
                      }),
                      _vm._v(" "),
                      _c(
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
                    ],
                    1
                  )
                ])
              ],
              2
            )
          ]
        )
      ])
    : _c(
        "div",
        [
          _c("loading", {
            attrs: {
              display: _vm.schema && _vm.item ? false : true,
              type: "loadAll"
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
    return _c("h3", [
      _vm._v("\n                Add a new Staff Member"),
      _c("br"),
      _vm._v(" "),
      _c("span", { staticClass: "small" }, [_vm._v("Location User")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "email" } }, [
      _vm._v("User Email (used as username/login) "),
      _c("span", { staticClass: "show-red small" }, [_vm._v(" *(Required)")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "password" } }, [
      _vm._v("Users Password (they, or you, can change this at any point) "),
      _c("span", { staticClass: "show-red small" }, [_vm._v(" *(Required)")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "password" } }, [
      _vm._v("Confirm above Password "),
      _c("span", { staticClass: "show-red small" }, [_vm._v(" *(Required)")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "title" }, [
      _c("i", { staticClass: "fal fa-envelope-open-text" }),
      _vm._v(" User will have to Activate.")
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a&":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a& ***!
  \****************************************************************************************************************************************************************************************************************************/
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
    ? _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("div", { staticClass: "nav-tabs-header mb-4" }, [
            _c("ul", { staticClass: "nav nav-tabs nav-tabs-custom" }, [
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "edit" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "edit"
                        }
                      }
                    },
                    [_vm._v("Profile")]
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "password" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "password"
                        }
                      }
                    },
                    [
                      _c("i", { staticClass: "fal fa-key" }),
                      _vm._v(" Password")
                    ]
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "pin" },
                  on: {
                    click: function($event) {
                      return _vm.hasPermissions(
                        _vm.item.id,
                        "Store Admin Update"
                      )
                    }
                  }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "pin"
                        }
                      }
                    },
                    [_c("i", { staticClass: "fal fa-key" }), _vm._v(" PIN")]
                  )
                ]
              )
            ])
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "edit"
              ? _c("div", {}, [
                  _c(
                    "form",
                    {
                      staticClass: "col-12 modal-form",
                      on: {
                        change: function($event) {
                          return _vm.autoSave()
                        }
                      }
                    },
                    [
                      _c(
                        "fieldset",
                        [
                          _c("div", { staticClass: "clearfix" }, [
                            _c(
                              "div",
                              {
                                staticClass: "col-sm-5 text-center float-left"
                              },
                              [
                                _c("img", {
                                  staticClass: "responsive",
                                  attrs: {
                                    src: _vm.item.avatar
                                      ? _vm.item.avatar
                                      : _vm.schema.model.avatar,
                                    width: "85%"
                                  }
                                })
                              ]
                            ),
                            _vm._v(" "),
                            _c("div", { staticClass: "col-sm-7 float-left" }, [
                              _c("h3", [_vm._v(_vm._s(_vm.item.name))]),
                              _vm._v(" "),
                              _c(
                                "a",
                                {
                                  staticClass: "d-block mb-2",
                                  attrs: { href: "" },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      return _vm.downloadExportFile(
                                        _vm.item.id,
                                        "pdf"
                                      )
                                    }
                                  }
                                },
                                [
                                  _vm.isDownloading
                                    ? _c("i", {
                                        staticClass:
                                          "far fa-circle-notch fa-spin"
                                      })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  _c("i", { staticClass: "far fa-file-pdf" }),
                                  _vm._v(" Download Profile")
                                ]
                              ),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _vm._v(
                                  "Created on: " +
                                    _vm._s(
                                      _vm._f("localDate")(_vm.item.created_at)
                                    )
                                )
                              ]),
                              _c("br"),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _vm._v(
                                  "Last Updated on: " +
                                    _vm._s(
                                      _vm._f("localDate")(_vm.item.updated_at)
                                    )
                                )
                              ])
                            ])
                          ]),
                          _vm._v(" "),
                          _vm._l(_vm.schema.form, function(formItem, ele) {
                            return _c("div", { staticClass: "mt-4 clearfix" }, [
                              formItem.type == "object"
                                ? _c(
                                    "div",
                                    { staticClass: "form-group-list" },
                                    _vm._l(formItem.sections, function(
                                      section,
                                      skey
                                    ) {
                                      return _c(
                                        "div",
                                        [
                                          _c("h5", { staticClass: "mb-2" }, [
                                            _vm._v(
                                              "Settings - " +
                                                _vm._s(section.title)
                                            )
                                          ]),
                                          _vm._v(" "),
                                          _vm._l(section.properties, function(
                                            property,
                                            pname
                                          ) {
                                            return _c(
                                              "p",
                                              { staticClass: "mt-1" },
                                              [
                                                property.type == "text"
                                                  ? _c("form-text", {
                                                      attrs: {
                                                        schema: property
                                                      },
                                                      model: {
                                                        value:
                                                          _vm.item[ele][pname],
                                                        callback: function(
                                                          $$v
                                                        ) {
                                                          _vm.$set(
                                                            _vm.item[ele],
                                                            pname,
                                                            $$v
                                                          )
                                                        },
                                                        expression:
                                                          "item[ele][pname]"
                                                      }
                                                    })
                                                  : _vm._e(),
                                                _vm._v(" "),
                                                property.type == "datetime"
                                                  ? _c("form-datetime", {
                                                      attrs: {
                                                        schema: property
                                                      },
                                                      model: {
                                                        value:
                                                          _vm.item[ele][pname],
                                                        callback: function(
                                                          $$v
                                                        ) {
                                                          _vm.$set(
                                                            _vm.item[ele],
                                                            pname,
                                                            $$v
                                                          )
                                                        },
                                                        expression:
                                                          "item[ele][pname]"
                                                      }
                                                    })
                                                  : _vm._e()
                                              ],
                                              1
                                            )
                                          })
                                        ],
                                        2
                                      )
                                    }),
                                    0
                                  )
                                : _c(
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
                                      formItem.type == "select"
                                        ? _c("form-select", {
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
                                      formItem.type == "checklist"
                                        ? _c("form-checklist", {
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
                                      formItem.name == "status" &&
                                      _vm.item.status == "pending_activation"
                                        ? _c(
                                            "a",
                                            {
                                              staticClass: "float-right",
                                              attrs: { href: "" },
                                              on: {
                                                click: function($event) {
                                                  $event.preventDefault()
                                                  return _vm.sendActivation()
                                                }
                                              }
                                            },
                                            [
                                              _c("i", {
                                                class: {
                                                  "ti-email":
                                                    _vm.sendState == "send",
                                                  "far fa-circle-notch fa-spin":
                                                    _vm.sendState ==
                                                    "preparing..",
                                                  "far fa-check-circle show-green":
                                                    _vm.sendState == "sending"
                                                }
                                              }),
                                              _vm._v(
                                                " " +
                                                  _vm._s(
                                                    _vm._f("ucwords")(
                                                      _vm.sendState
                                                    )
                                                  ) +
                                                  " Activation Link Email »\n                    "
                                              )
                                            ]
                                          )
                                        : _vm._e()
                                    ],
                                    1
                                  )
                            ])
                          }),
                          _vm._v(" "),
                          _c(
                            "div",
                            { staticClass: "col-12 mt-4" },
                            [
                              _c("auto-save", {
                                attrs: { type: "save", state: _vm.itemState },
                                on: {
                                  autoSave: function($event) {
                                    return _vm.autoSave(true)
                                  }
                                }
                              })
                            ],
                            1
                          )
                        ],
                        2
                      )
                    ]
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "password"
              ? _c("div", {}, [
                  _vm.$store.getters.userCan("Employees Update") ||
                  _vm.item.role_ids == _vm.item.id
                    ? _c("div", [
                        _c(
                          "form",
                          {
                            staticClass: "col-12 modal-form",
                            on: {
                              submit: function($event) {
                                $event.preventDefault()
                                return _vm.updatePassword()
                              }
                            }
                          },
                          [
                            _c(
                              "fieldset",
                              [
                                _c("h5", { staticClass: "box-title m-b-20" }, [
                                  _vm._v("Change User Password")
                                ]),
                                _vm._v(" "),
                                _c("form-password", {
                                  staticClass: "col-12 col-sm-12",
                                  attrs: { schema: _vm.schema.form.password },
                                  on: {
                                    confirm: function(upd) {
                                      _vm.resetForm.password_confirmation = upd
                                    }
                                  },
                                  model: {
                                    value: _vm.resetForm.password,
                                    callback: function($$v) {
                                      _vm.$set(_vm.resetForm, "password", $$v)
                                    },
                                    expression: "resetForm.password"
                                  }
                                }),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  {
                                    staticClass:
                                      "form-group text-center mt-3 mb-4"
                                  },
                                  [
                                    _c(
                                      "button",
                                      {
                                        staticClass:
                                          "btn btn-info btn-md waves-effect waves-light",
                                        attrs: { type: "submit" }
                                      },
                                      [
                                        _c("i", {
                                          class: {
                                            "far fa-circle-notch fa-spin":
                                              _vm.resetState == "resetting",
                                            "far fa-check-circle show-green":
                                              _vm.resetState == "resetted"
                                          }
                                        }),
                                        _vm._v(
                                          " " +
                                            _vm._s(
                                              _vm._f("ucwords")(_vm.resetState)
                                            ) +
                                            " Password\n                        "
                                        )
                                      ]
                                    )
                                  ]
                                )
                              ],
                              1
                            )
                          ]
                        )
                      ])
                    : _c(
                        "div",
                        { staticClass: "col-12 text-center mt-3 mb-4" },
                        [
                          _c("h5", [
                            _vm._v(
                              "Were Sorry, You do not have permission to change this users password."
                            )
                          ])
                        ]
                      )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "pin"
              ? _c("div", {}, [
                  _vm.$store.getters.userCan("Store Admin Update") &&
                  _vm.viewPINTab
                    ? _c("div", [
                        _c(
                          "form",
                          {
                            staticClass: "col-12 modal-form",
                            on: {
                              submit: function($event) {
                                $event.preventDefault()
                                return _vm.updatePIN()
                              }
                            }
                          },
                          [
                            _c(
                              "fieldset",
                              [
                                _c("h5", { staticClass: "box-title m-b-20" }, [
                                  _vm._v("Change Administrative User PIN")
                                ]),
                                _vm._v(" "),
                                _c("form-password", {
                                  staticClass: "col-12 col-sm-12",
                                  attrs: { schema: _vm.schema.form.pincode },
                                  on: {
                                    confirm: function(upd) {
                                      _vm.resetForm.pincode_confirmation = upd
                                    }
                                  },
                                  model: {
                                    value: _vm.resetForm.pincode,
                                    callback: function($$v) {
                                      _vm.$set(_vm.resetForm, "pincode", $$v)
                                    },
                                    expression: "resetForm.pincode"
                                  }
                                }),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  {
                                    staticClass:
                                      "form-group text-center mt-3 mb-4"
                                  },
                                  [
                                    _c(
                                      "button",
                                      {
                                        staticClass:
                                          "btn btn-round btn-info waves-effect waves-light",
                                        attrs: { type: "submit" }
                                      },
                                      [
                                        _c("i", {
                                          class: {
                                            "far fa-circle-notch fa-spin":
                                              _vm.resetState == "resetting",
                                            "far fa-check-circle show-green":
                                              _vm.resetState == "resetted"
                                          }
                                        }),
                                        _vm._v(
                                          " " +
                                            _vm._s(
                                              _vm._f("ucwords")(_vm.resetState)
                                            ) +
                                            " PIN\n                        "
                                        )
                                      ]
                                    )
                                  ]
                                )
                              ],
                              1
                            )
                          ]
                        )
                      ])
                    : _c(
                        "div",
                        { staticClass: "col-12 text-center mt-3 mb-4" },
                        [
                          _c("h5", [
                            _vm._v(
                              "This user account does not have the correct permissions to be given an adminstrative PIN."
                            )
                          ])
                        ]
                      )
                ])
              : _vm._e()
          ])
        ],
        1
      )
    : _c(
        "div",
        { staticClass: "no-content-block" },
        [
          _c("loading", {
            attrs: {
              display: _vm.item && _vm.schema ? false : true,
              type: "loadModal"
            }
          })
        ],
        1
      )
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef&":
/*!***********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef& ***!
  \***********************************************************************************************************************************************************************************************************************/
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
  return _vm.schema
    ? _c(
        "div",
        { staticClass: "col-12" },
        [
          _c("form", [
            _c("div", { staticClass: "table-filter-row" }, [
              _c(
                "div",
                {
                  staticClass: "filter-search",
                  class: { active: _vm.gridSearch }
                },
                [
                  _c("div", { staticClass: "input-group" }, [
                    _vm._m(0),
                    _vm._v(" "),
                    _c("input", {
                      directives: [
                        {
                          name: "model",
                          rawName: "v-model",
                          value: _vm.gridSearch,
                          expression: "gridSearch"
                        }
                      ],
                      staticClass: "form-control border-left-0",
                      attrs: {
                        type: "text",
                        placeholder:
                          _vm.schema.lang.searchPrompt || "Search Grid"
                      },
                      domProps: { value: _vm.gridSearch },
                      on: {
                        input: [
                          function($event) {
                            if ($event.target.composing) {
                              return
                            }
                            _vm.gridSearch = $event.target.value
                          },
                          _vm.searchGrid
                        ],
                        keydown: function($event) {
                          if (
                            !$event.type.indexOf("key") &&
                            _vm._k(
                              $event.keyCode,
                              "enter",
                              13,
                              $event.key,
                              "Enter"
                            )
                          ) {
                            return null
                          }
                          $event.preventDefault()
                          return _vm.searchGrid($event)
                        }
                      }
                    })
                  ])
                ]
              ),
              _vm._v(" "),
              _vm.gridFilters
                ? _c(
                    "div",
                    { staticClass: "filters" },
                    [
                      _vm.gridData
                        ? _c("filter-more", {
                            attrs: {
                              meta: _vm.gridData.meta,
                              schema: _vm.schema,
                              gridFilters: _vm.gridFilters,
                              columns: _vm.gridColumns,
                              isDownloading: _vm.isDownloading
                            },
                            on: {
                              "update:gridFilters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:grid-filters": function($event) {
                                _vm.gridFilters = $event
                              },
                              "update:columns": function($event) {
                                _vm.gridColumns = $event
                              },
                              downloadExport: _vm.downloadExport
                            }
                          })
                        : _vm._e(),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "wherein"
                          ? _c("filter-in", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      }),
                      _vm._v(" "),
                      _vm._l(_vm.schema.filters, function(filt, fkey) {
                        return filt.type == "tabular"
                          ? _c("filter-tabs", {
                              key: fkey,
                              attrs: {
                                schema: filt,
                                filter: _vm.gridFilters.filter[fkey]
                              },
                              on: {
                                update: function(upd) {
                                  _vm.gridFilters.filter[fkey] = upd
                                }
                              }
                            })
                          : _vm._e()
                      })
                    ],
                    2
                  )
                : _vm._e()
            ])
          ]),
          _vm._v(" "),
          _c("loading", {
            attrs: { display: _vm.isLoading, type: "loadGrid" }
          }),
          _vm._v(" "),
          _c(
            "transition",
            { attrs: { name: "bo-slide" } },
            [
              _vm.schema && _vm.gridFilters
                ? _c("b-table", {
                    attrs: {
                      striped: "",
                      hover: "",
                      id: "users_table",
                      "primary-key": "id",
                      items: _vm.gridData ? _vm.gridData.data : [],
                      fields: _vm.columnsVisible,
                      busy: _vm.isLoading,
                      "show-empty": true,
                      "sort-by": _vm.gridFilters.sortBy,
                      "sort-desc": _vm.gridFilters.orderDesc,
                      "no-local-sorting": true,
                      "no-local-filtering": true,
                      "per-page": 0,
                      tbodyTrClass: _vm.renderRowBg,
                      responsive: "md",
                      stacked: "sm"
                    },
                    on: {
                      "update:busy": function($event) {
                        _vm.isLoading = $event
                      },
                      "update:sortBy": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sort-by": function($event) {
                        return _vm.$set(_vm.gridFilters, "sortBy", $event)
                      },
                      "update:sortDesc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      },
                      "update:sort-desc": function($event) {
                        return _vm.$set(_vm.gridFilters, "orderDesc", $event)
                      }
                    },
                    scopedSlots: _vm._u(
                      [
                        _vm._l(_vm.schema.meta.fields, function(field, ind) {
                          return {
                            key: "head(field.key)",
                            fn: function(column) {
                              return [
                                _c("em", [
                                  field.icon
                                    ? _c("i", { class: field.icon })
                                    : _vm._e(),
                                  _vm._v(" "),
                                  field.description
                                    ? _c("i", {
                                        directives: [
                                          {
                                            name: "b-tooltip",
                                            rawName: "v-b-tooltip.hover",
                                            value: field.description,
                                            expression: "field.description",
                                            modifiers: { hover: true }
                                          }
                                        ],
                                        staticClass:
                                          "hotbox-icon hotbox-icon-c-question",
                                        attrs: { title: field.name }
                                      })
                                    : _vm._e(),
                                  _vm._v(
                                    " \n                " +
                                      _vm._s(column.label) +
                                      "\n              "
                                  )
                                ])
                              ]
                            }
                          }
                        }),
                        {
                          key: "cell(avatar)",
                          fn: function(row) {
                            return [
                              _c("img", {
                                staticClass: "responsive",
                                attrs: {
                                  src: row.item.avatar
                                    ? row.item.avatar
                                    : _vm.schema.model.avatar,
                                  width: "65"
                                }
                              })
                            ]
                          }
                        },
                        {
                          key: "cell(email)",
                          fn: function(row) {
                            return [
                              _vm._v("\n                " + _vm._s(row.value)),
                              _c("br"),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _c("b", [
                                  _vm._v(
                                    "As " +
                                      _vm._s(
                                        _vm._f("renderValue")(
                                          row.item.type,
                                          _vm.schema.form.type.values
                                        )
                                      ) +
                                      " \n                "
                                  ),
                                  _c(
                                    "i",
                                    {
                                      staticClass: "float-right",
                                      class: {
                                        "show-green":
                                          row.item.status == "activated",
                                        "show-red": row.item.status == "denied",
                                        "show-orange":
                                          row.item.status ==
                                          "pending_activation"
                                      }
                                    },
                                    [
                                      _vm._v(
                                        "\n                    (" +
                                          _vm._s(
                                            _vm._f("renderValue")(
                                              row.item.status,
                                              _vm.schema.form.status.values
                                            )
                                          ) +
                                          ")\n                "
                                      )
                                    ]
                                  )
                                ])
                              ])
                            ]
                          }
                        },
                        {
                          key: "cell(name)",
                          fn: function(row) {
                            return [
                              _vm._v("\n                " + _vm._s(row.value)),
                              _c("br"),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _vm._v(
                                  "Roles: " +
                                    _vm._s(_vm.renderRoleList(row.item.roles))
                                )
                              ])
                            ]
                          }
                        },
                        {
                          key: "cell(settings)",
                          fn: function(row) {
                            return [
                              row.item.settings
                                ? _c("span", [
                                    _vm._v(
                                      "\n                Contact: " +
                                        _vm._s(
                                          row.item.settings.contact_email
                                        ) +
                                        " " +
                                        _vm._s(row.item.settings.contact_phone)
                                    ),
                                    _c("br"),
                                    _vm._v(" "),
                                    row.item.type == "staff"
                                      ? _c("span", { staticClass: "small" }, [
                                          _vm._v(
                                            "Employee Lic: " +
                                              _vm._s(
                                                row.item.settings
                                                  .employee_licensenum
                                              ) +
                                              ", Hired on: " +
                                              _vm._s(
                                                _vm._f("localDate")(
                                                  row.item.settings
                                                    .employee_hired_on
                                                )
                                              )
                                          )
                                        ])
                                      : _vm._e()
                                  ])
                                : _c("span", [
                                    _c(
                                      "a",
                                      {
                                        attrs: { href: "" },
                                        on: {
                                          click: function($event) {
                                            $event.preventDefault()
                                            return _vm.loadModal(
                                              row.item.id,
                                              "edit"
                                            )
                                          }
                                        }
                                      },
                                      [_vm._v("Add Employee/Staff Settings »")]
                                    )
                                  ])
                            ]
                          }
                        },
                        {
                          key: "cell(created_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(updated_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(_vm._f("localDate")(row.value)) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(actions)",
                          fn: function(row) {
                            return [
                              _vm.$store.getters.userCan("Staff Update") ||
                              _vm.$store.state.user.id == row.item.id
                                ? _c("div", { staticClass: "dropdown" }, [
                                    _c(
                                      "a",
                                      {
                                        attrs: {
                                          "data-toggle": "dropdown",
                                          "aria-haspopup": "true",
                                          "aria-expanded": "false"
                                        }
                                      },
                                      [_c("i", { staticClass: "ti-more-alt" })]
                                    ),
                                    _vm._v(" "),
                                    _c(
                                      "div",
                                      {
                                        staticClass:
                                          "dropdown-menu tight dropdown-menu-right"
                                      },
                                      [
                                        _c(
                                          "a",
                                          {
                                            staticClass: "dropdown-item",
                                            attrs: { href: "" },
                                            on: {
                                              click: function($event) {
                                                $event.preventDefault()
                                                return _vm.loadModal(
                                                  row.item.id,
                                                  "edit"
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "hotbox-icon hotbox-icon-pencil"
                                            }),
                                            _vm._v(" Manage User Profile")
                                          ]
                                        ),
                                        _vm._v(" "),
                                        _c(
                                          "a",
                                          {
                                            staticClass: "dropdown-item",
                                            attrs: { href: "" },
                                            on: {
                                              click: function($event) {
                                                $event.preventDefault()
                                                return _vm.loadModal(
                                                  row.item.id,
                                                  "password"
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "hotbox-icon hotbox-icon-key-26"
                                            }),
                                            _vm._v(" Change Password")
                                          ]
                                        ),
                                        _vm._v(" "),
                                        row.item.status === "archived"
                                          ? _c(
                                              "a",
                                              {
                                                staticClass:
                                                  "dropdown-item action-danger",
                                                on: {
                                                  click: function($event) {
                                                    return _vm.unArchive(
                                                      row.item.id
                                                    )
                                                  }
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "hotbox-icon hotbox-icon-redo-81"
                                                }),
                                                _vm._v(" UnArchive User")
                                              ]
                                            )
                                          : _c(
                                              "a",
                                              {
                                                staticClass:
                                                  "dropdown-item action-danger",
                                                on: {
                                                  click: function($event) {
                                                    return _vm.confirmDelete(
                                                      row.item.id
                                                    )
                                                  }
                                                }
                                              },
                                              [
                                                _c("i", {
                                                  staticClass:
                                                    "hotbox-icon hotbox-icon-trash-round"
                                                }),
                                                _vm._v(" Archive User")
                                              ]
                                            )
                                      ]
                                    )
                                  ])
                                : _vm._e()
                            ]
                          }
                        },
                        _vm.gridData
                          ? {
                              key: "table-caption",
                              fn: function() {
                                return [
                                  _vm.gridData.data.length > 0
                                    ? _c(
                                        "div",
                                        { staticClass: "paginate-foot" },
                                        [
                                          _vm.gridData.meta
                                            ? _c(
                                                "span",
                                                [
                                                  _vm._v(
                                                    "\n                        Showing " +
                                                      _vm._s(
                                                        _vm.gridData.meta
                                                          .per_page *
                                                          (_vm.gridPage - 1) +
                                                          1
                                                      ) +
                                                      " to " +
                                                      _vm._s(
                                                        _vm.gridData.meta
                                                          .per_page *
                                                          _vm.gridPage <
                                                          _vm.gridData.meta
                                                            .total
                                                          ? _vm.gridData.meta
                                                              .per_page *
                                                              _vm.gridPage
                                                          : _vm.gridData.meta
                                                              .total
                                                      ) +
                                                      " of " +
                                                      _vm._s(
                                                        _vm.gridData.meta.total
                                                      ) +
                                                      " " +
                                                      _vm._s(
                                                        _vm.schema.lang.items
                                                          ? _vm.schema.lang
                                                              .items
                                                          : "Records"
                                                      ) +
                                                      "\n                        "
                                                  ),
                                                  _c("form-boolean", {
                                                    staticClass: "mt-1",
                                                    attrs: {
                                                      declared: _vm.gridArchive,
                                                      schema: {
                                                        name: "archived",
                                                        title:
                                                          "Include Archived Items"
                                                      }
                                                    },
                                                    on: {
                                                      input: function(upd) {
                                                        _vm.gridArchive = upd
                                                      }
                                                    }
                                                  })
                                                ],
                                                1
                                              )
                                            : _c("span", [
                                                _vm._v(
                                                  "Showing All " +
                                                    _vm._s(
                                                      _vm.schema.lang.items
                                                        ? _vm.schema.lang.items
                                                        : "Records"
                                                    )
                                                )
                                              ]),
                                          _vm._v(" "),
                                          _c(
                                            "div",
                                            {
                                              staticClass: "table-pager-footer"
                                            },
                                            [
                                              _vm.gridData.meta.total > 0
                                                ? _c("b-pagination", {
                                                    attrs: {
                                                      "total-rows":
                                                        _vm.gridData.meta.total,
                                                      "per-page":
                                                        _vm.gridData.meta
                                                          .per_page,
                                                      "aria-controls":
                                                        "users_table"
                                                    },
                                                    model: {
                                                      value: _vm.gridPage,
                                                      callback: function($$v) {
                                                        _vm.gridPage = $$v
                                                      },
                                                      expression: "gridPage"
                                                    }
                                                  })
                                                : _vm._e()
                                            ],
                                            1
                                          )
                                        ]
                                      )
                                    : _vm._e()
                                ]
                              },
                              proxy: true
                            }
                          : null,
                        {
                          key: "empty",
                          fn: function() {
                            return [
                              !_vm.isLoading
                                ? _c(
                                    "div",
                                    [
                                      _c("form-boolean", {
                                        staticClass: "mt-1",
                                        attrs: {
                                          declared: _vm.gridArchive,
                                          schema: {
                                            name: "archived",
                                            title: "Include Archived Items"
                                          }
                                        },
                                        on: {
                                          input: function(upd) {
                                            _vm.gridArchive = upd
                                          }
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("img", {
                                        attrs: {
                                          src: "/images/logo.png",
                                          alt: "No Results",
                                          width: "115"
                                        }
                                      }),
                                      _vm._v(" "),
                                      _c("h4", [
                                        _vm._v(
                                          "Hmm, There are currently no Results."
                                        )
                                      ])
                                    ],
                                    1
                                  )
                                : _c("div", { staticClass: "h-100" }, [
                                    _vm._v(" ")
                                  ])
                            ]
                          },
                          proxy: true
                        }
                      ],
                      null,
                      true
                    )
                  })
                : _vm._e()
            ],
            1
          ),
          _vm._v(" "),
          _c(
            "b-modal",
            {
              ref: "editModal",
              attrs: {
                centered: "",
                size: "lg",
                "header-bg-variant": "light",
                "header-text-variant": "primary"
              },
              model: {
                value: _vm.editModal,
                callback: function($$v) {
                  _vm.editModal = $$v
                },
                expression: "editModal"
              }
            },
            [
              _c("template", { slot: "modal-header" }, [
                _c("i", {
                  staticClass: "modal-top-close fal ti-close",
                  on: {
                    click: function($event) {
                      _vm.editModal = !_vm.editModal
                    }
                  }
                }),
                _vm._v(" "),
                _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                  _vm._v("Staff Management")
                ])
              ]),
              _vm._v(" "),
              _vm.editModal
                ? _c("edit-modal", {
                    attrs: { id: _vm.editRef, type: _vm.editType },
                    on: { refresh: _vm.refreshFromModal }
                  })
                : _vm._e(),
              _vm._v(" "),
              _c("template", { slot: "modal-footer" }, [
                _c(
                  "span",
                  {
                    staticClass: "btn-label btn-sm btn-light float-right",
                    on: {
                      click: function($event) {
                        _vm.editModal = !_vm.editModal
                      }
                    }
                  },
                  [_vm._v("Close")]
                )
              ])
            ],
            2
          )
        ],
        1
      )
    : _c(
        "div",
        [
          _c("loading", {
            attrs: { display: _vm.schema ? false : true, type: "loadPage" }
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
    return _c("div", { staticClass: "input-group-prepend" }, [
      _c("i", { staticClass: "hotbox-icon hotbox-icon-search-3" })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/batchEditModal.vue":
/*!***************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/batchEditModal.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./batchEditModal.vue?vue&type=template&id=45e9d186& */ "./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186&");
/* harmony import */ var _batchEditModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./batchEditModal.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _batchEditModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__["render"],
  _batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/addressbook/batchEditModal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_batchEditModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./batchEditModal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_batchEditModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186&":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./batchEditModal.vue?vue&type=template&id=45e9d186& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/batchEditModal.vue?vue&type=template&id=45e9d186&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_batchEditModal_vue_vue_type_template_id_45e9d186___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/editForm.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/editForm.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editForm.vue?vue&type=template&id=6d3e9c0b& */ "./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b&");
/* harmony import */ var _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editForm.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/addressbook/editForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b& ***!
  \****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=template&id=6d3e9c0b& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/editForm.vue?vue&type=template&id=6d3e9c0b&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_6d3e9c0b___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/grid.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/grid.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=0b11ebe3& */ "./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/addressbook/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3&":
/*!************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=0b11ebe3& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/addressbook/grid.vue?vue&type=template&id=0b11ebe3&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0b11ebe3___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/index.vue":
/*!******************************************************!*\
  !*** ./resources/js/components/views/auth/index.vue ***!
  \******************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.vue?vue&type=template&id=6ecafc37& */ "./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37&");
/* harmony import */ var _index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/index.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__["render"],
  _index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/index.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/index.vue?vue&type=script&lang=js&":
/*!*******************************************************************************!*\
  !*** ./resources/js/components/views/auth/index.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./index.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/index.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37&":
/*!*************************************************************************************!*\
  !*** ./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37& ***!
  \*************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./index.vue?vue&type=template&id=6ecafc37& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/index.vue?vue&type=template&id=6ecafc37&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_6ecafc37___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/locations/editModal.vue":
/*!********************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/editModal.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editModal.vue?vue&type=template&id=8c288c12& */ "./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12&");
/* harmony import */ var _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editModal.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/locations/editModal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=template&id=8c288c12& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/editModal.vue?vue&type=template&id=8c288c12&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_8c288c12___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/locations/grid.vue":
/*!***************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/grid.vue ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=fde1097c& */ "./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/locations/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js&":
/*!****************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c& ***!
  \**********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=fde1097c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/grid.vue?vue&type=template&id=fde1097c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_fde1097c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/locations/migration.vue":
/*!********************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/migration.vue ***!
  \********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./migration.vue?vue&type=template&id=0144c4fc& */ "./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc&");
/* harmony import */ var _migration_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./migration.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./migration.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _migration_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__["render"],
  _migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/locations/migration.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./migration.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css& ***!
  \*****************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./migration.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc&":
/*!***************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc& ***!
  \***************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./migration.vue?vue&type=template&id=0144c4fc& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/locations/migration.vue?vue&type=template&id=0144c4fc&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_migration_vue_vue_type_template_id_0144c4fc___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/settings/profile.vue":
/*!*****************************************************************!*\
  !*** ./resources/js/components/views/auth/settings/profile.vue ***!
  \*****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./profile.vue?vue&type=template&id=3aa82fe8& */ "./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8&");
/* harmony import */ var _profile_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./profile.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _profile_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__["render"],
  _profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/settings/profile.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js&":
/*!******************************************************************************************!*\
  !*** ./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_profile_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./profile.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/settings/profile.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_profile_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8&":
/*!************************************************************************************************!*\
  !*** ./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8& ***!
  \************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./profile.vue?vue&type=template&id=3aa82fe8& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/settings/profile.vue?vue&type=template&id=3aa82fe8&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_profile_vue_vue_type_template_id_3aa82fe8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/task/editForm.vue":
/*!**************************************************************!*\
  !*** ./resources/js/components/views/auth/task/editForm.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editForm.vue?vue&type=template&id=57047e9d& */ "./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d&");
/* harmony import */ var _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editForm.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editForm.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/task/editForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css& ***!
  \***********************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d&":
/*!*********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d& ***!
  \*********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editForm.vue?vue&type=template&id=57047e9d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/editForm.vue?vue&type=template&id=57047e9d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editForm_vue_vue_type_template_id_57047e9d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/task/grid.vue":
/*!**********************************************************!*\
  !*** ./resources/js/components/views/auth/task/grid.vue ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=1c43e775& */ "./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./grid.vue?vue&type=style&index=0&lang=css& */ "./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/task/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js&":
/*!***********************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css& ***!
  \*******************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=style&index=0&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=style&index=0&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775& ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=1c43e775& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/task/grid.vue?vue&type=template&id=1c43e775&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1c43e775___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/user/createForm.vue":
/*!****************************************************************!*\
  !*** ./resources/js/components/views/auth/user/createForm.vue ***!
  \****************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./createForm.vue?vue&type=template&id=4236eac9& */ "./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9&");
/* harmony import */ var _createForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./createForm.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _createForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__["render"],
  _createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/user/createForm.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_createForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createForm.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/createForm.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_createForm_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9& ***!
  \***********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./createForm.vue?vue&type=template&id=4236eac9& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/createForm.vue?vue&type=template&id=4236eac9&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_createForm_vue_vue_type_template_id_4236eac9___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/user/editModal.vue":
/*!***************************************************************!*\
  !*** ./resources/js/components/views/auth/user/editModal.vue ***!
  \***************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editModal.vue?vue&type=template&id=312b396a& */ "./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a&");
/* harmony import */ var _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editModal.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/user/editModal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js&":
/*!****************************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a& ***!
  \**********************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=template&id=312b396a& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/editModal.vue?vue&type=template&id=312b396a&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_312b396a___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/auth/user/grid.vue":
/*!**********************************************************!*\
  !*** ./resources/js/components/views/auth/user/grid.vue ***!
  \**********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=1ec4dfef& */ "./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/auth/user/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js&":
/*!***********************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef&":
/*!*****************************************************************************************!*\
  !*** ./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef& ***!
  \*****************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=1ec4dfef& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/auth/user/grid.vue?vue&type=template&id=1ec4dfef&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_1ec4dfef___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Addressbook.js":
/*!********************************************!*\
  !*** ./resources/js/models/Addressbook.js ***!
  \********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Addressbook; });
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



var Addressbook =
/*#__PURE__*/
function (_Model) {
  _inherits(Addressbook, _Model);

  function Addressbook() {
    _classCallCheck(this, Addressbook);

    return _possibleConstructorReturn(this, _getPrototypeOf(Addressbook).apply(this, arguments));
  }

  _createClass(Addressbook, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/addressbook';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Addressbook;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./resources/js/models/Location.js":
/*!*****************************************!*\
  !*** ./resources/js/models/Location.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Location; });
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



var Location =
/*#__PURE__*/
function (_Model) {
  _inherits(Location, _Model);

  function Location() {
    _classCallCheck(this, Location);

    return _possibleConstructorReturn(this, _getPrototypeOf(Location).apply(this, arguments));
  }

  _createClass(Location, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/location';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Location;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./resources/js/models/Locations.js":
/*!******************************************!*\
  !*** ./resources/js/models/Locations.js ***!
  \******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Locations; });
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



var Locations =
/*#__PURE__*/
function (_Model) {
  _inherits(Locations, _Model);

  function Locations() {
    _classCallCheck(this, Locations);

    return _possibleConstructorReturn(this, _getPrototypeOf(Locations).apply(this, arguments));
  }

  _createClass(Locations, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/locations';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Locations;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./resources/js/models/Task.js":
/*!*************************************!*\
  !*** ./resources/js/models/Task.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Task; });
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



var Task =
/*#__PURE__*/
function (_Model) {
  _inherits(Task, _Model);

  function Task() {
    _classCallCheck(this, Task);

    return _possibleConstructorReturn(this, _getPrototypeOf(Task).apply(this, arguments));
  }

  _createClass(Task, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/task';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Task;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ }),

/***/ "./resources/js/models/User.js":
/*!*************************************!*\
  !*** ./resources/js/models/User.js ***!
  \*************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return User; });
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



var User =
/*#__PURE__*/
function (_Model) {
  _inherits(User, _Model);

  function User() {
    _classCallCheck(this, User);

    return _possibleConstructorReturn(this, _getPrototypeOf(User).apply(this, arguments));
  }

  _createClass(User, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/users';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return User;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);