<template>
    <div
        class="mount-class"
        @mousemove.prevent="onMouseMove"
        @mouseleave="onMouseLeave"
        @mouseup.left="onMouseUpLeft"
    >
        <div
            v-for="(stickerParam, index) in stickerParams"
            v-bind:key="index"
            v-sticker-custom-directive=stickerParam
            class="sticker-class"
            @mousedown.left="onChildMouseDownLeft"
            @click.right.prevent="onChildClickRight"
        >
        </div>
        <work-sticker-context-menu
            v-bind:show-sticker-context-menu-props="showStickerContextMenuParam"
            @hide-sticker-context-menu-custom-event="onHideStickerContextMenu"
        >
        </work-sticker-context-menu>
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
                
                //
                // 選んだふせん
                //
                targetElem: null,
                targetElemMountPos: {  // targetElemがnullでないとき、有効な値が入っている。
                    x: 0,  // px（数値だけで単位の文字列は付けていない）
                    y: 0,
                },
                targetElemSize: {  // targetElemがnullでないとき、有効な値が入っている。
                    width:  0,  // px（数値だけで単位の文字列は付けていない）
                    height: 0,  // 枠の太さの分も込みの大きさ
                },
                moveStartTargetElemMountPos: {  // targetElemがnullでないとき、有効な値が入っている。
                    x: 0,  // px（数値だけで単位の文字列は付けていない）
                    y: 0,                    
                },
                moveStartMousePagePos: {  // targetElemがnullでないとき、有効な値が入っている。
                    x: 0,  // px（数値だけで単位の文字列は付けていない）
                    y: 0,
                },
                moveIntervalId: null,  // targetElemがnullでないとき、有効な値が入っている。
                lastUpdateTargetElemParam: {  // targetElemがnullでないとき、有効な値が入っている。
                                              // ただし、setIntervalに設定した関数が呼ばれる前は無効な値が入っているので、
                                              // idNoがnullでないことを確認すること。
                    idNo: null,  // 要素のidの文字列から抽出した数値
                    mountPos: {
                        x: 0,  // px（数値だけで単位の文字列は付けていない）
                        y: 0,
                    },
                },

                //
                // ふせんのコンテキストメニューに渡すパラメータ
                //                
                showStickerContextMenuParam: {
                    isShow: false,
                    mountPos: {
                        x: 0,
                        y: 0,
                    },
                },
            };
        },
        
        mounted() {
            axios.get(window.laravel.asset + '/api/work-mount')
                .then(response => {
                    console.log('axios.get');
                    
                    this.stickerParams = response.data;
                });
                
            window.Echo.private('sticker-info-item-pos-update-channel.' + window.laravel.user['id'])
                .listen('StickerInfoItemPosUpdate', response => {
                    console.log('window.Echo.private listen');
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${response.eventParam.id}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        if (this.targetElem !== updateElem) {  // 操作中の要素でなかったら更新する
                            updateElem.style.top  = `${response.eventParam.pos_top}px`;
                            updateElem.style.left = `${response.eventParam.pos_left}px`;
                        }
                    }
                });
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    const stickerParam = binding.value;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    el.style.top  = `${stickerParam['pos_top']}px`;
                    el.style.left = `${stickerParam['pos_left']}px`;
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                    
                    // const idBaseName = this.getStickerIdBaseName();  // directives内はthisが使えない？
                    const idBaseName = 'sticker-id-';                   // 調べている時間がないので直書きしておく。
                    el.id = `${idBaseName}${stickerParam['id']}`;
                    
                    const texts = stickerParam['texts'];
                    if (texts) {
                        for (let i = 0; i < texts.length; ++i) {
                            const divTextElem = document.createElement('div');
                            divTextElem.innerHTML = texts[i];  // TODO: html構文をそのまま出力して！
                            el.appendChild(divTextElem);
                        }
                    }
                },
            },
        },
        
        methods: {
            onChildMouseDownLeft: function (e) {
                if (this.targetElem === null) {
                    const idBaseName = this.getStickerIdBaseName();
                    
                    let target = e.target;
                    while (target) {
                        if (target.id) {
                            if (target.id.substr(0, idBaseName.length) == idBaseName) {
                                this.targetElem = target;
                                break;
                            }
                        }
                        // 子要素も@mousedown.leftに反応するので、親要素を見に行かなければならない。
                        target = target.parentElement;
                    }
                    
                    if (this.targetElem) {
                        // ターゲットの台紙内における座標
                        this.targetElemMountPos.x = parseInt(this.targetElem.style.left, 10);  // 単位を取り除く
                        this.targetElemMountPos.y = parseInt(this.targetElem.style.top, 10);
                        
                        // ターゲットの現在の画面内における座標
                        const targetElemRect = this.targetElem.getBoundingClientRect();
                        // ターゲットのサイズ
                        this.targetElemSize.width  = targetElemRect.width;   // 画面内のどこにあっても大きさは変わらない
                        this.targetElemSize.height = targetElemRect.height;  // 枠の太さの分も込みの大きさ
                        
                        this.moveStartTargetElemMountPos.x = this.targetElemMountPos.x;
                        this.moveStartTargetElemMountPos.y = this.targetElemMountPos.y;
                        
                        this.moveStartMousePagePos.x = e.pageX;
                        this.moveStartMousePagePos.y = e.pageY;
                        
                        this.moveIntervalId = setInterval(() => {
                            this.updateTargetElem();
                        },
                        500);  // mili seconds
                        //100);  // 100ミリ秒だと「429 Too Many Requests」エラーになってしまう
                        
                        // 確認
                        // ターゲットのページ内における座標
                        const targetElemPagePos = {};
                        targetElemPagePos.x = targetElemRect.left + window.pageXOffset;
                        targetElemPagePos.y = targetElemRect.top + window.pageYOffset;
                        // ターゲットの台紙内における座標
                        const convertedTargetElemMountPos = this.convertPosFromPageToMount(targetElemPagePos);
                        console.log(target.id, targetElemRect, convertedTargetElemMountPos, this.targetElemMountPos);
                    }
                }
            },
            
            onChildClickRight: function (e) {
                const pagePos = {};
                pagePos.x = e.pageX;
                pagePos.y = e.pageY;
                const mountPos = this.convertPosFromPageToMount(pagePos);
                
                this.showStickerContextMenuParam.isShow = true;
                this.showStickerContextMenuParam.mountPos.x = mountPos.x;
                this.showStickerContextMenuParam.mountPos.y = mountPos.y;
            },
            
            onMouseMove: function (e) {
                if (this.targetElem) {
                    this.targetElemMountPos.x = this.moveStartTargetElemMountPos.x + e.pageX - this.moveStartMousePagePos.x;
                    this.targetElemMountPos.y = this.moveStartTargetElemMountPos.y + e.pageY - this.moveStartMousePagePos.y;
                    
                    this.targetElemMountPos = this.modifyPosInMount(this.targetElemMountPos, this.targetElemSize);
                    
                    this.targetElem.style.top  = `${this.targetElemMountPos.y}px`;
                    this.targetElem.style.left = `${this.targetElemMountPos.x}px`;
                }
            },
            
            onMouseLeave: function (e) {
                if (this.targetElem) {
                    console.log('onMouseLeave');
                    this.releaseTargetElem();
                }
            },
            
            onMouseUpLeft: function (e) {
                if (this.targetElem) {
                    console.log('onMouseUpLeft');
                    this.releaseTargetElem();
                }
            },            
            
            onHideStickerContextMenu: function (param) {
                console.log('onHideStickerContextMenu', param.event);
                
                this.showStickerContextMenuParam.isShow = false;
                this.showStickerContextMenuParam.mountPos.x = 0;
                this.showStickerContextMenuParam.mountPos.y = 0;
            },
            
            releaseTargetElem: function () {
                this.updateTargetElem();
                clearInterval(this.moveIntervalId);
                this.resetLastUpdateTargetElemParam();
                this.targetElem = null;
            },
            
            updateTargetElem: function () {
                const idBaseName = this.getStickerIdBaseName();
                const idNo = this.targetElem.id.substr(idBaseName.length);
                const updateTargetElemParam = {
                    idNo: idNo,
                    mountPos: {
                        x: this.targetElemMountPos.x,
                        y: this.targetElemMountPos.y,
                    },
                };
                
                const isEqual = this.isEqualToUpdateTargetElemParam(this.lastUpdateTargetElemParam, updateTargetElemParam);
                if (isEqual) return;  // 同じなら更新しない
                this.setLastUpdateTargetElemParam(updateTargetElemParam);
                
                const param = {
                    id: updateTargetElemParam.idNo,
                    mountPos: {
                        x: updateTargetElemParam.mountPos.x,
                        y: updateTargetElemParam.mountPos.y,
                    },
                };
                this.updateStickerInfoItemPos(param);
            },
            
            // param = {
            //     id: null,
            //     mountPos: {
            //         x: null,  // px（数値だけで単位の文字列は付けていない）
            //         y: null,
            //     },
            // };
            // mountPosは台紙内における座標。            
            updateStickerInfoItemPos: function (param) {
                console.log('axios.put');
                
                axios.put(window.laravel.asset + '/api/work-sticker-info-item-pos-update', {
                    param: param,
                    user_id: window.laravel.user['id'],
                })
                    .then(response => {
                        // 特にすることなし
                    });
            },
            
            isEqualToUpdateTargetElemParam: function (paramA, paramB) {
                return (paramA.idNo === paramB.idNo
                    && paramA.mountPos.x === paramB.mountPos.x
                    && paramA.mountPos.y === paramB.mountPos.y); 
            },
            
            setLastUpdateTargetElemParam: function (param) {
                // 代入に気を付ける
                this.lastUpdateTargetElemParam.idNo = param.idNo;
                this.lastUpdateTargetElemParam.mountPos.x = param.mountPos.x;
                this.lastUpdateTargetElemParam.mountPos.y = param.mountPos.y;
            },
            
            resetLastUpdateTargetElemParam: function () {
                // 代入に気を付ける
                const oldParam = {
                    idNo: this.lastUpdateTargetElemParam.idNo,
                    mountPos: {
                        x: this.lastUpdateTargetElemParam.mountPos.x,
                        y: this.lastUpdateTargetElemParam.mountPos.y,
                    },
                };
                this.lastUpdateTargetElemParam.idNo = null;
                this.lastUpdateTargetElemParam.mountPos.x = 0;
                this.lastUpdateTargetElemParam.mountPos.y = 0;
                return oldParam;
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
            
            // mountPos = {
            //     x: 0,  // px（数値だけで単位の文字列は付けていない）
            //     y: 0,
            // };
            // size = {
            //     width:  0,  // px（数値だけで単位の文字列は付けていない）
            //     height: 0,  // 枠の太さの分も込みの大きさにしておくのがいい
            // };
            // mountPosは台紙内における座標。
            // sizeは大きさ。
            // modifiedMountPosは台紙内における座標。
            modifyPosInMount: function (mountPos, size) {
                const modifiedMountPos = {
                    x: mountPos.x,
                    y: mountPos.y,
                };
                
                // 台紙の枠の太さ
                const mountBorderWidth = this.getMountBorderWidth();
                
                const mountElem = this.$el;
                // 台紙の現在の画面内における座標
                const mountElemRect = mountElem.getBoundingClientRect();
                // 台紙のサイズ
                const mountElemSize = {};
                mountElemSize.width  = mountElemRect.width  - mountBorderWidth * 2;  // 画面内のどこにあっても大きさは変わらない
                mountElemSize.height = mountElemRect.height - mountBorderWidth * 2;  // 枠の太さの分も込みの大きさなので、その分は引いておく
                
                if (mountPos.x < 0) modifiedMountPos.x = 0;
                if (mountPos.x + size.width > mountElemSize.width) modifiedMountPos.x = mountElemSize.width - size.width;
                if (mountPos.y < 0) modifiedMountPos.y = 0;
                if (mountPos.y + size.height > mountElemSize.height) modifiedMountPos.y = mountElemSize.height - size.height;
                
                return modifiedMountPos;
            },
            
            getStickerIdBaseName: function () {
                return 'sticker-id-';
            },
            
            getMountBorderWidth: function () {
                return 1;  
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
        margin: 40px;
        padding: 0;
    }
    
    .sticker-class {
        position: absolute;
        width:  200px;
        height: 200px;
        border: 1px solid #000;
        margin: 0;
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }
</style>
