<template>
    <Master>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{name : 'home'}">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ this.$route.params.competitor }}</li>
            </ol>
        </nav>
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-list me-2"></i>
                            <span class="text-uppercase me-2">{{this.$route.params.competitor}}</span> LIST
                        </div>
                        <router-link :to="{name : 'create-competitors'}"  class="text-decoration-none">
                            <i class="fa fa-plus-circle"></i>
                        </router-link>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-borderless table-hover">
                            <thead>
                                <tr class="table-light">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th class="text-nowrap">Vote Count</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="competitor in competitors"
                                    :key="competitor.id"
                                >
                                    <td style="min-width:50px">{{ competitor.id }}</td>
                                    <td class="text-nowrap">{{ competitor.name }}</td>
                                    <td>{{ competitor.vote_count }}</td>
                                    <td class="text-nowrap">
                                        <span class="small">
                                            <i class="fa fa-calendar"></i>
                                            {{
                                                dateFormat(
                                                    competitor.created_at,
                                                    "MMM d YYYY"
                                                )
                                            }}
                                        </span>
                                        <br />
                                        <span class="small">
                                            <i class="fa fa-clock"></i>
                                            {{
                                                dateFormat(
                                                    competitor.created_at,
                                                    "h:mm a"
                                                )
                                            }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <router-link :to="{name : 'update-competitors',params: {id : competitor.id}}" class="btn">
                                            <i
                                                class="fa fa-pencil text-black"
                                            ></i>
                                        </router-link>
                                        <button class="btn" @click="deleteCompetitor(competitor.id)">
                                            <i
                                                class="fa fa-trash text-danger"
                                            ></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </Master>
</template>
<script>
import Master from "./layouts/Master.vue";
import moment from "moment";
export default {
    components : {Master,moment},
     created(){
        this.$Progress.start();
    },
    mounted(){
        this.$Progress.finish();
    },
    computed : {
        competitors(){
            if(this.$route.params.competitor){
                return this.$store.state.competitors.filter(
                    c => c.role === this.$route.params.competitor
                )
            }
        }
    },
    methods: {
        dateFormat(date, format) {
            return moment(date).format(format);
        },
        deleteCompetitor(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted successfully!',
                        'Competitor has been deleted.',
                        'success'
                    )
                    const user = JSON.parse(localStorage.auth);
                    axios
                        .delete(`/api/competitor/${id}/delete`,{
                            headers: {
                                Authorization: `Bearer ${user.data.token}`,
                            },
                        })
                        .then(response => {
                            console.log(response.data),
                            this.$store.dispatch("getCompetitors");
                        })
                        .catch((err) => console.error(err));
                        }
            })

            
        }
    }
}
</script>