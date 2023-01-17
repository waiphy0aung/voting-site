<template>
    <Master>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{name : 'home'}">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Create Role</li>
            </ol>
        </nav>
        <div class="col-12">
            <div class="card">
                <div class="card-header py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-plus-circle me-2"></i>
                        Create Role
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form @submit.prevent="addRole()" enctype="multipart/form-data">
                    <div class="mt-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" v-model="name" class="form-control" id="name">
                        <small v-if="this.errors.name" class="text-danger fw-bold">{{this.errors.name[0]}}</small>
                    </div>
                    <button class="btn btn-primary mt-3 text-white " type="submit" :disabled="loading === true">Add Role</button>
                </form>
                <div class="mt-3 table-responsive" v-if="role?.length > 0">
                    <table class="table table-borderless w-100">
                        <thead class="table-primary">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="r,i in role" :key="r.id">
                                <td>{{ i+1 }}</td>
                                <td v-if="editId === r.id">
                                    <input type="text" v-model="editname" class="form-control">
                                </td>
                                <td v-else>{{ r.name }}</td>
                                <td class="text-nowrap" v-if="editId === r.id">
                                    <button class="btn btn-primary btn-sm me-2" @click="updateRole(editId)">
                                        <i
                                            class="fa fa-pencil text-white"
                                        ></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" @click="() => {editId = '';editname = ''}">
                                        cancel
                                    </button>
                                </td>
                                <td class="text-nowrap" v-else>
                                    <button class="btn" @click="setEditId(r.id,r.name)">
                                        <i
                                            class="fa fa-pencil text-black"
                                        ></i>
                                    </button>
                                    <button class="btn" @click="deleteRole(r.id)">
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
export default {
    data(){
        return {
            name: "",
            errors : "",
            loading : false,
            editId: '',
            editname: '',

        }
    },
    components : {Master},
     created(){
        this.$Progress.start();
    },
    mounted(){
        this.$Progress.finish();
    },
    computed : {
        role(){
            return this.$store.state.roles;
        }
    },
    methods: {
        setEditId(id,name){
            console.log(id)
            this.editId = id
            this.editname = name
        },
        async addRole(){
            const user = JSON.parse(localStorage.auth);
            this.loading = true;
            const formData = new FormData();
            formData.append('name',this.name)
            const res = await axios.post('/api/roles/create',formData,{
                headers: {
                        Authorization: `Bearer ${user.data.token}`,
                    },
            });
            const {data,success} = res.data
            if (success === false){
                this.errors = data
                this.loading = false;

            }else{
                this.errors = {};
                this.name = ""
                // this.$store.commit('toast',data)
                this.loading = false;
                this.$store.commit('toast',data);
                this.$store.dispatch('getRoles');
            }
        },
        async updateRole(id){
            const user = JSON.parse(localStorage.auth);
            this.loading = true;
            const formData = new FormData();
            formData.append('name',this.editname)
            const res = await axios.post(`/api/roles/${id}/update`,formData,{
                headers: {
                        Authorization: `Bearer ${user.data.token}`,
                    },
            });
            const {data,success} = res.data
            if (success === false){
                this.errors = data
                this.loading = false;

            }else{
                this.errors = {};
                this.editId = ""
                this.editname = ""
                // this.$store.commit('toast',data)
                this.loading = false;
                this.$store.commit('toast',data);
                this.$store.dispatch('getRoles');
            }
        },
        deleteRole(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Deleted successfully!',
                        'Role has been deleted.',
                        'success'
                    )
                    const user = JSON.parse(localStorage.auth);
                    const res = await axios.delete(`/api/roles/${id}/delete`,{
                            headers: {
                                Authorization: `Bearer ${user.data.token}`,
                            },
                        })
                    const {data,success} = res.data;
                    if(success){
                        this.$store.dispatch("getRoles")
                    }

                }
            })


        }
    }
}
</script>
