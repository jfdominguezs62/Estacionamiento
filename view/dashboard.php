<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-3"><i class="fas fa-tachometer-alt me-2"></i>Panel de Control</h4>

<!-- Tarjetas resumen -->
<div class="row mb-3 g-2">
  <div class="col-6 col-md-3 mb-2">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-body text-center py-2">
        <div class="mb-1"><i class="fas fa-car fa-lg" style="color: #1a237e;"></i></div>
        <h4 class="fw-bold mb-0" id="stat-dentro">0</h4>
        <small class="text-muted" style="font-size:10px;">Vehículos Dentro</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-body text-center py-2">
        <div class="mb-1"><i class="fas fa-clipboard-list fa-lg" style="color: #0d47a1;"></i></div>
        <h4 class="fw-bold mb-0" id="stat-registros-hoy">0</h4>
        <small class="text-muted" style="font-size:10px;">Registros Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-body text-center py-2">
        <div class="mb-1"><i class="fas fa-dollar-sign fa-lg" style="color: #1b5e20;"></i></div>
        <h4 class="fw-bold mb-0" id="stat-cobro-hoy">$0.00</h4>
        <small class="text-muted" style="font-size:10px;">Cobro Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3 mb-2">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-body text-center py-2">
        <div class="mb-1"><i class="fas fa-calendar fa-lg" style="color: #e65100;"></i></div>
        <h4 class="fw-bold mb-0" id="stat-cobro-mes">$0.00</h4>
        <small class="text-muted" style="font-size:10px;">Cobro Mes</small>
      </div>
    </div>
  </div>
</div>

<!-- Ocupación y registros -->
<div class="row mb-3 g-2">
  <div class="col-12 col-md-6">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">
        <i class="fas fa-th-large me-2"></i>Ocupación por Sección
      </div>
      <div class="card-body py-2" id="secciones-ocupacion">
        <p class="text-muted">Cargando...</p>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">
        <i class="fas fa-car me-2"></i>Últimos Registros Hoy
      </div>
      <div class="card-body py-2" id="registros-hoy" style="max-height: 300px; overflow-y: auto;">
        <p class="text-muted">Cargando...</p>
      </div>
    </div>
  </div>
</div>

<script>
  function loadStats() {
    MsgServer(path.model + 'dashboard.php', function(dat) {
      if (dat.result) {
        var d = dat.data;
        $('#stat-dentro').text(d.vehiculos_dentro);
        $('#stat-registros-hoy').text(d.registros_hoy);
        $('#stat-cobro-hoy').text('$' + parseFloat(d.cobro_hoy).toFixed(2));
        $('#stat-cobro-mes').text('$' + parseFloat(d.cobro_mes).toFixed(2));
      }
    }, { action: 'stats' });
  }

  function loadSeccionesOcupacion() {
    MsgServer(path.model + 'dashboard.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(s) {
          var pct = s.capacidad > 0 ? Math.round((s.ocupados / s.capacidad) * 100) : 0;
          var color = pct > 80 ? '#dc3545' : pct > 50 ? '#ffc107' : '#198754';
          html += '<div class="mb-3">';
          html += '<div class="d-flex justify-content-between mb-1">';
          html += '<span class="fw-bold">' + s.nombre + '</span>';
          html += '<span>' + s.ocupados + '/' + s.capacidad + ' (' + pct + '%)</span>';
          html += '</div>';
          html += '<div class="progress" style="height: 8px;">';
          html += '<div class="progress-bar" role="progressbar" style="width: ' + pct + '%; background-color: ' + color + ';"></div>';
          html += '</div></div>';
        });
        $('#secciones-ocupacion').html(html);
      } else {
        $('#secciones-ocupacion').html('<p class="text-muted">No hay secciones configuradas.</p>');
      }
    }, { action: 'secciones_ocupacion' });
  }

  function loadRegistrosHoy() {
    MsgServer(path.model + 'dashboard.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '<table class="table table-sm table-hover">';
        html += '<thead><tr><th>Cajón</th><th>Placa</th><th>Marca/Color</th><th>Tipo</th><th>Sección</th><th>Entrada</th></tr></thead><tbody>';
        dat.data.forEach(function(r) {
          var icon = r.tipo_vehiculo === 'auto' ? 'fa-car' : r.tipo_vehiculo === 'moto' ? 'fa-motorcycle' : r.tipo_vehiculo === 'camioneta' ? 'fa-truck' : 'fa-bicycle';
          html += '<tr>';
          html += '<td><span class="badge bg-dark">' + r.cajon + '</span></td>';
          html += '<td class="fw-bold">' + r.placa + '</td>';
          html += '<td><small>' + (r.marca || '-') + ' / ' + (r.color || '-') + '</small></td>';
          html += '<td><i class="fas ' + icon + ' me-1"></i>' + r.tipo_vehiculo + '</td>';
          html += '<td>' + r.seccion_nombre + '</td>';
          html += '<td>' + r.fecha_entrada + '</td>';
          html += '</tr>';
        });
        html += '</tbody></table>';
        $('#registros-hoy').html(html);
      } else {
        $('#registros-hoy').html('<p class="text-muted">No hay registros hoy.</p>');
      }
    }, { action: 'registros_hoy' });
  }

  $(function() {
    loadStats();
    loadSeccionesOcupacion();
    loadRegistrosHoy();
  });
</script>

<?php include('view_footer.php'); ?>