<template>
    <div
        class="mount-class"
    >
        <div
            v-for="(stickerParam, index) in stickerParams"
            v-bind:key="stickerParam.id"
            v-sticker-custom-directive="{ stickerParam: stickerParam, index: index }"
            class="sticker-class"
        >
            <div
                class="sticker-inner-class"
            >
            </div>
        </div>
    </div>
</template>

<script>
    import commonScript from '../ProjectWorkCommonScript.js';
    
    export default {
        components: {
            commonScript,    
        },
        
        data() {
            return {
                //
                // 台紙に貼ってあるふせん
                //
                stickerParams: [],
                
                lastWindowInnerWidth: null,
            };
        },
        
        mounted() {
            axios.get(window.laravel.asset + '/api/work-mount')
                .then(response => {
                    console.log('axios.get');
                    
                    this.stickerParams = response.data;
                });
                
            window.onresize = () => {
                let needRelocate = false;
                if (this.lastWindowInnerWidth === null) {
                    needRelocate = true;
                } else {
                    if (this.lastWindowInnerWidth >= 576 && window.innerWidth < 576) {
                        needRelocate = true;
                    } else if (this.lastWindowInnerWidth < 576 && window.innerWidth >= 576) {
                        needRelocate = true;
                    }
                }
                
                if (needRelocate) {
                    // 配置し直し
                    if (window.innerWidth >= 576) {
                        // 画面の横幅が576px以上のとき
                        for (let stickerParam of this.stickerParams) {
                            const idBaseName = this.getStickerIdBaseName();
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = `${stickerParam['pos_top']}px`;
                                updateElem.style.left = `${stickerParam['pos_left']}px`;
                            }
                        }
                    } else {
                        for (let stickerParam of this.stickerParams) {
                            const idBaseName = this.getStickerIdBaseName();
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = 0;
                                updateElem.style.left = 0;
                            }
                        }    
                    }
                }
                
                this.lastWindowInnerWidth = window.innerWidth;
            };
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    console.log('sticker-custom-directive bind', binding.value.index);
                    
                    const stickerParam = binding.value.stickerParam;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    if (window.innerWidth >= 576) {
                    // 画面の横幅が576px以上のとき
                    el.style.top  = `${stickerParam['pos_top']}px`;
                    el.style.left = `${stickerParam['pos_left']}px`;
                    }
                    el.style.zIndex = stickerParam['depth'];  // z-index
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                    
                    // const idBaseName = this.getStickerIdBaseName();  // directives内はthisが使えない？
                    const idBaseName = 'sticker-id-';                   // 調べている時間がないので直書きしておく。
                    el.id = `${idBaseName}${stickerParam['id']}`;
                    
                    const divStickerInnerElems = el.getElementsByClassName('sticker-inner-class');
                    const divStickerInnerElem = divStickerInnerElems[0];
                    
                    // const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                    const contentLinkIdBaseName = 'content-link-id-';  // 直書き
                            
                    const contents = stickerParam['contents'];
                    for (let i = 0; i < contents.length; ++i) {
                        const content = contents[i];
                        
                        const divItemElem = document.createElement('div');
                        divItemElem.id = `${contentLinkIdBaseName}${content['link'].id}`;
                        divStickerInnerElem.appendChild(divItemElem);
                        
                        if (content['link'].item_type == 1) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-text-outer-class');
                            const text = content['item']['text'];
                            divItemElem.innerText = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                        } else if (content['link'].item_type == 2) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-image-outer-class');
                            const imageURL = content['item']['image_url'];
                            // divItemElem.innerHTML = `<img class="sticker-content-item-image-inner-class" src="${imageURL}">`;
                            
                            // img要素追加
                            commonScript.addImageElement(divItemElem, imageURL, 1);
                        } else if (content['link'].item_type == 3) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-image-outer-class');
                            const videoURL = content['item']['video_url'];
                            // divItemElem.innerHTML = `<video class="sticker-content-item-image-inner-class" src="${videoURL}" controls autoplay loop></video>`;
                            
                            // video要素追加
                            commonScript.addVideoElement(divItemElem, videoURL, 1);
                        }
                    }
                },
                
                inserted: function(el, binding) {
                    console.log('sticker-custom-directive inserted', binding.value.index);
                },
                
                update: function (el, binding) {
                    console.log('sticker-custom-directive update', binding.value.index);
                },
                
                componentUpdated: function (el, binding) {
                    console.log('sticker-custom-directive componentUpdated', binding.value.index);
                },
            },
        },
        
        methods: {
            getStickerIdBaseName: function () {
                return 'sticker-id-';
            },
        },
    };
</script>

<style scoped>
    /*
     * 台紙
     */
    .mount-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  100%;
        /*height: 900px;*/
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 0px auto 20px;
        padding: 0;
    }
    
    @media (min-width: 576px) {
    /* 画面の横幅が576px以上のとき */
    .mount-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 900px;
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 0px 20px 20px;
        padding: 0;
    }
    }
    
    /*
     * ふせん
     */
    .sticker-class {
        position: relative;
        width:      340px;
        min-height: 200px;
        max-height: 430px;
        border: 1px solid #000;
        margin: 10px auto 10px;
        padding: 0;
        overflow-y: scroll;        
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
    
    @media (min-width: 576px) {
    /* 画面の横幅が576px以上のとき */
    .sticker-class {
        position: absolute;
        width:      340px;
        min-height: 200px;
        max-height: 430px;
        border: 1px solid #000;
        margin: 0;
        padding: 0;
        overflow-y: scroll;        
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
    }

    .sticker-inner-class {
        width:      280px;
        min-height: 180px;
        max-height: 400px;
        margin: 10px auto 10px;
    }
    
    /*
     * 画像(動画も)
     */
    .sticker-inner-class ::v-deep .sticker-content-item-image-outer-class {
        position: relative;
        width:  280px;
        height: 200px;
        margin: 0;
        padding: 0;
    }
    
    .sticker-inner-class ::v-deep .sticker-content-item-image-inner-class {
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
    .sticker-inner-class ::v-deep .sticker-content-item-text-outer-class {
        position: relative;
        width:  280px;
        margin: 0;
        padding: 0;
    }
</style>
