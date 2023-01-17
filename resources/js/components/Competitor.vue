<template>
    <master>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{name : 'home'}">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ this.$route.params.competitor }}</li>
            </ol>
        </nav>
        <div v-if="competitors.length == 0">There is no competitors.Please create</div>
        <div class="col-ms-12 col-md-6 col-lg-4 mb-3"
            v-for="competitor in competitors"
            :key="competitor.id"
            v-else
        >
            <div class="card">
                <div class="shadow-sm rounded card-body shadow">
                    <vue-load-image v-if="competitor.profile">
                        <template v-slot:image style="posititon:relative">
                            <div class="vote-no position-absolute bg-primary me-2 text-white rounded-circle d-flex justify-content-center align-items-center"
                             style="width : 50px;height:50px;top : 25px ; left : 25px">
                                <h4 class="mb-0">{{ competitor.number_of_vote }}</h4>
                            </div>
                            <img :src="
                            competitor.profile ?
                            '/'+competitor.profile
                            :
                            '/images/batman.jpeg'
                            " alt="" class="img-fluid">
                        </template>
                        <template v-slot:preloader>
                            <div class="img-loader">
                                <img src="/images/im-loader.gif"/>
                            </div>
                        </template>
                        <template v-slot:error>
                            <div class="img-loader">Image load fails</div>
                        </template>
                    </vue-load-image>

                    <hr v-if="competitor.role !== 'performance'">
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0" style="line-height: inherit">{{ competitor.name }}</h3>
                        <form action="" @click.prevent="vote()"></form>
                        <div v-if="competitor.loading" :id="'spinner'+competitor.id" class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <button v-else
                        :disabled="isVoted == true && competitor.is_vote == false"
                         class="text-white"
                        :class="
                        isVoted == true && competitor.is_vote == false ?
                        'text-decoration-line-through btn btn-primary'
                        :
                        competitor.is_vote ? 'btn-success btn' : 'btn btn-primary'
                        "
                        @click="
                        isVoted == true && competitor.is_vote == false ?
                        ''
                        :
                        vote(competitor.role,this.$store.state.auth.data.user.id,competitor.id)
                        "
                        >

                        <span class="text-nowrap">
                            {{

                            competitor.is_vote ?
                            'voted'

                            :
                            'vote'
                         }}
                         <i v-if="competitor.is_vote" class="fa fa-check"></i>
                        </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </master>
</template>
<script>
import Master from "./layouts/Master.vue";
import VueLoadImage from 'vue-load-image'
export default{
  components: { Master ,'vue-load-image': VueLoadImage },
  data(){
      return {
          loading : false
      }
  },
  created(){
      this.$Progress.start();
      const votedCompetitor = this.competitors.find(
            c => c.is_vote === true
        )
      console.log(votedCompetitor)
  },
    mounted(){
        this.$Progress.finish();
    },
  computed: {
      isVoted(){
        const voted = this.competitors.find(
            c => c.is_vote === true
            )
        if(voted){
            return true;
        }else{
            return false;
        }
      },
      competitors(){
          return this.$store.state.competitors.filter(
              (c) => c.role === this.$route.params.competitor
          );
      },


  },
    methods: {
      async vote(role,id,competitor_id){
            const competitor = this.competitors.find(c => c.id === competitor_id);
            competitor.loading = true;
            const res = await axios.post(`/api/competitor/vote`,
            {
              user_id : id,
              competitor_id : competitor_id,
              role : role
              },
              {headers: {
                        Authorization: `Bearer ${this.$store.state.auth.data.token}`,
                    }},
          )
          this.$store.commit('toast',res.data.data)

            this.$store.dispatch("getCompetitors");
            this.$store.dispatch("getVoteCompetitors");
            setTimeout(()=>{
                competitor.loading = false;
            },2000)
        }
    }
}
</script>
