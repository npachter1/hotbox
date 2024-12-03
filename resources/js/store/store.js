import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

import createPersistedState from 'vuex-persistedstate';
import * as Cookies from 'js-cookie';
import _ from 'lodash';

import applicationModules from "./modules";
import router from '../routers';

const initState = () => {
	return {
		user: {},
		sections: [],
		settings:{},
		disp: {
			name: process.env.MIX_VUE_APP_NAME,
			section: 0,
			messageBag: [],
		}
	};
};


const store = new Vuex.Store({
	state: initState(),
	mutations: {
		resetAuthLocation (state,location) {
        	if(location.user) state.user = location.user;
        	if(location.sections) state.sections = location.sections;
        	if(location.settings) state.settings = location.settings;
        	if(location.messages) state.disp['messageBag'] = location.messages;		// app messages from backend
		},
		
		resetState(state){
			Object.assign(state, initState());									// merge to keep observers in place for reactivity
		},
		
		setSection(state,ind){
			state.disp['section'] = ind;
		},
		
		setLocation(state,id){
			state.disp['location'] = id;
		},
		
		addMessage(state,mes){
			if(typeof mes === 'object') state.disp['messageBag'].unshift(mes);
		},


	},
	actions: {
     	resetAuthLocation ({commit,state,dispatch},params){
     		if(params.reset!==false) commit('resetState');						// unless reset false param is sent, reset state before load to init factory
     		return axios.get('/api/v1/admin/auth/locations/load?scope='+params.scope).then(response =>  {
            	commit('resetAuthLocation',response.data);
            	commit('setLocation',state.user.location.id);					// set location
console.log('STORE: Auth Location store (re)Set');
            	if(params.route) dispatch('setLocationSection',params.route);
        	}).catch(error => {
				//console.log(error.response.data);
        	});
     	},

     	clearAuthLocation ({commit}){
     		commit('resetState');
console.log('STORE: Auth Location store cleared');
     	},

		clearSearch({commit, state}){
			
			Object.keys(applicationModules).forEach(module=>{
				if (typeof store._mutations[module + '/setSearch']==='object') //check if setSearch is available
					if (state[module].search && (state[module].search.gridPage || state[module].search.gridData || state[module].search.gridFilters)) //commit is pretty slow, so only do it if there's something to clear out
						commit(module+'/setSearch',{gridPage: null, gridData: null, gridFilters: null, gridColumns: null, gridSearch: null, options: { merge: true} });   // persist search setting
			});
			
			/*for (module in store._modules.root._children) { //don't really like going into _modules.root._children like this, but I know of no other way other than hard-coding the modules
				if (typeof store._mutations[module + '/setSearch']==='object') { //check if setSearch is available
					if (state[module].search && (state[module].search.gridPage || state[module].search.gridData || state[module].search.gridFilters)) { //commit is pretty slow, so only do it if there's something to clear out
						commit(module+'/setSearch',{gridPage: null, gridData: null, gridFilters: null, gridColumns: null, gridSearch: null, options: { merge: true} });   // persist search setting
					}
				}
			}*/
		},

     	setLocationSection({commit, state},route){
     		return state.sections.find((sec,ind) => {
        		if(ind==0) return false;										// section 0 is reserved for the app suite registry
        		else if(route.matched.some(m => m.name === sec.module || m.name === sec.module+'_index')){
        			commit('setSection',ind);									// set matched sections index as section
console.log('STORE: Auth Location Section set '+ind);
					this.dispatch(sec.module+'/setSchemas',_.uniq(sec.views.map(v=>{ return v.name; })).join(','));
					return true;
        		}else{
        			commit('setSection',0); // if section is unfound, it is not part of the loacations registered services (ie: grow/dispensary) - return to location settings page.
        			router.push('/admin/dashboard');
        		}
        	});
     	}
	},
	getters: {
		userCan: (state) => (permission) => {
			if(!state.user) return false;
			else if(!state.user.roles) return false;
			else if(!permission) return true;									// if no/blank permission was passed, assume we have permission
			else if(state.user.roles.indexOf('super-admin')!==-1) return true;
			else return (state.user.permissions.indexOf(permission || 'Reporting View')!==-1) ? true : false;
		},

		getAdminPin: (state) => {
			return process.env.MIX_VUE_APP_ADMINPIN;
		},
		
		isActivated: (state) => {
			if(state.user.location)
				return (state.user.location.status=='activated') ? true : false;
			return false;
		},
		
		isDemo: (state) => {
			if(state.user.location)
				return (state.user.location.is_demo) ? true : false;
			return false;
		},
		
		getAgent: (state) => {
			
			return (state.user.location || {}).regulatory_agent || 'misc';
		}
	},
	
	modules: applicationModules,
	
	plugins: [
		createPersistedState({ storage: window.localStorage,key:process.env.MIX_VUE_APP_NAME })
	]
});


export default store;