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
                    arrangementType: 'free',  // arrangementTypeDefs
                },
                
                arrangementTypeDefs: [  // TODO(kawadakoujisun): この定義をしているところを1か所だけにしたい
                    'free',
                    'sortedByIndividualNumber',
                    'sortedByTaskStartTime',
                    'sortedByTaskEndTime',
                ],
            };
        },
        
        methods: {
            onSendWorkMenuBarState: function (emitParam) {
                console.log('onSendWorkMenuBarState');
                
                if (emitParam.result != 'none') {
                    if (emitParam.result == 'arrangementTypeFree') {
                        this.receiveWorkMountParam.arrangementType = 'free';
                        this.updateIsArrangementButtonClicked();
                    } else if (emitParam.result == 'arrangementTypeSortedByIndividualNumber') {
                        this.receiveWorkMountParam.arrangementType = 'sortedByIndividualNumber';
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
                    (this.receiveWorkMountParam.isArrangementButtonClicked + 1) % this.arrangementTypeDefs.length;
            },
        },
    };

</script>

<style scoped>
</style>
