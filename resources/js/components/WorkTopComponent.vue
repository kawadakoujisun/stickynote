<template>
    <div>
        <work-menu-bar
            @send-work-menu-bar-state-custom-event="onSendWorkMenuBarState"
        >
        </work-menu-bar>
        <work-mount
            v-bind:receive-work-mount-props="receiveWorkMountParam"
        >
        </work-mount>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                //
                // work-mountに渡すパラメータ
                //
                receiveWorkMountParam: {
                    isArrangementButtonClicked: 0,
                        // メニューのボタンを押すたびにwork-mountのwatchに引っかかるようにするための工夫
                    arrangementType: 'free',
                },
            };
        },
        
        methods: {
            onSendWorkMenuBarState: function (emitParam) {
                console.log('onSendWorkMenuBarState');
                
                if (emitParam.result != 'none') {
                    if (emitParam.result == 'arrangementTypeFree') {
                        this.receiveWorkMountParam.arrangementType = 'free';
                        this.updateIsArrangementButtonClicked();
                    } else if (emitParam.result == 'arrangementTypeSortedByTaskStartTime') {
                        this.receiveWorkMountParam.arrangementType = 'sortedByTaskStartTime';
                        this.updateIsArrangementButtonClicked();
                    } else if (emitParam.result == 'arrangementTypeSortedByTaskEndTime') {
                        this.receiveWorkMountParam.arrangementType = 'sortedByTaskEndTime';
                        this.updateIsArrangementButtonClicked();
                    }
                }
            },
            
            updateIsArrangementButtonClicked: function () {
                this.receiveWorkMountParam.isArrangementButtonClicked =
                    (this.receiveWorkMountParam.isArrangementButtonClicked + 1) % 3;
            },
        },
    };

</script>

<style scoped>
</style>
