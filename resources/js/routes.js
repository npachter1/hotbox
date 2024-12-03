import VueRouter from 'vue-router';
import NProgress from 'vue-nprogress';

import store from './store/store.js';

const nprogress = new NProgress();

/* define route components by name via factory function for lazy loading */
import AppSectionViews from './routers';


let routes = [
    {
        path: '/admin',
        component: require('./components/layouts/admin-page').default,
        meta: { requiresAuth: true, showProgressBar: true },
        children: AppSectionViews                                               // HINT: All App Section views are in admin/[grow/dispensary/office] section..
    },
    {
        path: '/',
        component: require('./components/layouts/guest-page').default,
        meta: { requiresGuest: true, showProgressBar: false },
        children: [
            {
                path: '/login',
                component: require('./components/views/auth/login').default,
                name: 'login'
            },
            {
                path: '/password',
                component: require('./components/views/auth/password').default,
                name: 'password'
            },
            {
                path: '/register',
                component: require('./components/views/auth/register').default,
                name: 'register'
            },
            {
                path: '/auth/:token/activate',
                component: require('./components/views/auth/activate').default,
                name: 'activate'
            },
            {
                path: '/password/reset/:token',
                component: require('./components/views/auth/reset').default,
                name: 'passwordreset'
            },
            {
                path: '/auth/social',
                component: require('./components/views/auth/social-auth').default,
                name: 'social'
            },
            {
                path: '/',
                component: require('./components/views/auth/login').default,
                name: 'index'
            },
        ]
    },    
    {
        path: '*',
        component : require('./components/layouts/error-page').default,
        meta: { showProgressBar: false },
        children: [
            {
                path: '*',
                component: require('./components/views/page-not-found').default
            }
        ]
    }
];


const router = new VueRouter({
	routes,
    linkActiveClass: 'active',
    mode: 'history'
});

function shouldClearSearch(toPath, fromPath, ka=false) {
    if(ka){                                     // optional forcing the cached data from destination route params
        return false;       
    } else if (toPath.indexOf(fromPath)===0) { //staying in path
        //check to see, for example if to path is /admin/plants/plantbatch and from path is /admin/plants/plant
        let diff = toPath.replace(fromPath,'');
        if (/\/[\d]*\/edit/.test(diff)) { //back from path ('return' from edit)
            return false;
        }
    } else if (fromPath.indexOf(toPath)===0)  {
        //backing out of path, probably going from something like /admin/administration/customer/63/edit to /admin/administration/customer
        let diff = fromPath.replace(toPath,'');
        if (/\/[\d]*\/edit/.test(diff)) { //back from path ('return' from edit)
            return false;
        }
    }
    return true;
}

router.beforeEach(async (to, from, next) => {

    if (shouldClearSearch(to.path,from.path,(to.params.keepAlive || false))) await store.dispatch('clearSearch');

    if (to.matched.some(m => m.meta.showProgressBar)){   
        nprogress.start();
        nprogress.set(0.3);
    }

    if (to.matched.some(m => m.meta.requiresAuth)){
        if(!localStorage.getItem('access_token')) 
            return next({ path : '/login'})                                     // if we dont have an access token, return to login (else dashboard would intercept a 401 to relogin)
        else if(!store.state.user.location && to.path!='/admin/dashboard')
            await store.dispatch('resetAuthLocation',{reset:false});               // Or if we dont have a loaded location (ie: new tab of logged in browser) then await a location reset
    }

    if (to.matched.some(m => m.meta.requiresGuest)){                     
        if(localStorage.getItem('access_token') && to.path=='/') 
            return next({ path : '/admin/dashboard'})                           // if we have token and path is root, then go to dashboard.
    }
    

    return next();
});

router.afterEach((to, from) => {
    
    if (to.matched.some(m => m.meta.showProgressBar)){
        setTimeout(() => nprogress.done(), 400);
    }
    


});


export default router;
