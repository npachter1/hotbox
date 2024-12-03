import store from '../store/store.js';

/* Products / Priceset Vue Views */
const productsView = {
                path: 'admin/products',
                component: () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/index'),
                meta: { },
                name: 'products',                                    // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas
                    //if(to.query.search) store.commit('products/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/products',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/product/grid'),
                        name: 'products_index'
                    },
                    {
                        path: '/admin/products/product',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/product/grid'),
                        props: true,
                        name: 'product'
                    },
                    {
                        path: '/admin/products/product/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/product/editForm'),
                        props: true,
                        name: 'product_create'
                    },  
                    {
                        path: '/admin/products/product/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/product/editForm'),
                        props: true,
                        name: 'product_edit'
                    },
                    {
                        path: '/admin/products/receiving',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/receiving/grid'),
                        props: true,
                        name: 'receiving'
                    },
                    {
                        path: '/admin/products/receiving/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/receiving/editForm'),
                        props: true,
                        name: 'receiving_create'
                    },  
                    {
                        path: '/admin/products/receiving/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/receiving/editForm'),
                        props: true,
                        name: 'receiving_edit'
                    },
                    {
                        path: '/admin/products/priceset',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/priceset/grid'),
                        props: true,
                        name: 'priceset'
                    },
                    {
                        path: '/admin/products/priceset/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/priceset/editForm'),
                        props: true,
                        name: 'priceset_create'
                    },  
                    {
                        path: '/admin/products/priceset/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/priceset/editForm'),
                        props: true,
                        name: 'priceset_edit'
                    },
                    {
                        path: '/admin/products/inventory',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/inventory/grid'),
                        props: true,
                        name: 'inventory'
                    },
                    {
                        path: '/admin/products/inventory/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/inventory/editForm'),
                        props: true,
                        name: 'inventory_edit'
                    },
                    {
                        path: '/admin/products/category',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/category/grid'),
                        props: true,
                        name: 'category'
                    },
                    {
                        path: '/admin/products/category/create',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/category/editForm'),
                        props: true,
                        name: 'category_create'
                    },  
                    {
                        path: '/admin/products/category/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-dispensary" */ '../components/views/products/category/editForm'),
                        props: true,
                        name: 'category_edit'
                    }]
            };
            

export default productsView;