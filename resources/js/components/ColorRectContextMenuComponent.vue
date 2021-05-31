<template>
    <div class="color-rect-context-menu-class" v-show="isShow" @click.left="onClickLeft">
        <p>context menu</p>
    </div>
</template>

<script>
    export default {
        props: {
            showProps: Object,
        },

        data() {
            return {
                isShow: this.showProps.isShow,
            };
        },
        
        watch: {
            'showProps.isShow': function(newValue, oldValue) {  // showProps: functionでは変更を検知できなかった。Objectの変更は検知できないのだろうか？
                console.log('newValue', newValue);
                console.log('oldvalue', oldValue);
                console.log('this', this.showProps);
                this.isShow = this.showProps.isShow;  
            },
        },
        
        methods: {
            onClickLeft: function (e) {
                const param = {
                    pagePos: {
                        x: e.pageX,
                        y: e.pageY,
                    },
                    event: e,
                };
                this.$emit('hide-context-menu-custom-event', param);
            }
        },
    };
</script>

<style scoped>
    .color-rect-context-menu-class {
        position: absolute;
        width:  150px;
        height: 300px;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
        
        /* 外部から変更するもの */
        top:  300;
        left: 300;
    }
</style>