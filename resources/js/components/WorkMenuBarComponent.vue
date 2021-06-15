<template>
    <div class="menu-bar-class">
        <div class="menu-bar-mark-class"><img v-bind:src="markSrc"></div>
        
        <div class="menu-bar-button-outer-class" id="menu-bar-button-file-id">
            <a @click.prevent="onClickMainFile"><span class="menu-bar-button-inner-class">ファイル</span></a>
        </div>
        <div class="menu-bar-button-outer-class" id="menu-bar-button-insert-id">
            <a @click.prevent="onClickMainInsert"><span class="menu-bar-button-inner-class">挿入</span></a>
        </div>
        <div class="menu-bar-button-outer-class" id="menu-bar-button-user-id">
            <a @click.prevent="onClickMainUser"><span class="menu-bar-button-inner-class">ユーザー</span></a>
        </div>

        <!-- 背景 -->
        <div v-if="activeMainMenu !== ''">
            <div
                class="menu-bar-window-overlay-class"
                @click.self="onClickOverlay"
                @click.right.self="onClickOverlay"
            >
            </div>
        </div>
        
        <!-- ファイル -->
        <div v-show="activeMainMenu === 'mainFile'">  <!-- v-ifだとこの子要素にアクセスできない -->
            <div class="menu-bar-main-window-class" id="menu-bar-main-file-window-id">
                <div class="menu-bar-window-button-outer-class" id="menu-bar-main-file-import-button-id" @click.prevent="onClickFileSubImport" @mouseenter="onMouseEnterFileSubImport">
                    <span class="menu-bar-window-button-inner-space-class"></span>
                    インポート &gt;
                    <span class="menu-bar-window-button-inner-space-class"></span>
                </div>
                <div class="menu-bar-window-button-outer-class" id="menu-bar-main-file-download-button-id" @click.prevent="onClickFileSubDonwload" @mouseenter="onMouseEnterFileSubDownload">
                    <span class="menu-bar-window-button-inner-space-class"></span>
                    ダウンロード &gt;
                    <span class="menu-bar-window-button-inner-space-class"></span>
                </div>
            </div>

            <!-- ファイル > インポート -->
            <div v-show="activeSubMenu === 'fileSubImport'">
                <div class="menu-bar-sub-window-class" id="menu-bar-file-sub-import-window-id">
                    <div class="menu-bar-window-button-outer-class" @click.prevent="onClickFileSubImportText">
                        <span class="menu-bar-window-button-inner-space-class"></span>
                        テキストのみ(.json)
                        <span class="menu-bar-window-button-inner-space-class"></span>
                    </div>
                    <div class="menu-bar-window-button-outer-class" @click.prevent="onClickFileSubImportAll">
                        <span class="menu-bar-window-button-inner-space-class"></span>
                        全部(.zip)
                        <span class="menu-bar-window-button-inner-space-class"></span>
                    </div>                    
                </div>
            </div>

            <!-- ファイル > ダウンロード -->
            <div v-show="activeSubMenu === 'fileSubDownload'">
                <div class="menu-bar-sub-window-class" id="menu-bar-file-sub-download-window-id">
                    <div class="menu-bar-window-button-outer-class" @click.prevent="onClickFileSubDownloadText">
                        <span class="menu-bar-window-button-inner-space-class"></span>
                        テキストのみ(.json)
                        <span class="menu-bar-window-button-inner-space-class"></span>
                    </div>
                    <div class="menu-bar-window-button-outer-class" @click.prevent="onClickFileSubDownloadAll">
                        <span class="menu-bar-window-button-inner-space-class"></span>
                        全部(.zip)
                        <span class="menu-bar-window-button-inner-space-class"></span>
                    </div>                       
                </div>
            </div>
        </div>
        
        <!-- 挿入 -->
        <div v-show="activeMainMenu === 'mainInsert'">
            <div class="menu-bar-main-window-class" id="menu-bar-main-insert-window-id">
                <div class="menu-bar-window-button-outer-class" @click.prevent="onClickInsertSubSticker">
                    <span class="menu-bar-window-button-inner-space-class"></span>
                    ふせん
                    <span class="menu-bar-window-button-inner-space-class"></span>
                </div>
            </div>        
        </div>        

        <!-- ユーザー -->
        <div v-show="activeMainMenu === 'mainUser'">
            <div class="menu-bar-main-window-class" id="menu-bar-main-user-window-id">
                <div class="menu-bar-window-button-outer-class" @click.prevent="onClickUserSubRead">
                    <span class="menu-bar-window-button-inner-space-class"></span>
                    閲覧のみ
                    <span class="menu-bar-window-button-inner-space-class"></span>
                </div>                
                <div class="menu-bar-window-button-outer-class" @click.prevent="onClickUserSubLogout">
                    <span class="menu-bar-window-button-inner-space-class"></span>                  
                    Logout
                    <span class="menu-bar-window-button-inner-space-class"></span>
                </div>
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
                markSrc: window.laravel.asset + '/images/stickynote_mark.jpg',
            };
        },
        
        methods: {
            //
            // 背景
            //
            onClickOverlay: function (e) {
                if (this.activeMainMenu !== '') {
                    e.preventDefault();
                    
                    this.activeMainMenu = '';
                    this.activeSubMenu = '';
                }  
            },
            
            //
            // メイン
            //
            onClickMainFile: function (e) {
                if (this.activeMainMenu !== 'mainFile') {
                    this.activeMainMenu = 'mainFile';
                    this.activeSubMenu = '';
                    
                    this.setWindowPos('menu-bar-button-file-id', 'menu-bar-main-file-window-id', false);
                }
            },
            
            onClickMainInsert: function (e) {
                if (this.activeMainMenu !== 'mainInsert') {
                    this.activeMainMenu = 'mainInsert';
                    this.activeSubMenu = '';
                    
                    this.setWindowPos('menu-bar-button-insert-id', 'menu-bar-main-insert-window-id', false);
                }
            },
            
            onClickMainUser: function (e) {
                if (this.activeSubMenu !== 'mainUser') {
                    this.activeMainMenu = 'mainUser';
                    this.activeSubMenu = '';
                    
                    this.setWindowPos('menu-bar-button-user-id', 'menu-bar-main-user-window-id', false);
                }
            },
            
            //
            // ファイルサブ
            //
            onClickFileSubImport: function (e) {
                if (this.activeSubMenu !== 'fileSubImport') {
                    this.activeSubMenu = 'fileSubImport';
                
                    this.setWindowPos('menu-bar-main-file-import-button-id', 'menu-bar-file-sub-import-window-id', true);
                }
            },
            
            onMouseEnterFileSubImport: function (e) {
                if (this.activeSubMenu !== 'fileSubImport') {
                    this.activeSubMenu = 'fileSubImport';
                    
                    this.setWindowPos('menu-bar-main-file-import-button-id', 'menu-bar-file-sub-import-window-id', true);
                }
            },
            
            onClickFileSubDonwload: function (e) {
                if (this.activeSubMenu !== 'fileSubDownload') {
                    this.activeSubMenu = 'fileSubDownload';
                
                    this.setWindowPos('menu-bar-main-file-download-button-id', 'menu-bar-file-sub-download-window-id', true);
                }
            },
            
            onMouseEnterFileSubDownload: function (e) {
                if (this.activeSubMenu !== 'fileSubDownload') {
                    this.activeSubMenu = 'fileSubDownload';
                    
                    this.setWindowPos('menu-bar-main-file-download-button-id', 'menu-bar-file-sub-download-window-id', true);
                }
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
                                    
                                    let uncompressCount = 0;
                                    const uncompressIndexes = [];  // uncompressIndexes[stickerIndex][contentIndex] = uncompressInfosのインデックス
                                    
                                    for (let stickerIndex = 0; stickerIndex < stickerParams.length; ++stickerIndex) {
                                        const stickerParam = stickerParams[stickerIndex];
                                        const contents = stickerParam.contents;
                                        
                                        const uncompressContentIndexes = [];
                                        
                                        for (let contentIndex = 0; contentIndex < contents.length; ++contentIndex) {
                                            const content = contents[contentIndex];
                                            
                                            uncompressContentIndexes.push(-1);  // uncompressInfosのインデックスがない場合は-1
 
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
                                                    
                                                    uncompressContentIndexes.pop();  // pushしていた-1をpopしておく
                                                    uncompressContentIndexes.push(uncompressCount);
                                                    
                                                    ++uncompressCount;
                                                }
                                            }
                                        }
                                        
                                        uncompressIndexes.push(uncompressContentIndexes);
                                    }
                                    
                                    if (uncompressFuncs.length >= 1) {
                                        Promise.all(uncompressFuncs)
                                        .then((uncompressData) => {  // this.を使いたかったので、.then(function(uncompressData) {からアロー関数に変えた。
                                            /*
                                            //
                                            // 特別なプロパティitem_infoを使う場合
                                            //     1回のpostで済ませる
                                            //
                                            
                                            // 読み込んだ画像や動画をDataURLにする
                                            for (let dataIndex=0; dataIndex<uncompressData.length; ++dataIndex) {
                                                // 
                                                // // TODO(kawadakoujisun): このbase64から16進数へ変換する処理は間違っているかもしれない。要確認！
                                                // // ファイルの先頭数バイトからMIMEタイプを取得する
                                                // const header64 = uncompressData[dataIndex].slice(0, 16);
                                                // const headerRaw = atob(header64);
                                                // const header16 = '';
                                                // for (let charIndex=0; charIndex<headerRaw.length; ++charIndex) {
                                                //     header16 += headerRaw.charCodeAt(charIndex).toString(16);
                                                // }
                                                // 
                                                // const mimeType = this.getMimeTypeFromHeader(header16);
                                                // console.log(header64, headerRaw, header16, mimeType);
                                                // 
                                                
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
                                                
                                            //
                                            // 特別なプロパティitem_infoを使う場合 ここまで
                                            //
                                            */
                                            
                                            /**/
                                            //
                                            // 特別なプロパティitem_infoを使わない場合
                                            //     何回かpostを行う
                                            //
                                            
                                            // データベースを更新する
                                            console.log('axios.post');
                                            
                                            const reqParam = {
                                                stickerParams: stickerParams,
                                            };                                            
                                            
                                            // start
                                            axios.post(window.laravel.asset + '/api/work-sticky-note-import-start', {
                                                reqParam: reqParam,
                                                user_id: window.laravel.user['id'],
                                            })
                                                .then(async function (response) {
                                                    const stickerIds = response.data;
                                                    
                                                    // content-item
                                                    try {
                                                        for (let stickerIndex = 0; stickerIndex < stickerParams.length; ++stickerIndex) {
                                                            const stickerId = stickerIds[stickerIndex];
                                                            
                                                            if (stickerId >= 0) {
                                                                const stickerParam = stickerParams[stickerIndex];
                                                                const contents = stickerParam.contents;
                                                                
                                                                for (let contentIndex = 0; contentIndex < contents.length; ++contentIndex) {
                                                                    const content = contents[contentIndex];
                                                                    
                                                                    const contentItemReqParam = {};
                                                                    contentItemReqParam.id = stickerId;
                                                                    contentItemReqParam.item_type = content.link.item_type;
                         
                                                                    let contentItemExist = false;
                                                                    
                                                                    if (content.link.item_type == 1) {  // app/Sticker.phpで値を定義している
                                                                        contentItemReqParam.text = content.item.text;
                                                                        contentItemExist = true;
                                                                    } else if (content.link.item_type == 2) {  // app/Sticker.phpで値を定義している
                                                                        const dataIndex = uncompressIndexes[stickerIndex][contentIndex];
                                                                        if (dataIndex >= 0) {
                                                                            // 拡張子からMIMEタイプを取得する
                                                                            const uncompressInfo = uncompressInfos[dataIndex];
                                                                            const mimeType = this.getMimeTypeFromExtension(uncompressInfo.extName);
                                                                            console.log(uncompressInfo.extName, mimeType);
                                
                                                                            if (mimeType) {
                                                                                // DataURL
                                                                                const dataURL = 'data:' + mimeType + ';base64,' + uncompressData[dataIndex];
                                                                                contentItemReqParam.selectImageFileInfo = dataURL;
                                                                                contentItemExist = true;
                                                                            }
                                                                        }
                                                                    } else if (content.link.item_type == 3) {  // app/Sticker.phpで値を定義している
                                                                        const dataIndex = uncompressIndexes[stickerIndex][contentIndex];
                                                                        if (dataIndex >= 0) {
                                                                            // 拡張子からMIMEタイプを取得する
                                                                            const uncompressInfo = uncompressInfos[dataIndex];
                                                                            const mimeType = this.getMimeTypeFromExtension(uncompressInfo.extName);
                                                                            console.log(uncompressInfo.extName, mimeType);
                                
                                                                            if (mimeType) {
                                                                                // DataURL
                                                                                const dataURL = 'data:' + mimeType + ';base64,' + uncompressData[dataIndex];
                                                                                contentItemReqParam.selectVideoFileInfo = dataURL;
                                                                                contentItemExist = true;
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                    if (contentItemExist) {
                                                                        await axios.post(window.laravel.asset + '/api/work-sticky-note-import-content-item', {
                                                                            reqParam: contentItemReqParam,
                                                                            user_id: window.laravel.user['id'],
                                                                        });
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    } catch (error) {
                                                        console.log('axios.post', error);
                                                    }
                                                    
                                                    // end
                                                    axios.post(window.laravel.asset + '/api/work-sticky-note-import-end', {
                                                        user_id: window.laravel.user['id'],
                                                    })
                                                        .then(response => {
                                                            // 特にすることなし
                                                        })
                                                        .catch(error => {
                                                            console.log('axios.post', error);
                                                        });
                                                }.bind(this))  // コールバック関数内でthisを使用しているので
                                                .catch(error => {
                                                    console.log('axios.post', error);
                                                });
                                            
                                            //
                                            // 特別なプロパティitem_infoを使わない場合 ここまで
                                            //
                                            /**/
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
            
            //
            // ユーザーサブ
            //            
            onClickUserSubRead: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
                
                const link = document.createElement('a');
                link.href = window.laravel.asset + '/';
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                console.log('onClickUserSubRead end');
                // this.debug_sleep(2000);
                // ここまで来てからページ遷移するようなので
                // document.body.removeChild(link);
                // まで完了していると思われる。
            },
            
            onClickUserSubLogout: function (e) {
                this.activeMainMenu = '';
                this.activeSubMenu = '';
                
                const link = document.createElement('a');
                link.href = window.laravel.asset + '/logout'
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                console.log('onClickUserSubLogout end');
                // this.debug_sleep(2000);
                // ここまで来てからページ遷移するようなので
                // document.body.removeChild(link);
                // まで完了していると思われる。
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
            
            // ウィンドウの表示位置を設定する
            setWindowPos: function (buttonId, windowId, isSubWindow) {
                const buttonElem = document.getElementById(buttonId);
                const windowElem = document.getElementById(windowId);
                
                const buttonElemRect = buttonElem.getBoundingClientRect();
                const buttonElemPagePos = {};
                buttonElemPagePos.x = buttonElemRect.left + window.pageXOffset;
                buttonElemPagePos.y = buttonElemRect.top + window.pageYOffset;
                const windowElemMountPos = this.convertPosFromPageToMount(buttonElemPagePos);
                
                if (isSubWindow) {
                    windowElem.style.left = `${windowElemMountPos.x + buttonElemRect.width}px`;
                    windowElem.style.top  = `${windowElemMountPos.y}px`;
                } else {
                    windowElem.style.left = `${windowElemMountPos.x}px`;
                        // TODO(kawadakoujisun): 小数のこともある。
                        //     ここは大丈夫なので、他の.vueで使っているところを見直して！
                }
            },
            
            // pagePos = {
            //     x: 0,  // px（数値だけで単位の文字列は付けていない）
            //     y: 0,
            // };
            // pagePosはページ内における座標。
            // mountPosは台紙内における座標。
            convertPosFromPageToMount: function (pagePos) {
                const mountPos = {
                    x: 0,  // px（数値だけで単位の文字列は付けていない）
                    y: 0,
                };
                
                // 台紙の枠の太さ
                const mountBorderWidth = this.getMountBorderWidth();
                
                const mountElem = this.$el;
                // 台紙の現在の画面内における座標
                const mountElemRect = mountElem.getBoundingClientRect();
                
                // 台紙のページ内における座標
                const mountElemPagePos = {};
                mountElemPagePos.x = mountElemRect.left + window.pageXOffset;
                mountElemPagePos.y = mountElemRect.top + window.pageYOffset;
                
                mountPos.x = pagePos.x - mountElemPagePos.x - mountBorderWidth;
                mountPos.y = pagePos.y - mountElemPagePos.y - mountBorderWidth;
                    
                return mountPos;
            },            
            
            getMountBorderWidth: function () {
                return 0;  
            },            
            
            // ビジーwaitを使いsleepするデバッグ用途のメソッド
            debug_sleep: function (waitMilliSecond) {
              var startTime = new Date();
             
              // 指定ミリ秒間だけループさせる（CPUは常にビジー状態）
              while (new Date() - startTime < waitMilliSecond);
            },
        },
    };
</script>

<style scoped>
    /*
     * メニューバーのバーそのもの
     */
    .menu-bar-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 30px;
        /*border: 1px solid #000;*/
        background-color: #ffffff;
        margin: 20px 20px 0px;
        padding: 0;
    }
    
    .menu-bar-mark-class {
        display: inline-block;
        vertical-align: middle;
        padding: 0;
    }    
    
    /*
     * メニューバー上にあるボタン
     */
    .menu-bar-button-outer-class {
        position: relative;  /* z-indexを指定したいので、positionをデフォルトのstaticからrelativeに変えておく。 */
        z-index: 2001;
        margin: 0;
        display: inline-block;
    }
    
    .menu-bar-button-inner-class {
        display: inline-block;
        height: 30px;
        background-color: #ffffff;
        margin: 0;
        padding: 0px 10px;
        line-height: 30px;
    }

    .menu-bar-button-inner-class:hover {
        background-color: #eeeeee;
        cursor: pointer;
    }    
    
    /*
     * メニューバーに属するウィンドウを表示しているときのオーバーレイ
     */
    .menu-bar-window-overlay-class {  /* 「menu-bar-classが付いた要素」の子の要素のクラス */
        position: absolute;
        
        /*left:   0;*/
        /*top:    30px;*/  /* メニューバーの下の位置 */
        /*width:  100%;*/  /* メニューバーの横幅は台紙の横幅と合わせてある */
        /*height: 920px;*/  /* だいたい『「メニューバーと台紙の間の距離」+「台紙の高さ」+「ボーダーの太さ」』くらい */
        
        left:   -20px;  /* メニューバーの位置からマージン分左へ */
        top:    -20px;  /* メニューバーの位置からマージン分上へ */
        width:  1850px;  /* だいたい『「メニューバーの横幅(=台紙の横幅)」+「ボーダーの太さ」+「左右マージン分」』くらい */
        height: 980px;  /* だいたい『「メニューバーの高さ」+「メニューバーと台紙の間の距離」+「台紙の高さ」+「ボーダーの太さ」+「上下マージン分」』くらい */
        
        z-index: 2000;  /* 台紙より上に表示される */
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    /*
     * ウィンドウ内のボタン
     */
    .menu-bar-window-button-outer-class {
        width: 100%;
        height: 30px;
        background-color: #ffffff;
        margin: 0;
        line-height: 30px;
    }

    .menu-bar-window-button-outer-class:hover {
        background-color: #eeeeee;
        cursor: pointer;
    }
    
    .menu-bar-window-button-inner-space-class {
        display: inline-block;
        width: 10px;
    }    
    
    /*
     * メニューバーの上にあるボタンから表示するウィンドウ
     */
    .menu-bar-main-window-class {
        position: absolute;
        top:    30px;
        z-index: 2001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
        padding: 0;
        box-shadow: 0 0 3px 2px rgba(0, 0, 0, 0.3);
        
        /* 外部から変更するもの */
        left:   0;
    }
    
    /*
     * 「メニューバーの上にあるボタンから表示したウィンドウ」から表示するウィンドウ
     */
    .menu-bar-sub-window-class {
        position: absolute;
        z-index: 2001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
        padding: 0;
        box-shadow: 0 0 3px 2px rgba(0, 0, 0, 0.3);
        
        /* 外部から変更するもの */
        top:    0;
        left:   0;
    }
</style>