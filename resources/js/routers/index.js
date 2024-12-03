import camelCase from "lodash/camelCase";
const requireModule = require.context(".", false, /\.js$/); //extract js files inside modules folder


const views = [
            {
                path: '/admin',
                component: require('../components/views/dashboard').default,
                name: 'location_index'
            },
            {
                path: '/admin/dashboard',
                component: require('../components/views/dashboard').default,
                name: 'dashboard'
            },
];

requireModule.keys().forEach(fileName => {
    if (fileName === "./index.js") return; //reject the index.js file
    views.push(requireModule(fileName).default);                                // add the app section modules router views are in this directory
});


export default views;