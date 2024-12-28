
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatGPT Integration</title>
    <style>
        /* Basic styling for the chat interface */
        #chat-container {
            width: 400px;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        #chat-history {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f9f9f9;
            border-bottom: 1px solid #ccc;
        }

        #chat-history .user {
            text-align: right;
            color: blue;
            margin: 5px 0;
        }

        #chat-history .assistant {
            text-align: left;
            color: green;
            margin: 5px 0;
        }

        #chat-history .error {
            text-align: center;
            color: red;
            margin: 5px 0;
        }

        #chat-input {
            display: flex;
            padding: 10px;
            background-color: #fff;
        }

        #chat-input input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        #chat-input button {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #chat-input button:hover {
            background-color: #0056b3;
        }

         body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #0f9d58;
            text-align: center;
        }

        .scrollbox {
            background-color: #fff;
            border: 2px solid #0f9d58;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
            height: 200px;
            overflow-y: auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Scrollbar Styles */
        .scrollbox::-webkit-scrollbar {
            width: 12px;
        }

        .scrollbox::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        .scrollbox::-webkit-scrollbar-thumb {
            background: #0f9d58;
            border-radius: 8px;
        }

        .scrollbox::-webkit-scrollbar-thumb:hover {
            background: #0b8043;
        }

        /* For Firefox */
        .scrollbox {
            scrollbar-width: thin;
            scrollbar-color: #0f9d58 #f1f1f1;
        }

        @media screen and (max-width: 600px) {
            .scrollbox {
                height: 150px;
            }
        }
        #heading{
        text-align: center;
        }
    </style>
</head>
<body>
<h4 id="heading">chatGPT Integration</h4>
    <div id="chat-container">
    
        <div id="chat-history">
            <!-- Chat messages will appear here -->
        </div>
        <div id="chat-input">
            <input type="text" id="user-message" placeholder="Type your message..." />
            <button id="send-button">Send</button>
        </div>
    </div>
<h4 id="heading">chatGPT History</h4>
     <div class="container">
        <div class="scrollbox" id="message_value">
           
           
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // References to UI elements
        const chatHistory = document.getElementById('chat-history');
        const userMessageInput = document.getElementById('user-message');
        const sendButton = document.getElementById('send-button');

        // Add event listener to the Send button
        sendButton.addEventListener('click', async function () {
            const userMessage = userMessageInput.value.trim();

            if (!userMessage) {
                alert('Please type a message!');
                return;
            }

            // Display the user's message in the chat history
            addMessageToChat('user', userMessage);

            // Prepare the payload for ChatGPT
            const messages = [
                // { role: 'system', content: 'You are a helpful assistant.' },
                { role: 'user', content: userMessage }
            ];

            try {
                // Send the user's message to the Laravel backend
                const response = await axios.post('/api/chat', { messages });

                // Extract ChatGPT's response and display it
                const botReply = response.data.choices[0].message.content;
                addMessageToChat('assistant', botReply);
                chatDispaly();

            } catch (error) {
                console.error('Error:', error);
                addMessageToChat('error', 'Failed to get a response from ChatGPT.');
                chatDispaly();
            }
            // Clear the input field
            userMessageInput.value = '';
        });

        // Function to add a message to the chat history
        function addMessageToChat(role, message) {
            const newMessage = document.createElement('div');
            newMessage.classList.add(role);
            newMessage.textContent = `${role.toUpperCase()}: ${message}`;
            chatHistory.appendChild(newMessage);

            // Scroll to the bottom of the chat history
            chatHistory.scrollTop = chatHistory.scrollHeight;
            // chatDispaly();
        }

        $(document).ready(function() {
            chatDispaly();
        });

        function chatDispaly(){
             $('#message_value').empty();
            $.ajax({
            dataType: "JSON",
            type: "POST",
            data: '',
            url: 'http://127.0.0.1:8000/api/chatHistory'
            }).done(function (data) {
                // Select the scrollbox element
                const scrollbox = $('#message_value');

                // Iterate through the data and append content
                data.forEach(item => {
                    const date = item.entry_date ? item.entry_date : "No Date";
                    const messages = item.chat.split('||'); // Split messages by separator

                    // Create a new block for each date
                    const dateBlock = $('<div>').addClass('date-block');

                    // Add the date heading
                    const dateHeading = $('<h3>').text(`Date: ${date}`);
                    dateBlock.append(dateHeading);

                    // Add the messages as a list
                    const messageList = $('<ul>');
                    messages.forEach(msg => {
                        const listItem = $('<li>').text(msg);
                        messageList.append(listItem);
                    });
                    dateBlock.append(messageList);

                    // Append the new block to the scrollbox
                    scrollbox.append(dateBlock);
                });
            }).fail(function (xhr, status, error) {
                console.error("An error occurred:", error);
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\Admin\Desktop\Bluemein\chatGPT\resources\views/home.blade.php ENDPATH**/ ?>