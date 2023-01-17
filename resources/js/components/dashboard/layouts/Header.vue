<template>
    <nav class="navbar navbar-expand-lg bg-primary position-sticky top-0" style="z-index : 1000;">
            <div class="container-fluid">
                <a class="navbar-brand text-white d-flex align-items-center" href="#">
                    <img src="/images/logo_ucsm.png" class="me-2" style="width: 30px;" alt="">
                    <span class="fw-bold h3 mb-0 ">Dashboard</span>

                </a>
                <div class="d-flex">
                    <p class="text-white me-2 mb-0">Lock : </p>
                    <div class="form-check form-switch">
                        <input class="form-check-input lock" type="checkbox" id="flexSwitchCheckDefault" :checked="isLock" @change="() => {
                            setLock();
                        }">
                    </div>
                </div>

                <button class="btn btn-light menu-btn" type="button" data-bs-toggle="modal" data-bs-target="#admin">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

        </nav>
</template>
<script>
import axios from 'axios';


export default {
    data(){
        return {
            isLock : null
        }
    },
    created(){
        axios.get('/api/isLock')
            .then((res) => this.isLock = res.data.data)
            .catch(err => console.log(err))
    },
    methods: {
        async setLock(){
            const user = JSON.parse(localStorage.auth);
            console.log(user.data.token);
            const res = await axios.post('/api/setLock','',{
                            headers: {
                                Authorization: `Bearer ${user.data.token}`,
                            },
                        })
            if(res.data.success){
                axios.get('/api/isLock')
                    .then((res) => this.isLock = res.data.data)
                    .catch(err => console.log(err))
            }
        }
    }
}
</script>

Menu
