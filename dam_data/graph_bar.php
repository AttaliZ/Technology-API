<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Bar Chart Search - ข้อมูลอ่างเก็บน้ำ (Cyberpunk)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- โหลด Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1"></script>
  <!-- โหลดฟอนต์ Orbitron สำหรับสไตล์ Cyberpunk -->
  <link href="https://fonts.googleapis.com/css?family=Orbitron:400,700&display=swap" rel="stylesheet">
  <style>
    /* Global Cyberpunk Styles */
    * {
      box-sizing: border-box;
    }
    body {
      font-family: 'Orbitron', sans-serif;
      margin: 0;
      padding: 20px;
      text-align: center;
      background-color: #0d0d0d;
      color: #e0e0e0;
    }
    /* Menu Bar */
    .menu {
      background: #1a1a1a;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 16px;
      gap: 20px;
      border-top: 4px solid #333;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .menu a {
      text-decoration: none;
      color: #0ff;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      background: #000;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      border: 1px solid #0ff;
    }
    .menu a::after {
      content: "";
      position: absolute;
      left: 50%;
      bottom: 0;
      width: 60%;
      border-radius: 40%;
      height: 0;
      background: #ff00ff;
      transform: translateX(-50%);
      transition: height 0.3s ease-in-out;
    }
    .menu a:hover {
      background: linear-gradient(45deg, #ff00ff, #00ffff);
      color: #fff;
      transform: scale(1.05);
    }
    .menu a:hover::after {
      height: 5px;
    }
    /* Container */
    .container {
      max-width: 1200px;
      margin: 0 auto 30px;
      background: rgba(0, 0, 0, 0.85);
      border: 1px solid #1a1a1a;
      border-radius: 8px;
      padding: 20px;
      box-shadow: 0 0 20px rgba(0, 255, 255, 0.2);
    }
    h1 {
      text-align: center;
      color: #ff00ff;
      margin-bottom: 20px;
      text-shadow: 0 0 10px #00ffff;
    }
    /* Search Box */
    .search-container {
      margin: 20px 0;
    }
    input[type="text"] {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #0ff;
      border-radius: 5px;
      background: #000;
      color: #0ff;
      outline: none;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      background: linear-gradient(45deg, #ff00ff, #00ffff);
      color: #fff;
      cursor: pointer;
      margin-left: 10px;
      transition: transform 0.3s ease;
    }
    button:hover {
      transform: scale(1.05);
    }
    /* Canvas */
    canvas {
      display: block;
      margin: auto;
      background-color: #111;
      border: 1px solid #333;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
    }
  </style>
</head>
<body>
  <div class="menu">
    <a href="index.php">Home</a>
    <a href="graph_line.php">Line Chart</a>
    <a href="graph_bar.php">Bar Chart</a>
    <a href="graph_pie.php">Pie Chart</a>
    <a href="data_table.php">Table</a>
  </div>
  <div class="container">
    <h1>ค้นหาข้อมูลอ่างเก็บน้ำ</h1>
    <div class="search-container">
      <input type="text" id="searchInput" placeholder="ค้นหาชื่ออ่างเก็บน้ำ...">
      <button id="searchBtn">ค้นหา</button>
    </div>
    <canvas id="barChart" width="800" height="400"></canvas>
  </div>
  <script>
    // รับข้อมูลจาก PHP (ข้อมูลอ่างเก็บน้ำ)
    const reservoirs = <?php echo json_encode($reservoirs); ?>;
    
    // ฟังก์ชันกรองข้อมูลตามคำค้น (ค้นหาแบบ case-insensitive)
    function filterReservoirs(query) {
      query = query.toLowerCase();
      return reservoirs.filter(r => r.name && r.name.toLowerCase().includes(query));
    }
    
    // ฟังก์ชันสร้างข้อมูลกราฟจากอาร์เรย์ของอ่างเก็บน้ำ
    function createChartData(dataArray) {
      const labels = dataArray.map(r => r.name);
      const volumeData = dataArray.map(r => r.volume || 0);
      return { labels, volumeData };
    }
    
    // สร้างกราฟแท่งเริ่มต้น แสดงข้อมูลทั้งหมด
    const initialData = createChartData(reservoirs);
    const ctx = document.getElementById('barChart').getContext('2d');
    let barChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: initialData.labels,
        datasets: [{
          label: 'ปริมาณน้ำปัจจุบัน (ล้าน ลบ.ม.)',
          data: initialData.volumeData,
          backgroundColor: 'rgba(153, 102, 255, 0.5)',
          borderColor: 'rgba(153, 102, 255, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          x: { type: 'category' },
          y: { beginAtZero: true }
        }
      }
    });
    
    // เมื่อกดปุ่มค้นหา ให้อัปเดตกราฟตามคำค้น
    document.getElementById('searchBtn').addEventListener('click', function() {
      const query = document.getElementById('searchInput').value.trim();
      const filtered = filterReservoirs(query);
      const newData = createChartData(filtered);
      barChart.data.labels = newData.labels;
      barChart.data.datasets[0].data = newData.volumeData;
      barChart.update();
    });
    
    // รองรับการกด Enter ในช่องค้นหา
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
      }
    });
  </script>
</body>
</html>
