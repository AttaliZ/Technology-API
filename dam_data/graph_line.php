<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Line Chart - ข้อมูลอ่างเก็บน้ำ (Cyberpunk)</title>
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
      background-color: #0d0d0d;
      color: #e0e0e0;
      text-align: center;
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
    <h1>ข้อมูลอ่างเก็บน้ำ - Line Chart (Cyberpunk)</h1>
    <canvas id="lineChart" width="800" height="400"></canvas>
  </div>
  <script>
    // ส่งข้อมูล PHP เป็น JSON ไปให้ JavaScript
    const reservoirs = <?php echo json_encode($reservoirs); ?>;
    // ใช้ชื่ออ่างเก็บน้ำเป็น label และเปอร์เซ็นต์น้ำที่มีเป็นข้อมูล
    const labels = reservoirs.map(r => r.name);
    const percentData = reservoirs.map(r => r.percent_storage || 0);

    const ctx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'เปอร์เซ็นต์น้ำที่มี',
          data: percentData,
          backgroundColor: 'rgba(0, 255, 255, 0.2)',  // สีเนีออนไซแอน
          borderColor: 'rgba(0, 255, 255, 1)',
          borderWidth: 2,
          fill: true,
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            max: 100,
            ticks: {
              color: '#0ff'
            },
            grid: {
              color: 'rgba(0, 255, 255, 0.2)'
            }
          },
          x: {
            ticks: {
              color: '#0ff'
            },
            grid: {
              color: 'rgba(0, 255, 255, 0.2)'
            }
          }
        },
        plugins: {
          legend: {
            labels: {
              color: '#0ff'
            }
          }
        }
      }
    });
  </script>
</body>
</html>
