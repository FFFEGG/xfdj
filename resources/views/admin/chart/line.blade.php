<canvas id="types" width="200" height="200"></canvas>
<script>
  $(function () {

    var config = {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [
              {{ $types['m'] }},
              {{ $types['f'] }},
              {{ $types[''] }}
          ],
          backgroundColor: [
            'rgb(54, 162, 235)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)'
          ]
        }],
        labels: [
          '普通用户',
          '社区代理',
          '销售员'
        ]
      },
      options: {
        maintainAspectRatio: false
      }
    };

    var ctx = document.getElementById('types').getContext('2d');
    new Chart(ctx, config);
  });
</script>
