<?php
/**
 * view_footer.php - Pie común para todas las vistas del módulo
 */

echo '</div>'; // container-fluid
echo '</div>'; // app-content
?>
<script>
document.getElementById('app-main').style.marginLeft = '260px';

function logout() {
  MsgServer( path.model + 'login.php', function(dat) {
    if ( dat.result ) {
      MsgNotify("Sesión cerrada.", "success");
      location.href = '../index.php';
    }
  }, { action: 'logout' });
}
</script>
<?php $oWeb->End(); ?>