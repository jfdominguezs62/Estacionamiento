<?php 
include ( '../constants.php' );
Constants::setpath_root  ("../");
Constants::create_filejs( true );

include ( Constants::getpath_root() . 'config.php' );
include ( Constants::getpath_tweb() . 'core.php' );
include ( Constants::getpath_root() . 'helpers.php' );

include('view_header.php');
?>

<h4 class="fw-bold mb-3"><i class="fas fa-money-check-alt me-2"></i>Historial de Pagos</h4>

<!-- Resumen -->
<div class="row mb-3 g-2">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 10px;">
      <div class="card-body py-2">
        <i class="fas fa-calendar-day fa-lg text-primary mb-1"></i>
        <h5 class="fw-bold mb-0" id="res-cant-hoy">0</h5>
        <small class="text-muted" style="font-size:10px;">Pagos Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 10px;">
      <div class="card-body py-2">
        <i class="fas fa-dollar-sign fa-lg text-success mb-1"></i>
        <h5 class="fw-bold mb-0" id="res-total-hoy">$0.00</h5>
        <small class="text-muted" style="font-size:10px;">Cobrado Hoy</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 10px;">
      <div class="card-body py-2">
        <i class="fas fa-calendar-alt fa-lg text-info mb-1"></i>
        <h5 class="fw-bold mb-0" id="res-cant-mes">0</h5>
        <small class="text-muted" style="font-size:10px;">Pagos Mes</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm text-center" style="border-radius: 10px;">
      <div class="card-body py-2">
        <i class="fas fa-coins fa-lg text-warning mb-1"></i>
        <h5 class="fw-bold mb-0" id="res-total-mes">$0.00</h5>
        <small class="text-muted" style="font-size:10px;">Cobrado Mes</small>
      </div>
    </div>
  </div>
</div>

<!-- Buscar recibo -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
  <div class="card-body py-2 px-2">
    <div class="input-group input-group-sm">
      <input type="text" class="form-control" id="buscar-recibo" placeholder="Buscar recibo (Ej: REC-20260729-00001)" style="font-size:12px;">
      <button class="btn btn-primary" onclick="buscarRecibo()" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">
        <i class="fas fa-search"></i>
      </button>
    </div>
  </div>
</div>

<!-- Tabla de pagos -->
<div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover table-sm mb-0">
        <thead class="table-light">
          <tr>
            <th>Cajón</th>
            <th>Placa</th>
            <th class="d-none d-md-table-cell">Marca/Color</th>
            <th class="d-none d-lg-table-cell">Tipo</th>
            <th class="d-none d-md-table-cell">Sección</th>
            <th class="d-none d-lg-table-cell">Entrada</th>
            <th>Salida</th>
            <th>Horas</th>
            <th>Monto</th>
            <th class="d-none d-md-table-cell">Método</th>
            <th class="text-center">Ver</th>
          </tr>
        </thead>
        <tbody id="tbl-pagos">
          <tr><td colspan="11" class="text-center text-muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 12px; max-height: 90vh;">
      <div class="modal-header text-white py-2" style="background: linear-gradient(135deg, #1b5e20, #2e7d32); border-radius: 12px 12px 0 0;">
        <h6 class="modal-title fw-bold"><i class="fas fa-receipt me-2"></i>Comprobante de Pago</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3" id="comprobante-content" style="overflow-y: auto;">
      </div>
      <div class="modal-footer border-0 py-2">
        <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" style="background: linear-gradient(135deg, #1a237e, #283593); border: none;">Cerrar</button>
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
      html = '<tr><td colspan="11" class="text-center text-muted">No hay pagos registrados.</td></tr>';
    } else {
      data.forEach(function(r) {
        html += '<tr>';
        html += '<td><span class="badge bg-dark" style="font-size:10px;">' + r.cajon + '</span></td>';
        html += '<td class="fw-bold" style="font-size:12px;">' + r.placa + '</td>';
        html += '<td class="d-none d-md-table-cell"><small>' + (r.marca || '-') + ' / ' + (r.color || '-') + '</small></td>';
        html += '<td class="d-none d-lg-table-cell"><small>' + r.tipo_vehiculo + '</small></td>';
        html += '<td class="d-none d-md-table-cell"><small>' + r.seccion_nombre + '</small></td>';
        html += '<td class="d-none d-lg-table-cell"><small>' + r.fecha_entrada + '</small></td>';
        html += '<td><small>' + r.fecha_salida + '</small></td>';
        html += '<td>' + r.horas + 'h</td>';
        html += '<td class="fw-bold text-success">$' + parseFloat(r.monto_total).toFixed(2) + '</td>';
        html += '<td class="d-none d-md-table-cell"><small>' + r.metodo_pago + '</small></td>';
        html += '<td class="text-center"><button class="btn btn-sm btn-outline-primary" onclick="verRecibo(\'' + r.recibo + '\')"><i class="fas fa-eye"></i></button></td></tr>';
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