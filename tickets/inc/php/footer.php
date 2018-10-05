</div>
<div class="footer" style="margin-top: 20px; margin-bottom: 40px; text-align: right;">
    <div class="container">
        <p>Copyright © Bachmann Konrad <?php echo date('Y', microtime(true)); ?></p>
    </div>
</div>
<script src='inc/js/jquery.timeago.js' type='text/javascript'></script>
<script src='inc/js/bootstrap.min.js' type='text/javascript'></script>
<script>
    jQuery(document).ready(function () {
        jQuery("time.timeago").timeago();
    });
</script>
</body>
</html>