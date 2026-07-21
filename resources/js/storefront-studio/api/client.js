import axios from 'axios';
import { reportError } from '../errorReporter';

const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

const client = axios.create({
    baseURL: '/admin/api/storefront-studio',
    withCredentials: true,
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        ...(token ? { 'X-CSRF-TOKEN': token } : {}),
    },
});

let sessionExpiredShown = false;

client.interceptors.response.use(
    (response) => {
        sessionExpiredShown = false;
        return response;
    },
    (error) => {
        const status = error?.response?.status;
        if (status === 401) {
            if (!sessionExpiredShown) {
                sessionExpiredShown = true;
                reportError(
                    'Your session expired. Please log in again.',
                    'You may need to re-authenticate before saving further changes.'
                );
            }
        }
        return Promise.reject(error);
    }
);

export default client;
