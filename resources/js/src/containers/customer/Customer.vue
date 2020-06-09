<template>
    <div>
        <Header>
            <div class="breadcrumb" v-if="header.service">
                <a href>Setup Account</a> > Chọn gói sử dụng
            </div>
            <div class="breadcrumb" v-if="header.name">
                Setup Account >
                <a href>Chọn gói sử dụng</a>
            </div>
        </Header>
        <router-view @header="setHeader($event)"></router-view>
        <Footer></Footer>
    </div>
</template>

<script>
import Header from "./Header";
import Footer from "./Footer";
export default {
    components: { Header, Footer },
    data() {
        return {
            header: {
                name: false,
                service: false
            }
        };
    },
    methods: {
        setHeader($event) {
            if ($event == "name") {
                this.header.name = true;
                this.header.service = false;
            } else {
                this.header.service = true;
                this.header.name = false;
            }
        }
    }
};
</script>

<style>
</style>
