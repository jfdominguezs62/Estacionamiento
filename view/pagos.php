<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_tweb() . 'core.sidebar.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-4"><i class="fas fa-money-check-alt me-2"></i>Historial de Pagos</h4>

<!-- Resumen -->
<div class="row mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
      <div class="card-body">
        <i class="fas fa-calendar-day fa-2x text-primary mb-2"></i>
        <h4 class="fw-bold mb-0" id="res-cant-hoy">0</h4>
        <small class="text-muted">Pagos Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
      <div class="card-body">
        <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
        <h4 class="fw-bold mb-0" id="res-total-hoy">$0.00</h4>
        <small class="text-muted">Cobrado Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
      <div class="card-body">
        <i class="fas fa-calendar-alt fa-2x text-info mb-2"></i>
        <h4 class="fw-bold mb-0" id="res-cant-mes">0</h4>
        <small class="text-muted">Pagos Mes</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 12px;">
      <div class="card-body">
        <i class="fas fa-coins fa-2x text-warning mb-2"></i>
        <h4 class="fw-bold mb-0" id="res-total-mes">$0.00</h4>
        <small class="text-muted">Cobrado Mes</small>
      </div>
    </div>
  </div>
</div>

<!-- Buscar recibo -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
  <div class="card-body">
    <div class="input-group">
      <input type="text" class="form-control" id="buscar-recibo" placeholder="Buscar por número de recibo (Ej: REC-20260729-00001)" style="border-radius: 8px 0 0 8px;">
      <button class="btn btn-primary" onclick="buscarRecibo()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">
        <i class="fas fa-search me-1"></i>Buscar
      </button>
    </div>
  </div>
</div>

<!-- Tabla de pagos -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover table-striped">
        <thead style="background: #f8f9fa;">
          <tr>
            <th>Cajón</th>
            <th>Placa</th>
            <th>Marca/Color</th>
            <th>Tipo</th>
            <th>Sección</th>
            <th>Entrada</th>
            <th>Salida</th>
            <th>Horas</th>
            <th>Monto</th>
            <th>Método</th>
            <th>Fecha Pago</th>
            <th class="text-center">Acción</th>
          </tr>
        </thead>
        <tbody id="tbl-pagos">
          <tr><td colspan="13" class="text-center text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 12px;">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #1b5e20, #2e7d32); border-radius: 12px 12px 0 0;">
        <h5 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i>Comprobante de Pago</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4" id="comprobante-content">
      </div>
      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  function loadPagos() {
    MsgServer(path.model + 'pagos.php', function(dat) {
      if (dat.result) {
        renderTable(dat.data);
      }
    }, { action: 'getall' });
  }

  function loadResumen() {
    MsgServer(path.model + 'pagos.php', function(dat) {
      if (dat.result) {
        $('#res-cant-hoy').text(dat.data.cantidad);
        $('#res-total-hoy').text('$' + parseFloat(dat.data.total).toFixed(2));
      }
    }, { action: 'resumen_dia' });

    MsgServer(path.model + 'pagos.php', function(dat) {
      if (dat.result) {
        $('#res-cant-mes').text(dat.data.cantidad);
        $('#res-total-mes').text('$' + parseFloat(dat.data.total).toFixed(2));
      }
    }, { action: 'resumen_mes' });
  }

  function renderTable(data) {
    var html = '';
    if (data.length === 0) {
      html = '<tr><td colspan="13" class="text-center text-muted">No hay pagos registrados.</td></tr>';
    } else {
      data.forEach(function(r) {
        var icon = r.tipo_vehiculo === 'auto' ? 'fa-car' : r.tipo_vehiculo === 'moto' ? 'fa-motorcycle' : r.tipo_vehiculo === 'camioneta' ? 'fa-truck' : 'fa-bicycle';
        var marcaColor = (r.marca || '-') + ' / ' + (r.color || '-');
        html += '<tr>';
        html += '<td><span class="badge bg-dark">' + r.cajon + '</span></td>';
        html += '<td class="fw-bold">' + r.placa + '</td>';
        html += '<td><small>' + marcaColor + '</small></td>';
        html += '<td><i class="fas ' + icon + ' me-1"></i>' + r.tipo_vehiculo + '</td>';
        html += '<td>' + r.seccion_nombre + '</td>';
        html += '<td><small>' + r.fecha_entrada + '</small></td>';
        html += '<td><small>' + r.fecha_salida + '</small></td>';
        html += '<td>' + r.horas + 'h</td>';
        html += '<td class="fw-bold text-success">$' + parseFloat(r.monto_total).toFixed(2) + '</td>';
        html += '<td>' + r.metodo_pago + '</td>';
        html += '<td>' + r.fecha_pago + '</td>';
        html += '<td class="text-center">';
        html += '<button class="btn btn-sm btn-outline-primary" onclick="verRecibo(\'' + r.recibo + '\')"><i class="fas fa-eye"></i></button>';
        html += '</td></tr>';
      });
    }
    $('#tbl-pagos').html(html);
  }

  function buscarRecibo() {
    var recibo = $('#buscar-recibo').val().trim();
    if (!recibo) { MsgNotify("Ingrese un número de recibo", "error"); return; }
    
    MsgServer(path.model + 'pagos.php', function(dat) {
      if (dat.result) {
        mostrarComprobante(dat.data);
      } else {
        MsgNotify(dat.message || "Recibo no encontrado", "error");
      }
    }, { action: 'recibo', recibo: recibo });
  }

  function verRecibo(recibo) {
    MsgServer(path.model + 'pagos.php', function(dat) {
      if (dat.result) {
        mostrarComprobante(dat.data);
      }
    }, { action: 'recibo', recibo: recibo });
  }

  function mostrarComprobante(d) {
    var html = '';
    html += '<div class="text-center mb-3">';
    html += '<h4 class="fw-bold text-success">COMPROBANTE DE PAGO</h4>';
    html += '<h5 class="text-muted">' + d.recibo + '</h5>';
    html += '</div>';
    html += '<div class="card bg-light" style="border-radius: 8px;"><div class="card-body">';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Cajón:</div><div class="col-7 fw-bold">' + d.cajon + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Placa:</div><div class="col-7 fw-bold">' + d.placa + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Marca:</div><div class="col-7">' + (d.marca || '-') + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Color:</div><div class="col-7">' + (d.color || '-') + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Tipo:</div><div class="col-7">' + d.tipo_vehiculo + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Sección:</div><div class="col-7">' + d.seccion_nombre + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Entrada:</div><div class="col-7">' + d.fecha_entrada + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Salida:</div><div class="col-7">' + d.fecha_salida + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Horas:</div><div class="col-7 fw-bold">' + d.horas + ' horas</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Método:</div><div class="col-7">' + d.metodo_pago + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Fecha Pago:</div><div class="col-7">' + d.fecha_pago + '</div></div>';
    html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Registrado por:</div><div class="col-7">' + (d.registrado_por || '-') + '</div></div>';
    if (d.observaciones) {
      html += '<div class="row mb-2"><div class="col-5 text-end text-muted">Obs:</div><div class="col-7">' + d.observaciones + '</div></div>';
    }
    html += '<hr class="my-2">';
    html += '<div class="text-center"><h3 class="fw-bold text-success">TOTAL: $' + parseFloat(d.monto_total).toFixed(2) + '</h3></div>';
    html += '</div></div>';

    $('#comprobante-content').html(html);
    new bootstrap.Modal(document.getElementById('modalComprobante')).show();
  }

  $(function() { 
    loadPagos();
    loadResumen();
  });
</script>

<?php include('view_footer.php'); ?>