<template>
    <main class="container">
        <aside>
            <img src="/images/customer/vector image 1.svg" alt />
            <p>Linh hoạt trên mọi trường hợp chấm công!</p>
        </aside>
        <section class="register">
            <form action>
                <div class="form-title">Đăng ký</div>
                <div
                    class="form-description"
                >Miễn phí trải nghiệm 05 ngày cho việc quản lý nhân sự trở nên dễ dàng hơn</div>
                <div class="form-group">
                    <label for>
                        Email
                        <span class="required-field">(*)</span>
                    </label>
                    <input
                        type="email"
                        class="form-control"
                        v-model="$v.form.email.$model"
                        placeholder="Nhập Email của bạn"
                    />
                    <div class="form-feedback" v-if="$v.form.$dirty">
                        <span v-if="!$v.form.email.required">Bạn chưa nhập email</span>
                        <span v-if="!$v.form.email.email">Bạn hãy nhập một địa chỉ email</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for>
                        Mật khẩu
                        <span class="required-field">(*)</span>
                    </label>
                    <input
                        type="password"
                        class="form-control"
                        placeholder="Nhập mật khẩu"
                        v-model="$v.form.password.$model"
                    />
                    <div class="form-feedback" v-if="$v.form.$dirty">
                        <span v-if="!$v.form.password.required">Bạn chưa nhập mật khẩu</span>
                        <span
                            v-if="!$v.form.password.minLength"
                        >Mật khẩu phải có độ dài tối thiểu 8 kí tự</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for>Họ và tên</label>
                    <input
                        type="email"
                        class="form-control"
                        v-model="form.name"
                        placeholder="Nhập họ tên của bạn"
                    />
                </div>
                <div class="form-group">
                    <label for>Số điện thoại</label>
                    <input
                        type="email"
                        class="form-control"
                        v-model="form.phone"
                        placeholder="Nhập Email của bạn"
                    />
                </div>
                <div class="form-group">
                    <p>
                        Bằng việc nhấn vào nút
                        <b>Tạo tài khoản</b> dưới đây, bạn đã đồng ý các chính sách và điều
                        khoản sử dụng hệ thống của chúng tôi.
                    </p>
                </div>
                <div class="form-group">
                    <button class="btn btn-action" @click.prevent="submit">Tạo tài khoản</button>
                </div>
                <div
                    class="form-feedback"
                    style="text-align:center;margin-top:5px"
                    v-if="errorEmail"
                >Địa chỉ email đã tồn tại</div>
                <div class="divider">
                    <hr />
                    <span>Hoặc</span>
                </div>
                <div class="form-group">
                    <button class="btn btn-social btn-fb">
                        <img src="/images/customer/icon-fb.png" alt />
                        <span>Đăng kí bằng tài khoản Facebook</span>
                    </button>
                </div>
                <div class="form-group">
                    <button class="btn btn-social btn-gg">
                        <img src="/images/customer/icon-gg.png" alt />
                        <span>Đăng kí bằng tài khoản Google</span>
                    </button>
                </div>
                <div class="register-link">
                    Bạn không có tài khoản?
                    <router-link to="/customer/register">Đăng kí tài khoản</router-link>
                </div>
            </form>
        </section>
        <loading :active.sync="isLoading" :can-cancel="true" :is-full-page="true"></loading>
    </main>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, email, minLength } from "vuelidate/lib/validators";
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
export default {
    mixins: [validationMixin],
    components: { Loading },
    validations: {
        form: {
            email: {
                required,
                email
            },
            password: {
                required,
                minLength: minLength(8)
            }
        }
    },

    data() {
        return {
            isLoading: false,
            form: {
                email: "",
                password: "",
                name: "",
                phone: ""
            },
            errorEmail: false
        };
    },
    methods: {
        submit() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.register();
        },
        async register() {
            this.isLoading = true;
            try {
                let registerInfo = await axios
                    .post("/auth/register", this.form)
                    .then();
                this.isLoading = false;
                this.$router.push({
                    name: "email-confirmation",
                    params: { user_id: registerInfo.data.user_id }
                });
            } catch (error) {
                if (error.response.status === 422) {
                    if (error.response.data.errors.email) {
                        this.errorEmail = true;
                    }
                } else console.log(error);
                this.isLoading = false;
            }
        }
    }
};
</script>

<style>
</style>
