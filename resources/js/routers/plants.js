import store from '../store/store.js';

/* Plants Vue Views */
const plantsView = {
                path: 'admin/plants',
                component: () => import(/* webpackChunkName: "js/view-grow" */ '../components/views/plants/index'),
                meta: { },
                name: 'plants',                                              // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    //if(to.query.search) store.commit('plants/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('plants/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/plants/plantbatch',
                        component:  () => import('../components/views/plants/plantbatch/grid'),
                        props: true,
                        name: 'plantBatch'
                    },
                    {
                        path: '/admin/plants/plantbatch/:id/show',
                        component:  () => import('../components/views/plants/plantbatch/showDetails'),
                        props: true,
                        name: 'plantBatch_show'
                    },   
                    {
                        path: '/admin/plants/plant',
                        component:  () => import('../components/views/plants/plant/grid'),
                        props: true,
                        name: 'plant'
                    },
                    {
                        path: '/admin/plants/plant/:id/show',
                        component:  () => import('../components/views/plants/plant/showDetails'),
                        props: true,
                        name: 'plant_show'
                    },   
                    {
                        path: '/admin/plants/harvest',
                        component:  () => import('../components/views/plants/harvest/grid'),
                        props: true,
                        name: 'harvest'
                    },
                    {
                        path: '/admin/plants/harvest/:id/show',
                        component:  () => import('../components/views/plants/harvest/showDetails'),
                        props: true,
                        name: 'harvest_show'
                    },   
                ]
            };
            

export default plantsView;