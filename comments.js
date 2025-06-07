// Comments functionality
let currentRating = 0;
let currentProductId = null;

// Initialize comments system when modal opens
function initCommentsSystem(productId) {
    currentProductId = productId;
    console.log('Initializing comments for product:', productId);
    
    // Initialize star rating
    initStarRating();
    
    // Load comments
    loadProductComments(productId);
}

// Initialize star rating system
function initStarRating() {
    const stars = document.querySelectorAll('#userStarRating .star');
    
    stars.forEach((star, index) => {
        // Click event
        star.addEventListener('click', function() {
            currentRating = parseInt(this.dataset.value);
            updateStarDisplay();
        });
        
        // Hover events
        star.addEventListener('mouseenter', function() {
            const hoverRating = parseInt(this.dataset.value);
            highlightStars(hoverRating, true);
        });
    });
    
    // Reset on mouse leave
    const starRating = document.getElementById('userStarRating');
    if (starRating) {
        starRating.addEventListener('mouseleave', function() {
            updateStarDisplay();
        });
    }
}

// Highlight stars up to given rating
function highlightStars(rating, isHover = false) {
    const stars = document.querySelectorAll('#userStarRating .star');
    stars.forEach((star, index) => {
        const starValue = parseInt(star.dataset.value);
        
        star.classList.remove('active', 'hover');
        
        if (starValue <= rating) {
            star.classList.add(isHover ? 'hover' : 'active');
        }
    });
}

// Update star display based on current rating
function updateStarDisplay() {
    highlightStars(currentRating);
}

// Load comments for product
function loadProductComments(productId) {
    const commentsList = document.getElementById('commentsList');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    const commentsLoading = document.getElementById('commentsLoading');
    
    // Show loading state
    if (commentsLoading) commentsLoading.style.display = 'block';
    if (noCommentsMessage) noCommentsMessage.style.display = 'none';
    if (commentsList) commentsList.innerHTML = '';
    
    fetch('product-comments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'get_comments',
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayComments(data.comments);
            updateRatingSummary(data.average_rating, data.total_comments);
        } else {
            showCommentsError('Error loading comments');
        }
    })
    .catch(error => {
        console.error('Error loading comments:', error);
        showCommentsError('Network error while loading comments');
    })
    .finally(() => {
        if (commentsLoading) commentsLoading.style.display = 'none';
    });
}

// Display comments in the list
function displayComments(comments) {
    const commentsList = document.getElementById('commentsList');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    
    if (!commentsList) return;
    
    if (comments.length === 0) {
        commentsList.innerHTML = '';
        if (noCommentsMessage) noCommentsMessage.style.display = 'block';
        return;
    }
    
    if (noCommentsMessage) noCommentsMessage.style.display = 'none';
    
    let html = '';
    comments.forEach(comment => {
        html += createCommentHTML(comment);
    });
    
    commentsList.innerHTML = html;
}

// Create HTML for a single comment
function createCommentHTML(comment) {
    const stars = generateStarsHTML(comment.rating);
    const date = formatDate(comment.date);
    
    return `
        <div class="comment-item" data-comment-id="${comment.comment_id}">
            <div class="comment-header">
                <div class="comment-user-info">
                    <div class="comment-author">${escapeHtml(comment.user_name)}</div>
                    <div class="comment-rating">${stars}</div>
                </div>
                <div class="comment-date">${date}</div>
            </div>
            <p class="comment-text">${escapeHtml(comment.text)}</p>
        </div>
    `;
}

// Generate stars HTML for rating
function generateStarsHTML(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= rating ? '★' : '☆';
    }
    return stars;
}

// Update rating summary
function updateRatingSummary(averageRating, totalComments) {
    const averageStars = document.getElementById('averageStars');
    const ratingText = document.getElementById('ratingText');
    
    if (averageStars && averageRating > 0) {
        averageStars.innerHTML = generateStarsHTML(Math.round(averageRating));
    }
    
    if (ratingText) {
        if (totalComments === 0) {
            ratingText.textContent = 'No reviews yet';
        } else {
            ratingText.textContent = `${averageRating}/5 (${totalComments} review${totalComments !== 1 ? 's' : ''})`;
        }
    }
}

// Submit a new comment
function submitComment() {
    if (!currentProductId) {
        showNotification('No product selected', 'error');
        return;
    }
    
    const commentText = document.getElementById('commentText');
    if (!commentText) return;
    
    const text = commentText.value.trim();
    
    // Validation
    if (currentRating === 0) {
        showNotification('Please select a rating', 'error');
        return;
    }
    
    if (text.length < 5) {
        showNotification('Comment must be at least 5 characters long', 'error');
        return;
    }
    
    // Disable submit button
    const submitBtn = document.querySelector('.submit-comment-btn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posting...';
    }
    
    fetch('product-comments.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: 'add_comment',
            product_id: currentProductId,
            comment_text: text,
            rating: currentRating
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Review posted successfully!', 'success');
            
            // Clear form
            commentText.value = '';
            currentRating = 0;
            updateStarDisplay();
            
            // Reload comments
            loadProductComments(currentProductId);
        } else {
            showNotification(data.message || 'Error posting review', 'error');
        }
    })
    .catch(error => {
        console.error('Error posting comment:', error);
        showNotification('Network error while posting review', 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Post Review';
        }
    });
}

// Helper functions
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

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showCommentsError(message) {
    const commentsList = document.getElementById('commentsList');
    const noCommentsMessage = document.getElementById('noCommentsMessage');
    
    if (commentsList) {
        commentsList.innerHTML = `<div class="error-message" style="padding: 20px; text-align: center; color: #dc3545;">${message}</div>`;
    }
    
    if (noCommentsMessage) noCommentsMessage.style.display = 'none';
}

function cancelComment() {
    const commentText = document.getElementById('commentText');
    if (commentText) commentText.value = '';
    
    currentRating = 0;
    updateStarDisplay();
}