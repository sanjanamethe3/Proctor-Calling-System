/**
 * Admin Dashboard Script
 * Handles all admin panel operations
 */

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    initializeDashboard();
    setupEventListeners();
    setCurrentDate();
    loadDashboardData();
});

function checkAuth() {
    const user = sessionStorage.getItem('user');
    if (!user) {
        window.location.href = 'login.html';
        return;
    }
    const userData = JSON.parse(user);
    if (userData.role !== 'admin') {
        alert('Access Denied! Admin access required.');
        window.location.href = 'login.html';
    }
    document.getElementById('profileName').textContent = userData.name || 'Admin';
}

function initializeDashboard() {
    // Setup navigation
    setupNavigation();
    loadColleges();
    loadDepartments();
    loadDivisions();
    loadSemesters();
    loadSubjects();
    loadSlots();
    loadStudents();
}

function setupEventListeners() {
    // College Form
    document.getElementById('collegeForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createCollege(data);
        if (result.success) {
            alert('College added successfully!');
            this.reset();
            loadColleges();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Department Form
    document.getElementById('departmentForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createDepartment(data);
        if (result.success) {
            alert('Department added successfully!');
            this.reset();
            loadDepartments();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Division Form
    document.getElementById('divisionForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createDivision(data);
        if (result.success) {
            alert('Division added successfully!');
            this.reset();
            loadDivisions();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Semester Form
    document.getElementById('semesterForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createSemester(data);
        if (result.success) {
            alert('Semester added successfully!');
            this.reset();
            loadSemesters();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Subject Form
    document.getElementById('subjectForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createSubject(data);
        if (result.success) {
            alert('Subject added successfully!');
            this.reset();
            loadSubjects();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Slot Form
    document.getElementById('slotForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createSlot(data);
        if (result.success) {
            alert('Slot added successfully!');
            this.reset();
            loadSlots();
        } else {
            alert('Error: ' + result.message);
        }
    });

    // Student Form
    document.getElementById('studentForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData);
        
        const result = await api.createStudent(data);
        if (result.success) {
            alert('Student added successfully!');
            this.reset();
            loadStudents();
        } else {
            alert('Error: ' + result.message);
        }
    });
}

function setupNavigation() {
    // Handle nav items
    document.querySelectorAll('[data-page]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            
            if (page === 'masters') {
                const submenu = document.getElementById('masters-submenu');
                submenu.classList.toggle('hidden');
                const arrow = this.querySelector('.arrow');
                if (arrow) arrow.classList.toggle('rotated');
            } else {
                loadPage(page);
            }
        });
    });

    // Handle submenu items
    document.querySelectorAll('.submenu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            loadPage(page);
        });
    });
}

function loadPage(pageId) {
    // Hide all pages
    document.querySelectorAll('.page').forEach(page => {
        page.classList.add('hidden');
    });
    document.getElementById('dashboard').classList.add('hidden');

    // Show selected page
    const page = document.getElementById(pageId);
    if (page) {
        page.classList.remove('hidden');
    }
}

function setCurrentDate() {
    const today = new Date().toLocaleDateString('en-GB');
    document.querySelectorAll('[id^="date"]').forEach(el => {
        el.textContent = today;
    });
}

async function loadDashboardData() {
    const collegesData = await api.getAllColleges();
    const studentsData = await api.getAllStudents();
    
    document.getElementById('collegeCount').textContent = collegesData.count || 0;
    document.getElementById('studentCount').textContent = studentsData.count || 0;
}

async function loadColleges() {
    const result = await api.getAllColleges();
    if (result.success && result.data) {
        // Update select boxes
        const selects = document.querySelectorAll('[id$="CollegeSelect"], [id="deptCollegeSelect"]');
        selects.forEach(select => {
            const current = select.value;
            select.innerHTML = '<option>Select College</option>';
            result.data.forEach(college => {
                const option = document.createElement('option');
                option.value = college._id;
                option.textContent = college.name;
                select.appendChild(option);
            });
            select.value = current;
        });

        // Update table
        const tbody = document.getElementById('collegeTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((college, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${college.name}</td>
                        <td>${college.email || '-'}</td>
                        <td>${college.mobile || '-'}</td>
                        <td>${college.address || '-'}</td>
                        <td>
                            <button class="btn-action edit" onclick="editCollege('${college._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteCollege('${college._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadDepartments() {
    const result = await api.getAllDepartments();
    if (result.success && result.data) {
        const selects = document.querySelectorAll('[id$="DepartmentSelect"], [id="subjDepartmentSelect"]');
        selects.forEach(select => {
            select.innerHTML = '<option>Select Department</option>';
            result.data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept._id;
                option.textContent = dept.name;
                select.appendChild(option);
            });
        });

        const tbody = document.getElementById('departmentTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((dept, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${dept.college_name || '-'}</td>
                        <td>${dept.name}</td>
                        <td>
                            <button class="btn-action edit" onclick="editDepartment('${dept._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteDepartment('${dept._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadDivisions() {
    const result = await api.getAllDivisions();
    if (result.success && result.data) {
        const select = document.getElementById('stdDivisionSelect');
        if (select) {
            select.innerHTML = '<option>Select Division</option>';
            result.data.forEach(div => {
                const option = document.createElement('option');
                option.value = div._id;
                option.textContent = div.name;
                select.appendChild(option);
            });
        }

        const tbody = document.getElementById('divisionTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((div, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${div.department_name || '-'}</td>
                        <td>${div.name}</td>
                        <td>
                            <button class="btn-action edit" onclick="editDivision('${div._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteDivision('${div._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadSemesters() {
    const result = await api.getAllSemesters();
    if (result.success && result.data) {
        const select = document.getElementById('subjSemesterSelect');
        if (select) {
            select.innerHTML = '<option>Select Semester</option>';
            result.data.forEach(sem => {
                const option = document.createElement('option');
                option.value = sem._id;
                option.textContent = sem.name;
                select.appendChild(option);
            });
        }

        const tbody = document.getElementById('semesterTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((sem, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${sem.name}</td>
                        <td>
                            <button class="btn-action edit" onclick="editSemester('${sem._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteSemester('${sem._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadSubjects() {
    const result = await api.getAllSubjects();
    if (result.success && result.data) {
        const select = document.getElementById('slotSubjectSelect');
        if (select) {
            select.innerHTML = '<option>Select Subject</option>';
            result.data.forEach(subj => {
                const option = document.createElement('option');
                option.value = subj._id;
                option.textContent = subj.name;
                select.appendChild(option);
            });
        }

        const tbody = document.getElementById('subjectTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((subj, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${subj.department_name || '-'}</td>
                        <td>${subj.semester_name || '-'}</td>
                        <td>${subj.name}</td>
                        <td>
                            <button class="btn-action edit" onclick="editSubject('${subj._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteSubject('${subj._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadSlots() {
    const result = await api.getAllSlots();
    if (result.success && result.data) {
        const tbody = document.getElementById('slotTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((slot, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${slot.subject_name || '-'}</td>
                        <td>${slot.start_time}</td>
                        <td>${slot.end_time}</td>
                        <td>
                            <button class="btn-action edit" onclick="editSlot('${slot._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteSlot('${slot._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

async function loadStudents() {
    const result = await api.getAllStudents();
    if (result.success && result.data) {
        const tbody = document.getElementById('studentTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            result.data.forEach((student, index) => {
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${student.name}</td>
                        <td>${student.mobile}</td>
                        <td>${student.gender}</td>
                        <td>${student.parent_mobile}</td>
                        <td>${student.department_name || '-'}</td>
                        <td>${student.semester}</td>
                        <td>
                            <button class="btn-action edit" onclick="editStudent('${student._id}')"><i class="fas fa-edit"></i></button>
                            <button class="btn-action delete" onclick="deleteStudent('${student._id}')"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
    }
}

// Placeholder delete/edit functions
async function deleteCollege(id) {
    if (confirm('Are you sure?')) {
        const result = await api.deleteCollege(id);
        if (result.success) {
            alert('Deleted successfully');
            loadColleges();
        }
    }
}

async function deleteStudent(id) {
    if (confirm('Are you sure?')) {
        const result = await api.deleteStudent(id);
        if (result.success) {
            alert('Deleted successfully');
            loadStudents();
        }
    }
}

function deleteDepartment(id) {
    if (confirm('Are you sure?')) {
        api.deleteDepartment(id).then(result => {
            if (result.success) {
                alert('Deleted successfully');
                loadDepartments();
            }
        });
    }
}

function deleteDivision(id) {
    if (confirm('Are you sure?')) {
        api.deleteDivision(id).then(result => {
            if (result.success) {
                alert('Deleted successfully');
                loadDivisions();
            }
        });
    }
}

function deleteSemester(id) {
    if (confirm('Are you sure?')) {
        api.deleteSemester(id).then(result => {
            if (result.success) {
                alert('Deleted successfully');
                loadSemesters();
            }
        });
    }
}

function deleteSubject(id) {
    if (confirm('Are you sure?')) {
        api.deleteSubject(id).then(result => {
            if (result.success) {
                alert('Deleted successfully');
                loadSubjects();
            }
        });
    }
}

function deleteSlot(id) {
    if (confirm('Are you sure?')) {
        api.deleteSlot(id).then(result => {
            if (result.success) {
                alert('Deleted successfully');
                loadSlots();
            }
        });
    }
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        sessionStorage.clear();
        localStorage.clear();
        window.location.href = 'login.html';
    }
}
