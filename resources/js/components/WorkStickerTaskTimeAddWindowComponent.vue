<template>
    <div
        v-show="isShow"
    >
        <!-- 背景 -->
        <div
            class="sticker-task-time-add-window-overlay-class"
            @click.self.prevent="onClickStickerTaskTimeAddWindowOverlay"
            @click.right.self.prevent="onClickStickerTaskTimeAddWindowOverlay"
        >
        </div>        
        
        <!-- ウィンドウ -->
        <div
            class="sticker-task-time-add-window-class"
            id="sticker-task-time-add-window-id"
            @click.self.prevent="onClickStickerTaskTimeAddWindow"
        >
            <form
                @submit.prevent="onClickAddTaskTime"
            >
                <table>
                    <tr>
                        <td>
                            <input v-model="yearText" type="number" class="form-control sticker-task-time-add-input-4-digits-class">
                        </td>
                        <td>年</td>
                        <td> </td>
                        
                        <td>
                            <input v-model="monthText" type="number" class="form-control sticker-task-time-add-input-2-digits-class">
                        </td>
                        <td>月</td>
                        <td> </td>
                        
                        <td>
                            <input v-model="dayText" type="number" class="form-control sticker-task-time-add-input-2-digits-class">
                        </td>
                        <td>日</td>
                        <td> </td>
                        
                        <td>
                            <input v-model="hourText" type="number" class="form-control sticker-task-time-add-input-2-digits-class">
                        </td>
                        <td>時</td>
                        <td> </td>
                        
                        <td>
                            <input v-model="minuteText" type="number" class="form-control sticker-task-time-add-input-2-digits-class">
                        </td>
                        <td>分</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-muted text-center">（西暦4桁）</td>
                        <td colspan="1"> </td>
                        <td colspan="2" class="text-muted text-center">（1～12）</td>
                        <td colspan="1"> </td>
                        <td colspan="2" class="text-muted text-center">（1～31）</td>
                        <td colspan="1"> </td>
                        <td colspan="2" class="text-muted text-center">（0～23）</td>
                        <td colspan="1"> </td>
                        <td colspan="2" class="text-muted text-center">（0～59）</td>
                    </tr>
                </table>

                <div class="sticker-task-time-add-window-space-class"></div>
                <div><p><button type="submit" class="btn btn-secondary btn-block">追加</button></p></div>
            </form>

            <div class="sticker-task-time-add-window-space-class"></div>
            <div class="text-center"><button class="btn btn-secondary" @click.prevent="onClickClose">戻る</button></div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            showStickerTaskTimeAddWindowProps: Object,
        },

        data() {
            return {
                isShow: this.showStickerTaskTimeAddWindowProps.isShow,
                yearText: '',
                monthText: '',
                dayText: '',
                hourText: '',
                minuteText: '',
                stickerTaskTimeAddWindowTimeoutId: null,
            };
        },
        
        watch: {
            'showStickerTaskTimeAddWindowProps.isShow': function(newValue, oldValue) {
                this.isShow = this.showStickerTaskTimeAddWindowProps.isShow;
                
                if (this.isShow) {
                    // 前の入力が残っているので、消しておく。
                    this.yearText   = '';
                    this.monthText  = '';
                    this.dayText    = '';
                    this.hourText   = '';
                    this.minuteText = '';
                    
                    const windowElem = document.getElementById("sticker-task-time-add-window-id");
                    // いったん表示しないとサイズを取得できないので、最初は見えないところにおいておく。
                    windowElem.style.left = '-10000px';
                    windowElem.style.top  = 0;
                    
                    this.stickerTaskTimeAddWindowTimeoutId = setTimeout(() => {
                        const windowElemRect = windowElem.getBoundingClientRect();
                        windowElem.style.left = '50%';
                        windowElem.style.top  = '50%';

                        windowElem.style.marginLeft = `${-windowElemRect.width/2}px`;   // margin-left
                        windowElem.style.marginTop  = `${-windowElemRect.height/2}px`;  // margin-top
                    }, 10);
                }
            },
        },
        
        methods: {
            onClickStickerTaskTimeAddWindowOverlay: function (e) {
                console.log('onClickStickerTaskTimeAddWindowOverlay');
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickStickerTaskTimeAddWindow: function (e) {
                console.log('onClickStickerTaskTimeAddWindow');
                // 何もしない
            },
            
            onClickClose: function (e) {
                const emitParam = {
                    event: e,
                    result: 'none',
                };
                this.backToMount(emitParam);
            },
            
            onClickAddTaskTime: function (e) {
                // 時刻データを追加する
                const yearValue   = parseInt(this.yearText, 10);
                const monthValue  = parseInt(this.monthText, 10);
                const dayValue    = parseInt(this.dayText, 10);
                const hourValue   = parseInt(this.hourText, 10);
                const minuteValue = parseInt(this.minuteText, 10);
                
                console.log(`taskTime=${yearValue}/${monthValue}/${dayValue} ${hourValue}:${minuteValue}`);
                
                // 入力が適切かどうかチェックする
                let inputOK = false;
                do {
                    // 整数か？
                    if ( !( Number.isInteger(yearValue)
                        && Number.isInteger(monthValue)
                        && Number.isInteger(dayValue)
                        && Number.isInteger(hourValue)
                        && Number.isInteger(minuteValue) ) ) {
                        break;
                    }
                    
                    // 年、日、時、分
                    if (!(0<=yearValue && yearValue<=9999)) break;
                    if (!(1<=monthValue && monthValue<=12)) break;
                    if (!(0<=hourValue && hourValue<=23)) break;
                    if (!(0<=minuteValue && minuteValue<=59)) break;
                    
                    // 月
                    if (!(1<=dayValue && dayValue<=31)) break;
                    if (monthValue == 4 || monthValue == 6 || monthValue == 9 || monthValue == 11) {
                        if (dayValue > 30) break;
                    }
                    
                    // 2月
                    if (monthValue == 2) {
                        let leapYear = false;  // うるう年ならtrue
                        
                        if (yearValue % 4 == 0) {
                            if (yearValue % 100 == 0) {
                                if (yearValue % 400 == 0) {
                                    leapYear = true;
                                } else {
                                    leapYear = false;
                                }
                            } else {
                                leapYear = true;
                            }
                        } else {
                            leapYear = false;
                        }
                        
                        if (leapYear) {
                            // うるう年
                            if (dayValue > 29) break;
                        } else {
                            // 平年
                            if (dayValue > 28) break;
                        }
                    }
                    
                    // ここまで到達できたのなら、入力は適切
                    inputOK = true;
                } while (0);

                if (inputOK) {
                    this.yearText   = '';
                    this.monthText  = '';
                    this.dayText    = '';
                    this.hourText   = '';
                    this.minuteText = '';
                
                    console.log('axios.post');
                                    
                    const reqParam = {
                        id: this.showStickerTaskTimeAddWindowProps.idNo,
                        taskTimeType: this.showStickerTaskTimeAddWindowProps.taskTimeType,
                            // 'taskStartTime' or 'taskEndTime'
                        timeZoneType: 'jst',  // utcで保存して表示するときにjstに変えるほうがいいだろう
                        yearValue  : yearValue,
                        monthValue : monthValue,
                        dayValue   : dayValue,
                        hourValue  : hourValue,
                        minuteValue: minuteValue,
                    };
                    
                    axios.post(window.laravel.asset + '/api/work-sticker-content-item-task-time-create', {
                        reqParam: reqParam,
                        user_id: window.laravel.user['id'],
                    })
                        .then(response => {
                            // 特にすることなし
                        });
                
                    // 親に戻る
                    const emitParam = {
                        event: e,
                        result: 'addTaskTime',
                    };
                    this.backToMount(emitParam);
                }
            },
            
            backToMount: function (emitParam) {
                if (this.stickerTaskTimeAddWindowTimeoutId !== null) {
                    clearTimeout(this.stickerTaskTimeAddWindowTimeoutId);
                    this.stickerTaskTimeAddWindowTimeoutId = null;
                }
                
                this.$emit('hide-sticker-task-time-add-window-custom-event', emitParam);
            },
        },
    };
</script>

<style scoped>
    /*
     * オーバーレイ
     */
    .sticker-task-time-add-window-overlay-class {
        position: fixed;
        left:   0;
        top:    0;
        width:  100%;
        height: 100%;
        z-index: 3000;
        background: rgba(0, 0, 0, 0.0);
        margin: 0;
    }
    
    /*
     * ウィンドウ
     */
    .sticker-task-time-add-window-class {
        position: fixed;
        left:   50%;
        top:    50%;
        min-width: 400px;
        z-index: 3001;
        border: 1px solid #000;
        background-color: #ffffff;
        padding: 10px;
        box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.4);
        
        /* 外部から変更するもの */
        margin-left: 0;
        margin-top:  0;
    }
    
    .sticker-task-time-add-window-space-class {
        height: 10px;
    }
    
    /*
     * 入力欄
     */
    .sticker-task-time-add-input-4-digits-class {
        width: 130px;  /* 100 + 30 */
    }
    
    .sticker-task-time-add-input-2-digits-class {
        width: 80px;  /* 50 + 30 */  
    }
</style>