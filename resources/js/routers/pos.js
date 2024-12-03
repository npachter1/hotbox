import store from '../store/store.js';

/* Pos / Sale Vue Views */
const posView = {
                path: 'admin/pos',
                //redirect: {name: 'terminal-queue'},
                component: () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/index'),
                meta: { },
                name: 'pos',                                                    // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    await store.dispatch('pos/getCurrentDrawer');               // load current users drawer before entering this section
                    if(to.query.search) store.commit('pos/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/pos',
                        component:  () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/terminal/queue'),
                        name: 'pos_index'
                    },
                    {
                        path: '/admin/pos/terminal',
                        //redirect: {name: 'terminal-queue'},
                        component:  () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/terminal/queue'),
                        props: true,
                        name: 'terminal',
                        beforeEnter: async(to, from, next) => {
                            const cid = store.state['pos']['customerOrder'];
                            if (cid) {
                                axios.get('/api/v1/admin/dispensary/customersqueue/service/'+cid).then(response =>{
                                    next({name:'terminal-order',params:{customer_id:cid}}); // go to order form with customer_id
                                }).catch(error => {
                                    next();
                                });
                            } else {
                                next();
                            }
                        }
                    },
                    {
                      path: '/admin/pos/terminal/queue',
                      name: 'terminal-queue',
                      component: () => import( /* webpackChunkName: "js/view-pos" */ '../components/views/pos/terminal/queue'),
                      props: true
                    },
                    {
                      path: '/admin/pos/terminal/order',
                      name: 'terminal-order',
                      component: () => import( /* webpackChunkName: "js/view-pos" */ '../components/views/pos/terminal/order'),
                      props: true
                    },
                    {
                      path: '/admin/pos/terminal/drawer',
                      name: 'terminal-drawer',
                      component: () => import( /* webpackChunkName: "js/view-pos" */ '../components/views/pos/terminal/drawer'),
                      props: true
                    },
                    {
                        path: '/admin/pos/drawer',
                        component:  () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/drawer/grid'),
                        props: true,
                        name: 'drawer'
                    },
                    {
                        path: '/admin/pos/sale',
                        component:  () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/sale/grid'),
                        props: true,
                        name: 'sale'
                    },
                    {
                        path: '/admin/pos/sale/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-pos" */ '../components/views/pos/sale/editForm'),
                        props: true,
                        name: 'sale_edit'
                    },]
            };
            

export default posView;