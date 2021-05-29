<template>
    <div id="color-rect-mount-id">
    </div>
</template>

<script>
    export default {
        mounted() {
            const targetElement = this.$el;
            console.log('mounted ', targetElement);
        },
    }
</script>

<style scoped>
    #color-rect-mount-id {
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        background-color: #ff0000;
    }
</style>
