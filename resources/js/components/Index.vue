<template>
    <div>
        <nav class="navbar navbar-expand-lg bg-primary position-sticky top-0" style="z-index : 1000;">
            <div class="container-fluid">
                <a class="navbar-brand text-white d-flex align-items-center" href="#"> 
                    <img src="/images/logo_ucsm.png" class="me-2" style="width: 30px;" alt="">
                    <span class="fw-bold h3 mb-0 me-2">UCSM</span>
                    <span class="welcome">Flesher Welcome</span>
                    
                </a>
                
            </div>
        </nav>
        <div class="container" style="margin-top : 80px">
            <div class="row my-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h1 class="fw-bold">Welcome !</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</template>
<script>

export default{ 

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
