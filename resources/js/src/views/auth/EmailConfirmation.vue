<template>
    <div>
        <main class="container" v-if="user_id">
            <aside>
                <img src="/images/customer/vector image 4.svg" style="width:410px" alt />
                <p>Xác nhận địa chỉ email của bạn!</p>
            </aside>
            <section class="email-confirmation">
                <form action>
                    <div class="form-title">Xác nhận email</div>
                    <div
                        class="form-description"
                    >Vui lòng kiểm tra email và click vào link để xác nhận tài khoản của bạn!</div>

                    <div class="form-group">
                        <button class="btn btn-action" @click.prevent="resend">Gửi lại email</button>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-social">
                            <span>Liên hệ hỗ trợ</span>
                        </button>
                    </div>
                </form>
            </section>
            <loading :active.sync="isLoading" :can-cancel="true" :is-full-page="true"></loading>
        </main>
    </div>
</template>

<script>
import Loading from "vue-loading-overlay";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
export default {
    components: { Loading },
    props: ["user_id"],
    data() {
        return {
            isLoading: false
        };
    },
    created() {
        if (!this.user_id) this.$router.push("/");
    },
    methods: {
        async resend() {
            try {
                let status = axios.get("/auth/resend/" + this.user_id);
                this.$swal.fire({
                    type: "success",
                    title: "Đã gửi lại email",
                    showConfirmButton: false,
                    timer: 1500
                    //toast: true
                });
            } catch (error) {
                console.log(error.response.data);
                this.isLoading = false;
            }
        }
    }
};
</script>

<style>
</style>
