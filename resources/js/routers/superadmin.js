import store from '../store/store.js';

/* superadmin views */
const superadminView = {
                path: 'admin/superadmin',
                component:  () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/index'),
                meta: { },
                name: 'superadmin',                                             // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);                 // set the location section and its schemas
                    //if(to.query.search) store.commit('superadmin/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/superadmin',
                        component: () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/schemas/profile'),
                        name: 'superadmin_index'
                    },                 
                    {
                        path: '/admin/superadmin/schemas',
                        component: require('../components/views/superadmin/schemas/profile').default, // TODO when this chunks it doesnt load correct?! - but want this out of core bundle..
                        //component: () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/schemas/profile'),
                        props: true,
                        name: 'schemas'
                    },
                    {
                        path: '/admin/superadmin/servicelog',
                        component: () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/servicelog/grid'),
                        props: true,
                        name: 'servicelog'
                    },
                    {
                        path: '/admin/superadmin/tokens',
                        component: () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/tokens/grid'),
                        props: true,
                        name: 'tokens'
                    },
                    {
                        path: '/admin/superadmin/support',
                        component: () => import(/* webpackChunkName: "js/view-superadmin" */ '../components/views/superadmin/support/grid'),
                        props: true,
                        name: 'support'
                    }
                ]
            };
            
export default superadminView;