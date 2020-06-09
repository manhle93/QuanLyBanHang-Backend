<template>
    <main class="container">
        <aside>
            <img src="/images/customer/vector image 1.svg" alt />
            <p>Linh hoạt trên mọi trường hợp chấm công!</p>
        </aside>
        <section class="login">
            <form action>
                <div class="form-title">Đăng nhập</div>
                <div class="form-description">Chúc mừng! Vui lòng đăng nhập để sử dụng dịch vụ</div>
                <div class="form-group">
                    <label for>Email</label>
                    <input
                        type="email"
                        class="form-control"
                        placeholder="Nhập Email của bạn"
                        v-model="$v.form.email.$model"
                    />
                    <div class="form-feedback" v-if="$v.form.$dirty">
                        <span v-if="!$v.form.email.required">Bạn chưa nhập email</span>
                        <span v-if="!$v.form.email.email">Bạn hãy nhập một địa chỉ email</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for>
                        <div>
                            Mật khẩu
                            <a href>Quên mật khẩu?</a>
                        </div>
                    </label>
                    <input
                        type="password"
                        class="form-control"
                        placeholder="Nhập mật khẩu"
                        v-model="$v.form.password.$model"
                    />
                    <div class="form-feedback" v-if="$v.form.$dirty">
                        <span v-if="!$v.form.password.required">Bạn chưa nhập mật khẩu</span>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-action" @click.prevent="submit">Đăng nhập</button>
                </div>
                <div
                    class="form-feedback"
                    style="text-align:center;margin-top:5px"
                    v-if="error"
                >Sai địa chỉ email hoặc mật khẩu</div>
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
            <loading :active.sync="isLoading" :can-cancel="true" :is-full-page="true"></loading>
        </section>
    </main>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, email } from "vuelidate/lib/validators";
import Loading from "vue-loading-overlay";
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
                required
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
            error: false
        };
    },
    methods: {
        submit() {
            this.$v.form.$touch();
            if (this.$v.form.$anyError) {
                return;
            }
            this.isLoading = true;
            this.login();
        },
        async login() {
            try {
                let loginInfo = await axios.post("/auth/login", this.form);
                Auth.storeAfterLogin(loginInfo);
                this.$router.push("/");
            } catch (error) {
                if (error.response.status === 401) this.error = true;
                else if (error.response.status === 406)
                    this.$router.push({
                        name: "email-confirmation",
                        params: { user_id: error.response.data.user_id }
                    });
                else console.log(error);
                this.isLoading = false;
            }
        }
    }
};
</script>

<style>
</style>
