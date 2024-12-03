import store from '../store/store.js';

/* auth app settings */
const authView = {
                path: 'admin/auth',
                component: () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/index'),
                meta: { },
                name: 'auth',                                                   // must match $store.state.sections[].module
                beforeEnter: async(to, from, next) => {
                    await store.dispatch('setLocationSection',to);              // set the location section and its schemas before we enter
                    //if(to.query.search) store.commit('auth/setSearch',to.query.search || null);
                    if(to.params.focus) store.commit('auth/setFocus',to.params.focus || null);
                    next();
                },
                children: [
                    {
                        path: '/admin/auth',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/settings/profile'),
                        name: 'auth_index'
                    },                 
                    {
                        path: '/admin/auth/settings',
                        component: () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/settings/profile'),
                        props: true,
                        name: 'location'                                           // must match $store.state.sections[].views[].name
                    },                      
                    {
                        path: '/admin/auth/users',
                        component: () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/user/grid'),
                        props: true,
                        name: 'user'
                    },
                    {
                    path: '/admin/auth/users/create',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/user/createForm'),
                        props: true,
                        name: 'user_create'
                    },
                    {
                        path: '/admin/auth/task',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/task/grid'),
                        props: true,
                        name: 'task'
                    },
                    {
                        path: '/admin/auth/task/create',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/task/editForm'),
                        props: true,
                        name: 'task_create'
                    },  
                    {
                        path: '/admin/auth/task/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/task/editForm'),
                        props: true,
                        name: 'task_edit'
                    },
                    {
                        path: '/admin/auth/addressbook',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/addressbook/grid'),
                        props: true,
                        name: 'addressbook'
                    },
                    {
                        path: '/admin/auth/addressbook/create',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/addressbook/editForm'),
                        props: true,
                        name: 'addressbook_create'
                    },  
                    {
                        path: '/admin/auth/addressbook/:id/edit',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/addressbook/editForm'),
                        props: true,
                        name: 'addressbook_edit'
                    },
                    {
                        path: '/admin/auth/locations',
                        component: () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/locations/grid'),
                        props: true,
                        name: 'locations'                                       // must match $store.state.sections[].views[].name
                    },
                    {
                        path: '/admin/auth/migration',
                        component: () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/locations/migration'),
                        props: true,
                        name: 'migration'
                    },
                    {
                        path: '/admin/auth/test',
                        component:  () => import(/* webpackChunkName: "js/view-auth" */ '../components/views/auth/index'),
                        props: true,
                        name: 'test'
                    }
                ]
            };
            

export default authView;