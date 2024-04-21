<template>
    <div class="container-fluid">
        <div class="container box shadow p-5 mt-5">
            <h3 v-if="props.isEdit">Sửa thông tin khách hàng {{ client.name }}</h3>
            <h3 v-else>Thêm khách hàng đặc biệt</h3>
            <div class="row">
                <div class="col-6">
                    <label for="client-name" class="form-label">Tên khách hàng </label>
                    <input type="text" class="form-control" id="client-name" v-model="client.name">
                    <small v-if="errors.name" class="text-danger">{{ errors.name[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="client-phone" class="form-label">Số điện thoại: </label>
                    <input type="text" class="form-control" id="client-phone" v-model="client.phone_number"
                        @change="checkPhoneValid" />
                    <small v-if="errors.phone_number" class="text-danger">{{ errors.phone_number[0]
                        }}</small>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <label for="client-email" class="form-label">Email: </label>
                    <input type="email" class="form-control" id="client-email" v-model="client.email" />
                    <small v-if="errors.email" class="text-danger">{{ errors.email[0] }}</small>
                </div>
                <div class="col-6">
                    <label for="client-address" class="form-label">Địa chỉ: </label>
                    <input type="text" class="form-control" id="client-address" v-model="client.address" />
                    <small v-if="errors.address" class="text-danger">{{ errors.address[0] }}</small>
                </div>
            </div>
            <div class="row justify-content-center">
                <button v-if="props.isEdit" class="btn btn-primary col-2 mt-5" @click="updateClient">Cập nhật khách hàng</button>
                <button v-else class="btn btn-primary col-2 mt-5" @click="createClient">Thêm khách hàng</button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, reactive, onMounted } from "vue";
import { regexPhoneNumberVietNam } from "../../helpers/number";
import { useToast } from "vue-toastification";
import { createClientAPI, getClientAPI } from "../../api/admin/clients";
import { HttpStatusCode } from "axios";
import { useRoute, useRouter } from "vue-router";
const props = defineProps({
    isEdit: {
        type: Boolean,
        default: false
    }
})

const router = useRouter();
const route = useRoute();
const toast = useToast();
let errors = ref({});
let client = reactive({
    name: "",
    phone_number: "",
    email: "",
    address: ""
});

onMounted(async () => {
    if (props.isEdit) await getClient()
})

const checkPhoneValid = () => {
    let isValid = regexPhoneNumberVietNam(client.phone_number);
    if (!isValid) {
        errors.value.phone_number = ["Số điện thoại không hợp lệ"];
    } else {
        errors.value.phone_number = [];
    }
}

const createClient = async () => {
    let response = await createClientAPI(client);
    switch (response.status) {
        case HttpStatusCode.Ok:
            toast.success("Thêm khách hàng thành công!");
            router.push({ name: "admin-list-client" });
            break;
        case HttpStatusCode.UnprocessableEntity:
            errors.value = response.data.errors;
            break;
        default:
    }
}

const getClient = async () => {
    let response = await getClientAPI(route.params.clientId);
    client.address = response.data.address;
    client.email = response.data.email;
    client.name = response.data.name;
    client.phone_number = response.data.phone_number;
}

const updateClient = async () => {

}


</script>