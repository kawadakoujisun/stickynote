<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-text-add-window-overlay-class"
            @click.self.prevent="onClickStickerTextAddWindowOverlay"
            @click.right.self.prevent="onClickStickerTextAddWindowOverlay"
        >
        </div>        
        
        <!-- ウィンドウ -->
        <div
            class="sticker-text-add-window-class"
            id="sticker-text-add-window-id"
            @click.self.prevent="onClickStickerTextAddWindow"
        >
            <form
                @submit.prevent="onClickAddText"
            >
                <textarea
                    v-model="addText"
                    cols="40"
                    rows="2"
                    class="form-control"
                    id="sticker-text-add-window-textarea-id"
                >
                </textarea>
                <div class="sticker-text-add-window-space-class"></div>
                <div><p><button type="submit" class="btn btn-secondary btn-block">追加</button></p></div>
            </form>

            <div class="sticker-text-add-window-space-class"></div>
            <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
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
                stickerTextAddWindowTimeoutId: null,
            };
        },
        
        watch: {
            'showStickerTextAddWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerTextAddWindowProps.isShow;
                
                if (this.isShow) {
                    // 前の入力が残っているので、消しておく。
                    this.addText = '';
                    // textareaの大きさが前にユーザーが変更したものになっているので、戻しておく。
                    const textareaElem = document.getElementById("sticker-text-add-window-textarea-id");
                    textareaElem.style.height = '';  // ユーザーが大きさを変更するとheightが追加されていた。
                    
                    const windowElem = document.getElementById("sticker-text-add-window-id");
                    // いったん表示しないとサイズを取得できないので、最初は見えないところにおいておく。
                    windowElem.style.left = '-10000px';
                    windowElem.style.top  = 0;
                    
                    this.stickerTextAddWindowTimeoutId = setTimeout(() => {
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
            onClickStickerTextAddWindowOverlay: function (e) {
                console.log('onClickStickerTextAddWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
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
                this.backToMount(emitParam);
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
                    this.backToMount(emitParam);
                }
            },
            
            backToMount: function (emitParam) {
                if (this.stickerTextAddWindowTimeoutId !== null) {
                    clearTimeout(this.stickerTextAddWindowTimeoutId);
                    this.stickerTextAddWindowTimeoutId = null;
                }
                
                this.$emit('hide-sticker-text-add-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-text-add-window-overlay-class {
        position: fixed;
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
    .sticker-text-add-window-class {
        position: fixed;
        left:   50%;
        top:    50%;
        min-width: 400px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #ffffff;
        padding: 10px;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
        
        /* 外部から変更するもの */
        margin-left: 0;
        margin-top:  0;
    }
    
    .sticker-text-add-window-space-class {
        height: 10px;
    }
</style>