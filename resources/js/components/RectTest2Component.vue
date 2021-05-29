<template>
    <div class="drag-frame" @mousedown.left="onMouseDownLeft" @mousemove.prevent="onMouseMove" @mouseleave="onMouseLeave" @mouseup.left="onMouseUpLeft">
        <div class="drag-and-drop" id="red-box" :style="rect_position_style" :class="{drag: isDrag}"></div>
        <!--
        <div class="drag-and-drop" id="blue-box"></div>
        <div class="drag-and-drop" id="yellow-box"></div>
        -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                rect_position_style: {
                    left: '0px',
                    top:  '0px',
                },
                rect_position: {
                    left: 0,
                    top:  0,
                },
                isDrag: false,
                state: 0,
            };
        },
        
        mounted() {
            const targetElement = this.$el;
            const targetRect = targetElement.getBoundingClientRect();
            const targetRectTop  = targetRect.top + window.pageYOffset;
            const targetRectLeft = targetRect.left + window.pageXOffset;
            console.log(targetElement);
            console.log(targetRect);
            console.log(this.rect_position);
            
            this.setRectPosition(targetRectTop, targetRectLeft);
        },
        
        methods: {
            onMouseDownLeft: function (e) {
                //console.log('DownLeft out');
                if (this.state == 0) {
                    console.log('DownLeft in');
                    this.state = 1;
                    this.isDrag = true;
                    
                    this.setRectPosition(e.pageY, e.pageX);
                }
            },
            onMouseMove: function (e) {
                //console.log('Move out');
                if (this.state == 1) {
                    console.log('Move in');
                    
                    this.setRectPosition(e.pageY, e.pageX);
                }
            },
            onMouseLeave: function (e) {
                //console.log('Leave out');
                if (this.state == 1) {
                    console.log('Leave in');
                    this.state = 0;
                    this.isDrag = false;
                }
            },
            onMouseUpLeft: function (e) {
                //console.log('UpLeft out');
                if (this.state == 1) {
                    console.log('UpLeft in');
                    this.state = 0;
                    this.isDrag = false;
                }
            },
            
            setRectPosition: function (rectTop, rectLeft) {
                // rectTop, rectLeftはワールド座標
                // this.rect_positionはワールド座標
                // this.rect_position_styleは親内の座標
                this.rect_position.top  = rectTop;
                this.rect_position.left = rectLeft;

                // はみ出し修正
                const targetElement = this.$el;
                const targetRect = targetElement.getBoundingClientRect();
                const targetRectTop    = targetRect.top + window.pageYOffset;
                const targetRectLeft   = targetRect.left + window.pageXOffset;
                const targetRectBottom = targetRect.bottom + window.pageYOffset;
                const targetRectRight  = targetRect.right + window.pageXOffset;
                console.log(targetElement);
                
                console.log(this.rect_position.top, targetRect.top, targetRect.bottom, targetRectTop, targetRectBottom);
            
                if (this.rect_position.top < targetRectTop)          this.rect_position.top = targetRectTop;
                if (this.rect_position.top + 200 > targetRectBottom) this.rect_position.top = targetRectBottom - 200;
                
                if (this.rect_position.left < targetRectLeft)        this.rect_position.left = targetRectLeft;
                if (this.rect_position.left + 200 > targetRectRight) this.rect_position.left = targetRectRight - 200;
            
                // style用に代入
                // 親のpositionがabsolute、これのpositionもabsoluteなので、これの座標は親の左上を起点としたものになる。
                this.rect_position_style.top  = `${this.rect_position.top - targetRectTop}px`;
                this.rect_position_style.left = `${this.rect_position.left - targetRectLeft}px`;
            },
        },
    }
</script>

<style scoped>
    .drag-frame {
        width:  800px;
        height: 800px;
        left:   300px;
        top:    1000px;
        position: absolute;
        border: 1px solid #000;
    }
    
    .drag-and-drop {
        width:  200px;
        height: 200px;
        cursor: move;
        position: absolute;
        z-index: 1000;
    }
    
    .drag {
        z-index: 1001;
    }
    
    #red-box {
        background-color: #ffaaaa;
    }
    
    #blue-box {
        background-color: #aaaaff;
    }
    
    #yellow-box {
        background-color: #ffffaa;
    }
</style>