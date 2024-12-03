import store from '../store/store.js';

/* Warehouse Vue Views */
const warehouseView = {
                path: 'admin/warehouse',
                component: () => import(/* webpackChunkName: "js/view-grow" */ '../components/views/warehouse/index'),
                meta: { },
                name: 'warehouse',                                              // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    //if(to.query.search) store.commit('warehouse/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('warehouse/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/warehouse/room',
                        component:  () => import('../components/views/warehouse/room/grid'),
                        props: true,
                        name: 'room'
                    },
                    {
                        path: '/admin/warehouse/room/create',
                        component:  () => import('../components/views/warehouse/room/editForm'),
                        props: true,
                        name: 'room_create'
                    },  
                    {
                        path: '/admin/warehouse/room/:id/edit',
                        component:  () => import('../components/views/warehouse/room/editForm'),
                        props: true,
                        name: 'room_edit'
                    },
                    {
                        path: '/admin/warehouse/strain',
                        component:  () => import('../components/views/warehouse/strain/grid'),
                        props: true,
                        name: 'strain'
                    },
                    {
                        path: '/admin/warehouse/strain/create',
                        component:  () => import('../components/views/warehouse/strain/editForm'),
                        props: true,
                        name: 'strain_create'
                    },  
                    {
                        path: '/admin/warehouse/strain/:id/edit',
                        component:  () => import('../components/views/warehouse/strain/editForm'),
                        props: true,
                        name: 'strain_edit'
                    }, 
                    {
                        path: '/admin/warehouse/item',
                        component:  () => import('../components/views/warehouse/item/grid'),
                        props: true,
                        name: 'item'
                    },
                    {
                        path: '/admin/warehouse/item/create',
                        component:  () => import('../components/views/warehouse/item/editForm'),
                        props: true,
                        name: 'item_create'
                    },  
                    {
                        path: '/admin/warehouse/item/:id/edit',
                        component:  () => import('../components/views/warehouse/item/editForm'),
                        props: true,
                        name: 'item_edit'
                    }   
                ]
            };
            

export default warehouseView;