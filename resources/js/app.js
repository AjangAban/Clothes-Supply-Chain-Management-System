/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

import ExampleComponent from './components/ExampleComponent.vue';
import DeliveryDashboard from './components/DeliveryDashboard.vue';
import ManufacturerChat from './components/ManufacturerChat.vue';
import SupplierChat from './components/SupplierChat.vue';
import RetailerChat from './components/RetailerChat.vue';

const el = document.getElementById('delivery-dashboard-app');

if (el) {
    createApp({
        components: { DeliveryDashboard }
    }).mount('#delivery-dashboard-app');
} else {
    const app = createApp({});
    app.component('example-component', ExampleComponent);
    app.component('manufacturer-chat', ManufacturerChat);
    app.component('supplier-chat', SupplierChat);
    app.component('retailer-chat', RetailerChat);
    app.mount('#app');
}

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

Echo.private(`chat.${userId}`) // userId = current user's ID
    .listen('MessageSent', (e) => {
        console.log("New message:", e);
        // append message to the chat UI
    });
