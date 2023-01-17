<template>
    <div class="">

        <div class="container">
            <div class="row vh-100">
                <div class="col-12 mt-5 text-center">
                    <img src="/images/logo_ucsm.png" class="me-2" style="width: 100px;" alt="">
                    <h1 class="mb-5 mt-2 display-3 text-primary">

                        <span class="fw-bold mb-0 ">UCSM</span>
                        <br />
                        <span class="welcome fw-bold" style="font-size: 0.8em">2022 Flesher Welcome</span>
                    </h1>
                    <div class="text-center">
                        <button v-if="this.$store.state.auth.data.user.role === 'admin'" @click="() => {this.$router.push({name : 'create-competitors'})}"  class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill">Dashboard</button>
                        <ul>
                            <li v-for="r in roles" :key="r.id"><button
                            @click="menuBtn(r.slug)"
                             class="my-3 w-75 text-white btn-lg btn btn-primary rounded-pill"
                             >{{ r.name }}  <i v-if="isVoted(r.slug)" class="text-success fa fa-check"></i></button></li>
                        </ul>

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
    computed: {
        roles(){
            return this.$store.state.roles;
        }
    },
    methods : {
        menuBtn(competitor){
            if(competitor){
                this.$Progress.start();
                this.$router.push({name : 'competitor',params: {competitor : competitor}})
            }
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
    },
    created(){
        this.$Progress.start();
    },
    mounted(){
        this.$Progress.finish();
    },
}
</script>
