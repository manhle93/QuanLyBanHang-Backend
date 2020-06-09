import AppStorage from "./AppStorage";

class User {
    storeAfterLogin(res) {
        const access_token = res.data.access_token;
        const role = res.data.role;
        if (role == 2) AppStorage.storeIsSetup(res.data.isSetup)
        AppStorage.store(access_token, role);
        window.axios.defaults.headers.common["Authorization"] = 'Bearer' + ' ' + access_token;
    }
    isAdmin() {
        return AppStorage.getRole() === '2';
    }
    // redirectByRole() {
    //     return AppStorage.getRole() === 'admin' ? '/admin' : (AppStorage.getRole() === 'teacher' ? '/teacher' : '/student');
    // }
    hasToken() {
        const storedToken = AppStorage.getToken();
        if (storedToken) {
            return true;
        }
        return false;
    }
    loggedIn() {
        return this.hasToken();
    }
    logout() {
        AppStorage.clear();
    }
    role() {
        if (this.loggedIn) {
            return AppStorage.getRole();
        }
    }
    isSetup() {
        return AppStorage.getIsSetup();
    }
}
export default User = new User