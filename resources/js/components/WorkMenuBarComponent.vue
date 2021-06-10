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
                    <div><button @click.prevent="onClickFileSubDownloadText">テキストのみ(.json)</button></div>
                    <div><button @click.prevent="onClickFileSubDownloadAll">全部(.zip)</button></div>
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
                this.activeMainMenu = '';
                this.activeSubMenu = '';                
                
                console.log('onClickFileSubDownloadText');
                
                axios.get(window.laravel.asset + '/api/work-mount')
                    .then(response => {
                        console.log('axios.get');
                        
                        const stickerParams = response.data;
                        const stickerParamsJson = JSON.stringify(stickerParams, null, '\t');
                        
                        // ダウンロード
                        let blob = new Blob([stickerParamsJson], {type:"application/json"});
                        let link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);
                        link.download = 'stickynote.json';
        
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });
            },
            
            onClickFileSubDownloadAll: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';                
                
                console.log('onClickFileSubDownloadAll');
                
                axios.get(window.laravel.asset + '/api/work-mount')
                    .then(response => {
                        console.log('axios.get');
                        
                        const stickerParams = response.data;
                        this.downloadAll(stickerParams);
                    });
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
            
            //
            // 各種関数
            //            
            downloadAll: async function (stickerParams) {
                // JSON
                const stickerParamsJson = JSON.stringify(stickerParams, null, '\t');
                
                // 画像や動画のURL
                const sources = [];
                
                for (let stickerIndex = 0; stickerIndex < stickerParams.length; ++stickerIndex) {
                    const sticker = stickerParams[stickerIndex];
                    const contents = sticker.contents;
                    
                    for (let contentIndex = 0; contentIndex < contents.length; ++contentIndex) {
                        const content = contents[contentIndex];
                        if (content.link.item_type == 2) {  // app/Sticker.phpで値を定義してい
                            sources.push(content.item.image_url);
                        } else if (content.link.item_type == 3) {  // app/Sticker.phpで値を定義してい
                            sources.push(content.item.video_url);
                        }
                    }
                }
                
                // 画像や動画を収集する非同期リクエスト
                const itemPromises = sources.map(
                    (source) => {
                        const itemPromise = new Promise(
                            (resolve, reject) => {
                                const xhr = new XMLHttpRequest();
                                xhr.open('GET', source, true);
                                xhr.responseType = "blob";
                                
                                xhr.onload = function() {
                                    // resolveでデータとファイル名を渡す
                                    const fileName = source.slice(source.lastIndexOf("/") + 1);
                                    resolve({ data: this.response, fileName: fileName });
                                };
                                
                                // rejectだとawait Promise.allを抜けてしまうので、resolveでデータなしを渡す
                                xhr.onerror   = () => resolve({ data: null });
                                xhr.onabort   = () => resolve({ data: null });
                                xhr.ontimeout = () => resolve({ data: null });

                                xhr.send();
                            }
                        );
                        return itemPromise;
                    }
                );
                
                // 画像や動画を全て収集できるまで待つ
                const items = await Promise.all(itemPromises);
                
                // zip
                const zip = new JSZip();
                
                // フォルダ作成
                const folderName = "stickynote";
                const folder = zip.folder(folderName);
                
                // フォルダに画像や動画を追加
                items.forEach(item => {
                    if (item.data && item.fileName) {
                        folder.file(item.fileName, item.data);
                    }
                });
                
                // フォルダにJSONを追加
                folder.file('stickynote.json', stickerParamsJson);
                
                // zipを生成
                zip.generateAsync({ type: "blob" })
                .then(blob => {
                    // ダウンロード
                    let link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = 'stickynote.zip';
            
                    link.style.display = 'none';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
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