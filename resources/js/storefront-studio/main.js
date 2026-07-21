import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import { reportError } from './errorReporter';

import '../../css/app.css';

const app = createApp(App);
app.use(createPinia());

app.config.errorHandler = (err, instance, info) => {
    const message = err?.message ?? String(err);
    console.error('[StorefrontStudio] Unhandled error:', err, { info, component: instance?.$options?.name });
    reportError('Something went wrong.', `${info ? info + ': ' : ''}${message}`);
};

app.mount('#storefront-studio');
