<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลอ่างเก็บน้ำ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- ใช้ฟอนต์ Orbitron -->
  <link href="https://fonts.googleapis.com/css?family=Orbitron:400,700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Orbitron', sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #1a1a1a;
      color: #f0f0f0;
      text-align: center;
    }
    /* Menu Bar */
    .menu {
      background-color: #333;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 16px;
      gap: 20px;
      border-top: 4px solid #555;
      margin-bottom: 20px;
      border-radius: 8px;
    }
    .menu a {
      text-decoration: none;
      color: #0ff;
      font-weight: bold;
      padding: 10px 20px;
      border-radius: 5px;
      background-color: #222;
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
      background-color: #ff00ff;
      transform: translateX(-50%);
      transition: height 0.3s ease-in-out;
    }
    .menu a:hover {
      color: #fff;
      background: linear-gradient(45deg, #ff00ff, #00ffff);
      transform: scale(1.05);
    }
    .menu a:hover::after {
      height: 5px;
    }
    /* Container */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      background-color: #222;
      border: 1px solid #444;
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.7);
      padding: 20px;
      border-radius: 8px;
    }
    h1 {
      color: #0ff;
      margin-bottom: 20px;
    }
    /* Search Container */
    .search-container {
      margin: 20px 0;
      text-align: center;
    }
    .search-container input[type="text"] {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #0ff;
      border-radius: 5px;
      background-color: #111;
      color: #0ff;
      outline: none;
    }
    .search-container button {
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
    .search-container button:hover {
      transform: scale(1.05);
    }
    /* Group Section */
    .group {
      margin-bottom: 30px;
      text-align: left;
      padding: 10px;
      border-bottom: 1px solid #444;
    }
    .group h2 {
      color: #ff00ff;
      margin-bottom: 10px;
    }
    .dam-list {
      list-style: none;
      padding-left: 20px;
    }
    .dam-list li {
      padding: 5px 0;
      color: #ccc;
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
    
    <!-- Search Container สำหรับค้นหารายชื่ออ่างเก็บน้ำ -->
    <div class="search-container">
      <input type="text" id="searchInput" placeholder="ค้นหาอ่างเก็บน้ำ...">
      <button id="searchBtn">ค้นหา</button>
    </div>
    
    <?php
      // จัดกลุ่มข้อมูลอ่างเก็บน้ำโดยใช้ key 'province' ถ้ามี
      // หากไม่มีให้ใช้ key 'region' แทน
      $groups = array();
      foreach ($reservoirs as $reservoir) {
          if (isset($reservoir['province']) && !empty($reservoir['province'])) {
              $groupKey = $reservoir['province'];
          } elseif (isset($reservoir['region']) && !empty($reservoir['region'])) {
              $groupKey = $reservoir['region'];
          } else {
              $groupKey = 'รวมทั้งหมด';
          }
          
          if (!isset($groups[$groupKey])) {
              $groups[$groupKey] = array();
          }
          // เก็บชื่ออ่างเก็บน้ำ
          $groups[$groupKey][] = $reservoir['name'];
      }
      
      // แสดงข้อมูลแต่ละกลุ่ม
      if (!empty($groups)) {
          foreach ($groups as $groupName => $damNames) {
              echo '<div class="group">';
              echo '<h2>' . htmlspecialchars($groupName) . '</h2>';
              echo '<ul class="dam-list">';
              foreach ($damNames as $dam) {
                  echo '<li>' . htmlspecialchars($dam) . '</li>';
              }
              echo '</ul>';
              echo '</div>';
          }
      } else {
          echo '<p>ไม่พบข้อมูลอ่างเก็บน้ำ</p>';
      }
    ?>
  </div>
  
  <script>
    // ฟังก์ชันสำหรับค้นหาชื่ออ่างเก็บน้ำในแต่ละกลุ่ม
    function filterDams() {
      const query = document.getElementById('searchInput').value.toLowerCase();
      const groups = document.querySelectorAll('.group');
      groups.forEach(group => {
        const items = group.querySelectorAll('.dam-list li');
        let groupHasMatch = false;
        items.forEach(item => {
          if (item.textContent.toLowerCase().indexOf(query) > -1) {
            item.style.display = '';
            groupHasMatch = true;
          } else {
            item.style.display = 'none';
          }
        });
        // ซ่อนกลุ่มหากไม่มีข้อมูลตรงกับการค้นหา
        group.style.display = groupHasMatch ? '' : 'none';
      });
    }
    
    document.getElementById('searchBtn').addEventListener('click', filterDams);
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
      if (e.key === 'Enter') {
        filterDams();
      }
    });
  </script>
</body>
</html>
