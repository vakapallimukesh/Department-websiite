// Premium Faculty Page JavaScript
// SRKREC CSD-CSIT Department

// Initialize AOS
AOS.init({
    duration: 700,
    once: true,
    offset: 100,
    easing: 'ease-out-cubic'
});

// State Management
let currentFacultyIndex = 0;
let filteredFaculty = [...facultyData];
let currentFilter = 'all';

// DOM Elements
const facultyCardsList = document.getElementById('facultyCardsList');
const profileContent = document.getElementById('profileContent');
const facultyStats = document.getElementById('facultyStats');
const facultySearch = document.getElementById('facultySearch');
const filterChips = document.querySelectorAll('.filter-chip');
const prevBtn = document.getElementById('prevFaculty');
const nextBtn = document.getElementById('nextFaculty');
const backToTopBtn = document.getElementById('backToTop');

// Initialize Page
function init() {
    renderFacultyList();
    renderFacultyProfile(facultyData[0]);
    setupEventListeners();
}

// Render Faculty List (Right Aside)
function renderFacultyList() {
    facultyCardsList.innerHTML = '';
    
    filteredFaculty.forEach((faculty, index) => {
        const card = document.createElement('div');
        card.className = `faculty-mini-card chroma-card ${index === currentFacultyIndex ? 'active' : ''}`;
        card.setAttribute('data-index', index);
        card.setAttribute('data-aos', 'fade-left');
        card.setAttribute('data-aos-delay', index * 40);
        card.addEventListener('mousemove', handleChromaMove);
        
        card.innerHTML = `
            <div class="chroma-img-wrapper" style="width: 75px; height: 75px; padding: 4px; flex: none;">
                <img src="${faculty.photo}" alt="${faculty.name}" class="faculty-mini-photo" 
                     onerror="this.src='./assets/logos/default-avatar.png'" style="border-radius: 10px; width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="faculty-mini-info" style="z-index: 1;">
                <div class="faculty-mini-name">${faculty.name}</div>
                <div class="faculty-mini-designation">${faculty.designation}</div>
                <span class="dept-badge">${faculty.department}</span>
            </div>
        `;
        
        card.addEventListener('click', () => selectFaculty(index));
        facultyCardsList.appendChild(card);
    });
    
    updateNavigationButtons();
}

// ChromaGrid Spotlight Mouse Handler
function handleChromaMove(e) {
    const card = e.currentTarget;
    if (!card) return;
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    card.style.setProperty('--mouse-x', `${x}px`);
    card.style.setProperty('--mouse-y', `${y}px`);
}

// Render Faculty Profile with Left About & Right ChromaGrid Profile Card
function renderFacultyProfile(faculty) {
    profileContent.innerHTML = `
        <div class="profile-header">
            <!-- Left Side - About & Details -->
            <div class="profile-left">
                <h2 class="faculty-name">${faculty.name}</h2>
                <p class="faculty-designation">${faculty.designation}</p>
                <span class="faculty-department">${faculty.department} Faculty</span>
                
                <!-- About Him/Her Section -->
                <div class="info-card" style="margin-top: 20px; background: rgba(255,255,255,0.95);">
                    <h3 class="info-card-title">
                        <i class="fas fa-user-circle" style="color: #2563EB;"></i>
                        About ${faculty.name.split(' ')[1] || faculty.name}
                    </h3>
                    <p class="about-text" style="font-size: 1.05rem; line-height: 1.7; color: #334155;">${faculty.about}</p>
                </div>

                <div class="basic-info-grid">
                    <div class="info-item">
                        <i class="fas fa-briefcase"></i>
                        <div>
                            <div class="info-label">Experience</div>
                            <div class="info-value">${faculty.experience}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-graduation-cap"></i>
                        <div>
                            <div class="info-label">Qualification</div>
                            <div class="info-value">${faculty.qualification}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-star"></i>
                        <div>
                            <div class="info-label">Specialization</div>
                            <div class="info-value">${faculty.specialization}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Education Timeline -->
                <div class="info-card" style="margin-top: 20px;">
                    <h3 class="info-card-title">
                        <i class="fas fa-university" style="color: #10B981;"></i>
                        Education Background
                    </h3>
                    <div class="education-timeline">
                        ${faculty.education.map(edu => `
                            <div class="education-item">
                                <div class="education-degree">${edu.degree}</div>
                                <div class="education-institution">${edu.institution} • ${edu.year}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>

                <div class="social-buttons" style="margin-top: 20px;">
                    <a href="mailto:${faculty.email}" class="social-btn" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="${faculty.linkedin}" class="social-btn" title="LinkedIn" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="${faculty.scholar}" class="social-btn" title="Google Scholar" target="_blank">
                        <i class="fas fa-graduation-cap"></i>
                    </a>
                    <a href="${faculty.profile}" class="social-btn" title="Profile" target="_blank">
                        <i class="fas fa-user"></i>
                    </a>
                </div>
            </div>
            
            <!-- Right Side - ChromaGrid Profile Card -->
            <div class="profile-right">
                <article class="chroma-card chroma-profile-card" onmousemove="handleChromaMove(event)">
                    <div class="chroma-img-wrapper">
                        <img src="${faculty.photo}" alt="${faculty.name}" onerror="this.src='./assets/logos/default-avatar.png'">
                    </div>
                    <footer class="chroma-info">
                        <h3 class="name">${faculty.name}</h3>
                        <span class="handle">@${faculty.department.toLowerCase()}</span>
                        <p class="role">${faculty.designation}</p>
                    </footer>
                </article>
            </div>
        </div>
        
        <!-- Research Interests -->
        <div class="info-card" data-aos="fade-up">
            <h3 class="info-card-title">
                <i class="fas fa-microscope"></i>
                Research Interests
            </h3>
            <div class="research-tags">
                ${faculty.research.map(area => `
                    <span class="research-tag">${area}</span>
                `).join('')}
            </div>
        </div>
        
        <!-- Publications -->
        <div class="info-card" data-aos="fade-up" data-aos-delay="100">
            <h3 class="info-card-title">
                <i class="fas fa-book"></i>
                Recent Publications
            </h3>
            <div class="publications-list">
                ${faculty.publications.map(pub => `
                    <div class="publication-item">
                        <div class="publication-title">${pub.title}</div>
                        <div class="publication-meta">${pub.journal} • ${pub.year}</div>
                    </div>
                `).join('')}
            </div>
        </div>
        
        <!-- Subjects Teaching -->
        <div class="info-card" data-aos="fade-up" data-aos-delay="200">
            <h3 class="info-card-title">
                <i class="fas fa-chalkboard-teacher"></i>
                Subjects Teaching
            </h3>
            <div class="subject-chips">
                ${faculty.subjects.map(subject => `
                    <span class="subject-chip">${subject}</span>
                `).join('')}
            </div>
        </div>
        
        <!-- Achievements -->
        <div class="info-card" data-aos="fade-up" data-aos-delay="300">
            <h3 class="info-card-title">
                <i class="fas fa-trophy"></i>
                Achievements & Recognition
            </h3>
            <div class="achievements-grid">
                ${faculty.achievements.map(achievement => `
                    <div class="achievement-card">
                        <div class="achievement-icon">${achievement.icon}</div>
                        <div class="achievement-text">${achievement.text}</div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    
    // Render Statistics
    facultyStats.innerHTML = `
        <div class="stat-card" data-aos="zoom-in">
            <div class="stat-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-number">${faculty.stats.experience}</div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card" data-aos="zoom-in" data-aos-delay="100">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-number">${faculty.stats.publications}</div>
            <div class="stat-label">Publications</div>
        </div>
        <div class="stat-card" data-aos="zoom-in" data-aos-delay="200">
            <div class="stat-icon">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number">${faculty.stats.projects}</div>
            <div class="stat-label">Research Projects</div>
        </div>
        <div class="stat-card" data-aos="zoom-in" data-aos-delay="300">
            <div class="stat-icon">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-number">${faculty.stats.students}</div>
            <div class="stat-label">Students Guided</div>
        </div>
    `;
    
    // Refresh AOS
    AOS.refresh();
}

// Select Faculty
function selectFaculty(index) {
    currentFacultyIndex = index;
    
    // Update active card
    document.querySelectorAll('.faculty-mini-card').forEach((card, i) => {
        card.classList.toggle('active', i === index);
    });
    
    // Render new profile
    renderFacultyProfile(filteredFaculty[index]);
    
    // Update navigation buttons
    updateNavigationButtons();
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Navigation
function updateNavigationButtons() {
    prevBtn.disabled = currentFacultyIndex === 0;
    nextBtn.disabled = currentFacultyIndex === filteredFaculty.length - 1;
}

prevBtn.addEventListener('click', () => {
    if (currentFacultyIndex > 0) {
        selectFaculty(currentFacultyIndex - 1);
    }
});

nextBtn.addEventListener('click', () => {
    if (currentFacultyIndex < filteredFaculty.length - 1) {
        selectFaculty(currentFacultyIndex + 1);
    }
});

// Search Functionality
function searchFaculty(query) {
    const searchTerm = query.toLowerCase();
    
    filteredFaculty = facultyData.filter(faculty => {
        const matchesSearch = faculty.name.toLowerCase().includes(searchTerm) ||
                            faculty.designation.toLowerCase().includes(searchTerm) ||
                            faculty.department.toLowerCase().includes(searchTerm);
        
        const matchesFilter = currentFilter === 'all' || 
                            faculty.category.includes(currentFilter);
        
        return matchesSearch && matchesFilter;
    });
    
    currentFacultyIndex = 0;
    renderFacultyList();
    
    if (filteredFaculty.length > 0) {
        renderFacultyProfile(filteredFaculty[0]);
    } else {
        profileContent.innerHTML = `
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-search" style="font-size: 64px; color: #E2E8F0; margin-bottom: 20px;"></i>
                <h3 style="color: #64748B; font-size: 24px;">No faculty found</h3>
                <p style="color: #94a3b8;">Try adjusting your search or filter</p>
            </div>
        `;
        facultyStats.innerHTML = '';
    }
}

facultySearch.addEventListener('input', (e) => {
    searchFaculty(e.target.value);
});

// Filter Functionality
filterChips.forEach(chip => {
    chip.addEventListener('click', () => {
        // Update active chip
        filterChips.forEach(c => c.classList.remove('active'));
        chip.classList.add('active');
        
        // Get filter value
        currentFilter = chip.getAttribute('data-filter');
        
        // Apply filter
        searchFaculty(facultySearch.value);
    });
});

// Back to Top Button
window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        backToTopBtn.classList.add('show');
    } else {
        backToTopBtn.classList.remove('show');
    }
});

backToTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// Setup Event Listeners
function setupEventListeners() {
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' && !prevBtn.disabled) {
            selectFaculty(currentFacultyIndex - 1);
        } else if (e.key === 'ArrowRight' && !nextBtn.disabled) {
            selectFaculty(currentFacultyIndex + 1);
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', init);
