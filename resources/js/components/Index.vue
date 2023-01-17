<template>
    <div class="container bg-primary">
        <div class="row vh-100">
            <div class="col-12">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <img src="/images/logo_ucsm.png" alt="" width="300">
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Footer from './layouts/Footer.vue'
export default{
    components : {Footer},
  data(){
      return {
          url : []
      }
  },
   created(){
        this.$Progress.start();
    },
    mounted(){
        this.$Progress.finish();
    },

}
</script>
