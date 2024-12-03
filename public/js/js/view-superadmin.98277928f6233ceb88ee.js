(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["js/view-superadmin"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js&":
/*!*********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js& ***!
  \*********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
    return {};
  },
  components: {},
  mounted: function mounted() {},
  methods: {},
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

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _plugins_jsoneditor_vue_json_editor__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../../plugins/jsoneditor/vue-json-editor */ "./resources/plugins/jsoneditor/vue-json-editor.vue");
/* harmony import */ var _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Servicelog */ "./resources/js/models/Servicelog.js");
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



/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    model: {
      type: String,
      "default": 'Servicelog'
    },
    module: {
      type: String,
      "default": 'superadmin'
    },
    id: {
      type: [Number, String],
      "default": ''
    },
    view: {
      type: String,
      "default": 'request'
    }
  },
  data: function data() {
    return {
      item: null,
      tab: this.view,
      isLoading: false,
      isError: false,
      isModified: false,
      isProcessing: false,
      itemState: 'save',
      requestJson: null,
      responseJson: null,
      errorJson: null
    };
  },
  components: {
    VJsoneditor: _plugins_jsoneditor_vue_json_editor__WEBPACK_IMPORTED_MODULE_0__["default"]
  },
  mounted: function mounted() {
    var _this = this;

    this.isLoading = true;
    _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__["default"].find(this.id).then(function (response) {
      _this.schema = response.schema;
      _this.item = new _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__["default"](response).withDefaults(_this.schema);
      _this.requestJson = _this.item.parameters ? JSON.parse(_this.item.parameters) || _this.item.parameters : null;
      _this.responseJson = _this.item.response ? JSON.parse(_this.item.response) || _this.item.response : null;
      _this.errorJson = _this.item.error ? JSON.parse(_this.item.error) || _this.item.error : null;
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
  methods: {},
  computed: {},
  watch: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../models/Servicelog */ "./resources/js/models/Servicelog.js");
/* harmony import */ var _editModal__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editModal */ "./resources/js/components/views/superadmin/servicelog/editModal.vue");
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



/* harmony default export */ __webpack_exports__["default"] = ({
  props: {
    module: {
      type: String,
      "default": 'superadmin'
    },
    model: {
      type: String,
      "default": 'servicelog'
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
      editModal: false,
      editView: 'request',
      editId: 0
    };
  },
  components: {
    EditModal: _editModal__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  mounted: function () {
    var _mounted = _asyncToGenerator(
    /*#__PURE__*/
    _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
      return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
        while (1) {
          switch (_context.prev = _context.next) {
            case 0:
              _context.next = 2;
              return this.$store.dispatch(this.module + '/setSchemas', this.model.toLowerCase());

            case 2:
              this.gridSearch = this.$store.state[this.module].search || null; // if we have a search state - populate

              if (this.schema) this.setFilters(this.$route.params.focus); // if we have schema, then set filters, else we watch schema load/change and then set.

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
    fetchGrid: function () {
      var _fetchGrid = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee2() {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                if (!(!this.schema || !this.gridFilters)) {
                  _context2.next = 4;
                  break;
                }

                return _context2.abrupt("return", false);

              case 4:
                if (!(this.isLoading == true)) {
                  _context2.next = 6;
                  break;
                }

                return _context2.abrupt("return", false);

              case 6:
                // do not fetch if we are already fetching
                this.isLoading = true;
                _context2.next = 9;
                return new _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).params({
                  search: this.gridSearch
                }).orderBy((this.gridFilters.orderDesc ? '-' : '') + this.gridFilters.sortBy).limit(this.gridFilters.pageLimit).page(this.gridPage).get();

              case 9:
                this.gridData = _context2.sent;
                this.isLoading = false;

              case 11:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function fetchGrid() {
        return _fetchGrid.apply(this, arguments);
      }

      return fetchGrid;
    }(),
    searchGrid: lodash__WEBPACK_IMPORTED_MODULE_3___default.a.debounce(function (e) {
      // upon search filter update, throttle .5 sec grid and scope refresh
      this.gridPage = 1;
      this.$store.commit(this.module + '/setFocus', 'all'); // if we are searching - set the focus and tab of the index to all.

      this.$store.commit(this.module + '/setTab', this.model.toLowerCase());
      this.$store.commit(this.module + '/setSearch', this.gridSearch); // persist search setting

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
          return _defineProperty({}, k, _this.schema.filters[k].values.map(function (v) {
            return v.id;
          }));
        })), [_defineProperty({}, this.schema.meta.focus_field || 'type', [focus])]))
      };
    },
    renderRowBg: function renderRowBg(item, type) {
      if (!item) return null;
      return item.status == 'failed' ? 'table-warning' : null;
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
          new _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__["default"]({
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
        url: new _models_Servicelog__WEBPACK_IMPORTED_MODULE_1__["default"]().setFilters(this.gridFilters.filter).custom(this.schema.meta.resource + '/export/' + typ).getUrl(),
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
    loadModal: function loadModal(id, view) {
      this.editId = id;
      this.editView = view;
      this.editModal = !this.editModal;
    },
    renderUom: function renderUom(row, typ) {
      if (this.schema.form[typ]) if (this.schema.form[typ].values.find(function (v) {
        return v.id == row[typ];
      })) return this.schema.form[typ].values.find(function (v) {
        return v.id == row[typ];
      }).name;
      return row[typ];
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
        else if (!lodash__WEBPACK_IMPORTED_MODULE_3___default.a.isEqual(to.filters, from.filters)) this.setFilters(); // if a schema filter change - reset the filters - which will refresh the grid
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
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {},
  data: function data() {
    return {};
  },
  mounted: function mounted() {},
  methods: {}
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  /*
   * The component's data.
   */
  data: function data() {
    return {
      tokens: []
    };
  },

  /**
   * Prepare the component (Vue 1.x).
   */
  ready: function ready() {
    this.prepareComponent();
  },

  /**
   * Prepare the component (Vue 2.x).
   */
  mounted: function mounted() {
    this.prepareComponent();
  },
  methods: {
    /**
     * Prepare the component (Vue 2.x).
     */
    prepareComponent: function prepareComponent() {
      this.getTokens();
    },

    /**
     * Get all of the authorized tokens for the user.
     */
    getTokens: function getTokens() {
      var _this = this;

      axios.get('/oauth/tokens').then(function (response) {
        _this.tokens = response.data;
      });
    },

    /**
     * Revoke the given token.
     */
    revoke: function revoke(token) {
      var _this2 = this;

      axios["delete"]('/oauth/tokens/' + token.id).then(function (response) {
        _this2.getTokens();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js&":
/*!******************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js& ***!
  \******************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  /*
   * The component's data.
   */
  data: function data() {
    return {
      clients: [],
      createForm: {
        errors: [],
        name: '',
        redirect: ''
      },
      editForm: {
        errors: [],
        name: '',
        redirect: ''
      },
      createClientModal: false,
      editClientModal: false
    };
  },

  /**
   * Prepare the component (Vue 1.x).
   */
  ready: function ready() {
    this.prepareComponent();
  },

  /**
   * Prepare the component (Vue 2.x).
   */
  mounted: function mounted() {
    this.prepareComponent();
  },
  methods: {
    /**
     * Prepare the component.
     */
    prepareComponent: function prepareComponent() {
      this.getClients();
      $('#modal-create-client').on('shown.bs.modal', function () {
        $('#create-client-name').focus();
      });
      $('#modal-edit-client').on('shown.bs.modal', function () {
        $('#edit-client-name').focus();
      });
    },

    /**
     * Get all of the OAuth clients for the user.
     */
    getClients: function getClients() {
      var _this = this;

      axios.get('/oauth/clients').then(function (response) {
        _this.clients = response.data;
      });
    },

    /**
     * Create a new OAuth client for the user.
     */
    store: function store() {
      this.persistClient('post', '/oauth/clients', this.createForm, '#modal-create-client');
    },

    /**
     * Edit the given client.
     */
    edit: function edit(client) {
      this.editForm.id = client.id;
      this.editForm.name = client.name;
      this.editForm.redirect = client.redirect;
      this.editClientModal = true;
    },

    /**
     * Update the client being edited.
     */
    update: function update() {
      this.persistClient('put', '/oauth/clients/' + this.editForm.id, this.editForm, '#modal-edit-client');
    },

    /**
     * Persist the client to storage using the given form.
     */
    persistClient: function persistClient(method, uri, form, modal) {
      var _this2 = this;

      form.errors = [];
      axios[method](uri, form).then(function (response) {
        _this2.getClients();

        form.name = '';
        form.redirect = '';
        form.errors = [];
        $(modal).modal('hide');
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          form.errors = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.flatten(lodash__WEBPACK_IMPORTED_MODULE_0___default.a.toArray(error.response.data.errors));
        } else {
          form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },

    /**
     * Destroy the given client.
     */
    destroy: function destroy(client) {
      var _this3 = this;

      axios["delete"]('/oauth/clients/' + client.id).then(function (response) {
        _this3.getClients();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_0__);
function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  /*
   * The component's data.
   */
  data: function data() {
    return {
      accessToken: null,
      tokens: [],
      scopes: [],
      form: {
        name: '',
        scopes: [],
        errors: []
      },
      createTokenModal: false,
      accessTokenModal: false
    };
  },

  /**
   * Prepare the component (Vue 1.x).
   */
  ready: function ready() {
    this.prepareComponent();
  },

  /**
   * Prepare the component (Vue 2.x).
   */
  mounted: function mounted() {
    this.prepareComponent();
  },
  methods: {
    /**
     * Prepare the component.
     */
    prepareComponent: function prepareComponent() {
      this.getTokens();
      this.getScopes();
      $('#modal-create-token').on('shown.bs.modal', function () {
        $('#create-token-name').focus();
      });
    },

    /**
     * Get all of the personal access tokens for the user.
     */
    getTokens: function getTokens() {
      var _this = this;

      axios.get('/oauth/personal-access-tokens').then(function (response) {
        _this.tokens = response.data;
      });
    },

    /**
     * Get all of the available scopes.
     */
    getScopes: function getScopes() {
      var _this2 = this;

      axios.get('/oauth/scopes').then(function (response) {
        _this2.scopes = response.data;
      });
    },

    /**
     * Create a new personal access token.
     */
    store: function store() {
      var _this3 = this;

      this.accessToken = null;
      this.form.errors = [];
      axios.post('/oauth/personal-access-tokens', this.form).then(function (response) {
        _this3.form.name = '';
        _this3.form.scopes = [];
        _this3.form.errors = [];

        _this3.tokens.push(response.data.token);

        _this3.showAccessToken(response.data.accessToken);
      })["catch"](function (error) {
        if (_typeof(error.response.data) === 'object') {
          _this3.form.errors = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.flatten(lodash__WEBPACK_IMPORTED_MODULE_0___default.a.toArray(error.response.data.errors));
        } else {
          _this3.form.errors = ['Something went wrong. Please try again.'];
        }
      });
    },

    /**
     * Toggle the given scope in the list of assigned scopes.
     */
    toggleScope: function toggleScope(scope) {
      if (this.scopeIsAssigned(scope)) {
        this.form.scopes = lodash__WEBPACK_IMPORTED_MODULE_0___default.a.reject(this.form.scopes, function (s) {
          return s == scope;
        });
      } else {
        this.form.scopes.push(scope);
      }
    },

    /**
     * Determine if the given scope has been assigned to the token.
     */
    scopeIsAssigned: function scopeIsAssigned(scope) {
      return lodash__WEBPACK_IMPORTED_MODULE_0___default.a.indexOf(this.form.scopes, scope) >= 0;
    },

    /**
     * Show the given access token to the user.
     */
    showAccessToken: function showAccessToken(accessToken) {
      this.createTokenModal = true;
      this.accessToken = accessToken;
      this.accessTokenModal = true;
    },

    /**
     * Revoke the given token.
     */
    revoke: function revoke(token) {
      var _this4 = this;

      axios["delete"]('/oauth/personal-access-tokens/' + token.id).then(function (response) {
        _this4.getTokens();
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AuthorizedClients__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AuthorizedClients */ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue");
/* harmony import */ var _Clients__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Clients */ "./resources/js/components/views/superadmin/tokens/Clients.vue");
/* harmony import */ var _PersonalAccessTokens__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./PersonalAccessTokens */ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue");
//
//
//
//
//
//
//
//
//
//
//
//
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
  components: {
    AuthorizedClients: _AuthorizedClients__WEBPACK_IMPORTED_MODULE_0__["default"],
    Clients: _Clients__WEBPACK_IMPORTED_MODULE_1__["default"],
    PersonalAccessTokens: _PersonalAccessTokens__WEBPACK_IMPORTED_MODULE_2__["default"]
  },
  data: function data() {
    return {};
  },
  mounted: function mounted() {},
  methods: {}
});

/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.action-link[data-v-62992c92] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&":
/*!*************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& ***!
  \*************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.action-link[data-v-9774665c] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(/*! ../../../../../../node_modules/css-loader/lib/css-base.js */ "./node_modules/css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, "\n.action-link[data-v-4b7c7324] {\n    cursor: pointer;\n}\n", ""]);

// exports


/***/ }),

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&":
/*!***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& ***!
  \***************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&");

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&":
/*!*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& ***!
  \*****************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&");

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

/***/ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/style-loader!./node_modules/css-loader??ref--6-1!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src??ref--6-2!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {


var content = __webpack_require__(/*! !../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& */ "./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&");

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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143&":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143& ***!
  \*************************************************************************************************************************************************************************************************************************/
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
      ? _c("div", { staticClass: "col-xl-12 col-lg-12 col-md-12 col-sm-12" }, [
          _c("div", { staticClass: "card top-blue-bdr" }, [
            _vm._m(0),
            _vm._v(" "),
            _c(
              "div",
              { staticClass: "card-body" },
              [
                _c("router-view"),
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
                      : _vm._e()
                  ],
                  1
                )
              ],
              1
            )
          ])
        ])
      : _c("div", { staticClass: "col-12 text-center mt-4" }, [
          _c("h5", [
            _vm._v(
              "Hmm, It appears that this ection has not yet been activated.  Please choose another section."
            )
          ])
        ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "card-header" }, [
      _c("div", { staticClass: "w-100 small text-right mt-1 mb-1 mr-4" }, [
        _c("a", { attrs: { href: "../../../log-viewer", target: "_blank" } }, [
          _c("i", { staticClass: "fal fa-bug" }),
          _vm._v(" Error Logs »")
        ]),
        _vm._v(" | \n                    "),
        _c(
          "a",
          { attrs: { href: "../../../horizon/dashboard", target: "_blank" } },
          [
            _c("i", { staticClass: "fal fa-layer-plus" }),
            _vm._v(" Redis Horizon Queue »")
          ]
        )
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c&":
/*!****************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c& ***!
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
                  class: { active: _vm.tab == "request" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "request"
                        }
                      }
                    },
                    [_vm._v("Request")]
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "response" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "response"
                        }
                      }
                    },
                    [
                      _c("i", { staticClass: "fal fa-exchange" }),
                      _vm._v(" Response")
                    ]
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "entity" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "entity"
                        }
                      }
                    },
                    [
                      _c("i", { staticClass: "fal fa-key" }),
                      _vm._v(" Entity Ref")
                    ]
                  )
                ]
              ),
              _vm._v(" "),
              _c(
                "li",
                {
                  staticClass: "nav-link",
                  class: { active: _vm.tab == "service" }
                },
                [
                  _c(
                    "a",
                    {
                      attrs: { href: "" },
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          _vm.tab = "service"
                        }
                      }
                    },
                    [
                      _c("i", { staticClass: "fal fa-envelope-open" }),
                      _vm._v(" Support")
                    ]
                  )
                ]
              )
            ])
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "request"
              ? _c("div", {}, [
                  _c("h5", [_vm._v(_vm._s(_vm.item.resource_ref))]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "json-edit" },
                    [
                      _c("v-jsoneditor", {
                        attrs: {
                          mode: "view",
                          modes: ["code", "view", "tree"]
                        },
                        on: {
                          input: function(val) {
                            return (_vm.requestJson = val)
                          },
                          "has-error": function($event) {
                            _vm.isError = true
                          }
                        },
                        model: {
                          value: _vm.requestJson,
                          callback: function($$v) {
                            _vm.requestJson = $$v
                          },
                          expression: "requestJson"
                        }
                      })
                    ],
                    1
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "response"
              ? _c("div", {}, [
                  _c("h5", [_vm._v(_vm._s(_vm.item.resource_ref))]),
                  _vm._v(" "),
                  _c(
                    "div",
                    { staticClass: "json-edit" },
                    [
                      _c("v-jsoneditor", {
                        attrs: {
                          mode: "view",
                          modes: ["code", "view", "tree"]
                        },
                        on: {
                          input: function(val) {
                            return (_vm.responseJson = val)
                          },
                          "has-error": function($event) {
                            _vm.isError = true
                          }
                        },
                        model: {
                          value: _vm.responseJson,
                          callback: function($$v) {
                            _vm.responseJson = $$v
                          },
                          expression: "responseJson"
                        }
                      })
                    ],
                    1
                  )
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "entity"
              ? _c("div", {}, [
                  _c("h5", { staticClass: "text-center" }, [
                    _vm._v("Feature Coming Soon..")
                  ])
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("transition", { attrs: { name: "bo-slide" } }, [
            _vm.tab == "service"
              ? _c("div", {}, [
                  _c("h5", { staticClass: "text-center" }, [
                    _vm._v("Email API Trouble Ticket Feature Coming Soon..")
                  ])
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

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817&":
/*!***********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817& ***!
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
                      "no-sort-reset": true,
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
                                    ? _c("i", {
                                        class: field.icon,
                                        on: {
                                          click: function($event) {
                                            return _vm.fetchGrid()
                                          }
                                        }
                                      })
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
                                        staticClass: "fal fa-question-circle",
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
                          key: "cell(created_at)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(
                                    _vm._f("localDate")(
                                      row.value,
                                      "MM/DD/YYYY LTS"
                                    )
                                  ) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(name)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " +
                                  _vm._s(row.value) +
                                  "\n            "
                              )
                            ]
                          }
                        },
                        {
                          key: "cell(status)",
                          fn: function(row) {
                            return [
                              _vm._v(
                                "\n                " + _vm._s(row.value) + " "
                              ),
                              _c("span", { staticClass: "small" }, [
                                _vm._v("[" + _vm._s(row.item.state) + "]")
                              ])
                            ]
                          }
                        },
                        {
                          key: "cell(actions)",
                          fn: function(row) {
                            return [
                              _c(
                                "a",
                                {
                                  staticClass:
                                    "btn btn-sm btn-primary float-right",
                                  attrs: { href: "" },
                                  on: {
                                    click: function($event) {
                                      $event.preventDefault()
                                      return _vm.loadModal(
                                        row.item.id,
                                        "request"
                                      )
                                    }
                                  }
                                },
                                [
                                  _c("i", {
                                    staticClass:
                                      "hotbox-icon hotbox-icon-search-2"
                                  })
                                ]
                              )
                            ]
                          }
                        },
                        {
                          key: "row-details",
                          fn: function() {
                            return undefined
                          },
                          proxy: true
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
                size: "xl",
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
                  staticClass: "modal-top-close fal fa-times",
                  on: {
                    click: function($event) {
                      _vm.editModal = !_vm.editModal
                    }
                  }
                }),
                _vm._v(" "),
                _c("h5", { staticClass: "w-100 mb-0 text-center" }, [
                  _vm._v("Service Log Details")
                ])
              ]),
              _vm._v(" "),
              _vm.editModal
                ? _c("edit-modal", {
                    attrs: { id: _vm.editId, view: _vm.editView }
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
    return _c("span", { staticClass: "input-group-prepend" }, [
      _c(
        "button",
        {
          staticClass: "btn btn-outline-secondary border-right-0",
          attrs: { type: "button" }
        },
        [_c("i", { staticClass: "fal fa-search" })]
      )
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025&":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025& ***!
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
  return _c("div", [
    _c("div", { staticClass: "col-12 d-flex justify-content-center" }, [
      _c("div", { staticClass: "col-sm-10 text-center mt-4 mb-4" }, [
        _c("div", { staticClass: "block-announce info" }, [
          _vm._m(0),
          _vm._v(" "),
          _c("p", [
            _vm._v(
              'I guess contrary to popular belief, or, perhaps from the dozens of modern ways you can contact your reclusive, overly focused, and somewhat antisocial "IT Team", Email is still the best way to reach support..  Welcome to ' +
                _vm._s(_vm.$store.state.disp.name) +
                "!"
            )
          ]),
          _vm._v(" "),
          _vm._m(1)
        ])
      ])
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", { staticClass: "title" }, [
      _c("i", { staticClass: "fas fa-life-ring fa-2x" }),
      _vm._v(" You Know..")
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("p", [
      _c("a", { attrs: { href: "support@hotboxerp.com", target: "_blank" } }, [
        _c("i", { staticClass: "hotbox-icon hotbox-icon-email" }),
        _vm._v(" Email: support@hotboxerp.com")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true&":
/*!********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true& ***!
  \********************************************************************************************************************************************************************************************************************************************************/
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
  return _c("div", [
    _vm.tokens.length > 0
      ? _c("div", [
          _c("div", { staticClass: "card card-default" }, [
            _c("div", { staticClass: "card-header" }, [
              _vm._v("Authorized Applications")
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "card-body" }, [
              _c("table", { staticClass: "table table-borderless mb-0" }, [
                _vm._m(0),
                _vm._v(" "),
                _c(
                  "tbody",
                  _vm._l(_vm.tokens, function(token) {
                    return _c("tr", [
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _vm._v(
                            "\n                                " +
                              _vm._s(token.client.name) +
                              "\n                            "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          token.scopes.length > 0
                            ? _c("span", [
                                _vm._v(
                                  "\n                                    " +
                                    _vm._s(token.scopes.join(", ")) +
                                    "\n                                "
                                )
                              ])
                            : _vm._e()
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _c(
                            "a",
                            {
                              staticClass: "action-link text-danger",
                              on: {
                                click: function($event) {
                                  return _vm.revoke(token)
                                }
                              }
                            },
                            [
                              _vm._v(
                                "\n                                    Revoke\n                                "
                              )
                            ]
                          )
                        ]
                      )
                    ])
                  }),
                  0
                )
              ])
            ])
          ])
        ])
      : _vm._e()
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("thead", [
      _c("tr", [
        _c("th", [_vm._v("Name")]),
        _vm._v(" "),
        _c("th", [_vm._v("Scopes")]),
        _vm._v(" "),
        _c("th")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true&":
/*!**********************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true& ***!
  \**********************************************************************************************************************************************************************************************************************************************/
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
  return _c(
    "div",
    [
      _c("div", { staticClass: "card card-default" }, [
        _c("div", { staticClass: "card-header" }, [
          _c(
            "div",
            {
              staticStyle: {
                display: "flex",
                "justify-content": "space-between",
                "align-items": "center"
              }
            },
            [
              _c("span", [
                _vm._v("\n                    OAuth Clients\n                ")
              ]),
              _vm._v(" "),
              _c(
                "a",
                {
                  staticClass: "action-link",
                  attrs: { tabindex: "-1" },
                  on: {
                    click: function($event) {
                      _vm.createClientModal = true
                    }
                  }
                },
                [
                  _vm._v(
                    "\n                    Create New Client\n                "
                  )
                ]
              )
            ]
          )
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "card-body" }, [
          _vm.clients.length === 0
            ? _c("p", { staticClass: "mb-0" }, [
                _vm._v(
                  "\n                You have not created any OAuth clients.\n            "
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _vm.clients.length > 0
            ? _c("table", { staticClass: "table table-borderless mb-0" }, [
                _vm._m(0),
                _vm._v(" "),
                _c(
                  "tbody",
                  _vm._l(_vm.clients, function(client) {
                    return _c("tr", [
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _vm._v(
                            "\n                            " +
                              _vm._s(client.id) +
                              "\n                        "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _vm._v(
                            "\n                            " +
                              _vm._s(client.name) +
                              "\n                        "
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [_c("code", [_vm._v(_vm._s(client.secret))])]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _c(
                            "a",
                            {
                              staticClass: "action-link",
                              attrs: { tabindex: "-1" },
                              on: {
                                click: function($event) {
                                  return _vm.edit(client)
                                }
                              }
                            },
                            [
                              _vm._v(
                                "\n                                Edit\n                            "
                              )
                            ]
                          )
                        ]
                      ),
                      _vm._v(" "),
                      _c(
                        "td",
                        { staticStyle: { "vertical-align": "middle" } },
                        [
                          _c(
                            "a",
                            {
                              staticClass: "action-link text-danger",
                              on: {
                                click: function($event) {
                                  return _vm.destroy(client)
                                }
                              }
                            },
                            [
                              _vm._v(
                                "\n                                Delete\n                            "
                              )
                            ]
                          )
                        ]
                      )
                    ])
                  }),
                  0
                )
              ])
            : _vm._e()
        ])
      ]),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          ref: "modal-create-client",
          attrs: {
            centered: "",
            title: "Create Client",
            size: "lg",
            "header-bg-variant": "light",
            "header-text-variant": "primary"
          },
          model: {
            value: _vm.createClientModal,
            callback: function($$v) {
              _vm.createClientModal = $$v
            },
            expression: "createClientModal"
          }
        },
        [
          _vm.createForm.errors.length > 0
            ? _c("div", { staticClass: "alert alert-danger" }, [
                _c("p", { staticClass: "mb-0" }, [
                  _c("strong", [_vm._v("Whoops!")]),
                  _vm._v(" Something went wrong!")
                ]),
                _vm._v(" "),
                _c("br"),
                _vm._v(" "),
                _c(
                  "ul",
                  _vm._l(_vm.createForm.errors, function(error) {
                    return _c("li", [
                      _vm._v(
                        "\n                                " +
                          _vm._s(error) +
                          "\n                            "
                      )
                    ])
                  }),
                  0
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("form", { attrs: { role: "form" } }, [
            _c("div", { staticClass: "form-group row" }, [
              _c("label", { staticClass: "col-md-3 col-form-label" }, [
                _vm._v("Name")
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-md-9" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.createForm.name,
                      expression: "createForm.name"
                    }
                  ],
                  staticClass: "form-control",
                  attrs: { id: "create-client-name", type: "text" },
                  domProps: { value: _vm.createForm.name },
                  on: {
                    keyup: function($event) {
                      if (
                        !$event.type.indexOf("key") &&
                        _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
                      ) {
                        return null
                      }
                      return _vm.store($event)
                    },
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(_vm.createForm, "name", $event.target.value)
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "form-text text-muted" }, [
                  _vm._v(
                    "\n                                    Something your users will recognize and trust.\n                                "
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "form-group row" }, [
              _c("label", { staticClass: "col-md-3 col-form-label" }, [
                _vm._v("Redirect URL")
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-md-9" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.createForm.redirect,
                      expression: "createForm.redirect"
                    }
                  ],
                  staticClass: "form-control",
                  attrs: { type: "text", name: "redirect" },
                  domProps: { value: _vm.createForm.redirect },
                  on: {
                    keyup: function($event) {
                      if (
                        !$event.type.indexOf("key") &&
                        _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
                      ) {
                        return null
                      }
                      return _vm.store($event)
                    },
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(_vm.createForm, "redirect", $event.target.value)
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "form-text text-muted" }, [
                  _vm._v(
                    "\n                                    Your application's authorization callback URL.\n                                "
                  )
                ])
              ])
            ])
          ]),
          _vm._v(" "),
          _c(
            "button",
            {
              staticClass: "btn btn-primary text-center w-100",
              attrs: { type: "button" },
              on: { click: _vm.store }
            },
            [_vm._v("\n                        Create\n                    ")]
          ),
          _vm._v(" "),
          _c("div", { attrs: { slot: "modal-footer" }, slot: "modal-footer" }, [
            _c(
              "span",
              {
                staticClass: "btn-label btn-sm btn-light float-right",
                on: {
                  click: function($event) {
                    _vm.createClientModal = !_vm.createClientModal
                  }
                }
              },
              [_vm._v("Close")]
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          ref: "modal-edit-client",
          attrs: {
            centered: "",
            title: "Edit Client",
            size: "lg",
            "header-bg-variant": "light",
            "header-text-variant": "primary"
          },
          model: {
            value: _vm.editClientModal,
            callback: function($$v) {
              _vm.editClientModal = $$v
            },
            expression: "editClientModal"
          }
        },
        [
          _vm.editForm.errors.length > 0
            ? _c("div", { staticClass: "alert alert-danger" }, [
                _c("p", { staticClass: "mb-0" }, [
                  _c("strong", [_vm._v("Whoops!")]),
                  _vm._v(" Something went wrong!")
                ]),
                _vm._v(" "),
                _c("br"),
                _vm._v(" "),
                _c(
                  "ul",
                  _vm._l(_vm.editForm.errors, function(error) {
                    return _c("li", [
                      _vm._v(
                        "\n                                " +
                          _vm._s(error) +
                          "\n                            "
                      )
                    ])
                  }),
                  0
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c("form", { attrs: { role: "form" } }, [
            _c("div", { staticClass: "form-group row" }, [
              _c("label", { staticClass: "col-md-3 col-form-label" }, [
                _vm._v("Name")
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-md-9" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.editForm.name,
                      expression: "editForm.name"
                    }
                  ],
                  staticClass: "form-control",
                  attrs: { id: "edit-client-name", type: "text" },
                  domProps: { value: _vm.editForm.name },
                  on: {
                    keyup: function($event) {
                      if (
                        !$event.type.indexOf("key") &&
                        _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
                      ) {
                        return null
                      }
                      return _vm.update($event)
                    },
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(_vm.editForm, "name", $event.target.value)
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "form-text text-muted" }, [
                  _vm._v(
                    "\n                                    Something your users will recognize and trust.\n                                "
                  )
                ])
              ])
            ]),
            _vm._v(" "),
            _c("div", { staticClass: "form-group row" }, [
              _c("label", { staticClass: "col-md-3 col-form-label" }, [
                _vm._v("Redirect URL")
              ]),
              _vm._v(" "),
              _c("div", { staticClass: "col-md-9" }, [
                _c("input", {
                  directives: [
                    {
                      name: "model",
                      rawName: "v-model",
                      value: _vm.editForm.redirect,
                      expression: "editForm.redirect"
                    }
                  ],
                  staticClass: "form-control",
                  attrs: { type: "text", name: "redirect" },
                  domProps: { value: _vm.editForm.redirect },
                  on: {
                    keyup: function($event) {
                      if (
                        !$event.type.indexOf("key") &&
                        _vm._k($event.keyCode, "enter", 13, $event.key, "Enter")
                      ) {
                        return null
                      }
                      return _vm.update($event)
                    },
                    input: function($event) {
                      if ($event.target.composing) {
                        return
                      }
                      _vm.$set(_vm.editForm, "redirect", $event.target.value)
                    }
                  }
                }),
                _vm._v(" "),
                _c("span", { staticClass: "form-text text-muted" }, [
                  _vm._v(
                    "\n                                    Your application's authorization callback URL.\n                                "
                  )
                ])
              ])
            ])
          ]),
          _vm._v(" "),
          _c("div", { attrs: { slot: "modal-footer" }, slot: "modal-footer" }, [
            _c(
              "span",
              {
                staticClass: "btn-label btn-sm btn-light",
                on: {
                  click: function($event) {
                    _vm.editClientModal = !_vm.editClientModal
                  }
                }
              },
              [_vm._v("Close")]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-primary",
                attrs: { type: "button" },
                on: { click: _vm.update }
              },
              [
                _vm._v(
                  "\n                        Save Changes\n                    "
                )
              ]
            )
          ])
        ]
      )
    ],
    1
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("thead", [
      _c("tr", [
        _c("th", [_vm._v("Client ID")]),
        _vm._v(" "),
        _c("th", [_vm._v("Name")]),
        _vm._v(" "),
        _c("th", [_vm._v("Secret")]),
        _vm._v(" "),
        _c("th"),
        _vm._v(" "),
        _c("th")
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true&":
/*!***********************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true& ***!
  \***********************************************************************************************************************************************************************************************************************************************************/
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
  return _c(
    "div",
    [
      _c("div", [
        _c("div", { staticClass: "card card-default" }, [
          _c("div", { staticClass: "card-header" }, [
            _c(
              "div",
              {
                staticStyle: {
                  display: "flex",
                  "justify-content": "space-between",
                  "align-items": "center"
                }
              },
              [
                _c("span", [
                  _vm._v(
                    "\n                        Personal Access Tokens\n                    "
                  )
                ]),
                _vm._v(" "),
                _c(
                  "a",
                  {
                    staticClass: "action-link",
                    attrs: { tabindex: "-1" },
                    on: {
                      click: function($event) {
                        _vm.createTokenModal = true
                      }
                    }
                  },
                  [
                    _vm._v(
                      "\n                        Create New Token\n                    "
                    )
                  ]
                )
              ]
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "card-body" }, [
            _vm.tokens.length === 0
              ? _c("p", { staticClass: "mb-0" }, [
                  _vm._v(
                    "\n                    You have not created any personal access tokens.\n                "
                  )
                ])
              : _vm._e(),
            _vm._v(" "),
            _vm.tokens.length > 0
              ? _c("table", { staticClass: "table table-borderless mb-0" }, [
                  _vm._m(0),
                  _vm._v(" "),
                  _c(
                    "tbody",
                    _vm._l(_vm.tokens, function(token) {
                      return _c("tr", [
                        _c(
                          "td",
                          { staticStyle: { "vertical-align": "middle" } },
                          [
                            _vm._v(
                              "\n                                " +
                                _vm._s(token.name) +
                                "\n                            "
                            )
                          ]
                        ),
                        _vm._v(" "),
                        _c(
                          "td",
                          { staticStyle: { "vertical-align": "middle" } },
                          [
                            _c(
                              "a",
                              {
                                staticClass: "action-link text-danger",
                                on: {
                                  click: function($event) {
                                    return _vm.revoke(token)
                                  }
                                }
                              },
                              [
                                _vm._v(
                                  "\n                                    Delete\n                                "
                                )
                              ]
                            )
                          ]
                        )
                      ])
                    }),
                    0
                  )
                ])
              : _vm._e()
          ])
        ])
      ]),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          ref: "modal-create-token",
          attrs: {
            centered: "",
            title: "Create Token",
            size: "lg",
            "header-bg-variant": "light",
            "header-text-variant": "primary"
          },
          model: {
            value: _vm.createTokenModal,
            callback: function($$v) {
              _vm.createTokenModal = $$v
            },
            expression: "createTokenModal"
          }
        },
        [
          _vm.form.errors.length > 0
            ? _c("div", { staticClass: "alert alert-danger" }, [
                _c("p", { staticClass: "mb-0" }, [
                  _c("strong", [_vm._v("Whoops!")]),
                  _vm._v(" Something went wrong!")
                ]),
                _vm._v(" "),
                _c("br"),
                _vm._v(" "),
                _c(
                  "ul",
                  _vm._l(_vm.form.errors, function(error) {
                    return _c("li", [
                      _vm._v(
                        "\n                                " +
                          _vm._s(error) +
                          "\n                            "
                      )
                    ])
                  }),
                  0
                )
              ])
            : _vm._e(),
          _vm._v(" "),
          _c(
            "form",
            {
              attrs: { role: "form" },
              on: {
                submit: function($event) {
                  $event.preventDefault()
                  return _vm.store($event)
                }
              }
            },
            [
              _c("div", { staticClass: "form-group row" }, [
                _c("label", { staticClass: "col-md-4 col-form-label" }, [
                  _vm._v("Name")
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "col-md-6" }, [
                  _c("input", {
                    directives: [
                      {
                        name: "model",
                        rawName: "v-model",
                        value: _vm.form.name,
                        expression: "form.name"
                      }
                    ],
                    staticClass: "form-control",
                    attrs: {
                      id: "create-token-name",
                      type: "text",
                      name: "name"
                    },
                    domProps: { value: _vm.form.name },
                    on: {
                      input: function($event) {
                        if ($event.target.composing) {
                          return
                        }
                        _vm.$set(_vm.form, "name", $event.target.value)
                      }
                    }
                  })
                ])
              ]),
              _vm._v(" "),
              _vm.scopes.length > 0
                ? _c("div", { staticClass: "form-group row" }, [
                    _c("label", { staticClass: "col-md-4 col-form-label" }, [
                      _vm._v("Scopes")
                    ]),
                    _vm._v(" "),
                    _c(
                      "div",
                      { staticClass: "col-md-6" },
                      _vm._l(_vm.scopes, function(scope) {
                        return _c("div", [
                          _c("div", { staticClass: "checkbox" }, [
                            _c("label", [
                              _c("input", {
                                attrs: { type: "checkbox" },
                                domProps: {
                                  checked: _vm.scopeIsAssigned(scope.id)
                                },
                                on: {
                                  click: function($event) {
                                    return _vm.toggleScope(scope.id)
                                  }
                                }
                              }),
                              _vm._v(
                                "\n\n                                                " +
                                  _vm._s(scope.id) +
                                  "\n                                        "
                              )
                            ])
                          ])
                        ])
                      }),
                      0
                    )
                  ])
                : _vm._e()
            ]
          ),
          _vm._v(" "),
          _c("div", { attrs: { slot: "modal-footer" }, slot: "modal-footer" }, [
            _c(
              "span",
              {
                staticClass: "btn-label btn-sm btn-light",
                on: {
                  click: function($event) {
                    _vm.createTokenModal = !_vm.createTokenModal
                  }
                }
              },
              [_vm._v("Close")]
            ),
            _vm._v(" "),
            _c(
              "button",
              {
                staticClass: "btn btn-primary",
                attrs: { type: "button" },
                on: { click: _vm.store }
              },
              [_vm._v("\n                        Create\n                    ")]
            )
          ])
        ]
      ),
      _vm._v(" "),
      _c(
        "b-modal",
        {
          ref: "modal-access-token",
          attrs: {
            centered: "",
            title: "Personal Access Token",
            size: "lg",
            "header-bg-variant": "light",
            "header-text-variant": "primary"
          },
          model: {
            value: _vm.accessTokenModal,
            callback: function($$v) {
              _vm.accessTokenModal = $$v
            },
            expression: "accessTokenModal"
          }
        },
        [
          _c("p", [
            _vm._v(
              "\n                        Here is your new personal access token. This is the only time it will be shown so don't lose it!\n                        You may now use this token to make API requests.\n                    "
            )
          ]),
          _vm._v(" "),
          _c(
            "textarea",
            { staticClass: "form-control", attrs: { rows: "10" } },
            [_vm._v(_vm._s(_vm.accessToken))]
          ),
          _vm._v(" "),
          _c("div", { attrs: { slot: "modal-footer" }, slot: "modal-footer" }, [
            _c(
              "span",
              {
                staticClass: "btn-label btn-sm btn-light",
                on: {
                  click: function($event) {
                    _vm.accessTokenModal = !_vm.accessTokenModal
                  }
                }
              },
              [_vm._v("Close")]
            )
          ])
        ]
      )
    ],
    1
  )
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("thead", [
      _c("tr", [_c("th", [_vm._v("Name")]), _vm._v(" "), _c("th")])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868& ***!
  \*******************************************************************************************************************************************************************************************************************************/
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
  return _c("div", [
    _c(
      "div",
      { staticClass: "col-12 mt-2 mb-4" },
      [
        _c("h5", { staticClass: "mt-3" }, [_vm._v("Manage Clients:")]),
        _vm._v(" "),
        _c("clients")
      ],
      1
    ),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "col-12 mt-2 mb-4" },
      [
        _c("h5", { staticClass: "mt-3" }, [
          _vm._v("Manage Personal Acess Tokens:")
        ]),
        _vm._v(" "),
        _c("personal-access-tokens")
      ],
      1
    ),
    _vm._v(" "),
    _c(
      "div",
      { staticClass: "col-12 mt-2 mb-4" },
      [
        _c("h5", { staticClass: "mt-3" }, [_vm._v("Authorized Clients:")]),
        _vm._v(" "),
        _c("authorized-clients")
      ],
      1
    )
  ])
}
var staticRenderFns = []
render._withStripped = true



/***/ }),

/***/ "./resources/js/components/views/superadmin/index.vue":
/*!************************************************************!*\
  !*** ./resources/js/components/views/superadmin/index.vue ***!
  \************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.vue?vue&type=template&id=2017e143& */ "./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143&");
/* harmony import */ var _index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./index.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__["render"],
  _index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/index.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js&":
/*!*************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js& ***!
  \*************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./index.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/index.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143& ***!
  \*******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./index.vue?vue&type=template&id=2017e143& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/index.vue?vue&type=template&id=2017e143&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_index_vue_vue_type_template_id_2017e143___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/editModal.vue":
/*!***************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/editModal.vue ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editModal.vue?vue&type=template&id=44950b7c& */ "./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c&");
/* harmony import */ var _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./editModal.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/servicelog/editModal.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js&":
/*!****************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js& ***!
  \****************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c&":
/*!**********************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c& ***!
  \**********************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./editModal.vue?vue&type=template&id=44950b7c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/editModal.vue?vue&type=template&id=44950b7c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_editModal_vue_vue_type_template_id_44950b7c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/grid.vue":
/*!**********************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/grid.vue ***!
  \**********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=74db7817& */ "./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/servicelog/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817&":
/*!*****************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817& ***!
  \*****************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=74db7817& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/servicelog/grid.vue?vue&type=template&id=74db7817&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_74db7817___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/support/grid.vue":
/*!*******************************************************************!*\
  !*** ./resources/js/components/views/superadmin/support/grid.vue ***!
  \*******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=0665f025& */ "./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/support/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js&":
/*!********************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/support/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025&":
/*!**************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025& ***!
  \**************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=0665f025& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/support/grid.vue?vue&type=template&id=0665f025&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_0665f025___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue":
/*!*******************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue ***!
  \*******************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true& */ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true&");
/* harmony import */ var _AuthorizedClients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./AuthorizedClients.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& */ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _AuthorizedClients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "62992c92",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/tokens/AuthorizedClients.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AuthorizedClients.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&":
/*!****************************************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& ***!
  \****************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=style&index=0&id=62992c92&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_style_index_0_id_62992c92_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true&":
/*!**************************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true& ***!
  \**************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/AuthorizedClients.vue?vue&type=template&id=62992c92&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_AuthorizedClients_vue_vue_type_template_id_62992c92_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/Clients.vue":
/*!*********************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/Clients.vue ***!
  \*********************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Clients.vue?vue&type=template&id=9774665c&scoped=true& */ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true&");
/* harmony import */ var _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Clients.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& */ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "9774665c",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/tokens/Clients.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js&":
/*!**********************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js& ***!
  \**********************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&":
/*!******************************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& ***!
  \******************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=style&index=0&id=9774665c&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_style_index_0_id_9774665c_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true&":
/*!****************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true& ***!
  \****************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./Clients.vue?vue&type=template&id=9774665c&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/Clients.vue?vue&type=template&id=9774665c&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Clients_vue_vue_type_template_id_9774665c_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue":
/*!**********************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue ***!
  \**********************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true& */ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true&");
/* harmony import */ var _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& */ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");






/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_3__["default"])(
  _PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"],
  _PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  "4b7c7324",
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&":
/*!*******************************************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& ***!
  \*******************************************************************************************************************************************/
/*! no static exports found */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/style-loader!../../../../../../node_modules/css-loader??ref--6-1!../../../../../../node_modules/vue-loader/lib/loaders/stylePostLoader.js!../../../../../../node_modules/postcss-loader/src??ref--6-2!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css& */ "./node_modules/style-loader/index.js!./node_modules/css-loader/index.js?!./node_modules/vue-loader/lib/loaders/stylePostLoader.js!./node_modules/postcss-loader/src/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=style&index=0&id=4b7c7324&scoped=true&lang=css&");
/* harmony import */ var _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__);
/* harmony reexport (unknown) */ for(var __WEBPACK_IMPORT_KEY__ in _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__) if(__WEBPACK_IMPORT_KEY__ !== 'default') (function(key) { __webpack_require__.d(__webpack_exports__, key, function() { return _node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0__[key]; }) }(__WEBPACK_IMPORT_KEY__));
 /* harmony default export */ __webpack_exports__["default"] = (_node_modules_style_loader_index_js_node_modules_css_loader_index_js_ref_6_1_node_modules_vue_loader_lib_loaders_stylePostLoader_js_node_modules_postcss_loader_src_index_js_ref_6_2_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_style_index_0_id_4b7c7324_scoped_true_lang_css___WEBPACK_IMPORTED_MODULE_0___default.a); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true&":
/*!*****************************************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true& ***!
  \*****************************************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/PersonalAccessTokens.vue?vue&type=template&id=4b7c7324&scoped=true&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_PersonalAccessTokens_vue_vue_type_template_id_4b7c7324_scoped_true___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/grid.vue":
/*!******************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/grid.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./grid.vue?vue&type=template&id=e9d7a868& */ "./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868&");
/* harmony import */ var _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./grid.vue?vue&type=script&lang=js& */ "./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__["render"],
  _grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/views/superadmin/tokens/grid.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../../node_modules/vue-loader/lib??vue-loader-options!./grid.vue?vue&type=template&id=e9d7a868& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/components/views/superadmin/tokens/grid.vue?vue&type=template&id=e9d7a868&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_grid_vue_vue_type_template_id_e9d7a868___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/models/Servicelog.js":
/*!*******************************************!*\
  !*** ./resources/js/models/Servicelog.js ***!
  \*******************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "default", function() { return Servicelog; });
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



var Servicelog =
/*#__PURE__*/
function (_Model) {
  _inherits(Servicelog, _Model);

  function Servicelog() {
    _classCallCheck(this, Servicelog);

    return _possibleConstructorReturn(this, _getPrototypeOf(Servicelog).apply(this, arguments));
  }

  _createClass(Servicelog, [{
    key: "resource",
    value: function resource() {
      return 'admin/auth/servicelogs';
    }
  }, {
    key: "primaryKey",
    value: function primaryKey() {
      return 'id';
    }
  }]);

  return Servicelog;
}(_Model__WEBPACK_IMPORTED_MODULE_0__["default"]);



/***/ })

}]);