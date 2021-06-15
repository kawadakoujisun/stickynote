/*
 * img要素追加
 */
function addImageElement(parentElem, imageURL, retryCount) {
    // console.log('addImageElement', imageURL, retryCount);
    
    // img要素を作成して画像をロードする
    new Promise((resolve, reject) => {
        const img = new Image();
        img.classList.add('sticker-content-item-image-inner-class');
        img.onload = () => resolve(img);
        img.onerror = (e) => reject(e);
        img.src = imageURL;
    })
    .then((img) => {
        // 画像のロードが終わったので、img要素を表示する
        // console.log('Image onload END', imageURL, retryCount);
        // console.log(img);
        
        parentElem.appendChild(img);
        // console.log(parentElem.innerHTML);
    })
    .catch((e) => {
        // 画像のロードでエラー発生
        console.log('Image onerror END', imageURL, retryCount);
        console.log(e);
        
        // リトライするか？
        if (retryCount > 0) {
            // リトライしてみる
            this.addImageElement(parentElem, imageURL, retryCount - 1);
        } else {
            // リトライしない
            parentElem.textContent = '画像読み込みエラー';
        }
    });
}

/*
 * video要素追加
 */
function addVideoElement(parentElem, videoURL, retryCount) {
    // console.log('addVideoElement', videoURL, retryCount);
    
    // video要素を作成して動画をロードする
    new Promise((resolve, reject) => {
        const video = document.createElement("video");
        
        video.classList.add('sticker-content-item-image-inner-class');
        
        // video.setAttribute('controls', true);
        // video.setAttribute('muted', true);  // この書き方だとミュートにならなかったのでコメントアウト
        // video.setAttribute('autoplay', true);
        // video.setAttribute('loop', true);
        video.controls = true;
        video.muted    = true;
        video.autoplay = true;
        video.loop     = true;     
        
        // video.addEventListener('loadeddata', () => resolve(video));
        video.addEventListener('canplay', () => resolve(video));
        
        video.addEventListener('error', (e) => reject(e));
        
        video.setAttribute('src', videoURL);
    })
    .then((video) => {
        // 動画のロードが終わったので、video要素を表示する
        // console.log('Video loadeddata, canplay, or canplaythrough END', videoURL, retryCount);
        // console.log(video);
        
        parentElem.appendChild(video);
        console.log(parentElem.innerHTML);
    })
    .catch((e) => {
        // 動画のロードでエラー発生
        console.log('Video error END', videoURL, retryCount);
        console.log(e);
        
        // リトライするか？
        if (retryCount > 0) {
            // リトライしてみる
            this.addVideoElement(parentElem, videoURL, retryCount - 1);
        } else {
            // リトライしない
            parentElem.textContent = '動画読み込みエラー';
        }
    });
}

export default {
    addImageElement,
    addVideoElement,
};
