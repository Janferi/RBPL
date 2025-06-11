<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUZ Cafe Manager - Laporan Transaksi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f4e8c8 0%, #e8d5a8 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            border: 2px solid #333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .logo-icon::before {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid #333;
            border-radius: 50%;
            position: absolute;
        }
        
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            letter-spacing: 1px;
        }
        
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(220,53,69,0.3);
        }
        
        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220,53,69,0.4);
        }
        
        .report-container {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }
        
        .date-picker-btn {
            background: #333;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            min-width: 250px;
            justify-content: center;
        }
        
        .date-picker-btn:hover {
            background: #555;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .calendar-icon {
            font-size: 18px;
        }
        
        .download-btn {
            background: #333;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            min-width: 250px;
        }
        
        .download-btn:hover {
            background: #555;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 15px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .modal-header {
            background: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        
        .calendar-container {
            padding: 20px;
            background: linear-gradient(135deg, #f4e8c8 0%, #e8d5a8 100%);
        }
        
        .calendar {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .calendar-header {
            background: #333;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            position: relative;
        }
        
        .calendar-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .calendar-nav:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .prev {
            left: 15px;
        }
        
        .next {
            right: 15px;
        }
        
        .calendar th {
            background: #f8f9fa;
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        
        .calendar td {
            padding: 10px 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        
        .calendar td:hover {
            background: #e3f2fd;
        }
        
        .calendar td.selected {
            background: #2196f3;
            color: white;
            border-color: #2196f3;
        }
        
        .calendar td.other-month {
            color: #ccc;
        }
        
        .modal-actions {
            padding: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            background: #f8f9fa;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .cancel-modal-btn {
            background: #6c757d;
            color: white;
        }
        
        .cancel-modal-btn:hover {
            background: #5a6268;
        }
        
        .select-period-btn {
            background: #333;
            color: white;
        }
        
        .select-period-btn:hover {
            background: #555;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-left: 4px solid #333;
            z-index: 1001;
            display: none;
        }
        
        .notification.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .ok-btn {
            background: #333;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    ?>
    
    <div class="header">
        <div class="logo">
            <div class="logo-icon"></div>
            <div class="logo-text">CRUZ</div>
        </div>
        <button class="logout-btn" onclick="logout()">Logout</button>
    </div>
    
    <div class="report-container">
        <button class="date-picker-btn" onclick="openCalendar()">
            <span class="calendar-icon">📅</span>
            <span id="selectedPeriod">Pilih periode</span>
        </button>
        
        <button class="download-btn" onclick="downloadReport()" id="downloadBtn" style="display: none;">
            Unduh
        </button>
    </div>
    
    <!-- Calendar Modal -->
    <div id="calendarModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span id="currentMonth">June 2024</span>
                <button class="calendar-nav prev" onclick="previousMonth()">‹</button>
                <button class="calendar-nav next" onclick="nextMonth()">›</button>
            </div>
            <div class="calendar-container">
                <table class="calendar" id="calendar">
                    <thead>
                        <tr>
                            <th>Sun</th>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                        </tr>
                    </thead>
                    <tbody id="calendarBody">
                        <!-- Calendar days will be generated here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-actions">
                <button class="modal-btn cancel-modal-btn" onclick="closeCalendar()">Batal</button>
                <button class="modal-btn select-period-btn" onclick="selectPeriod()">Pilih</button>
            </div>
        </div>
    </div>
    <!-- Notification -->
    <div id="notification" class="notification">
        <span id="notificationMessage"></span>
        <button class="ok-btn" onclick="closeNotification()">OK</button>
    </div>
    <script>
        let selectedDate = null;
        let currentMonth = new Date();
        
        function openCalendar() {
            document.getElementById('calendarModal').style.display = 'block';
            generateCalendar();
        }
        
        function closeCalendar() {
            document.getElementById('calendarModal').style.display = 'none';
        }
        
        function generateCalendar() {
            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = '';
            
            const monthDays = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0).getDate();
            const firstDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1).getDay();
            
            let row = document.createElement('tr');
            
            for (let i = 0; i < firstDay; i++) {
                const cell = document.createElement('td');
                cell.classList.add('other-month');
                row.appendChild(cell);
            }
            
            for (let day = 1; day <= monthDays; day++) {
                const cell = document.createElement('td');
                cell.textContent = day;
                cell.onclick = () => selectDate(day);
                
                if (selectedDate && selectedDate.getDate() === day && selectedDate.getMonth() === currentMonth.getMonth()) {
                    cell.classList.add('selected');
                }
                
                row.appendChild(cell);
                
                if ((day + firstDay) % 7 === 0) {
                    calendarBody.appendChild(row);
                    row = document.createElement('tr');
                }
            }
            
            if (row.children.length > 0) {
                calendarBody.appendChild(row);
            }
            
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            document.getElementById('currentMonth').textContent = `${monthNames[currentMonth.getMonth()]} ${currentMonth.getFullYear()}`;
        }
        function selectDate(day) {
            const selectedCell = document.querySelector('.calendar td.selected');
            if (selectedCell) {
                selectedCell.classList.remove('selected');
            }
            
            const cell = document.querySelector(`.calendar td:nth-child(${day})`);
            cell.classList.add('selected');
            
            selectedDate = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), day);
        }
        function previousMonth() {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            generateCalendar();
        }
        function nextMonth() {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            generateCalendar();
        }
        function selectPeriod() {
            if (selectedDate) {
                const selectedPeriod = `${selectedDate.getDate()}-${selectedDate.getMonth() + 1}-${selectedDate.getFullYear()}`;
                document.getElementById('selectedPeriod').textContent = selectedPeriod;
                document.getElementById('downloadBtn').style.display = 'block';
                closeCalendar();
            } else {
                showNotification('Silakan pilih tanggal terlebih dahulu.');
            }
        }
        function downloadReport() {
            const selectedPeriod = document.getElementById('selectedPeriod').textContent;
            if (selectedPeriod !== 'Pilih periode') {
                // Simulate downloading the report
                showNotification(`Laporan untuk periode ${selectedPeriod} berhasil diunduh.`);
            } else {
                showNotification('Silakan pilih periode terlebih dahulu.');
            }
        }
        function showNotification(message) {
            const notification = document.getElementById('notification');
            const notificationMessage = document.getElementById('notificationMessage');
            notificationMessage.textContent = message;
            notification.classList.add('show');
        }
        function closeNotification() {
            const notification = document.getElementById('notification');
            notification.classList.remove('show');
        }
        function logout() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
<?php
// Include database connection
include 'koneksi.php';
// Check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Check if the user is a manager
if ($_SESSION['role'] !== 'manager') {
    header("Location: halamanoption.php");
    exit();
}
// Handle form submission for date selection
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Fetch transactions from the database
    $query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$start_date' AND '$end_date'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $transactions = [];
    }
}
// Handle download report
if (isset($_POST['download_report'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Fetch transactions from the database
    $query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$start_date' AND '$end_date'";
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Generate CSV file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="laporan_transaksi.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID Transaksi', 'Tanggal', 'Total Harga']);
        
        foreach ($transactions as $transaction) {
            fputcsv($output, $transaction);
        }
        
        fclose($output);
        exit();
    }
}
?>
<?php
// Close the database connection
mysqli_close($koneksi);
?>

