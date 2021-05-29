<template>
    <div class="color-rect-mount-class">
        <div
            v-for="(colorRect, index) in colorRects"
            v-bind:key="index"
            v-colorRectCustomDirective=colorRect
            class="color-rect-class"
        >
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                colorRects: [],
            };
        },
        
        mounted() {
            const targetElement = this.$el;
            console.log('mounted ', targetElement);
            
            axios.get(window.laravel.asset + '/api/color-rects')
                .then(response => {
                    this.colorRects = response.data;
                });
        },
        
        directives: {
            colorRectCustomDirective: {
                bind: function (el, binding) {
                    let colorHex = '000000' + binding.value.color.toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    console.log('bind ', el, binding);
                    console.log(binding.value.pos_top, binding.value.pos_left);
                    console.log(colorHex);
                    
                    el.style.top  = `${binding.value.pos_top}px`;
                    el.style.left = `${binding.value.pos_left}px`;
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                },
            },
        },
    }
</script>

<style scoped>
    .color-rect-mount-class {
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        background-color: #ff0000;
    }
    
    .color-rect-class {
        position: relative;
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
</style>
