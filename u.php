<?php
// Connect to DB
$connection = mysqli_connect("localhost", "root", "", "hospital_db");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Add Patient
if (isset($_POST['add_patient'])) {
    $name = $_POST['p_name'];
    $age = $_POST['p_age'];
    $gender = $_POST['p_gender'];
    $contact = $_POST['p_contact'];
    mysqli_query($connection, "INSERT INTO patients (name, age, gender, contact) VALUES ('$name', '$age', '$gender', '$contact')");
}

// Delete Patient
if (isset($_POST['delete_patient_id'])) {
    $id = $_POST['delete_patient_id'];
    mysqli_query($connection, "DELETE FROM patients WHERE id = $id");
}

// Add Doctor
if (isset($_POST['add_doctor'])) {
    $name = $_POST['d_name'];
    $specialty = $_POST['d_specialty'];
    $contact = $_POST['d_contact'];
    mysqli_query($connection, "INSERT INTO doctors (name, specialty, contact) VALUES ('$name', '$specialty', '$contact')");
}

// Delete Doctor
if (isset($_POST['delete_doctor_id'])) {
    $id = $_POST['delete_doctor_id'];
    mysqli_query($connection, "DELETE FROM doctors WHERE id = $id");
}

// Book Appointment
if (isset($_POST['add_appointment'])) {
    $pid = $_POST['patient_id'];
    $did = $_POST['doctor_id'];
    $date = $_POST['appointment_date'];
    mysqli_query($connection, "INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES ('$pid', '$did', '$date')");
}

// Fetch data
$patients = mysqli_query($connection, "SELECT * FROM patients");
$doctors = mysqli_query($connection, "SELECT * FROM doctors");
$appointments = mysqli_query($connection, "
    SELECT a.id, p.name AS patient, d.name AS doctor, a.appointment_date 
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    JOIN doctors d ON a.doctor_id = d.id
");
?>

<!DOCTYPE html>
<html>
<head>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f6f7fb;
            margin: 0;
        }
        header {
            background: linear-gradient(45deg, #1abc9c, #3498db);
            color: #fff;
            padding: 25px 40px;
            text-align: center;
        }
        h1, h2 { margin: 0; }
        .container { padding: 40px; max-width: 1200px; margin: auto; }
        .card {
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            color: #fff;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .blue { background: #2980b9; }
        .green { background: #27ae60; }
        .orange { background: #e67e22; }
        .purple { background: #8e44ad; }
        .white { background: #fff; color: #333; }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        input, select, button {
            padding: 12px;
            font-size: 16px;
            border-radius: 10px;
            border: none;
            width: calc(50% - 20px);
        }
        input, select { border: 1px solid #ccc; }
        button {
            background: #fff;
            color: #333;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background: #ecf0f1; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        table th {
            background: #2c3e50;
            color: #fff;
        }
        table tr:nth-child(even) { background: #f2f2f2; }
        table tr:hover { background: #dfe6e9; }
        .icon { margin-right: 10px; }
        @media (max-width: 768px) {
            input, select, button { width: 100%; }
        }
    </style>
</head>
<body>

<header>
    <h1><i class="fa-solid fa-hospital icon"></i> Hospital Management System</h1>
</header>

<div class="container">

    <!-- Add Patient -->
    <div class="card blue">
        <h2><i class="fa-solid fa-user-plus icon"></i> Add Patient</h2>
        <form method="POST">
            <input type="text" name="p_name" placeholder="Full Name" required>
            <input type="number" name="p_age" placeholder="Age" required>
            <input type="text" name="p_gender" placeholder="Gender" required>
            <input type="text" name="p_contact" placeholder="Contact" required>
            <button type="submit" name="add_patient">Save Patient</button>
        </form>
    </div>

    <!-- Add Doctor -->
    <div class="card green">
        <h2><i class="fa-solid fa-user-doctor icon"></i> Add Doctor</h2>
        <form method="POST">
            <input type="text" name="d_name" placeholder="Doctor Name" required>
            <input type="text" name="d_specialty" placeholder="Specialty" required>
            <input type="text" name="d_contact" placeholder="Contact" required>
            <button type="submit" name="add_doctor">Save Doctor</button>
        </form>
    </div>

    <!-- Book Appointment -->
    <div class="card orange">
        <h2><i class="fa-solid fa-calendar-check icon"></i> Book Appointment</h2>
        <form method="POST">
            <select name="patient_id" required>
                <option value="">Select Patient</option>
                <?php while ($p = mysqli_fetch_assoc($patients)) { ?>
                    <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
                <?php } mysqli_data_seek($patients, 0); ?>
            </select>

            <select name="doctor_id" required>
                <option value="">Select Doctor</option>
                <?php while ($d = mysqli_fetch_assoc($doctors)) { ?>
                    <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                <?php } mysqli_data_seek($doctors, 0); ?>
            </select>

            <input type="date" name="appointment_date" required>
            <button type="submit" name="add_appointment">Book</button>
        </form>
    </div>

    <!-- Patients Table -->
    <div class="card white">
        <h2><i class="fa-solid fa-users icon"></i> Patients</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Contact</th><th>Action</th></tr>
            <?php while ($row = mysqli_fetch_assoc($patients)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= $row['gender'] ?></td>
                    <td><?= $row['contact'] ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this patient?');">
                            <input type="hidden" name="delete_patient_id" value="<?= $row['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } mysqli_data_seek($patients, 0); ?>
        </table>
    </div>

    <!-- Doctors Table -->
    <div class="card white">
        <h2><i class="fa-solid fa-user-md icon"></i> Doctors</h2>
        <table>
            <tr><th>ID</th><th>Name</th><th>Specialty</th><th>Contact</th><th>Action</th></tr>
            <?php while ($row = mysqli_fetch_assoc($doctors)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['specialty'] ?></td>
                    <td><?= $row['contact'] ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this doctor?');">
                            <input type="hidden" name="delete_doctor_id" value="<?= $row['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php } mysqli_data_seek($doctors, 0); ?>
        </table>
    </div>

    <!-- Appointments Table -->
    <div class="card purple">
        <h2><i class="fa-solid fa-calendar-day icon"></i> Appointments</h2>
        <table>
            <tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th></tr>
            <?php while ($row = mysqli_fetch_assoc($appointments)) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['patient'] ?></td>
                    <td><?= $row['doctor'] ?></td>
                    <td><?= $row['appointment_date'] ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
</body>
</html>
