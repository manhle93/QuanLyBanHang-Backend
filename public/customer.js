(window["webpackJsonp"] = window["webpackJsonp"] || []).push([["customer"],{

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-loading-overlay */ "./node_modules/vue-loading-overlay/dist/vue-loading.min.js");
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vue-loading-overlay/dist/vue-loading.css */ "./node_modules/vue-loading-overlay/dist/vue-loading.css");
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_2__);


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
 // Import stylesheet


/* harmony default export */ __webpack_exports__["default"] = ({
  components: {
    Loading: vue_loading_overlay__WEBPACK_IMPORTED_MODULE_1___default.a
  },
  props: ["user_id"],
  data: function data() {
    return {
      isLoading: false
    };
  },
  created: function created() {
    if (!this.user_id) this.$router.push("/");
  },
  methods: {
    resend: function () {
      var _resend = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var status;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                try {
                  status = axios.get("/auth/resend/" + this.user_id);
                  this.$swal.fire({
                    type: "success",
                    title: "Đã gửi lại email",
                    showConfirmButton: false,
                    timer: 1500 //toast: true

                  });
                } catch (error) {
                  console.log(error.response.data);
                  this.isLoading = false;
                }

              case 1:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function resend() {
        return _resend.apply(this, arguments);
      }

      return resend;
    }()
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js&":
/*!********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js& ***!
  \********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuelidate */ "./node_modules/vuelidate/lib/index.js");
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vuelidate__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuelidate/lib/validators */ "./node_modules/vuelidate/lib/validators/index.js");
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-loading-overlay */ "./node_modules/vue-loading-overlay/dist/vue-loading.min.js");
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-loading-overlay/dist/vue-loading.css */ "./node_modules/vue-loading-overlay/dist/vue-loading.css");
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4__);


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




/* harmony default export */ __webpack_exports__["default"] = ({
  mixins: [vuelidate__WEBPACK_IMPORTED_MODULE_1__["validationMixin"]],
  components: {
    Loading: vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  validations: {
    form: {
      email: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["required"],
        email: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["email"]
      },
      password: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["required"]
      }
    }
  },
  data: function data() {
    return {
      isLoading: false,
      form: {
        email: "",
        password: "",
        name: "",
        phone: ""
      },
      error: false
    };
  },
  methods: {
    submit: function submit() {
      this.$v.form.$touch();

      if (this.$v.form.$anyError) {
        return;
      }

      this.isLoading = true;
      this.login();
    },
    login: function () {
      var _login = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var loginInfo;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.prev = 0;
                _context.next = 3;
                return axios.post("/auth/login", this.form);

              case 3:
                loginInfo = _context.sent;
                Auth.storeAfterLogin(loginInfo);
                this.$router.push("/");
                _context.next = 12;
                break;

              case 8:
                _context.prev = 8;
                _context.t0 = _context["catch"](0);
                if (_context.t0.response.status === 401) this.error = true;else if (_context.t0.response.status === 406) this.$router.push({
                  name: "email-confirmation",
                  params: {
                    user_id: _context.t0.response.data.user_id
                  }
                });else console.log(_context.t0);
                this.isLoading = false;

              case 12:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[0, 8]]);
      }));

      function login() {
        return _login.apply(this, arguments);
      }

      return login;
    }()
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js&":
/*!***********************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js& ***!
  \***********************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuelidate */ "./node_modules/vuelidate/lib/index.js");
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vuelidate__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! vuelidate/lib/validators */ "./node_modules/vuelidate/lib/validators/index.js");
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! vue-loading-overlay */ "./node_modules/vue-loading-overlay/dist/vue-loading.min.js");
/* harmony import */ var vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-loading-overlay/dist/vue-loading.css */ "./node_modules/vue-loading-overlay/dist/vue-loading.css");
/* harmony import */ var vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(vue_loading_overlay_dist_vue_loading_css__WEBPACK_IMPORTED_MODULE_4__);


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


 // Import stylesheet


/* harmony default export */ __webpack_exports__["default"] = ({
  mixins: [vuelidate__WEBPACK_IMPORTED_MODULE_1__["validationMixin"]],
  components: {
    Loading: vue_loading_overlay__WEBPACK_IMPORTED_MODULE_3___default.a
  },
  validations: {
    form: {
      email: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["required"],
        email: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["email"]
      },
      password: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["required"],
        minLength: Object(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_2__["minLength"])(8)
      }
    }
  },
  data: function data() {
    return {
      isLoading: false,
      form: {
        email: "",
        password: "",
        name: "",
        phone: ""
      },
      errorEmail: false
    };
  },
  methods: {
    submit: function submit() {
      this.$v.form.$touch();

      if (this.$v.form.$anyError) {
        return;
      }

      this.register();
    },
    register: function () {
      var _register = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee() {
        var registerInfo;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                this.isLoading = true;
                _context.prev = 1;
                _context.next = 4;
                return axios.post("/auth/register", this.form).then();

              case 4:
                registerInfo = _context.sent;
                this.isLoading = false;
                this.$router.push({
                  name: "email-confirmation",
                  params: {
                    user_id: registerInfo.data.user_id
                  }
                });
                _context.next = 13;
                break;

              case 9:
                _context.prev = 9;
                _context.t0 = _context["catch"](1);

                if (_context.t0.response.status === 422) {
                  if (_context.t0.response.data.errors.email) {
                    this.errorEmail = true;
                  }
                } else console.log(_context.t0);

                this.isLoading = false;

              case 13:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[1, 9]]);
      }));

      function register() {
        return _register.apply(this, arguments);
      }

      return register;
    }()
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js&":
/*!***************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js& ***!
  \***************************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vuelidate */ "./node_modules/vuelidate/lib/index.js");
/* harmony import */ var vuelidate__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vuelidate__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vuelidate/lib/validators */ "./node_modules/vuelidate/lib/validators/index.js");
/* harmony import */ var vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__);
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
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
  mixins: [vuelidate__WEBPACK_IMPORTED_MODULE_0__["validationMixin"]],
  validations: {
    form: {
      name: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__["required"]
      },
      code: {
        required: vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__["required"],
        minLength: Object(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__["minLength"])(3),
        maxLength: Object(vuelidate_lib_validators__WEBPACK_IMPORTED_MODULE_1__["maxLength"])(3)
      }
    }
  },
  data: function data() {
    return {
      form: {
        name: "",
        code: ""
      }
    };
  },
  created: function created() {
    this.$emit("header", "name");
    console.log(Auth.isSetup());
  },
  methods: {
    submit: function submit() {
      this.$v.form.$touch();

      if (this.$v.form.$anyError) {
        return;
      }

      this.$router.push({
        name: "services",
        params: {
          form: this.form
        }
      });
    }
  }
});

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js&":
/*!************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib??ref--4-0!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js& ***!
  \************************************************************************************************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


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
/* harmony default export */ __webpack_exports__["default"] = ({
  props: ["form"],
  created: function created() {
    this.$emit("header", "service");

    if (!this.form) {
      this.$router.push("/");
    }
  },
  methods: {
    setupOrganization: function () {
      var _setupOrganization = _asyncToGenerator(
      /*#__PURE__*/
      _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.mark(function _callee($id) {
        var status;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default.a.wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                _context.prev = 0;
                this.form["service_id"] = $id;
                _context.next = 4;
                return axios.post("/auth/setup", this.form);

              case 4:
                status = _context.sent;
                localStorage.setItem("isSetup", true);
                this.$router.push("/");
                _context.next = 12;
                break;

              case 9:
                _context.prev = 9;
                _context.t0 = _context["catch"](0);
                console.log(_context.t0.response.data);

              case 12:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[0, 9]]);
      }));

      function setupOrganization(_x) {
        return _setupOrganization.apply(this, arguments);
      }

      return setupOrganization;
    }()
  }
});

/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898&":
/*!************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898& ***!
  \************************************************************************************************************************************************************************************************************************/
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
    _vm.user_id
      ? _c(
          "main",
          { staticClass: "container" },
          [
            _vm._m(0),
            _vm._v(" "),
            _c("section", { staticClass: "email-confirmation" }, [
              _c("form", { attrs: { action: "" } }, [
                _c("div", { staticClass: "form-title" }, [
                  _vm._v("Xác nhận email")
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-description" }, [
                  _vm._v(
                    "Vui lòng kiểm tra email và click vào link để xác nhận tài khoản của bạn!"
                  )
                ]),
                _vm._v(" "),
                _c("div", { staticClass: "form-group" }, [
                  _c(
                    "button",
                    {
                      staticClass: "btn btn-action",
                      on: {
                        click: function($event) {
                          $event.preventDefault()
                          return _vm.resend($event)
                        }
                      }
                    },
                    [_vm._v("Gửi lại email")]
                  )
                ]),
                _vm._v(" "),
                _vm._m(1)
              ])
            ]),
            _vm._v(" "),
            _c("loading", {
              attrs: {
                active: _vm.isLoading,
                "can-cancel": true,
                "is-full-page": true
              },
              on: {
                "update:active": function($event) {
                  _vm.isLoading = $event
                }
              }
            })
          ],
          1
        )
      : _vm._e()
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("aside", [
      _c("img", {
        staticStyle: { width: "410px" },
        attrs: { src: "/images/customer/vector image 4.svg", alt: "" }
      }),
      _vm._v(" "),
      _c("p", [_vm._v("Xác nhận địa chỉ email của bạn!")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-social" }, [
        _c("span", [_vm._v("Liên hệ hỗ trợ")])
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8&":
/*!************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8& ***!
  \************************************************************************************************************************************************************************************************************/
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
  return _c("main", { staticClass: "container" }, [
    _vm._m(0),
    _vm._v(" "),
    _c(
      "section",
      { staticClass: "login" },
      [
        _c("form", { attrs: { action: "" } }, [
          _c("div", { staticClass: "form-title" }, [_vm._v("Đăng nhập")]),
          _vm._v(" "),
          _c("div", { staticClass: "form-description" }, [
            _vm._v("Chúc mừng! Vui lòng đăng nhập để sử dụng dịch vụ")
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _c("label", { attrs: { for: "" } }, [_vm._v("Email")]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.$v.form.email.$model,
                  expression: "$v.form.email.$model"
                }
              ],
              staticClass: "form-control",
              attrs: { type: "email", placeholder: "Nhập Email của bạn" },
              domProps: { value: _vm.$v.form.email.$model },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.$v.form.email, "$model", $event.target.value)
                }
              }
            }),
            _vm._v(" "),
            _vm.$v.form.$dirty
              ? _c("div", { staticClass: "form-feedback" }, [
                  !_vm.$v.form.email.required
                    ? _c("span", [_vm._v("Bạn chưa nhập email")])
                    : _vm._e(),
                  _vm._v(" "),
                  !_vm.$v.form.email.email
                    ? _c("span", [_vm._v("Bạn hãy nhập một địa chỉ email")])
                    : _vm._e()
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _vm._m(1),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.$v.form.password.$model,
                  expression: "$v.form.password.$model"
                }
              ],
              staticClass: "form-control",
              attrs: { type: "password", placeholder: "Nhập mật khẩu" },
              domProps: { value: _vm.$v.form.password.$model },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.$v.form.password, "$model", $event.target.value)
                }
              }
            }),
            _vm._v(" "),
            _vm.$v.form.$dirty
              ? _c("div", { staticClass: "form-feedback" }, [
                  !_vm.$v.form.password.required
                    ? _c("span", [_vm._v("Bạn chưa nhập mật khẩu")])
                    : _vm._e()
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-action",
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.submit($event)
                  }
                }
              },
              [_vm._v("Đăng nhập")]
            )
          ]),
          _vm._v(" "),
          _vm.error
            ? _c(
                "div",
                {
                  staticClass: "form-feedback",
                  staticStyle: { "text-align": "center", "margin-top": "5px" }
                },
                [_vm._v("Sai địa chỉ email hoặc mật khẩu")]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm._m(2),
          _vm._v(" "),
          _vm._m(3),
          _vm._v(" "),
          _vm._m(4),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "register-link" },
            [
              _vm._v(
                "\n                Bạn không có tài khoản?\n                "
              ),
              _c("router-link", { attrs: { to: "/customer/register" } }, [
                _vm._v("Đăng kí tài khoản")
              ])
            ],
            1
          )
        ]),
        _vm._v(" "),
        _c("loading", {
          attrs: {
            active: _vm.isLoading,
            "can-cancel": true,
            "is-full-page": true
          },
          on: {
            "update:active": function($event) {
              _vm.isLoading = $event
            }
          }
        })
      ],
      1
    )
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("aside", [
      _c("img", {
        attrs: { src: "/images/customer/vector image 1.svg", alt: "" }
      }),
      _vm._v(" "),
      _c("p", [_vm._v("Linh hoạt trên mọi trường hợp chấm công!")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "" } }, [
      _c("div", [
        _vm._v("\n                        Mật khẩu\n                        "),
        _c("a", { attrs: { href: "" } }, [_vm._v("Quên mật khẩu?")])
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "divider" }, [
      _c("hr"),
      _vm._v(" "),
      _c("span", [_vm._v("Hoặc")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-social btn-fb" }, [
        _c("img", { attrs: { src: "/images/customer/icon-fb.png", alt: "" } }),
        _vm._v(" "),
        _c("span", [_vm._v("Đăng kí bằng tài khoản Facebook")])
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-social btn-gg" }, [
        _c("img", { attrs: { src: "/images/customer/icon-gg.png", alt: "" } }),
        _vm._v(" "),
        _c("span", [_vm._v("Đăng kí bằng tài khoản Google")])
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0&":
/*!***************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0& ***!
  \***************************************************************************************************************************************************************************************************************/
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
    "main",
    { staticClass: "container" },
    [
      _vm._m(0),
      _vm._v(" "),
      _c("section", { staticClass: "register" }, [
        _c("form", { attrs: { action: "" } }, [
          _c("div", { staticClass: "form-title" }, [_vm._v("Đăng ký")]),
          _vm._v(" "),
          _c("div", { staticClass: "form-description" }, [
            _vm._v(
              "Miễn phí trải nghiệm 05 ngày cho việc quản lý nhân sự trở nên dễ dàng hơn"
            )
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _vm._m(1),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.$v.form.email.$model,
                  expression: "$v.form.email.$model"
                }
              ],
              staticClass: "form-control",
              attrs: { type: "email", placeholder: "Nhập Email của bạn" },
              domProps: { value: _vm.$v.form.email.$model },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.$v.form.email, "$model", $event.target.value)
                }
              }
            }),
            _vm._v(" "),
            _vm.$v.form.$dirty
              ? _c("div", { staticClass: "form-feedback" }, [
                  !_vm.$v.form.email.required
                    ? _c("span", [_vm._v("Bạn chưa nhập email")])
                    : _vm._e(),
                  _vm._v(" "),
                  !_vm.$v.form.email.email
                    ? _c("span", [_vm._v("Bạn hãy nhập một địa chỉ email")])
                    : _vm._e()
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _vm._m(2),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.$v.form.password.$model,
                  expression: "$v.form.password.$model"
                }
              ],
              staticClass: "form-control",
              attrs: { type: "password", placeholder: "Nhập mật khẩu" },
              domProps: { value: _vm.$v.form.password.$model },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.$v.form.password, "$model", $event.target.value)
                }
              }
            }),
            _vm._v(" "),
            _vm.$v.form.$dirty
              ? _c("div", { staticClass: "form-feedback" }, [
                  !_vm.$v.form.password.required
                    ? _c("span", [_vm._v("Bạn chưa nhập mật khẩu")])
                    : _vm._e(),
                  _vm._v(" "),
                  !_vm.$v.form.password.minLength
                    ? _c("span", [
                        _vm._v("Mật khẩu phải có độ dài tối thiểu 8 kí tự")
                      ])
                    : _vm._e()
                ])
              : _vm._e()
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _c("label", { attrs: { for: "" } }, [_vm._v("Họ và tên")]),
            _vm._v(" "),
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
              attrs: { type: "email", placeholder: "Nhập họ tên của bạn" },
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
          ]),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _c("label", { attrs: { for: "" } }, [_vm._v("Số điện thoại")]),
            _vm._v(" "),
            _c("input", {
              directives: [
                {
                  name: "model",
                  rawName: "v-model",
                  value: _vm.form.phone,
                  expression: "form.phone"
                }
              ],
              staticClass: "form-control",
              attrs: { type: "email", placeholder: "Nhập Email của bạn" },
              domProps: { value: _vm.form.phone },
              on: {
                input: function($event) {
                  if ($event.target.composing) {
                    return
                  }
                  _vm.$set(_vm.form, "phone", $event.target.value)
                }
              }
            })
          ]),
          _vm._v(" "),
          _vm._m(3),
          _vm._v(" "),
          _c("div", { staticClass: "form-group" }, [
            _c(
              "button",
              {
                staticClass: "btn btn-action",
                on: {
                  click: function($event) {
                    $event.preventDefault()
                    return _vm.submit($event)
                  }
                }
              },
              [_vm._v("Tạo tài khoản")]
            )
          ]),
          _vm._v(" "),
          _vm.errorEmail
            ? _c(
                "div",
                {
                  staticClass: "form-feedback",
                  staticStyle: { "text-align": "center", "margin-top": "5px" }
                },
                [_vm._v("Địa chỉ email đã tồn tại")]
              )
            : _vm._e(),
          _vm._v(" "),
          _vm._m(4),
          _vm._v(" "),
          _vm._m(5),
          _vm._v(" "),
          _vm._m(6),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "register-link" },
            [
              _vm._v(
                "\n                Bạn không có tài khoản?\n                "
              ),
              _c("router-link", { attrs: { to: "/customer/register" } }, [
                _vm._v("Đăng kí tài khoản")
              ])
            ],
            1
          )
        ])
      ]),
      _vm._v(" "),
      _c("loading", {
        attrs: {
          active: _vm.isLoading,
          "can-cancel": true,
          "is-full-page": true
        },
        on: {
          "update:active": function($event) {
            _vm.isLoading = $event
          }
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
    return _c("aside", [
      _c("img", {
        attrs: { src: "/images/customer/vector image 1.svg", alt: "" }
      }),
      _vm._v(" "),
      _c("p", [_vm._v("Linh hoạt trên mọi trường hợp chấm công!")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "" } }, [
      _vm._v("\n                    Email\n                    "),
      _c("span", { staticClass: "required-field" }, [_vm._v("(*)")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("label", { attrs: { for: "" } }, [
      _vm._v("\n                    Mật khẩu\n                    "),
      _c("span", { staticClass: "required-field" }, [_vm._v("(*)")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("p", [
        _vm._v(
          "\n                    Bằng việc nhấn vào nút\n                    "
        ),
        _c("b", [_vm._v("Tạo tài khoản")]),
        _vm._v(
          " dưới đây, bạn đã đồng ý các chính sách và điều\n                    khoản sử dụng hệ thống của chúng tôi.\n                "
        )
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "divider" }, [
      _c("hr"),
      _vm._v(" "),
      _c("span", [_vm._v("Hoặc")])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-social btn-fb" }, [
        _c("img", { attrs: { src: "/images/customer/icon-fb.png", alt: "" } }),
        _vm._v(" "),
        _c("span", [_vm._v("Đăng kí bằng tài khoản Facebook")])
      ])
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "form-group" }, [
      _c("button", { staticClass: "btn btn-social btn-gg" }, [
        _c("img", { attrs: { src: "/images/customer/icon-gg.png", alt: "" } }),
        _vm._v(" "),
        _c("span", [_vm._v("Đăng kí bằng tài khoản Google")])
      ])
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d&":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d& ***!
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
  return _c("main", { staticClass: "container" }, [
    _vm._m(0),
    _vm._v(" "),
    _c("section", { staticClass: "organization-information" }, [
      _c("form", { attrs: { action: "" } }, [
        _c("div", { staticClass: "form-title" }, [_vm._v("Thông tin tổ chức")]),
        _vm._v(" "),
        _c("div", { staticClass: "form-description" }, [
          _vm._v(
            "\n                Nhập tên cơ quan, tổ chức bạn quản lý, nhấn nút Tiếp theo để chuyển sang\n                phần chọn gói dịch vụ!\n            "
          )
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "form-group" }, [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.$v.form.name.$model,
                expression: "$v.form.name.$model"
              }
            ],
            staticClass: "form-control",
            attrs: { type: "text", placeholder: "Nhập tên cơ quan, tổ chức" },
            domProps: { value: _vm.$v.form.name.$model },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.$set(_vm.$v.form.name, "$model", $event.target.value)
              }
            }
          })
        ]),
        _vm._v(" "),
        _vm.$v.form.$dirty
          ? _c("div", { staticClass: "form-feedback" }, [
              !_vm.$v.form.name.required
                ? _c("span", [_vm._v("Bạn chưa nhập tên cơ quan tổ chức")])
                : _vm._e()
            ])
          : _vm._e(),
        _vm._v(" "),
        _c("div", { staticClass: "form-group" }, [
          _c("input", {
            directives: [
              {
                name: "model",
                rawName: "v-model",
                value: _vm.$v.form.code.$model,
                expression: "$v.form.code.$model"
              }
            ],
            staticClass: "form-control",
            attrs: {
              type: "text",
              placeholder: "Nhập mã công ty(mã bao gồm 3 kí tự)"
            },
            domProps: { value: _vm.$v.form.code.$model },
            on: {
              input: function($event) {
                if ($event.target.composing) {
                  return
                }
                _vm.$set(_vm.$v.form.code, "$model", $event.target.value)
              }
            }
          }),
          _vm._v(" "),
          _vm.$v.form.$dirty
            ? _c("div", { staticClass: "form-feedback" }, [
                !_vm.$v.form.code.required
                  ? _c("span", [_vm._v("Bạn chưa nhập mã cơ quan, tổ chức")])
                  : _vm._e(),
                _vm._v(" "),
                !_vm.$v.form.code.minLength
                  ? _c("span", [_vm._v("Mã công ty bao gồm 3 kí tự")])
                  : _vm._e(),
                _vm._v(" "),
                !_vm.$v.form.code.maxLength
                  ? _c("span", [_vm._v("Mã công ty bao gồm 3 kí tự")])
                  : _vm._e()
              ])
            : _vm._e()
        ]),
        _vm._v(" "),
        _c("div", { staticClass: "form-group" }, [
          _c(
            "button",
            {
              staticClass: "btn btn-action",
              on: {
                click: function($event) {
                  $event.preventDefault()
                  return _vm.submit($event)
                }
              }
            },
            [_vm._v("Tiếp theo")]
          )
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
    return _c("aside", [
      _c("img", {
        staticStyle: { width: "490px", "margin-top": "50px" },
        attrs: { src: "/images/customer/vector image 5.svg", alt: "" }
      })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c&":
/*!****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib??vue-loader-options!./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c& ***!
  \****************************************************************************************************************************************************************************************************************/
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
  return _c("main", { staticClass: "container" }, [
    _c("div", { staticClass: "service" }, [
      _vm._m(0),
      _vm._v(" "),
      _c("div", { staticClass: "service-name" }, [_vm._v("Personal")]),
      _vm._v(" "),
      _c("p", { staticClass: "service-description" }, [
        _vm._v(
          "\n            Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error\n            repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima\n        "
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "service-price" }, [_vm._v("$19.00")]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "btn btn-action",
          on: {
            click: function($event) {
              return _vm.setupOrganization(1)
            }
          }
        },
        [_vm._v("Choose plan")]
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "service" }, [
      _vm._m(1),
      _vm._v(" "),
      _c("div", { staticClass: "service-name" }, [_vm._v("Team")]),
      _vm._v(" "),
      _c("p", { staticClass: "service-description" }, [
        _vm._v(
          "\n            Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error\n            repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima\n        "
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "service-price" }, [_vm._v("$99.00")]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "btn btn-action",
          on: {
            click: function($event) {
              return _vm.setupOrganization(2)
            }
          }
        },
        [_vm._v("Choose plan")]
      )
    ]),
    _vm._v(" "),
    _c("div", { staticClass: "service" }, [
      _vm._m(2),
      _vm._v(" "),
      _c("div", { staticClass: "service-name" }, [_vm._v("Corporate")]),
      _vm._v(" "),
      _c("p", { staticClass: "service-description" }, [
        _vm._v(
          "\n            Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error\n            repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima\n        "
        )
      ]),
      _vm._v(" "),
      _c("div", { staticClass: "service-price" }, [_vm._v("$199.00")]),
      _vm._v(" "),
      _c(
        "button",
        {
          staticClass: "btn btn-action",
          on: {
            click: function($event) {
              return _vm.setupOrganization(3)
            }
          }
        },
        [_vm._v("Choose plan")]
      )
    ])
  ])
}
var staticRenderFns = [
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "service-img" }, [
      _c("img", {
        attrs: { src: "/images/customer/vector image 6.svg", alt: "" }
      })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "service-img" }, [
      _c("img", {
        attrs: { src: "/images/customer/vector image 7.svg", alt: "" }
      })
    ])
  },
  function() {
    var _vm = this
    var _h = _vm.$createElement
    var _c = _vm._self._c || _h
    return _c("div", { staticClass: "service-img" }, [
      _c("img", {
        attrs: { src: "/images/customer/vector image 8.svg", alt: "" }
      })
    ])
  }
]
render._withStripped = true



/***/ }),

/***/ "./resources/js/src/views/auth/EmailConfirmation.vue":
/*!***********************************************************!*\
  !*** ./resources/js/src/views/auth/EmailConfirmation.vue ***!
  \***********************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./EmailConfirmation.vue?vue&type=template&id=e7513898& */ "./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898&");
/* harmony import */ var _EmailConfirmation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./EmailConfirmation.vue?vue&type=script&lang=js& */ "./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _EmailConfirmation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__["render"],
  _EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/auth/EmailConfirmation.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js&":
/*!************************************************************************************!*\
  !*** ./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js& ***!
  \************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EmailConfirmation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./EmailConfirmation.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_EmailConfirmation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898&":
/*!******************************************************************************************!*\
  !*** ./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898& ***!
  \******************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./EmailConfirmation.vue?vue&type=template&id=e7513898& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/EmailConfirmation.vue?vue&type=template&id=e7513898&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_EmailConfirmation_vue_vue_type_template_id_e7513898___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/auth/Login.vue":
/*!***********************************************!*\
  !*** ./resources/js/src/views/auth/Login.vue ***!
  \***********************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Login.vue?vue&type=template&id=d9ffc4a8& */ "./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8&");
/* harmony import */ var _Login_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Login.vue?vue&type=script&lang=js& */ "./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Login_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/auth/Login.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js&":
/*!************************************************************************!*\
  !*** ./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js& ***!
  \************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Login_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Login.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Login.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Login_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8&":
/*!******************************************************************************!*\
  !*** ./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8& ***!
  \******************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Login.vue?vue&type=template&id=d9ffc4a8& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Login.vue?vue&type=template&id=d9ffc4a8&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Login_vue_vue_type_template_id_d9ffc4a8___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/auth/Register.vue":
/*!**************************************************!*\
  !*** ./resources/js/src/views/auth/Register.vue ***!
  \**************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Register.vue?vue&type=template&id=58ce0bb0& */ "./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0&");
/* harmony import */ var _Register_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Register.vue?vue&type=script&lang=js& */ "./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Register_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/auth/Register.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js&":
/*!***************************************************************************!*\
  !*** ./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js& ***!
  \***************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Register_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Register.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Register.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Register_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0&":
/*!*********************************************************************************!*\
  !*** ./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0& ***!
  \*********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Register.vue?vue&type=template&id=58ce0bb0& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/auth/Register.vue?vue&type=template&id=58ce0bb0&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Register_vue_vue_type_template_id_58ce0bb0___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/setup/OrganizationInformation.vue":
/*!******************************************************************!*\
  !*** ./resources/js/src/views/setup/OrganizationInformation.vue ***!
  \******************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./OrganizationInformation.vue?vue&type=template&id=4ee4de2d& */ "./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d&");
/* harmony import */ var _OrganizationInformation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./OrganizationInformation.vue?vue&type=script&lang=js& */ "./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _OrganizationInformation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__["render"],
  _OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/setup/OrganizationInformation.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js&":
/*!*******************************************************************************************!*\
  !*** ./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js& ***!
  \*******************************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_OrganizationInformation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./OrganizationInformation.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_OrganizationInformation_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d&":
/*!*************************************************************************************************!*\
  !*** ./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d& ***!
  \*************************************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./OrganizationInformation.vue?vue&type=template&id=4ee4de2d& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/OrganizationInformation.vue?vue&type=template&id=4ee4de2d&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_OrganizationInformation_vue_vue_type_template_id_4ee4de2d___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ }),

/***/ "./resources/js/src/views/setup/Services.vue":
/*!***************************************************!*\
  !*** ./resources/js/src/views/setup/Services.vue ***!
  \***************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Services.vue?vue&type=template&id=fb7ebd4c& */ "./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c&");
/* harmony import */ var _Services_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Services.vue?vue&type=script&lang=js& */ "./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport *//* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */

var component = Object(_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Services_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__["render"],
  _Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"],
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/src/views/setup/Services.vue"
/* harmony default export */ __webpack_exports__["default"] = (component.exports);

/***/ }),

/***/ "./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js&":
/*!****************************************************************************!*\
  !*** ./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js& ***!
  \****************************************************************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Services_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/babel-loader/lib??ref--4-0!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Services.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/Services.vue?vue&type=script&lang=js&");
/* empty/unused harmony star reexport */ /* harmony default export */ __webpack_exports__["default"] = (_node_modules_babel_loader_lib_index_js_ref_4_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Services_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c&":
/*!**********************************************************************************!*\
  !*** ./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c& ***!
  \**********************************************************************************/
/*! exports provided: render, staticRenderFns */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../../node_modules/vue-loader/lib??vue-loader-options!./Services.vue?vue&type=template&id=fb7ebd4c& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js?!./node_modules/vue-loader/lib/index.js?!./resources/js/src/views/setup/Services.vue?vue&type=template&id=fb7ebd4c&");
/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "render", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__["render"]; });

/* harmony reexport (safe) */ __webpack_require__.d(__webpack_exports__, "staticRenderFns", function() { return _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Services_vue_vue_type_template_id_fb7ebd4c___WEBPACK_IMPORTED_MODULE_0__["staticRenderFns"]; });



/***/ })

}]);