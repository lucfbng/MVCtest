class ToastService {
    static #container = null;
    static #defaultConfig = {
        duration: 5000,
        position: 'top-right',
        types: {
            success: {
                background: '#28a745',
                icon: '✓'
            },
            error: {
                background: '#dc3545',
                icon: '✕'
            },
            info: {
                background: '#17a2b8',
                icon: 'ℹ'
            }
        }
    };

    static init() {
        if (!this.#container) {
            this.#container = document.createElement('div');
            this.#container.id = 'toast-container';
            this.#container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            `;
            document.body.appendChild(this.#container);
            this.#addStyles();
        }
    }

    static show(message, type = 'info', config = {}) {
        this.init();
        const toastConfig = { ...this.#defaultConfig, ...config };
        const typeConfig = this.#defaultConfig.types[type] || this.#defaultConfig.types.info;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background: ${typeConfig.background};
            color: white;
            padding: 15px 25px;
            border-radius: 4px;
            margin-bottom: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease-in-out;
        `;

        toast.innerHTML = `
            <div class="toast-content">
                <span class="toast-icon">${typeConfig.icon}</span>
                <span class="toast-message">${message}</span>
            </div>
            <button class="toast-close">&times;</button>
        `;

        this.#container.appendChild(toast);
        this.#setupEventListeners(toast, toastConfig.duration);
    }

    static #setupEventListeners(toast, duration) {
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.onclick = () => this.#removeToast(toast);
        setTimeout(() => this.#removeToast(toast), duration);
    }

    static #removeToast(toast) {
        toast.style.animation = 'slideOut 0.3s ease-in-out';
        setTimeout(() => toast.remove(), 300);
    }

    static #addStyles() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
            .toast-icon { margin-right: 10px; }
            .toast-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 0 5px;
                font-size: 20px;
            }
        `;
        document.head.appendChild(style);
    }
}

export default ToastService;