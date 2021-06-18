/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('todos-list', require('./components/TodoListComponent.vue').default);
Vue.component('rect-test', require('./components/RectTestComponent.vue').default);
Vue.component('rect-test-2', require('./components/RectTest2Component.vue').default);
Vue.component('color-rect-mount', require('./components/ColorRectMountComponent.vue').default);
Vue.component('color-rect-context-menu', require('./components/ColorRectContextMenuComponent.vue').default);
Vue.component('work-top', require('./components/WorkTopComponent.vue').default);
Vue.component('work-menu-bar', require('./components/WorkMenuBarComponent.vue').default);
Vue.component('work-mount', require('./components/WorkMountComponent.vue').default);
Vue.component('work-sticker-context-menu', require('./components/WorkStickerContextMenuComponent.vue').default);
Vue.component('work-sticker-edit-window', require('./components/WorkStickerEditWindowComponent.vue').default);
Vue.component('work-sticker-color-change-window', require('./components/WorkStickerColorChangeWindowComponent.vue').default);
Vue.component('work-sticker-text-add-window', require('./components/WorkStickerTextAddWindowComponent.vue').default);
Vue.component('work-sticker-image-add-window', require('./components/WorkStickerImageAddWindowComponent.vue').default);
Vue.component('work-sticker-video-add-window', require('./components/WorkStickerVideoAddWindowComponent.vue').default);
Vue.component('work-sticker-task-time-add-window', require('./components/WorkStickerTaskTimeAddWindowComponent.vue').default);
Vue.component('read-mount', require('./components/ReadMountComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
