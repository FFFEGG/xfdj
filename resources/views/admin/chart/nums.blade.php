<canvas id="nums" width="200" height="200"></canvas>
<script>
  $(function () {
    var config = {
      type: 'line',
      data: {
        datasets: [
          {
            data: [
              81, 90, 40, 100, 130
            ],
            label: '男',
            borderColor: "#36A2EB", //路径颜色
            pointBackgroundColor: "#36A2EB", //数据点颜色
            pointBorderColor: "#fff", //数据点边框颜色
          },
          {
            data: [
              100, 150, 30, 60, 200
            ],
            label: '女',
            borderColor: "#F70938", //路径颜色
            pointBackgroundColor: "#F70938", //数据点颜色
            pointBorderColor: "#fff", //数据点边框颜色
          },
          {
            data: [
              181, 240, 70, 160, 330
            ],
            label: '总人数',

            borderColor: "#00FF00", //路径颜色
            pointBackgroundColor: "#00FF00", //数据点颜色
            pointBorderColor: "#fff", //数据点边框颜色
          }
        ],
        labels: [
          "第一项", "第二项", "第三项", "第四项", "第五项"
        ]
      },
      options: {
        maintainAspectRatio: false,
      }
    };

    var ctx = document.getElementById('nums').getContext('2d');
    new Chart(ctx, config);
  });
</script>
