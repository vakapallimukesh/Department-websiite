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
    }
}

// Load classes data
function loadClasses() {
    fetch('super_admin_api.php?action=get_classes')
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
    
    fetch('super_admin_api.php?action=add_class', {
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
        fetch('super_admin_api.php?action=delete_class', {
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
    fetch('super_admin_api.php?action=get_students')
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
    fetch('super_admin_api.php?action=get_classes')
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
    fetch('super_admin_api.php?action=get_houses')
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
    
    fetch('super_admin_api.php?action=add_student', {
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
    fetch(`super_admin_api.php?action=get_student&id=${studentId}`)
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
    
    fetch('super_admin_api.php?action=update_student', {
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
        fetch('super_admin_api.php?action=delete_student', {
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
            
            fetch('super_admin_api.php?action=bulk_upload_points', {
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
            
            fetch('super_admin_api.php?action=bulk_upload_students', {
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
    
    fetch(`super_admin_api.php?${params}`)
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
    fetch('super_admin_api.php?action=get_classes')
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
    fetch('super_admin_api.php?action=get_houses')
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
    
    window.open(`super_admin_api.php?${params}`, '_blank');
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
    fetch('super_admin_api.php?action=get_house_performance')
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
    fetch('super_admin_api.php?action=get_branch_distribution')
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
    fetch('super_admin_api.php?action=get_monthly_activity')
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
    fetch('super_admin_api.php?action=get_dashboard_stats')
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

// Add points to student functionality
function addPointsToStudent() {
    const formData = new FormData(document.getElementById('addPointsForm'));
    
    fetch('super_admin_api.php?action=add_points_to_student', {
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
                fetch(`super_admin_api.php?action=search_students&query=${encodeURIComponent(query)}`)
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
    fetch('super_admin_api.php?action=get_events')
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

// Select event
function selectEvent() {
    const eventSelect = document.getElementById('eventSelect');
    const eventId = eventSelect.value;
    
    if (!eventId) {
        document.getElementById('selectedEventCard').style.display = 'none';
        document.getElementById('participantsSection').style.display = 'none';
        selectedEventId = null;
        return;
    }
    
    selectedEventId = eventId;
    
    // Load event details
    fetch(`super_admin_api.php?action=get_event_details&id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.event;
                document.getElementById('eventTitle').textContent = event.title;
                document.getElementById('eventDescription').textContent = event.description || 'No description available';
                document.getElementById('eventDate').textContent = event.date;
                document.getElementById('eventType').textContent = event.type;
                
                // Set event IDs in modals
                document.getElementById('participantEventId').value = eventId;
                document.getElementById('bulkParticipantEventId').value = eventId;
                document.getElementById('bulkPointsEventId').value = eventId;
                
                document.getElementById('selectedEventCard').style.display = 'block';
                document.getElementById('participantsSection').style.display = 'block';
                
                loadEventParticipants();
            } else {
                showAlert('Error loading event details', 'danger');
            }
        })
        .catch(error => {
            console.error('Error loading event details:', error);
            showAlert('Error loading event details', 'danger');
        });
}

// Create new event
function createEvent() {
    const formData = new FormData(document.getElementById('createEventForm'));
    
    fetch('super_admin_api.php?action=create_event', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Event created successfully!', 'success');
            document.getElementById('createEventForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('createEventModal')).hide();
            loadEvents();
            updateDashboardStats();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error creating event:', error);
        showAlert('Error creating event', 'danger');
    });
}

// Load event participants
function loadEventParticipants() {
    if (!selectedEventId) return;
    
    const searchTerm = document.getElementById('searchParticipants')?.value || '';
    
    fetch(`super_admin_api.php?action=get_event_participants&event_id=${selectedEventId}&search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('participantsTableBody');
            tbody.innerHTML = '';
            
            let totalPoints = 0;
            const houseStats = {};
            
            data.participants.forEach(participant => {
                totalPoints += parseFloat(participant.points || 0);
                
                // Track house participation
                if (!houseStats[participant.house_name]) {
                    houseStats[participant.house_name] = 0;
                }
                houseStats[participant.house_name]++;
                
                const row = `
                    <tr>
                        <td><strong>${participant.student_id}</strong></td>
                        <td>${participant.name}</td>
                        <td><span class="badge bg-secondary">${participant.house_name}</span></td>
                        <td><span class="badge bg-success">${participant.points || 0}</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="editParticipantPoints('${participant.student_id}', ${participant.points || 0})" title="Edit Points">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeParticipant('${participant.student_id}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
            
            // Update statistics
            const participantCount = data.participants.length;
            document.getElementById('participantCount').textContent = `${participantCount} participants`;
            document.getElementById('totalParticipants').textContent = participantCount;
            document.getElementById('totalPointsAwarded').textContent = totalPoints.toFixed(1);
            document.getElementById('averagePoints').textContent = participantCount > 0 ? (totalPoints / participantCount).toFixed(1) : '0';
            
            // Update house participation
            const houseParticipationDiv = document.getElementById('houseParticipation');
            houseParticipationDiv.innerHTML = '';
            Object.entries(houseStats).forEach(([house, count]) => {
                houseParticipationDiv.innerHTML += `
                    <div class="mb-1">
                        <small class="text-muted">${house}:</small>
                        <span class="float-end">${count}</span>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error('Error loading participants:', error);
            showAlert('Error loading participants', 'danger');
        });
}

// Add participant
function addParticipant() {
    const formData = new FormData(document.getElementById('addParticipantForm'));
    
    fetch('super_admin_api.php?action=add_participant', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Participant added successfully!', 'success');
            document.getElementById('addParticipantForm').reset();
            document.getElementById('selectedParticipantInfo').classList.add('d-none');
            bootstrap.Modal.getInstance(document.getElementById('addParticipantModal')).hide();
            loadEventParticipants();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error adding participant:', error);
        showAlert('Error adding participant', 'danger');
    });
}

// Setup participant search
function setupParticipantSearch() {
    const participantIdInput = document.getElementById('participantStudentId');
    const suggestionsDiv = document.getElementById('participantSuggestions');
    const selectedInfo = document.getElementById('selectedParticipantInfo');
    const selectedName = document.getElementById('selectedParticipantName');
    const selectedDetails = document.getElementById('selectedParticipantDetails');
    
    let searchTimeout;
    
    if (participantIdInput) {
        participantIdInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                selectedInfo.classList.add('d-none');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                fetch(`super_admin_api.php?action=search_students&query=${encodeURIComponent(query)}`)
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
                                    selectParticipant(student);
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
            if (!participantIdInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                suggestionsDiv.style.display = 'none';
            }
        });
    }
    
    function selectParticipant(student) {
        participantIdInput.value = student.student_id;
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

// Edit participant points
function editParticipantPoints(studentId, currentPoints) {
    const newPoints = prompt(`Edit points for student ${studentId}:`, currentPoints);
    
    if (newPoints !== null && !isNaN(newPoints)) {
        const formData = new FormData();
        formData.append('event_id', selectedEventId);
        formData.append('student_id', studentId);
        formData.append('points', newPoints);
        
        fetch('super_admin_api.php?action=update_participant_points', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Points updated successfully!', 'success');
                loadEventParticipants();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error updating points:', error);
            showAlert('Error updating participant points', 'danger');
        });
    }
}

// Remove participant
function removeParticipant(studentId) {
    if (confirm(`Are you sure you want to remove student ${studentId} from this event?`)) {
        fetch('super_admin_api.php?action=remove_participant', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                event_id: selectedEventId,
                student_id: studentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Participant removed successfully!', 'success');
                loadEventParticipants();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error removing participant:', error);
            showAlert('Error removing participant', 'danger');
        });
    }
}

// Bulk award points to all participants
function bulkAwardPoints() {
    const formData = new FormData(document.getElementById('bulkParticipantPointsForm'));
    formData.append('overwrite', document.getElementById('overwritePoints').checked);
    
    fetch('super_admin_api.php?action=bulk_award_participant_points', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(`Points awarded to ${data.updated} participants!`, 'success');
            document.getElementById('bulkParticipantPointsForm').reset();
            bootstrap.Modal.getInstance(document.getElementById('bulkParticipantPointsModal')).hide();
            loadEventParticipants();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error awarding points:', error);
        showAlert('Error awarding points to participants', 'danger');
    });
}

// Add points to all participants (quick action)
function addPointsToAllParticipants() {
    const points = prompt('Enter points to award to all participants:');
    const reason = prompt('Enter reason for points:');
    
    if (points !== null && reason !== null && !isNaN(points)) {
        const formData = new FormData();
        formData.append('bulkPointsEventId', selectedEventId);
        formData.append('bulkPointsValue', points);
        formData.append('bulkPointsReason', reason);
        formData.append('overwrite', false);
        
        fetch('super_admin_api.php?action=bulk_award_participant_points', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`Points awarded to ${data.updated} participants!`, 'success');
                loadEventParticipants();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error awarding points:', error);
            showAlert('Error awarding points to participants', 'danger');
        });
    }
}

// Export participants
function exportParticipants() {
    if (!selectedEventId) {
        showAlert('Please select an event first', 'warning');
        return;
    }
    
    window.open(`super_admin_api.php?action=export_event_participants&event_id=${selectedEventId}`, '_blank');
}

// Clear all participants
function clearAllParticipants() {
    if (confirm('Are you sure you want to remove ALL participants from this event? This action cannot be undone.')) {
        fetch('super_admin_api.php?action=clear_all_participants', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({event_id: selectedEventId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(`Removed ${data.removed} participants from the event`, 'success');
                loadEventParticipants();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error clearing participants:', error);
            showAlert('Error clearing participants', 'danger');
        });
    }
}

// Initialize event management
document.addEventListener('DOMContentLoaded', function() {
    // Set default password to student ID when student ID changes
    const studentIdInput = document.getElementById('studentId');
    const studentPasswordInput = document.getElementById('studentPassword');
    
    if (studentIdInput && studentPasswordInput) {
        studentIdInput.addEventListener('input', function() {
            studentPasswordInput.value = this.value;
        });
    }
    
    // Add search functionality
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
    
    // Setup student search functionality
    setupStudentSearch();
    
    // Reset add points form when modal is closed
    const addPointsModal = document.getElementById('addPointsModal');
    if (addPointsModal) {
        addPointsModal.addEventListener('hidden.bs.modal', function() {
            document.getElementById('addPointsForm').reset();
            document.getElementById('selectedStudentInfo').classList.add('d-none');
            document.getElementById('studentSuggestions').style.display = 'none';
        });
    }
    
    // Setup participant search functionality
    setupParticipantSearch();
    
    // Setup bulk participants form
    const bulkParticipantsForm = document.getElementById('bulkParticipantsForm');
    if (bulkParticipantsForm) {
        bulkParticipantsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadResult = document.getElementById('bulkParticipantsResult');
            
            uploadResult.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Processing participants...</div>';
            
            fetch('super_admin_api.php?action=bulk_upload_participants', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadResult.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Participants Upload Successful!</h6>
                            <p>Successfully processed ${data.processed} records.</p>
                            <p>Added ${data.successful} new participants.</p>
                            ${data.skipped > 0 ? `<p class="text-info">Skipped ${data.skipped} duplicates.</p>` : ''}
                            ${data.errors.length > 0 ? `<div class="mt-2"><strong>Errors:</strong><ul class="mb-0">${data.errors.map(err => `<li>${err}</li>`).join('')}</ul></div>` : ''}
                        </div>
                    `;
                    bulkParticipantsForm.reset();
                    loadEventParticipants();
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
                console.error('Error uploading participants:', error);
                uploadResult.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Upload Error!</h6>
                        <p>An error occurred while processing the participants file.</p>
                    </div>
                `;
            });
        });
    }
    
    // Setup search participants
    const searchParticipants = document.getElementById('searchParticipants');
    if (searchParticipants) {
        searchParticipants.addEventListener('input', debounce(function() {
            loadEventParticipants();
        }, 300));
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

// Quick functions for better UX
function refreshCurrentSection() {
    const activeSection = document.querySelector('.content-section[style*="block"]');
    if (activeSection) {
        const sectionId = activeSection.id.replace('-section', '');
        showSection(sectionId);
    }
}

function showLoading(element) {
    if (element) {
        element.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    }
}

function hideLoading() {
    // Auto-hide loading indicators after operations
    setTimeout(() => {
        const loadingElements = document.querySelectorAll('.fa-spinner');
        loadingElements.forEach(el => {
            const parent = el.closest('.alert, .card-body');
            if (parent && parent.textContent.includes('Loading')) {
                parent.style.display = 'none';
            }
        });
    }, 5000);
}

// Enhanced error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showAlert('An unexpected error occurred. Please refresh the page and try again.', 'danger');
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save forms (prevent default save dialog)
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            const saveButton = activeModal.querySelector('.btn-primary');
            if (saveButton) {
                saveButton.click();
            }
        }
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal.show');
        if (activeModal) {
            bootstrap.Modal.getInstance(activeModal).hide();
        }
    }
});

// Auto-refresh dashboard stats every 5 minutes
setInterval(() => {
    updateDashboardStats();
}, 300000); // 5 minutes

// Utility function to format numbers
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

// Utility function to format dates
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Enhanced validation functions
function validateStudentId(studentId) {
    // Adjust pattern based on your student ID format
    const pattern = /^[0-9]{2}B[0-9]{2}A[0-9]{4}$/;
    return pattern.test(studentId);
}

function validateEmail(email) {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return pattern.test(email);
}

// Add visual feedback for form submissions
function addFormSubmitFeedback(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitButton.disabled = true;
                
                // Re-enable after 5 seconds as fallback
                setTimeout(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                }, 5000);
            }
        });
    }
}

// Apply form feedback to all forms
['addClassForm', 'addStudentForm', 'bulkUploadForm', 'bulkStudentForm', 'addPointsForm', 'createEventForm', 'addParticipantForm', 'bulkParticipantsForm', 'bulkParticipantPointsForm'].forEach(formId => {
    addFormSubmitFeedback(formId);
});

console.log('Super Admin Dashboard JavaScript loaded successfully!');