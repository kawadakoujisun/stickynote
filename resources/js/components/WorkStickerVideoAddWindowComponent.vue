<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-video-add-window-overlay-class"
            @click.self.prevent="onClickStickerVideoAddWindowOverlay"
            @click.right.self.prevent="onClickStickerVideoAddWindowOverlay"
        >
        </div> 
        
        <!-- ウィンドウ -->
        <div
            class="sticker-video-add-window-class"
            id="sticker-video-add-window-id"
            @click.self.prevent="onClickStickerVideoAddWindow"
        >
            <div class="sticker-video-add-window-video-outer-class">
                <video
                    v-show="isVideoFileEnabled"
                    class="sticker-video-add-window-video-inner-class"
                    id="sticker-video-preview-id"
                    src=""
                    controls
                    autoplay
                    loop
                >
                </video>
            </div>

            <div class="sticker-video-add-window-space-class"></div>
            
            <form
                enctype="multipart/form-data"
                @submit.prevent="onClickAddVideo"
            >
                <div>
                    <input
                        type="file"
                        accept="video/mp4, video/ogg, video/webm"
                        name="selectVideoFile"
                        class="form-control"
                        id="sticker-select-video-file-input-id"
                        @change="onSelectVideoFile"
                    >
                </div>
                
                <div class="sticker-video-add-window-space-class"></div>
                
                <div>
                    <p>
                        <button
                            v-bind:disabled="isVideoFileEnabled == false"
                            type="submit"
                            class="btn btn-secondary btn-block"
                        >
                            追加
                        </button>
                    </p>
                </div>
            </form>

            <div class="sticker-video-add-window-space-class"></div>
            
            <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerVideoAddWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerVideoAddWindowProps.isShow,
                
                isVideoFileEnabled: false,
                
                selectVideoFileInfo: null,
            };
        },
        
        watch: {
            'showStickerVideoAddWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerVideoAddWindowProps.isShow;
                
                // 前の入力が残っているので、消しておく。
                if (this.isShow) {
                    const videoElem = document.getElementById('sticker-video-preview-id');
                    videoElem.src = '';
                    this.isVideoFileEnabled = false;
                    
                    const inputElem = document.getElementById('sticker-select-video-file-input-id');
                    inputElem.value = '';
                }
            },
        },
        
        methods: {
            onClickStickerVideoAddWindowOverlay: function (e) {
                console.log('onClickStickerVideoAddWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-video-add-window-custom-event', emitParam);
            },
            
            onClickStickerVideoAddWindow: function (e) {
                console.log('onClickStickerVideoAddWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.$emit('hide-sticker-video-add-window-custom-event', emitParam);
            },
            
            onSelectVideoFile: function (e) {
                console.log('onSelectVideoFile');
                
                // 選んだ画像のプレビュー
                
                // まずは非表示にする
                const videoElem = document.getElementById('sticker-video-preview-id');
                videoElem.src = '';
                this.isVideoFileEnabled = false;
                
                // 画像を選んでいたら表示する
                if (e.target.files.length >= 1) {
                    const targetFile = e.target.files[0];
                    console.log(targetFile);
                    const targetFileType = targetFile.type;
                    
                    if ( targetFileType == 'video/mp4'
                        || targetFileType == 'video/ogg'
                        || targetFileType == 'video/webm' ) {
                        const fileReader = new FileReader();
        
                        fileReader.onload = this.onLoadSelectVideoFile;
                        
                        fileReader.readAsDataURL(targetFile);
                    }
                }
            },
            
            onClickAddVideo: function (e) {
                console.log('onClickAddVideo');
                
                const targetFile = e.target.elements.selectVideoFile.files[0];
                console.log(targetFile);
                
                // 画像をアップロードし、ふせんに追加する
                console.log('axios.post');
                
                const reqParam = {
                    id: this.showStickerVideoAddWindowProps.idNo,
                    selectVideoFileInfo: this.selectVideoFileInfo,
                };
                
                axios.post(window.laravel.asset + '/api/work-sticker-content-item-video-create', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });
                
                // 親に戻る
                const emitParam = {
                    event: e,
                    result: 'addVideo',
                };
                this.$emit('hide-sticker-video-add-window-custom-event', emitParam);
            },
            
            onLoadSelectVideoFile: function (e) {
                console.log('onLoadSelectVideoFile');
                                
                // 画像を読み込み終わったので表示する
                const videoElem = document.getElementById('sticker-video-preview-id');
                videoElem.src = e.target.result;
                this.isVideoFileEnabled = true;
                
                this.selectVideoFileInfo = e.target.result;
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-video-add-window-overlay-class {
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
    .sticker-video-add-window-class {
        position: fixed;
        left:         50%;
        top:          50%;
        margin-right: -50%;
        transform:    translate(-50%, -50%);
        width:  400px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #ffffff;
        padding: 10px;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
    }
    
    .sticker-video-add-window-video-outer-class {
        position: relative;
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        background-color: #cccccc;
        margin-left:  auto;
        margin-right: auto;
        padding: 0;
    }
    
    .sticker-video-add-window-video-inner-class {
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
    
    .sticker-video-add-window-space-class {
        height: 10px;
    }
</style>