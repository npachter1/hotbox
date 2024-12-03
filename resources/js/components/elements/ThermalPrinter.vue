<template>
  <div class="thermal">
    <div class="row" v-if="!connected">
      <img style="width:200px" src='/images/printer.png'>
    </div>
    <div class="row">
    <button
      @click="connect"
      class="btn btn-primary btn-round"
      style="width:200px"
      v-bind:class="connected ? 'btn-danger' : 'btn-success'"
    >{{!connected ? 'CONNECT' : 'DISCONNECT' }}</button>
    </div>
  </div>
</template>

<script>
import printerImage from '../../../images/printer.png';

export default {
  name: "thermal",
  mounted() {
      this.device = this.$store.state['pos']['receiptPrinter'];
  },
  computed: {
    connected: function() {
      return !!this.device;
    }
  },
  data: function() {
    return {
      device: null,
      ENDPOINT: 2
    };
  },
  methods: {
    connect: async function() {
      if (this.device === null) {
         // get all connected usb thermal printing devices filtered by hex value for Citizen Printers and Epson POS printers
                          //         { vendorId: 0x1D90 },
                          // { vendorId: 0x922 }
        const filter = [
                          { vendorId: 0x1D90 },
                          { vendorId: 0x4b8 }
                        ];
        const rawdevice = await navigator.usb.requestDevice({ filters: filter });
        // do the setup procedure on the connected device
        return this.setup(rawdevice);
      }
      else
      {
        return this.closeDevice();
      }
    },
    print: async function(filecontent) {
      if(this.device === null)
      {
        return null;
      }
      // send the bytes to the printer
      await this.device.transferOut(this.ENDPOINT, filecontent);
    },
    setup: async function(rawdevice) {
      // open the device (initiate communication)
      await rawdevice.open();
      // select the devices configuration descriptor
      await rawdevice.selectConfiguration(1);
      // claim the device interfaces
      rawdevice = await this.claimInterface(rawdevice);
      this.device = rawdevice;
      this.$store.commit('pos/setReceiptPrinter',rawdevice); 
      this.$emit('printerSetupAction',this.device);
      return rawdevice;
    },
    // walk over all interfaces of the device
    // check if they're claimed (and do claim them, if they're not yet)
    claimInterface: async function(d) {
      for (const config of d.configurations) {
        for (const iface of config.interfaces) {
          if (!iface.claimed) {
            await d.claimInterface(iface.interfaceNumber);
            return d;
          }
        }
      }
      return d;
    },
    // walk over all interfaces of the device
    // check if they're claimed (and release the claim, if they're not yet)
    releaseInterface: async function(d) {
      for (const config of d.configurations) {
        for (const iface of config.interfaces) {
          if (iface.claimed) {
            await d.releaseInterface(iface.interfaceNumber);
            return d;
          }
        }
      }
      return d;
    },
    closeDevice: async function()
    {
      await this.device.selectConfiguration(1);
      await this.releaseInterface(this.device);
      await this.device.close();
      this.device = null;
      this.$store.commit('pos/setReceiptPrinter',null); 
      this.$emit('printerSetupAction',this.device);
    },
    closePrinterSetup: async function() {
      this.$emit('printerSetupAction',this.device);
    }
  }
};
</script>
<style>
  .thermal .row {
    display: block;
  }
</style>