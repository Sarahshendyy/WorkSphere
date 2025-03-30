<?php 

# database connection file
include 'connection.php';

  if (isset($_SESSION['user_id'])) {

  	include 'app/helpers/user.php';
  	include 'app/helpers/chat.php';
  	include 'app/helpers/opened.php';
  	include 'app/helpers/timeAgo.php';

  	if (!isset($_GET['user'])) {
  		header("Location: home.php");
  		exit;
  	}

  	# Getting User data data
  	$chatWith = getUser($_GET['user'], $connect);

  	if (empty($chatWith)) {
  		header("Location: home.php");
  		exit;
  	}

  	$chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $connect);

  	opened($chatWith['user_id'], $connect, $chats);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/chat.css">
    <link rel="icon" href="img/keklogo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
        /* Additional CSS for file upload */
        .file-upload-wrapper {
            position: relative;
            margin-right: 10px;
        }
        .file-upload-label {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            background-color: #6c757d;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        .file-upload-label:hover {
            background-color: #5a6268;
        }
        .file-upload-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-name-display {
            margin-left: 5px;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100px;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-400 shadow p-4 rounded" id="bigdiv">
        <a href="home.php" class="fs-4 link-dark">&#8592;</a>

        <div class="d-flex align-items-center" id="frstdiv">
            <img src="./img/<?=$chatWith['image']?>" class="w-15 rounded-circle">

            <h3 class="display-4 fs-sm m-2">
                <?=$chatWith['name']?> <br>
                <div class="d-flex align-items-center" title="online">
                    <?php if (last_seen($chatWith['last_seen']) == "Active") { ?>
                        <div class="online"></div>
                        <small class="d-block p-1">Online</small>
                    <?php } else { ?>
                        <small class="d-block p-1">
                            Last seen: <?=last_seen($chatWith['last_seen'])?>
                        </small>
                    <?php } ?>
                </div>
            </h3>
        </div>

        <div class="shadow p-4 rounded d-flex flex-column mt-2 chat-box" id="chatBox">
            <?php 
                if (!empty($chats)) {
                    foreach($chats as $chat) {
                        if($chat['from_user'] == $_SESSION['user_id']) { 
                            if(!empty($chat['file'])) { ?>
                            <div style="display: flex; justify-content: end;">
                                <?php if($chat['edited'] == 1) { ?>
                                    <p class="edittxt">edited</p>
                                <?php } ?>
                                <p class="rtext align-self-end border rounded p-2 mb-1">
                                    <?=$chat['message']?>
                                    <?php if($chat['opened'] == 0) { ?>
                                        <i class="fa-regular fa-eye-slash" id="eyeicon"></i>
                                    <?php } else { ?>
                                        <i class="fa-regular fa-eye" id="eyeicon"></i>
                                    <?php } ?>
                                    <small class="d-block">
                                        <?=$chat['created_at']?>
                                    </small>
                                    
                                    <a href="/files/<?= htmlspecialchars($chat['file']) ?>" target="_blank" class="file-link">
                                        <i class="fas fa-paperclip"></i> View File
                                    </a>

                                    <?php
                                        $currentTime = date('Y-m-d H:i:s');
                                        $current_num = strtotime($currentTime);
                                        $created_num = strtotime($chat['created_at']);
                                    ?>
                                    <div class="dropdown d-inline">
                                        <button class="fa-solid fa-ellipsis-vertical" type="submit" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <?php if(($current_num - $created_num) < (60 * 90)) { ?>
                                                <li><a class="dropdown-item edit-message" data-id="<?=$chat['chat_id']?>" href="javascript:void()">Edit</a></li>
                                            <?php } ?>
                                            <li><a class="dropdown-item delete-message" data-id="<?=$chat['chat_id']?>" href="javascript:void()">Delete</a></li>
                                        </ul>
                                    </div>
                                </p>
                            </div>
                            <?php } else { ?>
                            <div style="display: flex; justify-content: end;">
                                <?php if($chat['edited'] == 1) { ?>
                                    <p class="edittxt">edited</p>
                                <?php } ?>
                                <p class="rtext align-self-end border rounded p-2 mb-1">
                                    <?=$chat['message']?>
                                    <?php if($chat['opened'] == 0) { ?>
                                        <i class="fa-regular fa-eye-slash" id="eyeicon"></i>
                                    <?php } else { ?>
                                        <i class="fa-regular fa-eye" id="eyeicon"></i>
                                    <?php } ?>
                                    <small class="d-block" id="scndtxt">
                                        <?=$chat['created_at']?>
                                    </small>
                                    <?php
                                        $currentTime = date('Y-m-d H:i:s');
                                        $current_num = strtotime($currentTime);
                                        $created_num = strtotime($chat['created_at']);
                                    ?>
                                    <div class="dropdown d-inline">
                                        <button class="fa-solid fa-ellipsis-vertical" type="submit" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <?php if(($current_num - $created_num) < (60 * 90)) { ?>
                                                <li><a class="dropdown-item edit-message" data-id="<?=$chat['chat_id']?>" href="javascript:void()">Edit</a></li>
                                            <?php } ?>
                                            <li><a class="dropdown-item delete-message" data-id="<?=$chat['chat_id']?>" href="javascript:void()">Delete</a></li>
                                        </ul>
                                    </div>
                                </p>
                            </div>
                            <?php } ?>
                        <?php } else { ?>
                        <p class="ltext border rounded p-2 mb-1">
                            <?=$chat['message']?> 
                            <?php if(!empty($chat['file'])) { ?>
                                <br>
                                <a href="/files/<?= htmlspecialchars($chat['file']) ?>" target="_blank" class="file-link">
                                    <i class="fas fa-paperclip"></i> View File
                                </a>
                            <?php } ?>
                            <small class="d-block">
                                <?php if($chat['edited'] == 1) { echo "edited"; } ?>
                                <?=$chat['created_at']?>
                            </small>
                        </p>
                        <?php } 
                    }
                } else { ?>
                <div class="alert alert-info text-center">
                    <i class="fa fa-comments d-block fs-big"></i>
                    No messages yet, Start the conversation
                </div>
                <?php } ?>
        </div>

        <div class="txtdiv">
            <form class="input-group mb-3" id="chatForm" enctype="multipart/form-data">
                <textarea cols="3" id="message" class="form-control" placeholder="Write your message.."></textarea>
                
                <!-- Improved file upload section -->
                <div class="file-upload-wrapper">
                    <label class="file-upload-label">
                        <i class="fa-solid fa-paperclip"></i>
                        <input type="file" id="file" name="file" class="file-upload-input">
                    </label>
                    <span id="file-name-display" class="file-name-display"></span>
                </div>
                
                <button class="btn btn-primary" id="sendBtn">
                    <i class="fa fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
 

 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
	var scrollDown = function(){
        let chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
	}

	scrollDown();

	$(document).ready(function(){
      
		$("#sendBtn").on('click', function() {
			var message = $("#message").val();
			var fileInput = document.getElementById('file');
			var file = fileInput.files[0]; 

			var formData = new FormData();
			formData.append('message', message);
			formData.append('to_user', <?=$chatWith['user_id']?>);
			if (file) {
				formData.append('file', file);
			}

			$.ajax({
				url: 'app/ajax/insert.php',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false, 
				success: function(data, status) {
					if (status === 'success') {
						$("#message").val("");
						$("#file").val("");
						$("#chatBox").append(data);
						scrollDown();
						location.reload();
					} else {
						alert("Message could not be sent.");
					}
				}
			});
		});


      /** 
      auto update last seen 
      for logged in user
      **/
      let lastSeenUpdate = function(){
      	$.get("app/ajax/update_last_seen.php");
      }
      lastSeenUpdate();
      /** 
      auto update last seen 
      every 10 sec
      **/
      setInterval(lastSeenUpdate, 10000);



      // auto refresh / reload
      let fechData = function(){
      	$.post("app/ajax/getMessage.php", 
      		   {
      		   	id_2: <?=$chatWith['user_id']?>
      		   },
      		   function(data, status){
                  $("#chatBox").append(data);
                  if (data != "") scrollDown();
      		    });
      }

      fechData();
      /** 
      auto update last seen 
      every 0.5 sec
      **/
      setInterval(fechData, 500);

	      // Edit message
			$(document).on('click', '.edit-message', function(e) {
			e.preventDefault();
			var messageId = $(this).data('id');
			var messageText = $(this).closest('.rtext').find('small').text();

			// Prompt for new message
			var newMessage = prompt("Edit your message:", messageText);
			if (newMessage != null && newMessage.trim() != "") {
				$.post("app/ajax/editMessage.php", {
					chat_id: messageId, 
					new_message: newMessage
				}, function(data, status) {
					if (data === 'success') {
						alert('Could not update the message.');
					} else {
                    	location.reload();
                	}
				});
			}
		});

	  // Handle message deletion
	  $(document).on('click', '.delete-message', function(e) {
        e.preventDefault();

        var chatId = $(this).data('id');

        // Confirm deletion
        if (confirm('Are you sure you want to delete this message?')) {
            $.post("app/ajax/deleteMessage.php", {
                chat_id: chatId
            }, function(response) {
                if (response === 'success') {
                    location.reload();
                } else {
                    alert("Error: Unable to delete message.");
                }
            });
        }
    });
    
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> </body>
</html>
<?php
    }else{
		header("location:index.php");
		exit;
	}
?>
