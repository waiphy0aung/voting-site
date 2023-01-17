require('./bootstrap');
window.$ = require('jquery');

import 'bootstrap/dist/js/bootstrap.bundle';
window.Swal = require("sweetalert2");
import axios from 'axios';
import VueSweetalert2 from "vue-sweetalert2";
import VueProgressBar from "@aacassandra/vue3-progressbar";

const options = {
    color: "#198754",
    failedColor: "#874b4b",
    thickness: "5px",
    transition: {
      speed: "0.2s",
      opacity: "0.6s",
      termination: 300,
    },
    autoRevert: true,
    location: "top",
    inverse: false,
  };

import { createApp } from "vue";

import {
    createRouter,
    createWebHashHistory,
    createWebHistory,
} from "vue-router";

import { createStore } from "vuex";

const router = new createRouter({
    history: createWebHistory(),
    routes: require("./routes"),
});

router.beforeEach(async (to,from,next) => {

    console.log(localStorage.role)

    store.dispatch("getCompetitors");
    if (localStorage.role == "user") {
        if (to.meta.isAdmin) {
            return router.push("/home");
        }
    }

    const auth = localStorage.getItem("auth");
    if(to.fullPath !== "/"){
        if(auth){
            if(localStorage.role === 'admin'){
                return next();
            }else{
                const res = await axios.get('/api/isLock');
                if(res.data.data){
                    return next('/')
                }else{
                    return next();
                }
            }
        }
    }

    return next()
})

// router.beforeEach((to,from,next)=>{
//     const auth = localStorage.getItem("auth");
//     if(to.fullPath !== "/"){
//         if(auth){
//             if(JSON.parse(auth).data.user.role === 'admin'){
//                 // next();
//                 return false
//             }else{
//                 if(store.state.lock){
//                     return router.push('/')
//                 }else{
//                     // next();
//                     return false
//                 }
//             }
//         }else{
//             return router.push('/');
//         }
//     }
//     return next();

// })

router.afterEach((to, from) => {
    window.scroll(0,0);
  })
// router.beforeEach((to, from, next) => {

//     if (!to.meta.isAuth) {
//         return router.push('/');
//     }
//     next();

// });

const store = createStore({
    state(){
        return {
            roles: [],
            competitors : [],
            voteCompetitors : [],
            auth : [],
            loading : false,
            lock: null
        }
    },
    mutations: {
        updateRoles(state,roles){
            state.roles = roles;
        },
        updateCompetitors(state,competitors){
            state.competitors = competitors;
        },
        updateVoteCompetitors(state,voteCompetitors){
            state.voteCompetitors = voteCompetitors;
        },
        setAuth(state, payload) {
            state.auth = payload;
        },
        setLogout(state) {
            localStorage.clear();
            state.auth = [];
        },
        toast(state, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: "bottom",
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });

            Toast.fire({
                icon: "success",
                title: title,
            });
        }
    },
    actions: {
        async getRoles({commit}){
            if(localStorage.auth){
                const user = JSON.parse(localStorage.auth);
                if(user){
                    const user_id = user.data.user.id;

                    const result = await axios.get('/api/roles',{
                        headers: {
                            Authorization: `Bearer ${user.data.token}`,
                        },
                    })
                    console.log(result);
                    if(result.data.success){
                        commit('updateRoles',result.data.data);
                    }
                        // .then((res) => {
                        //     commit('updateCompetitors',res.data);
                        // })
                        // .catch((err) => console.log(err))
                }
            }
        },
        getCompetitors({commit}){
            if(localStorage.auth){
                const user = JSON.parse(localStorage.auth);
                if(user){
                    const user_id = user.data.user.id;

                    axios.post('/api/competitors',{user_id : user_id},{
                        headers: {
                            Authorization: `Bearer ${user.data.token}`,
                        },
                    })
                        .then((res) => {
                            commit('updateCompetitors',res.data);
                        })
                        .catch((err) => console.log(err))
                }
            }

        },
        getVoteCompetitors({commit}){
            const token = localStorage.token;
            if(token){
                axios.get('/api/vote-competitors',{
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                })
                .then((res) => {
                    commit('updateVoteCompetitors',res.data);
                })
                .catch((err) => console.log(err))
            }
        },
        getAuth(context, payload) {
            if (localStorage.getItem("auth")) {
                context.commit(
                    "setAuth",
                    JSON.parse(localStorage.getItem("auth"))
                );
            } else {
                context.commit("setAuth", payload);
            }
        },
        getAuth(context, payload) {
            if (localStorage.getItem("auth")) {
                context.commit(
                    "setAuth",
                    JSON.parse(localStorage.getItem("auth"))
                );
            } else {
                context.commit("setAuth", payload);
            }
        },
        logout({ commit, state }) {
            commit("setLogout");
            router.push({name : 'index'});
            commit("updateCompetitors", []);
            commit("updateVoteCompetitors", []);
        }

    }
});

const app = createApp({
    created(){
        store.dispatch('getRoles')
            .then((_)=>{})
            .catch(err => console.log(err));
        store.dispatch('getCompetitors')
            .then((_) => {})
            .catch(err => console.log(err));
        store.dispatch('getVoteCompetitors')
            .then((_) => {})
            .catch(err => console.log(err));
        store.dispatch("getAuth")
            .then((_) => {})
            .catch((error) => console.log(error));
    }
})

app.use(store);
app.use(router);
app.use(VueSweetalert2);
app.use(VueProgressBar,options);
app.mount("#app");

$(window).on("load",function (){
    $(".loader-container").fadeOut(500,function (){
        $(this).remove()
    })
});
