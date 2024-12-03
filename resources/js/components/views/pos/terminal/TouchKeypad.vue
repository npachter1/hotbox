<template>
  <div class="touch-keypad">
    <div v-if="showTextInput" class="d-flex flex-row mb-2">
      <div v-if="beforeTextInput"
           class="before-text-input">
        {{ beforeTextInput }}
      </div>
      <div v-if="beforeIconInput">
        <i class="before-text-input" :class="beforeIconInput"></i>
      </div>
      <div>
        <input type="text" autoFocus
               class="form-control form-control-my-value"
               :placeholder="textInputPlaceholder"
               v-model="myValue"
               ref="myInputRef_29174319"
               @keydown.enter.prevent="performAction()"
               @focus="$event.target.select()">
      </div>
      <div v-if="afterTextInput"
           class="after-text-input ml-2">
        {{ afterTextInput }}
      </div>
    </div>
    <div v-if="showHiddenInput" class="row">
      <div class="col-12 text-center">
        <button v-for="n in myValue.length"
                :key="n + 100"
                type="button"
                class="btn btn-default btn-pin-secret btn-on">
        </button>
        <button v-for="m in (maxLength - myValue.length)"
                :key="m"
                type="button"
                class="btn btn-default btn-pin-secret btn-off">
        </button>
      </div>
    </div>
    <table>
      <tr>
        <td>
          <button @click="inputCharacter(1)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            1
          </button>
        </td>
        <td>
          <button @click="inputCharacter(2)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            2
          </button>
        </td>
        <td>
          <button @click="inputCharacter(3)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            3
          </button>
        </td>
      </tr>
      <tr>
        <td>
          <button @click="inputCharacter(4)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            4
          </button>
        </td>
        <td>
          <button @click="inputCharacter(5)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            5
          </button>
        </td>
        <td>
          <button @click="inputCharacter(6)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            6
          </button>
        </td>
      </tr>
      <tr>
        <td>
          <button @click="inputCharacter(7)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            7
          </button>
        </td>
        <td>
          <button @click="inputCharacter(8)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            8
          </button>
        </td>
        <td>
          <button @click="inputCharacter(9)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            9
          </button>
        </td>
      </tr>
      <tr>
        <td v-if="maxNumberOfDecimalPlaces > 0">
          <button @click="inputCharacter('.')"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            .
          </button>
        </td>
        <td v-else></td>
        <td>
          <button @click="inputCharacter(0)"
                  type="button"
                  class="btn btn-simple btn-weigh-pad">
            0
          </button>
        </td>
        <td>
          <button @click="deleteCharacter()"
                  type="button" class="btn btn-simple btn-weigh-pad btn-weigh-pad-action">
            <i class="hotbox-icon hotbox-icon-delete-49"></i>
          </button>
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
  export default {
    props: {
      showTextInput: {
        type: Boolean,
        default: true
      },
      showHiddenInput: {
        type: Boolean,
        default: false
      },
      beforeTextInput: {
        type: String,
        default: null
      },
      beforeIconInput:{
        type: String,
        default:null
      },
      afterTextInput: {
        type: String,
        default: null
      },
      textInputPlaceholder: {
        type: String,
        default: '0.00'
      },
      maxNumberOfDecimalPlaces: {
        type: Number,
        default: 2
      },
      minNumberOfDisplayDecimalPlaces: {
        type: Number,
        default: 0
      },
      maxValue: {
        type: Number,
        default: 0
      },
      maxLength: {
        type: Number,
        default: 100
      },
      isString: {
        type: Boolean,
        default: false
      },
      seedValue:{
        type: [Number,String],
        default:0
      },
      resetFlag: {
        type: Boolean,
        default: false
      }
    },
    data() {
      return {
        myValue: '',
        isError: false,
        alertMessage: null,
        alertClass: null
      }
    },
    mounted() {
      this.myValue = this.textInputPlaceholder === '0.00' ? '' : Number(this.textInputPlaceholder);
      this.focusTextInput();
    },
    updated() {
      this.$nextTick(() => this.$refs.myInputRef_29174319.focus());
    },
    computed: {
      /*
      customers: function () {
        return this.$store.getters['customer/customers'];
      },
      customersPagination: function () {
        return this.$store.getters['customer/pagination']
      }
      */
    },
    watch: {
      myValue: function () {
        this.$emit('touchKeypadChangeValue', this.myValue);
      },
      resetFlag: function () {
        this.myValue = '';
        this.focusTextInput();
      }
    },
    methods: {
      inputCharacter: function (character) {
        if (!this.isString && (!this.myValue && !character)) {
          return;
        }
        if (this.maxLength > 0 && this.myValue.length === this.maxLength) {
          return;
        }
        var testNumberString = this.myValue + '';
        if (!isNaN(character)) {
          // Add a numeric digit to the string
          testNumberString = testNumberString + character;
        }
        else if (character === '.' && testNumberString.indexOf('.') === -1) {
          // Only add decimal point if doesn't already exist in string
          testNumberString = testNumberString + '.';
        }
        let decimalPlaces = this.countDecimals(testNumberString);
        if (decimalPlaces > this.maxNumberOfDecimalPlaces) {
          // trim the string to the proper number of decimal places
          testNumberString = testNumberString.substring(0, testNumberString.length - (decimalPlaces - this.maxNumberOfDecimalPlaces))
        }
        decimalPlaces = this.countDecimals(testNumberString);
        if (decimalPlaces > 0 && decimalPlaces < this.minNumberOfDisplayDecimalPlaces) {
          for (decimalPlaces; decimalPlaces < this.minNumberOfDisplayDecimalPlaces; decimalPlaces++) {
            testNumberString = testNumberString + '0';
          }
        }
        if (isNaN(testNumberString) && testNumberString !== '.') {
          console.log('inputCharacter yielding non-numeric string: ' + testNumberString);
          return;
        }
        if (this.maxValue && Number(testNumberString) > this.maxValue) {
          // Do not allow to go over maxValue; just retain old value
          return;
        }
        this.myValue = testNumberString;
        //this.$emit('touchKeypadChangeValue', this.myValue);
      },

      deleteCharacter: function () {
        if (this.myValue.length > 0) {
          let decimalPlaces = this.countDecimals(this.myValue);
          if (decimalPlaces > this.maxNumberOfDecimalPlaces) {
            this.myValue = this.myValue.substring(0, this.myValue.length - (1 + (decimalPlaces-this.maxNumberOfDecimalPlaces)));
          }
          else {
            this.myValue = this.myValue.substring(0, this.myValue.length - 1)
          }
        }
      },

      performAction: function () {
        this.$emit('touchKeypadAction', this.myValue);
      },

      focusTextInput: function () {
        if (this.showTextInput) {
          this.$nextTick(() => {
                requestAnimationFrame(() => {
                  this.$refs.myInputRef_29174319.focus();
                })
            });
        }
      },

      countDecimals: function (num) {
        if(isNaN(num) || num.indexOf('.') === -1 || Math.floor(num.valueOf()) === num.valueOf()) return 0;
        return num.toString().split(".")[1].length || 0;
      }

    }
  }
</script>
<style scoped>
  .touch-keypad {
    width: 204px;
  }

  .before-text-input {
    line-height: 44px;
    font-size: 24px;
  }

  .after-text-input {
    line-height: 44px;
    font-size: 24px;
  }

  .form-control-my-value {
    padding: 5px 10px;
    line-height: 34px;
    font-size: 20px;
    border-radius: 10px;
    margin: 0 5px;
  }

  .btn-weigh-pad {
    font-size: 24px;
    height: 60px;
    min-width: 60px;
    width: 60px;
    border-radius: 30px;
    padding: 0;
    margin: 4px;
  }

  .btn-weigh-pad-action {
    font-size: 30px;
    padding-top: 10px;
    padding-right: 2px;
  }

  .btn-pin-secret {
    font-size: 2px;
    height: 30px;
    min-width: 30px;
    width: 30px;
    border-radius: 15px;
    margin: 10px;
    padding: 0;
  }

  .btn-pin-secret.btn-on {
    background-color: rgba(100,100,100,0.8);
  }

  .btn-pin-secret.btn-off {
    background-color: rgba(100,100,100,0.2);
  }

</style>