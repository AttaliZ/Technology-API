<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Map Chart - อ่างเก็บน้ำประเทศไทย</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #1a1a1a;
      color: #f0f0f0;
      text-align: center;
    }

    #map-container {
      position: relative;
      width: 600px; /* ปรับขนาดได้ */
      height: 900px;
      margin: 20px auto;
      background-image: url('thailand_map.png'); /* ใช้ภาพแผนที่ประเทศไทย */
      background-size: cover;
      border: 2px solid #00ffff;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
    }

    .marker {
      position: absolute;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      text-align: center;
      font-size: 12px;
      font-weight: bold;
      cursor: pointer;
      transition: transform 0.2s ease-in-out;
    }

    .marker:hover {
      transform: scale(1.3);
    }

    /* สีของหมุดตามปริมาณน้ำ */
    .low-water { background: yellow; }
    .medium-water { background: green; }
    .high-water { background: blue; }
    .full-water { background: red; color: white; }

    .info-box {
      position: absolute;
      display: none;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
      z-index: 10;
    }
  </style>
</head>
<body>

  <h2>แผนที่อ่างเก็บน้ำประเทศไทย</h2>
  <div id="map-container"></div>

  <div id="infoBox" class="info-box"></div>

  <script>
    let reservoirs = [];

    // ฟังก์ชันดึงข้อมูลจาก fetch_data.php
    function fetchReservoirs() {
      fetch('fetch_data.php')
        .then(response => response.json())
        .then(data => {
          reservoirs = data;
          console.log("Loaded Reservoirs:", reservoirs);
          plotMarkers();
        })
        .catch(error => console.error("Error loading reservoirs:", error));
    }

    // ฟังก์ชันเลือกสีของหมุดตามปริมาณน้ำ
    function getMarkerClass(percentage) {
      if (percentage <= 30) return 'low-water';
      if (percentage <= 50) return 'medium-water';
      if (percentage <= 80) return 'high-water';
      return 'full-water';
    }

    // ฟังก์ชันวางหมุดลงบนแผนที่
    function plotMarkers() {
      const mapContainer = document.getElementById('map-container');
      const infoBox = document.getElementById('infoBox');

      reservoirs.forEach(reservoir => {
        if (!reservoir.lat || !reservoir.lng) return; // ถ้าไม่มีพิกัดให้ข้าม

        // คำนวณตำแหน่งหมุดในแผนที่ (ต้องแมป lat, lng กับตำแหน่งในภาพ)
        let x = (reservoir.lng - 97) * 10; // ค่าตัวอย่าง, ปรับตามขนาดแผนที่
        let y = (15 - reservoir.lat) * 15; // ค่าตัวอย่าง, ปรับตามขนาดแผนที่

        const marker = document.createElement('div');
        marker.classList.add('marker', getMarkerClass(reservoir.percent_storage));
        marker.style.left = `${x}px`;
        marker.style.top = `${y}px`;
        marker.title = reservoir.name;

        // แสดงข้อมูลเมื่อ hover
        marker.addEventListener('mouseenter', () => {
          infoBox.innerHTML = `
            <strong>${reservoir.name}</strong><br>
            ภาค: ${reservoir.region}<br>
            ปริมาณน้ำ: ${reservoir.percent_storage}%
          `;
          infoBox.style.left = `${x + 30}px`;
          infoBox.style.top = `${y + 30}px`;
          infoBox.style.display = 'block';
        });

        marker.addEventListener('mouseleave', () => {
          infoBox.style.display = 'none';
        });

        mapContainer.appendChild(marker);
      });
    }

    // โหลดข้อมูลเมื่อหน้าเว็บพร้อม
    window.onload = fetchReservoirs;
  </script>

</body>
</html>
