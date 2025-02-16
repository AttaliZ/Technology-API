<?php include 'fetch_data.php'; ?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Map Chart - อ่างเก็บน้ำประเทศไทย</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="https://d3js.org/d3.v6.min.js"></script>
  <script src="https://d3-geo-projection.github.io/d3-geo-projection.v2.min.js"></script>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #1a1a1a;
      color: white;
      text-align: center;
    }

    #map-container {
      width: 800px;
      height: 900px;
      margin: 20px auto;
    }

    svg {
      width: 100%;
      height: 100%;
    }

    .region {
      fill: #333;
      stroke: #ffffff;
      stroke-width: 0.5px;
    }

    .marker {
      fill: red;
      stroke: white;
      stroke-width: 1px;
      cursor: pointer;
    }

    .tooltip {
      position: absolute;
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 8px;
      border-radius: 5px;
      font-size: 14px;
      display: none;
    }
  </style>
</head>
<body>

  <h2>แผนที่อ่างเก็บน้ำประเทศไทย</h2>
  <div id="map-container"></div>
  <div id="tooltip" class="tooltip"></div>

  <script>
    const width = 800, height = 900;

    // ตั้งค่าการฉายแผนที่ประเทศไทย (Projection)
    const projection = d3.geoMercator()
      .center([100.9925, 13.736717])  // จุดศูนย์กลางของประเทศไทย
      .scale(3500) // ปรับขนาดแผนที่
      .translate([width / 2, height / 2]);

    const path = d3.geoPath().projection(projection);

    const svg = d3.select("#map-container").append("svg")
      .attr("width", width)
      .attr("height", height);

    const tooltip = document.getElementById("tooltip");

    // โหลด GeoJSON ของประเทศไทย
    d3.json("https://raw.githubusercontent.com/apisit/thailand.json/master/thailand.json").then(geoData => {
      svg.selectAll(".region")
        .data(geoData.features)
        .enter().append("path")
        .attr("class", "region")
        .attr("d", path);

      // ดึงข้อมูลอ่างเก็บน้ำจาก fetch_data.php
      fetch('fetch_data.php')
        .then(response => response.json())
        .then(data => {
          data.forEach(reservoir => {
            if (!reservoir.lat || !reservoir.lng) return;

            const [x, y] = projection([reservoir.lng, reservoir.lat]);

            svg.append("circle")
              .attr("class", "marker")
              .attr("cx", x)
              .attr("cy", y)
              .attr("r", 5)
              .on("mouseover", function(event) {
                tooltip.style.left = event.pageX + "px";
                tooltip.style.top = event.pageY - 30 + "px";
                tooltip.style.display = "block";
                tooltip.innerHTML = `<strong>${reservoir.name}</strong><br>ภาค: ${reservoir.region}<br>ปริมาณน้ำ: ${reservoir.percent_storage}%`;
              })
              .on("mouseout", function() {
                tooltip.style.display = "none";
              });
          });
        })
        .catch(error => console.error("Error loading reservoirs:", error));
    });

  </script>

</body>
</html>
