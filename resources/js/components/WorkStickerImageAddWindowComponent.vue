<template>
    <div
        v-show="isShow"
        class="sticker-image-add-window-overlay-class"
        @click.self.prevent="onClickStickerImageAddWindowOverlay"
    >
        <div
            class="sticker-image-add-window-class"
            id="sticker-image-add-window-id"
            @click.self.prevent="onClickStickerImageAddWindow"
        >
            <img
                v-show="isImageFileEnabled"
                id="sticker-image-preview-id"
                src=""
                width="200px"
                height="200px"
            >

            <form
                enctype="multipart/form-data"
                @submit.prevent="onClickAddImage"
            >
                <div>
                    <input
                        type="file"
                        accept="image/jpeg, image/gif, image/png"
                        name="selectImageFile"
                        id="sticker-select-image-file-input-id"
                        @change="onSelectImageFile"
                    >
                </div>
                <div>
                    <button
                        v-bind:disabled="isImageFileEnabled == false"
                        type="submit"
                    >
                        追加
                    </button>
                </div>
            </form>

            <div><button @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerImageAddWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerImageAddWindowProps.isShow,
                
                isImageFileEnabled: false,
                
                selectImageFileInfo: null,
            };
        },
        
        watch: {
            'showStickerImageAddWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerImageAddWindowProps.isShow;
                
                // 前の入力が残っているので、消しておく。
                if (this.isShow) {
                    const imageElem = document.getElementById('sticker-image-preview-id');
                    imageElem.src = '';
                    this.isImageFileEnabled = false;
                    
                    const inputElem = document.getElementById('sticker-select-image-file-input-id');
                    inputElem.value = '';
                }
            },
        },
        
        methods: {
            onClickStickerImageAddWindowOverlay: function (e) {
                console.log('onClickStickerImageAddWindowOverlay');
                // 何もしない
            },
            
            onClickStickerImageAddWindow: function (e) {
                console.log('onClickStickerImageAddWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-image-add-window-custom-event', emitParam);
            },
            
            onSelectImageFile: function (e) {
                console.log('onSelectImageFile');
                
                // 選んだ画像のプレビュー
                
                // まずは非表示にする
                const imageElem = document.getElementById('sticker-image-preview-id');
                imageElem.src = '';
                this.isImageFileEnabled = false;
                
                // 画像を選んでいたら表示する
                if (e.target.files.length >= 1) {
                    const targetFile = e.target.files[0];
                    console.log(targetFile);
                    const targetFileType = targetFile.type;
                    
                    if ( targetFileType == 'image/jpeg'
                        || targetFileType == 'image/gif'
                        || targetFileType == 'image/png' ) {
                        const fileReader = new FileReader();
        
                        fileReader.onload = this.onLoadSelectImageFile;
                        
                        fileReader.readAsDataURL(targetFile);
                    }
                }
            },
            
            onClickAddImage: function (e) {
                console.log('onClickAddImage');
                
                const targetFile = e.target.elements.selectImageFile.files[0];
                console.log(targetFile);
                
                // 画像をアップロードし、ふせんに追加する
                console.log('axios.post');
                
                const reqParam = {
                    id: this.showStickerImageAddWindowProps.idNo,
                    selectImageFileInfo: this.selectImageFileInfo,
                };
                
                axios.post(window.laravel.asset + '/api/work-sticker-content-item-image-create', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });
                
                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'addImage',
                };
                this.$emit('hide-sticker-image-add-window-custom-event', emitParam);
            },
            
            onLoadSelectImageFile: function (e) {
                console.log('onLoadSelectImageFile');
                                
                // 画像を読み込み終わったので表示する
                const imageElem = document.getElementById('sticker-image-preview-id');
                imageElem.src = e.target.result;
                this.isImageFileEnabled = true;
                
                this.selectImageFileInfo = e.target.result;
                
                // console.log(this.selectImageFileInfo);
            },
        },
    };
</script>

<style scoped>
    .sticker-image-add-window-overlay-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    .sticker-image-add-window-class {
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
</style>