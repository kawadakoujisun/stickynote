<template>
    <div
        class="color-rect-mount-class"
        @mousemove.prevent="onMouseMove"
        @mouseleave="onMouseLeave"
        @mouseup.left="onMouseUpLeft"
    >
        <div
            v-for="(colorRect, index) in colorRects"
            v-bind:key="index"
            v-colorRectCustomDirective=colorRect
            class="color-rect-class"
            @mousedown.left="onChildMouseDownLeft"
        >
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                colorRects: [],
                
                // targetId: null,  // コメントアウトしているテストコードでしか使っていない
                
                // debugMouseDownCount: 0,
                // debugMouseMoveCount: 0,
                // debugMouseLeaveCount: 0,
                // debugMouseUpCount: 0,
                
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
                lastUpdateColorRectParam: {
                    id: null,
                    mountPos: {
                        x: null,  // px（数値だけで単位の文字列は付けていない）
                        y: null,
                    },
                },
            };
        },
        
        mounted() {
            const targetElement = this.$el;
            console.log('mounted ', targetElement);
            
            axios.get(window.laravel.asset + '/api/color-rects')
                .then(response => {
                    this.colorRects = response.data;
                });
        },
        
        directives: {
            colorRectCustomDirective: {
                bind: function (el, binding) {
                    let colorHex = '000000' + binding.value.color.toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    console.log('bind ', el, binding);
                    console.log(binding.value.pos_top, binding.value.pos_left);
                    console.log(colorHex);
                    
                    el.style.top  = `${binding.value.pos_top}px`;
                    el.style.left = `${binding.value.pos_left}px`;
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                    
                    // const idBaseName = this.getColorRectIdBaseName();  // directives内はthisが使えない？
                    const idBaseName = 'color-rect-id-';
                    el.id = `${idBaseName}${binding.value.id}`;
                    
                    const divTextElem = document.createElement('div');
                    divTextElem.innerHTML = `id=${binding.value.id}<br>background-color=#${colorHex}`;
                    el.appendChild(divTextElem);
                },
            },
        },
        
        methods: {
            /*
            // テストコード
            onChildMouseDownLeft: function (e) {
                if (this.targetId === null) {
                    console.log(e.target.id);
                    this.targetId = e.target.id;
                    
                    const targetElem = document.getElementById(this.targetId);
                    const targetElemRect = targetElem.getBoundingClientRect();
                    console.log(targetElemRect);
                    
                    const targetWorldRect = {};
                    targetWorldRect.top  = targetElemRect.top + window.pageYOffset;
                    targetWorldRect.left = targetElemRect.left + window.pageXOffset;
                    console.log(targetWorldRect);
                    
                    const parentElem = this.$el;
                    const parentElemRect = parentElem.getBoundingClientRect();
                    
                    const parentWorldRect = {};
                    parentWorldRect.top  = parentElemRect.top + window.pageYOffset;
                    parentWorldRect.left = parentElemRect.left + window.pageXOffset;
                    console.log(parentWorldRect);
                    
                    const parentBorder = 1;  // 親の枠線の太さ分
                    
                    const targetRectInParent = {};
                    targetRectInParent.top  = targetWorldRect.top - parentWorldRect.top - parentBorder;
                    targetRectInParent.left = targetWorldRect.left - parentWorldRect.left - parentBorder;
                    console.log(targetRectInParent);
                }
            },
            
            onMouseMove: function (e) {
                if (this.targetId) {
                }
            },
            
            onMouseLeave: function (e) {
                if (this.targetId) {
                    this.targetId = null;
                }
            },
            
            onMouseUpLeft: function (e) {
                if (this.targetId) {
                    this.targetId = null;
                }
            },
            */
            
            onChildMouseDownLeft: function (e) {
                // console.log('onChildMouseDownLeft', ++this.debugMouseDownCount, e, e.target, e.target.id);  return;
                
                if (this.targetElem === null) {
                    console.log('onChildMouseDownLeft', e, e.target, e.target.id);
                    
                    const idBaseName = this.getColorRectIdBaseName();
                    
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
                        this.targetElemMountPos.x = parseInt(this.targetElem.style.left);  // 単位を取り除く
                        this.targetElemMountPos.y = parseInt(this.targetElem.style.top);
                        
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
                            this.updateColorRect();
                        },
                        500);  // mili seconds
                        
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
            
            onMouseMove: function (e) {
                // console.log('onMouseMove', ++this.debugMouseMoveCount, e, e.target, e.target.id);  return;
                
                if (this.targetElem) {
                    this.targetElemMountPos.x = this.moveStartTargetElemMountPos.x + e.pageX - this.moveStartMousePagePos.x;
                    this.targetElemMountPos.y = this.moveStartTargetElemMountPos.y + e.pageY - this.moveStartMousePagePos.y;
                    
                    this.targetElemMountPos = this.modifyPosInMount(this.targetElemMountPos, this.targetElemSize);
                    
                    this.targetElem.style.top  = `${this.targetElemMountPos.y}px`;
                    this.targetElem.style.left = `${this.targetElemMountPos.x}px`;
                }
            },
            
            onMouseLeave: function (e) {
                // console.log('onMouseLeave', ++this.debugMouseLeaveCount, e, e.target, e.target.id);  return;
                
                if (this.targetElem) {
                    console.log('onMouseLeave');
                    this.releaseTargetElem();
                }
            },
            
            onMouseUpLeft: function (e) {
                // console.log('onMouseUpLeft', ++this.debugMouseUpCount, e, e.target, e.target.id);  return;
                
                if (this.targetElem) {
                    console.log('onMouseUpLeft');
                    this.releaseTargetElem();
                }
            },            
            
            releaseTargetElem() {
                this.updateColorRect();
                clearInterval(this.moveIntervalId);
                this.resetLastUpdateColorRectParam();
                this.targetElem = null;
            },
            
            updateColorRect: function () {
                const idBaseName = this.getColorRectIdBaseName();
                const id = this.targetElem.id.substr(idBaseName.length);
                const updateColorRectParam = {
                    id: id,
                    mountPos: this.targetElemMountPos,
                };
                
                const isEqual = this.isEqualToUpdateColorRectParam(this.lastUpdateColorRectParam, updateColorRectParam);
                console.log(isEqual, updateColorRectParam);
                if (isEqual) return;  // 同じなら更新しない
                this.setLastUpdateColorRectParam(updateColorRectParam);
                
                this.updateColorRectInner(updateColorRectParam);
            },
            
            // updateColorRectParam = {
            //     id: null,
            //     mountPos: {
            //         x: null,  // px（数値だけで単位の文字列は付けていない）
            //         y: null,
            //     },
            // };
            // mountPosは台紙内における座標。            
            updateColorRectInner: function (updateColorRectParam) {
                axios.put(window.laravel.asset + '/api/color-rects', {
                    updateColorRectParam: updateColorRectParam,
                })
                    .then(response => {
                        // 特にすることなし
                    });
            },
            
            isEqualToUpdateColorRectParam: function (paramA, paramB) {
                return (paramA.id === paramB.id
                    && paramA.mountPos.x === paramB.mountPos.x
                    && paramA.mountPos.y === paramB.mountPos.y); 
            },
            
            setLastUpdateColorRectParam: function (param) {
                // 代入に気を付ける
                this.lastUpdateColorRectParam.id = param.id;
                this.lastUpdateColorRectParam.mountPos.x = param.mountPos.x;
                this.lastUpdateColorRectParam.mountPos.y = param.mountPos.y;
            },
            
            resetLastUpdateColorRectParam: function () {
                // 代入に気を付ける
                const oldParam = {
                    id: this.lastUpdateColorRectParam.id,
                    mountPos: {
                        x: this.lastUpdateColorRectParam.mountPos.x,
                        y: this.lastUpdateColorRectParam.mountPos.y,
                    },
                };
                this.lastUpdateColorRectParam.id = null;
                this.lastUpdateColorRectParam.mountPos.x = null;
                this.lastUpdateColorRectParam.mountPos.y = null;
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
            
            getColorRectIdBaseName() {
                return 'color-rect-id-';
            },
            
            getMountBorderWidth() {
                return 1;  
            },
        },
    }
</script>

<style scoped>
    .color-rect-mount-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 2800px;
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 40px;
        padding: 0;
    }
    
    .color-rect-class {
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
