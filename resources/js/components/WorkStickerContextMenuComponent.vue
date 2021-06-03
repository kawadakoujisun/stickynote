<template>
    <div
        v-show="isShow"
        class="sticker-context-menu-overlay-class"
        @click.left="onClickLeft"
    >
        <div
            class="sticker-context-menu-class"
            id="sticker-content-menu-id"
        >
            <p>context menu</p>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerContextMenuProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerContextMenuProps.isShow,
            };
        },
        
        watch: {
            'showStickerContextMenuProps.isShow': function(newValue, oldValue) {  // showStickerContextMenuProps: functionでは変更を検知できなかった。Objectの変更は検知できないのだろうか？
                this.isShow = this.showStickerContextMenuProps.isShow;
                
                if (this.isShow) {
                    const contextMenuElem = document.getElementById('sticker-content-menu-id');
                    contextMenuElem.style.top  = `${this.showStickerContextMenuProps.mountPos.y}px`;
                    contextMenuElem.style.left = `${this.showStickerContextMenuProps.mountPos.x}px`;
                }
            },
        },
        
        methods: {
            onClickLeft: function (e) {
                const param = {
                    event: e,
                    dummy: '',
                };
                this.$emit('hide-sticker-context-menu-custom-event', param);
            }
        },
    };
</script>

<style scoped>
    .sticker-context-menu-overlay-class {
        position: absolute;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 1000;
        background: rgba(0, 0, 0, 0.0);
    }
    
    .sticker-context-menu-class {
        position: absolute;
        width:  150px;
        height: 300px;
        z-index: 1001;
        border: 1px solid #000;
        background-color: #aaaaaa;
        margin: 0;
        
        /* 外部から変更するもの */
        top:  300;
        left: 300;
    }
</style>