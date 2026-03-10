<?php
session_start();
if (!isset($_SESSION['name'])) {
    header("Location: login.html");
    exit();
}
?>
<!-- Then use $_SESSION['name'], $_SESSION['email'], etc. to show profile info -->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-image: url('bgimage.jpg');
      background-size: cover;
      background-position: center;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .profile-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      text-align: center;
      width: 90%;
      max-width: 450px;
    }

    .profile-photo {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid #0f9b8e;
      margin-bottom: 1rem;
      object-fit: cover;
    }

    p {
      margin-bottom: 0.5rem;
      color: #0f9b8e;
    }

    .info {
      text-align: left;
      margin-top: 1.5rem;
    }

    .info p {
      margin: 0.5rem 0;
      font-size: 1rem;
      color: #333;
    }

    .edit-btn {
      display: inline-block;
      margin-top: 1.5rem;
      padding: 0.7rem 1.5rem;
      background-color: #0f9b8e;
      color: white;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .edit-btn:hover {
      background-color: #087f73;
    }

  </style>
</head>
<body>
  <div class="profile-container">
    <h1 style="color: rgb(0, 0, 0);"><strong>Finance Tracker User</strong></h1>

    <div class="info">
      <p><strong>Name:</strong> <?= $_SESSION['name']; ?></p>
      <p><strong>Email:</strong> <?= $_SESSION['email']; ?></p>
      <p><strong>Phone:</strong> <?= $_SESSION['phone']; ?></p>
      <p><strong>Gender:</strong> <?= $_SESSION['gender']; ?></p>
    </div>

    <a href="change profile.html" class="edit-btn">Edit Profile</a>
    <a href="home.html" class="edit-btn">Back</a>
  </div>


</script>
</body>
</html>
