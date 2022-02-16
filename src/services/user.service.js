import http from "../http-common";
class UserService {
    getPublicContent() {
        return http.get('/test/all');
    }
    getUserBoard() {
        return http.get('/test/user');
    }
    getModeratorBoard() {
        return http.get('/test/mod');
    }
    getAdminBoard() {
        return http.get('/test/admin');
    }
}
export default new UserService();
