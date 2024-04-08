<template>
    <div class="login-page bg-light min-vh-100">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-5 offset-lg-1">
                    <div class="bg-white shadow rounded">
                        <div class="form-left h-100 py-5 px-5">
                            <h3 class="mb-3">HGO Admin Login</h3>
                            <form action="" class="row g-4">
                                <div class="col-12">
                                    <label>Email<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-person-fill"></i></div>
                                        <input v-model="credentials.email" type="email" class="form-control"
                                            placeholder="Enter Email">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label>Password<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="bi bi-lock-fill"></i></div>
                                        <input type="password" v-model="credentials.password" class="form-control"
                                        placeholder="Enter Password">
                                    </div>
                                    <small v-if="errors.credentials" class="text-danger m-0 p-0">{{ errors.credentials }}</small>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="inlineFormCheck"
                                            v-model="credentials.remember_me">
                                        <label class="form-check-label" for="inlineFormCheck">Remember me</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <a href="#" class="float-end text-primary">Forgot Password?</a>
                                </div>
                                <div class="col-12">
                                    <button @click.prevent="login()"
                                        class="btn btn-primary px-4 float-end mt-4 text-white">Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <p class="text-end text-secondary mt-3">Hoguom Opera</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { loginAPI } from "../../api/admin/auth";
import { useRouter } from "vue-router";
import { BAD_REQUEST, HTTP_SUCCESS } from "../../config/const";
import { useAuthenticateStore } from "../../pinia.ts";

const authenticatedStore = useAuthenticateStore();
let credentials = reactive({
    email: null,
    password: null,
    remember_me: false
});
let errors = reactive({});
let router = useRouter();
const login = async () => {
    let result = await loginAPI(credentials);
    if (result.status == BAD_REQUEST) errors.credentials = result.data.message;
    if (result.status == HTTP_SUCCESS) {
        authenticatedStore.setAdminLoggedIn();
        router.push({ name: "admin-list-events" })
    };
}
</script>