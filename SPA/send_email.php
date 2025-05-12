<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Record</title>
    <style>
       
        #myModal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4); 
}

.modal-content {
    background-color: #f8f0e3; 
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #d0c2b4; 
    border-radius: 15px; 
    width: 300px; 
    text-align: left; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
}

.modal-content p {
    margin-bottom: 15px; 
    font-size: 16px; 
    color: #333; 
}

.modal-header {
    font-size: 18px; 
    font-weight: bold;
    margin-bottom: 10px; 
    color: #555;
}

.modal-buttons {
    text-align: center;
}

.modal-buttons button {
    background-color: #a67b5b; 
    border: 2px solid #805533; 
    color: white; 
    padding: 10px 25px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    margin: 10px;
    cursor: pointer;
    border-radius: 25px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.modal-buttons button:hover {
    background-color: #8c6347;
    border-color: #664124; 
    </style>
</head>
<body>
    <form method="post" action="contact.php">
        <input type="text" name="data1" placeholder="localhost says">
        <input type="text" name="data2" placeholder="Message sent successfully">
        <button type="submit">Email sent</button>
    </form>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <button id="closeModal">OK</button>
        </div>
    </div>

    <script>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "document.getElementById('myModal').style.display = 'block';";
            echo "document.getElementById('modalMessage').textContent = '" . $_SESSION['success_message'] . "';";
            unset($_SESSION['success_message']);
        }
        ?>

        document.getElementById('closeModal').addEventListener('click', function() {
            document.getElementById('myModal').style.display = 'none';
        });
    </script>
</body>
</html>