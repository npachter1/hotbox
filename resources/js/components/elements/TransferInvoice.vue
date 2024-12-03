<template>
    <div v-if="location" class="col-12">

    <form class="modal-form" @submit.prevent="print()">
        <fieldset>

            <div v-if="item" class="form-group row gutters mt-3 mb-2">
              	<div id="page-wrap">
                    <div id="header">INVOICE</div>
                    
                    <div id="identity">
                        <div>Payable To</div>
                        <div id="address" v-if="location">
                            <span>{{location.name}}</span>
                            <span v-if="location.address.address1">{{location.address.address1}}</span>
                            <span v-if="location.address.address2">{{location.address.address2}}</span>
                            <span>{{location.address.city}}, {{location.address.region}} {{location.address.zip}}</span>
                            <span v-if="location.address.phone">{{location.address.phone}}</span>
                            <span v-if="location.licensenum">License: {{location.licensenum}}</span>
                        </div>

                        <!-- <div id="logo">
                        <img id="image" src="/images/logo.png" alt="logo" />
                        </div> -->
                    
                    </div>
                    
                    <div style="clear:both"></div>
                    
                    <div id="customer" v-if="item.receiver">
                        <div>Order From</div>
                        <div id="customer-title">
                            <span>{{item.receiver.name}}</span>
                            <span v-if="item.receiver.address1">{{item.receiver.address1}}</span>
                            <span v-if="item.receiver.address2">{{item.receiveraddress2}}</span>
                            <span>{{item.receiver.city}}, {{item.receiver.region}} {{item.receiver.zip}}</span>
                            <span v-if="item.receiver.phone">{{item.receiver.phone}}</span>
                        </div>

                        <table id="meta">
                            <tr>
                                <td class="meta-head">Order #</td>
                                <td><div>{{item.manifest_number}}</div></td>
                            </tr>
                            <tr>

                                <td class="meta-head">Date</td>
                                <td><div id="date">{{item.created_at | formattedLocalDate}}</div></td>
                            </tr>
                            <tr>
                                <td class="meta-head">Amount Due</td>
                                <td><div class="due">${{item.transfersale_total | dollar}}</div></td>
                            </tr>

                        </table>
                    
                    </div>
                    
                    <table id="items">
                    
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Unit Cost</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                        
                        <tr class="item-row" v-for="pkg in item.packages">
                            <td class="item-name"><div>{{pkg.label}}</div></td>
                            <td class="descriptor"><div>{{pkg.item.name}}</div><div>{{pkg.item.category_type | ucwords}}</div></td>
                            <td class="cost"><div>{{(pkg.received_price/pkg.quantity) | dollar(4)}}</div></td>
                            <td class="qty"><div>{{pkg.quantity}} ({{pkg.unit_of_measure}})</div></td>
                            <td class="price"><span>${{pkg.received_price | dollar}}</span></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2" class="blank"> </td>
                            <td colspan="2" class="total-line">Subtotal</td>
                            <td class="total-value"><div id="subtotal">${{(item.transfersale_total - item.transfersale_fee) | dollar}}</div></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="blank"> </td>
                            <td colspan="2" class="total-line">Service Fee</td>
                            <td class="total-value"><div id="subtotal">${{item.transfersale_fee | dollar}}</div></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="blank"> </td>
                            <td colspan="2" class="total-line">Total</td>
                            <td class="total-value"><div id="total">${{item.transfersale_total | dollar}}</div></td>
                        </tr>

                        <tr>
                            <td colspan="2" class="blank"> </td>
                            <td colspan="2" class="total-line">Amount Paid</td>
                            <td class="total-value"><div id="paid">${{item.status !== 'confirmed' ? '0.00' : item.transfersale_total|dollar}}</div></td>
                        </tr>
                        
                        <tr>
                            <td colspan="2" class="blank"> </td>
                            <td colspan="2" class="total-line balance">Balance Due</td>
                            <td class="total-value balance"><div class="due">${{item.status === 'confirmed' ? '0.00' : item.transfersale_total|dollar}}</div></td>
                        </tr>
                    
                    </table>
                    
                    <div id="terms">
                    <h5>Terms</h5>
                    <div>NET 30 Days. Finance Charge of 1.5% will be made on unpaid balances after 30 days.</div>
                    </div>
                
                </div>
            </div>

            <div v-if="item" class="col-12 text-center mb-3">
                <button type="submit"
                   class="btn btn-primary">
                  <span class="btn-label"><spinner :isProcessing="isDownloading" :isFullScreen="false" :isLine="true" :spinnerWidth="25" class="float-left" /><i class="hotbox-icon hotbox-icon-download-file"></i></span> {{ !isDownloading ? 'Download' : 'Downloading' }}</button>
            </div>

  
        </fieldset>
    </form>
    
    </div>
    <div v-else>
        <loading :display="location ? false : true" type="loadModal" />
    </div>
</template>

<script>

    import { Printd } from 'printd';
    import { toPng, toJpeg, toBlob, toPixelData, toSvgDataURL } from 'html-to-image';
    import { isNullOrUndefined } from 'util';

    
    export default {
        name: "TransferInvoice",
        props: {
            item: {
                type: Object,
                default: {}
            }
        },

        data() {
          return {
            location_id: null,
            location: null,
            isLoading: false,
            numberToPrint: 1,
            isDownloading: false
          };
        },
        async mounted() {
          this.isLoading = true;
          this.location_id = this.$store.state.user.location.id;
          axios.get('/api/v1/admin/auth/location/' + this.location_id).then(response =>  {
            this.location = response.data;
            this.d = new Printd();
            this.isLoading = false;
          }).catch(error => {
            this.isLoading = false;
            this.$announcer({status:500,data:{message:error.message}});
          });
        },

    methods: {

      print() {
          this.$emit('downloadingInvoice',true);
          // TODO: do page breaks after however many invoice lines will fit on the page by adding pagebreak class to empty div..
          const printStyles = [
                `@media print {
                    .pagebreak {
                        clear: both;
                        page-break-after: always;
                    }
                }
                #page-wrap { width: 800px; margin: 0 auto; }

                table { border-collapse: collapse; }
                table td, table th { border: 1px solid black; padding: 5px; }

                #header { height: 30px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }

                #address { width: 250px; height: 150px; float: left; font-weight: bold;}
                #address span { display:block; }
                #customer { overflow: hidden; }

                #logo { text-align: right; float: right; position: relative; margin-top: 25px; margin-bottom: 25px; border: 1px solid #fff; width: 50px;}
                #image {max-width: 100%; max-height: 100%; display: block;}

                #customer-title { font-weight: bold; float: left; }
                #customer-title span { display:block; }

                #meta { margin-top: 1px; width: 300px; float: right; }
                #meta td { text-align: right;  }
                #meta td.meta-head { text-align: left; background: #eee; }
                #meta td div { width: 100%; height: 20px; text-align: right; }

                #items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
                #items th { background: #eee; }
                #items tr.item-row td { border: 0; vertical-align: top; }
                #items td.descriptor { width: 250px; }
                #items td.cost { text-align: right; }
                #items td.qty { text-align: right; }
                #items td.price { text-align: right; }
                #items td.item-name { width: 225px; }
                #items td.descriptor div, #items td.item-name div { width: 100%; }
                #items td.total-line { border-right: 0; text-align: right; }
                #items td.total-value { border-left: 0; padding: 10px; text-align: right;}
                /* #items td.total-value div { height: 20px; background: none; text-align: right;} */
                #items td.balance { background: #eee; text-align: right;}
                #items td.blank { border: 0; }

                #terms { text-align: center; margin: 20px 0 0 0; }
                #terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
                #terms div { width: 100%; text-align: center;}`
              ]

          const invoiceArea = document.getElementById('page-wrap').outerHTML.toString();

          var test = '<style>' + printStyles[0] + '</style>' + invoiceArea;

          var toprint = {'data':test};
          this.isDownloading = true;
          axios({  url: '/api/v1/admin/grow/transfers/'+this.item.id+'/invoice/export',
                   method: 'POST',
                   responseType: 'blob',
                   data: toprint
                }).then(response =>  {
            this.isDownloading = false;
            this.$emit('downloadingInvoice',false);
            this.downloadFile(response);
          
          }).catch(error => {
            this.isDownloading = false;
            this.$emit('downloadingInvoice',false);
            this.$announcer({status:500,data:{message:error.message}});
          });
      },
  }
}
</script>

<style scoped>

#page-wrap { width: 800px; margin: 0 auto; }

table { border-collapse: collapse; }
table td, table th { border: 1px solid black; padding: 5px; }

#header { height: 30px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }

#address { width: 250px; height: 150px; float: left; font-weight: bold;}
#address span { display:block; }
#customer { overflow: hidden; }

#logo { text-align: right; float: right; position: relative; margin-top: 25px; margin-bottom: 25px; border: 1px solid #fff; width: 50px;}
#image {max-width: 100%; max-height: 100%; display: block;}

#customer-title { font-weight: bold; float: left; }
#customer-title span { display:block; }

#meta { margin-top: 1px; width: 300px; float: right; }
#meta td { text-align: right;  }
#meta td.meta-head { text-align: left; background: #eee; }
#meta td div { width: 100%; height: 20px; text-align: right; }

#items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
#items th { background: #eee; }
#items tr.item-row td { border: 0; vertical-align: top; }
#items td.descriptor { width: 250px; }
#items td.cost { text-align: right; }
#items td.qty { text-align: right; }
#items td.price { text-align: right; }
#items td.item-name { width: 225px; }
#items td.descriptor div, #items td.item-name div { width: 100%; }
#items td.total-line { border-right: 0; text-align: right; }
#items td.total-value { border-left: 0; padding: 10px; text-align: right;}
#items td.balance { background: #eee; text-align: right;}
#items td.blank { border: 0; }

#terms { text-align: center; margin: 20px 0 0 0; }
#terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
#terms div { width: 100%; text-align: center;}

</style>