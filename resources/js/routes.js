const hasAuth = (to, from, next) => {
    const auth = localStorage.getItem("auth");
    if (auth) {
        return next();
    } else {
        return next('/');
    }
};

module.exports = [
    {
        path : '/',
        name : 'index',
        component: () => import("./components/Index.vue"),
        meta : {
            isAuth : true
        }
    },
    {
        path : '/home',
        name : 'home',
        component : () => import('./components/Home.vue'),
        beforeEnter : hasAuth

    },
    {
        path : '/competitors/:competitor',
        name : 'competitor',
        component : () => import('./components/Competitor.vue'),
        beforeEnter : hasAuth

    },
    {
        path : '/dashboard/competitors/create',
        name : 'create-competitors',
        component : () => import('./components/dashboard/CreateCompetitor.vue'),
        meta : {
            isAdmin : true
        },
        beforeEnter : hasAuth
    },
    {
        path : '/dashboard/competitors/:id/update',
        name : 'update-competitors',
        component : () => import('./components/dashboard/UpdateCompetitor.vue'),
        meta : {
            isAdmin : true
        },
        beforeEnter : hasAuth

    },
    {
        path : '/dashboard/competitors/:competitor',
        name : 'competitor-list',
        component : () => import('./components/dashboard/CompetitorList.vue'),
        meta : {
            isAdmin : true
        },
        beforeEnter : hasAuth

    },
    {
        path : '/dashboard/urls',
        name : 'urls',
        component : () => import('./components/dashboard/Qrcode.vue'),
        meta : {
            isAdmin : true
        },
        beforeEnter : hasAuth

    }
    
]