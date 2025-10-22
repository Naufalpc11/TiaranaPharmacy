import { createInertiaApp, router } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import './bootstrap';
import '/resources/css/app.scss';

// Fungsi helper untuk secara otomatis mengimpor komponen Vue
function resolvePageComponent(name, pages) {
    for (const path in pages) {
        if (path.endsWith(`${name}.vue`)) {
            return typeof pages[path] === 'function'
                ? pages[path]()
                : pages[path];
        }
    }
    throw new Error(`Page not found: ${name}`);
}

createInertiaApp({
    // Nama aplikasi Anda (opsional)
    title: (title) => (title ? `${title} - Tiaranapharmacy` : 'Tiaranapharmacy'),

    // Fungsi ini mencari komponen Vue di direktori Resources/js/Pages/
    resolve: (name) => resolvePageComponent(name, import.meta.glob('./Pages/**/*.vue')),

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});

router.on('finish', (event) => {
    const { visit } = event.detail;

    if (!visit.completed || visit.preserveScroll) {
        return;
    }

    requestAnimationFrame(() => {
        window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
    });
});
