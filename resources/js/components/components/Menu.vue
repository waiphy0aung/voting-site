<template>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <!-- <p v-if="user" class="fw-bold text-black-50 mb-0">id : {{ user.voter_id }}</p> -->
                    <h3 class="welcome mb-0">Flesher Welcome</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <button v-if="this.$store.state.auth.data.user.role === 'admin'" data-bs-dismiss="modal" @click="dashboard"  class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill">Dashboard</button>
                        
                        <ul>
                            <li v-for="r in role" :key="r.name"><button
                            data-bs-dismiss="modal"
                             class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill" @click="menuBtn(r.slug)"
                             >{{ r.name }}  <i v-if="isVoted(r.slug)" class="text-success fa fa-check"></i></button></li>
                        </ul>
                        <button data-bs-dismiss="modal" class="my-3 w-75 text-white btn-lg btn logoutBtn rounded-pill" @click="this.$store.dispatch('logout')">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            role : [
                {name : 'King',slug : 'king'},
                {name : 'Queen',slug : 'queen'},
                {name : 'Prince',slug : 'prince'},
                {name : 'Princess',slug : 'princess'},
                {name : 'Best Performance',slug : 'performance'},
            ]
        }
    },  
    computed : {
        
        user(){
            if(this.$store.state.auth){
                return this.$store.state.auth.data.user; 
            }
        }
    },
    methods: {
        menuBtn(competitor){
            if(competitor){
                
                this.$router.push({name : 'competitor',params: {competitor : competitor}})
            }
        },
        dashboard(){
            this.$Progress.start();
            this.$router.push({name : 'create-competitors'})
        },
        isVoted(role){
            const competitors = this.$store.state.competitors.filter(
                c => c.role === role
            );
            const voted = competitors.find(
                c => c.is_vote === true
                ) 
        if(voted){
            return true;
        }else{
            return false;
        }
      },
    }
}
</script>
