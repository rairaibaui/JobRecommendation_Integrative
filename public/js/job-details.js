function toggleJobDetails(button) {
    const jobCard = button.closest('.job-card');
    const detailsSection = jobCard.querySelector('.job-details');
    const isExpanded = detailsSection.classList.contains('expanded');
    
    // Toggle the expanded class
    detailsSection.classList.toggle('expanded');
    
    // Update button text
    button.innerHTML = isExpanded ? 
        '<i class="fas fa-chevron-down"></i> View Details' : 
        '<i class="fas fa-chevron-up"></i> Hide Details';
    
    // Animate the height
    if (isExpanded) {
        detailsSection.style.maxHeight = '0';
    } else {
        detailsSection.style.maxHeight = detailsSection.scrollHeight + 'px';
    }
}