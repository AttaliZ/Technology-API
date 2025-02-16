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
    <title>Pie Chart - ข้อมูลอ่างเก็บน้ำ (Cyberpunk)</title>
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
        /* Canvas */
        canvas {
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
        <h1>ข้อมูลอ่างเก็บน้ำ - Pie Chart (Cyberpunk)</h1>
        <!-- Tab Bar สำหรับเลือกภาค -->
        <div id="tabBar" class="tab-bar"></div>
        <canvas id="pieChart" width="800" height="400"></canvas>
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
                updateChart(this.dataset.region);
            });
            tabBar.appendChild(tab);
        });

        // ฟังก์ชันสำหรับเตรียมข้อมูล Pie Chart สำหรับภาคที่เลือก
        function getChartData(region) {
            const reservoirs = regionsData[region];
            const labels = reservoirs.map(r => r.name);
            // ใช้ "ความจุทั้งหมด" ของอ่างเก็บน้ำเป็นข้อมูล
            const storageData = reservoirs.map(r => r.storage || 0);
            // สร้างสีแบบสุ่มสำหรับแต่ละชิ้นส่วน (สีที่มีสไตล์ neon)
            const backgroundColors = labels.map(() => 'rgba(' +
                Math.floor(Math.random() * 156 + 100) + ', ' +  // ค่าสี 100-255
                Math.floor(Math.random() * 156 + 100) + ', ' +
                Math.floor(Math.random() * 156 + 100) + ', 0.7)'
            );
            const borderColors = backgroundColors.map(color => color.replace('0.7', '1'));
            return { labels, storageData, backgroundColors, borderColors };
        }

        // สร้าง Pie Chart เริ่มต้นสำหรับภาคแรก
        const initialRegion = regionNames[0];
        let chartData = getChartData(initialRegion);

        const ctx = document.getElementById('pieChart').getContext('2d');
        let pieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'ความจุทั้งหมด (ล้าน ลบ.ม.)',
                    data: chartData.storageData,
                    backgroundColor: chartData.backgroundColors,
                    borderColor: chartData.borderColors,
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

        // ฟังก์ชันสำหรับอัปเดต Pie Chart เมื่อเลือกภาคใหม่
        function updateChart(region) {
            const newData = getChartData(region);
            pieChart.data.labels = newData.labels;
            pieChart.data.datasets[0].data = newData.storageData;
            pieChart.data.datasets[0].backgroundColor = newData.backgroundColors;
            pieChart.data.datasets[0].borderColor = newData.borderColors;
            pieChart.update();
        }
    </script>
</body>
</html>
