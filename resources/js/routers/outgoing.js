import store from '../store/store.js';

/* Outgoing Vue Views */
const outgoingView = {
                path: 'admin/outgoing',
                component: () => import(/* webpackChunkName: "js/view-grow" */ '../components/views/outgoing/index'),
                meta: { },
                name: 'outgoing',                                              // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    //if(to.query.search) store.commit('outgoing/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('outgoing/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/outgoing/package',
                        component:  () => import('../components/views/outgoing/package/grid'),
                        props: true,
                        name: 'package'
                    },
                    {
                        path: '/admin/outgoing/package/:id/show',
                        component:  () => import('../components/views/outgoing/package/showDetails'),
                        props: true,
                        name: 'package_show'
                    },
                    {
                        path: '/admin/outgoing/package/:id/createPackages',
                        component:  () => import('../components/views/outgoing/package/createMultiplePackagesForm'),
                        props: true,
                        name: 'package_create_packages'
                    },
                    {
                        path: '/admin/outgoing/transfer',
                        component:  () => import('../components/views/outgoing/transfer/grid'),
                        props: true,
                        name: 'transfer'
                    }
                ]
            };
            

export default outgoingView;