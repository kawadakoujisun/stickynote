<template>
    <div
        v-show="isShow"
        class="sticker-text-add-window-overlay-class"
        @click.self.prevent="onClickStickerTextAddWindowOverlay"
    >
        <div
            class="sticker-text-add-window-class"
            id="sticker-text-add-window-id"
            @click.self.prevent="onClickStickerTextAddWindow"
        >
            <form
                @submit.prevent="onClickAddText"
            >
                <input
                    v-model="addText"
                    type="textarea"
                >
                <button type="submit">
                    追加
                </button>
            </form>

            <div><button @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerTextAddWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerTextAddWindowProps.isShow,
                addText: '',
            };
        },
        
        watch: {
            'showStickerTextAddWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerTextAddWindowProps.isShow;
            },
        },
        
        methods: {
            onClickStickerTextAddWindowOverlay: function (e) {
                console.log('onClickStickerTextAddWindowOverlay');
                // 何もしない
            },
            
            onClickStickerTextAddWindow: function (e) {
                console.log('onClickStickerTextAddWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-text-add-window-custom-event', emitParam);
            },
            
            onClickAddText: function (e) {
                // テキストを追加する
                const text = this.addText;
                this.addText = '';
                console.log('text=', text);
                
                if (text) {  // 「nullでない」かつ「空文字('')でない」とき
                    console.log('axios.post');
                                    
                    const reqParam = {
                        id: this.showStickerTextAddWindowProps.idNo,
                        text: text,
                    };
                    
                    axios.post(window.laravel.asset + '/api/work-sticker-content-item-text-create', {
                        reqParam: reqParam,
                        user_id: window.laravel.user['id'],
                    })
                        .then(response => {
                            // 特にすることなし
                        });
                
                    // 親に戻る
                    const emitParam = {
                        event: e,
                        result: 'addText',
                    };
                    this.$emit('hide-sticker-text-add-window-custom-event', emitParam);
                }
            },
        },
    };
</script>

<style scoped>
    .sticker-text-add-window-overlay-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    .sticker-text-add-window-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  400px;
        height: 200px;
        z-index: 1001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
    }
</style>