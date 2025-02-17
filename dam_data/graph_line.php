<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลอ่างเก็บน้ำ</title>
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
    /* Pagination */
    .pagination {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
    }
    .pagination button {
      margin: 0;
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
    <a href="map_chart.php">Map(Example)</a>
  </div>
  <div class="container">
    <h1>ข้อมูลอ่างเก็บน้ำ</h1>
    <div class="search-container">
      <input type="text" id="searchInput" placeholder="ค้นหาชื่ออ่างเก็บน้ำ...">
      <button id="searchBtn">ค้นหา</button>
    </div>
    <canvas id="lineChart" width="800" height="400"></canvas>
    <div class="pagination">
      <button id="prevBtn">Previous</button>
      <span id="pageInfo">Page 1</span>
      <button id="nextBtn">Next</button>
    </div>
  </div>
  <script>
    // ส่งข้อมูล PHP เป็น JSON ไปให้ JavaScript
    const reservoirs = <?php echo json_encode($reservoirs); ?>;
    
    // ตัวแปรสำหรับค้นหาและแบ่งหน้า
    let filteredData = reservoirs;  // ข้อมูลที่กรองแล้ว (เริ่มต้นคือข้อมูลทั้งหมด)
    let currentPage = 1;
    const itemsPerPage = 10; // จำนวนรายการต่อหน้า

    // ฟังก์ชันกรองข้อมูลตามคำค้น (ค้นหาแบบ case-insensitive)
    function filterReservoirs(query) {
      query = query.toLowerCase();
      return reservoirs.filter(r => r.name && r.name.toLowerCase().includes(query));
    }
    
    // ฟังก์ชันสำหรับแบ่งข้อมูลตามหน้า
    function getPageData(data, page, perPage) {
      const start = (page - 1) * perPage;
      return data.slice(start, start + perPage);
    }
    
    // ฟังก์ชันสำหรับสร้างข้อมูลกราฟจากข้อมูลในแต่ละหน้าของข้อมูลที่กรองแล้ว
    function createChartData(dataArray) {
      const labels = dataArray.map(r => r.name);
      const percentData = dataArray.map(r => r.percent_storage || 0);
      return { labels, percentData };
    }
    
    // สร้างกราฟ Line Chart เริ่มต้นด้วยข้อมูลในหน้าปัจจุบัน
    const ctx = document.getElementById('lineChart').getContext('2d');
    const initialPageData = getPageData(filteredData, currentPage, itemsPerPage);
    const initialChartData = createChartData(initialPageData);
    
    const lineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: initialChartData.labels,
        datasets: [{
          label: 'เปอร์เซ็นต์น้ำที่มี',
          data: initialChartData.percentData,
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
    
    // ฟังก์ชันอัปเดตกราฟตามข้อมูลที่กรองและหน้าปัจจุบัน
    function updateChart(dataArray) {
      const totalPages = Math.ceil(dataArray.length / itemsPerPage) || 1;
      if (currentPage > totalPages) {
        currentPage = totalPages;
      }
      if (currentPage < 1) {
        currentPage = 1;
      }
      const pageData = getPageData(dataArray, currentPage, itemsPerPage);
      const chartData = createChartData(pageData);
      lineChart.data.labels = chartData.labels;
      lineChart.data.datasets[0].data = chartData.percentData;
      lineChart.update();
      
      // อัปเดตหมายเลขหน้าและสถานะของปุ่ม Previous/Next
      document.getElementById('pageInfo').innerText = `Page ${currentPage} of ${totalPages}`;
      document.getElementById('prevBtn').disabled = (currentPage <= 1);
      document.getElementById('nextBtn').disabled = (currentPage >= totalPages);
    }
    
    // เมื่อกดปุ่มค้นหา ให้กรองข้อมูลแล้วรีเซ็ตหน้ากลับไปที่หน้าแรก
    document.getElementById('searchBtn').addEventListener('click', function() {
      const query = document.getElementById('searchInput').value.trim();
      filteredData = query ? filterReservoirs(query) : reservoirs;
      currentPage = 1;
      updateChart(filteredData);
    });
    
    // รองรับการกด Enter ในช่องค้นหา
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        document.getElementById('searchBtn').click();
      }
    });
    
    // ปุ่มเปลี่ยนหน้าก่อนหน้าและถัดไป
    document.getElementById('prevBtn').addEventListener('click', function() {
      if (currentPage > 1) {
        currentPage--;
        updateChart(filteredData);
      }
    });
    document.getElementById('nextBtn').addEventListener('click', function() {
      const totalPages = Math.ceil(filteredData.length / itemsPerPage);
      if (currentPage < totalPages) {
        currentPage++;
        updateChart(filteredData);
      }
    });
    
    // เรียกอัปเดตกราฟในครั้งแรก
    updateChart(filteredData);
  </script>
</body>
</html>
