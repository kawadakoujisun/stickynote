<template>
    <div
        v-show="isShow"
        class="sticker-color-change-window-overlay-class"
        @click.self.prevent="onClickStickerColorChangeWindowOverlay"
    >
        <div
            class="sticker-color-change-window-class"
            id="sticker-color-change-window-id"
            @click.self.prevent="onClickStickerColorChangeWindow"
        >
            <div><button @click.prevent="onClickColorRed">赤</button></div>
            <div><button @click.prevent="onClickColorBlue">青</button></div>
            <div><button @click.prevent="onClickColorYellow">黄色</button></div>
            <div><button @click.prevent="onClickColorGreen">緑</button></div>
            <div><button @click.prevent="onClickColorPink">ピンク</button></div>
            <div><button @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerColorChangeWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerColorChangeWindowProps.isShow,
            };
        },
        
        watch: {
            'showStickerColorChangeWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerColorChangeWindowProps.isShow;
            },
        },
        
        methods: {
            onClickStickerColorChangeWindowOverlay: function (e) {
                console.log('onClickStickerColorChangeWindowOverlay');
                // 何もしない
            },
            
            onClickStickerColorChangeWindow: function (e) {
                console.log('onClickStickerColorChangeWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-color-change-window-custom-event', emitParam);
            },
            
            onClickColorRed: function (e) {
                this.changeColor(e, 0xffaaaa);
            },
            onClickColorBlue: function (e) {
                this.changeColor(e, 0xaaaaff);
            },
            onClickColorYellow: function (e) {
                this.changeColor(e, 0xffffaa);
            },
            onClickColorGreen: function (e) {
                this.changeColor(e, 0xaaffaa);
            },
            onClickColorPink: function (e) {
                this.changeColor(e, 0xffaaff);
            },
            
            changeColor: function (e, color) {
                // 色を変更する
                console.log('axios.put');
                
                const reqParam = {
                    id: this.showStickerColorChangeWindowProps.idNo,
                    color: color, 
                };
                
                axios.put(window.laravel.asset + '/api/work-sticker-info-item-color-update', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });
                
                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'changeColor',
                };
                this.$emit('hide-sticker-color-change-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    .sticker-color-change-window-overlay-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    .sticker-color-change-window-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  150px;
        height: 300px;
        z-index: 1001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
    }
</style>