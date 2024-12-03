import axios from "axios";

const state = {
  search: null,
  focus: null,
  roomSchema: null,
  itemSchema: null,
  strainSchema: null
};

const getters = {

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
  }
  
};

const actions = {
  setSchemas({commit,dispatch},list){
    return axios.get('/api/v1/admin/schemas/grow/'+list).then(response =>  {
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