<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-individual-number-change-window-overlay-class"
            @click.self.prevent="onClickStickerIndividualNumberChangeWindowOverlay"
            @click.right.self.prevent="onClickStickerIndividualNumberChangeWindowOverlay"
        >
        </div>
        
        <!-- ウィンドウ -->
        <div
            class="sticker-individual-number-change-window-class"
            id="sticker-individual-number-change-window-id"
            @click.self.prevent="onClickStickerIndividualNumberChangeWindow"
        >
            <form
                @submit.prevent="onClickChangeIndividualNumber"
            >
                <div><input v-model="mainNumber" type="number" class="sticker-individual-number-change-input-5-digits-class"></div>
                <div class="text-muted text-center">（1～10000）</div>
                
                <div class="sticker-individual-number-change-window-space-class"></div>
                <div><p><button type="submit" class="btn btn-secondary btn-block">変更</button></p></div>                
            </form>
            
            <div class="sticker-individual-number-change-window-space-class"></div>
            <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerIndividualNumberChangeWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerIndividualNumberChangeWindowProps.isShow,
                mainNumber: '',
                subNumber:  '',
                stickerIndividualNumberChangeWindowTimeoutId: null,
            };
        },
        
        watch: {
            'showStickerIndividualNumberChangeWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerIndividualNumberChangeWindowProps.isShow;
                
                if (this.isShow) {
                    // 現在の個別番号を表示する。
                    this.mainNumber = this.showStickerIndividualNumberChangeWindowProps.mainNumber;
                    this.subNumber  = this.showStickerIndividualNumberChangeWindowProps.subNumber;
                    
                    const windowElem = document.getElementById("sticker-individual-number-change-window-id");
                    // いったん表示しないとサイズを取得できないので、最初は見えないところにおいておく。
                    windowElem.style.left = '-10000px';
                    windowElem.style.top  = 0;
                    
                    this.stickerIndividualNumberChangeWindowTimeoutId = setTimeout(() => {
                        const windowElemRect = windowElem.getBoundingClientRect();
                        windowElem.style.left = '50%';
                        windowElem.style.top  = '50%';

                        windowElem.style.marginLeft = `${-windowElemRect.width/2}px`;   // margin-left
                        windowElem.style.marginTop  = `${-windowElemRect.height/2}px`;  // margin-top
                    }, 10);
                        // TODO(kawadakoujisun): setTimeoutの代わりにthis.$nextTick(() => {});を使ってもいいかも。
                }
            },
        },
        
        methods: {
            onClickStickerIndividualNumberChangeWindowOverlay: function (e) {
                console.log('onClickStickerIndividualNumberChangeWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickStickerIndividualNumberChangeWindow: function (e) {
                console.log('onClickStickerIndividualNumberChangeWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickChangeIndividualNumber: function (e) {
                // 個別番号を変更する
                const mainNumberValue = parseInt(this.mainNumber, 10);
                
                // 入力が適切かどうかチェックする
                let inputOK = false;
                if ( Number.isInteger(mainNumberValue) ) {
                    if (1<=mainNumberValue && mainNumberValue<=10000) {
                        if (this.showStickerIndividualNumberChangeWindowProps.mainNumber != mainNumberValue) {
                            // 値が変更されていたら
                            inputOK = true;
                        }
                    }
                }
                
                if (inputOK) {
                    console.log('axios.put');
                                    
                    const reqParam = {
                        id: this.showStickerIndividualNumberChangeWindowProps.idNo,
                        mainNumber: mainNumberValue,
                    };
                    
                    axios.put(window.laravel.asset + '/api/work-all-sticker-info-item-individual-number-update', {
                        reqParam: reqParam,
                        user_id: window.laravel.user['id'],
                    })
                        .then(response => {
                            // 特にすることなし
                        });
                    
                    // 親に戻る
                    const emitParam = {
                        event: e,
                        result: 'changeIndividualNumber',
                    };
                    this.backToMount(emitParam);
                }
            },
            
            backToMount: function (emitParam) {
                if (this.stickerIndividualNumberChangeWindowTimeoutId !== null) {
                    clearTimeout(this.stickerIndividualNumberChangeWindowTimeoutId);
                    this.stickerIndividualNumberChangeWindowTimeoutId = null;
                }
                
                this.$emit('hide-sticker-individual-number-change-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-individual-number-change-window-overlay-class {
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
    .sticker-individual-number-change-window-class {
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
    
    .sticker-individual-number-change-window-space-class {
        height: 10px;
    }
    
    /*
     * 入力欄
     */
    .sticker-individual-number-change-input-5-digits-class {
        width: 155px;  /* 125 + 30 */
    }
</style>