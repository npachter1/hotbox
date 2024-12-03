import store from '../store/store.js';

/* Loyalty / Campaign Vue Views */
const loyaltyView = {
                path: 'admin/loyalty',
                component: () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/index'),
                meta: { },
                name: 'loyalty',                                    // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    if(to.query.search) store.commit('loyalty/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/loyalty',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/group/grid'),
                        name: 'loyalty_index'
                    },
                    {
                        path: '/admin/loyalty/group',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/group/grid'),
                        props: true,
                        name: 'group'
                    },
                    {
                        path: '/admin/loyalty/group/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/group/editForm'),
                        props: true,
                        name: 'group_create'
                    },  
                    {
                        path: '/admin/loyalty/group/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/group/editForm'),
                        props: true,
                        name: 'group_edit'
                    },
                    {
                        path: '/admin/loyalty/discount',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/discount/grid'),
                        props: true,
                        name: 'discount'
                    },
                    {
                        path: '/admin/loyalty/discount/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/discount/editForm'),
                        props: true,
                        name: 'discount_create'
                    },  
                    {
                        path: '/admin/loyalty/discount/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/discount/editForm'),
                        props: true,
                        name: 'discount_edit'
                    },
                    {
                        path: '/admin/loyalty/reward',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/reward/grid'),
                        props: true,
                        name: 'reward'
                    },
                    {
                        path: '/admin/loyalty/reward/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/reward/editForm'),
                        props: true,
                        name: 'reward_create'
                    },  
                    {
                        path: '/admin/loyalty/reward/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/reward/editForm'),
                        props: true,
                        name: 'reward_edit'
                    },
                    {
                        path: '/admin/loyalty/campaign',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/campaign/grid'),
                        props: true,
                        name: 'campaign'
                    },
                    {
                        path: '/admin/loyalty/campaign/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/campaign/editForm'),
                        props: true,
                        name: 'campaign_create'
                    },  
                    {
                        path: '/admin/loyalty/campaign/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/loyalty/campaign/editForm'),
                        props: true,
                        name: 'campaign_edit'
                    },]
            };
            

export default loyaltyView;