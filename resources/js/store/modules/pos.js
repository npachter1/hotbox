import axios from "axios";

const state = {
  search: null,
  focus:null,
  drawer: null,
  saleSchema: null,
  drawerSchema: null,
  customerOrder: null,
  receiptPrinter: null
};

const getters = {
  drawer(state) {
    return state.drawer;
  },
  receiptPrinter(state) {
    return state.receiptPrinter;
  },
  isOpen(state) {
    return (state.drawer !== null);
  }
};

const mutations = {
  setView(state, payload) {
    state.view = payload;
  },

  setSearch(state, payload) {
    if (payload.options && payload.options.merge) {
      state.search = {...state.search, ...payload} //sending partial update, just merge into existing object
    } else {
      state.search = payload;
    }
    if ((state.search||{}).options) delete state.search.options; //don't save options that may have been passed in.
  },

  setFocus(state, payload) {
    state.focus = payload;
  },
  
  setSchema(state,payload){
    state[payload.key] = Object.assign({},payload.data);                        // merge schema with state to keep observers in place for reactivity
  },
  
  setDrawer(state, payload) {
    state.drawer = payload;
  },

  setCustomerOrder(state, payload) {
    state.customerOrder = payload;
  },
  
  setReceiptPrinter(state, payload) {
    state.receiptPrinter = payload;
  },
};

const actions = {
  
  open (context,
        {
          bill_1, bill_5, bill_10, bill_20, bill_50, bill_100,
          coin_1, coin_5, coin_10, coin_25, coin_50,
          extra
        }
  ) {
    return new Promise((resolve, reject) => {
      const form = {
        bill_1: bill_1, bill_5: bill_5, bill_10: bill_10,
        bill_20: bill_20, bill_50: bill_50, bill_100: bill_100,
        coin_1: coin_1, coin_5: coin_5, coin_10: coin_10,
        coin_25: coin_25, coin_50: coin_50,
        extra: extra,
      };

      axios.post('/api/v1/admin/dispensary/drawers', form)
        .then((response) => {
          if (response.data.error) reject(response.data.error);
          else context.commit('setDrawer', response.data);
          resolve();
        })
        .catch(response => {
          if (response.response && response.response.status)
            reject(response.response.message);
          reject('Unknown Error');
        });
    });
  },

  close (context,
        {
          bill_1, bill_5, bill_10, bill_20, bill_50, bill_100,
          coin_1, coin_5, coin_10, coin_25, coin_50,
          extra
        }
  ) {
    return new Promise((resolve, reject) => {

      const drawer = context.getters['drawer'];

      const form = {
        bill_1: bill_1, bill_5: bill_5, bill_10: bill_10,
        bill_20: bill_20, bill_50: bill_50, bill_100: bill_100,
        coin_1: coin_1, coin_5: coin_5, coin_10: coin_10,
        coin_25: coin_25, coin_50: coin_50,
        extra: extra,
      };

      axios.post('/api/v1/admin/dispensary/drawers/' + drawer.id + '/close', form)
        .then((response) => {
          if (response.data.error) reject(response.response.error);
          else context.commit('setDrawer', null);
          resolve();
        })
        .catch(response => {
          if (response.response && response.response.status) reject(response.response.message);
          reject('Unknown Error');
        })
    });
  },

  payin (context,
         {
           bill_1, bill_5, bill_10, bill_20, bill_50, bill_100,
           coin_1, coin_5, coin_10, coin_25, coin_50,
           extra
         }
  ) {
    return new Promise((resolve, reject) => {

      const drawer = context.getters['drawer'];

      const form = {
        bill_1: bill_1, bill_5: bill_5, bill_10: bill_10,
        bill_20: bill_20, bill_50: bill_50, bill_100: bill_100,
        coin_1: coin_1, coin_5: coin_5, coin_10: coin_10,
        coin_25: coin_25, coin_50: coin_50,
        extra: extra,
      };

      axios.post('/api/v1/admin/dispensary/drawers/' + drawer.id + '/payin', form)
        .then((response) => {
          if(response.data.error) reject(response.data.error);
          else context.commit('setDrawer', response.data);
          resolve();
        })
        .catch(response => {
          if (response.response && response.response.status) reject(response.response.message);
          reject('Unknown Error');
        });
    });
  },

  payout (context,
       {
         bill_1, bill_5, bill_10, bill_20, bill_50, bill_100,
         coin_1, coin_5, coin_10, coin_25, coin_50,
         extra
       }
  ) {
    return new Promise((resolve, reject) => {
      context.commit('isProcessing');

      const drawer = context.getters['drawer'];

      const form = {
        bill_1: bill_1, bill_5: bill_5, bill_10: bill_10,
        bill_20: bill_20, bill_50: bill_50, bill_100: bill_100,
        coin_1: coin_1, coin_5: coin_5, coin_10: coin_10,
        coin_25: coin_25, coin_50: coin_50,
        extra: extra,
      };

      axios.post('/api/v1/admin/dispensary/drawers/' + drawer.id + '/payout', form)
        .then((response) => {
          if (response.data.error) reject(response.data.error);
          else { context.commit('setDrawer', response.data); }
          resolve();
        })
        .catch(response => {
          if (response.response && response.response.status) reject(response.data.error);
          reject('Unknown Error');
        });
    });
  },

  getCurrentDrawer(context){
    return axios.get('/api/v1/admin/dispensary/drawers/getOpenDrawer')
        .then((response) => {
          context.commit('setDrawer', response.data);
        }).catch(error => {
          //
          context.commit('setDrawer',null);
        });
  }, 

  setSchemas({commit,dispatch},list){
    return axios.get('/api/v1/admin/schemas/dispensary/'+list).then(response =>  {
      if(response.data)
        Object.keys(response.data).forEach((k)=>{
          commit('setSchema',{data:response.data[k],key:k});
        });
    }).catch(error => {
      //
    });
  }

};

export default {
  state,
  mutations,
  actions,
  getters,
  namespaced: true
};