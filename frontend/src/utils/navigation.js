import router from 'router';

class Navigation {
    constructor() {
        this.key = 'navigation-stack';
    }
    go(url) {
        router.push(url);
        return this;
    }
    addNext(next) {
        const stack = JSON.parse(localStorage.getItem(this.key) || '[]');
        stack.push(next);
        localStorage.setItem(this.key, JSON.stringify(stack));
        return this;
    }
    next() {
        const stack = JSON.parse(localStorage.getItem(this.key) || '[]');
        const next = stack.pop;
        localStorage.setItem(this.key, JSON.stringify(stack));
        if (next) {
            router.push(next);
        } else {
            console.warn('No "next" in navigation stack, check your code.');
        }
        return this;
    }
}

export default new Navigation();
