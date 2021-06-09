<template>
    <div class="menu-bar-class">
        <div class="menu-bar-button-outer-class">
            <button @click.prevent="onClickMainFile">ファイル</button>
        </div>
        <div class="menu-bar-button-outer-class">
            <button @click.prevent="onClickMainInsert">挿入</button>
        </div>

        <!-- 背景 -->
        <div v-if="activeMainMenu !== ''">
            <div class="menu-bar-window-overlay-class">
            </div>
        </div>
        
        <!-- ファイル -->
        <div v-if="activeMainMenu === 'mainFile'">
            <div class="menu-bar-main-file-window-class">
                <div><button @click.prevent="onClickFileSubImport">インポート</button></div>
                <div><button @click.prevent="onClickFileSubDonwload">ダウンロード &gt;</button></div>
                <div><button @click.prevent="onClickFileSubClose">戻る</button></div>
            </div>

            <!-- ファイル > ダウンロード -->
            <div v-if="activeSubMenu === 'fileSubDownload'">
                <div class="menu-bar-file-sub-download-window-class">
                    <div><button @click.prevent="onClickFileSubDownloadText">テキストのみ : カンマ区切りの値 (.csv)</button></div>
                    <div><button @click.prevent="onClickFileSubDownloadCsv">全部 : カンマ区切りの値 (.csv) + 各ファイル</button></div>
                    <div><button @click.prevent="onClickFileSubDownloadClose">戻る</button></div>
                </div>
            </div>
        </div>
        
        <!-- 挿入 -->
        <div v-if="activeMainMenu === 'mainInsert'">
            <div class="menu-bar-main-insert-window-class">
                <div><button @click.prevent="onClickInsertSubSticker">ふせん</button></div>
                <div><button @click.prevent="onClickInsertSubClose">戻る</button></div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                activeMainMenu: '',
                activeSubMenu:  '',
            };
        },
        
        methods: {
            //
            // メイン
            //
            onClickMainFile: function (e) {
                if (this.activeMainMenu !== 'mainFile') {
                    this.activeMainMenu = 'mainFile';
                    this.activeSubMenu = '';
                }
            },
            
            onClickMainInsert: function (e) {
                if (this.activeSubMenu !== 'mainInsert') {
                    this.activeMainMenu = 'mainInsert';
                    this.activeSubMenu = '';
                }
            },
            
            //
            // ファイルサブ
            //
            onClickFileSubImport: function (e) {
                this.activeSubMenu = '';
                
                console.log('onClickFileSubImport');
            },
            
            onClickFileSubDonwload: function (e) {
                this.activeSubMenu = 'fileSubDownload';
            },
            
            onClickFileSubClose: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
            },
                
            //
            // ファイルサブのダウンロード
            //
            onClickFileSubDownloadText: function (e) {
                console.log('onClickFileSubDownloadText');
            },
            
            onClickFileSubDownloadCsv: function (e) {
                
            },
            
            onClickFileSubDownloadClose: function (e) {
                this.activeSubMenu = '';
            },
            
            //
            // 挿入サブ
            //
            onClickInsertSubSticker: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
                
                // ふせんを作成する
                console.log('axios.post');
                                
                const reqParam = {
                };
                
                axios.post(window.laravel.asset + '/api/work-sticker-create', {
                    reqParam: reqParam,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });
            },
            
            onClickInsertSubClose: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
            },
        },
    };
</script>

<style scoped>
    .menu-bar-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 30px;
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 40px 40px 2px;
        padding: 0;
    }
    
    .menu-bar-button-outer-class {
        display: inline-block;
    }
    
    .menu-bar-window-overlay-class {  /* 「menu-bar-classが付いた要素」の子の要素のクラス */
        position: absolute;
        left:   0;
        top:    30px;  /* メニューバーの下の位置 */
        width:  100%;  /* メニューバーの横幅は台紙の横幅と合わせてある */
        height: 920px;  /* だいたい『「メニューバーと台紙の間の距離」+「台紙の高さ」+「ボーダーの太さ」』くらい */
        z-index: 2000;  /* 台紙より上に表示される */
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    .menu-bar-main-file-window-class {
        position: absolute;
        left:   0;
        top:    30px;
        width:  120px;
        height: 90px;
        z-index: 2001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
    }
    
    .menu-bar-main-insert-window-class {
        position: absolute;
        left:   80px;
        top:    30px;
        width:  120px;
        height: 60px;
        z-index: 2001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
    }
    
    .menu-bar-file-sub-download-window-class {
        position: absolute;
        left:   120px;
        top:    60px;
        width:  300px;
        height: 90px;
        z-index: 2001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;        
    }
</style>