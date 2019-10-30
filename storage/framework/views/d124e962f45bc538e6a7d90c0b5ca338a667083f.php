<canvas id="doughnut" width="200" height="200"></canvas>
<script>
  $(function () {

    var config = {
      type: 'doughnut',
      data: {
        datasets: [{
          data: [
              <?php echo e($gender['star'], false); ?>,
              <?php echo e($gender['yg'], false); ?>,
              <?php echo e($gender['end'], false); ?>

          ],
          backgroundColor: [
            'rgb(54, 162, 235)',
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)'
          ]
        }],
        labels: [
          '已开始',
          '预告',
          '已结束'
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
<?php /**PATH /www/wwwroot/xfdj/resources/views/admin/product/gender.blade.php ENDPATH**/ ?>