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
                        <ul>
                            <li v-for="r in role" :key="r.name"><button
                            @click="menuBtn(r.slug)"
                             class="my-3 w-75 text-white btn-lg btn btn-primary rounded-pill"
                             >{{ r.name }}</button></li> 
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
    methods : {
        menuBtn(competitor){
            if(competitor){
                this.$Progress.start();
                this.$router.push({name : 'competitor',params: {competitor : competitor}})
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