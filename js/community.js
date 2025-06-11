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
            e.preventDefault();
            const postId = this.closest('.post-card').dataset.postId;
            const likeCountSpan = this.querySelector('.like-count');
            const heartIcon = this.querySelector('i');
            const buttonItself = this;

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=like&post_id=' + encodeURIComponent(postId)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    likeCountSpan.textContent = data.like_count;
                    heartIcon.style.color = data.liked ? 'red' : 'black';
                } else {
                    throw new Error(data.error || 'Error liking post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while processing your request.');
            });
        });
    });

    // COMMENT SUBMIT Button Click Handler
    document.querySelectorAll('.comment-submit').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const postId = this.dataset.postId;
            const postCard = this.closest('.post-card');
            const commentText = postCard.querySelector('.text').value.trim();
            const commentsList = postCard.querySelector('.comments-list');

            if (!commentText) {
                alert('Please enter a comment before submitting.');
                return;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=comment&post_id=' + encodeURIComponent(postId) +
                    '&text=' + encodeURIComponent(commentText)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const newCommentDiv = document.createElement('div');
                    newCommentDiv.classList.add('comment');
                    newCommentDiv.dataset.commentId = data.comment.comment_id;

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

                    commentsList.prepend(newCommentDiv);
                    postCard.querySelector('.text').value = '';

                    newCommentDiv.querySelector('.delete-comment-btn').addEventListener('click', function() {
                        deleteComment(this.dataset.commentId, newCommentDiv);
                    });
                } else {
                    throw new Error(data.error || 'Error adding comment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred while processing your request.');
            });
        });
    });

    // DELETE COMMENT Function (Used by both initial comments and new comments)
    function deleteComment(commentId, commentElement) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This comment will be deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-add',
                cancelButton: 'btn btn-add'
            }
        }).then((result) => {
            if (result.isConfirmed) {
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
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Your comment has been deleted.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-add',
                                    cancelButton: 'btn btn-add'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error deleting comment.',
                                icon: 'error',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-add',
                                    cancelButton: 'btn btn-add'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred.',
                            icon: 'error',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-add',
                                cancelButton: 'btn btn-add'
                            }
                        });
                    });
            }
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

            Swal.fire({
                title: 'Are you sure?',
                text: 'This post will be deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-add',
                    cancelButton: 'btn btn-add'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'action=delete_post&post_id=' + encodeURIComponent(postId)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            postCard.remove();
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Your post has been deleted.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false,
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-add',
                                    cancelButton: 'btn btn-add'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.error || 'Error deleting post',
                                icon: 'error',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-add',
                                    cancelButton: 'btn btn-add'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: error.message || 'An error occurred while processing your request.',
                            icon: 'error',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-add',
                                cancelButton: 'btn btn-add'
                            }
                        });
                    });
                }
            });
        });
    });
});
