<template>
    <Master>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{name : 'home'}">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">URLs</li>
            </ol>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p v-for="u in url" :key="u.id">
                                {{ `http://localhost:8000/api/login?voter_id=${u.voter_id}&password=${u.password}` }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Master>
</template>
<script>
import Master from "./layouts/Master.vue";
export default {
    data(){
        return {
            url : []
        }
    },
    components : {Master},
    created(){
        this.$Progress.start();
        const user = JSON.parse(localStorage.auth);
        axios.get('/api/users',{
            headers: {
                        Authorization: `Bearer ${user.data.token}`,
                    },
        })
        .then((res) => {
            this.url = res.data;
        })
    },
    
    mounted(){
        this.$Progress.finish();
    },

}
</script>