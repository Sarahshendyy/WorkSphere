document.addEventListener("DOMContentLoaded", function() {

    // Hide all comments lists and comment forms on page load
    document.querySelectorAll(".comments-list, .comment-form").forEach(element => {
        element.style.display = "none";
    });

    document.querySelectorAll(".comment-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postCard = this.closest(".post-card");
            const commentsList = postCard.querySelector(".comments-list");
            const commentForm = postCard.querySelector(".comment-form");

            // Toggle visibility
            if (commentsList.style.display === "none") {
                commentsList.style.display = "block";
                commentForm.style.display = "block"; // Ensure text area appears
            } else {
                commentsList.style.display = "none";
                commentForm.style.display = "none"; // Hide both when clicked again
            }
        });
    });

    // LIKE Button Click Handler
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission / page reload
            const postId = this.closest('.post-card').dataset.postId; // get post id
            const likeCountSpan = this.querySelector('.like-count');
            const heartIcon = this.querySelector('i'); // get heart icon
            const buttonItself = this; // Store the button element

            // Send AJAX request
            fetch('', { // URL: empty string because we're submitting to the same page
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=like&post_id=' + encodeURIComponent(
                        postId) // Data sent to PHP
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        likeCountSpan.textContent = data
                            .like_count; // Update the like count

                        // Toggle the heart icon based on like status
                        if (data.liked) {
                            heartIcon.style.color = 'red'; // change icon
                        } else {
                            heartIcon.style.color = 'black'; // change icon
                        }
                    } else {
                        alert('Error liking post.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        });
    });


    // COMMENT SUBMIT Button Click Handler
    document.querySelectorAll('.comment-submit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form submission / page reload
            const postId = this.dataset.postId;
            const postCard = this.closest('.post-card'); // Find the parent post-card
            const commentText = postCard.querySelector('.text').value; // Get the comment text
            const commentsList = postCard.querySelector(
                '.comments-list'); // Get the comment list

            // Send AJAX request
            fetch('', { // URL: empty string because we're submitting to the same page
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=comment&post_id=' + encodeURIComponent(postId) +
                        '&text=' + encodeURIComponent(commentText) // Data sent to PHP
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Create new comment element
                        const newCommentDiv = document.createElement('div');
                        newCommentDiv.classList.add('comment');
                        newCommentDiv.dataset.commentId = data.comment
                            .comment_id; // Add comment ID

                        newCommentDiv.innerHTML = `
                        <a href="profile.php?user_id=${data.comment.user_id}">
                            <img src="./img/${data.comment.image}" alt="user image">
                        </a>
                        <div class="comment-content">
                            <a href="profile.php?user_id=${data.comment.user_id}">
                                <p><strong>${data.comment.name}:</strong></p>
                            </a>
                            <p>${data.comment.text}</p>
                        </div>
                        <button class="delete-icon delete-comment-btn" data-comment-id="${data.comment.comment_id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;

                        commentsList.prepend(newCommentDiv); // Add to comment list

                        // Clear the textarea
                        postCard.querySelector('.text').value = '';

                        // Add event listener for the newly added delete button
                        newCommentDiv.querySelector('.delete-comment-btn').addEventListener(
                            'click',
                            function() {
                                deleteComment(this.dataset.commentId, newCommentDiv);
                            });


                    } else {
                        alert('Error adding comment: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
        });
    });


    // DELETE COMMENT Function (Used by both initial comments and new comments)
    function deleteComment(commentId, commentElement) {
        fetch('', { // URL: empty string because we're submitting to the same page
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=delete&comment_id=' + encodeURIComponent(commentId) // Data sent to PHP
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    commentElement.remove(); // Remove comment from the UI
                } else {
                    alert('Error deleting comment.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
    }


    // Attach event listeners to existing delete buttons on page load
    document.querySelectorAll('.delete-comment-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const commentElement = this.closest('.comment'); // Find the comment div to remove
            deleteComment(commentId, commentElement); // Call the delete function
        });
    });
        // DELETE POST Button Click Handler
    document.querySelectorAll('.delete-post-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const postId = this.dataset.postId;
            const postCard = this.closest('.post-card');

            if (confirm('Are you sure you want to delete this post?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=delete_post&post_id=' + encodeURIComponent(postId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the post from the UI
                        postCard.remove();
                    } else {
                        alert('Error deleting post: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            }
        });
    });
});