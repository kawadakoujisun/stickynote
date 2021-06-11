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
                <div><button @click.prevent="onClickFileSubImport">インポート &gt;</button></div>
                <div><button @click.prevent="onClickFileSubDonwload">ダウンロード &gt;</button></div>
                <div><button @click.prevent="onClickFileSubClose">戻る</button></div>
            </div>

            <!-- ファイル > インポート -->
            <div v-if="activeSubMenu === 'fileSubImport'">
                <div class="menu-bar-file-sub-import-window-class">
                    <div><button @click.prevent="onClickFileSubImportText">テキストのみ(.json)</button></div>
                    <div><button @click.prevent="onClickFileSubImportAll">全部(.zip)</button></div>
                    <div><button @click.prevent="onClickFileSubImportClose">戻る</button></div>
                </div>
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
                this.activeSubMenu = 'fileSubImport';
            },
            
            onClickFileSubDonwload: function (e) {
                this.activeSubMenu = 'fileSubDownload';
            },
            
            onClickFileSubClose: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
            },

            //
            // ファイルサブのインポート
            //
            onClickFileSubImportText: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
                
                console.log('onClickFileSubImportText');
                
                // HTMLのinput要素を生成
                const input = document.createElement("input");
                input.type = "file";
                input.accept = "application/json";
                
                // ファイルが指定された後に行う処理
                input.addEventListener("change", e => {
                    const file = e.target.files[0];
                    
                    if (file) {
                        console.log(file.name);  // ファイル名
                        
                        const reader = new FileReader();
                        // ファイルの非同期読み込み
                        reader.readAsText(file);

                        // ファイルが読み込まれた後に行う処理
                        reader.addEventListener("load", () => {
                            // 読み込んだJSONをJavaScriptのオブジェクトに変換する
                            const stickyNoteData = JSON.parse(reader.result);
                            console.log(stickyNoteData);
                            const stickerParams = stickyNoteData.sticker_params;
                            // routes/api.phpのRoute::postのwork-sticky-note-importにて、
                            // 特別なプロパティitem_infoがなければ画像や動画を追加しないようにしてあるので、
                            // テキストだけ抽出するというようなことはしなくてもよい。
                            
                            // データベースを更新する
                            console.log('axios.post');
                            
                            const reqParam = {
                                stickerParams: stickerParams,
                            };
                            
                            axios.post(window.laravel.asset + '/api/work-sticky-note-import', {
                                reqParam: reqParam,
                                user_id: window.laravel.user['id'],
                            })
                                .then(response => {
                                    // 特にすることなし
                                });
                        });
                    }
                });
                
                // 「ファイルを開く」ダイアログを表示
                input.style.display = 'none';
                document.body.appendChild(input);
                input.click();
                document.body.removeChild(input);
            },
            
            onClickFileSubImportAll: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
                
                console.log('onClickFileSubImportAll');
                
                // HTMLのinput要素を生成
                const input = document.createElement("input");
                input.type = "file";
                input.accept = "application/zip";                
                
                // ファイルが指定された後に行う処理
                input.addEventListener("change", e => {
                    const file = e.target.files[0];
                    
                    if (file) {
                        console.log(file.name);  // ファイル名
                        
                        const zip = new JSZip();
                        // ファイルの非同期読み込み
                        zip.loadAsync(file)
                        .then((zipContent) => {  // this.を使いたかったので、.then(function(zipContent) {からアロー関数に変えた。
                            // ファイルが読み込まれた後に行う処理
                            console.log(zipContent);
                        
                            // zipに含まれているファイル名を取得しながら、JSONファイルを探す
                            const compressFileNames = [];
                            let jsonFileName = null;
                            for(const key in zipContent.files) {
                                const value = zipContent.files[key];
                                console.log(key, value.name, value.dir);
                                
                                compressFileNames.push(value.name);
                                
                                // 拡張子jsonか？
                                const extName = value.name.match(/[^.]+$/);
                                if (extName == 'json') {
                                    jsonFileName = value.name;
                                    // break;  // compressFileNamesを作りながらなのでbreakはしないので、コメントアウトしておく。
                                }
                            }
                        
                            // JSONファイル取得
                            if (jsonFileName) {
                                zipContent.files[jsonFileName].async('string')
                                .then((jsonData) => {  // this.を使いたかったので、.then(function(jsonData) {からアロー関数に変えた。
                                    // 読み込んだJSONをJavaScriptのオブジェクトに変換する
                                    const stickyNoteData = JSON.parse(jsonData);
                                    console.log(stickyNoteData);
                                    const stickerParams = stickyNoteData.sticker_params;
                                    
                                    // 画像や動画ファイルを探し、取得する
                                    const uncompressInfos = [];
                                    const uncompressFuncs = [];
                                    
                                    for (let stickerIndex = 0; stickerIndex < stickerParams.length; ++stickerIndex) {
                                        const stickerParam = stickerParams[stickerIndex];
                                        const contents = stickerParam.contents;
                                        
                                        for (let contentIndex = 0; contentIndex < contents.length; ++contentIndex) {
                                            const content = contents[contentIndex];
 
                                            let source = null;
                                            if (content.link.item_type == 2) {  // app/Sticker.phpで値を定義している
                                                source = content.item.image_url;
                                            } else if (content.link.item_type == 3) {  // app/Sticker.phpで値を定義している
                                                source = content.item.video_url;
                                            }
                                            
                                            if (source) {
                                                console.log(source);
                                                
                                                // contentのitemが画像か動画のとき
                                                const itemFileNameA = source.slice(source.lastIndexOf("/") + 1);  // lastIndexOfは値が見つからない場合は-1
                                                
                                                let itemFileName = null;
                                                for (const compressFileName of compressFileNames) {
                                                    const itemFileNameB = compressFileName.slice(compressFileName.lastIndexOf("/") + 1);  // lastIndexOfは値が見つからない場合は-1
                                                    if (itemFileNameA == itemFileNameB) {
                                                        itemFileName = compressFileName;
                                                        break;
                                                    }
                                                }
                                                
                                                if (itemFileName) {
                                                    console.log(itemFileName);
                                                    
                                                    const extNameArray = itemFileName.match(/[^.]+$/);  // 拡張子
                                                    let extName = '';
                                                    if (extNameArray) {
                                                        extName = extNameArray[0];
                                                    }
                                                    console.log(extNameArray, extNameArray.length, extName);
                                                    
                                                    // 画像や動画ファイルがzipに含まれているので、取得する
                                                    const uncompressInfo = {
                                                        stickerIndex:    stickerIndex,
                                                        contentIndex:    contentIndex,
                                                        contentItemType: content.link.item_type,
                                                        extName:         extName,
                                                    };
                                                    
                                                    uncompressInfos.push(uncompressInfo);
                                                    uncompressFuncs.push(zipContent.files[itemFileName].async('base64'));
                                                }
                                            }
                                        }
                                    }
                                    
                                    if (uncompressFuncs.length >= 1) {
                                        Promise.all(uncompressFuncs)
                                        .then((uncompressData) => {  // this.を使いたかったので、.then(function(uncompressData) {からアロー関数に変えた。
                                            // 読み込んだ画像や動画をDataURLにする
                                            for (let dataIndex=0; dataIndex<uncompressData.length; ++dataIndex) {
                                                /*
                                                // TODO(kawadakoujisun): このbase64から16進数へ変換する処理は間違っているかもしれない。要確認！
                                                // ファイルの先頭数バイトからMIMEタイプを取得する
                                                const header64 = uncompressData[dataIndex].slice(0, 16);
                                                const headerRaw = atob(header64);
                                                const header16 = '';
                                                for (let charIndex=0; charIndex<headerRaw.length; ++charIndex) {
                                                    header16 += headerRaw.charCodeAt(charIndex).toString(16);
                                                }

                                                const mimeType = this.getMimeTypeFromHeader(header16);
                                                console.log(header64, headerRaw, header16, mimeType);
                                                */
                                                
                                                // 拡張子からMIMEタイプを取得する
                                                const uncompressInfo = uncompressInfos[dataIndex];
                                                const mimeType = this.getMimeTypeFromExtension(uncompressInfo.extName);
                                                console.log(uncompressInfo.extName, mimeType);

                                                if (mimeType) {
                                                    // DataURL
                                                    const dataURL = 'data:' + mimeType + ';base64,' + uncompressData[dataIndex];
                                                
                                                    // 新しいプロパティitem_infoを追加し、DataURLを設定する。
                                                    // ファイルがあるかどうかは、この特別なプロパティitem_infoがあるかどうかで判定する。
                                                    if (uncompressInfo.contentItemType == 2) {  // app/Sticker.phpで値を定義している
                                                        stickerParams[uncompressInfo.stickerIndex].contents[uncompressInfo.contentIndex].item_info = {
                                                            selectImageFileInfo: dataURL,
                                                        };
                                                    } else if (uncompressInfo.contentItemType == 3) {  // app/Sticker.phpで値を定義している
                                                        stickerParams[uncompressInfo.stickerIndex].contents[uncompressInfo.contentIndex].item_info = {
                                                            selectVideoFileInfo: dataURL,
                                                        };
                                                    }
                                                }
                                            }
                                            
                                            // 特別なプロパティitem_infoを足した後の状態を確認
                                            console.log(stickerParams);
                                    
                                            // データベースを更新する
                                            console.log('axios.post');
                                            
                                            const reqParam = {
                                                stickerParams: stickerParams,
                                            };
                                            
                                            axios.post(window.laravel.asset + '/api/work-sticky-note-import', {
                                                reqParam: reqParam,
                                                user_id: window.laravel.user['id'],
                                            })
                                                .then(response => {
                                                    // 特にすることなし
                                                });
                                        });
                                    } else {
                                        // TODO(kawadakoujisun): Promise.all(uncompressFuncs)の配列が空だったときの挙動次第では
                                        //     axios.postを1か所だけにできるはず。要確認！
                                        {
                                            // データベースを更新する
                                            console.log('axios.post');
                                            
                                            const reqParam = {
                                                stickerParams: stickerParams,
                                            };
                                            
                                            axios.post(window.laravel.asset + '/api/work-sticky-note-import', {
                                                reqParam: reqParam,
                                                user_id: window.laravel.user['id'],
                                            })
                                                .then(response => {
                                                    // 特にすることなし
                                                });
                                        }
                                    }
                                });
                            }
                        });
                    }
                });

                // 「ファイルを開く」ダイアログを表示
                input.style.display = 'none';
                document.body.appendChild(input);
                input.click();
                document.body.removeChild(input);
            },
            
            onClickFileSubImportClose: function (e) {
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
                        // JSON
                        const stickyNoteJson = this.getStickyNoteJson(stickerParams);
                        
                        // ダウンロード
                        let blob = new Blob([stickyNoteJson], {type:"application/json"});
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
            getStickyNoteJson: function (stickerParams) {
                const stickyNoteData = {
                    file_info: {
                        app_name: 'stickynote',
                        version: '0.1.0',
                    },
                    sticker_params: stickerParams,
                };
                
                const stickyNoteJson = JSON.stringify(stickyNoteData, null, '\t');
                
                return stickyNoteJson;
            },
            
            downloadAll: async function (stickerParams) {
                // JSON
                const stickyNoteJson = this.getStickyNoteJson(stickerParams);
                
                // 画像や動画のURL
                const sources = [];
                
                for (let stickerIndex = 0; stickerIndex < stickerParams.length; ++stickerIndex) {
                    const stickerParam = stickerParams[stickerIndex];
                    const contents = stickerParam.contents;
                    
                    for (let contentIndex = 0; contentIndex < contents.length; ++contentIndex) {
                        const content = contents[contentIndex];
                        if (content.link.item_type == 2) {  // app/Sticker.phpで値を定義している
                            sources.push(content.item.image_url);
                        } else if (content.link.item_type == 3) {  // app/Sticker.phpで値を定義している
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
                                    const fileName = source.slice(source.lastIndexOf("/") + 1);  // lastIndexOfは値が見つからない場合は-1
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
                folder.file('stickynote.json', stickyNoteJson);
                
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
            
            /*
            // TODO(kawadakoujisun): base64から16進数へ変換したヘッダーでMIMEタイプを判定しているが、
            //     base64から16進数への変換が間違っているかもしれない、その間違ったものに合わせた判定をこの関数内で行っている
            //     ので、ヘッダーから判定するのはやめた。コメントアウトしておく。
            getMimeTypeFromHeader: function (header) {
                let mimeType = null;
                
                if (header.startsWith('ffd8ff')) {
                    mimeType = 'image/jpeg';
                } else if (header.startsWith('47494638')) {
                    mimeType = 'image/gif';
                } else if (header.startsWith('89504e47')) {
                    mimeType = 'image/png';
                //{
                //    mimeType = 'image/webp';
                //}
                } else if (header.startsWith('00020')) {
                    mimeType = 'video/mp4';
                }
                //{
                //    mimeType = 'video/ogg';
                //}
                //{
                //    mimeType = 'video/webm';
                //}

                return mimeType;
            },
            */
            
            getMimeTypeFromExtension: function (extensionString) {
                let mimeType = null;
                
                const ext = extensionString.toLowerCase();  // 小文字にしておく
                
                if (ext == 'jpg' || ext == 'jpeg') {  // 他にjfif, pjpeg, pjpもあるらしいが見たことがないのでそれは今回は含まないことにした
                    mimeType = 'image/jpeg';
                } else if (ext == 'gif') {
                    mimeType = 'image/gif';
                } else if (ext == 'png') {
                    mimeType = 'image/png';
                } else if (ext == 'webp') {
                    mimeType = 'image/webp';
                } else if (ext == 'mp4') {
                    mimeType = 'video/mp4';
                } else if (ext == 'ogv') {
                    mimeType = 'video/ogg';
                } else if (ext == 'webm') {
                    mimeType = 'video/webm';
                }

                return mimeType;
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
    
    .menu-bar-file-sub-import-window-class {
        position: absolute;
        left:   120px;
        top:    30px;
        width:  300px;
        height: 90px;
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