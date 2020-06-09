class AppStorage {
    storeToken(token) {
        localStorage.setItem('token', token);
    }
    storeRole(role) {
        localStorage.setItem('role', role);
    }
    storeIsSetup(isSetup) {
        localStorage.setItem('isSetup', isSetup);
    }
    store(token, role) {
        this.storeToken(token);
        this.storeRole(role);
    }

    clear() {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
    }

    getToken() {
        return localStorage.getItem('token');
    }
    getRole() {
        return localStorage.getItem('role');
    }
    getIsSetup() {
        localStorage.getItem('isSetup');
    }
}
export default AppStorage = new AppStorage