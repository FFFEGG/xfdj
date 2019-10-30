<canvas id="doughnut" width="200" height="200"></canvas>
<script>
  $(function () {

    var config = {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [
              <?php echo e($gender['m'], false); ?>,
              <?php echo e($gender['f'], false); ?>,
              <?php echo e($gender[''], false); ?>

          ],
          backgroundColor: [
            'rgb(54, 162, 235)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)'
          ]
        }],
        labels: [
          '男',
          '女',
          '未知'
        ]
      },
      options: {
        maintainAspectRatio: false
      }
    };

    var ctx = document.getElementById('doughnut').getContext('2d');
    new Chart(ctx, config);
  });
</script>
<?php /**PATH /www/wwwroot/xfdj/resources/views/admin/chart/gender.blade.php ENDPATH**/ ?>