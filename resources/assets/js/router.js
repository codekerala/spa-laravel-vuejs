import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        {path: '/', component: require('./views/customer/index.vue')},
        {path: '/customer/create', component: require('./views/customer/form.vue')},
        {path: '/customer/:id/edit', component: require('./views/customer/form.vue'), meta: {mode: 'edit'}},
        {path: '/customer/:id', component: require('./views/customer/show.vue')},

        {path: '/invoice', component: require('./views/invoice/index.vue')},
        {path: '/invoice/create', component: require('./views/invoice/form.vue')},
        {path: '/invoice/:id/edit', component: require('./views/invoice/form.vue'), meta: {mode: 'edit'}},
        {path: '/invoice/:id', component: require('./views/invoice/show.vue')},
    ]
})

export default router