<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2"></i>Reportes</h4>

<!-- Tabs de reportes -->
<ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist" style="flex-wrap:nowrap;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none;">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-diario" style="font-size:11px;white-space:nowrap;"><i class="fas fa-calendar-day me-1"></i>Diario</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-semanal" style="font-size:11px;white-space:nowrap;"><i class="fas fa-calendar-week me-1"></i>Semanal</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-mensual" style="font-size:11px;white-space:nowrap;"><i class="fas fa-calendar me-1"></i>Mensual</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seccion" style="font-size:11px;white-space:nowrap;"><i class="fas fa-th-large me-1"></i>Sección</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tipo" style="font-size:11px;white-space:nowrap;"><i class="fas fa-car me-1"></i>Tipo</button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-rango" style="font-size:11px;white-space:nowrap;"><i class="fas fa-search me-1"></i>Rango</button></li>
</ul>

<div class="tab-content">
  <!-- Diario -->
  <div class="tab-pane fade show active" id="tab-diario">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">Recaudación Diaria (Últimos 30 días)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Fecha</th><th>Pagos</th><th>Total</th></tr>
            </thead>
            <tbody id="tbl-diario"><tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Semanal -->
  <div class="tab-pane fade" id="tab-semanal">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">Recaudación Semanal (Últimas 12 semanas)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Semana</th><th class="d-none d-md-table-cell">Inicio</th><th class="d-none d-md-table-cell">Fin</th><th>Pagos</th><th>Total</th></tr>
            </thead>
            <tbody id="tbl-semanal"><tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Mensual -->
  <div class="tab-pane fade" id="tab-mensual">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">Recaudación Mensual (Últimos 12 meses)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Mes</th><th>Pagos</th><th>Total</th></tr>
            </thead>
            <tbody id="tbl-mensual"><tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Por Sección -->
  <div class="tab-pane fade" id="tab-seccion">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">Recaudación por Sección (Últimos 30 días)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Sección</th><th>Pagos</th><th>Total</th></tr>
            </thead>
            <tbody id="tbl-seccion"><tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Por Tipo Vehículo -->
  <div class="tab-pane fade" id="tab-tipo">
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-header bg-white border-0 fw-bold py-2" style="font-size:13px;">Recaudación por Tipo (Últimos 30 días)</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Tipo</th><th>Pagos</th><th>Total</th></tr>
            </thead>
            <tbody id="tbl-tipo"><tr><td colspan="3" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Por Rango -->
  <div class="tab-pane fade" id="tab-rango">
    <div class="card border-0 shadow-sm mb-2" style="border-radius: 10px;">
      <div class="card-body py-2 px-2">
        <div class="row g-2 align-items-end">
          <div class="col-5 col-md-3">
            <label class="form-label fw-bold mb-0" style="font-size:11px;">Inicio</label>
            <input type="date" class="form-control form-control-sm" id="rango-inicio" style="font-size:12px;">
          </div>
          <div class="col-5 col-md-3">
            <label class="form-label fw-bold mb-0" style="font-size:11px;">Fin</label>
            <input type="date" class="form-control form-control-sm" id="rango-fin" style="font-size:12px;">
          </div>
          <div class="col-2 col-md-3">
            <button class="btn btn-primary btn-sm w-100" onclick="loadRango()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="card border-0 shadow-sm" style="border-radius: 10px;">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-sm mb-0">
            <thead class="table-light">
              <tr><th>Cajón</th><th>Placa</th><th class="d-none d-md-table-cell">Marca/Color</th><th class="d-none d-lg-table-cell">Tipo</th><th class="d-none d-md-table-cell">Sección</th><th>Entrada</th><th>Salida</th><th>Monto</th><th class="d-none d-md-table-cell">Recibo</th></tr>
            </thead>
            <tbody id="tbl-rango"><tr><td colspan="9" class="text-center text-muted">Selecciona fechas</td></tr></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function loadDiario() {
    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr><td>' + r.fecha + '</td><td>' + r.cantidad + '</td><td class="fw-bold text-success">$' + parseFloat(r.total).toFixed(2) + '</td></tr>';
        });
        $('#tbl-diario').html(html);
      } else {
        $('#tbl-diario').html('<tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>');
      }
    }, { action: 'recaudacion_diaria' });
  }

  function loadSemanal() {
    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr><td>' + r.semana + '</td><td class="d-none d-md-table-cell">' + r.inicio + '</td><td class="d-none d-md-table-cell">' + r.fin + '</td><td>' + r.cantidad + '</td><td class="fw-bold text-success">$' + parseFloat(r.total).toFixed(2) + '</td></tr>';
        });
        $('#tbl-semanal').html(html);
      } else {
        $('#tbl-semanal').html('<tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>');
      }
    }, { action: 'recaudacion_semanal' });
  }

  function loadMensual() {
    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr><td>' + r.mes + '</td><td>' + r.cantidad + '</td><td class="fw-bold text-success">$' + parseFloat(r.total).toFixed(2) + '</td></tr>';
        });
        $('#tbl-mensual').html(html);
      } else {
        $('#tbl-mensual').html('<tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>');
      }
    }, { action: 'recaudacion_mensual' });
  }

  function loadPorSeccion() {
    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr><td>' + r.seccion + '</td><td>' + r.cantidad + '</td><td class="fw-bold text-success">$' + parseFloat(r.total).toFixed(2) + '</td></tr>';
        });
        $('#tbl-seccion').html(html);
      } else {
        $('#tbl-seccion').html('<tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>');
      }
    }, { action: 'por_seccion' });
  }

  function loadPorTipo() {
    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var icons = { auto: '🚗', moto: '🏍️', camioneta: '🛻', bicicleta: '🚲' };
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr><td>' + (icons[r.tipo_vehiculo] || '') + ' ' + r.tipo_vehiculo + '</td><td>' + r.cantidad + '</td><td class="fw-bold text-success">$' + parseFloat(r.total).toFixed(2) + '</td></tr>';
        });
        $('#tbl-tipo').html(html);
      } else {
        $('#tbl-tipo').html('<tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>');
      }
    }, { action: 'por_tipo_vehiculo' });
  }

  function loadRango() {
    var inicio = $('#rango-inicio').val();
    var fin = $('#rango-fin').val();
    if (!inicio || !fin) { MsgNotify("Selecciona ambas fechas", "error"); return; }

    MsgServer(path.model + 'reportes.php', function(dat) {
      if (dat.result && dat.data.length > 0) {
        var html = '';
        dat.data.forEach(function(r) {
          html += '<tr>';
          html += '<td><span class="badge bg-dark" style="font-size:10px;">' + r.cajon + '</span></td>';
          html += '<td class="fw-bold" style="font-size:12px;">' + r.placa + '</td>';
          html += '<td class="d-none d-md-table-cell"><small>' + (r.marca || '-') + ' / ' + (r.color || '-') + '</small></td>';
          html += '<td class="d-none d-lg-table-cell"><small>' + r.tipo_vehiculo + '</small></td>';
          html += '<td class="d-none d-md-table-cell"><small>' + r.seccion_nombre + '</small></td>';
          html += '<td><small>' + r.fecha_entrada + '</small></td>';
          html += '<td><small>' + (r.fecha_salida || '-') + '</small></td>';
          html += '<td class="fw-bold text-success">' + (r.monto_total ? '$' + parseFloat(r.monto_total).toFixed(2) : '-') + '</td>';
          html += '<td class="d-none d-md-table-cell"><code style="font-size:10px;">' + (r.recibo || '-') + '</code></td>';
          html += '</tr>';
        });
        $('#tbl-rango').html(html);
      } else {
        $('#tbl-rango').html('<tr><td colspan="9" class="text-center text-muted">Sin registros en este rango</td></tr>');
      }
    }, { action: 'registros_rango', fecha_inicio: inicio, fecha_fin: fin });
  }

  $(function() { 
    $('#rango-inicio').val(new Date().toISOString().split('T')[0]);
    $('#rango-fin').val(new Date().toISOString().split('T')[0]);
    loadDiario();
    loadSemanal();
    loadMensual();
    loadPorSeccion();
    loadPorTipo();
  });
</script>

<?php include('view_footer.php'); ?>