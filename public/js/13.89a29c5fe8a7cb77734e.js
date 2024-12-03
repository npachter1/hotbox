(window["webpackJsonp"] = window["webpackJsonp"] || []).push([[13],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var printd__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! printd */ "./node_modules/printd/index.js");
/* harmony import */ var printd__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(printd__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var html_to_image__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! html-to-image */ "./node_modules/html-to-image/lib/index.js");
/* harmony import */ var html_to_image__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(html_to_image__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var util__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! util */ "./node_modules/util/util.js");
/* harmony import */ var util__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(util__WEBPACK_IMPORTED_MODULE_3__);


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



/* harmony default export */ __webpack_exports__["default"] = ({
  name: "TransferInvoice",
  props: {
    item: {
      type: Object,
      "default": {}
    }
  },
  data: function data() {
    return {
      location_id: null,
      location: null,
      isLoading: false,
      numberToPrint: 1,
      isDownloading: false
    };
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
              this.isLoading = true;
              this.location_id = this.$store.state.user.location.id;
              axios.get('/api/v1/admin/auth/location/' + this.location_id).then(function (response) {
                _this.location = response.data;
                _this.d = new printd__WEBPACK_IMPORTED_MODULE_1__["Printd"]();
                _this.isLoading = false;
              })["catch"](function (error) {
                _this.isLoading = false;

                _this.$announcer({
                  status: 500,
                  data: {
                    message: error.message
                  }
                });
              });

            case 3:
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
    print: function print() {
      var _this2 = this;

      this.$emit('downloadingInvoice', true); // TODO: do page breaks after however many invoice lines will fit on the page by adding pagebreak class to empty div..

      var printStyles = ["@media print {\n                    .pagebreak {\n                        clear: both;\n                        page-break-after: always;\n                    }\n                }\n                #page-wrap { width: 800px; margin: 0 auto; }\n\n                table { border-collapse: collapse; }\n                table td, table th { border: 1px solid black; padding: 5px; }\n\n                #header { height: 30px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }\n\n                #address { width: 250px; height: 150px; float: left; font-weight: bold;}\n                #address span { display:block; }\n                #customer { overflow: hidden; }\n\n                #logo { text-align: right; float: right; position: relative; margin-top: 25px; margin-bottom: 25px; border: 1px solid #fff; width: 50px;}\n                #image {max-width: 100%; max-height: 100%; display: block;}\n\n                #customer-title { font-weight: bold; float: left; }\n                #customer-title span { display:block; }\n\n                #meta { margin-top: 1px; width: 300px; float: right; }\n                #meta td { text-align: right;  }\n                #meta td.meta-head { text-align: left; background: #eee; }\n                #meta td div { width: 100%; height: 20px; text-align: right; }\n\n                #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }\n                #items th { background: #eee; }\n                #items tr.item-row td { border: 0; vertical-align: top; }\n                #items td.descriptor { width: 250px; }\n                #items td.cost { text-align: right; }\n                #items td.qty { text-align: right; }\n                #items td.price { text-align: right; }\n                #items td.item-name { width: 225px; }\n                #items td.descriptor div, #items td.item-name div { width: 100%; }\n                #items td.total-line { border-right: 0; text-align: right; }\n                #items td.total-value { border-left: 0; padding: 10px; text-align: right;}\n                /* #items td.total-value div { height: 20px; background: none; text-align: right;} */\n                #items td.balance { background: #eee; text-align: right;}\n                #items td.blank { border: 0; }\n\n                #terms { text-align: center; margin: 20px 0 0 0; }\n                #terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }\n                #terms div { width: 100%; text-align: center;}"];
      var invoiceArea = document.getElementById('page-wrap').outerHTML.toString();
      var test = '<style>' + printStyles[0] + '</style>' + invoiceArea;
      var toprint = {
        'data': test
      };
      this.isDownloading = true;
      axios({
        url: '/api/v1/admin/grow/transfers/' + this.item.id + '/invoice/export',
        method: 'POST',
        responseType: 'blob',
        data: toprint
      }).then(function (response) {
        _this2.isDownloading = false;

        _this2.$emit('downloadingInvoice', false);

        _this2.downloadFile(response);
      })["catch"](function (error) {
        _this2.isDownloading = false;

        _this2.$emit('downloadingInvoice', false);

        _this2.$announcer({
          status: 500,
          data: {
            message: error.message
          }
        });
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Transfer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Transfer */ "./resources/js/models/Transfer.js");
/* harmony import */ var _elements_TransferInvoice__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../elements/TransferInvoice */ "./resources/js/components/elements/TransferInvoice.vue");
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
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
 //   import BatchEditModal from './batchEditModal';



/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    module: {
      type: String,
      "default": 'outgoing'
    },
    model: {
      type: String,
      "default": 'transfer'
    },
    filters: {
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
      invoiceData: null,
      transferInvoicePreviewModal: false
    };
  },
  components: {
    //          BatchEditModal
    TransferInvoice: _elements_TransferInvoice__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  mounted: function mounted() {
    this.gridSearch = this.$route.query.search || null;
    this.regulatoryAgent = this.$store.getters.getAgent;

    if (this.schema) {
      this.setFilters(this.$route.params.focus); // focus filter tab is logged in location id.

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
                return new _models_Transfer__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).params({
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
        pageLimit: 20,
        sortBy: Object.keys(this.schema.filters).find(function (key) {
          return _this.schema.filters[key].type === 'daterange';
        }) || 'rank',
        // use first daterange field(key) in schema
        orderDesc: true,
        filter: Object.assign.apply(Object, [{}].concat(_toConsumableArray(Object.keys(this.schema.filters).map(function (k) {
          return _defineProperty({}, k, ['all']);
        })), [this.filters])) //filter: Object.assign({}, ...Object.keys(this.schema.filters).map((k) => { return {[k]:this.schema.filters[k].values.map((v) => { return v.id; })}; }),this.filters)

      };
    },
    renderRowBg: function renderRowBg(item, type) {
      if (!item) return null;else if (item.status == 'unpaid') return 'table-warning';else return null;
    },
    confirmDelete: function confirmDelete(id) {
      var _this2 = this;

      this.$swal.fire({
        title: 'Are you sure?',
        text: 'This will Cancel the transfer, and rollback any receiving requests and the package assignments.',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel Transfer'
      }).then(function (result) {
        if (result.value) {
          _this2.isLoading = true;
          new _models_Transfer__WEBPACK_IMPORTED_MODULE_1__["default"]({
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
    updateStatus: function updateStatus(id, typ) {
      var _this3 = this;

      this.isLoading = true;
      axios.put('/api/v1/' + this.schema.meta.resource + '/' + id + '/modify', {
        id: id,
        status: typ
      }).then(function (response) {
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
        url: new _models_Transfer__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
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
    downloadInvoice: function downloadInvoice(rowData) {
      var _this5 = this;

      this.invoiceData = rowData;
      this.$nextTick(function () {
        _this5.$refs.invoiceMaker.print();
      });
    },
    downloadingInvoice: function downloadingInvoice(downloading) {
      this.isDownloading = downloading;
    },
    openTransferInvoicePreviewModal: function openTransferInvoicePreviewModal() {
      this.transferInvoicePreviewModal = true;
    },
    transferInvoicePreviewAction: function transferInvoicePreviewAction(message) {
      this.transferInvoicePreviewModal = false;
    },
    rowToggle: function rowToggle(row) {
      var _this6 = this;

      if (row.item.changed) {
        this.$swal.fire({
          title: 'Changes Not Saved',
          text: 'Do you want to save changes?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Save Changes',
          cancelButtonText: 'Discard Changes'
        }).then(function (result) {
          if (result.value) {
            _this6.$refs.editForm._save(true);
          } else {
            row.item.changed = false;
            row.toggleDetails();
          }
        });
      } else {
        row.toggleDetails();
      }
    },
    rowChange: function rowChange(row) {
      row.item.changed = true;
    },
    rowReset: function rowReset(row) {
      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (data) {
        var index = this.gridData.data.findIndex(function (v) {
          return v.id == data.id;
        });
        if (data.archived_at) this.gridData.data.splice(index, 1);else this.gridData.data.splice(index, 1, Object.assign(this.gridData.data[index], data));
      }

      row.item.changed = false;
    },
    scrollIntoView: function scrollIntoView(expandedRowId) {
      //this is invoked *after* the form has expanded and form data loaded.
      // There were issues trying to scroll to the row on slow network connections
      // where the scroll went to the top then expanded so the user was staring at bottom of expansion.  Now we just wait until child component loads.
      if (this.scrollIntoViewId && this.scrollIntoViewId === expandedRowId) {
        var rowElement = document.getElementById(this.model.toLowerCase() + '_table__row_' + this.scrollIntoViewId);

        if (rowElement) {
          rowElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
          this.scrollIntoViewId = null; //clear so we don't try again
        }
      }
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
      var _this7 = this;

      if (this.$store.state[this.module].search && this.$store.state[this.module].search.gridData && Array.isArray(this.gridData.data)) {
        //merge any saved options into griddata
        this.$store.state[this.module].search.gridData.forEach(function (saved, i) {
          var row = _this7.gridData.data.findIndex(function (e) {
            return e.id === saved.id;
          });

          if (row > -1) _this7.$set(_this7.gridData.data[row], '_showDetails', true); //$set if the _showDetails isn't already there we need the reactivity

          if (i === 0) _this7.scrollIntoViewId = saved.id; //only scroll to first expanded row (in case there are > 1). Save id and we'll scroll when the individual row has expanded and form data loaded
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
    gridSearch: function gridSearch() {
      this.$store.commit(this.module + '/setSearch', {
        gridSearch: this.gridSearch,
        options: {
          merge: true
        }
      });
    },
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

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n#page-wrap[data-v-71e801d5] { width: 800px; margin: 0 auto;\n}\ntable[data-v-71e801d5] { border-collapse: collapse;\n}\ntable td[data-v-71e801d5], table th[data-v-71e801d5] { border: 1px solid black; padding: 5px;\n}\n#header[data-v-71e801d5] { height: 30px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; -webkit-text-decoration: uppercase; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px;\n}\n#address[data-v-71e801d5] { width: 250px; height: 150px; float: left; font-weight: bold;}\n#address span[data-v-71e801d5] { display:block;\n}\n#customer[data-v-71e801d5] { overflow: hidden;\n}\n#logo[data-v-71e801d5] { text-align: right; float: right; position: relative; margin-top: 25px; margin-bottom: 25px; border: 1px solid #fff; width: 50px;}\n#image[data-v-71e801d5] {max-width: 100%; max-height: 100%; display: block;}\n#customer-title[data-v-71e801d5] { font-weight: bold; float: left;\n}\n#customer-title span[data-v-71e801d5] { display:block;\n}\n#meta[data-v-71e801d5] { margin-top: 1px; width: 300px; float: right;\n}\n#meta td[data-v-71e801d5] { text-align: right;\n}\n#meta td.meta-head[data-v-71e801d5] { text-align: left; background: #eee;\n}\n#meta td div[data-v-71e801d5] { width: 100%; height: 20px; text-align: right;\n}\n#items[data-v-71e801d5] { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black;\n}\n#items th[data-v-71e801d5] { background: #eee;\n}\n#items tr.item-row td[data-v-71e801d5] { border: 0; vertical-align: top;\n}\n#items td.descriptor[data-v-71e801d5] { width: 250px;\n}\n#items td.cost[data-v-71e801d5] { text-align: right;\n}\n#items td.qty[data-v-71e801d5] { text-align: right;\n}\n#items td.price[data-v-71e801d5] { text-align: right;\n}\n#items td.item-name[data-v-71e801d5] { width: 225px;\n}\n#items td.descriptor div[data-v-71e801d5], #items td.item-name div[data-v-71e801d5] { width: 100%;\n}\n#items td.total-line[data-v-71e801d5] { border-right: 0; text-align: right;\n}\n#items td.total-value[data-v-71e801d5] { border-left: 0; padding: 10px; text-align: right;}\n#items td.balance[data-v-71e801d5] { background: #eee; text-align: right;}\n#items td.blank[data-v-71e801d5] { border: 0;\n}\n#terms[data-v-71e801d5] { text-align: center; margin: 20px 0 0 0;\n}\n#terms h5[data-v-71e801d5] { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0;\n}\n#terms div[data-v-71e801d5] { width: 100%; text-align: center;}\n\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n[data-v-360d5c13] .grid-table > tbody > tr > td:last-of-type {\n    width: 1%;\n    white-space: nowrap;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&":
/*!**********************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& ***!
  \**********************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&");

if(typeof content === 'string') content = [[module.i, content, '']];

var transform;
var insertInto;



var options = {"hmr":true}

options.transform = transform
options.insertInto = undefined;

var update = __webpack_require__(/*! ../../../../node_modules/style-loader/lib/addStyles.js */ "./node_modules/style-loader/lib/addStyles.js")(content, options);

if(content.locals) module.exports = content.locals;

if(false) {}

/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&");

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true&":
/*!***************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true& ***!
  \***************************************************************************************************************************************************************************************************************************************/
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
  return _vm.location
    ? _c("div", { staticClass: "col-12" }, [
        _c(
          "form",
          {
            staticClass: "modal-form",
            on: {
              submit: function($event) {
                $event.preventDefault()
                return _vm.print()
              }
            }
          },
          [
            _c("fieldset", [
              _vm.item
                ? _c(
                    "div",
                    { staticClass: "form-group row gutters mt-3 mb-2" },
                    [
                      _c("div", { attrs: { id: "page-wrap" } }, [
                        _c("div", { attrs: { id: "header" } }, [
                          _vm._v("INVOICE")
                        ]),
                        _vm._v(" "),
                        _c("div", { attrs: { id: "identity" } }, [
                          _c("div", [_vm._v("Payable To")]),
                          _vm._v(" "),
                          _vm.location
                            ? _c("div", { attrs: { id: "address" } }, [
                                _c("span", [_vm._v(_vm._s(_vm.location.name))]),
                                _vm._v(" "),
                                _vm.location.address.address1
                                  ? _c("span", [
                                      _vm._v(
                                        _vm._s(_vm.location.address.address1)
                                      )
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _vm.location.address.address2
                                  ? _c("span", [
                                      _vm._v(
                                        _vm._s(_vm.location.address.address2)
                                      )
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _c("span", [
                                  _vm._v(
                                    _vm._s(_vm.location.address.city) +
                                      ", " +
                                      _vm._s(_vm.location.address.region) +
                                      " " +
                                      _vm._s(_vm.location.address.zip)
                                  )
                                ]),
                                _vm._v(" "),
                                _vm.location.address.phone
                                  ? _c("span", [
                                      _vm._v(_vm._s(_vm.location.address.phone))
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _vm.location.licensenum
                                  ? _c("span", [
                                      _vm._v(
                                        "License: " +
                                          _vm._s(_vm.location.licensenum)
                                      )
                                    ])
                                  : _vm._e()
                              ])
                            : _vm._e()
                        ]),
                        _vm._v(" "),
                        _c("div", { staticStyle: { clear: "both" } }),
                        _vm._v(" "),
                        _vm.item.receiver
                          ? _c("div", { attrs: { id: "customer" } }, [
                              _c("div", [_vm._v("Order From")]),
                              _vm._v(" "),
                              _c("div", { attrs: { id: "customer-title" } }, [
                                _c("span", [
                                  _vm._v(_vm._s(_vm.item.receiver.name))
                                ]),
                                _vm._v(" "),
                                _vm.item.receiver.address1
                                  ? _c("span", [
                                      _vm._v(_vm._s(_vm.item.receiver.address1))
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _vm.item.receiver.address2
                                  ? _c("span", [
                                      _vm._v(_vm._s(_vm.item.receiveraddress2))
                                    ])
                                  : _vm._e(),
                                _vm._v(" "),
                                _c("span", [
                                  _vm._v(
                                    _vm._s(_vm.item.receiver.city) +
                                      ", " +
                                      _vm._s(_vm.item.receiver.region) +
                                      " " +
                                      _vm._s(_vm.item.receiver.zip)
                                  )
                                ]),
                                _vm._v(" "),
                                _vm.item.receiver.phone
                                  ? _c("span", [
                                      _vm._v(_vm._s(_vm.item.receiver.phone))
                                    ])
                                  : _vm._e()
                              ]),
                              _vm._v(" "),
                              _c("table", { attrs: { id: "meta" } }, [
                                _c("tr", [
                                  _c("td", { staticClass: "meta-head" }, [
                                    _vm._v("Order #")
                                  ]),
                                  _vm._v(" "),
                                  _c("td", [
                                    _c("div", [
                                      _vm._v(_vm._s(_vm.item.manifest_number))
                                    ])
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("tr", [
                                  _c("td", { staticClass: "meta-head" }, [
                                    _vm._v("Date")
                                  ]),
                                  _vm._v(" "),
                                  _c("td", [
                                    _c("div", { attrs: { id: "date" } }, [
                                      _vm._v(
                                        _vm._s(
                                          _vm._f("formattedLocalDate")(
                                            _vm.item.created_at
                                          )
                                        )
                                      )
                                    ])
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("tr", [
                                  _c("td", { staticClass: "meta-head" }, [
                                    _vm._v("Amount Due")
                                  ]),
                                  _vm._v(" "),
                                  _c("td", [
                                    _c("div", { staticClass: "due" }, [
                                      _vm._v(
                                        "$" +
                                          _vm._s(
                                            _vm._f("dollar")(
                                              _vm.item.transfersale_total
                                            )
                                          )
                                      )
                                    ])
                                  ])
                                ])
                              ])
                            ])
                          : _vm._e(),
                        _vm._v(" "),
                        _c(
                          "table",
                          { attrs: { id: "items" } },
                          [
                            _vm._m(0),
                            _vm._v(" "),
                            _vm._l(_vm.item.packages, function(pkg) {
                              return _c("tr", { staticClass: "item-row" }, [
                                _c("td", { staticClass: "item-name" }, [
                                  _c("div", [_vm._v(_vm._s(pkg.label))])
                                ]),
                                _vm._v(" "),
                                _c("td", { staticClass: "descriptor" }, [
                                  _c("div", [_vm._v(_vm._s(pkg.item.name))]),
                                  _c("div", [
                                    _vm._v(
                                      _vm._s(
                                        _vm._f("ucwords")(
                                          pkg.item.category_type
                                        )
                                      )
                                    )
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("td", { staticClass: "cost" }, [
                                  _c("div", [
                                    _vm._v(
                                      _vm._s(
                                        _vm._f("dollar")(
                                          pkg.received_price / pkg.quantity,
                                          4
                                        )
                                      )
                                    )
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("td", { staticClass: "qty" }, [
                                  _c("div", [
                                    _vm._v(
                                      _vm._s(pkg.quantity) +
                                        " (" +
                                        _vm._s(pkg.unit_of_measure) +
                                        ")"
                                    )
                                  ])
                                ]),
                                _vm._v(" "),
                                _c("td", { staticClass: "price" }, [
                                  _c("span", [
                                    _vm._v(
                                      "$" +
                                        _vm._s(
                                          _vm._f("dollar")(pkg.received_price)
                                        )
                                    )
                                  ])
                                ])
                              ])
                            }),
                            _vm._v(" "),
                            _c("tr", [
                              _c("td", {
                                staticClass: "blank",
                                attrs: { colspan: "2" }
                              }),
                              _vm._v(" "),
                              _c(
                                "td",
                                {
                                  staticClass: "total-line",
                                  attrs: { colspan: "2" }
                                },
                                [_vm._v("Subtotal")]
                              ),
                              _vm._v(" "),
                              _c("td", { staticClass: "total-value" }, [
                                _c("div", { attrs: { id: "subtotal" } }, [
                                  _vm._v(
                                    "$" +
                                      _vm._s(
                                        _vm._f("dollar")(
                                          _vm.item.transfersale_total -
                                            _vm.item.transfersale_fee
                                        )
                                      )
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            _c("tr", [
                              _c("td", {
                                staticClass: "blank",
                                attrs: { colspan: "2" }
                              }),
                              _vm._v(" "),
                              _c(
                                "td",
                                {
                                  staticClass: "total-line",
                                  attrs: { colspan: "2" }
                                },
                                [_vm._v("Service Fee")]
                              ),
                              _vm._v(" "),
                              _c("td", { staticClass: "total-value" }, [
                                _c("div", { attrs: { id: "subtotal" } }, [
                                  _vm._v(
                                    "$" +
                                      _vm._s(
                                        _vm._f("dollar")(
                                          _vm.item.transfersale_fee
                                        )
                                      )
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            _c("tr", [
                              _c("td", {
                                staticClass: "blank",
                                attrs: { colspan: "2" }
                              }),
                              _vm._v(" "),
                              _c(
                                "td",
                                {
                                  staticClass: "total-line",
                                  attrs: { colspan: "2" }
                                },
                                [_vm._v("Total")]
                              ),
                              _vm._v(" "),
                              _c("td", { staticClass: "total-value" }, [
                                _c("div", { attrs: { id: "total" } }, [
                                  _vm._v(
                                    "$" +
                                      _vm._s(
                                        _vm._f("dollar")(
                                          _vm.item.transfersale_total
                                        )
                                      )
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            _c("tr", [
                              _c("td", {
                                staticClass: "blank",
                                attrs: { colspan: "2" }
                              }),
                              _vm._v(" "),
                              _c(
                                "td",
                                {
                                  staticClass: "total-line",
                                  attrs: { colspan: "2" }
                                },
                                [_vm._v("Amount Paid")]
                              ),
                              _vm._v(" "),
                              _c("td", { staticClass: "total-value" }, [
                                _c("div", { attrs: { id: "paid" } }, [
                                  _vm._v(
                                    "$" +
                                      _vm._s(
                                        _vm._f("dollar")(
                                          _vm.item.status !== "confirmed"
                                            ? "0.00"
                                            : _vm.item.transfersale_total
                                        )
                                      )
                                  )
                                ])
                              ])
                            ]),
                            _vm._v(" "),
                            _c("tr", [
                              _c("td", {
                                staticClass: "blank",
                                attrs: { colspan: "2" }
                              }),
                              _vm._v(" "),
                              _c(
                                "td",
                                {
                                  staticClass: "total-line balance",
                                  attrs: { colspan: "2" }
                                },
                                [_vm._v("Balance Due")]
                              ),
                              _vm._v(" "),
                              _c("td", { staticClass: "total-value balance" }, [
                                _c("div", { staticClass: "due" }, [
                                  _vm._v(
                                    "$" +
                                      _vm._s(
                                        _vm._f("dollar")(
                                          _vm.item.status === "confirmed"
                                            ? "0.00"
                                            : _vm.item.transfersale_total
                                        )
                                      )
                                  )
                                ])
                              ])
                            ])
                          ],
                          2
                        ),
                        _vm._v(" "),
                        _vm._m(1)
                      ])
                    ]
                  )
                : _vm._e(),
              _vm._v(" "),
              _vm.item
                ? _c("div", { staticClass: "col-12 text-center mb-3" }, [
                    _c(
                      "button",
                      {
                        staticClass: "btn btn-primary",
                        attrs: { type: "submit" }
                      },
                      [
                        _c(
                          "span",
                          { staticClass: "btn-label" },
                          [
                            _c("spinner", {
                              staticClass: "float-left",
                              attrs: {
                                isProcessing: _vm.isDownloading,
                                isFullScreen: false,
                                isLine: true,
                                spinnerWidth: 25
                              }
                            }),
                            _c("i", {
                              staticClass:
                                "hotbox-icon hotbox-icon-download-file"
                            })
                          ],
                          1
                        ),
                        _vm._v(
                          " " +
                            _vm._s(
                              !_vm.isDownloading ? "Download" : "Downloading"
                            )
                        )
                      ]
                    )
                  ])
                : _vm._e()
            ])
          ]
        )
      ])
    : _c(
        "div",
        [
          _c("loading", {
            attrs: { display: _vm.location ? false : true, type: "loadModal" }
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
    return _c("tr", [
      _c("th", [_vm._v("Item")]),
      _vm._v(" "),
      _c("th", [_vm._v("Description")]),
      _vm._v(" "),
      _c("th", [_vm._v("Unit Cost")]),
      _vm._v(" "),
      _c("th", [_vm._v("Quantity")]),
      _vm._v(" "),
      _c("th", [_vm._v("Price")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { attrs: { id: "terms" } }, [
      _c("h5", [_vm._v("Terms")]),
      _vm._v(" "),
      _c("div", [
        _vm._v(
          "NET 30 Days. Finance Charge of 1.5% will be made on unpaid balances after 30 days."
        )
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true&":
/*!*******************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true& ***!
  \*******************************************************************************************************************************************************************************************************************************************/
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
                    staticClass: "grid-table",
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
                      "tbody-transition-props": { name: "hb-list-fade" },
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
                                  _c("span", [_vm._v(_vm._s(column.label))])
                                ])
                              ]
                            }
                          }
                        }),
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
                          key: "cell(received_at)",
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
                          key: "cell(manifest_number)",
                          fn: function(row) {
                            return [
                              _vm._v("\n                " + _vm._s(row.value)),
                              _c("br"),
                              _vm._v(" "),
                              _c("span", { staticClass: "small" }, [
                                _vm._v(
                                  _vm._s(
                                    _vm._f("renderValue")(
                                      row.item.type,
                                      _vm.schema.form.type.values
                                    )
                                  )
                                )
                              ])
                            ]
                          }
                        },
                        {
                          key: "cell(receiver)",
                          fn: function(row) {
                            return [
                              row.item.receiver
                                ? _c("span", [
                                    _vm._v(
                                      "\n                    " +
                                        _vm._s(row.item.receiver.name) +
                                        " " +
                                        _vm._s(row.item.receiver.licensenum)
                                    ),
                                    _c("br"),
                                    _vm._v(" "),
                                    _c("span", { staticClass: "small" }, [
                                      _vm._v(_vm._s(row.item.receiver.city))
                                    ])
                                  ])
                                : _c("span", [_vm._v("--")])
                            ]
                          }
                        },
                        {
                          key: "cell(transporter_name)",
                          fn: function(row) {
                            return [
                              row.item.transporter_name
                                ? _c("span", [
                                    _vm._v(
                                      "\n                    " +
                                        _vm._s(row.value)
                                    ),
                                    _c("br"),
                                    _vm._v(" "),
                                    _c("span", { staticClass: "small" }, [
                                      _vm._v(
                                        "Lic: " +
                                          _vm._s(
                                            row.item.transporter_licensenum
                                          )
                                      )
                                    ])
                                  ])
                                : _c("span", [_vm._v("--")])
                            ]
                          }
                        },
                        {
                          key: "cell(packages_count)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(row.item.packages.length || 0) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(status)",
                          fn: function(row) {
                            return [
                              _c("i", {
                                staticClass: "float-right mr-2 mt-1",
                                class: {
                                  "ti-angle-double-down": !row.detailsShowing,
                                  "ti-angle-double-up": row.detailsShowing
                                },
                                on: { click: row.toggleDetails }
                              }),
                              _vm._v(
                                "\n                " +
                                  _vm._s(
                                    _vm._f("ucwords")(row.value || "confirmed")
                                  ) +
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
                                  [
                                    _vm.isDownloading &&
                                    _vm.invoiceData === row.item
                                      ? _c("spinner", {
                                          staticClass: "float-left",
                                          attrs: {
                                            isProcessing:
                                              _vm.isDownloading &&
                                              _vm.invoiceData === row.item,
                                            isFullScreen: false,
                                            isLine: true,
                                            spinnerWidth: 25
                                          }
                                        })
                                      : _c("i", { staticClass: "ti-more-alt" })
                                  ],
                                  1
                                ),
                                _vm._v(" "),
                                _c(
                                  "div",
                                  {
                                    staticClass:
                                      "dropdown-menu tight dropdown-menu-right"
                                  },
                                  [
                                    row.item.type == "external"
                                      ? _c(
                                          "a",
                                          {
                                            staticClass:
                                              "dropdown-item action-primary",
                                            attrs: { href: "" },
                                            on: {
                                              click: function($event) {
                                                $event.preventDefault()
                                                return _vm.downloadInvoice(
                                                  row.item
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "hotbox-icon hotbox-icon-square-download"
                                            }),
                                            _vm._v(" Download Invoice")
                                          ]
                                        )
                                      : _vm._e(),
                                    _vm._v(" "),
                                    row.item.status == "unpaid"
                                      ? _c(
                                          "a",
                                          {
                                            staticClass:
                                              "dropdown-item action-success",
                                            attrs: { href: "" },
                                            on: {
                                              click: function($event) {
                                                $event.preventDefault()
                                                return _vm.updateStatus(
                                                  row.item.id,
                                                  "confirmed"
                                                )
                                              }
                                            }
                                          },
                                          [
                                            _c("i", {
                                              staticClass:
                                                "hotbox-icon hotbox-icon-s-check"
                                            }),
                                            _vm._v(" Mark as Paid")
                                          ]
                                        )
                                      : _vm._e(),
                                    _vm._v(" "),
                                    _c(
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
                                        _vm._v(" Delete and Unlink")
                                      ]
                                    )
                                  ]
                                )
                              ])
                            ]
                          }
                        },
                        {
                          key: "row-details",
                          fn: function(row) {
                            return [
                              _c(
                                "div",
                                {
                                  staticClass:
                                    "card card-stats px-3 justify-content-center"
                                },
                                [
                                  _c("div", { staticClass: "card-header" }),
                                  _vm._v(" "),
                                  _c("div", { staticClass: "card-body" }, [
                                    _c("h5", { staticClass: "mt-2" }, [
                                      _vm._v("Packages Manifest")
                                    ]),
                                    _vm._v(" "),
                                    _c(
                                      "ul",
                                      _vm._l(row.item.packages, function(
                                        pack,
                                        pid
                                      ) {
                                        return _c(
                                          "li",
                                          {
                                            staticClass:
                                              "d-flex justify-content-between align-items-center position-relative px-2 mb-3"
                                          },
                                          [
                                            _c("div", {}, [
                                              _vm._v(
                                                "\n                                    " +
                                                  _vm._s(
                                                    (pack.item || {}).name ||
                                                      "Misc Item"
                                                  )
                                              ),
                                              _c("br"),
                                              _vm._v(" "),
                                              _c(
                                                "span",
                                                { staticClass: "small" },
                                                [
                                                  _c("i", {
                                                    staticClass:
                                                      "hotbox-icon hotbox-icon-tag-content"
                                                  }),
                                                  _vm._v(
                                                    " " + _vm._s(pack.label)
                                                  )
                                                ]
                                              )
                                            ]),
                                            _vm._v(" "),
                                            _c(
                                              "div",
                                              {
                                                staticClass:
                                                  "ml-auto text-right"
                                              },
                                              [
                                                _vm._v(
                                                  "\n                                    " +
                                                    _vm._s(pack.quantity) +
                                                    "/" +
                                                    _vm._s(pack.unit_of_measure)
                                                ),
                                                _c("br"),
                                                _vm._v(" "),
                                                pack.received_price > 0
                                                  ? _c(
                                                      "span",
                                                      { staticClass: "small" },
                                                      [
                                                        _vm._v(
                                                          "$" +
                                                            _vm._s(
                                                              _vm._f("dollar")(
                                                                pack.received_price
                                                              )
                                                            )
                                                        )
                                                      ]
                                                    )
                                                  : _vm._e()
                                              ]
                                            )
                                          ]
                                        )
                                      }),
                                      0
                                    ),
                                    _vm._v(" "),
                                    row.item.transfersale_total > 0
                                      ? _c(
                                          "div",
                                          {
                                            staticClass:
                                              "col-12 text-right mt-1 mb-3",
                                            staticStyle: {
                                              "border-top": "1px solid #E6E6E6"
                                            }
                                          },
                                          [
                                            row.item.transfersale_fee > 0
                                              ? _c("span", {}, [
                                                  _vm._v(
                                                    "Service Fee: $" +
                                                      _vm._s(
                                                        _vm._f("dollar")(
                                                          row.item
                                                            .transfersale_fee
                                                        )
                                                      )
                                                  ),
                                                  _c("br")
                                                ])
                                              : _vm._e(),
                                            _vm._v(" "),
                                            _c("b", [
                                              _vm._v(
                                                "Total Billed: $" +
                                                  _vm._s(
                                                    _vm._f("dollar")(
                                                      row.item
                                                        .transfersale_total
                                                    )
                                                  )
                                              )
                                            ])
                                          ]
                                        )
                                      : _vm._e()
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
                                        width: "75"
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
              ref: "transferInvoicePreviewModal",
              attrs: {
                centered: "",
                static: true,
                size: "lg",
                "header-bg-variant": "light",
                "header-text-variant": "primary"
              },
              model: {
                value: _vm.transferInvoicePreviewModal,
                callback: function($$v) {
                  _vm.transferInvoicePreviewModal = $$v
                },
                expression: "transferInvoicePreviewModal"
              }
            },
            [
              _c("template", { slot: "modal-header" }, [
                _c("i", {
                  staticClass: "modal-top-close fal ti-close",
                  on: { click: _vm.transferInvoicePreviewAction }
                }),
                _vm._v(" "),
                _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                  _vm._v("Invoice Preview")
                ])
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "justify-content-center" }, [
                _c(
                  "div",
                  [
                    _c("TransferInvoice", {
                      ref: "invoiceMaker",
                      attrs: { item: _vm.invoiceData },
                      on: {
                        downloadingInvoice: function($event) {
                          return _vm.downloadingInvoice($event)
                        }
                      }
                    })
                  ],
                  1
                )
              ]),
              _vm._v(" "),
              _c("template", { slot: "modal-footer" }, [
                _c(
                  "span",
                  {
                    staticClass: "btn-label btn-sm btn-light float-right",
                    on: { click: _vm.transferInvoicePreviewAction }
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

/***/ "./resources/js/components/elements/TransferInvoice.vue":
/*!**************************************************************!*\
  !*** ./resources/js/components/elements/TransferInvoice.vue ***!
  \**************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true& */ "./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true&");
/* harmony import */ var _TransferInvoice_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TransferInvoice.vue?vue&type=script&lang=js& */ "./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& */ "./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _TransferInvoice_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "71e801d5",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/elements/TransferInvoice.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js&":
/*!***************************************************************************************!*\
  !*** ./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib??ref--4-0!../../../../node_modules/vue-loader/lib??vue-loader-options!./TransferInvoice.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&":
/*!***********************************************************************************************************************!*\
  !*** ./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& ***!
  \***********************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/style-loader!../../../../node_modules/css-loader??ref--6-1!../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../node_modules/postcss-loader/src??ref--6-2!../../../../node_modules/vue-loader/lib??vue-loader-options!./TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=style&index=0&id=71e801d5&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_style_index_0_id_71e801d5_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true&":
/*!*********************************************************************************************************!*\
  !*** ./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true& ***!
  \*********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib??vue-loader-options!./TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/elements/TransferInvoice.vue?vue&type=template&id=71e801d5&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_TransferInvoice_vue_vue_type_template_id_71e801d5_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/outgoing/transfer/grid.vue":
/*!******************************************************************!*\
  !*** ./resources/js/components/views/outgoing/transfer/grid.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=360d5c13&scoped=true& */ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& */ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "360d5c13",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/outgoing/transfer/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&":
/*!***************************************************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& ***!
  \***************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=style&index=0&id=360d5c13&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_style_index_0_id_360d5c13_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true&":
/*!*************************************************************************************************************!*\
  !*** ./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true& ***!
  \*************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=360d5c13&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/outgoing/transfer/grid.vue?vue&type=template&id=360d5c13&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_360d5c13_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Transfer.js":
/*!*****************************************!*\
  !*** ./resources/js/models/Transfer.js ***!
  \*****************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Transfer; });
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



var Transfer =
/*#__PURE__*/
function (_Model) {
  _inherits(Transfer, _Model);

  function Transfer() {
    _classCallCheck(this, Transfer);

    return _possibleConstructorReturn(this, _getPrototypeOf(Transfer).apply(this, arguments));
  }

  _createClass(Transfer, [{
    key: "resource",
    value: function resource() {
      return 'admin/grow/transfers';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Transfer;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);