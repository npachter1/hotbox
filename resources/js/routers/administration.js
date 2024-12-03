import store from '../store/store.js';

/* Administration / Customer Vue Views */
const administrationView = {
                path: 'admin/administration',
                component: () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/index'),
                meta: { },
                name: 'administration',                                    // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    //if(to.query.search) store.commit('administration/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/administration',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/adminDashboard'),
                        name: 'administration_index'
                    },
                    {
                        path: '/admin/administration/customer',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/customer/grid'),
                        props: true,
                        name: 'customer'
                    },
                    {
                        path: '/admin/administration/customer/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/customer/editForm'),
                        props: true,
                        name: 'customer_edit'
                    },
                    {
                        path: '/admin/administration/customer/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/customer/editForm'),
                        props: true,
                        name: 'customer_create'
                    },
                    {
                        path: '/admin/administration/tax',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/tax/grid'),
                        props: true,
                        name: 'tax'
                    },
                    {
                        path: '/admin/administration/tax/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/tax/editForm'),
                        props: true,
                        name: 'tax_create'
                    },  
                    {
                        path: '/admin/administration/tax/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/tax/editForm'),
                        props: true,
                        name: 'tax_edit'
                    },
                    
                    
                    {
                        path: '/admin/administration/analytics',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/adminDashboard'),
                        props: true,
                        name: 'analytics'
                    },
                    {
                        path: '/admin/administration/analytics/merchandise',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/analytics/merchandise'),
                        props: true,
                        name: 'analytics-merchandise'
                    },
                    {
                        path: '/admin/administration/analytics/demographics',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/analytics/demographics'),
                        props: true,
                        name: 'analytics-demographics'
                    },
                    {
                        path: '/admin/administration/analytics/loyalty',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/analytics/loyalty'),
                        props: true,
                        name: 'analytics-loyalty'
                    },
                    {
                        path: '/admin/administration/reports/pos',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/administration/reports/pos'),
                        props: true,
                        name: 'reports'
                    }]
            };
            

export default administrationView;