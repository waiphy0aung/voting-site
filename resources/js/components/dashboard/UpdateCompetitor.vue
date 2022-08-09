<template>
    <Master>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{name : 'home'}">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Update Competitors</li>
            </ol>
        </nav>
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-plus-circle me-2"></i>
                        Update Competitor
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form @submit.prevent="updateCompetitor(Number(this.$route.params.id))" enctype="multipart/form-data">
                    <div class="mt-3">
                            <label for="role" class="form-label">Role</label>
                            <select v-model="competitor.role" class="form-select" id="role">
                                <option v-for="r in role" :key="r.slug" :value="r.slug">{{ r.slug }}</option>
                            </select>
                            <small v-if="this.errors.role" class="text-danger fw-bold">Please select a category</small>
                        </div>
                        <div class="mt-3">
                            <label for="name" class="form-label">Competitor Name</label>
                            <input type="text" v-model="competitor.name" class="form-control" id="name">
                            <small v-if="this.errors.name" class="text-danger fw-bold">{{this.errors.name[0]}}</small>
                        </div>
                        <div class="mt-3">
                            <label for="name" class="form-label">Competitor Photo</label>
                            <input type="text" v-model="competitor.photo" class="form-control" id="name">
                            <small v-if="this.errors.photo" class="text-danger fw-bold">{{this.errors.photo[0]}}</small>
                        </div>
                        <div class="mt-3">
                            <label for="no" class="form-label">Competitor No</label>
                            <input type="text" v-model="competitor.no" class="form-control" id="no">
                            <small v-if="this.errors.no" class="text-danger fw-bold">{{this.errors.no[0]}}</small>
                        </div>
                        <button class="btn btn-primary mt-3 text-white " type="submit" :disabled="loading === true">Update Competitor</button>
                </form>
            </div>
        </div>
    </Master>
</template>
<script>
import Master from "./layouts/Master.vue";
export default {
    data(){
        return {
            role : [
                {name : 'Prince',slug : 'prince'},
                {name : 'Princess',slug : 'princess'},
                {name : 'Best Performance',slug : 'performance'},
                
            ],
            competitor : {
                "name" : "",
                "role" : "",
                "photo" : "",
                "no" : ""
            },
            errors : "",
            loading : false
        }
    },
    components : {Master},
    created(){
        this.$Progress.start();
        console.log(this.$route.params.id)
        const competitor = this.$store.state.competitors.find((c) => c.id === Number(this.$route.params.id))
        console.log(competitor)
        this.competitor.name = competitor.name;
        this.competitor.role = competitor.role;
        this.competitor.photo = competitor.profile;
        this.competitor.no = competitor.number_of_vote;
    },
     
    mounted(){
        this.$Progress.finish();
    },
    methods: {
        async updateCompetitor(id){
            const user = JSON.parse(localStorage.auth);
            this.loading = true;
            const formData = new FormData();
            formData.append('name',this.competitor.name)
            formData.append('role',this.competitor.role)
            formData.append('photo',this.competitor.photo)
            formData.append('no',this.competitor.no)
            console.log(formData)
            const res = await axios.post(`/api/competitor/${id}/update`,formData,{
                headers: {
                        Authorization: `Bearer ${user.data.token}`,
                    },
            });
            const {data,success} = res.data
            console.log(res.data)
            if (success === false){
                this.errors = data
                this.loading = false;
            }else{
                this.errors = {};

                
                // this.$store.commit('toast',data)
                this.loading = false;
                this.$store.commit('toast',data);
                this.$Progress.start();

                this.$router.push({name : 'competitor-list',params: {
                    competitor : this.competitor.role
                }})
                
            }
        }
    }
}
</script>