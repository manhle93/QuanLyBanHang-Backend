<template>
    <main class="container">
        <div class="service">
            <div class="service-img">
                <img src="/images/customer/vector image 6.svg" alt />
            </div>
            <div class="service-name">Personal</div>
            <p class="service-description">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
                repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima
            </p>
            <div class="service-price">$19.00</div>
            <button class="btn btn-action" @click="setupOrganization(1)">Choose plan</button>
        </div>
        <div class="service">
            <div class="service-img">
                <img src="/images/customer/vector image 7.svg" alt />
            </div>
            <div class="service-name">Team</div>
            <p class="service-description">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
                repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima
            </p>
            <div class="service-price">$99.00</div>
            <button class="btn btn-action" @click="setupOrganization(2)">Choose plan</button>
        </div>
        <div class="service">
            <div class="service-img">
                <img src="/images/customer/vector image 8.svg" alt />
            </div>
            <div class="service-name">Corporate</div>
            <p class="service-description">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestiae error
                repudiandae magnam facere hic ipsa nihil, similique tenetur, tempore voluptatem minima
            </p>
            <div class="service-price">$199.00</div>
            <button class="btn btn-action" @click="setupOrganization(3)">Choose plan</button>
        </div>
    </main>
</template>

<script>
export default {
    props: ["form"],
    created() {
        this.$emit("header", "service");
        if (!this.form) {
            this.$router.push("/");
        }
    },
    methods: {
        async setupOrganization($id) {
            try {
                this.form["service_id"] = $id;
                let status = await axios.post("/auth/setup", this.form);
                localStorage.setItem("isSetup", true);
                this.$router.push("/");
            } catch (error) {
                console.log(error.response.data);
            }
        }
    }
};
</script>

<style>
</style>
