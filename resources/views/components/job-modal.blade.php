<!-- Job Details Modal -->
<div id="jobModal" class="fixed inset-0 hidden z-50" style="background: rgba(0, 0, 0, 0.5); overflow: auto; opacity: 0; transition: opacity 0.3s ease;">
    <div class="bg-white w-4/5 max-w-4xl mx-auto my-10 rounded-lg shadow-lg relative modal-content" 
         style="background: white; width: 80%; max-width: 56rem; margin: 2.5rem auto; border-radius: 0.5rem; 
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); padding: 1.5rem; position: relative;
                transform: translateY(-20px); opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease;">
        
        <!-- Close Button -->
        <button onclick="closeModal()" class="absolute" 
                style="top: 1rem; right: 1rem; color: #4a5568; font-size: 1.5rem; font-weight: bold; border: none; background: none; cursor: pointer;">
            &times;
        </button>

        <!-- Job Header -->
        <div class="flex items-center justify-between mb-6" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <div>
                <h1 class="job-title text-2xl font-bold" style="font-size: 1.5rem; font-weight: bold; color: #1E3A5F;"></h1>
                <div class="job-meta" style="color: #666; margin-top: 0.5rem;">
                    <p class="job-location"><i class="fas fa-map-marker-alt" style="color: #648EB5;"></i> <span></span></p>
                    <p class="job-type"><i class="fas fa-briefcase" style="color: #648EB5;"></i> <span></span></p>
                    <p class="job-salary"><i class="fas fa-money-bill-wave" style="color: #648EB5;"></i> <span></span></p>
                </div>
            </div>
        </div>

        <!-- Job Details -->
        <div class="job-content" style="margin-top: 1.5rem;">
            <div class="mb-6" style="margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: bold; color: #1E3A5F; margin-bottom: 0.75rem;">Job Description</h2>
                <p class="job-description" style="color: #4a5568; line-height: 1.625;"></p>
            </div>

            <div class="mb-6">
                <h2 style="font-size: 1.25rem; font-weight: bold; color: #1E3A5F; margin-bottom: 0.75rem;">Required Skills</h2>
                <div class="job-skills" style="display: flex; flex-wrap: wrap; gap: 0.5rem;"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4" style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                <button onclick="closeModal()" class="btn-secondary" 
                        style="padding: 0.5rem 1rem; border: 1px solid #648EB5; border-radius: 0.375rem; color: #648EB5; background: white;">
                    Close
                </button>
                <button id="modalBookmarkBtn" class="btn-primary bookmark-btn" 
                        style="padding: 0.5rem 1rem; background: #648EB5; color: white; border: none; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="far fa-bookmark"></i>
                    <span>Bookmark Job</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openModal(jobData) {
    const modal = document.getElementById('jobModal');
    const modalContent = modal.querySelector('.modal-content');
    
    // Update modal content
    modal.querySelector('.job-title').textContent = jobData.title;
    modal.querySelector('.job-location span').textContent = jobData.location || 'N/A';
    modal.querySelector('.job-type span').textContent = jobData.type || 'Full-time';
    modal.querySelector('.job-salary span').textContent = jobData.salary || 'Negotiable';
    modal.querySelector('.job-description').textContent = jobData.description || '';
    
    // Update skills with animation delay
    const skillsContainer = modal.querySelector('.job-skills');
    skillsContainer.innerHTML = '';
    if (jobData.skills && jobData.skills.length > 0) {
        jobData.skills.forEach((skill, index) => {
            const skillBadge = document.createElement('span');
            skillBadge.className = 'skill';
            skillBadge.textContent = skill;
            skillBadge.style.cssText = `
                background: #648EB5;
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 0.25rem;
                font-size: 0.875rem;
                opacity: 0;
                transform: translateY(10px);
                transition: opacity 0.3s ease, transform 0.3s ease;
                transition-delay: ${index * 0.1}s;
            `;
            skillsContainer.appendChild(skillBadge);
            // Trigger animation after a brief delay
            setTimeout(() => {
                skillBadge.style.opacity = '1';
                skillBadge.style.transform = 'translateY(0)';
            }, 50);
        });
    } else {
        const noSkills = document.createElement('span');
        noSkills.textContent = 'No specific skills listed';
        noSkills.style.color = '#666';
        skillsContainer.appendChild(noSkills);
    }

    // Update bookmark button
    const bookmarkBtn = modal.querySelector('#modalBookmarkBtn');
    const icon = bookmarkBtn.querySelector('i');
    if (jobData.isBookmarked) {
        icon.className = 'fas fa-bookmark';
        bookmarkBtn.querySelector('span').textContent = 'Remove Bookmark';
    } else {
        icon.className = 'far fa-bookmark';
        bookmarkBtn.querySelector('span').textContent = 'Bookmark Job';
    }
    
    // Store job data for bookmark toggle
    bookmarkBtn.dataset.jobTitle = jobData.title;

    // Show modal with animations
    modal.style.display = 'block';
    // Trigger fade-in animations
    requestAnimationFrame(() => {
        modal.style.opacity = '1';
        modalContent.style.opacity = '1';
        modalContent.style.transform = 'translateY(0)';
    });
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('jobModal');
    const modalContent = modal.querySelector('.modal-content');
    
    // Trigger fade-out animations
    modal.style.opacity = '0';
    modalContent.style.opacity = '0';
    modalContent.style.transform = 'translateY(-20px)';
    
    // Remove modal after animations complete
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('jobModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Add keyboard support (Esc to close)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('jobModal');
        if (modal.style.display === 'block') {
            closeModal();
        }
    }
});

// Handle bookmark toggle in modal
document.getElementById('modalBookmarkBtn').addEventListener('click', function() {
    const jobTitle = this.dataset.jobTitle;
    const isCurrentlyBookmarked = this.querySelector('i').classList.contains('fas');
    
    // Reuse existing toggleBookmark function
    const cardButton = document.querySelector(`.job-card[data-title="${jobTitle}"] .bookmark-btn`);
    if (cardButton) {
        cardButton.click(); // This will trigger the existing toggleBookmark function
        
        // Update modal button state
        const icon = this.querySelector('i');
        const text = this.querySelector('span');
        if (isCurrentlyBookmarked) {
            icon.className = 'far fa-bookmark';
            text.textContent = 'Bookmark Job';
        } else {
            icon.className = 'fas fa-bookmark';
            text.textContent = 'Remove Bookmark';
        }
    }
});
</script>