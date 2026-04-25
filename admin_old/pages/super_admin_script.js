// Super Admin Dashboard JavaScript

// Global variables
let currentPage = 1;
let itemsPerPage = 10;

// Show/hide sections
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Remove active class from all nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(sectionName + '-section').style.display = 'block';
    
    // Add active class to clicked nav link
    event.target.classList.add('active');
    
    // Load section-specific data
    switch(sectionName) {
        case 'classes':
            loadClasses();
            break;
        case 'students':
            loadStudents();
            loadFilters();
            break;
        case 'view-students':
            loadAllStudents();
            loadAllFilters();
            break;
        case 'events':
            loadEvents();
            break;
        case 'reports':
            loadReports();
            break;
        case 'appreciations':
            initializeAppreciationsSection();
            break;
    }
}

// Load classes data
function loadClasses() {
    fetch('../../super_admin_api.php?action=get_classes')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('classesTableBody');
            tbody.innerHTML = '';
            
            data.forEach(classData => {
                const row = `
                    <tr>
                        <td>${classData.class_id}</td>
                        <td>${classData.academic_year}</td>
                        <td>${classData.year}</td>
                        <td>${classData.semester}</td>
                        <td>${classData.branch}</td>
                        <td>${classData.section}</td>
                        <td><span class="badge bg-info">${classData.student_count}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteClass(${classData.class_id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error loading classes:', error);
            showAlert('Error loading classes data', 'danger');
        });
}

// Add new class
function addClass() {
    const formData = new FormData(document.getElementById('addClassForm'));
    
    fetch('../../super_admin_api.php?action=add_class', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Class added successfully!', 'success');
            document.getElementById('addClassForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addClassModal')).hide();
            loadClasses();
            updateDashboardStats();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding class:', error);
        showAlert('Error adding class', 'danger');
    });
}

// Delete class
function deleteClass(classId) {
    if (confirm('Are you sure you want to delete this class? This action cannot be undone.')) {
        fetch('../../super_admin_api.php?action=delete_class', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({class_id: classId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Class deleted successfully!', 'success');
                loadClasses();
                updateDashboardStats();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error deleting class:', error);
            showAlert('Error deleting class', 'danger');
        });
    }
}

// Load students data
function loadStudents() {
    fetch('../../super_admin_api.php?action=get_students')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('studentsTableBody');
            tbody.innerHTML = '';
            
            data.forEach(student => {
                const row = `
                    <tr>
                        <td><strong>${student.student_id}</strong></td>
                        <td>${student.name}</td>
                        <td><small>${student.email}</small></td>
                        <td><span class="badge bg-primary">${student.branch}</span></td>
                        <td>${student.section}</td>
                        <td><span class="badge bg-secondary">${student.house_name}</span></td>
                        <td><span class="badge bg-success">${student.total_points}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editStudent('${student.student_id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent('${student.student_id}')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Error loading students:', error);
            showAlert('Error loading students data', 'danger');
        });
}

// Load filters for students
function loadFilters() {
    // Load classes for filter
    fetch('../../super_admin_api.php?action=get_classes')
        .then(response => response.json())
        .then(data => {
            const classSelects = ['filterClass', 'studentClass', 'editStudentClass'];
            classSelects.forEach(selectId => {
                const select = document.getElementById(selectId);
                if (select) {
                    // Clear existing options except first one
                    const firstOption = select.firstElementChild;
                    select.innerHTML = '';
                    select.appendChild(firstOption);
                    
                    data.forEach(classData => {
                        const option = document.createElement('option');
                        option.value = classData.class_id;
                        option.textContent = `${classData.academic_year} - ${classData.branch} ${classData.section} (Year ${classData.year})`;
                        select.appendChild(option);
                    });
                }
            });
        });
    
    // Load houses for filter
    fetch('../../super_admin_api.php?action=get_houses')
        .then(response => response.json())
        .then(data => {
            const houseSelect = document.getElementById('filterHouse');
            data.forEach(house => {
                const option = document.createElement('option');
                option.value = house.hid;
                option.textContent = house.name;
                houseSelect.appendChild(option);
            });
        });
}

// Add new student
function addStudent() {
    const formData = new FormData(document.getElementById('addStudentForm'));
    
    fetch('../../super_admin_api.php?action=add_student', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Student added successfully!', 'success');
            document.getElementById('addStudentForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
            loadStudents();
            updateDashboardStats();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding student:', error);
        showAlert('Error adding student', 'danger');
    });
}

// Edit student
function editStudent(studentId) {
    fetch(`../../super_admin_api.php?action=get_student&id=${studentId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                document.getElementById('editStudentId').value = student.student_id;
                document.getElementById('editStudentName').value = student.name;
                document.getElementById('editStudentEmail').value = student.email;
                document.getElementById('editStudentBranch').value = student.branch;
                document.getElementById('editStudentSection').value = student.section;
                document.getElementById('editStudentClass').value = student.class_id;
                document.getElementById('editStudentHouse').value = student.hid;
                
                new bootstrap.Modal(document.getElementById('editStudentModal')).show();
            } else {
                showAlert('Error loading student data', 'danger');
            }
        })
        .catch(error => {
            console.error('Error loading student:', error);
            showAlert('Error loading student data', 'danger');
        });
}

// Update student
function updateStudent() {
    const formData = new FormData(document.getElementById('editStudentForm'));
    formData.append('reset_password', document.getElementById('resetPassword').checked);
    
    fetch('../../super_admin_api.php?action=update_student', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Student updated successfully!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('editStudentModal')).hide();
            loadStudents();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating student:', error);
        showAlert('Error updating student', 'danger');
    });
}

// Delete student
function deleteStudent(studentId) {
    if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
        fetch('../../super_admin_api.php?action=delete_student', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({student_id: studentId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Student deleted successfully!', 'success');
                loadStudents();
                updateDashboardStats();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error deleting student:', error);
            showAlert('Error deleting student', 'danger');
        });
    }
}

// Bulk upload functionality
document.addEventListener('DOMContentLoaded', function() {
    const bulkUploadForm = document.getElementById('bulkUploadForm');
    if (bulkUploadForm) {
        bulkUploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadResult = document.getElementById('uploadResult');
            
            uploadResult.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing file...</div>';
            
            fetch('../../super_admin_api.php?action=bulk_upload_points', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadResult.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Upload Successful!</h6>
                            <p>Successfully processed ${data.processed} records.</p>
                            <p>Points added to ${data.successful} students.</p>
                            ${data.errors.length > 0 ? `<p class="text-warning">Errors: ${data.errors.join(', ')}</p>` : ''}
                        </div>
                    `;
                    bulkUploadForm.reset();
                    updateDashboardStats();
                } else {
                    uploadResult.innerHTML = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Upload Failed!</h6>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error uploading file:', error);
                uploadResult.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Upload Error!</h6>
                        <p>An error occurred while processing the file.</p>
                    </div>
                `;
            });
        });
    }
    
    // Bulk student upload functionality
    const bulkStudentForm = document.getElementById('bulkStudentForm');
    if (bulkStudentForm) {
        bulkStudentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('skip_duplicates', document.getElementById('skipDuplicates').checked);
            
            const uploadResult = document.getElementById('bulkStudentResult');
            
            uploadResult.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing student data...</div>';
            
            fetch('../../super_admin_api.php?action=bulk_upload_students', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadResult.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Students Upload Successful!</h6>
                            <p>Successfully processed ${data.processed} records.</p>
                            <p>Added ${data.successful} new students.</p>
                            ${data.skipped > 0 ? `<p class="text-info">Skipped ${data.skipped} duplicate entries.</p>` : ''}
                            ${data.errors.length > 0 ? `<div class="mt-2"><strong>Errors:</strong><ul class="mb-0">${data.errors.map(err => `<li>${err}</li>`).join('')}</ul></div>` : ''}
                        </div>
                    `;
                    bulkStudentForm.reset();
                    updateDashboardStats();
                    loadStudents(); // Refresh the students table
                } else {
                    uploadResult.innerHTML = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Upload Failed!</h6>
                            <p>${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error uploading students:', error);
                uploadResult.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Upload Error!</h6>
                        <p>An error occurred while processing the student file.</p>
                    </div>
                `;
            });
        });
    }
});

// Load all students with pagination
function loadAllStudents(page = 1) {
    const searchTerm = document.getElementById('searchAllStudents')?.value || '';
    const classFilter = document.getElementById('filterAllClass')?.value || '';
    const houseFilter = document.getElementById('filterAllHouse')?.value || '';
    const branchFilter = document.getElementById('filterAllBranch')?.value || '';
    const sortBy = document.getElementById('sortBy')?.value || 'name';
    
    const params = new URLSearchParams({
        action: 'get_all_students',
        page: page,
        limit: itemsPerPage,
        search: searchTerm,
        class_filter: classFilter,
        house_filter: houseFilter,
        branch_filter: branchFilter,
        sort_by: sortBy
    });
    
    fetch(`../../super_admin_api.php?${params}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('allStudentsTableBody');
            tbody.innerHTML = '';
            
            data.students.forEach(student => {
                const row = `
                    <tr>
                        <td><strong>${student.student_id}</strong></td>
                        <td>${student.name}</td>
                        <td><small>${student.email}</small></td>
                        <td><span class="badge bg-primary">${student.branch}</span></td>
                        <td>${student.section}</td>
                        <td>${student.class_info}</td>
                        <td><span class="badge bg-secondary">${student.house_name}</span></td>
                        <td><span class="badge bg-success">${student.total_points}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="editStudent('${student.student_id}')">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
            
            // Update pagination
            updatePagination(data.total_pages, page);
        })
        .catch(error => {
            console.error('Error loading all students:', error);
            showAlert('Error loading students data', 'danger');
        });
}

// Update pagination
function updatePagination(totalPages, currentPage) {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
    
    if (totalPages <= 1) return;
    
    let paginationHTML = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAllStudents(${currentPage - 1})">Previous</a>
        </li>
    `;
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadAllStudents(${i})">${i}</a>
                </li>
            `;
        } else if (i === currentPage - 3 || i === currentPage + 3) {
            paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadAllStudents(${currentPage + 1})">Next</a>
        </li>
    `;
    
    paginationHTML += '</ul></nav>';
    pagination.innerHTML = paginationHTML;
}

// Load filters for all students section
function loadAllFilters() {
    // Load classes
    fetch('../../super_admin_api.php?action=get_classes')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('filterAllClass');
            data.forEach(classData => {
                const option = document.createElement('option');
                option.value = classData.class_id;
                option.textContent = `${classData.academic_year} - ${classData.branch} ${classData.section}`;
                select.appendChild(option);
            });
        });
    
    // Load houses
    fetch('../../super_admin_api.php?action=get_houses')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('filterAllHouse');
            data.forEach(house => {
                const option = document.createElement('option');
                option.value = house.hid;
                option.textContent = house.name;
                select.appendChild(option);
            });
        });
}

// Apply filters
function applyFilters() {
    loadAllStudents(1);
}

// Export students data
function exportStudentsData() {
    const searchTerm = document.getElementById('searchAllStudents')?.value || '';
    const classFilter = document.getElementById('filterAllClass')?.value || '';
    const houseFilter = document.getElementById('filterAllHouse')?.value || '';
    const branchFilter = document.getElementById('filterAllBranch')?.value || '';
    
    const params = new URLSearchParams({
        action: 'export_students',
        search: searchTerm,
        class_filter: classFilter,
        house_filter: houseFilter,
        branch_filter: branchFilter
    });
    
    window.open(`../../super_admin_api.php?${params}`, '_blank');
}

// Reset filters
function resetFilters() {
    document.getElementById('searchStudent').value = '';
    document.getElementById('filterClass').value = '';
    document.getElementById('filterHouse').value = '';
    loadStudents();
}

// Load reports and charts
function loadReports() {
    // Load house performance chart
    fetch('../../super_admin_api.php?action=get_house_performance')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('houseChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Total Points',
                        data: data.data,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    
    // Load branch distribution chart
    fetch('../../super_admin_api.php?action=get_branch_distribution')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('branchChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)'
                        ]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });
    
    // Load activity chart
    fetch('../../super_admin_api.php?action=get_monthly_activity')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('activityChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Events',
                        data: data.events,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.4
                    }, {
                        label: 'Appreciations',
                        data: data.appreciations,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
}

// Update dashboard statistics
function updateDashboardStats() {
    fetch('../../super_admin_api.php?action=get_dashboard_stats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-classes').textContent = data.total_classes;
            document.getElementById('total-students').textContent = data.total_students;
            document.getElementById('total-houses').textContent = data.total_houses;
            document.getElementById('total-events').textContent = data.total_events;
        })
        .catch(error => {
            console.error('Error updating dashboard stats:', error);
        });
}

// Show alert messages
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of main content
    const mainContent = document.querySelector('main .pt-3');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Award appreciation points
function awardAppreciation() {
    const studentId = document.getElementById('appreciationStudentId').value;
    const eventId = document.getElementById('appreciationEventId').value;
    const points = document.getElementById('appreciationPoints').value;
    const reason = document.getElementById('appreciationReason').value;
    
    if (!studentId || !eventId || !points || !reason) {
        showAlert('All fields are required', 'danger');
        return;
    }
    
    const data = {
        student_id: studentId,
        event_id: eventId,
        points: points,
        reason: reason
    };
    
    fetch('../../super_admin_api.php?action=awardAppreciation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Appreciation points awarded successfully!', 'success');
            document.getElementById('awardAppreciationForm').reset();
            document.getElementById('selectedAppreciationStudentInfo').classList.add('d-none');
            loadRecentAppreciations();
            updateDashboardStats();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error awarding appreciation:', error);
        showAlert('Error awarding appreciation points', 'danger');
    });
}

// Load events for appreciation dropdowns
function loadAppreciationEvents() {
    fetch('../../super_admin_api.php?action=getEvents')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const eventSelects = ['appreciationEventId', 'bulkAppreciationEventId', 'appreciationFilter'];
                
                eventSelects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    if (select) {
                        // Clear existing options except first one
                        const firstOption = select.firstElementChild;
                        if (firstOption) {
                            select.innerHTML = '';
                            select.appendChild(firstOption);
                        }
                        
                        data.events.forEach(event => {
                            const option = document.createElement('option');
                            option.value = event.id;
                            option.textContent = `${event.name} - ${event.date}`;
                            select.appendChild(option);
                        });
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading events for appreciations:', error);
            showAlert('Error loading events', 'danger');
        });
}

// Load students for event dropdown
function loadStudentsForEvent() {
    const eventId = document.getElementById('appreciationEventId').value;
    const studentSelect = document.getElementById('appreciationStudentSelect');
    
    if (!eventId) {
        if (studentSelect) {
            studentSelect.innerHTML = '<option value="">Select a student...</option>';
            studentSelect.disabled = true;
        }
        return;
    }
    
    fetch(`../../super_admin_api.php?action=getStudentsForEvent&event_id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (studentSelect) {
                if (data.success) {
                    studentSelect.innerHTML = '<option value="">Select a student...</option>';
                    data.students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = student.name;
                        studentSelect.appendChild(option);
                    });
                    studentSelect.disabled = false;
                } else {
                    studentSelect.innerHTML = '<option value="">No students found</option>';
                    studentSelect.disabled = true;
                }
            }
        })
        .catch(error => {
            console.error('Error loading students for event:', error);
            if (studentSelect) {
                studentSelect.innerHTML = '<option value="">Error loading students</option>';
                studentSelect.disabled = true;
            }
        });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    updateDashboardStats();
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-fill password with student ID
    const studentIdInput = document.getElementById('studentId');
    const studentPasswordInput = document.getElementById('studentPassword');
    
    if (studentIdInput && studentPasswordInput) {
        studentIdInput.addEventListener('input', function() {
            studentPasswordInput.value = this.value;
        });
    }
    
    // Initialize search functionality with debouncing
    const searchStudent = document.getElementById('searchStudent');
    if (searchStudent) {
        searchStudent.addEventListener('input', debounce(function() {
            loadStudents();
        }, 300));
    }
    
    const searchAllStudents = document.getElementById('searchAllStudents');
    if (searchAllStudents) {
        searchAllStudents.addEventListener('input', debounce(function() {
            loadAllStudents(1);
        }, 300));
    }
    
    const searchParticipants = document.getElementById('searchParticipants');
    if (searchParticipants) {
        searchParticipants.addEventListener('input', debounce(function() {
            loadEventParticipants();
        }, 300));
    }
    
    // Add filter change listeners
    ['filterClass', 'filterHouse'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', loadStudents);
        }
    });
    
    ['filterAllClass', 'filterAllHouse', 'filterAllBranch', 'sortBy'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', () => loadAllStudents(1));
        }
    });
    
    // Setup student search functionality for points modal
    setupStudentSearch();
    
    // Setup participant search functionality
    setupParticipantSearch();
    
    // Reset forms when modals are closed
    const addPointsModal = document.getElementById('addPointsModal');
    if (addPointsModal) {
        addPointsModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('addPointsForm').reset();
            document.getElementById('selectedStudentInfo').classList.add('d-none');
            document.getElementById('studentSuggestions').style.display = 'none';
        });
    }
    
    const addParticipantModal = document.getElementById('addParticipantModal');
    if (addParticipantModal) {
        addParticipantModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('addParticipantForm').reset();
            document.getElementById('selectedParticipantInfo').classList.add('d-none');
            document.getElementById('participantSuggestions').style.display = 'none';
        });
    }
});

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Add points to student functionality
function addPointsToStudent() {
    const formData = new FormData(document.getElementById('addPointsForm'));
    
    fetch('../../super_admin_api.php?action=add_points_to_student', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Points added successfully!', 'success');
            document.getElementById('addPointsForm').reset();
            document.getElementById('selectedStudentInfo').classList.add('d-none');
            bootstrap.Modal.getInstance(document.getElementById('addPointsModal')).hide();
            loadStudents();
            updateDashboardStats();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding points:', error);
        showAlert('Error adding points to student', 'danger');
    });
}

// Student search functionality for points modal
function setupStudentSearch() {
    const studentIdInput = document.getElementById('pointsStudentId');
    const suggestionsDiv = document.getElementById('studentSuggestions');
    const selectedInfo = document.getElementById('selectedStudentInfo');
    const selectedName = document.getElementById('selectedStudentName');
    const selectedDetails = document.getElementById('selectedStudentDetails');
    
    let searchTimeout;
    
    if (studentIdInput) {
        studentIdInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                selectedInfo.classList.add('d-none');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`../../super_admin_api.php?action=search_students&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestionsDiv.innerHTML = '';
                        
                        if (data.length > 0) {
                            data.forEach(student => {
                                const suggestion = document.createElement('a');
                                suggestion.className = 'dropdown-item';
                                suggestion.href = '#';
                                suggestion.innerHTML = `
                                    <strong>${student.student_id}</strong> - ${student.name}<br>
                                    <small class="text-muted">${student.branch} ${student.section} | ${student.house_name}</small>
                                `;
                                suggestion.onclick = (e) => {
                                    e.preventDefault();
                                    selectStudent(student);
                                };
                                suggestionsDiv.appendChild(suggestion);
                            });
                            suggestionsDiv.style.display = 'block';
                        } else {
                            suggestionsDiv.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error searching students:', error);
                        suggestionsDiv.style.display = 'none';
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!studentIdInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });
    }
    
    function selectStudent(student) {
        studentIdInput.value = student.student_id;
        selectedName.textContent = student.name;
        selectedDetails.innerHTML = `
            <strong>ID:</strong> ${student.student_id} | 
            <strong>Branch:</strong> ${student.branch} ${student.section} | 
            <strong>House:</strong> ${student.house_name} | 
            <strong>Current Points:</strong> ${student.total_points}
        `;
        selectedInfo.classList.remove('d-none');
        suggestionsDiv.style.display = 'none';
    }
}

// Event Management Functions
let selectedEventId = null;

// Load events
function loadEvents() {
    fetch('../../super_admin_api.php?action=get_events')
        .then(response => response.json())
        .then(data => {
            const eventSelect = document.getElementById('eventSelect');
            // Clear existing options except first one
            eventSelect.innerHTML = '<option value="">Select an Event</option>';
            
            data.forEach(event => {
                const option = document.createElement('option');
                option.value = event.eid;
                option.textContent = `${event.title} - ${event.date} (${event.type})`;
                eventSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading events:', error);
            showAlert('Error loading events', 'danger');
        });
}

// Setup student search for appreciations
function setupAppreciationStudentSearch() {
    const studentIdInput = document.getElementById('appreciationStudentId');
    const suggestionsDiv = document.getElementById('appreciationStudentSuggestions');
    const selectedInfo = document.getElementById('selectedAppreciationStudentInfo');
    const selectedName = document.getElementById('selectedAppreciationStudentName');
    const selectedDetails = document.getElementById('selectedAppreciationStudentDetails');
    
    let searchTimeout;
    
    if (studentIdInput) {
        studentIdInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                if (suggestionsDiv) suggestionsDiv.style.display = 'none';
                if (selectedInfo) selectedInfo.classList.add('d-none');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`../../super_admin_api.php?action=search_students&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (suggestionsDiv) {
                            suggestionsDiv.innerHTML = '';
                            
                            if (data.length > 0) {
                                data.forEach(student => {
                                    const suggestion = document.createElement('a');
                                    suggestion.className = 'dropdown-item';
                                    suggestion.href = '#';
                                    suggestion.innerHTML = `
                                        <strong>${student.student_id}</strong> - ${student.name}<br>
                                        <small class="text-muted">${student.branch} ${student.section} | ${student.house_name}</small>
                                    `;
                                    suggestion.onclick = (e) => {
                                        e.preventDefault();
                                        selectAppreciationStudent(student);
                                    };
                                    suggestionsDiv.appendChild(suggestion);
                                });
                                suggestionsDiv.style.display = 'block';
                            } else {
                                suggestionsDiv.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error searching students:', error);
                        if (suggestionsDiv) suggestionsDiv.style.display = 'none';
                    });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (suggestionsDiv && !studentIdInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });
    }
    
    function selectAppreciationStudent(student) {
        studentIdInput.value = student.student_id;
        if (selectedName) selectedName.textContent = student.name;
        if (selectedDetails) {
            selectedDetails.innerHTML = `
                <strong>ID:</strong> ${student.student_id} | 
                <strong>Branch:</strong> ${student.branch} ${student.section} | 
                <strong>House:</strong> ${student.house_name} | 
                <strong>Current Points:</strong> ${student.total_points}
            `;
        }
        if (selectedInfo) selectedInfo.classList.remove('d-none');
        if (suggestionsDiv) suggestionsDiv.style.display = 'none';
    }
}

// Load recent appreciations
function loadRecentAppreciations(page = 1) {
    const searchTerm = document.getElementById('searchAppreciations')?.value || '';
    const eventFilter = document.getElementById('appreciationFilter')?.value || '';
    
    const params = new URLSearchParams({
        action: 'get_appreciations',
        page: page,
        limit: 10,
        search: searchTerm,
        event_filter: eventFilter
    });
    
    fetch(`../../super_admin_api.php?${params}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('appreciationsTableBody');
            if (tbody) {
                tbody.innerHTML = '';
                
                if (data.appreciations && data.appreciations.length > 0) {
                    data.appreciations.forEach(appreciation => {
                        const row = `
                            <tr>
                                <td><strong>${appreciation.student_id}</strong></td>
                                <td>${appreciation.student_name}</td>
                                <td><span class="badge bg-info">${appreciation.event_title || 'N/A'}</span></td>
                                <td><span class="badge bg-success">${appreciation.points}</span></td>
                                <td><small>${appreciation.reason}</small></td>
                                <td><small>${formatDate(appreciation.created_at)}</small></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAppreciation(${appreciation.id})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No appreciations found</td></tr>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading appreciations:', error);
            const tbody = document.getElementById('appreciationsTableBody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>';
            }
        });
}

// Delete appreciation
function deleteAppreciation(appreciationId) {
    if (confirm('Are you sure you want to delete this appreciation? This action cannot be undone.')) {
        fetch('../../super_admin_api.php?action=delete_appreciation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({appreciation_id: appreciationId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Appreciation deleted successfully!', 'success');
                loadRecentAppreciations();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error deleting appreciation:', error);
            showAlert('Error deleting appreciation', 'danger');
        });
    }
}

// Initialize appreciations section
function initializeAppreciationsSection() {
    // Load events for dropdowns
    loadAppreciationEvents();
    
    // Setup student search
    setupAppreciationStudentSearch();
    
    // Load recent appreciations
    loadRecentAppreciations();
    
    // Setup form submission
    const awardForm = document.getElementById('awardAppreciationForm');
    if (awardForm) {
        awardForm.addEventListener('submit', function(e) {
            e.preventDefault();
            awardAppreciation();
        });
    }
    
    // Setup event change handler
    const eventSelect = document.getElementById('appreciationEventId');
    if (eventSelect) {
        eventSelect.addEventListener('change', loadStudentsForEvent);
    }
}

// Enhanced showSection function to handle appreciations
const originalShowSection = showSection;
showSection = function(sectionName) {
    originalShowSection.call(this, sectionName);
    
    // Load section-specific data for appreciations
    if (sectionName === 'appreciations') {
        setTimeout(() => {
            initializeAppreciationsSection();
        }, 100);
    }
};

// Utility function to format dates
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Setup participant search (stub function to prevent errors)
function setupParticipantSearch() {
    // This function exists to prevent JavaScript errors
    // Can be implemented later for event management
}

// Quick functions for better UX
function refreshCurrentSection() {
    const activeSection = document.querySelector('.content-section[style*="block"]');
    if (activeSection) {
        const sectionId = activeSection.id.replace('-section', '');
        showSection(sectionId);
    }
}

// Enhanced error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showAlert('An unexpected error occurred. Please refresh the page and try again.', 'danger');
});

// Auto-refresh dashboard stats every 5 minutes
setInterval(() => {
    updateDashboardStats();
}, 300000); // 5 minutes

console.log('Super Admin Dashboard JavaScript loaded successfully!');