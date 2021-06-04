<template>
    <div
        v-show="isShow"
        class="sticker-edit-window-overlay-class"
        @click.self.prevent="onClickStickerEditWindowOverlay"
    >
        <div
            class="sticker-edit-window-class"
            id="sticker-edit-window-id"
            @click.self.prevent="onClickStickerEditWindow"
        >
            <div v-if="(stickerParam != null)">
                <div
                    v-sticker-custom-directive="{ stickerParam: stickerParam }"
                    class="sticker-class"
                    id="sticker-id"
                >
                    <div
                        v-for="(content, index) in stickerParam.contents"
                        v-bind:key="index"
                        v-sticker-content-custom-directive="{ content: content, index: index }"
                    >
                        <div
                            class="sticker-content-item-outer-class"
                        >
                        </div>
                        <div
                            class="sticker-content-remove-button-outer-class"
                        >
                            <button @click.prevent="onClickStickerContentRemove">削除</button>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="sticker-edit-buttons-outer-class"
            >
                <div><button @click.prevent="onClickClose">色を変更</button></div>
                <div><button @click.prevent="onClickClose">テキストを追加</button></div>
                <div><button @click.prevent="onClickClose">閉じる</button></div>
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
            };
        },
        
        watch: {
            'showStickerEditWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerEditWindowProps.isShow;
                this.stickerParam = this.showStickerEditWindowProps.stickerParam;
            },
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    const stickerParam = binding.value.stickerParam;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    el.style.top  = '20px';
                    el.style.left = '20px';
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                },
            },
            'sticker-content-custom-directive': {
                bind: function (el, binding) {
                    console.log('sticker-content-custom-directive bind');
                    
                    const content = binding.value.content;
                    
                    const idBaseName = 'content-link-id-';
                    el.id = `${idBaseName}${content['link'].id}`;
                  
                    const divItemOuterElems = el.getElementsByClassName('sticker-content-item-outer-class');
                    
                    if (content['link'].item_type == 1) {  // app/Sticker.phpで値を定義している
                        const text = content['item']['text'];
                        
                        const divTextElem = document.createElement('span');
                        divTextElem.innerHTML = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                        divItemOuterElems[0].appendChild(divTextElem);
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
                // 何もしない
            },
            
            onClickStickerEditWindow: function (e) {
                console.log('onClickStickerEditWindow');
                // 何もしない
            },
            
            onClickStickerContentRemove: function (e) {
                // テキストを削除する
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
                    const contentLinkId = contentElem.id.substr(idBaseName.length);
                    console.log(contentLinkId);
                    
                    console.log('axios.delete');
                                    
                    const reqParam = {
                        id: this.showStickerEditWindowProps.idNo,
                        content_link_id: contentLinkId,
                    };
                    
                    axios.delete(window.laravel.asset + '/api/work-sticker-content-item-text-destroy', {
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
                        result: 'removeText',
                    };
                    this.$emit('hide-sticker-edit-window-custom-event', emitParam);
                }
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-edit-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    .sticker-edit-window-overlay-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    .sticker-edit-window-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  400px;
        height: 400px;
        z-index: 1001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
    }
    
    .sticker-class {
        position: absolute;
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        margin: 0;
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
    
    .sticker-content-item-outer-class {
        display: inline-block;  /* 2つのdivを横に並べるには、2つともinline-blockにしておかなければならないようだ。 */
    }
    
    .sticker-content-remove-button-outer-class {
        display: inline-block;  /* 2つのdivを横に並べるには、2つともinline-blockにしておかなければならないようだ。 */
    }
    
    .sticker-edit-buttons-outer-class {
        position: absolute;
        top: 300px;
    }
</style>