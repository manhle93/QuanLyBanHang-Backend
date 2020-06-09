<template>
    <main class="container">
        <aside>
            <img src="/images/customer/vector image 5.svg" style="width:490px;margin-top:50px" alt />
        </aside>
        <section class="organization-information">
            <form action>
                <div class="form-title">Thông tin tổ chức</div>
                <div class="form-description">
                    Nhập tên cơ quan, tổ chức bạn quản lý, nhấn nút Tiếp theo để chuyển sang
                    phần chọn gói dịch vụ!
                </div>
                <div class="form-group">
                    <input
                        type="text"
                        class="form-control"
                        v-model="$v.form.name.$model"
                        placeholder="Nhập tên cơ quan, tổ chức"
                    />
                </div>
                <div class="form-feedback" v-if="$v.form.$dirty">
                    <span v-if="!$v.form.name.required">Bạn chưa nhập tên cơ quan tổ chức</span>
                </div>
                <div class="form-group">
                    <input
                        type="text"
                        class="form-control"
                        v-model="$v.form.code.$model"
                        placeholder="Nhập mã công ty(mã bao gồm 3 kí tự)"
                    />
                    <div class="form-feedback" v-if="$v.form.$dirty">
                        <span v-if="!$v.form.code.required">Bạn chưa nhập mã cơ quan, tổ chức</span>
                        <span v-if="!$v.form.code.minLength">Mã công ty bao gồm 3 kí tự</span>
                        <span v-if="!$v.form.code.maxLength">Mã công ty bao gồm 3 kí tự</span>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-action" @click.prevent="submit">Tiếp theo</button>
                </div>
            </form>
        </section>
    </main>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
export default {
    mixins: [validationMixin],
    validations: {
        form: {
            name: {
                required
            },
            code: {
                required,
                minLength: minLength(3),
                maxLength: maxLength(3)
            }
        }
    },
    data() {
        return {
            form: {
                name: "",
                code: ""
            }
        };
    },
    created() {
        this.$emit("header", "name");
        console.log(Auth.isSetup());
    },
    methods: {
        submit() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.$router.push({
                name: "services",
                params: { form: this.form }
            });
        }
    }
};
</script>

<style>
</style>
