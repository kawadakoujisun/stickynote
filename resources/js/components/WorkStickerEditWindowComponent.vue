<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-edit-window-overlay-class"
            @click.self.prevent="onClickStickerEditWindowOverlay"
            @click.right.self.prevent="onClickStickerEditWindowOverlay"
        >
        </div> 
        
        <!-- ウィンドウ -->
        <div
            class="sticker-edit-window-class"
            id="sticker-edit-window-id"
            @click.self.prevent="onClickStickerEditWindow"
        >
            <div v-if="(stickerParam != null)">
                <div
                    v-sticker-custom-directive="{ stickerParam: stickerParam }"
                    class="sticker-edit-sticker-class"
                    id="sticker-id"
                >
                    <div
                        class="sticker-edit-sticker-inner-class"
                    >
                        <div
                            v-for="(content, index) in stickerParam.contents"
                            v-bind:key="index"
                            v-sticker-content-custom-directive="{ content: content, index: index }"
                        >
                            <div
                                class="sticker-edit-sticker-content-item-outer-class"
                            >
                            </div>
                            <div
                                class="sticker-edit-sticker-content-remove-button-outer-class"
                            >
                                <button class="btn btn-secondary" @click.prevent="onClickStickerContentRemove">削除</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="sticker-edit-button-space-class"></div>
            
            <div
                class="sticker-edit-buttons-outer-class"
            >
                <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickChangeColor">色を変更</button></p></div>
                <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickAddText">テキストを追加</button></p></div>
                <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickAddImage">画像を追加</button></p></div>
                <div><p><button class="btn btn-secondary btn-block" @click.prevent="onClickAddVideo">動画を追加</button></p></div>
                
                <div class="sticker-edit-button-space-class"></div>
                <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerEditWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerEditWindowProps.isShow,
                stickerParam: this.showStickerEditWindowProps.stickerParam,
                stickerEditWindowTimeoutId: null,
            };
        },
        
        watch: {
            'showStickerEditWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerEditWindowProps.isShow;
                this.stickerParam = this.showStickerEditWindowProps.stickerParam;
                
                if (this.isShow) {
                    const windowElem = document.getElementById("sticker-edit-window-id");
                    // いったん表示しないとサイズを取得できないので、最初は見えないところにおいておく。
                    windowElem.style.left = '-10000px';
                    windowElem.style.top  = 0;
                    
                    this.stickerEditWindowTimeoutId = setTimeout(() => {
                        const windowElemRect = windowElem.getBoundingClientRect();
                        windowElem.style.left = '50%';
                        windowElem.style.top  = '50%';

                        windowElem.style.marginLeft = `${-windowElemRect.width/2}px`;   // margin-left
                        windowElem.style.marginTop  = `${-windowElemRect.height/2}px`;  // margin-top
                    }, 10);
                }
            },
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    const stickerParam = binding.value.stickerParam;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                },
            },
            'sticker-content-custom-directive': {
                bind: function (el, binding) {
                    console.log('sticker-content-custom-directive bind');
                    
                    const content = binding.value.content;
                    
                    const idBaseName = 'content-link-id-';
                    el.id = `${idBaseName}${content['link'].id}`;
                  
                    const divItemOuterElems = el.getElementsByClassName('sticker-edit-sticker-content-item-outer-class');
                    
                    const divItemElem = document.createElement('div');
                    divItemOuterElems[0].appendChild(divItemElem);
                        
                    if (content['link'].item_type == 1) {  // app/Sticker.phpで値を定義している
                        divItemElem.classList.add('sticker-edit-sticker-content-item-text-outer-class');
                        const text = content['item']['text'];
                        divItemElem.innerText = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                    } else if (content['link'].item_type == 2) {  // app/Sticker.phpで値を定義している
                        divItemElem.classList.add('sticker-edit-sticker-content-item-image-outer-class');
                        const imageURL = content['item']['image_url'];
                        divItemElem.innerHTML = `<img class="sticker-edit-sticker-content-item-image-inner-class" src="${imageURL}">`;
                    } else if (content['link'].item_type == 3) {  // app/Sticker.phpで値を定義している
                        divItemElem.classList.add('sticker-edit-sticker-content-item-image-outer-class');
                        const videoURL = content['item']['video_url'];
                        divItemElem.innerHTML = `<video class="sticker-edit-sticker-content-item-image-inner-class" src="${videoURL}" controls autoplay loop></video>`;
                    }
                },
                
                inserted: function(el, binding) {
                    console.log('sticker-content-custom-directive inserted');
                },
                
                update: function (el, binding) {
                    console.log('sticker-content-custom-directive update');
                },
                
                componentUpdated: function (el, binding) {
                    console.log('sticker-content-custom-directive componentUpdated');
                },
            },
        },
                    
        methods: {
            onClickStickerEditWindowOverlay: function (e) {
                console.log('onClickStickerEditWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickStickerEditWindow: function (e) {
                console.log('onClickStickerEditWindow');
                // 何もしない
            },
            
            onClickStickerContentRemove: function (e) {
                // ContentItem〇〇を削除する
                console.log('onClickStickerContentRemove');
                
                const idBaseName = 'content-link-id-';
                
                let contentElem = null;
                let target = e.target;
                while (target) {
                    if (target.id) {
                        if (target.id.substr(0, idBaseName.length) == idBaseName) {
                            contentElem = target;
                            break;
                        }
                    }
                    target = target.parentElement;
                }
                
                if (contentElem) {
                    const contentLinkIdNo = contentElem.id.substr(idBaseName.length);
                    console.log(contentLinkIdNo);
                    
                    const contents = this.stickerParam.contents;  // JavaScriptの配列は参照渡し
                    
                    let contentItemType = null;
                    for (let i = 0; i < contents.length; ++i) {
                        if (contents[i].link.id == contentLinkIdNo) {
                            contentItemType = contents[i].link.item_type;
                            break;
                        }
                    }
                        
                    if (contentItemType !== null) {
                        console.log('axios.delete');
                                        
                        const reqParam = {
                            id: this.showStickerEditWindowProps.idNo,
                            content_link_id: contentLinkIdNo,
                        };
                        
                        let result = '';
                        
                        if (contentItemType == 1) {  // app/Sticker.phpで値を定義している
                            axios.delete(window.laravel.asset + '/api/work-sticker-content-item-text-destroy', {
                                data: {
                                    reqParam: reqParam,
                                    user_id: window.laravel.user['id'],
                                },
                            })
                                .then(response => {
                                    // 特にすることなし
                                });
                                
                            result = 'removeText';
                        } else if (contentItemType == 2) {  // app/Sticker.phpで値を定義している
                            axios.delete(window.laravel.asset + '/api/work-sticker-content-item-image-destroy', {
                                data: {
                                    reqParam: reqParam,
                                    user_id: window.laravel.user['id'],
                                },
                            })
                                .then(response => {
                                    // 特にすることなし
                                });
                                
                            result = 'removeImage';
                        } else if (contentItemType == 3) {  // app/Sticker.phpで値を定義している
                            axios.delete(window.laravel.asset + '/api/work-sticker-content-item-video-destroy', {
                                data: {
                                    reqParam: reqParam,
                                    user_id: window.laravel.user['id'],
                                },
                            })
                                .then(response => {
                                    // 特にすることなし
                                });
                                
                            result = 'removeVideo';
                        }
                            
                        // 親に戻る
                        const emitParam = {
                            event: e,
                            result: result,
                        };
                        this.backToMount(emitParam);
                    }
                }
            },

            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickChangeColor: function (e) {
                console.log('onClickChangeColor');
                const emitParam = {
                    event: e,
                    result: 'openStickerColorChangeWindow',
                };
                this.backToMount(emitParam);
            },
            
            onClickAddText: function (e) {
                console.log('onClickAddText');
                const emitParam = {
                    event: e,
                    result: 'openStickerTextAddWindow',
                };
                this.backToMount(emitParam);
            },
            
            onClickAddImage: function (e) {
                console.log('onClickAddImage');
                const emitParam = {
                    event: e,
                    result: 'openStickerImageAddWindow',
                };
                this.backToMount(emitParam);
            },

            onClickAddVideo: function (e) {
                console.log('onClickAddVideo');
                const emitParam = {
                    event: e,
                    result: 'openStickerVideoAddWindow',
                };
                this.backToMount(emitParam);
            },
            
            backToMount: function (emitParam) {
                if (this.stickerEditWindowTimeoutId !== null) {
                    clearTimeout(this.stickerEditWindowTimeoutId);
                    this.stickerEditWindowTimeoutId = null;
                }
                
                this.$emit('hide-sticker-edit-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-edit-window-overlay-class {
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
    .sticker-edit-window-class {
        position: fixed;
        /*left:   50%;
        top:    50%;*/
        left: 10px;
        top: 10px;
        min-width: 480px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #ffffff;
        padding: 10px;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
        
        /* 外部から変更するもの */
        margin-left: 0;
        margin-top:  0;
    }
    
    /*
     * ふせん(+削除ボタン)
     */
    .sticker-edit-sticker-class {
        position: relative;
        width:      440px;
        min-height: 200px;
        max-height: 430px;
        border: 1px solid #000;
        margin-left:  auto;
        margin-right: auto;
        padding: 0;
        overflow-y: scroll;
        
        /* 外部から変更するもの */
        background-color: #000000;
    }
    
    .sticker-edit-sticker-inner-class {
        width:      380px;
        min-height: 180px;
        max-height: 400px;
        margin: 10px auto 10px;
    }
    
    .sticker-edit-sticker-content-item-outer-class {
        display: inline-block;
        width: 280px;
        padding: 0;
        vertical-align: middle;
    }
    
    .sticker-edit-sticker-content-remove-button-outer-class {
        display: inline-block;
        width:  80px;
        padding: 0;
        vertical-align: middle;
        text-align: right;
    }
    
    /*
     * ボタン
     */
    .sticker-edit-buttons-outer-class {
        position: relative;
        width:      440px;
    }
    
    .sticker-edit-button-space-class {
        height: 10px;
    }
</style>

<style>
    /* TODO(kawadakoujisun): ::v-deepを試してみたい */

    /*
     * 画像(動画も)
     */
    .sticker-edit-sticker-content-item-image-outer-class {
        position: relative;
        width:  280px;
        height: 200px;
        margin: 0;
        padding: 0;
    }
    
    .sticker-edit-sticker-content-item-image-inner-class {
        position: absolute;
        left:         50%;
        top:          50%;
        margin-right: -50%;
        transform:    translate(-50%, -50%);
        width:      auto;
        height:     auto;
        max-width:  100%;
        max-height: 100%;
    }
    
    /*
     * テキスト
     */
    .sticker-edit-sticker-content-item-text-outer-class {
        position: relative;
        width:  280px;
        margin: 0;
        padding: 0;
    }
</style>