/**
 * API Client
 * Handles all API calls to backend
 */

class APIClient {
    constructor(baseURL = '../backend/api') {
        this.baseURL = baseURL;
    }

    /**
     * Authentication APIs
     */
    async login(email, password) {
        return this.post('auth.php', {
            action: 'login',
            email,
            password
        });
    }

    async register(email, password, name, role) {
        return this.post('auth.php', {
            action: 'register',
            email,
            password,
            name,
            role
        });
    }

    async logout() {
        return this.post('auth.php', { action: 'logout' });
    }

    /**
     * College APIs
     */
    async getAllColleges() {
        return this.get('colleges.php');
    }

    async getCollege(id) {
        return this.get(`colleges.php?id=${id}`);
    }

    async createCollege(data) {
        return this.post('colleges.php', data);
    }

    async updateCollege(id, data) {
        return this.put(`colleges.php?id=${id}`, data);
    }

    async deleteCollege(id) {
        return this.delete(`colleges.php?id=${id}`);
    }

    /**
     * Department APIs
     */
    async getAllDepartments(collegeId = null) {
        let url = 'departments.php';
        if (collegeId) url += `?college_id=${collegeId}`;
        return this.get(url);
    }

    async createDepartment(data) {
        return this.post('departments.php', data);
    }

    async updateDepartment(id, data) {
        return this.put(`departments.php?id=${id}`, data);
    }

    async deleteDepartment(id) {
        return this.delete(`departments.php?id=${id}`);
    }

    /**
     * Division APIs
     */
    async getAllDivisions(departmentId = null) {
        let url = 'divisions.php';
        if (departmentId) url += `?department_id=${departmentId}`;
        return this.get(url);
    }

    async createDivision(data) {
        return this.post('divisions.php', data);
    }

    async updateDivision(id, data) {
        return this.put(`divisions.php?id=${id}`, data);
    }

    async deleteDivision(id) {
        return this.delete(`divisions.php?id=${id}`);
    }

    /**
     * Semester APIs
     */
    async getAllSemesters() {
        return this.get('semesters.php');
    }

    async createSemester(data) {
        return this.post('semesters.php', data);
    }

    async updateSemester(id, data) {
        return this.put(`semesters.php?id=${id}`, data);
    }

    async deleteSemester(id) {
        return this.delete(`semesters.php?id=${id}`);
    }

    /**
     * Subject APIs
     */
    async getAllSubjects(departmentId = null, semesterId = null) {
        let url = 'subjects.php';
        const params = [];
        if (departmentId) params.push(`department_id=${departmentId}`);
        if (semesterId) params.push(`semester_id=${semesterId}`);
        if (params.length) url += '?' + params.join('&');
        return this.get(url);
    }

    async createSubject(data) {
        return this.post('subjects.php', data);
    }

    async updateSubject(id, data) {
        return this.put(`subjects.php?id=${id}`, data);
    }

    async deleteSubject(id) {
        return this.delete(`subjects.php?id=${id}`);
    }

    /**
     * Slot APIs
     */
    async getAllSlots(subjectId = null) {
        let url = 'slots.php';
        if (subjectId) url += `?subject_id=${subjectId}`;
        return this.get(url);
    }

    async createSlot(data) {
        return this.post('slots.php', data);
    }

    async updateSlot(id, data) {
        return this.put(`slots.php?id=${id}`, data);
    }

    async deleteSlot(id) {
        return this.delete(`slots.php?id=${id}`);
    }

    /**
     * Student APIs
     */
    async getAllStudents(filters = {}) {
        let url = 'students.php';
        const params = [];
        if (filters.collegeId) params.push(`college_id=${filters.collegeId}`);
        if (filters.departmentId) params.push(`department_id=${filters.departmentId}`);
        if (filters.semester) params.push(`semester=${filters.semester}`);
        if (params.length) url += '?' + params.join('&');
        return this.get(url);
    }

    async getStudent(id) {
        return this.get(`students.php?id=${id}`);
    }

    async createStudent(data) {
        return this.post('students.php', data);
    }

    async updateStudent(id, data) {
        return this.put(`students.php?id=${id}`, data);
    }

    async deleteStudent(id) {
        return this.delete(`students.php?id=${id}`);
    }

    /**
     * Attendance APIs
     */
    async markAttendance(data) {
        return this.post('attendance.php', data);
    }

    async getAttendance(filters = {}) {
        let url = 'attendance.php';
        const params = [];
        if (filters.studentId) params.push(`student_id=${filters.studentId}`);
        if (filters.date) params.push(`date=${filters.date}`);
        if (params.length) url += '?' + params.join('&');
        return this.get(url);
    }

    async getDefaulters(semester, subjectId) {
        return this.get(`attendance.php?action=defaulters&semester=${semester}&subject_id=${subjectId}`);
    }

    async getAttendanceStatistics(filters = {}) {
        let url = 'attendance.php?action=statistics';
        if (filters.studentId) url += `&student_id=${filters.studentId}`;
        if (filters.subjectId) url += `&subject_id=${filters.subjectId}`;
        return this.get(url);
    }

    /**
     * Reports APIs
     */
    async getReport(reportType, filters = {}) {
        let url = `reports.php?type=${reportType}`;
        Object.entries(filters).forEach(([key, value]) => {
            if (value) url += `&${key}=${value}`;
        });
        return this.get(url);
    }

    async generateExcelReport(data) {
        return this.post('reports.php', { action: 'export', ...data });
    }

    /**
     * Parent Call APIs
     */
    async logParentCall(data) {
        return this.post('parent-calls.php', data);
    }

    async getParentCallHistory(filters = {}) {
        let url = 'parent-calls.php';
        const params = [];
        if (filters.studentId) params.push(`student_id=${filters.studentId}`);
        if (filters.date) params.push(`date=${filters.date}`);
        if (params.length) url += '?' + params.join('&');
        return this.get(url);
    }

    /**
     * HTTP Methods
     */
    async get(endpoint) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('GET Error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    async post(endpoint, data) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('POST Error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    async put(endpoint, data) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            return await response.json();
        } catch (error) {
            console.error('PUT Error:', error);
            return { success: false, message: 'Network error' };
        }
    }

    async delete(endpoint) {
        try {
            const response = await fetch(`${this.baseURL}/${endpoint}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            return await response.json();
        } catch (error) {
            console.error('DELETE Error:', error);
            return { success: false, message: 'Network error' };
        }
    }
}

// Create global API client instance
const api = new APIClient();
