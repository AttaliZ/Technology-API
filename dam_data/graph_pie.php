<?php 
include 'fetch_data.php'; 

// จัดกลุ่มข้อมูลอ่างเก็บน้ำตามภาค (region)
$regions = [];
if (!empty($data['data'])) {
    foreach ($data['data'] as $regionData) {
        if (!empty($regionData['reservoir'])) {
            // สมมติว่าในแต่ละ region มี key 'region' สำหรับชื่อภาค
            $regionName = isset($regionData['region']) ? $regionData['region'] : 'ไม่ระบุภาค';
            $regions[$regionName] = $regionData['reservoir'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ข้อมูลอ่างเก็บน้ำ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- รวม Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Cyberpunk Theme CSS -->
    <link href="https://fonts.googleapis.com/css?family=Orbitron:400,700&display=swap" rel="stylesheet">
    <style>
        /* Global Cyberpunk Styles */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #0d0d0d;
            color: #e0e0e0;
            overflow-x: hidden;
            animation: fadeIn 1s ease-out;
            text-align: center;
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
            margin-bottom: 20px;
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
        /* Tab Bar สำหรับภาค */
        .tab-bar {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            margin: 20px 0;
            padding: 10px 0;
            border-top: 1px solid #333;
        }
        .tab {
            display: inline-block;
            padding: 10px 20px;
            margin: 0 5px;
            background: #000;
            color: #0ff;
            border: 1px solid #0ff;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }
        .tab:hover {
            background: linear-gradient(45deg, #ff00ff, #00ffff);
            transform: scale(1.05);
        }
        .tab.active {
            background: linear-gradient(45deg, #ff00ff, #00ffff);
            color: #fff;
            transform: scale(1.1);
        }
        /* Search Box */
        .search-container {
            margin: 20px 0;
        }
        .search-container input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #0ff;
            border-radius: 5px;
            background: #000;
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
        /* Canvas */
        canvas {
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
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background: linear-gradient(45deg, #ff00ff, #00ffff);
            color: #fff;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .pagination button:hover {
            transform: scale(1.05);
        }
        .pagination button:disabled {
            opacity: 0.5;
            cursor: default;
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
        <!-- Tab Bar สำหรับเลือกภาค -->
        <div id="tabBar" class="tab-bar"></div>
        <!-- Search Box สำหรับค้นหาชื่ออ่างเก็บน้ำในภาคที่เลือก -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="ค้นหาชื่ออ่างเก็บน้ำ...">
            <button id="searchBtn">ค้นหา</button>
        </div>
        <canvas id="pieChart" width="800" height="400"></canvas>
        <!-- Pagination -->
        <div class="pagination">
            <button id="prevBtn">Previous</button>
            <span id="pageInfo">Page 1</span>
            <button id="nextBtn">Next</button>
        </div>
    </div>
    <script>
        // รับข้อมูลจาก PHP แยกตามภาค
        const regionsData = <?php echo json_encode($regions); ?>;
        // ดึงรายชื่อภาค
        const regionNames = Object.keys(regionsData);

        // สร้าง Tab Bar สำหรับภาค
        const tabBar = document.getElementById('tabBar');
        regionNames.forEach((region, index) => {
            const tab = document.createElement('div');
            tab.className = 'tab' + (index === 0 ? ' active' : '');
            tab.innerText = region;
            tab.dataset.region = region;
            tab.addEventListener('click', function() {
                // เปลี่ยน active tab
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                // เปลี่ยนภาคที่เลือกและรีเซ็ตการค้นหาและแบ่งหน้า
                currentRegion = this.dataset.region;
                currentRegionData = regionsData[currentRegion];
                filteredData = currentRegionData;
                currentPage = 1;
                document.getElementById('searchInput').value = '';
                updateChart();
            });
            tabBar.appendChild(tab);
        });

        // กำหนดตัวแปรสำหรับจัดการค้นหาและแบ่งหน้า
        let currentRegion = regionNames[0];
        let currentRegionData = regionsData[currentRegion];
        let filteredData = currentRegionData; // เริ่มต้นคือข้อมูลทั้งหมดของภาคที่เลือก
        let currentPage = 1;
        const itemsPerPage = 10; // จำนวนรายการต่อหน้า

        // ฟังก์ชันกรองข้อมูลตามคำค้น (ค้นหาแบบ case-insensitive)
        function filterData(query, dataArray) {
            query = query.toLowerCase();
            return dataArray.filter(r => r.name && r.name.toLowerCase().includes(query));
        }

        // ฟังก์ชันแบ่งข้อมูลตามหน้า
        function getPageData(data, page, perPage) {
            const start = (page - 1) * perPage;
            return data.slice(start, start + perPage);
        }

        // ฟังก์ชันสำหรับสร้างข้อมูล Pie Chart จากอาร์เรย์ข้อมูลที่เลือก (paginated)
        function getChartData(dataArray) {
            const labels = dataArray.map(r => r.name);
            const storageData = dataArray.map(r => r.storage || 0);
            // สร้างสีแบบสุ่มที่มีสไตล์ neon
            const backgroundColors = labels.map(() => 'rgba(' +
                Math.floor(Math.random() * 156 + 100) + ', ' +
                Math.floor(Math.random() * 156 + 100) + ', ' +
                Math.floor(Math.random() * 156 + 100) + ', 0.7)'
            );
            const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));
            return { labels, storageData, backgroundColors, borderColors };
        }

        // สร้าง Pie Chart เริ่มต้นด้วยข้อมูลหน้าแรกของภาคเริ่มต้น
        const ctx = document.getElementById('pieChart').getContext('2d');
        const initialPageData = getPageData(filteredData, currentPage, itemsPerPage);
        let initialChartData = getChartData(initialPageData);

        let pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: initialChartData.labels,
                datasets: [{
                    label: 'ความจุทั้งหมด (ล้าน ลบ.ม.)',
                    data: initialChartData.storageData,
                    backgroundColor: initialChartData.backgroundColors,
                    borderColor: initialChartData.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#0ff',
                            font: { family: 'Orbitron' }
                        }
                    }
                }
            }
        });

        // ฟังก์ชันอัปเดต Pie Chart ตามข้อมูลที่กรองและหน้าปัจจุบัน
        function updateChart() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage) || 1;
            if (currentPage > totalPages) { currentPage = totalPages; }
            if (currentPage < 1) { currentPage = 1; }
            const pageData = getPageData(filteredData, currentPage, itemsPerPage);
            const chartData = getChartData(pageData);
            pieChart.data.labels = chartData.labels;
            pieChart.data.datasets[0].data = chartData.storageData;
            pieChart.data.datasets[0].backgroundColor = chartData.backgroundColors;
            pieChart.data.datasets[0].borderColor = chartData.borderColors;
            pieChart.update();

            document.getElementById('pageInfo').innerText = `Page ${currentPage} of ${totalPages}`;
            document.getElementById('prevBtn').disabled = (currentPage <= 1);
            document.getElementById('nextBtn').disabled = (currentPage >= totalPages);
        }

        // เมื่อกดปุ่มค้นหา ให้กรองข้อมูลในภาคที่เลือกแล้วรีเซ็ตหน้าเป็นหน้าแรก
        document.getElementById('searchBtn').addEventListener('click', function() {
            const query = document.getElementById('searchInput').value.trim();
            filteredData = query ? filterData(query, currentRegionData) : currentRegionData;
            currentPage = 1;
            updateChart();
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
                updateChart();
            }
        });
        document.getElementById('nextBtn').addEventListener('click', function() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updateChart();
            }
        });

        // เริ่มต้นแสดงผล Pie Chart
        updateChart();
    </script>
</body>
</html>
