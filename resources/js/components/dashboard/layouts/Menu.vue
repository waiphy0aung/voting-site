<template>
    <div class="modal fade" id="admin" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center">
                    <!-- <p v-if="user" class="fw-bold text-black-50 mb-0">id : {{ user.voter_id }}</p> -->
                    <h3 class="welcome mb-0">dashboard</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <ul>
                            <li>
                                <button data-bs-dismiss="modal" @click="this.$router.push({name : 'urls'})" class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill">
                                    User Management
                                </button>
                            </li>
                            <li>
                                <button data-bs-dismiss="modal" @click="this.$router.push({name : 'create-competitors'})" class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill">
                                    Create Competitors
                                </button>
                            </li>
                            <li>
                                <button data-bs-dismiss="modal" @click="this.$router.push({name : 'roles'})" class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill">
                                    Create Roles
                                </button>
                            </li>
                            <li v-for="r in role" :key="r.id"><button
                            data-bs-dismiss="modal"
                             class="my-2 w-75 text-white btn-lg btn btn-primary rounded-pill" @click="menuBtn(r.slug)"
                             >{{ r.name }} List</button></li>
                        </ul>
                        <button data-bs-dismiss="modal" class="my-3 w-75 text-white btn-lg btn btn-success rounded-pill" @click="this.$store.dispatch('logout')">Logout</button>
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
            // role : [
            //     {name : 'King',slug : 'king'},
            //     {name : 'Queen',slug : 'queen'},
            //     {name : 'Prince',slug : 'prince'},
            //     {name : 'Princess',slug : 'princess'},
            //     {name : 'Best Performance',slug : 'performance'},
            // ]
        }
    },
    computed : {
        role(){
            return this.$store.state.roles;
        }
    },
    methods: {
        menuBtn(competitor){
            console.log(competitor);
            if(competitor){
                this.$Progress.start();
                this.$router.push({name : 'competitor-list',params: {competitor : competitor}})
            }
        }
    }
}
</script>
