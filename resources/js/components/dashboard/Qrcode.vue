<template>
    <Master>
        <nav
            style="
                --bs-breadcrumb-divider: url(
                    &#34;data:image/svg + xml,
                    %3Csvgxmlns='http://www.w3.org/2000/svg'width='8'height='8'%3E%3Cpathd='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z'fill='%236c757d'/%3E%3C/svg%3E&#34;
                );
            "
            aria-label="breadcrumb"
        >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <router-link :to="{ name: 'home' }">Home</router-link>
                </li>
                <li class="breadcrumb-item active" aria-current="page">URLs</li>
            </ol>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div
                                class="mb-3 d-flex justify-content-between align-items-center"
                            >
                                <div class="" v-if="!edit">
                                    <label for="no" class="form-label"
                                        >Create Users</label
                                    >
                                    <form
                                        @submit.prevent="addUser()"
                                        class="d-flex w-50"
                                    >
                                        <input
                                            type="number"
                                            class="form-control me-2"
                                            id="no"
                                            v-model="user_no"
                                        />
                                        <button
                                            class="btn btn-primary text-white"
                                            type="submit"
                                            :disabled="loading === true"
                                        >
                                            <div
                                                class="spinner-border me-2"
                                                role="status"
                                                style="
                                                    width: 20px;
                                                    height: 20px;
                                                "
                                                v-if="loading"
                                            >
                                                <span class="sr-only"
                                                    >Loading...</span
                                                >
                                            </div>
                                            <span v-else>Add</span>
                                        </button>
                                    </form>
                                </div>
                                <div>
                                    <button
                                        class="btn btn-danger"
                                        v-if="!edit"
                                        @click="() => (edit = true)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        class="btn btn-warning"
                                        v-else
                                        @click="
                                            () => {
                                                edit = false;
                                                ids = [];
                                            }
                                        "
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <div class="form-check my-3" v-if="edit">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    :checked="ids.length === url.length - 1"
                                    id="flexCheckDefault"
                                    @change="
                                        () => {
                                            ids.length === url.length - 1
                                                ? (ids = [])
                                                : url.map((u) =>
                                                      u.id !== 1 &&
                                                      !ids.includes(u.id)
                                                          ? ids.push(u.id)
                                                          : null
                                                  );
                                        }
                                    "
                                />
                                <label
                                    class="form-check-label"
                                    for="flexCheckDefault"
                                >
                                    Select All
                                </label>
                            </div>
                            <div class="overflow-scroll" style="height: 60vh">
                                <div v-for="(u, i) in url" :key="u.id">
                                    <div v-if="edit" class="d-flex">
                                        <div class="me-2">{{ i + 1 }}</div>
                                        <div class="form-check">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                @change="
                                                    () => {
                                                        ids.includes(u.id)
                                                            ? ids.splice(
                                                                  ids.indexOf(
                                                                      u.id
                                                                  ),
                                                                  1
                                                              )
                                                            : ids.push(u.id);
                                                    }
                                                "
                                                :checked="ids.includes(u.id)"
                                                :id="u.id"
                                                :disabled="u.id == 1"
                                            />
                                            <label
                                                class="form-check-label"
                                                :for="u.id"
                                            >
                                                {{
                                                    `${link}/api/login?voter_id=${u.voter_id}&password=${u.password}`
                                                }}
                                            </label>
                                        </div>
                                    </div>
                                    <a
                                        :href="`${link}/api/login?voter_id=${u.voter_id}&password=${u.password}`"
                                        v-else
                                    >
                                        <p>
                                            {{
                                                `${link}/api/login?voter_id=${u.voter_id}&password=${u.password}`
                                            }}
                                        </p>
                                    </a>
                                </div>
                            </div>
                            <button
                                class="btn btn-danger mt-2"
                                v-if="edit"
                                :class="ids.length === 0 ? 'disabled' : ''"
                                @click="() => deleteUser()"
                            >
                                <div
                                    class="spinner-border me-2"
                                    role="status"
                                    style="width: 20px; height: 20px"
                                    v-if="loading"
                                >
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <span>Delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Master>
</template>
<script>
import Master from "./layouts/Master.vue";
export default {
    data() {
        return {
            url: [],
            link: window.location.origin,
            user_no: "",
            loading: false,
            edit: false,
            ids: [],
        };
    },
    components: { Master },
    created() {
        console.log(window.location.origin);
        this.$Progress.start();
        const user = JSON.parse(localStorage.auth);
        axios
            .get("/api/users", {
                headers: {
                    Authorization: `Bearer ${user.data.token}`,
                },
            })
            .then((res) => {
                this.url = res.data;
            });
    },

    mounted() {
        this.$Progress.finish();
    },
    methods: {
        async addUser() {
            const user = JSON.parse(localStorage.auth);
            this.loading = true;
            const formData = new FormData();
            formData.append("no", this.user_no);
            const res = await axios.post("/api/users/create", formData, {
                headers: {
                    Authorization: `Bearer ${user.data.token}`,
                },
            });
            const { data, success } = res.data;
            if (success) {
                this.$store.commit("toast", data);
                this.loading = false;
                this.user_no = "";
                axios
                    .get("/api/users", {
                        headers: {
                            Authorization: `Bearer ${user.data.token}`,
                        },
                    })
                    .then((res) => {
                        this.url = res.data;
                    });
            } else console.log("error");
        },
        async deleteUser() {
            const user = JSON.parse(localStorage.auth);
            this.loading = true;
            const formData = new FormData();
            formData.append("ids", JSON.stringify(this.ids));
            const res = await axios.post("/api/users/delete", formData, {
                headers: {
                    Authorization: `Bearer ${user.data.token}`,
                },
            });
            const { data, success } = res.data;
            if (success) {
                this.$store.commit("toast", data);
                this.loading = false;
                this.edit = false;
                this.ids = [];
                axios
                    .get("/api/users", {
                        headers: {
                            Authorization: `Bearer ${user.data.token}`,
                        },
                    })
                    .then((res) => {
                        this.url = res.data;
                    });
                // this.loading = false;
            } else console.log("error");
        },
    },
};
</script>
