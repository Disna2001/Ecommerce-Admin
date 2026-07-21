import { ref } from 'vue';

export const errors = ref([]);

let seq = 0;

export function reportError(message, detail = null) {
    const id = ++seq;
    errors.value.push({ id, message, detail });
    return id;
}

export function dismissError(id) {
    errors.value = errors.value.filter((e) => e.id !== id);
}
