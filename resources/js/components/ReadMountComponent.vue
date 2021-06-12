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
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                //
                // 台紙に貼ってあるふせん
                //
                stickerParams: [],
            };
        },
        
        mounted() {
            axios.get(window.laravel.asset + '/api/work-mount')
                .then(response => {
                    console.log('axios.get');
                    
                    this.stickerParams = response.data;
                });
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    console.log('sticker-custom-directive bind', binding.value.index);
                    
                    const stickerParam = binding.value.stickerParam;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    el.style.top  = `${stickerParam['pos_top']}px`;
                    el.style.left = `${stickerParam['pos_left']}px`;
                    el.style.zIndex = stickerParam['depth'];  // z-index
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                    
                    // const idBaseName = this.getStickerIdBaseName();  // directives内はthisが使えない？
                    const idBaseName = 'sticker-id-';                   // 調べている時間がないので直書きしておく。
                    el.id = `${idBaseName}${stickerParam['id']}`;
                    
                    // const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                    const contentLinkIdBaseName = 'content-link-id-';  // 直書き
                            
                    const contents = stickerParam['contents'];
                    for (let i = 0; i < contents.length; ++i) {
                        const content = contents[i];
                        
                        const divItemElem = document.createElement('div');
                        divItemElem.id = `${contentLinkIdBaseName}${content['link'].id}`;
                        el.appendChild(divItemElem);
                        
                        if (content['link'].item_type == 1) {  // app/Sticker.phpで値を定義している
                            const text = content['item']['text'];
                            divItemElem.innerHTML = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                        } else if (content['link'].item_type == 2) {  // app/Sticker.phpで値を定義している
                            const imageURL = content['item']['image_url'];
                            divItemElem.innerHTML = `<img src="${imageURL}" width="200px">`;
                            // TODO(kawadakoujisun): https://techacademy.jp/my/frontend/frontend2/jquery
                            //     const img = new Image();をお手本にして画像を表示してみるか？
                        } else if (content['link'].item_type == 3) {  // app/Sticker.phpで値を定義している
                            const videoURL = content['item']['video_url'];
                            divItemElem.innerHTML = `<video src="${videoURL}" width="200px" controls autoplay loop></video>`;
                        }
                    }
                },
            },
        },
    };
</script>

<style scoped>
    .mount-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 900px;
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 0px 20px 20px;
        padding: 0;
    }
    
    .sticker-class {
        position: absolute;
        width:  400px;
        height: 400px;
        border: 1px solid #000;
        margin: 0;
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
</style>
