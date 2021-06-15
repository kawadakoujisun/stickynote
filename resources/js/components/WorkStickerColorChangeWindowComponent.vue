<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-color-change-window-overlay-class"
            @click.self.prevent="onClickStickerColorChangeWindowOverlay"
            @click.right.self.prevent="onClickStickerColorChangeWindowOverlay"
        >
        </div>
        
        <!-- ウィンドウ -->
        <div
            class="sticker-color-change-window-class"
            id="sticker-color-change-window-id"
            @click.self.prevent="onClickStickerColorChangeWindow"
        >
            <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickColorRed">赤</button></p></div>
            <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickColorBlue">青</button></p></div>
            <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickColorYellow">黄色</button></p></div>
            <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickColorGreen">緑</button></p></div>
            <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickColorPink">ピンク</button></p></div>
            <div class="sticker-color-change-window-space-class"></div>
            <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
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
                stickerColorChangeWindowTimeoutId: null,
            };
        },
        
        watch: {
            'showStickerColorChangeWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerColorChangeWindowProps.isShow;
                
                if (this.isShow) {
                    const windowElem = document.getElementById("sticker-color-change-window-id");
                    // いったん表示しないとサイズを取得できないので、最初は見えないところにおいておく。
                    windowElem.style.left = '-10000px';
                    windowElem.style.top  = 0;
                    
                    this.stickerColorChangeWindowTimeoutId = setTimeout(() => {
                        const windowElemRect = windowElem.getBoundingClientRect();
                        windowElem.style.left = '50%';
                        windowElem.style.top  = '50%';

                        windowElem.style.marginLeft = `${-windowElemRect.width/2}px`;   // margin-left
                        windowElem.style.marginTop  = `${-windowElemRect.height/2}px`;  // margin-top
                    }, 10);
                }
            },
        },
        
        methods: {
            onClickStickerColorChangeWindowOverlay: function (e) {
                console.log('onClickStickerColorChangeWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
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
                this.backToMount(emitParam);
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
                this.backToMount(emitParam);
            },
            
            backToMount: function (emitParam) {
                if (this.stickerColorChangeWindowTimeoutId !== null) {
                    clearTimeout(this.stickerColorChangeWindowTimeoutId);
                    this.stickerColorChangeWindowTimeoutId = null;
                }
                
                this.$emit('hide-sticker-color-change-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-color-change-window-overlay-class {
        position: fixed;  /* TODO(kawadakoujisun): オーバーレイがこうなっていないところをこうして */
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 3000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    /*
     * ウィンドウ
     */
    .sticker-color-change-window-class {
        position: fixed;
        left:   50%;
        top:    50%;
        min-width: 150px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #ffffff;
        padding: 10px;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
        
        /* 外部から変更するもの */
        margin-left: 0;
        margin-top:  0;
    }
    
    .sticker-color-change-window-space-class {
        height: 20px;
    }
</style>