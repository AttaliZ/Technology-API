<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แผนที่ประเทศไทย</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- โหลด CSS ของ Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    
    <style>
        /* ทำให้แผนที่เต็มหน้าจอ */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
            width: 100%;
        }

        #legend {
    position: absolute;
    bottom: 30px;
    left: 10px;
    background: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    font-size: 14px;
    z-index: 1000;
}

        #legend h4 {
            margin: 0 0 10px;
            text-align: center;
        }
        #legend div {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        #legend img {
            width: 20px;
            height: 20px;
        }
    </style>
</head>
<body>

    <div id="map"></div> <!-- แสดงแผนที่เต็มจอ -->

    <!-- กล่อง Legend สำหรับแสดงสีของหมุด -->
    <div id="legend">
        <h4>ระดับน้ำ</h4>
        <div><img src="https://maps.google.com/mapfiles/ms/icons/green-dot.png"> มากกว่า 80% (สูง)</div>
        <div><img src="https://maps.google.com/mapfiles/ms/icons/yellow-dot.png"> 50% - 79% (ปานกลาง)</div>
        <div><img src="https://maps.google.com/mapfiles/ms/icons/red-dot.png"> น้อยกว่า 50% (ต่ำ)</div>
    </div>

    <!-- โหลด JS ของ Leaflet -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        // สร้างแผนที่เต็มจอ และตั้งศูนย์กลางที่ประเทศไทย
        var map = L.map('map').setView([13.736717, 100.523186], 6);

        // ใช้แผนที่จาก OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // ✅ ข้อมูลตัวอย่างของอ่างเก็บน้ำในประเทศไทย
        var reservoirs = [
          { name: "เขื่อนภูมิพล", lat: 17.2419, lng: 99.0300, water_level: 85 },
    { name: "เขื่อนสิริกิติ์", lat: 17.7600, lng: 100.5500, water_level: 72 },
    { name: "เขื่อนป่าสักชลสิทธิ์", lat: 14.9100, lng: 101.0150, water_level: 60 },
    { name: "เขื่อนแควน้อยบำรุงแดน", lat: 16.8200, lng: 100.2083, water_level: 45 },
    { name: "เขื่อนศรีนครินทร์", lat: 14.4380, lng: 99.1486, water_level: 30 },
    { name: "เขื่อนวชิราลงกรณ", lat: 14.5500, lng: 99.2500, water_level: 50 },
    { name: "เขื่อนอุบลรัตน์", lat: 16.7500, lng: 102.6667, water_level: 65 },
    { name: "เขื่อนจุฬาภรณ์", lat: 16.5333, lng: 101.6667, water_level: 40 },
    { name: "เขื่อนลำพระเพลิง", lat: 14.8333, lng: 101.8333, water_level: 55 },
    { name: "เขื่อนลำนางรอง", lat: 14.6167, lng: 102.8000, water_level: 70 },
    { name: "เขื่อนลำแซะ", lat: 14.9167, lng: 102.0333, water_level: 48 },
    { name: "เขื่อนลำมูลบน", lat: 15.0833, lng: 102.3000, water_level: 62 },
    { name: "เขื่อนลำตะคอง", lat: 14.9333, lng: 101.7667, water_level: 58 },
    { name: "เขื่อนลำปลายมาศ", lat: 15.1833, lng: 102.8167, water_level: 47 },
    { name: "เขื่อนลำสนธิ", lat: 14.9667, lng: 101.9167, water_level: 64 },
    { name: "เขื่อนลำเชียงไกร", lat: 15.0167, lng: 102.1333, water_level: 59 },
    { name: "เขื่อนน้ำพุง", lat: 17.1000, lng: 103.0833, water_level: 75 },
    { name: "เขื่อนห้วยหลวง", lat: 17.8833, lng: 102.5833, water_level: 68 },
    { name: "เขื่อนน้ำอูน", lat: 17.1667, lng: 103.8333, water_level: 52 },
    { name: "เขื่อนน้ำพอง", lat: 16.7333, lng: 102.8333, water_level: 80 },
    { name: "เขื่อนห้วยเสนง", lat: 14.8833, lng: 103.4833, water_level: 57 },
    { name: "เขื่อนทับเสลา", lat: 15.3167, lng: 99.5167, water_level: 63 },
    { name: "เขื่อนกระเสียว", lat: 14.8333, lng: 99.8000, water_level: 49 },
    { name: "เขื่อนขุนด่านปราการชล", lat: 14.0833, lng: 101.3667, water_level: 71 },
    { name: "เขื่อนคลองสียัด", lat: 13.2167, lng: 101.5500, water_level: 66 },
    { name: "เขื่อนบางลาง", lat: 6.1667, lng: 101.2667, water_level: 54 },
    { name: "เขื่อนรัชชประภา", lat: 8.9167, lng: 98.5333, water_level: 77 },
    { name: "เขื่อนบางพระ", lat: 13.207124, lng: 100.97352876277691, water_level: 43 },
    { name: "เขื่อนหนองปลาไหล", lat: 12.8333, lng: 101.1667, water_level: 61 },
    { name: "เขื่อนประแสร์", lat: 12.6333, lng: 101.4500, water_level: 69 },
        ];

        // ฟังก์ชันเลือกไอคอน Marker ตามระดับน้ำ
        function getIcon(waterLevel) {
            let iconUrl;
            if (waterLevel >= 80) {
                iconUrl = "https://maps.google.com/mapfiles/ms/icons/green-dot.png"; // 🟢 น้ำเยอะ
            } else if (waterLevel >= 50) {
                iconUrl = "https://maps.google.com/mapfiles/ms/icons/yellow-dot.png"; // 🟠 ปานกลาง
            } else {
                iconUrl = "https://maps.google.com/mapfiles/ms/icons/red-dot.png"; // 🔴 น้ำน้อย
            }

            return L.icon({
                iconUrl: iconUrl,
                iconSize: [32, 32], // ขนาดหมุด
                iconAnchor: [16, 32], // จุดยึด
                popupAnchor: [0, -32] // ทำให้ Popup อยู่ด้านบน
            });
        }

        // วนลูปสร้าง Marker และใช้ไอคอนที่เหมาะสม
        reservoirs.forEach(res => {
            var marker = L.marker([res.lat, res.lng], {
                icon: getIcon(res.water_level),
                draggable: true // ✅ ทำให้ลากหมุดได้
            }).addTo(map);

            // ✅ แสดง popup เมื่อคลิกที่ Marker
            marker.bindPopup(`<b>${res.name}</b><br>ระดับน้ำ: ${res.water_level}%`);
        });
    </script>
</body>
</html>
