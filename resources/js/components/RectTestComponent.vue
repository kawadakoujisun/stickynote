<template>
    <div>
        <div class="el_contextArea" @click.left="onleftclick" @click.right.prevent="onrightclick">
            右クリックでコンテキストメニュー表示
        </div>
        <ul class="el_contextMenu el_contextMenu__colorful" :style="menu_position" v-show="show">
          <li><a href="#">menu1</a></li>
          <li><a href="#">menu2</a></li>
          <li><a href="#">menu3</a></li>
        </ul>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                menu_position: {
                    left: 0,
                    top:  0,
                },
                show: false,
            };
        },
        
        methods: {
            onleftclick: function () {
                this.show = false;
            },
            onrightclick: function (e) {
                this.menu_position = {
                    top:  `${e.pageY}px`,
                    left: `${e.pageX}px`
                    //top:  `${e.clientY}px`,
                    //left: `${e.clientX}px`
                };
                this.show = true;
            },
        },

    }
</script>
