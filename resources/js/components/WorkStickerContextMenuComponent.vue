<template>
    <div
        v-show="isShow"
    >
        <!--
            背景はtopやleftの位置をずらしているので、
            コンテキストメニューを背景の子にするとずれてしまう。
            だから、背景とコンテキストメニューは並列にした。
        -->
        <!-- 背景 -->
        <div
            class="sticker-context-menu-overlay-class"
            @click.self.prevent="onClickStickerContextMenuOverlay"
            @click.right.self.prevent="onClickStickerContextMenuOverlay"
        >
        </div>

        <!-- コンテキストメニュー -->
        <div
            class="sticker-context-menu-class"
            id="sticker-content-menu-id"
            @click.self.prevent="onClickStickerContextMenu"
        >
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickEdit">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                編集
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div>
            <!--
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickIncreaseDepth">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                前面へ移動
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div>
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickDecreaseDepth">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                背面へ移動
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div>
            -->
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickChangeDepthFrontMost">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                最前面へ移動
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div>
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickChangeDepthBackMost">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                最背面へ移動
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div>
            <div class="sticker-context-menu-button-outer-class" @click.prevent="onClickDestroy">
                <span class="sticker-context-menu-button-inner-space-class"></span>
                削除
                <span class="sticker-context-menu-button-inner-space-class"></span>
            </div> 
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerContextMenuProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerContextMenuProps.isShow,
            };
        },
        
        watch: {
            'showStickerContextMenuProps.isShow': function(newValue, oldValue) {  // showStickerContextMenuProps: functionでは変更を検知できなかった。Objectの変更は検知できないのだろうか？
                this.isShow = this.showStickerContextMenuProps.isShow;
                
                if (this.isShow) {
                    const contextMenuElem = document.getElementById('sticker-content-menu-id');
                    contextMenuElem.style.top  = `${this.showStickerContextMenuProps.mountPos.y}px`;
                    contextMenuElem.style.left = `${this.showStickerContextMenuProps.mountPos.x}px`;
                }
            },
        },
        
        methods: {
            onClickStickerContextMenuOverlay: function (e) {
                console.log('onClickStickerContextMenuOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam); 
            },
            
            onClickStickerContextMenu: function (e) {
                console.log('onClickStickerContextMenu');
                // 何もしないでもいいのだが、コンテキストメニューを閉じることにする。
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam); 
            },
            
            onClickEdit: function (e) {
                console.log('onClickEdit');
                const emitParam = {
                    event: e,
                    result: 'openStickerEditWindow',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);
            },
            
            onClickIncreaseDepth: function (e) {
                // ふせんを前面へ移動する
                console.log('onClickIncreaseDepth');
                
                console.log('axios.put');
                                    
                const reqParam = {
                    id: this.showStickerContextMenuProps.idNo,
                    change_type : 'increment',
                    change_value: null,
                };
                
                axios.put(window.laravel.asset + '/api/work-sticker-info-item-depth-update', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });

                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'increaseDepth',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);
            },
            
            onClickDecreaseDepth: function (e) {
                // ふせんを背面へ移動する
                console.log('onClickDecreaseDepth');
                
                console.log('axios.put');
                                    
                const reqParam = {
                    id: this.showStickerContextMenuProps.idNo,
                    change_type : 'decrement',
                    change_value: null,
                };
                
                axios.put(window.laravel.asset + '/api/work-sticker-info-item-depth-update', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });

                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'decreaseDepth',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);
            },
            
            onClickChangeDepthFrontMost: function (e) {
                // ふせんを最前面へ移動する
                console.log('onClickChangeDepthFrontMost');
                
                console.log('axios.put');
                                    
                const reqParam = {
                    id: this.showStickerContextMenuProps.idNo,
                    change_type : 'frontMost',
                    change_value: null,
                };
                
                axios.put(window.laravel.asset + '/api/work-all-sticker-info-item-depth-update', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });

                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'changeDepthFrontMost',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);
            },
            
            onClickChangeDepthBackMost: function (e) {
                // ふせんを最背面へ移動する
                console.log('onClickChangeDepthBackMost');
                
                console.log('axios.put');
                                    
                const reqParam = {
                    id: this.showStickerContextMenuProps.idNo,
                    change_type : 'backMost',
                    change_value: null,
                };
                
                axios.put(window.laravel.asset + '/api/work-all-sticker-info-item-depth-update', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });

                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'changeDepthBackMost',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);                
            },
            
            onClickDestroy: function (e) {
                // ふせんを削除する
                console.log('onClickDestroy');
                
                console.log('axios.delete');
                                    
                const reqParam = {
                    id: this.showStickerContextMenuProps.idNo,
                };
                
                axios.delete(window.laravel.asset + '/api/work-sticker-destroy', {
                    data: {
                        reqParam: reqParam,
                        user_id: window.laravel.user['id'],
                    },
                })
                    .then(response => {
                        // 特にすることなし
                    });

                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'destroySticker',
                };
                this.$emit('hide-sticker-context-menu-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-context-menu-overlay-class {
        position: absolute;
        
        /*
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        */
        
        left:   -20px;
        top:    -60px;
        width:  1850px;
        height: 990px;
        
        z-index: 3000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    /*
     * コンテキストメニュー内のボタン
     */
    .sticker-context-menu-button-outer-class {
        width: 100%;
        height: 30px;
        background-color: #ffffff;
        margin: 0;
        line-height: 30px;
    }

    .sticker-context-menu-button-outer-class:hover {
        background-color: #eeeeee;
        cursor: pointer;
    }
    
    .sticker-context-menu-button-inner-space-class {
        display: inline-block;
        width: 10px;
    }
    
    /*
     * コンテキストメニューのウィンドウ
     */
    .sticker-context-menu-class {
        position: absolute;
        min-width: 150px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
        padding: 0;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
        
        /* 外部から変更するもの */
        top:  300;
        left: 300;
    }
</style>