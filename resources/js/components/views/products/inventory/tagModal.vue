<template>
    <div v-if="schema && location" class="col-12">

    <form class="modal-form" @submit.prevent="print">
        <fieldset>

            <div v-if="item" class="form-group row gutters mt-3 mb-2">
              <label class="col-md-2 col-form-label">Tag Preview</label>
              <div id="productTag" class="col-md-12" style="border: none; text-align: center;">
                  <span>
                    <barcode :value="item.item_barcode"  tag="svg" :options="{ height:40 }"></barcode><br>
                  </span>
                  <div id="productTagWithoutBarcode" class="col-md-12 product-text" >
                  <span v-if="item.product.inv_meta.vendor">
                    {{ item.product.inv_meta.vendor }}<br>
                  </span>
                  <span v-if="item.created_at">
                    {{ item.created_at | localDate('MM/DD/YYYY LTS')  }}, {{ (item.product.inv_meta.licensenum) ? item.product.inv_meta.licensenum : '' }}<br>
                  </span>
                  <span v-if="item.product">
                    <i>{{ item.product.name }}</i><br>
                  </span>
                  <span v-if="item.id">
                    Batch #: {{(item.item_batch) ? item.item_batch : item.id*30}}<br>
                  </span>
                  <span v-if="item.product.thc_percentage">
                      THC: {{Number(item.product.thc_percentage).toFixed(2)}}%
                  </span>
                  <span v-if="item.product.cbd_percentage">
                      CBD: {{Number(item.product.cbd_percentage).toFixed(2)}}%
                  </span>
                  <span v-if="item.product.terpene_percentage">
                      Terp: {{Number(item.product.terpene_percentage).toFixed(2)}}%
                  </span><br />
                  <!--<span v-if="item.weight_potency >= 1000">
                    THC: {{ Number((item.weight_potency/item.amount_unit)/1000).toFixed(2) + 'g' }}, CBD: 0mg<br>
                  </span>
                  <span v-else="">
                    THC: {{ Number(item.weight_potency/item.amount_unit).toFixed(0)  + 'mg'}}, CBD: 0mg<br>
                  </span>       -->
                  <span v-if="item.metrc_tag">
                    From Package: {{item.metrc_tag}}<br>
                  </span>
                  </div>
                </div>
            </div>

            <div class="form-group row gutters mb-3">
                <label class="col-md-2 col-form-label">Select Printer</label>
                <div class="col-md-7">

                <multiselect v-if="printerList"
                              v-model="printerName"
                              :placeholder="'Click to Select'"
                              :searchable="true"
                              :allow-empty="false"
                              label="name"
                              track-by="value"
                              selected-label="Selected"
                              deselect-label=""
                              select-label=""
                              :multiple="false"
                              v-validate=""
                              @select="onChangePrinter"
                              :options="printerList"
                              ref="printerSelect">
                            </multiselect>
                </div>
                <div class="float-right small">Is Default?<form-boolean :declared="isPrinterDefault" :hideLabel="true" @input="onChangeDefaultPrinter" class="ml-1 float-right" /></div>
            </div>

            <div v-if="printerName.name.indexOf('DYMO') === -1 && printerName.name.indexOf('Other') === -1" class="form-group row gutters mb-3">
                <label class="col-md-2 col-form-label">Label Width (inches)</label>
                <div class="col-md-2">
                  <input type="text" v-model="labelWidthInches" @input="onLabelChange" class="form-control">
                </div>
                <label class="col-md-2 col-form-label">Label Length (inches)</label>
                <div class="col-md-2">
                  <input type="text" v-model="labelLengthInches" @input="onLabelChange" class="form-control">
                </div>
                <button type="button" @click="setPrinterDefaults" v-if="printerName.name.indexOf('DYMO') === -1 && printerName.name.indexOf('Other') === -1 && showSetLabelDefault"
                      class="btn btn-success btn-round" style="margin-top: 0px;margin-bottom:24px;padding-top: 0px;padding-bottom:0px;">
                    <span class="btn-label"><i class="hotbox-icon hotbox-icon-settings"></i></span> Save Settings
                </button>
            </div>

            <div v-if="labelTypes" class="form-group row gutters mb-3">
                <label class="col-md-2 col-form-label">Select Label</label>
                <div class="col-md-7">
                            <multiselect v-if="labelTypes"
                              v-model="labelId"
                              :placeholder="'Click to Select'"
                              :searchable="true"
                              :allow-empty="true"
                              label="Id"
                              track-by="Id"
                              :multiple="false"
                              v-validate=""
                              @select="onLabelChange"
                              :options="labelTypes">
                              <template slot="singleLabel" slot-scope="{ option }">{{ option.Names['en'] }} (Width {{ option.UISize['inch']['@attributes'].Width }}" Length {{ option.UISize['inch']['@attributes'].Height }}")</template>
                              <template slot="option" slot-scope="props">
                                <div class="option__desc"><span class="option__title">{{ props.option.Names['en'] }} </span><span class="option__small">(Width {{ props.option.UISize['inch']['@attributes'].Width }}" Length {{ props.option.UISize['inch']['@attributes'].Height }}")</span></div>
                              </template>
                            </multiselect>
                </div>
                <button type="button" @click="setPrinterDefaults" v-if="printerName.name.indexOf('DYMO') !== -1 && showSetLabelDefault"
                      class="btn btn-success btn-round" style="margin-top:0px;margin-bottom: 0px;">
                    <span class="btn-label"><i class="hotbox-icon hotbox-icon-settings"></i></span> Save Settings
                </button>
            </div>

            <div class="form-group row gutters mb-4">
                <label class="col-md-2 col-form-label">Qty to Print</label>
                <div class="col-md-9">
                  <input type="number" v-model="numberToPrint" class="form-control">
                  <a @click="setQtyOnHand()" style="color:red;font-size:smaller;margin-left:5px;cursor:pointer">(Set to quantity on hand)</a>
                </div>
            </div>

            <div class="col-12 text-center mb-3">
                <button type="submit"
                    class="btn btn-primary">
                  <span class="btn-label"><i class="hotbox-icon hotbox-icon-square-download"></i></span> PRINT TAGS
                </button>
            </div>


        </fieldset>
    </form>

    </div>
    <div v-else>
        <loading :display="(schema && location) ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import Item from '../../../../models/Inventory';
    import { Printd } from 'printd';
    import { toPng, toJpeg, toBlob, toPixelData, toSvgDataURL } from 'html-to-image';
    import { isNullOrUndefined } from 'util';
    import _ from 'lodash';


    export default {

        props: {
            model: {
                type: String,
                default: 'Inventory'
            },
            module: {
                type: String,
                default: 'products',
            }
        },

        data() {
          return {
            id:null,
            item:null,
            location:{},
            isLoading: false,
            isDownloading: false,
            showSetLabelDefault: false,
            isPrinterDefault: false,
            numberToPrint: 1,
            labelImage: null,
            labelId: null,
            labelTypes: null,
            printerList:[{'name':'Other','value':'PDF'}],
            printerName: {'name':'Other','value':'PDF'},
            selected_device: null,
            devices:[],
            labelText:[],
            labelWidthInches: 2.25,
            labelLengthInches: 2,
            polling: null,
            dymoFrameworkLoaded:false,
            zebraFrameworkLoaded:false
          };
        },
        created() {
          this.$loadScript("/js/DYMO.Label.Framework.3.0.js").then(() => {
            this.dymoFrameworkLoaded = true;
          })
          .catch(() => {
            this.$announcer({status:500,data:{message:"Failed to load DYMO Label Framework"}});
          });
          this.$loadScript("/js/BrowserPrint-2.0.0.75.min.js").then(() => {
            this.zebraFrameworkLoaded = true;
          })
          .catch(() => {
            this.$announcer({status:500,data:{message:"Failed to load Zebra Framework"}});
          });

        },
        beforeDestroy () {
          clearInterval(this.polling)
        },
        async mounted() {
          await this.$store.dispatch(this.module+'/setSchemas',this.model.toLowerCase()); // this is a sub resource - it loads its own schema upon modal load
          this.isLoading = true;
          axios.get('/api/v1/admin/auth/location/' + this.location_id).then(response =>  {
            this.location = response.data;
            this.d = new Printd();
            this.zebraFrameworkInitShim();
            this.dymoFrameworkInitShim();
            this.isLoading = false;
          }).catch(error => {
            this.isLoading = false;
            this.$announcer({status:500,data:{message:error.message}});
          });
        },

    methods: {

      setId(itemId){
        this.id = itemId;

            if(this.id){
                Item.find(this.id).then(response => {
                    this.item = new Item(response).withDefaults(this.schema,false);
                    this.isLoading = false;
                    return true;
                }).catch(error => {
                    this.isLoading = false;
                    this.$announcer({status:400,data:{message:'We had a hiccup fetching the data - Please try again later.'}});
                    this.$emit('refresh',{},'tag');
                    return false;
                });
            }else{
                this.isLoading = false;
                this.$announcer({status:422,data:{message:'Whoops - couldnt find the associated record - Please try again later.'}});
                this.$emit('refresh',{},'tag');
                return false;
            }
      },

      print() {

        this.printTag();

      },

      printTag() {

        if(this.printerName.name.includes("DYMO"))
        {

          const productLabel = document.getElementById('productTag');
          const productLabelDymo = document.getElementById('productTagWithoutBarcode');
          let glob = this;
          toPng(productLabelDymo, { height: 180, width: 400 })
            .then(function (dataUrl) {
              var img = new Image();
              img.src = dataUrl;
              glob.labelImage = dataUrl.slice(22);
            })
            .then(function(){
              glob.writeToSelectedDYMOPrinter();
            })
            .catch(function (error) {
              glob.$announcer({status:500,data:{message:error}});
            });
        }

        if(this.printerName.name.indexOf('DYMO') === -1 && this.printerName.name.indexOf('Other') === -1)
        {

          const productLabelDymo = document.getElementById('productTagWithoutBarcode');

          this.labelText = [];

          productLabelDymo.childNodes.forEach(element => {
            if(!isNullOrUndefined(element.innerText))
            {
              this.labelText.push(element.innerText);
            }
          });

          this.writeToSelectedZPLPrinter();

        }

        if(this.printerName.name.includes("Other"))
        {
          // TODO: do page breaks after however many labels will fit on the page by adding pagebreak class to empty div..
          const printStyles = [
                `@media print {
                    .pagebreak {
                        clear: both;
                        page-break-after: always;
                    }
                }`
              ]

          const productLabel = document.getElementById('productTag');
          const productLabels = document.createElement('div');

          // repeat :: forall a. Int -> (a -> a) -> a -> a
          const repeat = n => f => x => {
            if (n > 0)
              return repeat (n - 1) (f) (f (x))
            else
              return x
          }

          // times :: Int -> (Int -> Int) -> Int
          const times = n=> f=>
            repeat (n) (i => (f(i), i + 1)) (0)

          // usage :: this will build up the new div with n copies of the label
          times (this.numberToPrint) (i => productLabels.appendChild(productLabel.cloneNode(true)))

          this.d.print(productLabels,printStyles)
        }
      },
      setQtyOnHand(){
        this.numberToPrint = Number(this.item.quantity_on_hand).toFixed(0);
      },
      onChangeDefaultPrinter: function($event) {
        if(this.isPrinterDefault != $event)
        {
          this.isPrinterDefault = $event;
          this.showSetLabelDefault = true;
        }
        else
        {
          this.showSetLabelDefault = false;
        }
      },
      onChangePrinter: function($event) {
        this.labelTypes = null;
        this.isPrinterDefault = false;

        this.printerName = $event;
        console.log($event);
         if(this.printerName.name.includes("DYMO"))
         {
          this.isLoading = true;
          axios.get('/api/v1/admin/dispensary/inventories/label/schema').then(response =>  {
            this.labelTypes = response.data.schema;
            this.isLoading = false;
            this.$nextTick(() => {
              for (var i = 0; i < this.location.settings.label_printers.length; i++) {
                var device = this.location.settings.label_printers[i];
                if (this.printerName.name.includes(device.type) && device.route === this.$route.path) {
                  const defaultLabel = this.labelTypes.find( ({ Id }) => Id === device.labelId );
                  this.labelId = defaultLabel;
                  this.isPrinterDefault = device.isDefault ? device.isDefault : false;
                  this.showSetLabelDefault = false;
                }
              }
            });

          }).catch(error => {
            this.isLoading = false;
            this.$announcer({status:500,data:{message:error.message}});
          });
        }

        else if(!this.printerName.name.includes("Other"))
        {
              for (var i = 0; i < this.location.settings.label_printers.length; i++) {
                var device = this.location.settings.label_printers[i];
                if (this.printerName.name.includes(device.type) && device.route === this.$route.path) {
                  this.labelWidthInches = device.labelWidthInches;
                  this.labelLengthInches = device.labelLengthInches;
                  this.isPrinterDefault = device.isDefault ? device.isDefault : false;
                  this.showSetLabelDefault = false;
                }
              }

        }

      },
      onLabelChange: function()
      {
        this.showSetLabelDefault = true;
      },
      setPrinterDefaults(){

              let printerDefault = {};

              if(this.printerName.name.includes("DYMO")){
              printerDefault = {label_printer:{
                            type: "DYMO",
                            route: this.$route.path,
                            isDefault: this.isPrinterDefault,
                            labelId: this.labelId['Id'],
                            DefaultOrientation: this.labelId['DefaultOrientation'],
                            UISize: this.labelId['UISize']
                            }};
              }
              else if(!this.printerName.name.includes("Other")){
              printerDefault = {label_printer:{
                            type: this.printerName.value,
                            route: this.$route.path,
                            isDefault: this.isPrinterDefault,
                            labelWidthInches: Number(this.labelWidthInches),
                            labelLengthInches: Number(this.labelLengthInches)
                            }};
              }
              const data = printerDefault;

              this.isLoading = true;
              axios.post('/api/v1/admin/auth/location/printers/' + this.location_id,data).then(response =>  {
                this.isLoading = false;
                this.showSetLabelDefault = false;
              }).catch(error => {
                this.isLoading = false;
                this.$announcer({status:500,data:{message:error.message}});
              });

      },
      dymoFrameworkInitShim() {
        dymo.label.framework.trace = 1; //true
        dymo.label.framework.init(this.startupDYMOCode);
      },
      mergeById(arr, obj, idProp) {
        var index = _.findIndex(arr, function (elem) {
            // double check, since undefined === undefined
            return typeof elem[idProp] !== "undefined" && elem[idProp] === obj[idProp];
        });

        if (index > -1) {
            arr[index] = obj;
        } else {
            arr.push(obj);
        }

        return arr;
      },
      startupDYMOCode() {
        let glob = this;
        var printers = dymo.label.framework.getPrinters();
        if (printers.length == 0){
          //this.$announcer({data:{message:"No DYMO printers are installed."}});
          return false;
        }
        dymo.label.framework
          .getPrintersAsync()
          .then(function(printers) {
            // Successful result, printers variable has a list of all supported by the DYMO Label Framework
            for (var i = 0; i < printers.length; i++) {
                let printer = printers[i];
                if (printer.printerType == "LabelWriterPrinter") {
                    let printerName = printer.name;
                    let isDisabled = false;
                    clearInterval(glob.polling);

                    if(!printer.isConnected)
                    {
                      printerName += ' (Offline)';
                      isDisabled = true;
                      glob.pollDYMO();
                    }

                    let dymoPrinter = {'name':printerName,'value':printer.name,$isDisabled:isDisabled};

                    glob.printerList = glob.mergeById(glob.printerList, dymoPrinter,'value').filter(function (item) {
                        return item;
                    });

                    for (var i = 0; i < glob.location.settings.label_printers.length; i++) {
                      var device = glob.location.settings.label_printers[i];
                      if(device.isDefault && device.type.includes("DYMO") && device.route === glob.$route.path)
                      {
                        if(!isDisabled)
                        {
                          glob.printerName = dymoPrinter;
                          glob.isPrinterDefault = true;
                          glob.showSetLabelDefault = false;
                          glob.$refs.printerSelect.$emit("select", dymoPrinter);
                        }
                      }
                    }
                }
            }
          })
          .thenCatch(function(error) {
            glob.$announcer({status:500,data:{message:error.message}});
          });

      },
      writeToSelectedDYMOPrinter(){

          var labelXml = '';
          const data = {
                        type: "DYMO",
                        labelId: this.labelId['Id'],
                        DefaultOrientation: this.labelId['DefaultOrientation'],
                        UISize: this.labelId['UISize']
                        };

              this.isLoading = true;
              axios.post('/api/v1/admin/dispensary/inventories/label/render',data).then(response =>  {

                labelXml = window.atob(response.data);

                  var label = dymo.label.framework.openLabelXml(labelXml);
                  label.setObjectText("BARCODE", this.item.item_barcode);
                  label.setObjectText("GRAPHIC", this.labelImage);
                  try
                  {

                    let params = {copies:this.numberToPrint};
                    let paramString = dymo.label.framework.createLabelWriterPrintParamsXml(params)

                    label.print(this.printerName.name, paramString); // This is the NAME of the printer
                  }
                  catch (e) {
                    this.$announcer({status:500,data:{message:e.message}});
                  }

                this.isLoading = false;
              }).catch(error => {
                this.isLoading = false;
                this.$announcer({status:500,data:{message:error.message}});
              });

      },
      zebraFrameworkInitShim() {
        //Get the default device from the application as a first step. Discovery takes longer to complete.
        let glob = this;
        BrowserPrint.getDefaultDevice(
          "printer",
          function(device) {
            //Add device to list of devices and add to the dropdown
            glob.selected_device = device;
            glob.devices.push(device);
            //Let's get the model of the printer to add to the dropdown
            glob.selected_device.sendThenRead(
            "~HI",
            function(text) {

              glob.printerList.push({'name':"Zebra" + text.substr(0,14), 'value':device.uid});

              for (var i = 0; i < glob.location.settings.label_printers.length; i++) {
                var zplDevice = glob.location.settings.label_printers[i];
                if(zplDevice.isDefault && !zplDevice.type.includes("DYMO") && !zplDevice.type.includes("PDF") && zplDevice.route === glob.$route.path)
                {
                  glob.printerName = {'name':"Zebra" + text.substr(0,14), 'value':device.uid};
                  glob.isPrinterDefault = true;
                  glob.showSetLabelDefault = false;
                }
              }
            });
            //Discover any other devices available to the application
            BrowserPrint.getLocalDevices(
              function(device_list) {
                for (var i = 0; i < device_list.length; i++) {
                  var device = device_list[i];
                  if (!glob.selected_device || device.uid != glob.selected_device.uid) {
                    glob.devices.push(device);
                    glob.printerList.push({'name':"Zebra" + text.substr(0,14), 'value':device.uid});
                    for (var i = 0; i < glob.location.settings.label_printers.length; i++) {
                      var zplDevice = glob.location.settings.label_printers[i];
                      if(zplDevice.isDefault && !zplDevice.type.includes("DYMO") && !zplDevice.type.includes("PDF") && zplDevice.route === glob.$route.path)
                      {
                        glob.printerName = {'name':"Zebra" + text.substr(0,14), 'value':device.uid};
                        glob.isPrinterDefault = true;
                        glob.showSetLabelDefault = false;
                      }
                    }
                  }
                }

                // glob.$announcer({status:200,data:{message:"Found " + device_list.length + " Zebra printers."}});
              },
              function(e) {
                glob.$announcer({status:500,data:{message:e.message}});
              },
              "printer"
            );
          },
          function(error) {
            this.$announcer({status:500,data:{message:error}});
          }
        );
      },
      writeToSelectedZPLPrinter() {

        var labelData = '';
        const data = {
                        type: "Zebra",
                        labelWidthInches: this.labelWidthInches,
                        labelLengthInches: this.labelLengthInches,
                        barcode: this.item.item_barcode,
                        labelText: this.labelText,
                        copies: this.numberToPrint
                        };

              this.isLoading = true;
              axios.post('/api/v1/admin/dispensary/inventories/label/render',data).then(response =>  {

                labelData = response.data;

                this.selected_device.send(labelData, undefined, this.hotboxErrorAlert);

                this.isLoading = false;
              }).catch(error => {
                this.isLoading = false;
                this.$announcer({status:500,data:{message:error.message}});
              });

      },
      pollDYMO () {
        this.polling = setInterval(() => {
          this.startupDYMOCode();
        }, 35000)
      }

    },
    computed: {
        location_id(){
                return ((this.$store.state.user || {}).location || {}).id || 'none';
            },
        schema() {
            return this.$store.state[this.module][this.model.toLowerCase()+'Schema'];
        }
    }
  };
</script>

<style>
.product-text {
  font-family: Arial;
  line-height: 1.2;
}

</style>
