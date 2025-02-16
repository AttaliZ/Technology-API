<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลอ่างเก็บน้ำ - Cyberpunk Table</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- ใช้ฟอนต์ Orbitron สำหรับสไตล์ Cyberpunk -->
  <link href="https://fonts.googleapis.com/css?family=Orbitron:400,700&display=swap" rel="stylesheet">
  <style>
    /* Global Styles */
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Orbitron', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #0d0d0d;
      color: #f0f0f0;
      overflow-x: hidden;
      animation: fadeIn 1s ease-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* Container */
    .container {
      max-width: 1200px;
      margin: 30px auto;
      background: rgba(0, 0, 0, 0.85);
      border: 1px solid #1a1a1a;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 0 20px rgba(255, 0, 255, 0.2);
    }
    
    /* Menu Bar */
    .menu {
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 16px;
      gap: 20px;
      background: #1a1a1a;
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
      border-bottom: 1px solid #333;
    }
    .menu a {
      text-decoration: none;
      color: #0ff;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      background: #000;
      position: relative;
      transition: all 0.3s ease;
      border: 1px solid #0ff;
    }
    .menu a:hover {
      color: #fff;
      background: linear-gradient(45deg, #ff00ff, #00ffff);
      box-shadow: 0 0 10px #ff00ff, 0 0 10px #00ffff;
      transform: scale(1.05);
    }
    
    /* Heading */
    h1 {
      text-align: center;
      color: #ff00ff;
      margin: 20px 0;
      text-shadow: 0 0 10px #00ffff;
      animation: neon 2s ease-in-out infinite alternate;
    }
    @keyframes neon {
      from { text-shadow: 0 0 10px #00ffff, 0 0 20px #00ffff; }
      to { text-shadow: 0 0 20px #ff00ff, 0 0 30px #ff00ff; }
    }
    
    /* Table Styles */
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border: 1px solid #333;
      text-align: center;
      transition: background 0.3s, transform 0.3s;
    }
    th {
      background: #1a1a1a;
      color: #0ff;
    }
    tr:nth-child(even) {
      background: rgba(0, 0, 0, 0.5);
    }
    tr:hover {
      background: rgba(0, 255, 255, 0.1);
      transform: scale(1.02);
    }
    /* Glitch effect for table rows */
    tr {
      animation: glitch 1s infinite;
    }
    @keyframes glitch {
      0% {
        text-shadow: 2px 2px 5px #ff00ff;
      }
      50% {
        text-shadow: -2px -2px 5px #00ffff;
      }
      100% {
        text-shadow: 2px 2px 5px #ff00ff;
      }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .menu {
        flex-direction: column;
      }
      th, td {
        padding: 8px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="menu">
      <a href="index.php">Home</a>
      <a href="graph_line.php">Line Chart</a>
      <a href="graph_bar.php">Bar Chart</a>
      <a href="graph_pie.php">Pie Chart</a>
      <a href="data_table.php">Table</a>
    </div>
    <h1>ข้อมูลอ่างเก็บน้ำ</h1>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>ชื่ออ่างเก็บน้ำ</th>
          <th>ความจุทั้งหมด (ล้าน ลบ.ม.)</th>
          <th>ปริมาณน้ำต่ำสุด (ล้าน ลบ.ม.)</th>
          <th>ปริมาณน้ำปัจจุบัน (ล้าน ลบ.ม.)</th>
          <th>เปอร์เซ็นต์น้ำที่มี</th>
          <th>ปริมาณน้ำไหลเข้า (ล้าน ลบ.ม.)</th>
          <th>ปริมาณน้ำไหลออก (ล้าน ลบ.ม.)</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reservoirs)): ?>
          <?php foreach ($reservoirs as $reservoir): ?>
            <tr>
              <td><?= htmlspecialchars($reservoir['id'] ?? '-') ?></td>
              <td><?= htmlspecialchars($reservoir['name'] ?? '-') ?></td>
              <td><?= htmlspecialchars($reservoir['storage'] ?? '-') ?></td>
              <td><?= htmlspecialchars($reservoir['dead_storage'] ?? '-') ?></td>
              <td><?= htmlspecialchars($reservoir['volume'] ?? '-') ?></td>
              <td><?= isset($reservoir['percent_storage']) ? number_format($reservoir['percent_storage'], 2) . '%' : '-' ?></td>
              <td><?= htmlspecialchars($reservoir['inflow'] ?? '-') ?></td>
              <td><?= htmlspecialchars($reservoir['outflow'] ?? '-') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8">ไม่พบข้อมูล</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
