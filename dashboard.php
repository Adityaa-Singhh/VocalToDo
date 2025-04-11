<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: LoginPage.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voice To-Do</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --background: #000430;
    --secondaryBackground: #171c48;
    --text: #fff;
    --purple: #828dff;
    --teal: #24feee;
    --lightpurple: #7982e4af;
}

body {
    background-color: var(--background);
    color: var(--text);
}

.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 120px auto 0 auto;
    max-width: 500px;
}

.stats-container {
    padding: 30px;
    border-radius: 20px;
    border: 2px solid var(--purple);
    display: flex;
    justify-content: space-between;
    gap: 50px;
    width: 100%;
}

.details {
    width: 100%;
}

#progressbar {
    width: 100%;
    height: 10px;
    background-color: var(--secondaryBackground);
    border-radius: 5px;
    margin-top: 20px;
}

#progress {
    width: 80%;
    height: 10px;
    background-color: var(--teal);
    border-radius: 10px;
    transition: all 0.3s ease;
}

#numbers {
    width: 100px;
    height: 100px;
    background: var(--purple);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 30px;
    font-weight: bold;
}

form {
    margin-top: 60px;
    width: 100%;
    display: flex;
    gap: 10px;
}

input {
    flex: 1;
    padding: 16px;
    background-color: var(--secondaryBackground);
    border: 1px solid purple;
    border-radius: 10px;
    outline: none;
    color: white;
}

button {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid purple;
    background: transparent;
    color: var(--text);
    font-size: 30px;
    font-weight: bold;
    outline: none;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: var(--purple);
    color: var(--background);
}

#task-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 30px;
    width: 100%;
    list-style: none;
}

.taskItem {
    width: 100%;
    display: flex;
    background: var(--secondaryBackground);
    padding: 10px;
    border-radius: 10px;
    justify-content: space-between;
    align-items: center;
    transition: 0.3s;
}

.taskItem:hover {
    background: var(--lightpurple);
}

.task {
    display: flex;
    align-items: center;
    gap: 10px;
}

#taskInput {
    flex: 1;
    padding: 16px;
    padding-right: 40px; /* Make space for voice icon */
    background-color: var(--secondaryBackground);
    border: 1px solid purple;
    border-radius: 10px;
    outline: none;
    color: white;
    margin-right: 10px; /* Add margin if needed */
    font-size: 18px;
}

.task.completed{
    text-decoration: line-through;
    color: teal;
}

.taskItem img {
    width: 24px;
    height: 24px;
    margin: 0 10px;
    cursor: pointer;
    transition: 0.3s;
}

.voice-command {
    position: absolute;
    right: 10px;
    width: 24px;
    height: 24px;
    cursor: pointer;
}
.input-container img{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid purple;
    background: transparent;
    color: var(--text);
    font-size: 30px;
    font-weight: bold;
    outline: none;
    cursor: pointer;
    transition: 0.3s;
    right: 60px;
}
.input-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.header{
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
    padding: 10px;
}
.header img{
    cursor: pointer;
    width: 70px;
    height: 70px;
}
form {
    margin-top: 60px;
    width: 100%;
    display: flex;
    gap: 10px;
    position: relative; /* Add this for proper positioning */
}

.reminder-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: var(--secondaryBackground);
    border: 2px solid var(--purple);
    border-radius: 10px;
    padding: 15px;
    max-width: 300px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.5s ease-out;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
      
    <div class="header">
    <a href="backend/logout.php">
    <img src="Images/power.png" alt="">
    </a>
    </div>

    <div class="container">
        <div class="stats-container">
            <div class="details">
                <h1>Todo App</h1>
                <p>Keep it up!</p>
                <div id="progressbar">
                    <div id="progress"></div>
                </div>
            </div>
            <div class="stats-numbers">
                <p id="numbers">0 / 0</p>
            </div>
        </div>

        <form id="taskForm">
    <div class="input-container">
        <input type="text" id="taskInput" placeholder="Write your task" required>
        <img src="Images/voice.png" alt="Voice Command" class="voice-icon">
    </div>
    <button type="submit" id="newTask">+</button>
</form>
        <ul id="task-list"></ul>  
    </div>

    <script>
        // Global task array
        let tasks = [];

// Load tasks from database when page loads
const loadTasks = async () => {
    try {
        const response = await fetch('backend/tasks.php');
        const data = await response.json();
        
        if (data.success) {
            // Convert string "1"/"0" to boolean
            tasks = data.tasks.map(task => ({
                id: task.id,
                text: task.task_text,
                completed: Number(task.completed) === 1 // Convert to boolean
            }));
            updateTasksList();
            updateStats();
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
    }
};

// Add task to database
const addTask = async () => {
    const taskInput = document.getElementById("taskInput");
    const text = taskInput.value.trim();

    if (text) {
        try {
            const response = await fetch('backend/tasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    task: text
                })
            });
            
            const result = await response.json();
            if (result.success) {
                taskInput.value = "";
                await loadTasks(); // Refresh the task list
            }
        } catch (error) {
            console.error('Error adding task:', error);
        }
    }
};

// Toggle task completion status
const toggleTaskComplete = async (id) => {
    const taskIndex = tasks.findIndex(t => t.id == id);
    if (taskIndex !== -1) {
        try {
            const newStatus = !tasks[taskIndex].completed;
            const response = await fetch('backend/tasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'toggle',
                    id: id,
                    completed: newStatus
                })
            });
            
            const data = await response.json();
            if (data.success) {
                // Update local state only after successful DB update
                tasks[taskIndex].completed = newStatus;
                updateTasksList();
                updateStats();
            }
        } catch (error) {
            console.error('Error toggling task:', error);
        }
    }
};

// Delete task from database
const deleteTask = async (id) => {
    try {
        await fetch('backend/tasks.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: id
            })
        });
        await loadTasks();
    } catch (error) {
        console.error('Error deleting task:', error);
    }
};

// Edit task in database
const editTask = async (id) => {
    const taskInput = document.getElementById("taskInput");
    const task = tasks.find(t => t.id == id);
    
    if (task) {
        const newText = prompt("Edit task:", task.text);
        if (newText !== null) {
            try {
                await fetch('backend/tasks.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'edit',
                        id: id,
                        newText: newText
                    })
                });
                await loadTasks();
            } catch (error) {
                console.error('Error editing task:', error);
            }
        }
    }
};


// Update progress stats
const updateStats = () => {
    const completeTasks = tasks.filter(task => task.completed).length;
    const totalTasks = tasks.length;
    const progress = totalTasks > 0 ? (completeTasks / totalTasks) * 100 : 0;

    const progressBar = document.getElementById('progress');
    progressBar.style.width = `${progress}%`;

    document.getElementById('numbers').innerText = `${completeTasks} / ${totalTasks}`;

    // Only show confetti if there are tasks and all are completed
    if(totalTasks > 0 && completeTasks === totalTasks){
        blastConfetti();
    }
};

// Update task list UI
const updateTasksList = () => {
    const tasksList = document.getElementById("task-list");
    tasksList.innerHTML = "";

    tasks.forEach((task) => {
        const listItem = document.createElement("li");
        listItem.className = "taskItem";
        
        const isCompleted = Boolean(Number(task.completed));
        
        listItem.innerHTML = `
            <div class="task ${isCompleted ? 'completed' : ''}">
                <input type="checkbox" class="task-input" ${isCompleted ? 'checked' : ''}/>
                <p>${task.text}</p>
            </div>
            <div class="icons">
                <img src="Images/edit.png" alt="Edit" />
                <img src="Images/bin.png" alt="Delete" />
            </div>`;
        
        // Add event listeners after creating the element
        const checkbox = listItem.querySelector('.task-input');
        const editBtn = listItem.querySelector('img[alt="Edit"]');
        const deleteBtn = listItem.querySelector('img[alt="Delete"]');
        
        checkbox.addEventListener('change', () => toggleTaskComplete(task.id));
        editBtn.addEventListener('click', () => editTask(task.id));
        deleteBtn.addEventListener('click', () => deleteTask(task.id));
        
        tasksList.appendChild(listItem);
    });
};

// Confetti animation
const blastConfetti = () => {
    const count = 200;
    const defaults = {
        origin: { y: 0.7 },
    };

    function fire(particleRatio, opts) {
        confetti(
            Object.assign({}, defaults, opts, {
                particleCount: Math.floor(count * particleRatio),
            })
        );
    }

    fire(0.25, { spread: 26, startVelocity: 55 });
    fire(0.2, { spread: 60 });
    fire(0.35, { spread: 100, decay: 0.91, scalar: 0.8 });
    fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.2 });
    fire(0.1, { spread: 120, startVelocity: 45 });
};

// Initialize when page loads
document.addEventListener("DOMContentLoaded", () => {
    loadTasks();
    
  // Voice recognition setup
const voiceIcon = document.querySelector(".voice-icon");
const taskInput = document.getElementById("taskInput");

if ("webkitSpeechRecognition" in window || "SpeechRecognition" in window) {
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.continuous = false; // Changed from true to false
    recognition.interimResults = false;
    recognition.lang = "en-US";
    recognition.maxAlternatives = 1;
    
    // Timeout variables
    let inactivityTimer;
    const INACTIVITY_TIMEOUT = 10000; // 10 seconds
    
    voiceIcon.addEventListener("click", () => {
        recognition.start();
        // Start inactivity timer
        resetInactivityTimer();
        // Change icon to indicate listening
        voiceIcon.src = "Images/voice-active.png"; // You'll need this image
    });

    recognition.onresult = async (event) => {
        const voiceText = event.results[0][0].transcript.toLowerCase().trim();
        console.log("Voice Command:", voiceText);
        
        if (voiceText.startsWith("add")) {
            const newTask = voiceText.replace("add", "").trim();
            if (newTask) {
                taskInput.value = newTask;
                await addTask();
            }
        } else if (voiceText.startsWith("delete")) {
            const taskToDelete = voiceText.replace("delete", "").trim();
            await deleteTaskByName(taskToDelete);
        } else if (voiceText.startsWith("complete")) {
            const taskToComplete = voiceText.replace("complete", "").trim();
            await completeTaskByName(taskToComplete);
        } else {
            taskInput.value = voiceText;
        }
        
        // Reset timer after getting a result
        resetInactivityTimer();
    };

    recognition.onerror = (event) => {
        console.error("Speech recognition error:", event.error);
        stopRecognition();
    };
    
    recognition.onend = () => {
        // Reset icon when recognition ends
        voiceIcon.src = "Images/voice.png";
        clearTimeout(inactivityTimer);
    };
    
    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            recognition.stop();
            console.log("Voice recognition stopped due to inactivity");
        }, INACTIVITY_TIMEOUT);
    }
    
    function stopRecognition() {
        recognition.stop();
        clearTimeout(inactivityTimer);
        voiceIcon.src = "Images/voice.png";
    }
} else {
    voiceIcon.style.display = "none";
}

    // Form submission
    document.getElementById("taskForm").addEventListener("submit", function(e) {
        e.preventDefault();
        addTask();
    });
});

// Voice command helper functions
const addTaskFromVoice = async (taskName) => {
    if (taskName) {
        taskInput.value = taskName;
        await addTask();
    }
};

const deleteTaskByName = async (taskName) => {
    const task = tasks.find(t => t.text.toLowerCase() === taskName.toLowerCase());
    if (task) {
        await deleteTask(task.id);
    } else {
        alert(`Task "${taskName}" not found.`);
    }
};

const completeTaskByName = async (taskName) => {
    const task = tasks.find(t => t.text.toLowerCase() === taskName.toLowerCase());
    if (task) {
        await toggleTaskComplete(task.id);
    } else {
        alert(`Task "${taskName}" not found.`);
    }
};

function showReminderNotification(tasks) {
    const notification = document.createElement('div');
    notification.className = 'reminder-notification';
    notification.innerHTML = `
        <div class="reminder-content">
            <h3>⏰ Task Reminder</h3>
            <p>You have ${tasks.length} uncompleted task(s):</p>
            <ul>
                ${tasks.map(task => `<li>${task.task_text}</li>`).join('')}
            </ul>
            <button class="reminder-close">OK</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    notification.querySelector('.reminder-close').addEventListener('click', () => {
        notification.remove();
    });
    
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.remove();
        }
    }, 30000);
}

window.toggleTaskComplete = toggleTaskComplete;
window.editTask = editTask;
window.deleteTask = deleteTask;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>
</body>
</html>