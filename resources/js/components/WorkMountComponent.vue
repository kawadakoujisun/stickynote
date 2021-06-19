<template>
    <div
        class="mount-class"
        @mousemove.prevent="onMouseMove"
        @mouseleave="onMouseLeave"
        @mouseup.left="onMouseUpLeft"
    >
        <div
            v-for="(stickerParam, index) in stickerParams"
            v-bind:key="stickerParam.id"
            v-sticker-custom-directive="{ stickerParam: stickerParam, index: index }"
            class="sticker-class"
            @mousedown.left="onChildMouseDownLeft"
            @click.right.prevent="onChildClickRight"
        >
            <div
                class="sticker-inner-class"
            >
            </div>
        </div>
        <work-sticker-context-menu
            v-bind:show-sticker-context-menu-props="showStickerContextMenuParam"
            @hide-sticker-context-menu-custom-event="onHideStickerContextMenu"
        >
        </work-sticker-context-menu>
        <work-sticker-edit-window
            v-bind:show-sticker-edit-window-props="showStickerEditWindowParam"
            @hide-sticker-edit-window-custom-event="onHideStickerEditWindow"
        >
        </work-sticker-edit-window>
        <work-sticker-individual-number-change-window
            v-bind:show-sticker-individual-number-change-window-props="showStickerIndividualNumberChangeWindowParam"
            @hide-sticker-individual-number-change-window-custom-event="onHideStickerIndividualNumberChangeWindow"
        >
        </work-sticker-individual-number-change-window>
        <work-sticker-color-change-window
            v-bind:show-sticker-color-change-window-props="showStickerColorChangeWindowParam"
            @hide-sticker-color-change-window-custom-event="onHideStickerColorChangeWindow"
        >
        </work-sticker-color-change-window>
        <work-sticker-text-add-window
            v-bind:show-sticker-text-add-window-props="showStickerTextAddWindowParam"
            @hide-sticker-text-add-window-custom-event="onHideStickerTextAddWindow"
        >
        </work-sticker-text-add-window>
        <work-sticker-image-add-window
            v-bind:show-sticker-image-add-window-props="showStickerImageAddWindowParam"
            @hide-sticker-image-add-window-custom-event="onHideStickerImageAddWindow"
        >
        </work-sticker-image-add-window>
        <work-sticker-video-add-window
            v-bind:show-sticker-video-add-window-props="showStickerVideoAddWindowParam"
            @hide-sticker-video-add-window-custom-event="onHideStickerVideoAddWindow"
        >
        </work-sticker-video-add-window>        
        <work-sticker-task-time-add-window
            v-bind:show-sticker-task-time-add-window-props="showStickerTaskTimeAddWindowParam"
            @hide-sticker-task-time-add-window-custom-event="onHideStickerTaskTimeAddWindow"
        >
        </work-sticker-task-time-add-window>
    </div>
</template>

<script>
    import commonScript from '../ProjectWorkCommonScript.js';
    
    export default {
        components: {
            commonScript,    
        },
        
        props: {
            receiveWorkMountProps: Object,
        },        
        
        data() {
            return {
                //
                // 台紙に貼ってあるふせん
                //
                stickerParams: [],
                
                //
                // 配置
                //
                arrangementType: 'free',  // 'free', 'sortedByTaskStartTime', 'sortedByTaskEndTime'
                
                //
                // 選んだふせん
                //
                targetElem: null,
                reserveDestroyTargetElem: false,  // targetElemがnullでないとき、有効な値が入っている。
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
                    idNo: null,  // 要素のidの文字列から抽出した数値
                    mountPos: {
                        x: 0,
                        y: 0,
                    },
                },

                //
                // ふせんの編集ウィンドウに渡すパラメータ
                //
                showStickerEditWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                    stickerParam: null,
                },

                //
                // ふせんの個別番号を変更するウィンドウに渡すパラメータ
                //
                showStickerIndividualNumberChangeWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                    mainNumber: null,
                    subNumber:  null,
                },
                
                //
                // ふせんの色変更するウィンドウに渡すパラメータ
                //
                showStickerColorChangeWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                },
                
                //
                // ふせんにテキストを追加するウィンドウに渡すパラメータ
                //
                showStickerTextAddWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                },
                
                //
                // ふせんに画像を追加するウィンドウに渡すパラメータ
                //
                showStickerImageAddWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                },
                
                //
                // ふせんに動画を追加するウィンドウに渡すパラメータ
                //
                showStickerVideoAddWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                },
                
                //
                // ふせんに時刻を追加するウィンドウに渡すパラメータ
                //
                showStickerTaskTimeAddWindowParam: {
                    isShow: false,
                    idNo: null,  // 要素のidの文字列から抽出した数値
                    taskTimeType: null,  // 'taskStartTime' or 'taskEndTime'
                },                
            };
        },
        
        watch: {
            'receiveWorkMountProps.isArrangementButtonClicked': function(newValue, oldValue) {
                console.log('receiveWorkMountProps.arrangementType',
                    this.receiveWorkMountProps.isArrangementButtonClicked,
                    this.receiveWorkMountProps.arrangementType);
                
                // if (this.arrangementType != this.receiveWorkMountProps.arrangementType) {
                // リアルタイム並び替えは行わない。
                // ユーザーがメニューの配置で並び順を指定したときにだけ並び替えを実施する。
                // 既に並び替えているところに、新たにふせんを足したりitemを足したりしても並び替えは実施されない。
                // 最新の状態で並び替えを実施したい場合は、ユーザーがメニューの配置から
                // 今選んでいるのと同じ並び順を選ぶ必要がある。
                // 同じ並び順を選んだときに、再度並び替えを実施しなければならないので、
                // 条件 (this.arrangementType != this.receiveWorkMountProps.arrangementType) はナシにする。
                {
                    this.arrangementType = this.receiveWorkMountProps.arrangementType;
                    
                    // 配置変更
                    const mountElems = document.getElementsByClassName('mount-class');
                    const mountElem = mountElems[0];
                    
                    const idBaseName = this.getStickerIdBaseName();
                    
                    if (this.arrangementType == 'free') {
                        mountElem.classList.remove('mount-sorted-class');
                        
                        for (let stickerParam of this.stickerParams) {
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = `${stickerParam.pos_top}px`;
                                updateElem.style.left = `${stickerParam.pos_left}px`;
                                updateElem.classList.remove('sticker-sorted-class');
                            }
                        }
                    } else if (this.arrangementType == 'sortedByTaskStartTime') {
                        if (mountElem.classList.contains('mount-sorted-class') == false) {
                            mountElem.classList.add('mount-sorted-class');
                                // 同じclassを何度addしても1つしか追加されないようなので、
                                // containsで有無を事前に確認する必要はなさそうだが、念のため。
                        }

                        for (let stickerParam of this.stickerParams) {
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = 0;
                                updateElem.style.left = 0;
                                if (updateElem.classList.contains('sticker-sorted-class') == false) {
                                    updateElem.classList.add('sticker-sorted-class');
                                }
                            }
                        }
                        
                        // 並び替え
                        for (let stickerParam of this.stickerParams) {
                            let value0 = 10000;
                            let value1 = 0;
                            for (let content of stickerParam.contents) {
                                if (content.link.item_type == 4) {  // app/Sticker.phpで値を定義している
                                    value0 = content.item.year_value;
                                    value1 = content.item.month_value * 1000000
                                        + content.item.day_value * 10000
                                        + content.item.hour_value * 100
                                        + content.item.minute_value;
                                    break;
                                }
                            }
                            stickerParam.sortValue = {
                                value0: value0,
                                value1: value1,
                            };
                        }
                        
                        this.stickerParams.sort((a, b) => {
                            if (a.sortValue.value0 == b.sortValue.value0) {
                                if (a.sortValue.value1 == b.sortValue.value1) {
                                    return a.id - b.id;
                                } else {
                                    return a.sortValue.value1 - b.sortValue.value1;
                                }
                            } else {
                                return a.sortValue.value0 - b.sortValue.value0;
                            }
                        });
                        
                        console.log(this.stickerParams);
                    } else if (this.arrangementType == 'sortedByTaskEndTime') {
                        if (mountElem.classList.contains('mount-sorted-class') == false) {
                            mountElem.classList.add('mount-sorted-class');
                        }
                        
                        for (let stickerParam of this.stickerParams) {
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = 0;
                                updateElem.style.left = 0;
                                if (updateElem.classList.contains('sticker-sorted-class') == false) {
                                    updateElem.classList.add('sticker-sorted-class');
                                }
                            }
                        }
                        
                        // 並び替え
                        for (let stickerParam of this.stickerParams) {
                            let value0 = 10000;
                            let value1 = 0;
                            for (let content of stickerParam.contents) {
                                if (content.link.item_type == 5) {  // app/Sticker.phpで値を定義している
                                    value0 = content.item.year_value;
                                    value1 = content.item.month_value * 1000000
                                        + content.item.day_value * 10000
                                        + content.item.hour_value * 100
                                        + content.item.minute_value;
                                    break;
                                }
                            }
                            stickerParam.sortValue = {
                                value0: value0,
                                value1: value1,
                            };
                        }
                        
                        this.stickerParams.sort((a, b) => {
                            if (a.sortValue.value0 == b.sortValue.value0) {
                                if (a.sortValue.value1 == b.sortValue.value1) {
                                    return a.id - b.id;
                                } else {
                                    return a.sortValue.value1 - b.sortValue.value1;
                                }
                            } else {
                                return a.sortValue.value0 - b.sortValue.value0;
                            }
                        });
                        
                        console.log(this.stickerParams);
                    }
                }
            },
        },
        
        mounted() {
            console.log('mounted');
            
            axios.get(window.laravel.asset + '/api/work-mount')
                .then(response => {
                    console.log('axios.get');
                    
                    this.stickerParams = response.data;
                });
 
            window.Echo.private('sticky-note-import-channel.' + window.laravel.user['id'])
                .listen('StickyNoteImport', response => {
                    console.log('window.Echo.private sticky-note-import-channel listen');
                    
                    const importStickerParams = response.eventParam.stickerParams;
                    
                    console.log(this.stickerParams);
                    console.log(importStickerParams);
                    
                    // データ更新
                    const targetStickerParams = [];  // 要素なし or 操作中の要素のみ
                    
                    // 操作中の要素については今は更新しない
                    if (this.targetElem) {
                        // 削除の予約だけしておく
                        this.reserveDestroyTargetElem = true;                        
                        
                        // 操作中の要素はtargetStickerParams配列にいれておく
                        const idBaseName = this.getStickerIdBaseName();
                        const idNo = this.targetElem.id.substr(idBaseName.length);
                        
                        const index = this.getStickerParamIndex(idNo);
                        targetStickerParams.push(this.stickerParams[index]);
                    }
                    
                    // インポートしてきた配列をtargetStickerParams配列に結合する
                    this.stickerParams = targetStickerParams.concat(importStickerParams);
                    console.log(this.stickerParams);
                    
                    // 見た目更新
                    // this.stickerParamsを更新すると勝手に見た目の更新も行われたので、何もしなくてよい。
                });
                
            window.Echo.private('sticker-create-channel.' + window.laravel.user['id'])
                .listen('StickerCreate', response => {
                    console.log('window.Echo.private sticker-create-channel listen');
                    
                    // ふせんを追加する前に、既存のふせんのdepthを更新しておく

                    {
                    
                        const stickerDepths = response.eventParam.sticker_depths;
                        
                        const idBaseName = this.getStickerIdBaseName();
                        
                        for (let stickerDepth of stickerDepths) {
                            const idNo = stickerDepth.id;
                            const updateId = `${idBaseName}${idNo}`;
                            
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                const depth = stickerDepth.depth;
                                
                                // データ更新
                                const index = this.getStickerParamIndex(idNo);
                                if (index !== null) {
                                    this.stickerParams[index]['depth'] = depth;
                                }
                                
                                // 見た目更新
                                updateElem.style.zIndex = depth;  // z-index
                            }
                        }
                    }


                    // ふせんを追加する
                    
                    // データ更新
                    const stickerParam = {
        				'id'       : response.eventParam.id,
        				'individual_main_number' : response.eventParam.individual_main_number,
        				'individual_sub_number'  : response.eventParam.individual_sub_number,
        				'pos_top'  : response.eventParam.pos_top,
        				'pos_left' : response.eventParam.pos_left,
        				'depth'    : response.eventParam.depth,
        				'color'    : response.eventParam.color,
        				'contents' : [],  // 要素数0であっても必ず配列を設定します。
                    };
                    
                    this.stickerParams.push(stickerParam);
                    
                    // 見た目更新
                    // this.stickerParamsに追加すると勝手に見た目の更新も行われたので、何もしなくてよい。
                    // ↑
                    // directivesのsticker-custom-directiveのbindが呼ばれているおかげで見た目が更新される。
                    // 追加したstickerParamについてだけsticker-custom-directiveのbindが呼ばれている（既存のstickerParamについては呼ばれない）。
                    
                    // DOMの更新サイクル後に見た目更新
                    this.$nextTick(() => {
                        console.log('nextTick', stickerParam.id);
                        
                        if (this.arrangementType != 'free') {
                            const idBaseName = this.getStickerIdBaseName();
                            const updateId = `${idBaseName}${stickerParam.id}`;
                            const updateElem = document.getElementById(updateId);
                            
                            if (updateElem) {
                                updateElem.style.top  = 0;
                                updateElem.style.left = 0;
                                if (updateElem.classList.contains('sticker-sorted-class') == false) {
                                    updateElem.classList.add('sticker-sorted-class');
                                }
                            }
                        }
                    });
                });                
                
            window.Echo.private('sticker-destroy-channel.' + window.laravel.user['id'])
                .listen('StickerDestroy', response => {
                    console.log('window.Echo.private sticker-destroy-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    let destroyNow = true;
                    
                    if (this.targetElem) {
                        const idBaseName = this.getStickerIdBaseName();
                        const updateId = `${idBaseName}${idNo}`; 
                        
                        const updateElem = document.getElementById(updateId);
                        
                        if (updateElem) {
                            if (this.targetElem == updateElem) {  // 操作中の要素だったら今は更新しない
                                // 削除の予約だけしておく
                                this.reserveDestroyTargetElem = true;
                                destroyNow = false;
                            }
                        }
                    }
                    
                    if (destroyNow) {
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            this.stickerParams.splice(index, 1);
        
                            // 見た目更新
                            // this.stickerParamsから削除すると勝手に見た目の更新も行われたので、何もしなくてよい。
                            // ↑
                            // なぜ見た目が更新されたのかはよく分からない。
                            // v-bind:keyに設定している値でなくなったものを消してくれているようだ。
                        }
                    }
                });
                
            window.Echo.private('sticker-info-item-pos-update-channel.' + window.laravel.user['id'])
                .listen('StickerInfoItemPosUpdate', response => {
                    console.log('window.Echo.private sticker-info-item-pos-update-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        if (this.targetElem !== updateElem) {  // 操作中の要素でなかったら更新する
                            const posTop  = response.eventParam.pos_top;
                            const posLeft = response.eventParam.pos_left;
                        
                            // データ更新
                            const index = this.getStickerParamIndex(idNo);
                            if (index !== null) {
                                this.stickerParams[index]['pos_top']  = posTop;
                                this.stickerParams[index]['pos_left'] = posLeft;
                            }
                        
                            // 見た目更新
                            if (this.arrangementType == 'free') {
                                updateElem.style.top  = `${posTop}px`;
                                updateElem.style.left = `${posLeft}px`;
                            }
                        }
                    }
                });
                
            window.Echo.private('sticker-info-item-depth-update-channel.' + window.laravel.user['id'])
                .listen('StickerInfoItemDepthUpdate', response => {
                    console.log('window.Echo.private sticker-info-item-depth-update-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const depth = response.eventParam.depth;
                        
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            this.stickerParams[index]['depth'] = depth;
                        }
                        
                        // 見た目更新
                        updateElem.style.zIndex = depth;  // z-index
                    }
                });
                
            window.Echo.private('all-sticker-info-item-depth-update-channel.' + window.laravel.user['id'])
                .listen('AllStickerInfoItemDepthUpdate', response => {
                    console.log('window.Echo.private all-sticker-info-item-depth-update-channel listen');
                    
                    const stickerDepths = response.eventParam.sticker_depths;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    
                    for (let stickerDepth of stickerDepths) {
                        const idNo = stickerDepth.id;
                        const updateId = `${idBaseName}${idNo}`;
                        
                        const updateElem = document.getElementById(updateId);
                        
                        if (updateElem) {
                            const depth = stickerDepth.depth;
                            
                            // データ更新
                            const index = this.getStickerParamIndex(idNo);
                            if (index !== null) {
                                this.stickerParams[index]['depth'] = depth;
                            }
                            
                            // 見た目更新
                            updateElem.style.zIndex = depth;  // z-index
                        }
                    }
                });
                
            window.Echo.private('all-sticker-info-item-individual-number-update-channel.' + window.laravel.user['id'])
                .listen('AllStickerInfoItemIndividualNumberUpdate', response => {
                    console.log('window.Echo.private all-sticker-info-item-individual-number-update-channel listen');
                    
                    const stickerIndividualNumbers = response.eventParam.sticker_individual_numbers;

                    const individualNumberIdBaseName = this.getIndividualNumberIdBaseName();
                    
                    for (let stickerIndividualNumber of stickerIndividualNumbers) {
                        const idNo = stickerIndividualNumber.id;
                        const updateId = `${individualNumberIdBaseName}${idNo}`;

                        const updateElem = document.getElementById(updateId);
                        
                        if (updateElem) {
                            // データ更新
                            const index = this.getStickerParamIndex(idNo);
                            if (index !== null) {
                                this.stickerParams[index]['individual_main_number'] = stickerIndividualNumber.individual_main_number;
                                this.stickerParams[index]['individual_sub_number']  = stickerIndividualNumber.individual_sub_number;
                            }
                            
                            // 見た目更新
                            commonScript.addIndividualNumber(updateElem, stickerIndividualNumber.individual_main_number, stickerIndividualNumber.individual_sub_number);
                        }
                    }
                });
                
            window.Echo.private('sticker-info-item-color-update-channel.' + window.laravel.user['id'])
                .listen('StickerInfoItemColorUpdate', response => {
                    console.log('window.Echo.private sticker-info-item-color-update-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const color = response.eventParam.color;
                        
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            this.stickerParams[index]['color'] = color;
                        }
                        
                        // 見た目更新
                        let colorHex = '000000' + color.toString(16);
                        colorHex = colorHex.substr(colorHex.length - 6);
                        updateElem.style.backgroundColor = '#'+colorHex;  // background-color
                    }
                });
                
            window.Echo.private('sticker-content-item-text-create-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemTextCreate', response => {
                    console.log('window.Echo.private sticker-content-item-text-create-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const text = response.eventParam.text;
                        const contentLinkIdNo = response.eventParam.content_link_id;
                        
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            const contents = this.stickerParams[index]['contents'];  // JavaScriptの配列は参照渡し
                            
                            const content = {
                                link: {
                                    id:        contentLinkIdNo,
                                    item_type: response.eventParam.content_item_type,
                                    item_id:   response.eventParam.content_item_id,
                                },
                                item: {
                                    text: text,
                                },
                            };
                            
                            contents.push(content);  // TODO(kawadakoujisun): id順に並び替える必要あるかも。見た目のdivの並びも。
                        }
                        
                        const divStickerInnerElems = updateElem.getElementsByClassName('sticker-inner-class');
                        const divStickerInnerElem = divStickerInnerElems[0];
                        
                        // 見た目更新
                        const divItemElem = document.createElement('div');
                        
                        const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                        divItemElem.id = `${contentLinkIdBaseName}${contentLinkIdNo}`;
                        
                        divItemElem.classList.add('sticker-content-item-text-outer-class');
                        divItemElem.innerText = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                        divStickerInnerElem.appendChild(divItemElem);
                    }
                });
                
            window.Echo.private('sticker-content-item-text-destroy-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemTextDestroy', response => {
                    console.log('window.Echo.private sticker-content-item-text-destroy-channel listen');
                    
                    this.removeStickerContentItem(response.eventParam);
                });

            window.Echo.private('sticker-content-item-image-create-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemImageCreate', response => {
                    console.log('window.Echo.private sticker-content-item-image-create-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const imageURL      = response.eventParam.image_url;
                        const imagePublicId = response.eventParam.image_public_id;
                        const contentLinkIdNo = response.eventParam.content_link_id;
                        
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            const contents = this.stickerParams[index]['contents'];  // JavaScriptの配列は参照渡し
                            
                            const content = {
                                link: {
                                    id:        contentLinkIdNo,
                                    item_type: response.eventParam.content_item_type,
                                    item_id:   response.eventParam.content_item_id,
                                },
                                item: {
                                    image_url:       imageURL,
                                    image_public_id: imagePublicId,
                                },
                            };
                            
                            contents.push(content);  // TODO(kawadakoujisun): id順に並び替える必要あるかも。見た目のdivの並びも。
                        }
                        
                        const divStickerInnerElems = updateElem.getElementsByClassName('sticker-inner-class');
                        const divStickerInnerElem = divStickerInnerElems[0];
                    
                        // 見た目更新
                        const divItemElem = document.createElement('div');
                        
                        const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                        divItemElem.id = `${contentLinkIdBaseName}${contentLinkIdNo}`;
                        
                        divItemElem.classList.add('sticker-content-item-image-outer-class');
                        // divItemElem.innerHTML = `<img class="sticker-content-item-image-inner-class" src="${imageURL}" >`;
                        // console.log(divItemElem.innerHTML);
                        divStickerInnerElem.appendChild(divItemElem);
                        
                        // img要素追加
                        commonScript.addImageElement(divItemElem, imageURL, 1);
                    }
                });
                
            window.Echo.private('sticker-content-item-image-destroy-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemImageDestroy', response => {
                    console.log('window.Echo.private sticker-content-item-image-destroy-channel listen');
                    
                    this.removeStickerContentItem(response.eventParam);
                });
                
            window.Echo.private('sticker-content-item-video-create-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemVideoCreate', response => {
                    console.log('window.Echo.private sticker-content-item-video-create-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const videoURL      = response.eventParam.video_url;
                        const videoPublicId = response.eventParam.video_public_id;
                        const contentLinkIdNo = response.eventParam.content_link_id;
                        
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            const contents = this.stickerParams[index]['contents'];  // JavaScriptの配列は参照渡し
                            
                            const content = {
                                link: {
                                    id:        contentLinkIdNo,
                                    item_type: response.eventParam.content_item_type,
                                    item_id:   response.eventParam.content_item_id,
                                },
                                item: {
                                    video_url:       videoURL,
                                    video_public_id: videoPublicId,
                                },
                            };
                            
                            contents.push(content);  // TODO(kawadakoujisun): id順に並び替える必要あるかも。見た目のdivの並びも。
                        }
                        
                        const divStickerInnerElems = updateElem.getElementsByClassName('sticker-inner-class');
                        const divStickerInnerElem = divStickerInnerElems[0];
                        
                        // 見た目更新
                        const divItemElem = document.createElement('div');
                        
                        const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                        divItemElem.id = `${contentLinkIdBaseName}${contentLinkIdNo}`;
                        
                        divItemElem.classList.add('sticker-content-item-image-outer-class');
                        // divItemElem.innerHTML = `<video class="sticker-content-item-image-inner-class" src="${videoURL}" controls autoplay loop></video>`;
                        // console.log(divItemElem.innerHTML);
                        divStickerInnerElem.appendChild(divItemElem);
                        
                        // video要素追加
                        commonScript.addVideoElement(divItemElem, videoURL, 1);
                    }
                });
                
            window.Echo.private('sticker-content-item-video-destroy-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemVideoDestroy', response => {
                    console.log('window.Echo.private sticker-content-item-video-destroy-channel listen');
                    
                    this.removeStickerContentItem(response.eventParam);
                });
                
            window.Echo.private('sticker-content-item-task-time-create-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemTaskTimeCreate', response => {
                    console.log('window.Echo.private sticker-content-item-task-time-create-channel listen');
                    
                    const idNo = response.eventParam.id;
                    
                    const idBaseName = this.getStickerIdBaseName();
                    const updateId = `${idBaseName}${idNo}`; 
                    
                    const updateElem = document.getElementById(updateId);
                    
                    if (updateElem) {
                        const contentLinkIdNo = response.eventParam.content_link_id;
                        
                        const content = {
                            link: {
                                id:        contentLinkIdNo,
                                item_type: response.eventParam.content_item_type,
                                item_id:   response.eventParam.content_item_id,
                            },
                            item: {
                                time_zone_type: response.eventParam.time_zone_type,
                                year_value:     response.eventParam.year_value,
                                month_value:    response.eventParam.month_value,
                                day_value:      response.eventParam.day_value,
                                hour_value:     response.eventParam.hour_value,
                                minute_value:   response.eventParam.minute_value,
                            },
                        };
                            
                        // データ更新
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            const contents = this.stickerParams[index]['contents'];  // JavaScriptの配列は参照渡し
                            contents.push(content);  // TODO(kawadakoujisun): id順に並び替える必要あるかも。見た目のdivの並びも。
                        }
                        
                        const divStickerInnerElems = updateElem.getElementsByClassName('sticker-inner-class');
                        const divStickerInnerElem = divStickerInnerElems[0];
                        
                        // 見た目更新
                        const divItemElem = document.createElement('div');
                        
                        const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                        divItemElem.id = `${contentLinkIdBaseName}${contentLinkIdNo}`;
                        
                        divItemElem.classList.add('sticker-content-item-text-outer-class');
                        commonScript.addTaskTimeText(divItemElem, content);
                        divStickerInnerElem.appendChild(divItemElem);
                    }
                });
                
            window.Echo.private('sticker-content-item-task-time-destroy-channel.' + window.laravel.user['id'])
                .listen('StickerContentItemTaskTimeDestroy', response => {
                    console.log('window.Echo.private sticker-content-item-task-time-destroy-channel listen');
                    
                    this.removeStickerContentItem(response.eventParam);
                });
        },
        
        directives: {
            'sticker-custom-directive': {
                bind: function (el, binding) {
                    console.log('sticker-custom-directive bind', binding.value.index);
                    
                    const stickerParam = binding.value.stickerParam;
                    
                    let colorHex = '000000' + stickerParam['color'].toString(16);
                    colorHex = colorHex.substr(colorHex.length - 6);
                    
                    el.style.top  = `${stickerParam['pos_top']}px`;
                    el.style.left = `${stickerParam['pos_left']}px`;
                    el.style.zIndex = stickerParam['depth'];  // z-index
                    el.style.backgroundColor = '#'+colorHex;  // background-color
                    
                    // const idBaseName = this.getStickerIdBaseName();  // directives内はthisが使えない？
                    const idBaseName = 'sticker-id-';                   // 調べている時間がないので直書きしておく。
                    el.id = `${idBaseName}${stickerParam['id']}`;
                    
                    const divStickerInnerElems = el.getElementsByClassName('sticker-inner-class');
                    const divStickerInnerElem = divStickerInnerElems[0];
                    
                    // individualNumber
                    {
                        // const individualNumberIdBaseName = this.getIndividualNumberIdBaseName();
                        const individualNumberIdBaseName = 'individual-number-id-';  // 直書き
                        
                        const divItemElem = document.createElement('div');
                        divItemElem.id = `${individualNumberIdBaseName}${stickerParam['id']}`;  // sticker1つにつき1つだけなので、stickerのidでいい。
                        divStickerInnerElem.appendChild(divItemElem);
                        
                        divItemElem.classList.add('sticker-info-item-individual-number-outer-class');
                        commonScript.addIndividualNumber(divItemElem, stickerParam['individual_main_number'], stickerParam['individual_sub_number']);
                    }
                    
                    // const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                    const contentLinkIdBaseName = 'content-link-id-';  // 直書き
                            
                    const contents = stickerParam['contents'];
                    for (let i = 0; i < contents.length; ++i) {
                        const content = contents[i];
                        
                        const divItemElem = document.createElement('div');
                        divItemElem.id = `${contentLinkIdBaseName}${content['link'].id}`;
                        divStickerInnerElem.appendChild(divItemElem);
                        
                        if (content['link'].item_type == 1) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-text-outer-class');
                            const text = content['item']['text'];
                            divItemElem.innerText = text;  // TODO(kawadakoujisun): html構文をそのまま出力して！
                        } else if (content['link'].item_type == 2) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-image-outer-class');
                            const imageURL = content['item']['image_url'];
                            // divItemElem.innerHTML = `<img class="sticker-content-item-image-inner-class" src="${imageURL}">`;
                            
                            // img要素追加
                            commonScript.addImageElement(divItemElem, imageURL, 1);
                        } else if (content['link'].item_type == 3) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-image-outer-class');
                            const videoURL = content['item']['video_url'];
                            // divItemElem.innerHTML = `<video class="sticker-content-item-image-inner-class" src="${videoURL}" controls autoplay loop></video>`;
                            
                            // video要素追加
                            commonScript.addVideoElement(divItemElem, videoURL, 1);
                        } else if (content['link'].item_type == 4 || content['link'].item_type == 5) {  // app/Sticker.phpで値を定義している
                            divItemElem.classList.add('sticker-content-item-text-outer-class');
                            commonScript.addTaskTimeText(divItemElem, content);
                        }
                    }
                },

                inserted: function(el, binding) {
                    console.log('sticker-custom-directive inserted', binding.value.index);
                },
                
                update: function (el, binding) {
                    console.log('sticker-custom-directive update', binding.value.index);
                },
                
                componentUpdated: function (el, binding) {
                    console.log('sticker-custom-directive componentUpdated', binding.value.index);
                },
            },
        },
        
        methods: {
            onChildMouseDownLeft: function (e) {
                if (this.arrangementType != 'free') return;
                
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
                const idBaseName = this.getStickerIdBaseName();
                    
                let idNo = null;
                let target = e.target;
                while (target) {
                    if (target.id) {
                        if (target.id.substr(0, idBaseName.length) == idBaseName) {
                            idNo = target.id.substr(idBaseName.length);
                            break;
                        }
                    }
                    // 子要素も@mousedown.leftに反応するので、親要素を見に行かなければならない。
                    target = target.parentElement;
                }
                
                const pagePos = {};
                pagePos.x = e.pageX;
                pagePos.y = e.pageY;
                const mountPos = this.convertPosFromPageToMount(pagePos);
                
                this.showStickerContextMenuParam.isShow = true;
                this.showStickerContextMenuParam.idNo = idNo;
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
            
            onHideStickerContextMenu: function (emitParam) {
                console.log('onHideStickerContextMenu', emitParam.event);
                
                const idNo = this.showStickerContextMenuParam.idNo;
                
                this.showStickerContextMenuParam.isShow = false;
                this.showStickerContextMenuParam.idNo = null;
                this.showStickerContextMenuParam.mountPos.x = 0;
                this.showStickerContextMenuParam.mountPos.y = 0;
                
                if (emitParam.result != 'none') {
                    if (emitParam.result == 'openStickerEditWindow') {
                        this.showStickerEditWindowParam.isShow = true;
                        this.showStickerEditWindowParam.idNo = idNo;
                        
                        this.showStickerEditWindowParam.stickerParam = null;
                        const index = this.getStickerParamIndex(idNo);
                        if (index !== null) {
                            this.showStickerEditWindowParam.stickerParam = this.copyStickerParam(this.stickerParams[index]);
                            // this.stickerParams[index]の参照を渡している場合は、
                            // this.showStickerEditWindowParam.stickerParamを使っているときに
                            // this.stickerParams[index]を削除されると困る。
                            // だから、this.stickerParams[index]をコピーした別物を渡すようにした。
                        }
                    } else if(emitParam.result == 'increaseDepth') {
                        // ここに来る前にふせんを前面へ移動しているので、ここでは何もしない
                    } else if(emitParam.result == 'decreaseDepth') {
                        // ここに来る前にふせんを背面へ移動しているので、ここでは何もしない
                    } else if(emitParam.result == 'changeDepthFrontMost') {
                        // ここに来る前にふせんを最前面へ移動しているので、ここでは何もしない
                    } else if(emitParam.result == 'changeDepthBackMost') {
                        // ここに来る前にふせんを最背面へ移動しているので、ここでは何もしない    
                    } else if(emitParam.result == 'destroySticker') {
                        // ここに来る前にふせんを削除しているので、ここでは何もしない
                    }
                }
            },

            onHideStickerEditWindow: function (emitParam) {
                console.log('onHideStickerEditWindow', emitParam.event);
                
                const idNo = this.showStickerEditWindowParam.idNo;
                
                this.showStickerEditWindowParam.isShow = false;
                this.showStickerEditWindowParam.idNo = null;
                this.showStickerEditWindowParam.stickerParam = null;
                
                if (emitParam.result != 'none') {
                    // stickerParamを取得しておく
                    let stickerParam = null;
                    const index = this.getStickerParamIndex(idNo);
                    if (index !== null) {
                        stickerParam = this.stickerParams[index];
                    }
                    
                    // emitParam.resultで分岐
                    if (emitParam.result == 'removeText') {
                        // ここに来る前にテキストを削除しているので、ここでは何もしない
                    } else if (emitParam.result == 'removeImage') {
                        // ここに来る前に画像を削除しているので、ここでは何もしない
                    } else if (emitParam.result == 'removeVideo') {
                        // ここに来る前に動画を削除しているので、ここでは何もしない
                    } else if (emitParam.result == 'removeTaskTime') {
                        // ここに来る前に時刻を削除しているので、ここでは何もしない
                    } else if (emitParam.result == 'openStickerIndividualNumberChangeWindow') {
                        this.showStickerIndividualNumberChangeWindowParam.isShow = true;
                        this.showStickerIndividualNumberChangeWindowParam.idNo = idNo;
                        
                        this.showStickerIndividualNumberChangeWindowParam.mainNumber = null;
                        this.showStickerIndividualNumberChangeWindowParam.subNumber  = null;
                        if (stickerParam) {
                            this.showStickerIndividualNumberChangeWindowParam.mainNumber = stickerParam.individual_main_number;
                            this.showStickerIndividualNumberChangeWindowParam.subNumber  = stickerParam.individual_sub_number;
                        }
                    } else if (emitParam.result == 'openStickerColorChangeWindow') {
                        this.showStickerColorChangeWindowParam.isShow = true;
                        this.showStickerColorChangeWindowParam.idNo = idNo;
                    } else if (emitParam.result == 'openStickerTextAddWindow') {
                        this.showStickerTextAddWindowParam.isShow = true;
                        this.showStickerTextAddWindowParam.idNo = idNo;
                    } else if (emitParam.result == 'openStickerImageAddWindow') {
                        this.showStickerImageAddWindowParam.isShow = true;
                        this.showStickerImageAddWindowParam.idNo = idNo;
                    } else if (emitParam.result == 'openStickerVideoAddWindow') {
                        this.showStickerVideoAddWindowParam.isShow = true;
                        this.showStickerVideoAddWindowParam.idNo = idNo;
                    } else if (emitParam.result == 'openStickerTaskStartTimeAddWindow') {
                        this.showStickerTaskTimeAddWindowParam.isShow = true;
                        this.showStickerTaskTimeAddWindowParam.idNo = idNo;
                        this.showStickerTaskTimeAddWindowParam.taskTimeType = 'taskStartTime';
                    } else if (emitParam.result == 'openStickerTaskEndTimeAddWindow') {
                        this.showStickerTaskTimeAddWindowParam.isShow = true;
                        this.showStickerTaskTimeAddWindowParam.idNo = idNo;
                        this.showStickerTaskTimeAddWindowParam.taskTimeType = 'taskEndTime';                        
                    }
                }
            },

            onHideStickerIndividualNumberChangeWindow: function (emitParam) {
                console.log('onHideStickerIndividualNumberChangeWindow', emitParam.event);
                
                this.showStickerIndividualNumberChangeWindowParam.isShow = false;
                this.showStickerIndividualNumberChangeWindowParam.idNo = null;
                this.showStickerIndividualNumberChangeWindowParam.mainNumber = null;
                this.showStickerIndividualNumberChangeWindowParam.subNumber  = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'changeIndividualNumber') {
                        // ここに来る前に個別番号を変更しているので、ここでは何もしない
                    }
                }
            },
            
            onHideStickerColorChangeWindow: function (emitParam) {
                console.log('onHideStickerColorChangeWindow', emitParam.event);
                
                this.showStickerColorChangeWindowParam.isShow = false;
                this.showStickerColorChangeWindowParam.idNo = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'changeColor') {
                        // ここに来る前に色を変更しているので、ここでは何もしない
                    }
                }
            },
            
            onHideStickerTextAddWindow: function (emitParam) {
                console.log('onHideStickerTextAddWindow', emitParam.event);
                
                this.showStickerTextAddWindowParam.isShow = false;
                this.showStickerTextAddWindowParam.idNo = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'addText') {
                        // ここに来る前にテキストを追加しているので、ここでは何もしない
                    }
                }
            },
            
            onHideStickerImageAddWindow: function (emitParam) {
                console.log('onHideStickerImageAddWindow', emitParam.event);
                
                this.showStickerImageAddWindowParam.isShow = false;
                this.showStickerImageAddWindowParam.idNo = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'addImage') {
                        // ここに来る前に画像を追加しているので、ここでは何もしない
                    }
                }
            },
            
            onHideStickerVideoAddWindow: function (emitParam) {
                console.log('onHideStickerVideoAddWindow', emitParam.event);
                
                this.showStickerVideoAddWindowParam.isShow = false;
                this.showStickerVideoAddWindowParam.idNo = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'addVideo') {
                        // ここに来る前に動画を追加しているので、ここでは何もしない
                    }
                }
            },            

            onHideStickerTaskTimeAddWindow: function (emitParam) {
                console.log('onHideStickerTaskTimeAddWindow', emitParam.event);
                
                this.showStickerTaskTimeAddWindowParam.isShow = false;
                this.showStickerTaskTimeAddWindowParam.idNo = null;
                this.showStickerTaskTimeAddWindowParam.taskTimeType = null;

                if (emitParam.result != 'none') {
                    if (emitParam.result == 'addTaskTime') {
                        // ここに来る前に時刻を追加しているので、ここでは何もしない
                    }
                }
            },
            
            releaseTargetElem: function () {
                this.updateTargetElem();
                clearInterval(this.moveIntervalId);
                this.resetLastUpdateTargetElemParam();
                
                if (this.reserveDestroyTargetElem) {
                    // 削除の予約が入っているので削除する
                    const idBaseName = this.getStickerIdBaseName();
                    const idNo = this.targetElem.id.substr(idBaseName.length);
                    
                    // データ更新
                    const index = this.getStickerParamIndex(idNo);
                    if (index !== null) {
                        this.stickerParams.splice(index, 1);
        
                        // 見た目更新
                        // 勝手に行われるはず（window.Echo.private('sticker-destroy-channel.'のコメント参照）。
                    }
                }               

                this.reserveDestroyTargetElem = false;
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
                
                const reqParam = {
                    id: updateTargetElemParam.idNo,
                    mountPos: {
                        x: updateTargetElemParam.mountPos.x,
                        y: updateTargetElemParam.mountPos.y,
                    },
                };
                this.updateStickerInfoItemPos(reqParam);
            },
            
            // reqParam = {
            //     id: null,
            //     mountPos: {
            //         x: null,  // px（数値だけで単位の文字列は付けていない）
            //         y: null,
            //     },
            // };
            // mountPosは台紙内における座標。            
            updateStickerInfoItemPos: function (reqParam) {
                console.log('axios.put');
                
                axios.put(window.laravel.asset + '/api/work-sticker-info-item-pos-update', {
                    reqParam: reqParam,
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
            
            removeStickerContentItem: function (eventParam) {
                const idNo = eventParam.id;
                
                const idBaseName = this.getStickerIdBaseName();
                const updateId = `${idBaseName}${idNo}`; 
                
                const updateElem = document.getElementById(updateId);
                
                if (updateElem) {
                    const divStickerInnerElems = updateElem.getElementsByClassName('sticker-inner-class');
                    const divStickerInnerElem = divStickerInnerElems[0];
                                
                    const contentLinkIdNo = eventParam.content_link_id;
                    
                    // データ更新
                    const index = this.getStickerParamIndex(idNo);
                    if (index !== null) {
                        const contents = this.stickerParams[index]['contents'];  // JavaScriptの配列は参照渡し
                        
                        let contentIndex = null;
                        for (let i = 0; i < contents.length; ++i) {
                            if (contents[i].link.id == contentLinkIdNo) {
                                contentIndex = i;
                                break;
                            }
                        }
                    
                        if (contentIndex !== null) {
                            contents.splice(contentIndex, 1);
                            
                            // 見た目更新
                            const contentLinkIdBaseName = this.getContentLinkIdBaseName();
                            const divItemElemId = `${contentLinkIdBaseName}${contentLinkIdNo}`;
                                // TODO(kawadakoujisun): refを使えばループを回さなくて済むか？
                            
                            const childElems = divStickerInnerElem.childNodes;
    
                            let divItemElem = null;
                            for (let i = 0; i < childElems.length; ++i) {
                                const childElem = childElems.item(i);
                                if (childElem.id == divItemElemId) {
                                    divItemElem = childElem;
                                    break;
                                }
                            }
                            
                            if (divItemElem !== null) {
                                divStickerInnerElem.removeChild(divItemElem);
                            }
                        }
                    }
                }
            },

            getStickerIdBaseName: function () {
                return 'sticker-id-';
            },
            
            getIndividualNumberIdBaseName: function () {
                return 'individual-number-id-'  
            },
            
            getContentLinkIdBaseName: function () {
                return 'content-link-id-';
            },
            
            // stickerParams配列において、引数で与えたidを持つstickerParamが何番目(0開始)かを取得する。
            // 戻り値がnullのときは、idを持つstickerParamは存在しない。
            // 配列の添え字なので0のこともあるので、nullと0は区別すること（if (index !== null) と判定するとよい）。
            getStickerParamIndex: function (id) {
                let index = null;
                
                for (let i = 0; i < this.stickerParams.length; ++i) {
                    const stickerParam = this.stickerParams[i];
                    if (stickerParam.id == id) {
                        index = i;
                        break;
                    }
                }
                
                return index;
            },
            
            copyStickerParam: function (src) {
                let dst = null;
                
                if (src) {
                    dst = {
                        id       : src.id,
                        individual_main_number : src.individual_main_number,
                        individual_sub_number  : src.individual_sub_number,
                        pos_top  : src.pos_top,
                        pos_left : src.pos_left,
                        depth    : src.depth,
                        color    : src.color,
                        contents : [],  // 要素数0であっても必ず配列を設定します。
                    };
                    
                    let contents = [];
                    
                    for (let i = 0; i < src.contents.length; ++i) {
                        const srcContent = src.contents[i];
                        
                        const content = {
                            link: {
                                id        : srcContent.link.id,
                                item_type : srcContent.link.item_type,
                                item_id   : srcContent.link.item_id,
                            },
                            
                            // itemには全item_typeのプロパティを設定しておく。
                            // item_typeが違うためにそのプロパティがないときはundefinedになるだけ。
                            // 使う側はitem_typeを確認して、そのitem_typeに存在するプロパティにしか使うときにアクセスしないはずなので、
                            // このような全item_typeのプロパティを設定するやり方でよい。
                            item: {  
                                text : srcContent.item.text,
                                image_url       : srcContent.item.image_url,
                                image_public_id : srcContent.item.image_public_id,
                                video_url       : srcContent.item.video_url,
                                video_public_id : srcContent.item.video_public_id,
                	            time_zone_type  : srcContent.item.time_zone_type,
                	            year_value      : srcContent.item.year_value,
                	            month_value     : srcContent.item.month_value,
                	            day_value       : srcContent.item.day_value,
                	            hour_value      : srcContent.item.hour_value,
                	            minute_value    : srcContent.item.minute_value,
                            },
                        };
                        
                        contents.push(content);
                    }
                    
                    dst.contents = contents;
                }
                
                return dst;
            },
            
            getMountBorderWidth: function () {
                return 1;  
            },
        },
    };
</script>

<style scoped>
    /*
     * 台紙
     */
    .mount-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  1800px;
        height: 900px;
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 0px 20px 20px;
        padding: 0;
    }
    
    .mount-sorted-class {
        position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
        width:  100%;
        height: auto;  /* heightをここで指定し直さないと、先に書いてあるmount-classのheightが使われてしまう。 */
        border: 1px solid #000;
        background-color: #ffffff;
        margin: 0px auto 20px;
        padding: 0;
    }
    
    /*
     * ふせん
     */
    .sticker-class {
        position: absolute;
        width:      340px;
        min-height: 200px;
        max-height: 430px;
        border: 1px solid #000;
        margin: 0;
        padding: 0;
        overflow-y: scroll;        
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }

    /* 要素のclass=""に書いた順番ではなく、css内で書いた順番によって優先度が決まるようだ。 */
    .sticker-sorted-class {
        position: relative;
        width:      340px;
        min-height: 200px;
        max-height: 430px;
        border: 1px solid #000;
        margin: 10px auto 10px;
        padding: 0;
        overflow-y: scroll;        
        
        /* 外部から変更するもの */
        top:  0;
        left: 0;
        background-color: #000000;
    }

    .sticker-inner-class {
        width:      280px;
        min-height: 180px;
        max-height: 400px;
        margin: 10px auto 10px;
    }
</style>

<style>
    /*
     * 画像(動画も)
     */
    .sticker-content-item-image-outer-class {
        position: relative;
        width:  280px;
        height: 200px;
        margin: 0;
        padding: 0;
    }
    
    .sticker-content-item-image-inner-class {
        position: absolute;
        left:         50%;
        top:          50%;
        margin-right: -50%;
        transform:    translate(-50%, -50%);
        width:      auto;
        height:     auto;
        max-width:  100%;
        max-height: 100%;
    }
    
    /*
     * テキスト(時刻も)
     */
    .sticker-content-item-text-outer-class {
        position: relative;
        width:  280px;
        margin: 0;
        padding: 0;
    }
    
    /*
     * 個別番号
     */
    .sticker-info-item-individual-number-outer-class {
        position: relative;
        width:  280px;
        margin: 0;
        padding: 0;
        text-align: right;
        font-size: small;
        color: rgba(0, 0, 0, 0.5);
    }
</style>
