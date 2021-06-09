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
                
                /*
                // ファイルのインポートの例1
                this.importTextFile();
                */
                
                // ファイルのインポートの例2
                this.importZipFile();
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
                
                /*
                // ファイルのダウンロードの例1
                const a = document.createElement('a');
                a.href = 'data:text/plain,' + encodeURIComponent('test text\n');
                a.download = 'testdir/text.txt';
                a.click();
                */
                
                /*
                // ファイルのダウンロードの例2
                let blob = new Blob(['あいうえお'],{type:"text/plain"});
                let link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = '作ったファイル.txt';

                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                */
                
                /*
                // ファイルのダウンロードの例3
                var zip = new Zlib.Zip();
                var plainData = JSON.stringify(this.getJsonObj());
                zip.addFile(this.strToUtf8Array(plainData), {
                    filename: this.strToUtf8Array('sample/sample001.json')
                });
                zip.addFile(this.strToUtf8Array('あいうえお'), {
                    filename: this.strToUtf8Array('sample2/sample3/sample4.txt'),
                });
                var compressData = zip.compress();
                
                var blob = new Blob([compressData], { 'type': 'application/zip' });
                
                let link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'samplefile.zip';

                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                */
                
                /*
                // ファイルのダウンロードの例4 → 画像ファイルをうまく取得できなかった
                const url = 'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg';
                fetch(url)
                    .then((res) => {
                        // blob
                        // const blob = res.blob().then( (myBlob) => { console.log(myBlob);  console.log(myBlob.type); } );
                        const blob = res.blob();
                        console.log(blob);
                        
                        // ファイル名
                        // 最後の/の位置を取得
                        const extensionIndex = url.lastIndexOf('/');
                        // その位置 + 1以降の文字列(ファイル名)を取得
                        const str = url.slice(extensionIndex + 1);
                        console.log(str);

                        // Blob -> Fileへの変換
                        const file = new File([blob], str, {type: "application/octet-stream"});
                        
                        // zip
                        var zip = new Zlib.Zip();
                        var plainData = JSON.stringify(this.getJsonObj());
                        zip.addFile(this.strToUtf8Array(plainData), {
                            filename: this.strToUtf8Array('sample/sample001.json')
                        });
                        // zip.addFile([blob], {  // ダメ
                        zip.addFile([file], {  // ダメ
                            filename: this.strToUtf8Array('sample/'+str),
                        });
                        var compressData = zip.compress();
                        
                        var compressedBlob = new Blob([compressData], { 'type': 'application/zip' });
                        
                        let link = document.createElement('a');
                        link.href = URL.createObjectURL(compressedBlob);
                        link.download = 'samplefile.zip';
        
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                */
                
                /*
                // ファイルのダウンロードの例5 → 画像ファイルをうまく取得できなかった
                const url = 'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg';
                fetch(url)
                    .then((res) => {
                        // blob
                        const blob = res.blob().then((myBlob) => {
                            console.log(myBlob);
                            console.log(myBlob.type);
                            
                            // ファイル名
                            // 最後の/の位置を取得
                            const extensionIndex = url.lastIndexOf('/');
                            // その位置 + 1以降の文字列(ファイル名)を取得
                            const str = url.slice(extensionIndex + 1);
                            console.log(str);
    
                            // Blob -> Fileへの変換
                            const file = new File([myBlob], str, {type: myBlob.type});
                            
                            // zip
                            var zip = new Zlib.Zip();
                            var plainData = JSON.stringify(this.getJsonObj());
                            zip.addFile(this.strToUtf8Array(plainData), {
                                filename: this.strToUtf8Array('sample/sample001.json')
                            });
                            // zip.addFile([myBlob], {  // ダメ
                            zip.addFile([file], {  // ダメ
                                filename: this.strToUtf8Array('sample/'+str),
                            });
                            var compressData = zip.compress();
                            
                            var compressedBlob = new Blob([compressData], { 'type': 'application/zip' });
                            
                            let link = document.createElement('a');
                            link.href = URL.createObjectURL(compressedBlob);
                            link.download = 'samplefile.zip';
            
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                */
                
                /*
                // ファイルのダウンロードの例6 → URL.createObjectURL(blob)でエラーになってしまった
                const url = 'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg';
                fetch(url)
                    .then((res) => {
                        // blob
                        const blob = res.blob();
                        
                        // ファイル名
                        // 最後の/の位置を取得
                        const extensionIndex = url.lastIndexOf('/');
                        // その位置 + 1以降の文字列(ファイル名)を取得
                        const str = url.slice(extensionIndex + 1);
                        console.log(str);
    
                        let link = document.createElement('a');
                        link.href = URL.createObjectURL(blob);  // ダメ
                        link.download = str;
            
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);    
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                */
                
                /*
                // ファイルのダウンロードの例7
                const url = 'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg';
                fetch(url)
                    .then((res) => {
                        // blob
                        const blob = res.blob().then((myBlob) => {
                            console.log(myBlob);
                            console.log(myBlob.type);
                            
                            // ファイル名
                            // 最後の/の位置を取得
                            const extensionIndex = url.lastIndexOf('/');
                            // その位置 + 1以降の文字列(ファイル名)を取得
                            const str = url.slice(extensionIndex + 1);
                            console.log(str);
    
                            let link = document.createElement('a');
                            link.href = URL.createObjectURL(myBlob);
                            link.download = str;
            
                            link.style.display = 'none';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);    
                        });
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                */
                
                /*
                // ファイルのダウンロードの例8 → URL.createObjectURL(myBlob)でエラーになってしまった
                const url = 'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg';
                axios.get(url)
                    .then(response => {
                        console.log('axios.get');
                        const myBlob = response.data;
                        console.log(myBlob);
                        console.log(myBlob.type);  // ダメ undefined
                        
                        // ファイル名
                        // 最後の/の位置を取得
                        const extensionIndex = url.lastIndexOf('/');
                        // その位置 + 1以降の文字列(ファイル名)を取得
                        const str = url.slice(extensionIndex + 1);
                        console.log(str);
    
                        let link = document.createElement('a');
                        link.href = URL.createObjectURL(myBlob);
                        link.download = str;
            
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);    
                    });
                */
                
                // ファイルのダウンロードの例9
                /*
                this.downloadImages();
                */
                
                // ファイルのダウンロードの例10 ← ファイルのダウンロードはしていない。Promiseの使い方を確認した。
                /*
                this.testPromise();
                */
                
                // ファイルのダウンロードの例11 ← ファイルのダウンロードはしていない。awaitの使い方を確認した。
                /*
                this.testAwait();
                */
                
                // ファイルのダウンロードの例12 ← 「ファイルのダウンロードの例9」を改造してみて、理解を深める。
                this.downloadImages2();
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

            // ファイルのダウンロードの例3用
            strToUtf8Array: function (str) {
                var n = str.length,
                    idx = -1,
                    bytes = [],
                    i, j, c;
        
                for (i = 0; i < n; ++i) {
                    c = str.charCodeAt(i);
                    if (c <= 0x7F) {
                        bytes[++idx] = c;
                    } else if (c <= 0x7FF) {
                        bytes[++idx] = 0xC0 | (c >>> 6);
                        bytes[++idx] = 0x80 | (c & 0x3F);
                    } else if (c <= 0xFFFF) {
                        bytes[++idx] = 0xE0 | (c >>> 12);
                        bytes[++idx] = 0x80 | ((c >>> 6) & 0x3F);
                        bytes[++idx] = 0x80 | (c & 0x3F);
                    } else {
                        bytes[++idx] = 0xF0 | (c >>> 18);
                        bytes[++idx] = 0x80 | ((c >>> 12) & 0x3F);
                        bytes[++idx] = 0x80 | ((c >>> 6) & 0x3F);
                        bytes[++idx] = 0x80 | (c & 0x3F);
                    }
                }
                return bytes;
            },
        
            // ファイルのダウンロードの例3用
            getJsonObj: function () {
                return {
                    inputText1: "あいうえお１２３４５",
                    inputRadio1: "1",
                    select1: "3",
                };
            },
            
            // ファイルのダウンロードの例9用
            downloadImages: async function () {
                // 画像の一括ダウンロード
                
                // 画像 URL
                const sources = [
                    'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg',
                    'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089081/stickynote_uploads/images/txr3igl4epq7wzyrbjrn.jpg',
                    'https://res.cloudinary.com/dreevk9fx/video/upload/v1623212886/stickynote_uploads/videos/ytjglxzh93ogzesud97e.mp4',
                ];
                
                // JSZip に追加するために非同期リクエストを Promise で wrap
                const imagePromises = sources.map(
                    (src, i) => new Promise((resolve, reject) => {
                        let xhr = new XMLHttpRequest();
                        xhr.open('GET', src, true);
                        xhr.responseType = "blob";
                        xhr.onload = function() {
                            // ファイル名とデータ返却
                            const fileName = src.slice(src.lastIndexOf("/") + 1);
                            resolve({ data: this.response, fileName: fileName });
                        };
                        // reject だと await Promise.all を抜けてしまう
                        // => resolve でデータ無し
                        xhr.onerror = () => resolve({ data: null });
                        xhr.onabort = () => resolve({ data: null });
                        xhr.ontimeout = () => resolve({ data: null });
                        xhr.send();
                    })
                );
                
                // すべての画像が取得できたら zip 生成
                const images = await Promise.all(imagePromises);
                
                // zip ファイルで画像をダウンロード
                let zip = new JSZip();
                
                // フォルダ作成
                const folderName = "sampleFolder";
                let folder = zip.folder(folderName);
                
                // フォルダ下に画像を格納
                images.forEach(image => {
                    if (image.data && image.fileName) {
                        folder.file(image.fileName, image.data);
                    }
                });
                
                // フォルダ下にテキストを格納
                let textData = '';
                textData = textData + 'begin\n';
                textData = textData + 'Hello World\n';
                textData = textData + 'end\n';
                folder.file('sample_text.txt', textData);
                
                // zip を生成
                zip.generateAsync({ type: "blob" }).then(blob => {
                    // ダウンロードリンクを 生成
                    let dlLink = document.createElement("a");
                
                    // blob から URL を生成
                    const dataUrl = URL.createObjectURL(blob);
                    dlLink.href = dataUrl;
                    dlLink.download = `${folderName}.zip`;
                
                    // 設置/クリック/削除
                    dlLink.style.display = 'none';
                    document.body.appendChild(dlLink);
                    dlLink.click();
                    document.body.removeChild(dlLink);
                });
            },

            // ファイルのダウンロードの例10用
            testPromise: function () {
                /*
                テストその1
                //日付データを取得する
                function getDate(callback) {
                    var date = new Date;
                    callback(date);
                }
                
                //日付データを元に西暦を取得する
                function getYear(date) {
                    var year = date.getFullYear();
                    console.log('year=', year);
                }
                
                getDate(function (date) {
                    getYear(date);
                });
                */
                
                /*
                // テストその2
                function getYear(date) {
                    var year = date.getFullYear();
                    console.log('year=', year);
                }
                
                var result = new Promise(function(resolve) {
                    var date = new Date;
                    resolve(date);
                });
                
                result.then(function (date) {
                   getYear(date); 
                });
                */
                
                /*
                // テストその3
                function getSomething1(item) {
                    return new Promise(function(resolve) {
                        console.log('Something1 ', item);
                        resolve(item);
                    });
                }
                
                function getSomething2(item) {
                    return new Promise(function(resolve) {
                        console.log('Something2 ', item);
                        resolve(item);
                    });
                }                
                
                function getSomething3(item) {
                    return new Promise(function(resolve) {
                        console.log('Something3 ', item);
                        resolve(item);
                    });
                }
                
                var result = new Promise(function(resolve) {
                        console.log('Something');
                        resolve('Hello');
                });
                
                result
                .then(function(data) { return getSomething1(data); })
                .then(function(data) { return getSomething2(data); })
                .then(function(data) { return getSomething3(data); });
                */
                
                /*
                // テストその4
                function something1(item) {
                    return new Promise(function(resolve) {
                        setTimeout(function() {
                            resolve('1つ目の処理');
                        }, 1000)
                    });
                }
                
                function something2(item) {
                    return new Promise(function(resolve) {
                        setTimeout(function() {
                            resolve('2つ目の処理');
                        }, 2000)
                    });
                }
                
                function something3(item) {
                    return new Promise(function(resolve) {
                        setTimeout(function() {
                            resolve('3つ目の処理');
                        }, 3000)
                    });
                }
                
                Promise.all([
                    something1(),
                    something2(),
                    something3(),
                ])
                .then(function(data) {
                    console.log(data);
                    console.log('すべての処理が終了しました！');
                });
                */
                
                /*
                // テストその5
                // GitHubからJavaScriptのリポジトリ総数を取得するURL
                var url = 'https://api.github.com/search/repositories?q=javascript';
 
                function get(url) {
                    return new Promise(function(resolve) {
                        var xhr = new XMLHttpRequest();
                        
                        xhr.open('GET', url);
                        xhr.send();
    
                        xhr.onreadystatechange = function() {
                            if(xhr.readyState === 4 && xhr.status === 200) {
                                var result = JSON.parse(xhr.responseText);
                                resolve( result.total_count );
                            }
                        }
                    });
                }
                
                get(url)
                .then(function(response) {
                    console.log("成功！", response); 
                });
                */
                
                // テストその6
                function getYear(date) {
                    var year = date.getFullYear();
                    console.log('year=', year);
                }
                
                var result = new Promise(function(resolve, reject) {
                    console.log('Promise');  // result.then();がなくても、ここは実行される。
                    var date = new Date;
                    
                    let isOK = true;
                    if (isOK) {
                        resolve(date);
                    } else {
                        reject('エラーです！');
                    }
                });
                
                result
                .then(function (date) {
                   getYear(date); 
                }, function (error) {
                    // エラー処理を記述する
                   console.error(error); 
                });
                
                console.log('result= ', result);
            },
            
            // ファイルのダウンロードの例11用
            testAwait: function () {
                function sampleResolve(value) {
                    return new Promise(resolve => {
                        setTimeout(() => {
                            resolve(value * 2);
                        }, 2000);
                    })
                }
                
                async function sample() {
                    const result = await sampleResolve(5);
                    console.log('await result= ', result);
                    return result + 5;
                }
                
                const result = sampleResolve(5);
                console.log('result= ', result);
                
                sample().then(result => {
                    console.log(result); // => 15
                });
            },
            
            // ファイルのダウンロードの例12用 ← 「ファイルのダウンロードの例9用」を改造してみて、理解を深める。
            // XMLHttpRequestをfetchに変えてみたが、うまくいったようだ。
            downloadImages2: async function () {
                // 画像の一括ダウンロード
                
                // 画像 URL
                const sources = [
                    'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089060/stickynote_uploads/images/mfvdex5nsup2hgqmw8yq.jpg',
                    'https://res.cloudinary.com/dreevk9fx/image/upload/v1623089081/stickynote_uploads/images/txr3igl4epq7wzyrbjrn.jpg',
                    'https://res.cloudinary.com/dreevk9fx/video/upload/v1623212886/stickynote_uploads/videos/ytjglxzh93ogzesud97e.mp4',
                ];
                
                // JSZip に追加するために非同期リクエストを Promise で wrap
                const imagePromises = sources.map(
                    (src, i) => new Promise((resolve, reject) => {
                        fetch(src)
                            .then(response => {
                                return response.blob();  // Promiseを返す
                            })
                            .then(data => {  // データ
                                console.log(data);
                                // ファイル名とデータ返却
                                const fileName = src.slice(src.lastIndexOf("/") + 1);
                                resolve({ data: data, fileName: fileName });
                            })
                            .catch(error => {  // エラーの場合
                                // resolve でデータ無し
                                resolve({ data: null });
                            });
                    })
                );
                
                // すべての画像が取得できたら zip 生成
                const images = await Promise.all(imagePromises);
                
                // zip ファイルで画像をダウンロード
                let zip = new JSZip();
                
                // フォルダ作成
                const folderName = "sampleFolder";
                let folder = zip.folder(folderName);
                
                // フォルダ下に画像を格納
                images.forEach(image => {
                    if (image.data && image.fileName) {
                        folder.file(image.fileName, image.data);
                    }
                });
                
                // フォルダ下にテキストを格納
                const textData = `
{
  "id" : 123,
  "name" : "mochi",
  "favoriteFoods" : [
    "くさもち",
    "くるみもち",
    "道明寺"
  ]
}
                `;
                folder.file('sample.json', textData);
                
                // zip を生成
                zip.generateAsync({ type: "blob" }).then(blob => {
                    // ダウンロードリンクを 生成
                    let dlLink = document.createElement("a");
                
                    // blob から URL を生成
                    const dataUrl = URL.createObjectURL(blob);
                    dlLink.href = dataUrl;
                    dlLink.download = `${folderName}.zip`;
                
                    // 設置/クリック/削除
                    dlLink.style.display = 'none';
                    document.body.appendChild(dlLink);
                    dlLink.click();
                    document.body.removeChild(dlLink);
                });
            },

            // ファイルのインポートの例1用
            importTextFile: function () {
                // HTMLのinput要素を生成する。
                const input = document.createElement("input");
                input.type = "file";
                
                // ファイルが指定された後に行う処理を定義する。
                input.addEventListener("change", e => {
                    // ファイルの中身
                    var file = e.target.files[0];

                    var reader = new FileReader();

                    // ファイルの中身をテキストデータとして読み取る。
                    reader.readAsText(file);

                    // ファイルの中身が読み取られた後に行う処理を定義する。
                    reader.addEventListener("load", () => {
                        // ファイル名
                        var fileName = file.name;

                        // 読み込んだデータ（JSON形式の文字列）をJavaScriptオブジェクトに変換する。
                        var originalData = JSON.parse(reader.result);
                    
                        console.log(fileName);
                        console.log(reader.result);
                        console.log(originalData);
                    });
                });
                
                // 「ファイルを開く」ダイアログを表示する。
                input.style.display = 'none';
                document.body.appendChild(input);
                input.click();
                document.body.removeChild(input);
            },
                
                
            // ファイルのインポートの例2用
            importZipFile: function () {
                // HTMLのinput要素を生成する。
                const input = document.createElement("input");
                input.type = "file";
                input.accept = "application/zip"
                
                
                // ファイルが指定された後に行う処理を定義する。
                input.addEventListener("change", e => {
                    // ファイルの中身
                    var file = e.target.files[0];
                    
                    var zip = new JSZip();
                    
                    // ZIPの読み込み
                    zip.loadAsync(file)
                    .then(function(target) {
                        console.log(target);
                        
                        let jsonFileName = null;
                        let jpegFileNameArray = [];
                        
                        for(const key in target.files) {
                            const value = target.files[key];
                            console.log(key, value.name, value.dir);
                            
                            const extName = value.name.match(/[^.]+$/);
                            if (extName == 'json') {
                                jsonFileName = value.name;
                            } else if (extName == 'jpg' || extName == 'jpeg') {
                                jpegFileNameArray.push(value.name);
                            }
                        }
                        
                        // テキストファイルの読み込み
                        if (jsonFileName) {
                            const jsonFileContent = target.files[jsonFileName].async('string')
                            .then(function(data) {
                                console.log(data);
                                var originalData = JSON.parse(data);
                                console.log(originalData);
                                
                                // 画像ファイルの読み込み
                                const jpegFileDecompressFuncs = [];
                                for (let i=0; i<jpegFileNameArray.length; ++i) {
                                    jpegFileDecompressFuncs.push(target.files[jpegFileNameArray[i]].async('base64'));
                                }
                                
                                Promise.all(jpegFileDecompressFuncs)
                                .then(function(jpegFileDecompressData) {
                                    console.log(jpegFileDecompressData.length);
                                    for (let i=0; i<jpegFileDecompressData.length; ++i) {
                                        //console.log(jpegFileDecompressData[i]);

                                        // こうしたい
                                        //const imgUrl = "data:image/jpeg:base64," + jpegFileDecompressData[i];
                                        //console.log(imgUrl);
                                        
                                        // 上記のようにするにはmimeタイプを判明させる必要がある
                                        
                                        // 先頭150byteも取得すれば
                                        // 大方のシグネチャはカバーできるはず。
                                        // そう、医用画像のDICOMファイルもね。
                                        const header64 = jpegFileDecompressData[i].slice(0, 16);
                                        console.log(header64);
                                        const headerRaw = atob(header64);
                                        console.log(headerRaw);
                                        const header = '';
                                        for (let j=0; j<headerRaw.length; ++j) {
                                            header += headerRaw.charCodeAt(j).toString(16);
                                        }
                                        console.log(header);
                                        let mimeType = '';
                                        if (header.startsWith('ffd8ff')) {
                                            mimeType = 'image/jpeg';
                                        }
                                        const imgUrl = `data:${mimeType}:base64,`;
                                        console.log(imgUrl);
                                    }
                                });
                            });
                        }
                    });
                });
                
                // 「ファイルを開く」ダイアログを表示する。
                input.style.display = 'none';
                document.body.appendChild(input);
                input.click();
                document.body.removeChild(input);
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